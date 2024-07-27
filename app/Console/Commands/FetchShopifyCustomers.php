<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;

class FetchShopifyCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:shopify-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch customers from Shopify created in the year 2024 to the present and save to database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $baseUrl = config('services.shopify.api_url');
        $auth = config('services.shopify.api_key') . ':' . config('services.shopify.api_password');
        $nextPageInfo = null;
        $createdAtMin = '2024-01-01T00:00:00Z';
        $createdAtMax = now()->toIso8601String(); // Current date and time

        do {
            $url = $baseUrl;
            $queryParams = [
                'limit' => 250, // Maximum number of results per page (adjust if needed)
            ];

            if (!$nextPageInfo) {
                $queryParams['created_at_min'] = $createdAtMin;
                $queryParams['created_at_max'] = $createdAtMax;
            } else {
                $queryParams['page_info'] = $nextPageInfo;
            }

            $response = Http::withBasicAuth(config('services.shopify.api_key'), config('services.shopify.api_password'))
                ->get($url, $queryParams);

            if ($response->successful()) {
                $customers = $response->json('customers');

                if (!$customers) {
                    $this->error('No customers found.');
                    break;
                }

                foreach ($customers as $customer) {
                    Customer::updateOrCreate(
                        ['customer_id' => $customer['id']], // Use a unique identifier, like id
                        [
                            'name' => $customer['first_name'],
                            'email' => $customer['email'],
                            'phone' => $customer['phone']
                        ]
                    );
                }

                // Check if there is a link for the next page
                $linkHeader = $response->header('Link');
                $nextPageInfo = $this->getNextPageInfo($linkHeader);
            } else {
                $this->error('Failed to fetch customers. Response: ' . $response->body());
                break;
            }
        } while ($nextPageInfo);

        $this->info('Customers have been fetched and saved successfully.');
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
