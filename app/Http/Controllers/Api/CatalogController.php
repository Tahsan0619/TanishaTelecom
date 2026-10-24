<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = Category::active()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'image_url' => $c->image_url,
                'products_count' => $c->products_count,
            ]);

        return response()->json(['data' => $categories]);
    }

    public function products(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $query = Product::active()->with('category');

        if ($slug = $request->query('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', (string) $request->query('ids'))));
            $query->whereIn('id', $ids ?: [0]);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
        }

        if ($request->filled('min_price')) {
            $query->whereRaw(Product::FINAL_PRICE_SQL.' >= ?', [(float) $request->query('min_price')]);
        }

        if ($request->filled('max_price')) {
            $query->whereRaw(Product::FINAL_PRICE_SQL.' <= ?', [(float) $request->query('max_price')]);
        }

        match ($request->query('sort')) {
            'price_asc' => $query->orderByRaw(Product::FINAL_PRICE_SQL.' asc'),
            'price_desc' => $query->orderByRaw(Product::FINAL_PRICE_SQL.' desc'),
            'popular' => $query->orderByDesc('sold_count'),
            'name' => $query->orderBy('name'),
            'oldest' => $query->orderBy('id'),
            default => $query->orderByDesc('is_featured')->orderByDesc('id'),
        };

        $paginator = $query->paginate((int) $request->query('per_page', 16))->withQueryString();

        $range = Product::active()
            ->selectRaw('MIN('.Product::FINAL_PRICE_SQL.') as min_price, MAX('.Product::FINAL_PRICE_SQL.') as max_price')
            ->first();

        return response()->json([
            'data' => collect($paginator->items())->map(fn (Product $p) => $p->toApi())->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'price_range' => [
                    'min' => (float) ($range->min_price ?? 0),
                    'max' => (float) ($range->max_price ?? 0),
                ],
            ],
        ]);
    }

    public function product(string $slug): JsonResponse
    {
        $product = Product::active()->with('category')->where('slug', $slug)->firstOrFail();

        $related = Product::active()
            ->with('category')
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        if ($related->count() < 4) {
            $related = $related->merge(
                Product::active()->with('category')
                    ->where('id', '!=', $product->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->inRandomOrder()
                    ->limit(4 - $related->count())
                    ->get()
            );
        }

        return response()->json([
            'data' => $product->toApi(true),
            'related' => $related->map(fn (Product $p) => $p->toApi())->values(),
        ]);
    }
}
