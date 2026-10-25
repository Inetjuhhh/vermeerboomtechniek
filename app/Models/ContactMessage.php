<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class ContactMessage extends Model
{
    protected function casts(): array
    {
        return [
            'calculator_summary' => 'array',
            'read_at' => 'datetime',
        ];
    }
}
