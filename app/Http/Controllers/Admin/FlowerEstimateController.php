<?php


namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Subscription;
use App\Models\FlowerProduct;
use App\Http\Controllers\Controller;

class FlowerEstimateController extends Controller
{
    public function index(Request $request)
    {
        // Parse range, default to today → today
        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::today();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();

        // Swap if out of order
        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // Build the date period (inclusive)
        $period = CarbonPeriod::create($start->toDateString(), $end->toDateString());

        /**
         * We’ll compute per-day estimates like:
         * $dailyEstimates[‘YYYY-MM-DD’] = [
         *   'products' => [
         *      product_id => [
         *        'product' => FlowerProduct,
         *        'subs_count' => int,
         *        'items' => [
         *           [ 'item_name', 'unit', 'per_item_qty', 'per_item_price', 'total_qty', 'total_price' ]
         *        ],
         *        'product_total' => float
         *      ],
         *   ],
         *   'grand_total_amount' => float
         * ];
         */

        $dailyEstimates = [];

        foreach ($period as $day) {
            /** @var \Illuminate\Support\Collection $subs */
            $subs = Subscription::with([
                'flowerProducts:id,product_id,name',
                'flowerProducts.packageItems:product_id,item_name,quantity,unit,price',
            ])
            ->activeOn($day) // uses your scopeActiveOn(Carbon $date)
            ->get();

            // Group subscriptions by product_id
            $byProduct = $subs->groupBy('product_id');

            $productsForDay = [];
            $grandTotalForDay = 0;

            foreach ($byProduct as $productId => $subsForProduct) {
                // One product instance from the group
                $product = optional($subsForProduct->first())->flowerProducts;

                $subsCount = $subsForProduct->count();

                // Package items and their per-subscription quantities/prices
                $items = [];
                $productTotal = 0;

                if ($product) {
                    foreach ($product->packageItems as $pi) {
                        $perItemQty = (float) ($pi->quantity ?? 0); // per subscription
                        $unit = $pi->unit ?? '';
                        $price = (float) ($pi->price ?? 0); // price per unit
                        $totalQty = $perItemQty * $subsCount;
                        $totalPrice = $price * $totalQty;

                        $items[] = [
                            'item_name'      => $pi->item_name,
                            'unit'           => $unit,
                            'per_item_qty'   => $perItemQty,
                            'per_item_price' => $price,
                            'total_qty'      => $totalQty,
                            'total_price'    => $totalPrice,
                        ];

                        $productTotal += $totalPrice;
                    }
                }

                $grandTotalForDay += $productTotal;

                $productsForDay[$productId] = [
                    'product'      => $product,
                    'subs_count'   => $subsCount,
                    'items'        => $items,
                    'product_total'=> $productTotal,
                ];
            }

            $dailyEstimates[$day->toDateString()] = [
                'products'            => $productsForDay,
                'grand_total_amount'  => $grandTotalForDay,
            ];
        }

        return view('admin.reports.flower-estimates', [
            'start'          => $start->toDateString(),
            'end'            => $end->toDateString(),
            'dailyEstimates' => $dailyEstimates,
        ]);
    }
}
