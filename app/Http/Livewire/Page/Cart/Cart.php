<?php

namespace App\Http\Livewire\Page\Cart;

use App\Models\CommissionRateKap;
use App\Models\InvCart;
use App\Models\MarketPrice;
use App\Models\Promotion;
use App\Models\InvCartKoop;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Cart extends Component
{
    public $prod_qty, $type;
    public $total, $comm;
    public $karts, $marketP, $commR;
    public $info_061, $info_062, $info_063, $info_064, $info_065;
    public $qty_061, $qty_062, $qty_063, $qty_064, $qty_065;
    public $promo_code;
    public $customerName;

    protected $listeners = ['buyingForCustomerCleared' => '$refresh'];

    public function mount()
    {
        if (Session::has('buying_for_customer_id')) {
            $customerId = Session::get('buying_for_customer_id');
            $customer = User::find($customerId);
            $this->customerName = $customer ? $customer->name : 'Unknown Customer';
            Session::put('buying_for_customer_name', $this->customerName);
        }
    }

    public function clearBuyingForCustomer()
    {
        Session::forget(['buying_for_customer_id', 'buying_for_customer_name']);
        $this->emit('buyingForCustomerCleared');
    }

    public function subProd($cartID)
    {
        $cartItem = Session::has('buying_for_customer_id') ? InvCartKoop::where('id', $cartID)->first() : InvCart::where('id', $cartID)->first();
        $this->total -= $cartItem->products->item->marketPrice->price;
        if ((auth()->user()->isAgentKAP())) {
            $this->comm -= $cartItem->commission->agent_rate;
        }
        if ($cartItem->prod_qty != 1) {
            $cartItem->prod_qty -= 1;
            $cartItem->save();
        } else {
            $cartItem->delete();
        }
    }

    public function addProd($cartID)
    {
        $cartItem = Session::has('buying_for_customer_id') ? InvCartKoop::where('id', $cartID)->first() : InvCart::where('id', $cartID)->first();
        $this->total += $cartItem->products->item->marketPrice->price;
        if ((auth()->user()->isAgentKAP())) {
            $this->comm += $cartItem->commission->agent_rate;
        }
        if ($cartItem->prod_qty != 99) {
            $cartItem->prod_qty += 1;
            $cartItem->save();
        } else {
            $cartItem->delete();
        }
    }

    public function calculatePromo()
    {
        $this->validate([
            'promo_code' => 'required|max:6'
        ]);

        $code = Promotion::where('promo_code', $this->promo_code)->first();
        $current_date = now()->format('Y-m-d');
        $start_date = $code->start_date ?? '';
        $end_date = $code->end_date ?? '';
        $promo_period = false;

        if (($current_date >= $start_date) && ($current_date <= $end_date)) {
            $promo_period = true;
        }

        if (!$code) {
            $this->emit('message', 'Invalid Promotion Code.');
        } elseif ($code && $promo_period == false) {
            $this->emit('message', 'Promotion Code expired.');
        }
    }

    public function render()
    {
        $this->total = 0;
        $this->comm = 0;

        if (Session::has('buying_for_customer_id')) {
            $customerId = Session::get('buying_for_customer_id');
            $this->karts = InvCartKoop::where('exit_type', NULL)
                ->with('item.promotions')
                ->where('user_id', $customerId)
                ->get();
        } else {
            $this->karts = InvCart::where('exit_type', NULL)
                ->with('item.promotions')
                ->where('user_id', auth()->user()->id)
                ->get();
        }

        $currentDate = date('Y-m-d');
        $currentDate = date('Y-m-d', strtotime($currentDate));

        foreach ($this->karts as $kart) {
            if ($kart->item->promotions != NULL && ($currentDate >= $kart->item->promotions->start_date) && ($currentDate <= $kart->item->promotions->end_date)) {
                $this->total += $kart->products->item->promotions->promo_price * $kart->prod_qty;
            } elseif ($kart->products->prod_cat == 3) {
                $this->total += ($kart->products->item->marketPrice->price + round(($kart->products->item->marketPrice->price * $kart->percentage()), 2)) * $kart->prod_gram;
            } else {
                $this->total += $kart->products->item->marketPrice->price * $kart->prod_qty;
            }

            if ((auth()->user()->isAgentKAP())) {
                $this->comm += $kart->commission->agent_rate * $kart->prod_qty;
            }
        }

        return view('livewire.page.cart.cart');
    }
}
