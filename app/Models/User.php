<?php

namespace App\Models;

use App\Support\Img;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role', 'avatar', 'api_token'];

    protected $hidden = ['password', 'api_token'];

    protected $appends = ['avatar_url'];

    protected $casts = ['password' => 'hashed'];

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return Img::url($this->avatar);
    }

    public function publicData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar_url' => $this->avatar_url,
        ];
    }
}
