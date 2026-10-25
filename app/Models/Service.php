<?php

namespace App\Models;

use App\Enums\TreePart;
use App\Models\Concerns\HasImage;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class Service extends Model
{
    use HasImage;

    protected function casts(): array
    {
        return [
            'show_on_home' => 'boolean',
            'is_active' => 'boolean',
            'tree_part' => TreePart::class,
        ];
    }

    public function calculatorService(): BelongsTo
    {
        return $this->belongsTo(CalculatorService::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
