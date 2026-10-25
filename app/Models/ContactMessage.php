<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

#[Guarded(['id'])]
class ContactMessage extends Model
{
    use Prunable;

    /** Removed after 12 months, as promised on the privacy page (runs daily, see routes/console.php). */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subMonths(12));
    }

    protected function casts(): array
    {
        return [
            'calculator_summary' => 'array',
            'read_at' => 'datetime',
        ];
    }
}
