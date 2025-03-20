<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
class DashboardController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request): Response
    {
        
        // $users = [
        //     ['name' => 'Devi', 'image' => '/images/devi.jpg'],
        //     ['name' => 'John', 'image' => '/images/john.jpg'],
        //     ['name' => 'Mary', 'image' => '/images/mary.jpg'],
        //     ['name' => 'Kris', 'image' => '/images/kris.jpg'],
        // ];

        // $balances = [
        //     ['month' => 'Jan', 'value' => 50000],
        //     ['month' => 'Feb', 'value' => 75000],
        //     ['month' => 'Mar', 'value' => 50000],
        //     ['month' => 'Apr', 'value' => 50000],
        //     ['month' => 'May', 'value' => 50000],
        //     ['month' => 'Jun', 'value' => 70685],
        //     ['month' => 'Jul', 'value' => 50000],
        //     ['month' => 'Aug', 'value' => 75000],
        //     ['month' => 'Sep', 'value' => 75000],
        //     ['month' => 'Oct', 'value' => 50000],
        //     ['month' => 'Nov', 'value' => 50000],
        // ];


          // Fetch total sales per month for the last 4 months
        // $salesData = Order::selectRaw('SUM(total_amount) as total, MONTH(created_at) as month')
        // ->where('created_at', '>=', Carbon::now()->subMonths(4)) // Last 4 months
        // ->groupBy('month')
        // ->orderBy('month')
        // ->get();

        // Format the data for Chart.js
        // $labels = [];
        // $data = [];

        // foreach ($salesData as $sale) {
        //     $labels[] = Carbon::create()->month($sale->month)->format('F'); // Convert month number to name
        //     $data[] = $sale->total;
        // }


        // $chartData = [
        //     'labels' => $labels,
        //     'datasets' => [
        //         [
        //             'label' => 'Sales (USD)',
        //             'backgroundColor' => '#6451FF',
        //             'borderColor' => '#6451FF',
        //             'borderWidth' => 1,
        //             'data' => $data,
        //         ],
        //     ],
        // ];
        

        // $chartData = [
        //     'labels' => ['January', 'February', 'March', 'April', 'May', 'Jun','July','Augest', 'September', 'October', 'November', 'December'],
        //     'datasets' => [
        //         [
        //             'label' => 'Sales',
        //             'backgroundColor' => '#6451FF',
        //             'borderColor' => '#6451FF',
        //             'borderWidth' => 1,
        //             'data' => [65, 59, 80, 81,89,50,78,99, 55, 86, 33, 45],
        //         ],
        //     ],
        // ];



        // dd([
        //     'Laravel Timezone' => config('app.timezone'),
        //     'Server Timezone' => date_default_timezone_get(),
        //     'Carbon Now' => Carbon::now()->toDateTimeString(),
        //     'Carbon Today' => Carbon::today()->toDateString(),
        //     'Carbon UTC Now' => Carbon::now('UTC')->toDateTimeString(),
        //     'Carbon UTC Today' => Carbon::today('UTC')->toDateString(),
        //     'Carbon Asia/Kabul Now' => Carbon::now('Asia/Kabul')->toDateTimeString(),
        //     'Carbon Asia/Kabul Today' => Carbon::today('Asia/Kabul')->toDateString(),
        //     'Latest Order Date' => Order::latest('created_at')->value('created_at'),
        // ]);
     
            // Get the current week's number (Saturday as the first day)
            $currentWeek = Carbon::now()->weekOfYear;
            $currentYear = Carbon::now()->year;

            // Fetch total sales per day for the current week (Saturday–Friday)
            $salesDataDaily = Order::selectRaw('SUM(total_amount) as total_sales, WEEKOFYEAR(created_at) as week_number, DAYOFWEEK(created_at) as day_number')
                ->whereYear('created_at', $currentYear)
                ->whereRaw('WEEKOFYEAR(created_at) = ?', [$currentWeek]) // Fetch only current week's data
                ->groupBy('week_number', 'day_number')
                ->orderBy('day_number')
                ->get();

            // Initialize data arrays for full week (Saturday - Friday)
            $labels = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
            $data = array_fill(0, 7, 0); // Default 0 for all 7 days
            $textData = [];

            // Get current date in 'YYYY-MM-DD' format
            $currentDay = Carbon::today('UTC')->toDateTimeString();
    
            // Calculate total sales for the current day
            $totalSalesDay = Order::whereDate('created_at', $currentDay)->sum('total_amount');
            

            foreach ($salesDataDaily as $sale) {
                $dayIndex = $sale->day_number - 1; // MySQL Sunday = 1, Saturday = 7

                // Adjust the index so that Saturday starts first
                if ($dayIndex == 6) {
                    $dayIndex = 0; // Saturday should be index 0
                } else {
                    $dayIndex += 1; // Shift the rest accordingly
                }
            
                $data[$dayIndex] = $sale->total_sales;
                $textData[] = [
                    'day' => $labels[$dayIndex],
                    'total_sales' => number_format($sale->total_sales, 2)
                ];
            }

            // Format chart data
            $chartDataSalesDaily = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Sales per Day (USD)',
                        'backgroundColor' => '#6451FF',
                        'borderColor' => '#6451FF',
                        'borderWidth' => 1,
                        'data' => $data,
                    ],
                ],
            ];


            // Fetch total customers per month for the current year
            $customerData = Customer::selectRaw('COUNT(id) as total_customers, MONTH(created_at) as month')
            ->whereYear('created_at', Carbon::now()->year) // Filter for the current year
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            // Calculate total customers for the current year
            $totalCustomers = $customerData->sum('total_customers');

            // Format data for Chart.js
            $labels = [];
            $data = [];

            foreach ($customerData as $customer) {
                $labels[] = Carbon::create()->month($customer->month)->format('F'); // Convert month number to name
                $data[] = $customer->total_customers;
            }

            // Prepare chart data for Vue
            $CustomerChartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'New Customers',
                        'backgroundColor' => '#5A52E6',
                        'borderColor' => '#5A52E6',
                        'borderWidth' => 1,
                        'data' => $data,
                    ],
                ],
            ];




            // Fetch total orders per month for the current year
            $TotalOrdersData = Order::selectRaw('COUNT(id) as total_orders, MONTH(created_at) as month')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            $totalOrders = $TotalOrdersData->sum('total_orders');

            $labels = [];
            $data = [];

            foreach ($TotalOrdersData as $order) {
                $labels[] = Carbon::create()->month($order->month)->format('F');
                $data[] = $order->total_orders;
            }

            $OrderChartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'New Orders',
                        'backgroundColor' => '#5A52E6',
                        'borderColor' => '#5A52E6',
                        'borderWidth' => 1,
                        'data' => $data,
                    ],
                ],
            ];



            // Fetch total sales per month
           $SalesDataMonthly = Order::selectRaw('SUM(total_amount) as total_amount, MONTH(created_at) as month')
           ->whereYear('created_at', Carbon::now()->year) // Only for the current year
           ->groupBy('month')
           ->orderBy('month')
           ->get();

            // Calculate total sales for the entire year
             $totalSalesMonthly = Order::whereMonth('created_at', Carbon::now()->month)->sum('total_amount');

            // Format data for Chart.js
            $labels = [];
            $data = [];

            foreach ($SalesDataMonthly as $sale) {
                $labels[] = Carbon::create()->month($sale->month)->format('F'); // Convert month number to name
                $data[] = $sale->total_amount;
            }

            // Chart data structure for Vue
            $SalesChartDataMonthly = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Sales (USD)',
                        'backgroundColor' => '#6451FF',
                        'borderColor' => '#6451FF',
                        'borderWidth' => 1,
                        'data' => $data,
                    ],
                ],
            ];


            
        
        // Fetch top 5-10 products by total sales
        $topProducstData = Product::selectRaw('products.product_name, SUM(orders.total_amount) as top_sale_products')
        ->join('orders', 'orders.product_id', '=', 'products.id')
        ->groupBy('products.id', 'products.product_name')
        ->orderByDesc('top_sale_products')
        ->limit(10)
        ->get();

        // Format data for Chart.js
        $labels = [];
        $data = [];

        foreach ($topProducstData as $product) {
            $labels[] = $product->product_name;      // Product names for labels
            $data[] = $product->top_sale_products; // Total sales for each product
        }

        // Prepare chart data for Vue
        $topProductsChartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Top 5 Products Sales',
                    'backgroundColor' => '#6451FF',
                    'borderColor' => '#6451FF',
                    'borderWidth' => 1,
                    'data' => $data,
                ],
            ],
        ];

        // Fetch low stock products and out of stock products 
        $stockData = Product::selectRaw("
        SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
        SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= 10 THEN 2 ELSE 0 END) as low_stock
        ")
        ->first();

        $labels = ['Out of Stock', 'Low Stock'];
        $data = [$stockData->out_of_stock, $stockData->low_stock];

        $StockChartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Stock Status',
                'backgroundColor' => ['#FF6384', '#FFCE56'],
                'borderColor' => '#ccc',
                'borderWidth' => 1,
                'data' => $data,
            ],
        ],
        ];

        $bestSellingProducts = Product::select('products.product_name', \DB::raw('SUM(order_details.order_quantity) as total_sold'))
        ->join('orders', 'orders.product_id', '=', 'products.id') 
        ->join('order_details', 'order_details.order_id', '=', 'orders.id') 
        ->groupBy('products.id', 'products.product_name')
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();


        $lowPerformingProducts = Product::select('products.product_name', \DB::raw('COALESCE(SUM(order_details.order_quantity), 0) as total_sold'))
        ->leftJoin('orders', 'orders.product_id', '=', 'products.id') 
        ->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
        ->groupBy('products.id', 'products.product_name')
        ->orderBy('total_sold')
        ->limit(5)
        ->get();


        $lowStockProducts = Product::where('stock_quantity', '<=', 10)->get();


        // financial reports
        $period = $request->input('period', 'monthly'); // Default to monthly

        switch ($period) {
            case 'daily':
                $dateFormat = '%Y-%m-%d';
                break;
            case 'weekly':
                $dateFormat = '%Y-%u';
                break;
            case 'yearly':
                $dateFormat = '%Y';
                break;
            default:
                $dateFormat = '%Y-%m';
                break;
        }

        $financialData = \DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '$dateFormat') as period, 
                SUM(total_amount) as total_sales, 
                SUM(discount_amount) as total_returns, 
                SUM(total_amount) - SUM(discount_amount) as net_sales")
            ->groupBy('period')
            ->get();


            // $profitLossData = \DB::table(function($query) {
            //     $query->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, 
            //                        SUM(CAST(total_amount AS DECIMAL(10, 2))) as profit, 
            //                        0 as expense")
            //           ->from('orders')
            //           ->groupBy('period')
            //           ->unionAll(
            //               \DB::table('expenses')
            //                 ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, 
            //                              0 as profit, 
            //                              SUM(CAST(expense_total AS DECIMAL(10, 2))) as expense")
            //                 ->groupBy('period')
            //           );
            // }, 'combined')
            // ->selectRaw("period, 
            //              SUM(profit) as total_profit, 
            //              SUM(expense) as total_expense, 
            //              SUM(profit) - SUM(expense) as net_profit")
            // ->groupBy('period')
            // ->get();

            $profitLossData = \DB::table(function($query) {
                $query->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, 
                                   SUM(CAST(total_amount AS DECIMAL(10, 2))) as profit, 
                                   0 as expense")
                      ->from('orders')
                      ->groupBy('period')
                      ->unionAll(
                          \DB::table('expenses')
                            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, 
                                         0 as profit, 
                                         SUM(CAST(expense_total AS DECIMAL(10, 2))) as expense")
                            ->groupBy('period')
                      );
            }, 'combined')
            ->selectRaw("period, 
                         SUM(profit) as total_profit, 
                         SUM(expense) as total_expense, 
                         SUM(profit) - SUM(expense) as net_profit")
            ->groupBy('period')
            ->get();
            
            $labels = $profitLossData->pluck('period')->toArray();
            $profitData = $profitLossData->pluck('total_profit')->toArray();
            $expenseData = $profitLossData->pluck('total_expense')->toArray();

            $ProfitLossChartData = [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Profit',
                        'backgroundColor' => '#4CAF50',  // شین رنګ د ګټې لپاره
                        'borderColor' => '#388E3C',
                        'borderWidth' => 1,
                        'data' => $profitData,
                    ],
                    [
                        'label' => 'Expense',
                        'backgroundColor' => '#FF6384',  // سور رنګ د لګښت لپاره
                        'borderColor' => '#D32F2F',
                        'borderWidth' => 1,
                        'data' => $expenseData,
                    ],
                ],
            ];

        return Inertia::render('Dashboard', [
            'salesDataDaily' => $salesDataDaily,
            'chartDataSalesDaily' => $chartDataSalesDaily,
            'textData' => $textData, // Send text data for display
            'totalSalesDay' => number_format($totalSalesDay),
            'currentWeek' => $currentWeek,
            'customerData' => $customerData,
            'CustomerChartData' => $CustomerChartData,
            'totalCustomers' => $totalCustomers,
            'TotalOrdersData' => $TotalOrdersData,
            'totalOrders' => $totalOrders,
            'OrderChartData' => $OrderChartData,
            'SalesDataMonthly' => $SalesDataMonthly,
            'SalesChartDataMonthly' => $SalesChartDataMonthly,
            'totalSalesMonthly' => number_format($totalSalesMonthly),
            'topProducstData' => $topProducstData,
            'topProductsChartData' => $topProductsChartData,
            'stockData' => $stockData,
            'StockChartData' => $StockChartData,
            'bestSellingProducts' => $bestSellingProducts,
            'lowPerformingProducts' => $lowPerformingProducts,
            'lowStockProducts' => $lowStockProducts,
            'financialData' => $financialData,
            'period' => $period,
            'profitLossData' => $profitLossData,
            'ProfitLossChartData' => $ProfitLossChartData
        ]);
    }

   
}
