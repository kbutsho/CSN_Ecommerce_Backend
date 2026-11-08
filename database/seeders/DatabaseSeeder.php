<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    private function image(string $name): string
    {
        $path = 'products/'.$name.'.svg';
        Storage::disk('public')->put($path, file_get_contents(database_path('seeders/images/'.$name.'.svg')));

        return $path;
    }

    private function product(string $name, string $description, string $image, int $userId): Product
    {
        return Product::create([
            'name' => $name,
            'description' => $description,
            'thumbnail' => $this->image($image),
            'created_by' => $userId,
        ]);
    }

    public function run(): void
    {
        $admin = User::create(['name' => 'Kaushik Biswas', 'email' => 'admin@gmail.com', 'password' => 'Password#0000', 'role' => 'admin']);
        $owner1 = User::create(['name' => 'Rafi Ahmed', 'email' => 'owner1@gmail.com', 'password' => 'Password#0000', 'role' => 'owner']);
        $owner2 = User::create(['name' => 'Nusrat Jahan', 'email' => 'owner2@gmail.com', 'password' => 'Password#0000', 'role' => 'owner']);

        $shop1 = Shop::create(['name' => 'Urban Gadget House', 'description' => 'Phones, headphones and everyday tech for city life.', 'user_id' => $owner1->id]);
        $shop2 = Shop::create(['name' => 'Home & Living Corner', 'description' => 'Small things that make your home and your day better.', 'user_id' => $owner2->id]);

        $headphones = $this->product('Wireless Headphones', 'Over-ear Bluetooth headphones with deep bass and 30 hours of battery life.', 'headphones', $admin->id);
        $speaker = $this->product('Bluetooth Speaker', 'Portable speaker with rich sound and a splash proof body.', 'speaker', $admin->id);
        $watch = $this->product('Smart Watch', 'Tracks steps, sleep and heart rate. Battery lasts up to 7 days.', 'smartwatch', $admin->id);
        $bottle = $this->product('Steel Water Bottle', 'Double wall steel bottle. Keeps drinks cold for 24 hours.', 'bottle', $owner2->id);
        $lamp = $this->product('Desk Lamp', 'Dimmable LED lamp with a flexible arm. Easy on the eyes.', 'lamp', $owner2->id);
        $bag = $this->product('Canvas Backpack', 'Strong everyday backpack with a laptop pocket.', 'backpack', $owner2->id);
        $mug = $this->product('Ceramic Mug', 'Smooth glazed mug that holds 350 ml.', 'mug', $owner2->id);
        $this->product('Notebook Set', 'Three hard cover notebooks. Not listed in any shop yet, so customers cannot see it.', 'notebook', $admin->id);

        $shop1->products()->attach($headphones->id, ['price' => 2499]);
        $shop1->products()->attach($speaker->id, ['price' => 1899, 'details' => 'Splash proof speaker, great for trips and the beach.']);
        $shop1->products()->attach($watch->id, ['price' => 3999]);
        $shop1->products()->attach($bag->id, ['price' => 1990]);

        $shop2->products()->attach($bottle->id, ['price' => 650]);
        $shop2->products()->attach($lamp->id, ['price' => 1250]);
        $shop2->products()->attach($bag->id, ['price' => 1850]);
        $shop2->products()->attach($mug->id, ['price' => 320]);
        $shop2->products()->attach($headphones->id, ['price' => 2650]);
    }
}
