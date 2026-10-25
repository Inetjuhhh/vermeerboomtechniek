<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** The rows of the services overview, from the top of the tree down. */
enum TreePart: string implements HasLabel
{
    case Kroon = 'kroon';
    case Stam = 'stam';
    case Wortels = 'wortels';

    public function getLabel(): string
    {
        return match ($this) {
            self::Kroon => 'In de kroon',
            self::Stam => 'Aan de stam',
            self::Wortels => 'Onder de grond',
        };
    }

    public function title(): string
    {
        return match ($this) {
            self::Kroon => 'Kroon',
            self::Stam => 'Stam',
            self::Wortels => 'Wortels',
        };
    }

    /** Background and text classes of the row: two greens and one brown. */
    public function classes(): string
    {
        return match ($this) {
            self::Kroon => 'bg-crown text-on-accent',
            self::Stam => 'bg-stem text-text',
            self::Wortels => 'bg-roots text-text',
        };
    }
}
