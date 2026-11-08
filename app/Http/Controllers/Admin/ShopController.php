<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function index()
    {
        return Shop::with('owner:id,name,email')->withCount('products')->orderBy('id', 'desc')->get();
    }

    public function show(Shop $shop)
    {
        return [
            'id' => $shop->id,
            'name' => $shop->name,
            'description' => $shop->description,
            'owner' => $shop->owner ? $shop->owner->only('id', 'name', 'email') : null,
            'products' => $shop->productList(),
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id|unique:shops,user_id',
        ]);

        return Shop::create($data);
    }

    public function update(Request $request, Shop $shop)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('shops', 'user_id')->ignore($shop->id)],
        ]);

        $shop->update($data);

        return $shop;
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();

        return ['message' => 'Shop deleted.'];
    }
}
