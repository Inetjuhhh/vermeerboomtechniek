<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Guarded(['id'])]
class CalculatorOtherCost extends Model
{
    protected function casts(): array
    {
        return [
            'amount_min' => 'float',
            'amount_max' => 'float',
            'per_tree' => 'boolean',
            'applies_to_all' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(CalculatorService::class, 'calculator_other_cost_service');
    }

    /** Active costs for this service: the ones for all services plus the ones linked to it. */
    public function scopeFor(Builder $query, CalculatorService $service): void
    {
        $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->where('applies_to_all', true)
                ->orWhereHas('services', fn (Builder $s) => $s->whereKey($service->getKey())))
            ->orderBy('sort');
    }
}
