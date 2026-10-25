<?php

namespace App\Models;

use App\Enums\OptionEffect;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class CalculatorOption extends Model
{
    protected function casts(): array
    {
        return [
            'effect' => OptionEffect::class,
            'factor' => 'float',
            'amount_min' => 'float',
            'amount_max' => 'float',
            'is_default' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(CalculatorQuestion::class, 'calculator_question_id');
    }
}
