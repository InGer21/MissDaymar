<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Entity;
use App\Models\PresentationPrice;
use App\Models\Product;
use App\Models\ProductPresentation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfitImportCommand extends Command
{
    protected $signature = 'profit:import';

    protected $description = 'Importa datos desde Profit Plus a Miss Daymar';

    private string $dataPath;

    private array $unitMap = [
        'UNI' => ['type' => 'por_kilo', 'unit' => 'unit'],
        'KG' => ['type' => 'por_kilo', 'unit' => 'kg'],
        'G' => ['type' => 'por_kilo', 'unit' => 'g'],
        'BUL' => ['type' => 'bulto', 'unit' => 'multipack'],
        'SACO' => ['type' => 'saco', 'unit' => 'sack'],
        'CAJA' => ['type' => 'bulto', 'unit' => 'multipack'],
        'DOC' => ['type' => 'bulto', 'unit' => 'multipack'],
        'PAQ' => ['type' => 'bolsa_individual', 'unit' => 'unit'],
        'BOL' => ['type' => 'bolsa_individual', 'unit' => 'unit'],
        'GAL' => ['type' => 'por_kilo', 'unit' => 'unit'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->dataPath = database_path('seeders/data');
    }

    public function handle(): int
    {
        $this->info('=== Import Profit Plus -> Miss Daymar ===');

        $this->importCategories();
        $this->importProducts();
        $this->importPresentations();
        $this->importPrices();
        $this->importSalespersons();
        $this->importClients();
        $this->importStock();

        $this->info('=== Done ===');

        return self::SUCCESS;
    }

    private function insertBatch(string $table, array &$batch, int $maxSize, int $delay = 300000): void
    {
        if (count($batch) >= $maxSize) {
            $this->retryInsert($table, $batch);
            $batch = [];
            usleep($delay);
        }
    }

    private function flushBatch(string $table, array &$batch, int $delay = 300000): void
    {
        if (! empty($batch)) {
            $this->retryInsert($table, $batch);
            $batch = [];
            usleep($delay);
        }
    }

    private function retryInsert(string $table, array $rows): void
    {
        $maxRetries = 3;

        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                DB::table($table)->insert($rows);

                return;
            } catch (\Exception $e) {
                if ($i === $maxRetries - 1) {
                    throw $e;
                }

                $this->warn('  Retry '.($i + 1).' for '.$table.' ('.count($rows).' rows)...');
                DB::reconnect();
                sleep(3);
            }
        }
    }

    private function parseTsv(string $filename): array
    {
        $path = $this->dataPath.'/'.$filename;

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");

            return [];
        }

        $content = file_get_contents($path);
        $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
        $content = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $content);

        $lines = explode("\n", $content);
        $lines = array_filter($lines, fn ($l) => ! empty(trim($l)));
        $rows = [];

        foreach ($lines as $line) {
            $line = rtrim($line, "\r");
            $row = array_map('trim', explode("\t", $line));
            $row = array_pad($row, 22, null);
            $rows[] = $row;
        }

        return $rows;
    }

    private function importCategories(): void
    {
        $this->info('Categories...');

        $rows = $this->parseTsv('productos.txt');
        $existing = Category::pluck('id', 'slug');
        $batch = [];
        $seen = [];
        $now = now()->toDateTimeString();

        foreach ($rows as $row) {
            $coCat = $row[6] ?? '';
            $catDes = $row[7] ?? '';
            $tipo = $row[8] ?? '';

            if ($tipo !== 'V') {
                continue;
            }

            $catSlug = Str::slug($catDes) ?: $coCat;

            if (isset($seen[$catSlug]) || isset($existing[$catSlug])) {
                continue;
            }

            $seen[$catSlug] = true;
            $batch[] = [
                'slug' => $catSlug,
                'name' => $catDes,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $this->insertBatch('categories', $batch, 5);
        }

        $this->flushBatch('categories', $batch);
        $this->info('  -> '.count($seen).' categories');
    }

    private function importProducts(): void
    {
        $this->info('Products...');

        $rows = $this->parseTsv('productos.txt');
        $existingCodes = Product::whereNotNull('profit_code')->pluck('id', 'profit_code');
        $categories = Category::pluck('id', 'slug');

        $batch = [];
        $now = now()->toDateTimeString();
        $count = 0;
        $seen = [];

        foreach ($rows as $row) {
            $coArt = $row[0] ?? '';
            $artDes = $row[1] ?? '';
            $coLin = $row[2] ?? '';
            $linDes = $row[3] ?? '';
            $coSubl = $row[4] ?? '';
            $sublDes = $row[5] ?? '';
            $coCat = $row[6] ?? '';
            $catDes = $row[7] ?? '';
            $tipo = $row[8] ?? '';

            if ($tipo !== 'V') {
                continue;
            }

            if (isset($existingCodes[$coArt]) || isset($seen[$coArt])) {
                continue;
            }

            $seen[$coArt] = true;
            $catSlug = Str::slug($catDes) ?: $coCat;

            $batch[] = [
                'profit_code' => $coArt,
                'name' => mb_convert_case(trim($artDes), MB_CASE_TITLE, 'UTF-8'),
                'category_id' => $categories[$catSlug] ?? null,
                'type' => 'PT',
                'line_1' => $linDes ?: $coLin,
                'line_2' => $sublDes ?: $coSubl,
                'profit_line' => $coLin,
                'profit_subl' => $coSubl,
                'is_pure' => false,
                'is_service' => false,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $count++;
            $this->insertBatch('products', $batch, 5);
        }

        $this->flushBatch('products', $batch);
        $this->info("  -> {$count} products");
    }

    private function importPresentations(): void
    {
        $this->info('Presentations...');

        $rows = $this->parseTsv('presentaciones.txt');
        $products = Product::pluck('id', 'profit_code');
        $allExisting = ProductPresentation::select('product_id', 'profit_unit_code')
            ->get()
            ->mapWithKeys(fn ($p) => [$p->product_id.'|'.$p->profit_unit_code => true]);

        $batch = [];
        $now = now()->toDateTimeString();
        $count = 0;
        $seen = [];

        foreach ($rows as $row) {
            $coArt = $row[0] ?? '';
            $coUni = $row[1] ?? '';
            $equivalencia = (float) ($row[2] ?? 0);
            $usoVenta = (int) ($row[3] ?? 0);
            $uniPrincipal = (int) ($row[5] ?? 0);

            if (! isset($products[$coArt])) {
                continue;
            }

            $productId = $products[$coArt];
            $key = $productId.'|'.$coUni;

            if (isset($allExisting[$key]) || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $mapping = $this->unitMap[strtoupper($coUni)] ?? ['type' => 'por_kilo', 'unit' => 'unit'];

            $batch[] = [
                'product_id' => $productId,
                'presentation_type' => $mapping['type'],
                'profit_unit_code' => $coUni,
                'profit_equivalence' => $equivalencia,
                'format' => (string) $equivalencia,
                'unit' => $mapping['unit'],
                'current_stock' => 0,
                'is_active' => (bool) $usoVenta,
                'is_main_unit' => (bool) $uniPrincipal,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $count++;
            $this->insertBatch('product_presentations', $batch, 5);
        }

        $this->flushBatch('product_presentations', $batch);
        $this->info("  -> {$count} presentations");
    }

    private function importPrices(): void
    {
        $this->info('Prices...');

        $rows = $this->parseTsv('precios.txt');
        $products = Product::pluck('id', 'profit_code');
        $presentations = ProductPresentation::select('id', 'product_id', 'is_main_unit')
            ->get()
            ->groupBy('product_id');

        $latestPricePerProduct = [];

        foreach ($rows as $row) {
            $coArt = $row[0] ?? '';
            $monto = (float) ($row[3] ?? 0);
            $desde = $row[4] ?? '';

            if (! isset($products[$coArt])) {
                continue;
            }

            if ($monto > 1000) {
                continue;
            }

            if (! isset($latestPricePerProduct[$coArt]) || $desde > $latestPricePerProduct[$coArt]['desde']) {
                $latestPricePerProduct[$coArt] = [
                    'monto' => $monto,
                    'desde' => $desde,
                ];
            }
        }

        $existingPrices = PresentationPrice::pluck('id', 'presentation_id');
        $batch = [];
        $now = now()->toDateTimeString();
        $count = 0;

        foreach ($latestPricePerProduct as $coArt => $data) {
            $productId = $products[$coArt];

            if (! isset($presentations[$productId])) {
                continue;
            }

            $presList = $presentations[$productId];
            $presentationId = $presList->firstWhere('is_main_unit', true)?->id
                ?? $presList->first()?->id;

            if (! $presentationId || isset($existingPrices[$presentationId])) {
                continue;
            }

            $batch[] = [
                'presentation_id' => $presentationId,
                'line' => 1,
                'price_usd' => $data['monto'],
                'unit_price_usd' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $count++;
            $this->insertBatch('presentation_prices', $batch, 10);
        }

        $this->flushBatch('presentation_prices', $batch);
        $this->info("  -> {$count} prices");
    }

    private function importSalespersons(): void
    {
        $this->info('Salespersons...');

        $rows = $this->parseTsv('vendedores.txt');
        $existing = User::where('is_salesperson', true)->pluck('id', 'profit_code');
        $seen = [];
        $count = 0;

        foreach ($rows as $row) {
            $coVen = $row[0] ?? '';
            $venDes = $row[1] ?? '';
            $email = $row[4] ?? '';
            $inactivo = (int) ($row[6] ?? 0);

            if (str_contains($coVen, '-')) {
                continue;
            }

            if (isset($existing[$coVen]) || isset($seen[$coVen])) {
                continue;
            }

            $seen[$coVen] = true;
            $name = trim($venDes) ?: "Vendedor {$coVen}";

            User::create([
                'name' => $name,
                'email' => ($email && $email !== 'NULL') ? $email : Str::slug($name).'@missdaymar.local',
                'password' => bcrypt(Str::random(16)),
                'role' => 'vendedor',
                'profit_code' => $coVen,
                'is_salesperson' => true,
            ]);

            $count++;
            usleep(200000);
        }

        $this->info("  -> {$count} salespersons");
    }

    private function importClients(): void
    {
        $this->info('Clients...');

        $rows = $this->parseTsv('clientes.txt');
        $existingCodes = array_flip(Entity::whereNotNull('profit_code')->pluck('profit_code')->toArray());

        $batch = [];
        $now = now()->toDateTimeString();
        $count = 0;
        $skipped = 0;
        $seen = [];

        foreach ($rows as $row) {
            $coCli = $row[0] ?? '';
            $cliDes = $row[1] ?? '';
            $coVen = $row[6] ?? '';
            $rif = $row[3] ?? '';
            $direc1 = $row[7] ?? '';
            $telefonos = $row[9] ?? '';
            $email = $row[11] ?? '';
            $ciudad = $row[13] ?? '';
            $inactivo = (int) ($row[20] ?? 0);

            if (empty($coCli) || empty($cliDes)) {
                continue;
            }

            if (isset($existingCodes[$coCli]) || isset($seen[$coCli])) {
                $skipped++;

                continue;
            }

            $seen[$coCli] = true;

            $batch[] = [
                'profit_code' => $coCli,
                'profit_vendor' => $coVen && $coVen !== 'NULL' ? $coVen : null,
                'profit_zone' => '',
                'type' => 'customer',
                'name' => trim($cliDes),
                'rif' => $rif !== 'NULL' ? $rif : null,
                'sunagro' => null,
                'fiscal_state' => '',
                'fiscal_city' => $ciudad && $ciudad !== 'NULL' ? $ciudad : '',
                'address' => $direc1 !== 'NULL' ? $direc1 : null,
                'phone' => $telefonos !== 'NULL' ? $telefonos : null,
                'email' => ($email && $email !== 'NULL') ? $email : null,
                'is_active' => ! $inactivo,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $count++;
            $this->insertBatch('entities', $batch, 5);
        }

        $this->flushBatch('entities', $batch);
        $this->info("  -> {$count} clients".($skipped > 0 ? " ({$skipped} dup)" : ''));
    }

    private function importStock(): void
    {
        $this->info('Stock...');

        $rows = $this->parseTsv('inventario.txt');
        $products = Product::pluck('id', 'profit_code');
        $presentations = ProductPresentation::select('id', 'product_id', 'is_main_unit')
            ->get()
            ->groupBy('product_id');

        $count = 0;

        foreach ($rows as $row) {
            $coAlma = $row[0] ?? '';
            $coArt = $row[2] ?? '';
            $stock = (float) ($row[4] ?? 0);

            if ($coAlma !== '001' || ! isset($products[$coArt])) {
                continue;
            }

            $productId = $products[$coArt];

            if (! isset($presentations[$productId])) {
                continue;
            }

            $presList = $presentations[$productId];
            $presId = $presList->firstWhere('is_main_unit', true)?->id
                ?? $presList->first()?->id;

            if (! $presId) {
                continue;
            }

            DB::table('product_presentations')
                ->where('id', $presId)
                ->update(['current_stock' => $stock]);

            $count++;
        }

        $this->info("  -> {$count} stocks updated");
    }
}
