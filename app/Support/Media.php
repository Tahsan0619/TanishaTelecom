<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Media
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return asset($path);
    }

    /**
     * Files are written straight into public/uploads so the app works on
     * shared hosting without a storage symlink.
     */
    public static function store(UploadedFile $file, string $folder): string
    {
        $directory = public_path('uploads/'.$folder);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = now()->format('YmdHis').'-'.Str::random(8).'.'.strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $file->move($directory, $name);

        return 'uploads/'.$folder.'/'.$name;
    }

    public static function delete(?string $path): void
    {
        if ($path && Str::startsWith($path, 'uploads/')) {
            $full = public_path($path);
            if (is_file($full)) {
                @unlink($full);
            }
        }
    }
}
