<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rellena `product_presentations.sku` con el SKU completo de 6 caracteres
 * leyendo `catalogo.csv`.
 *
 * No se puede resolver corriendo `catalog:import`: sin `--force` se salta la
 * reimportación cuando el catálogo ya existe, y con `--force` haría `cleanAll()`,
 * que borra las presentaciones y con ellas el `current_stock` real del almacén.
 * Este relleno solo hace UPDATE sobre las filas que ya existen.
 */
return new class extends Migration
{
    private array $presentationMap = [
        'Ristra' => 'ristra',
        'Bolsa Individual' => 'bolsa_individual',
        'Por Kilo' => 'por_kilo',
        'Bulto' => 'bulto',
        'Medio Bulto' => 'medio_bulto',
        'Bolsa 4kg' => 'bolsa_4kg',
        'Saco' => 'saco',
    ];

    public function up(): void
    {
        $path = database_path('seeders/data/catalogo.csv');

        if (! file_exists($path)) {
            return;
        }

        $products = DB::table('products')->whereNotNull('sku')->pluck('id', 'sku');
        $vistos = [];

        foreach ($this->parseCsv($path) as $row) {
            $sku = trim($row[0] ?? '');
            $presType = trim($row[3] ?? '');
            $format = trim($row[4] ?? '');

            if ($sku === '' || $presType === '' || $format === '') {
                continue;
            }

            $productId = $products[substr($sku, 0, 4)] ?? null;

            if (! $productId) {
                continue;
            }

            $mappedType = $this->presentationMap[$presType] ?? 'por_kilo';
            $clave = $productId.'|'.$mappedType.'|'.$format;

            // El CSV puede traer filas repetidas para la misma presentación;
            // el importador original también se queda con la primera.
            if (isset($vistos[$clave])) {
                continue;
            }

            $vistos[$clave] = true;

            DB::table('product_presentations')
                ->where('product_id', $productId)
                ->where('presentation_type', $mappedType)
                ->where('format', $format)
                ->whereNull('sku')
                ->update(['sku' => $sku]);
        }
    }

    public function down(): void
    {
        DB::table('product_presentations')->update(['sku' => null]);
    }

    private function parseCsv(string $path): array
    {
        $content = mb_convert_encoding(file_get_contents($path), 'UTF-8', 'ISO-8859-1');
        $rows = [];

        foreach (explode("\n", $content) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $rows[] = str_getcsv($line, ';');
        }

        array_shift($rows); // encabezado

        return $rows;
    }
};
