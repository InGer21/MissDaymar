<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogImportCommand extends Command
{
    protected $signature = 'catalog:import {--force : Reimport even if catalog exists}';

    protected $description = 'Importa el catálogo limpio desde CSV con SKU';

    private array $presentationMap = [
        'Ristra' => 'ristra',
        'Bolsa Individual' => 'bolsa_individual',
        'Por Kilo' => 'por_kilo',
        'Bulto' => 'bulto',
        'Medio Bulto' => 'medio_bulto',
        'Bolsa 4kg' => 'bolsa_4kg',
        'Saco' => 'saco',
    ];

    private array $unitMap = [
        'gr' => 'g',
        'kg' => 'kg',
        'multipack' => 'multipack',
    ];

    public function handle(): int
    {
        $this->info('=== Catalog Import ===');

        $this->cleanGarbage();

        $hasCatalog = Product::whereNotNull('sku')->exists();

        if ($hasCatalog && ! $this->option('force')) {
            $this->info('Catalog already imported. Use --force to reimport.');

            return self::SUCCESS;
        }

        $this->cleanAll();
        $this->importCategories();
        $this->importProducts();
        $this->importPresentations();

        $this->info('=== Done ===');

        return self::SUCCESS;
    }

    private function cleanGarbage(): void
    {
        $this->info('Cleaning garbage data...');

        DB::table('entities')->whereNull('user_id')->update(['user_id' => 1]);

        DB::table('product_presentations')
            ->whereIn('product_id', Product::whereNull('sku')->pluck('id'))
            ->delete();

        Product::whereNull('sku')->forceDelete();

        DB::table('categories')->where('slug', 'general')->delete();

        DB::table('raw_materials')->delete();

        $this->info('  -> Garbage cleaned');
    }

    private function cleanAll(): void
    {
        $this->info('Cleaning all catalog data...');

        DB::table('presentation_prices')->delete();
        DB::table('product_presentations')->delete();
        Product::query()->forceDelete();
        DB::table('categories')->where('slug', 'general')->delete();

        $this->info('  -> All cleaned');
    }

    private function importCategories(): void
    {
        $this->info('Categories...');

        $rows = $this->parseCsv();
        $seen = [];

        foreach ($rows as $row) {
            $cat = $row[2] ?? '';

            if (empty($cat)) {
                continue;
            }

            $slug = Str::slug($cat);

            if (isset($seen[$slug])) {
                continue;
            }

            $seen[$slug] = true;

            Category::firstOrCreate(
                ['slug' => $slug],
                ['name' => $cat]
            );
        }

        $this->info('  -> '.count($seen).' categories');
    }

    private function importProducts(): void
    {
        $this->info('Products...');

        $rows = $this->parseCsv();
        $categories = Category::pluck('id', 'slug');
        $products = [];
        $count = 0;

        foreach ($rows as $row) {
            $sku = $row[0] ?? '';
            $name = $row[1] ?? '';
            $cat = $row[2] ?? '';

            if (empty($name) || empty($sku)) {
                continue;
            }

            $productKey = Str::slug($name);
            $catSlug = Str::slug($cat);

            if (isset($products[$productKey])) {
                if (empty($products[$productKey]['sku'])) {
                    $products[$productKey]['sku'] = $sku;
                }

                continue;
            }

            $products[$productKey] = [
                'sku' => substr($sku, 0, 4),
                'name' => $name,
                'category_id' => $categories[$catSlug] ?? null,
                'type' => 'PT',
            ];
        }

        $batch = [];
        $now = now()->toDateTimeString();

        foreach ($products as $data) {
            $batch[] = [
                'sku' => $data['sku'],
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'type' => 'PT',
                'line_1' => '',
                'line_2' => '',
                'is_pure' => false,
                'is_service' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $count++;
        }

        DB::table('products')->insertOrIgnore($batch);
        $this->info("  -> {$count} products");
    }

    private function importPresentations(): void
    {
        $this->info('Presentations...');

        $rows = $this->parseCsv();
        $products = DB::table('products')->pluck('id', 'sku');
        $existing = [];
        $batch = [];
        $now = now()->toDateTimeString();
        $count = 0;

        foreach ($rows as $row) {
            $sku = $row[0] ?? '';
            $name = $row[1] ?? '';
            $presType = $row[3] ?? '';
            $format = $row[4] ?? '';
            $unitRaw = $row[5] ?? '';

            if (empty($sku) || empty($presType) || empty($format)) {
                continue;
            }

            $productSku = substr($sku, 0, 4);
            $productId = $products[$productSku] ?? null;

            if (! $productId) {
                continue;
            }

            $mappedType = $this->presentationMap[$presType] ?? 'por_kilo';
            $mappedUnit = $this->unitMap[$unitRaw] ?? $unitRaw;

            $key = $productId.'|'.$mappedType.'|'.$format;

            if (isset($existing[$key])) {
                continue;
            }

            $existing[$key] = true;

            $batch[] = [
                'product_id' => $productId,
                'presentation_type' => $mappedType,
                'format' => $format,
                'unit' => $mappedUnit,
                'current_stock' => 0,
                'is_active' => true,
                'is_main_unit' => in_array($mappedType, ['bulto', 'saco']),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $count++;

            if (count($batch) >= 25) {
                DB::table('product_presentations')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('product_presentations')->insert($batch);
        }

        $this->info("  -> {$count} presentations");
    }

    private function parseCsv(): array
    {
        $path = database_path('seeders/data/catalogo.csv');

        $content = file_get_contents($path);
        $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
        $lines = explode("\n", $content);
        $rows = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            $row = str_getcsv($line, ';');
            $rows[] = $row;
        }

        array_shift($rows); // Remove header

        return $rows;
    }
}
