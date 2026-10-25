<?php

namespace App\Livewire;

use App\Calculator\Estimate;
use App\Calculator\QuoteCalculator;
use App\Models\CalculatorService;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * The public "Bereken je offerte" page. Uses the same sum as the admin (QuoteCalculator).
 */
class QuoteCalculatorForm extends Component
{
    public ?int $serviceId = null;

    public int $trees = 1;

    /** @var array<int, int> question id => option id */
    public array $answers = [];

    public function mount(?string $dienst = null): void
    {
        $service = CalculatorService::query()->active()->where('slug', $dienst)->first()
            ?? CalculatorService::query()->active()->first();

        $this->serviceId = $service?->id;
        $this->resetAnswers();
    }

    public function updatedServiceId(): void
    {
        unset($this->service);
        $this->trees = 1;
        $this->resetAnswers();
    }

    public function updatedTrees(): void
    {
        $this->trees = max(1, min(50, (int) $this->trees));
    }

    #[Computed]
    public function services(): Collection
    {
        return CalculatorService::query()->active()->get();
    }

    #[Computed]
    public function service(): ?CalculatorService
    {
        return $this->serviceId
            ? CalculatorService::query()->active()->with(['questions.options', 'priceModels'])->find($this->serviceId)
            : null;
    }

    /** @return list<Estimate> */
    #[Computed]
    public function estimates(): array
    {
        return $this->service ? app(QuoteCalculator::class)->calculate($this->service, $this->answers, $this->trees) : [];
    }

    /** Puts the answers and the estimate in the session and continues on the contact page. */
    public function requestQuote()
    {
        $service = $this->service;
        if (! $service) {
            return null;
        }

        $calculator = app(QuoteCalculator::class);
        $chosen = $calculator->chosenOptions($service, $this->answers);

        session()->put('calculator_summary', [
            'service' => $service->name,
            'trees' => $service->asks_tree_count ? $this->trees : null,
            'answers' => $service->questions->mapWithKeys(fn ($q) => [$q->label => $chosen[$q->id]?->label])->filter()->all(),
            'estimates' => array_map(fn (Estimate $e) => [
                'name' => $e->name,
                'excl' => $calculator->money($e->exclMin) . ' – ' . $calculator->money($e->exclMax),
                'incl' => $calculator->money($e->inclMin) . ' – ' . $calculator->money($e->inclMax),
            ], $this->estimates),
        ]);

        return $this->redirectRoute('contact', ['onderwerp' => 'Offerte ' . mb_strtolower($service->name)]);
    }

    private function resetAnswers(): void
    {
        $this->answers = $this->service?->questions
            ->mapWithKeys(fn ($q) => [$q->id => $q->defaultOption()?->id])
            ->all() ?? [];
    }

    public function render()
    {
        return view('calculator.form', [
            'disclaimer' => Setting::get('calculator.disclaimer', config('calculator.disclaimer')),
            'money' => fn (float $n) => app(QuoteCalculator::class)->money($n),
        ]);
    }
}
