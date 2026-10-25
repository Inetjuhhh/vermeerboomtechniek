<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class Page extends Model
{
    use HasImage;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
