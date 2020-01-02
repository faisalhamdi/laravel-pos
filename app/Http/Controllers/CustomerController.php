<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function search(Request $r) {
        $this->validate($r, [
            'email' => 'required|email'
        ]);

        $customer = Customer::where('email', $r->email)->first();
        if ($customer) {
            return response()->json([
                'status' => 'success',
                'data' => $customer
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'data' => []
        ]);
    }
}
