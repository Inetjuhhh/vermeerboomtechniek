<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Images are either temporary files in public/images (from the old site)
 * or uploads from the admin on the public disk.
 */
trait HasImage
{
    public function imageUrl(string $column = 'image'): ?string
    {
        $path = $this->{$column};

        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'images/') ? asset($path) : Storage::disk('public')->url($path);
    }
}
