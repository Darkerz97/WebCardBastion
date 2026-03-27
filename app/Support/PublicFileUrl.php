<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PublicFileUrl
{
    public static function fromPublicDisk(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $publicDisk = Storage::disk('public');
        $publicUrl = $publicDisk->url($path);
        $publicStoragePath = public_path('storage/'.str_replace('/', DIRECTORY_SEPARATOR, $path));

        if (is_file($publicStoragePath)) {
            return $publicUrl;
        }

        return route('media.public', ['path' => $path]);
    }
}
