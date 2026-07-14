<?php

namespace Database\Seeders;

use App\Models\PresentationPrice;
use App\Models\Product;
use App\Models\ProductPresentation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PresentationPriceSeeder extends Seeder
{
    public function run(): void
    {
        $path = realpath(base_path('../miss_daymar_limpio.xlsx'));

        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getSheetByName('Precios Bultos')->toArray();

        $products = Product::pluck('id', 'name');
        $productsLower = [];
        foreach ($products as $name => $id) {
            $productsLower[mb_strtolower($name)] = $id;
        }

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $formatNormalized = $this->normalizeFormat(trim($row[2]));
            $productNameLower = mb_strtolower(trim($row[0]));
            $productId = $productsLower[$productNameLower] ?? null;

            if (! $productId) {
                continue;
            }

            $presentation = ProductPresentation::where('product_id', $productId)
                ->where(DB::raw('LOWER(REPLACE(REPLACE(format, "X", "x"), " ", ""))'), $formatNormalized)
                ->first();

            if (! $presentation) {
                continue;
            }

            $exists = PresentationPrice::where('presentation_id', $presentation->id)
                ->where('line', trim($row[1]))
                ->exists();

            if ($exists) {
                continue;
            }

            PresentationPrice::create([
                'presentation_id' => $presentation->id,
                'line' => trim($row[1]),
                'price_usd' => is_numeric($row[3]) ? (float) $row[3] : 0,
                'unit_price_usd' => is_numeric($row[4]) ? (float) $row[4] : 0,
            ]);
        }
    }

    private function normalizeFormat(string $format): string
    {
        return strtolower(str_replace(' ', '', $format));
    }
}
