<?php

namespace App\Models;

use App\Support\Img;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'thumbnail', 'created_by'];

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class)->withPivot('price', 'details', 'thumbnail');
    }

    public function catalogData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'thumbnail' => Img::url($this->thumbnail),
        ];
    }

    public function shopData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'details' => $this->pivot->details ?: $this->description,
            'price' => $this->pivot->price,
            'thumbnail' => Img::url($this->pivot->thumbnail ?: $this->thumbnail),
        ];
    }
}
