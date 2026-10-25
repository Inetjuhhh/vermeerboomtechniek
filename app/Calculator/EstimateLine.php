<?php

namespace App\Calculator;

/** One row of the breakdown: an amount range excl. btw, plus an optional note like "×1,25" or "€ 35 per boom". */
final readonly class EstimateLine
{
    public function __construct(
        public string $label,
        public float $min,
        public float $max,
        public ?string $note = null,
    ) {}
}
