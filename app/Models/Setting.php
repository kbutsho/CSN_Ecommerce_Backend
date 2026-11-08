<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'value'];

    const DEFAULTS = [
        'site_name' => 'Ecommerce',
        'hero_title' => 'Discover products from local shops',
        'hero_subtitle' => 'Pick a shop, browse what it sells and see its own prices.',
        'currency' => '৳',
        'contact_email' => 'kbutsho@gmail.com',
        'contact_phone' => '+8801621599321',
        'contact_address' => 'Dhaka, Bangladesh',
    ];

    public static function values(): array
    {
        return array_merge(self::DEFAULTS, self::pluck('value', 'name')->all());
    }
}
