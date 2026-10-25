<?php

namespace App\Calculator;

use App\Enums\OptionEffect;
use App\Models\CalculatorOption;
use App\Models\CalculatorOtherCost;
use App\Models\CalculatorPriceModel;
use App\Models\CalculatorService;
use App\Models\Setting;
use Illuminate\Support\Collection;

/**
 * The sum behind "Bereken je offerte". Per price model:
 *
 *   work  = (base + per tree + hours × rate + base amounts of answers) × all factors
 *   total = work + surcharges of answers + other costs (voorrijkosten etc.)
 *
 * From the 2nd tree on the per-tree part gets the volume discount. Totals are rounded
 * (setting "afronden op") so the estimate does not look more precise than it is.
 */
class QuoteCalculator
{
    /**
     * @param  array<int|string, int|string|null>  $answers  question id => option id; missing answers use the default option
     * @return list<Estimate>  one per active price model, empty for contact-only services
     */
    public function calculate(CalculatorService $service, array $answers = [], int $trees = 1): array
    {
        if ($service->contact_only) {
            return [];
        }

        $service->loadMissing(['priceModels', 'questions.options']);
        $trees = $service->asks_tree_count ? max(1, $trees) : 1;
        $chosen = $this->chosenOptions($service, $answers);
        $otherCosts = CalculatorOtherCost::query()->for($service)->get();

        return $service->priceModels
            ->where('is_active', true)
            ->map(fn (CalculatorPriceModel $model) => $this->estimate($model, $chosen, $otherCosts, $trees))
            ->values()
            ->all();
    }

    /**
     * @param  array<int|string, int|string|null>  $answers
     * @return Collection<int, CalculatorOption>  keyed by question id
     */
    public function chosenOptions(CalculatorService $service, array $answers): Collection
    {
        return $service->questions
            ->mapWithKeys(function ($question) use ($answers) {
                $option = $question->options->firstWhere('id', (int) ($answers[$question->id] ?? 0))
                    ?? $question->defaultOption();

                return [$question->id => $option];
            })
            ->filter();
    }

    /**
     * @param  Collection<int, CalculatorOption>  $chosen
     * @param  Collection<int, CalculatorOtherCost>  $otherCosts
     */
    private function estimate(CalculatorPriceModel $model, Collection $chosen, Collection $otherCosts, int $trees): Estimate
    {
        // Trees that count for per-tree amounts after the volume discount, e.g. 3 trees at 10% = 2.8
        $discount = $this->setting('volume_discount_percentage') / 100;
        $discountedTrees = 1 + ($trees - 1) * (1 - $discount);

        $lines = [];
        $min = $max = 0.0;
        $perTreeMin = $perTreeMax = 0.0;

        $add = function (string $label, float $lo, float $hi, ?string $note = null) use (&$lines, &$min, &$max) {
            if ($lo == 0 && $hi == 0) {
                return;
            }
            $lines[] = new EstimateLine($label, $lo, $hi, $note);
            $min += $lo;
            $max += $hi;
        };

        $add('Vaste prijs', $model->base_min, $model->base_max);

        if ($model->per_tree_max > 0) {
            $add($this->treesLabel($trees, 'Per boom'), $model->per_tree_min * $trees, $model->per_tree_max * $trees, $this->perTreeNote($model->per_tree_min, $model->per_tree_max));
            $perTreeMin += $model->per_tree_min;
            $perTreeMax += $model->per_tree_max;
        }

        if ($model->hourly_rate > 0) {
            $hoursMin = $model->hours_min + $model->hours_per_tree_min * $trees;
            $hoursMax = $model->hours_max + $model->hours_per_tree_max * $trees;
            $add('Uren', $hoursMin * $model->hourly_rate, $hoursMax * $model->hourly_rate,
                $this->number($hoursMin) . ' tot ' . $this->number($hoursMax) . ' uur × ' . $this->money($model->hourly_rate));
            $perTreeMin += $model->hours_per_tree_min * $model->hourly_rate;
            $perTreeMax += $model->hours_per_tree_max * $model->hourly_rate;
        }

        foreach ($chosen as $option) {
            if ($option->effect === OptionEffect::BaseFixed) {
                $add($option->label, $option->amount_min, $option->amount_max);
            } elseif ($option->effect === OptionEffect::BasePerTree) {
                $add($this->treesLabel($trees, $option->label), $option->amount_min * $trees, $option->amount_max * $trees, $this->perTreeNote($option->amount_min, $option->amount_max));
                $perTreeMin += $option->amount_min;
                $perTreeMax += $option->amount_max;
            }
        }

        if ($trees > 1 && $discount > 0) {
            $off = $trees - $discountedTrees;
            $add('Staffelkorting vanaf de 2e boom', -$perTreeMin * $off, -$perTreeMax * $off, '−' . $this->number($discount * 100) . '% per extra boom');
        }

        foreach ($chosen as $option) {
            if ($option->effect === OptionEffect::Factor && $option->factor && $option->factor != 1) {
                [$beforeMin, $beforeMax] = [$min, $max];
                $add($option->label, $beforeMin * ($option->factor - 1), $beforeMax * ($option->factor - 1), '×' . $this->number($option->factor));
            }
        }

        foreach ($chosen as $option) {
            if ($option->effect === OptionEffect::SurchargeFixed) {
                $add($option->label, $option->amount_min, $option->amount_max);
            } elseif ($option->effect === OptionEffect::SurchargePerTree) {
                $add($this->treesLabel($trees, $option->label), $option->amount_min * $trees, $option->amount_max * $trees, $this->perTreeNote($option->amount_min, $option->amount_max));
            }
        }

        $workLines = $lines;
        $lines = [];

        foreach ($otherCosts as $cost) {
            $times = $cost->per_tree ? $trees : 1;
            $add($cost->per_tree ? $this->treesLabel($trees, $cost->name) : $cost->name, $cost->amount_min * $times, $cost->amount_max * $times,
                $cost->per_tree ? $this->perTreeNote($cost->amount_min, $cost->amount_max) : null);
        }

        $vat = (float) $this->setting('vat_percentage');
        [$exclMin, $exclMax] = $this->roundRange($min, $max);
        [$inclMin, $inclMax] = $this->roundRange($min * (1 + $vat / 100), $max * (1 + $vat / 100));

        return new Estimate($model->name, $model->description, $workLines, $lines, $exclMin, $exclMax, $inclMin, $inclMax, $vat);
    }

    /** @return array{float, float} */
    private function roundRange(float $min, float $max): array
    {
        $step = max(1, (float) $this->setting('round_to'));
        $min = max(0, round($min / $step) * $step);

        return [$min, max($min, round($max / $step) * $step)];
    }

    private function setting(string $key): mixed
    {
        return Setting::get("calculator.$key", config("calculator.$key"));
    }

    private function treesLabel(int $trees, string $label): string
    {
        return $trees > 1 ? "$label ({$trees}×)" : $label;
    }

    private function perTreeNote(float $min, float $max): string
    {
        return ($min == $max ? $this->money($min) : $this->money($min) . ' – ' . $this->money($max)) . ' per boom';
    }

    public function money(float $amount): string
    {
        return '€ ' . number_format($amount, $amount == floor($amount) ? 0 : 2, ',', '.');
    }

    private function number(float $n): string
    {
        return rtrim(rtrim(number_format($n, 2, ',', ''), '0'), ',');
    }
}
