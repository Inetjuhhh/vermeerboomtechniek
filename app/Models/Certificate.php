<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Certificate extends Model
{
    use HasImage;

    protected function casts(): array
    {
        return ['show_in_timeline' => 'boolean'];
    }
}
