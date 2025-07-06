<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ProductFeedController extends Controller
{
    public function generateFeed()
    {
        $products = Product::where('status', 1)->get();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"/>');
        $xml->addAttribute('xmlns:g', 'http://base.google.com/ns/1.0');

        $channel = $xml->addChild('channel');
        $channel->addChild('title', 'Facebook Catalog');
        $channel->addChild('link', 'https://varsityheadwear.com/');
        $channel->addChild('description', 'A sample catalog for Facebook Shop');

        foreach ($products as $product) {
            $item = $channel->addChild('item');
            $item->addChild('g:id', htmlspecialchars($product->sku ?? 'N/A'));
            $item->addChild('g:title', htmlspecialchars($product->name_en));
            $item->addChild('g:description', htmlspecialchars($product->description_en ?? ''));

            $availability = $product->stock > 0 ? 'in stock' : 'out of stock';
            $item->addChild('g:availability', $availability);
            $item->addChild('g:condition', 'new');

            $formattedPrice = number_format($product->price, 2) . ' KWD';
            $item->addChild('g:price', $formattedPrice);

            $productUrl = $product->link;
            $item->addChild('g:link', $productUrl);
            $imageUrl = Str::startsWith($product->image, ['http://', 'https://'])
                ? $product->image
                : asset('storage/' . ltrim($product->image, '/'));

            $item->addChild('g:image_link', $imageUrl);

            $item->addChild('g:brand', htmlspecialchars($product->brand ?? 'N/A'));
        }

        return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
    }
}
