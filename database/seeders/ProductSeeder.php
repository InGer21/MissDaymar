<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $path = realpath(base_path('../miss_daymar_limpio.xlsx'));

        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getSheetByName('Productos')->toArray();

        $categories = Category::pluck('id', 'name');

        $typeMap = [
            'Producto Terminado' => 'PT',
            'Ambos' => 'Ambos',
            'Materia Prima' => 'MP',
            'Producto Puro' => 'Puro',
        ];

        $existing = Product::pluck('id', 'name');

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $name = trim($row[1]);

            if (isset($existing[$name])) {
                continue;
            }

            Product::create([
                'name' => $name,
                'category_id' => $categories[trim($row[2])] ?? null,
                'type' => $typeMap[trim($row[3])] ?? 'MP',
                'line_1' => trim($row[4] ?? '') === 'Si' ? '1' : null,
                'line_2' => trim($row[5] ?? '') === 'Si' ? '2' : null,
                'is_pure' => trim($row[6] ?? '') === 'Si',
            ]);
        }
    }
}
