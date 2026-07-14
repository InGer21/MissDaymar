<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPresentation;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductPresentationSeeder extends Seeder
{
    public function run(): void
    {
        $path = realpath(base_path('../miss_daymar_limpio.xlsx'));

        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getSheetByName('Presentaciones')->toArray();

        $products = Product::pluck('id', 'name');
        $existing = ProductPresentation::pluck('id', 'id');

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $productName = trim($row[0]);

            if (! isset($products[$productName])) {
                continue;
            }

            $signature = $products[$productName].'-'.trim($row[2]).'-'.trim($row[3]);

            if (ProductPresentation::where('product_id', $products[$productName])
                ->where('presentation_type', trim($row[2]))
                ->where('format', trim($row[3]))
                ->exists()) {
                continue;
            }

            ProductPresentation::create([
                'product_id' => $products[$productName],
                'presentation_type' => trim($row[2]),
                'format' => trim($row[3]),
                'unit' => trim($row[4]),
                'current_stock' => 0,
                'is_active' => true,
            ]);
        }
    }
}
