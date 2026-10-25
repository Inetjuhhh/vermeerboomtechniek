<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

/**
 * What an answer in the quote calculator does to the price.
 * "Basis" amounts are added before the factors (so a factor makes them more expensive),
 * "toeslagen" are added after the factors.
 */
enum OptionEffect: string implements HasLabel, HasDescription
{
    case None = 'none';
    case Factor = 'factor';
    case BaseFixed = 'base_fixed';
    case BasePerTree = 'base_per_tree';
    case SurchargeFixed = 'surcharge_fixed';
    case SurchargePerTree = 'surcharge_per_tree';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => 'Geen invloed op de prijs',
            self::Factor => 'Factor (keer)',
            self::BaseFixed => 'Basisbedrag, eenmalig',
            self::BasePerTree => 'Basisbedrag per boom',
            self::SurchargeFixed => 'Toeslag, eenmalig',
            self::SurchargePerTree => 'Toeslag per boom',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::None => 'Alleen ter informatie voor Bas.',
            self::Factor => 'Vermenigvuldigt het werk: 1,25 is 25% duurder, 0,8 is 20% goedkoper.',
            self::BaseFixed, self::BasePerTree => 'Telt op bij het werk, vóór de factoren.',
            self::SurchargeFixed, self::SurchargePerTree => 'Komt erbij ná de factoren, bijvoorbeeld stobbe frezen.',
        };
    }

    public function usesAmount(): bool
    {
        return ! in_array($this, [self::None, self::Factor], true);
    }

    public function isPerTree(): bool
    {
        return in_array($this, [self::BasePerTree, self::SurchargePerTree], true);
    }

    public function isBase(): bool
    {
        return in_array($this, [self::BaseFixed, self::BasePerTree], true);
    }
}
