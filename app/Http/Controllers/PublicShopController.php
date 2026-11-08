<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Support\Img;
use Illuminate\Http\Request;

class PublicShopController extends Controller
{
    public function index()
    {
        return Shop::with('products')->withCount('products')->orderBy('name')->get()->map(function ($shop) {
            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'description' => $shop->description,
                'products_count' => $shop->products_count,
                'previews' => $shop->products->take(3)->map(fn ($p) => Img::url($p->pivot->thumbnail ?: $p->thumbnail))->values(),
            ];
        });
    }

    public function products(Request $request)
    {
        $items = Shop::with('products')->get()
            ->flatMap(fn ($shop) => $shop->products->map(
                fn ($product) => $product->shopData() + ['shop' => ['id' => $shop->id, 'name' => $shop->name]]
            ))
            ->sortByDesc('id')
            ->values();

        if ($limit = (int) $request->query('limit')) {
            $items = $items->take($limit)->values();
        }

        return $items;
    }

    public function show(Shop $shop)
    {
        return [
            'id' => $shop->id,
            'name' => $shop->name,
            'description' => $shop->description,
            'products' => $shop->productList(),
        ];
    }

    public function product(Shop $shop, Product $product)
    {
        $item = $shop->products()->where('products.id', $product->id)->firstOrFail();

        return $item->shopData() + ['shop' => ['id' => $shop->id, 'name' => $shop->name]];
    }
}
