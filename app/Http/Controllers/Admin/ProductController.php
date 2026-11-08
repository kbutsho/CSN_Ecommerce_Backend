<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return Product::withCount('shops')->orderBy('id', 'desc')->get()->map(function ($product) {
            return $product->catalogData() + ['shops_count' => $product->shops_count];
        });
    }

    public function show(Product $product)
    {
        $shops = $product->shops()->get()->map(fn ($shop) => [
            'id' => $shop->id,
            'name' => $shop->name,
            'price' => $shop->pivot->price,
        ]);

        return $product->catalogData() + ['shops' => $shops];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }
        $data['created_by'] = $request->user()->id;

        return Product::create($data)->catalogData();
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        } else {
            unset($data['thumbnail']);
        }

        $product->update($data);

        return $product->catalogData();
    }

    public function destroy(Product $product)
    {
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }
        $product->delete();

        return ['message' => 'Product deleted.'];
    }
}
