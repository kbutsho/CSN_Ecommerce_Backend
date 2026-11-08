<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Everything a shop owner can do with his own shop
class ShopProductController extends Controller
{
    // Get the shop of the logged in owner
    private function myShop(Request $request)
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            abort(404, 'No shop is assigned to your account yet. Please ask the admin.');
        }

        return $shop;
    }

    // My shop and the products listed in it
    public function shop(Request $request)
    {
        $shop = $this->myShop($request);

        return [
            'id' => $shop->id,
            'name' => $shop->name,
            'description' => $shop->description,
            'products' => $shop->productList(),
        ];
    }

    // The shared catalog. "in_shop" tells if I already listed the product.
    public function catalog(Request $request)
    {
        $shop = $this->myShop($request);
        $listedIds = $shop->products()->pluck('products.id')->all();

        return Product::orderBy('name')->get()->map(function ($product) use ($listedIds) {
            return $product->catalogData() + ['in_shop' => in_array($product->id, $listedIds)];
        });
    }

    // Create a new product. It goes to the shared catalog and into my shop.
    public function createProduct(Request $request)
    {
        $shop = $this->myShop($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $path = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('products', 'public')
            : null;

        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'thumbnail' => $path,
            'created_by' => $request->user()->id,
        ]);

        $shop->products()->attach($product->id, ['price' => $data['price']]);

        return ['message' => 'Product created and added to your shop.'];
    }

    // Add a product from the catalog to my shop
    public function addProduct(Request $request)
    {
        $shop = $this->myShop($request);

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $path = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('products', 'public')
            : null;

        $shop->products()->syncWithoutDetaching([
            $data['product_id'] => [
                'price' => $data['price'],
                'details' => $data['details'] ?? null,
                'thumbnail' => $path,
            ],
        ]);

        return ['message' => 'Product added to your shop.'];
    }

    // Change price / details / thumbnail of a product in my shop
    public function updateProduct(Request $request, Product $product)
    {
        $shop = $this->myShop($request);

        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $row = $shop->products()->where('products.id', $product->id)->first();
        if (! $row) {
            abort(404, 'This product is not in your shop.');
        }

        $values = ['price' => $data['price'], 'details' => $data['details'] ?? null];

        if ($request->hasFile('thumbnail')) {
            if ($row->pivot->thumbnail) {
                Storage::disk('public')->delete($row->pivot->thumbnail);
            }
            $values['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        $shop->products()->updateExistingPivot($product->id, $values);

        return ['message' => 'Product updated.'];
    }

    // Remove a product from my shop (it stays in the catalog)
    public function removeProduct(Request $request, Product $product)
    {
        $this->myShop($request)->products()->detach($product->id);

        return ['message' => 'Product removed from your shop.'];
    }
}
