<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return ['stats' => [
                'shops' => Shop::count(),
                'products' => Product::count(),
                'owners' => User::where('role', 'owner')->count(),
                'unlisted' => Product::doesntHave('shops')->count(),
            ]];
        }

        $shop = $user->shop;

        return [
            'shop' => $shop ? ['id' => $shop->id, 'name' => $shop->name] : null,
            'stats' => [
                'listed' => $shop ? $shop->products()->count() : 0,
                'catalog' => Product::count(),
                'created' => Product::where('created_by', $user->id)->count(),
            ],
        ];
    }
}
