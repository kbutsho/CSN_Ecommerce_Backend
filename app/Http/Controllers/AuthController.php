<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Email or password is wrong.'], 422);
        }

        $user->api_token = Str::random(60);
        $user->save();

        return [
            'token' => $user->api_token,
            'user' => $user->publicData(),
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->update(['api_token' => null]);

        return ['message' => 'Logged out.'];
    }
}
