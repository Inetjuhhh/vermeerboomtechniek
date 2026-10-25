<?php

namespace App\Calculator;

/** The estimate for one price model (rekenmethode) of a service. Totals are rounded, lines are not. */
final readonly class Estimate
{
    /**
     * @param  list<EstimateLine>  $workLines
     * @param  list<EstimateLine>  $otherCostLines
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public array $workLines,
        public array $otherCostLines,
        public float $exclMin,
        public float $exclMax,
        public float $inclMin,
        public float $inclMax,
        public float $vatPercentage,
    ) {}
}
