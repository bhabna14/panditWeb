<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Poojaitemlists;

use App\Models\Variant;

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
      
        $nextPageInfo = null;

        do {
            $url = $baseUrl;
            $queryParams = [
                'limit' => 250, // Maximum number of results per page (adjust if needed)
            ];

            if ($nextPageInfo) {
                $queryParams['page_info'] = $nextPageInfo;
            }

            $response = Http::withBasicAuth($auth, '')
                ->get($url, $queryParams);

            if ($response->successful()) {
                $products = $response->json('products');

                if (!$products) {
                    $this->error('No products found.');
                    break;
                }

                foreach ($products as $productData) {
                    $product = Poojaitemlists::updateOrCreate(
                        ['product_id' => $productData['id']],
                        [
                            'item_name' => $productData['title'],
                            'slug' => $productData['handle'],
                            'product_type' => $productData['product_type'],
                            'status' => $productData['status']
                        ]
                    );

                    foreach ($productData['variants'] as $variantData) {
                        Variant::updateOrCreate(
                            ['variant_id' => $variantData['id']],
                            [
                                'product_id' => $product->id,
                                'title' => $variantData['title'],
                                'price' => $variantData['price']
                            ]
                        );
                    }
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
