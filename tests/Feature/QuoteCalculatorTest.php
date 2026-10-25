<?php

namespace Tests\Feature;

use App\Calculator\QuoteCalculator;
use App\Models\CalculatorOtherCost;
use App\Models\CalculatorService;
use App\Models\Setting;
use Database\Seeders\CalculatorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CalculatorSeeder::class);
    }

    private function service(string $slug): CalculatorService
    {
        return CalculatorService::where('slug', $slug)->firstOrFail();
    }

    public function test_removing_three_trees_with_default_answers(): void
    {
        [$estimate] = app(QuoteCalculator::class)->calculate($this->service('boom-verwijderen'), [], 3);

        // 3 × 350–700, minus 10% on trees 2 and 3 (70–140), × 1.25 for taking the wood away, + voorrijkosten 0–60
        $this->assertSame(1225.0, $estimate->exclMin);
        $this->assertSame(2500.0, $estimate->exclMax); // 2510 rounded to 25
        $this->assertSame(1475.0, $estimate->inclMin); // 1225 × 1.21 = 1482.25
        $this->assertSame(3025.0, $estimate->inclMax); // 2510 × 1.21 = 3037.10
        $this->assertSame('Voorrijkosten', $estimate->otherCostLines[0]->label);
    }

    public function test_answers_change_the_price(): void
    {
        $service = $this->service('boom-verwijderen');
        $fall = $service->questions->firstWhere('label', 'Kan de boom in één keer vrij vallen?');
        $stump = $service->questions->firstWhere('label', 'Moet de stobbe (stronk) weg?');

        [$estimate] = app(QuoteCalculator::class)->calculate($service, [
            $fall->id => $fall->options->firstWhere('label', 'Nee, moet in delen naar beneden')->id,
            $stump->id => $stump->options->firstWhere('label', 'Ja, wegfrezen')->id,
        ]);

        // 350 × 1.4 × 1.25 = 612.50, + stump 75 = 687.50, + voorrijkosten 0 → 700 (rounded)
        $this->assertSame(700.0, $estimate->exclMin);
        // 700 × 1.4 × 1.25 = 1225, + stump 200 + voorrijkosten 60 = 1485 → 1475
        $this->assertSame(1475.0, $estimate->exclMax);
    }

    public function test_bea_gives_an_estimate_for_each_of_the_three_methods(): void
    {
        $estimates = app(QuoteCalculator::class)->calculate($this->service('boom-effect-analyse'), [], 10);

        $this->assertSame(['Vaste prijs + per boom', 'Op uurbasis', 'Vaste projectprijs'], array_map(fn ($e) => $e->name, $estimates));
        // Project price does not depend on the number of trees: 750–1250 + voorrijkosten 0–60
        $this->assertSame([750.0, 1300.0], [$estimates[2]->exclMin, $estimates[2]->exclMax]);
    }

    public function test_storm_damage_gives_no_estimate(): void
    {
        $this->assertSame([], app(QuoteCalculator::class)->calculate($this->service('stormschade')));
    }

    public function test_settings_and_other_costs_from_the_admin_are_used(): void
    {
        Setting::put('calculator.vat_percentage', 9);
        Setting::put('calculator.round_to', 1);
        CalculatorOtherCost::query()->update(['amount_min' => 50, 'amount_max' => 50]);

        [$estimate] = app(QuoteCalculator::class)->calculate($this->service('veiligheidscontrole'), [], 2);

        // 95 + 2 × 35 − 10% of one tree (3.50) + 50 = 211.50 → 212 (rounded to 1)
        $this->assertSame(212.0, $estimate->exclMin);
        $this->assertSame(231.0, $estimate->inclMin); // 211.50 × 1.09 = 230.54
    }

    public function test_other_costs_can_be_limited_to_some_services(): void
    {
        $cost = CalculatorOtherCost::create(['name' => 'Afvoer stamhout', 'amount_min' => 100, 'amount_max' => 100, 'applies_to_all' => false]);
        $cost->services()->attach($this->service('boom-verwijderen'));

        $calculator = app(QuoteCalculator::class);
        $this->assertCount(2, $calculator->calculate($this->service('boom-verwijderen'))[0]->otherCostLines);
        $this->assertCount(1, $calculator->calculate($this->service('boom-snoeien'))[0]->otherCostLines);
    }
}
