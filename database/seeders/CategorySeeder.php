<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Ropa', 'Calzado', 'Accesorios', 'Electrónica'] as $name) {
            Category::create([
                'name' => $name,
                'description' => "Categoría de {$name}",
            ]);
        }
    }
}
