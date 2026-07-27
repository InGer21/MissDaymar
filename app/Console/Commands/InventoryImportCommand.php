<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InventoryImportCommand extends Command
{
    protected $signature = 'inventory:import';

    protected $description = 'Importa inventario actualizado desde Excel y actualiza stock + lineas';

    public function handle(): int
    {
        $this->info('=== Inventory Import ===');

        $path = database_path('seeders/data/Inventario.xlsx');

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $updated = 0;
        $skipped = 0;

        foreach ($sheet->getRowIterator() as $row) {
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = trim((string) $cell->getValue());
            }

            $numero = $cells[1] ?? '';
            $produccion = $cells[2] ?? '';
            $producto = $cells[3] ?? '';
            $descripcion = $cells[4] ?? '';
            $cantidadRaw = $cells[5] ?? '';
            $observacion = $cells[6] ?? '';

            if (! is_numeric($numero)) {
                continue;
            }

            if (empty($producto) || empty($descripcion)) {
                continue;
            }

            $productoNormalized = mb_strtolower(trim($producto));
            $descripcionNormalized = strtolower(str_replace(' ', '', $descripcion));
            $cantidad = is_numeric($cantidadRaw) ? (float) $cantidadRaw : 0;
            $linea = in_array($produccion, ['LINEA 1', 'LINEA 2']) ? $produccion : null;

            $product = DB::table('products')
                ->whereRaw('LOWER(name) LIKE ?', ["%{$productoNormalized}%"])
                ->first();

            if (! $product) {
                $this->warn("  Product not found: {$producto}");
                $skipped++;

                continue;
            }

            $presentation = DB::table('product_presentations')
                ->where('product_id', $product->id)
                ->where(function ($q) use ($descripcionNormalized) {
                    $q->whereRaw("LOWER(REPLACE(format, ' ', '')) = ?", [$descripcionNormalized])
                        ->orWhereRaw("LOWER(REPLACE(format, ' ', '')) LIKE ?", ["%{$descripcionNormalized}%"]);
                })
                ->first();

            if (! $presentation) {
                $this->warn("  Presentation not found: {$producto} - {$descripcion}");
                $skipped++;

                continue;
            }

            DB::table('product_presentations')
                ->where('id', $presentation->id)
                ->update(['current_stock' => $cantidad]);

            if ($linea) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['line_1' => $linea]);
            }

            $this->line("  [{$numero}] {$produccion} | {$producto} {$descripcion} → stock={$cantidad}");
            $updated++;
        }

        $this->info("  Updated: {$updated}, Skipped: {$skipped}");
        $this->info('=== Done ===');

        return self::SUCCESS;
    }
}
