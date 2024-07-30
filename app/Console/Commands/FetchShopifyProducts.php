<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Poojaitemlists;

class FetchShopifyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:shopify-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch products from Shopify and save to database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $baseUrl = 'https://33croresadmin.myshopify.com/admin/api/2024-01/products.json';
        $auth = config('services.shopify.api_key') . ':' . config('services.shopify.api_password');
        $nextPageInfo = null;

        do {
            $url = $baseUrl;
            $queryParams = [];

            if ($nextPageInfo) {
                $queryParams['page_info'] = $nextPageInfo;
            }

            $response = Http::withBasicAuth(config('services.shopify.api_key'), config('services.shopify.api_password'))
                ->get($url, $queryParams);

            if ($response->successful()) {
                $products = $response->json('products');

                if (!$products) {
                    $this->error('No products found.');
                    break;
                }

                foreach ($products as $product) {
                    Poojaitemlists::updateOrCreate(
                        ['product_id' => $product['id']], // Unique identifier
                        [
                            'item_name' => $product['title'],
                            'slug' => $product['handle'],
                            'product_type' => $product['product_type'],
                            'status' => 'active',
                        ]
                    );
                }

                // Check if there is a link for the next page
                $linkHeader = $response->header('Link');
                $nextPageInfo = $this->getNextPageInfo($linkHeader);
            } else {
                $this->error('Failed to fetch products. Response: ' . $response->body());
                break;
            }
        } while ($nextPageInfo);

        $this->info('Products have been fetched and saved successfully.');
        return Command::SUCCESS;
    }

    /**
     * Parse the Link header to get the next page info.
     *
     * @param string|null $linkHeader
     * @return string|null
     */
    protected function getNextPageInfo($linkHeader)
    {
        if ($linkHeader) {
            preg_match('/<([^>]+)>; rel="next"/', $linkHeader, $matches);
            if (isset($matches[1])) {
                $urlParts = parse_url($matches[1]);
                parse_str($urlParts['query'], $queryParams);
                return $queryParams['page_info'] ?? null;
            }
        }

        return null;
    }
}
