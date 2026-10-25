<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class CalculatorPriceModel extends Model
{
    protected function casts(): array
    {
        return [
            'base_min' => 'float',
            'base_max' => 'float',
            'per_tree_min' => 'float',
            'per_tree_max' => 'float',
            'hourly_rate' => 'float',
            'hours_min' => 'float',
            'hours_max' => 'float',
            'hours_per_tree_min' => 'float',
            'hours_per_tree_max' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(CalculatorService::class, 'calculator_service_id');
    }
}
