<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

#[Guarded(['id'])]
class Page extends Model
{
    use HasImage;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'show_in_menu' => 'boolean',
            'gallery' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /** Published pages that want a link in the menu and footer, e.g. the optional Houtbewerking page. */
    public static function menuPages(): Collection
    {
        return static::query()->published()->where('show_in_menu', true)->orderBy('title')->get(['title', 'slug', 'menu_label']);
    }

    /** @return list<array{url: string, caption: ?string, credit: ?string, credit_url: ?string}> */
    public function galleryItems(): array
    {
        return collect($this->gallery ?? [])
            ->filter(fn ($item) => filled($item['image'] ?? null))
            ->map(fn ($item) => [
                'url' => str_starts_with($item['image'], 'images/') ? asset($item['image']) : Storage::disk('public')->url($item['image']),
                'caption' => $item['caption'] ?? null,
                'credit' => $item['credit'] ?? null,
                'credit_url' => $item['credit_url'] ?? null,
            ])
            ->values()
            ->all();
    }
}
