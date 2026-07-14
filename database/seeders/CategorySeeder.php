<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Especias', 'Granos y Semillas', 'Productos Puros'];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name]
            );
        }
    }
}
