<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateProductDataToRelational extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:migrate-relational';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate flat product data to relational tables (variants, images, descriptions) and update relations.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data migration...');

        DB::beginTransaction();

        try {
            $products = DB::table('products')->get();

            $bar = $this->output->createProgressBar(count($products));
            $bar->start();

            foreach ($products as $product) {
                // 1. Create Default Variant
                $variantId = DB::table('product_variants')->insertGetId([
                    'product_id' => $product->id,
                    'name'       => 'Default',
                    'sku'        => null,
                    'price'      => $product->price,
                    'stock'      => $product->stock,
                    'weight'     => $product->weight,
                    'is_active'  => $product->is_active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2. Create Image if exists
                if (!empty($product->image)) {
                    DB::table('product_images')->insert([
                        'product_id' => $product->id,
                        'image_path' => $product->image,
                        'is_primary' => true,
                        'sort_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 3. Create Description if exists
                if (!empty($product->description)) {
                    DB::table('product_descriptions')->insert([
                        'product_id' => $product->id,
                        'title'      => 'Deskripsi',
                        'content'    => $product->description,
                        'sort_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 4. Update order_items to point to the new variant
                DB::table('order_items')
                    ->where('product_id', $product->id)
                    ->update(['product_variant_id' => $variantId]);

                // 5. Update stock_movements to point to the new variant
                DB::table('stock_movements')
                    ->where('product_id', $product->id)
                    ->update(['product_variant_id' => $variantId]);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            DB::commit();
            $this->info('Data migration completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Data migration failed: ' . $e->getMessage());
        }
    }
}
