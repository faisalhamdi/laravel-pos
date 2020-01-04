<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Order;
use App\Product;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /* public function __construct()
    {
        $this->middleware('auth');
    } */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $product = Product::count();
        $order = Order::count();
        $customer = Customer::count();
        $user = User::count();

        return view('home', compact('product', 'order', 'customer', 'user'));
    }

    public function getChart() {
        // get date 7 days before
        $start = Carbon::now()->subWeek()->addDay()->format('Y-m-d') . ' 00:00:01';
        // get date now
        $end = Carbon::now()->format('Y-m-d') . ' 23:59:59';

        $order = Order::select(DB::raw('date(created_at) as order_date'), DB::raw('count(*) as total_order'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('created_at')
            ->get()->pluck('total_order', 'order_date')->all();

        // looping interval 7 days before
        for ($i = Carbon::now()->subWeek()->addDay(); $i <= Carbon::now(); $i->addDay()) {
            if (array_key_exists($i->format('Y-m-d'), $order)) {
                // total order pushed with date key
                $data[$i->format('Y-m-d')] = $order[$i->format('Y-m-d')];
            } else {
                $data[$i->format('Y-m-d')] = 0;
            }
        }
        return response()->json($data); 
    }
}
