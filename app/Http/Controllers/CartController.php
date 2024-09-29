<?php

namespace App\Http\Controllers;

use App\Models\InvCart;
use App\Models\InvCartKoop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        return view('pages.cart.cart');
    }

    public function destroy($id)
    {
        if (Session::has('buying_for_customer_id')) {
            $customerId = Session::get('buying_for_customer_id');
            InvCartKoop::where('id', $id)
                ->where('user_id', $customerId)
                ->delete();
        } else {
            InvCart::where('id', $id)
                ->where('user_id', auth()->user()->id)
                ->delete();
        }

        return response()->json([
            'success' => true
        ]);
    }
}
