<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseHistoryController extends Controller
{
    public function index()
    {
        return view('pages.purchase-history.purchase-history');
    }
}
