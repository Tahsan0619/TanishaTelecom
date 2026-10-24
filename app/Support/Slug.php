<?php

namespace App\Support;

use Illuminate\Support\Str;

class Slug
{
    /**
     * Bengali names transliterate to an empty slug, so fall back to a
     * prefix + random suffix in that case.
     */
    public static function unique(string $modelClass, string $source, string $fallbackPrefix, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        if ($base === '') {
            $base = $fallbackPrefix.'-'.Str::lower(Str::random(6));
        }

        $slug = $base;
        $i = 2;
        while ($modelClass::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
