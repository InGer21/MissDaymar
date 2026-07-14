<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RawMaterialSeeder extends Seeder
{
    private array $nameMap = [
        'Maiz para Cotufa' => 'Maíz para Cotufa',
        'Avena en Hojuelas' => 'Avena en hojuelas',
        'Ajonjoli Blanco' => 'Ajonjoli',
        'Anis Dulce' => 'Anis dulce',
        'Anis Estrellado' => 'Anis estrellado',
        'Bicarbonato de Sodio' => 'Bicarbonato',
        'Canela Entera' => 'Canela entera',
        'Chia Negra' => 'Chia',
        'Clavos de Olor' => 'Clavos dulces',
        'Comino Entero' => 'Comino entero',
        'Curry Puro' => 'Curry',
        'Flor de Jamaica' => 'Flor Jamaica',
        'Laurel en Hojas' => 'Laurel',
        'Onoto Entero' => 'Onoto entero',
        'Oregano Entero' => 'Oregano',
        'Paprika Dulce' => 'Paprika dulce',
        'Pimienta Negra Entera' => 'Pimienta negra entera',
        'Sal (Kilogramo)' => 'Sal',
        'Sal (Saco)' => 'Sal',
        'Ajinomoto (Glutamato)' => 'Ajinomoto',
        'Jenjibre (Jengibre)' => 'Jengibre',
    ];

    public function run(): void
    {
        $path = realpath(base_path('../miss_daymar_limpio.xlsx'));

        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getSheetByName('Materia Prima')->toArray();

        $products = Product::pluck('id', 'name');

        $existing = RawMaterial::pluck('id', 'code');

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $code = trim($row[0]);

            if (isset($existing[$code])) {
                continue;
            }

            $costRaw = trim($row[4] ?? '0');
            $cost = (float) str_replace(',', '.', str_replace('.', '', $costRaw));

            $productName = trim($row[1]);
            $resolvedName = $this->nameMap[$productName] ?? $productName;

            RawMaterial::create([
                'code' => $code,
                'name' => $productName,
                'product_id' => $products[$resolvedName] ?? null,
                'purchase_presentation' => trim($row[2] ?? ''),
                'unit' => trim($row[3] ?? ''),
                'unit_cost' => $cost,
                'notes' => trim($row[5] ?? ''),
            ]);
        }
    }
}
