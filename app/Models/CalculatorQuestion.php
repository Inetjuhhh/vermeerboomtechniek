<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id'])]
class CalculatorQuestion extends Model
{
    public function service(): BelongsTo
    {
        return $this->belongsTo(CalculatorService::class, 'calculator_service_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(CalculatorOption::class)->orderBy('sort');
    }

    public function defaultOption(): ?CalculatorOption
    {
        return $this->options->firstWhere('is_default', true) ?? $this->options->first();
    }
}
