<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    public function index()
    {
        return User::where('role', 'owner')->with('shop:id,name,user_id')->orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $data['role'] = 'owner';

        return User::create($data);
    }

    public function update(Request $request, User $owner)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($owner->id)],
            'password' => 'nullable|string|min:6',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $owner->update($data);

        return $owner;
    }

    public function destroy(User $owner)
    {
        $owner->delete();

        return ['message' => 'Owner deleted.'];
    }
}
