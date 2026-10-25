<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id'])]
class CalculatorService extends Model
{
    protected function casts(): array
    {
        return [
            'asks_tree_count' => 'boolean',
            'contact_only' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function priceModels(): HasMany
    {
        return $this->hasMany(CalculatorPriceModel::class)->orderBy('sort');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CalculatorQuestion::class)->orderBy('sort');
    }

    public function otherCosts(): BelongsToMany
    {
        return $this->belongsToMany(CalculatorOtherCost::class, 'calculator_other_cost_service');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort');
    }
}
