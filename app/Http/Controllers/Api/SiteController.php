<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
{
    public function settings(): JsonResponse
    {
        return response()->json(['data' => Setting::publicValues()]);
    }

    public function sliders(): JsonResponse
    {
        $sliders = Slider::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Slider $s) => [
                'id' => $s->id,
                'title' => $s->title,
                'subtitle' => $s->subtitle,
                'image_url' => $s->image_url,
                'link' => $s->link,
                'position' => $s->position,
            ]);

        return response()->json([
            'data' => [
                'main' => $sliders->where('position', 'main')->values(),
                'side' => $sliders->where('position', 'side')->values(),
            ],
        ]);
    }

    public function page(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return response()->json([
            'data' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'updated_at' => $page->updated_at?->toIso8601String(),
            ],
        ]);
    }
}
