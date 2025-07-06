<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class ImportProductsFromXml extends Command
{
    protected $signature = 'products:import {file}';
    protected $description = 'Import products from XML file';

    public function handle()
    {
        // $filePath = $this->argument('file');
        $filePath = storage_path('app/products/products_file.xml'); // for example

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        $xmlContent = file_get_contents($filePath);
        $xml = simplexml_load_string($xmlContent);

        if (!$xml) {
            $this->error("Invalid XML format.");
            return 1;
        }

        $count = 0;
        foreach ($xml->channel->item as $item) {
            $g = $item->children('http://base.google.com/ns/1.0');

            $sku = (string) $g->id;
            $nameEn = (string) $g->title;
            $descriptionEn = (string) $g->description;
            $brand = (string) $g->brand;
            $link = (string) $g->link;

            // Parse price and currency
            preg_match('/([\d.]+)\s*(\w+)/', (string) $g->price, $matches);
            $price = isset($matches[1]) ? floatval($matches[1]) : 0.0;
            $currency = $matches[2] ?? 'KWD';

            // Map availability to status (e.g. out of stock => 0, in stock => 1)
            $availability = strtolower((string) $g->availability);
            $status = ($availability === 'in stock') ? 1 : 0;

            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'name_en' => $nameEn,
                    'name_ar' => $nameEn,
                    'description_en' => $descriptionEn,
                    'description_ar' => $descriptionEn,
                    'brand' => $brand,
                    'price' => $price,
                    'stock' => $status ? 10 : 0, // Default stock if needed
                    'image' => (string) $g->image_link,
                    'link' => $link,
                    'status' => $status,
                ]
            );
            $count++;
        }

        $this->info("Imported/updated $count products from XML.");

        return 0;
    }
}
