<?php

namespace App\Http\Livewire\Page\Shop;

use App\Models\CommissionDetailKap;
use App\Models\CommissionPromotion;
use App\Models\FiuuBills;
use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\GoldbarOwnershipPending;
use Illuminate\Support\Str;
use App\Models\InvCart;
use App\Models\InvCartKoop;
use App\Models\InvMaster;
use App\Models\NewOrders;
use App\Models\Promotion;
use App\Models\SnapNPay;
use App\Models\States;
use App\Models\ToyyibBills;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ProductBuy extends Component
{

    public $iid, $item_id;
    public $fname, $lname, $cname, $nric;
    public $address1, $address2, $address3, $town, $postcode, $state_id;
    public $promo_code, $apply_code_type, $apply_code;
    public $selectPayment;


    public function select_payment($selectPayment)
    {
        $this->selectPayment = $selectPayment;
    }


    public function mount()
    {
        $this->state               = States::all();
        $this->redipay = 0;
    }

    public function updated($field)
    {
        $this->validateOnly($field, [
            'fname'               => 'required|string',
            'lname'               => 'required|string',
            'cname'               => 'required|string',
            'nric'                => 'required|digits:12',
            'address1'            => 'required|string',
            'address2'            => 'nullable|string',
            'address3'            => 'nullable|string',
            'town'                => 'required|string',
            'postcode'            => 'required|digits:5',
            'state_id'            => 'required|integer',
        ]);
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
            $this->emit('message', ['icon' => 'error', 'message' => 'Invalid Promotion Code.']);
        } elseif ($code && $promo_period == false) {
            $this->emit('message', ['icon' => 'error', 'message' => 'Promotion Code expired.']);
        } else {
            $this->emit('message', ['icon' => 'success', 'message' => 'Promotion Code Applied.']);
            $this->apply_code_type = ucfirst($code->types->description);
            $this->apply_code = $code->promo_code;
        }

        $this->promo_code = '';
    }

    public function payOrder($payment_method)
    {
        return match($payment_method) {
            'toyyib' => $this->buy(),
            'snapNpay' => $this->buySnapNpay(),
            'fiuu' => $this->buyFiuu(),
            default => throw new \InvalidArgumentException("Invalid payment method: {$payment_method}")
        };
    }

    public function buy()
    {
        $products = Session::has('buying_for_customer_id')
            ? InvCartKoop::with('item.promotions')->where('user_id', Session::get('buying_for_customer_id'))->get()
            : InvCart::with('item.promotions')->where('user_id', auth()->user()->id)->get();
        $total = 1.0; //Total is RM1 because of FPX Payment
        $comm = 0.0;
        $currentDate = date('Y-m-d');
        $currentDate = date('Y-m-d', strtotime($currentDate));

        foreach ($products as $prod) {
            $comm += $prod->commission->agent_rate * $prod->prod_qty;

            if ($prod->products->item->promotions != NULL && ($currentDate >= $prod->products->item->promotions->start_date) && ($currentDate <= $prod->products->item->promotions->end_date)) {
                $total += $prod->products->item->promotions->promo_price * $prod->prod_qty;
            } elseif ($prod->products->prod_cat == 3) {
                $total += ($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram;
            } else {
                $total += $prod->products->item->marketPrice->price * $prod->prod_qty;
            }
        }
        $refPayment = (string) Str::uuid();

        $option = array(
            'userSecretKey' => config('toyyibpay.key'),
            'categoryCode' => config('toyyibpay.category'),
            'billName' => 'Kasih AP Digital',
            'billDescription' => 'Digital Gold Purchase',
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => Session::has('buying_for_customer_id')
                ? ($total * 100)
                : (auth()->user()->isAgentKAP() ? (($total - $comm) * 100) : ($total * 100)),
            'billReturnUrl' => route('toyyibpay-status-buy'),
            'billCallbackUrl' => route('toyyibpay-callback'),
            'billExternalReferenceNo' => $refPayment,
            'billTo' => Session::has('buying_for_customer_id') ? User::find(Session::get('buying_for_customer_id'))->name : auth()->user()->name,
            'billEmail' => Session::has('buying_for_customer_id') ? User::find(Session::get('buying_for_customer_id'))->email : auth()->user()->email,
            'billPhone' => Session::has('buying_for_customer_id')
                ? (User::find(Session::get('buying_for_customer_id'))->role == 3
                    ? User::find(Session::get('buying_for_customer_id'))->profile->phone1
                    : User::find(Session::get('buying_for_customer_id'))->phone_no)
                : (auth()->user()->role == 3 ? auth()->user()->profile->phone1 : auth()->user()->phone_no),
            'billSplitPayment' => 0,
            'billSplitPaymentArgs' => '',
            'billPaymentChannel' => '0',
            'billContentEmail' => 'Thank you for purchasing our product!',
            'billChargeToCustomer' => 1
        );

        $url = 'https://dev.toyyibpay.com/index.php/api/createBill';
        $response = Http::asForm()->post($url, $option);
        $billCode = $response[0]['BillCode'];

        // if promo code applied
        if ($this->apply_code) {
            $cart = $products->map(function ($item, $key) {
                return [
                    'item_id' => $item->item_id,
                    'prod_qty' => $item->prod_qty
                ];
            });

            $prod_json = [];
            $prod_json = $cart->toArray();

            CommissionPromotion::create([
                'user_id' => auth()->user()->id,
                'product' => json_encode($prod_json),
                'promo_code' => $this->apply_code,
                'billCode' => $billCode
            ]);
        }

        ToyyibBills::create([
            'ref_payment'       => $refPayment,
            'bill_code'         => $billCode,
            'bill_amount' => Session::has('buying_for_customer_id')
                ? ($total)
                : (auth()->user()->isAgentKAP() ? (($total - $comm)) : ($total)),
            // 'bill_amount'       => (auth()->user()->isAgentKAP()) ? (($total - $comm)) : ($total),
            'status'            => 2,
            'created_by'        => auth()->user()->id,
            'updated_by'        => auth()->user()->id,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);


        foreach ($products as $prod) { //Filling all these request to Goldbar Ownership Pending
            // Search for available gold bar to be filled
            $goldbar = Goldbar::where('weight_vacant', '>=', $prod->products->prod_weight)->first();
            for ($i = 0; $i < $prod->prod_qty; $i++) {
                GoldbarOwnershipPending::create([
                    'referenceNumber'   => $billCode,
                    'gold_id'           => $goldbar->id,
                    'user_id'           => Session::has('buying_for_customer_id') ? Session::get('buying_for_customer_id') : auth()->user()->id,
                    'item_id'           => $prod->item_id,
                    'weight'            => ($prod->products->prod_cat == 3 ? $prod->prod_gram : $prod->products->prod_weight),
                    'bought_price' => Session::has('buying_for_customer_id')
                        ? ($prod->products->prod_cat != 3
                            ? $prod->products->item->marketPrice->price
                            : ($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram)
                        : (auth()->user()->isAgentKAP()
                            ? ($prod->products->prod_cat != 3
                                ? ($prod->products->item->marketPrice->price - $prod->commission->agent_rate)
                                : ((($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) - $prod->commission->agent_rate) * $prod->prod_gram))
                            : ($prod->products->prod_cat != 3
                                ? $prod->products->item->marketPrice->price
                                : (($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram))),
                    'status'            => 2,
                    'spot_gold'         => ($prod->products->prod_cat == 3 ? 1 : 0),
                    'financing_flag'    => Session::has('buying_for_customer_id') ? 1 : 0,
                    'created_by'        => auth()->user()->id,
                    'updated_by'        => auth()->user()->id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // update available gold bar weight

                $currentGoldbar = Goldbar::where('id', $goldbar->id)->first();

                $currentGoldbar->weight_on_hold += $prod->products->prod_weight;
                $currentGoldbar->weight_vacant -= $prod->products->prod_weight;
                $currentGoldbar->save();
            }
            $prod->delete();
        }

        // Clear the session after successful purchase
        Session::forget(['buying_for_customer_id', 'buying_for_customer_name']);

        return redirect('https://dev.toyyibpay.com/' . $billCode);
    }

    public function buySnapNpay()
    {
        $products = Session::has('buying_for_customer_id')
            ? InvCartKoop::with('item.promotions')->where('user_id', Session::get('buying_for_customer_id'))->get()
            : InvCart::with('item.promotions')->where('user_id', auth()->user()->id)->get();
        $total = 1.0; //Total is RM1 because of FPX Payment
        $comm = 0.0;
        $currentDate = date('Y-m-d');
        $currentDate = date('Y-m-d', strtotime($currentDate));

        foreach ($products as $prod) { // Count total price for the transaction
            $comm += $prod->commission->agent_rate * $prod->prod_qty;

            if ($prod->products->item->promotions != NULL && ($currentDate >= $prod->products->item->promotions->start_date) && ($currentDate <= $prod->products->item->promotions->end_date)) {
                $total += $prod->products->item->promotions->promo_price * $prod->prod_qty;
            } elseif ($prod->products->prod_cat == 3) {
                $total += ($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram;
            } else {
                $total += $prod->products->item->marketPrice->price * $prod->prod_qty;
            }
        }
        $refPayment = (string) Str::uuid();


        // if promo code applied
        if ($this->apply_code) {
            $cart = $products->map(function ($item, $key) {
                return [
                    'item_id' => $item->item_id,
                    'prod_qty' => $item->prod_qty
                ];
            });

            $prod_json = [];
            $prod_json = $cart->toArray();

            CommissionPromotion::create([
                'user_id' => auth()->user()->id,
                'product' => json_encode($prod_json),
                'promo_code' => $this->apply_code,
                'billCode' => $refPayment
            ]);
        }

        SnapNPay::create([
            'ref_no'            => $refPayment,
            'amount' => Session::has('buying_for_customer_id')
                ? ($total)
                : (auth()->user()->isAgentKAP() ? (($total - $comm)) : ($total)),
            // 'amount'            => (auth()->user()->isAgentKAP()) ? (($total - $comm)) : ($total),
            'status'            => 2,
            'created_by'        => auth()->user()->id,
            'updated_by'        => auth()->user()->id,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);


        foreach ($products as $prod) { //Filling all these request to Goldbar Ownership Pending
            // Search for available gold bar to be filled
            $goldbar = Goldbar::where('weight_vacant', '>=', $prod->products->prod_weight)->first();
            for ($i = 0; $i < $prod->prod_qty; $i++) {
                GoldbarOwnershipPending::create([
                    'referenceNumber'   => $refPayment,
                    'gold_id'           => $goldbar->id,
                    'user_id'           => Session::has('buying_for_customer_id') ? Session::get('buying_for_customer_id') : auth()->user()->id,
                    'item_id'           => $prod->item_id,
                    'weight'            => ($prod->products->prod_cat == 3 ? $prod->prod_gram : $prod->products->prod_weight),
                    'bought_price' => Session::has('buying_for_customer_id')
                        ? ($prod->products->prod_cat != 3
                            ? $prod->products->item->marketPrice->price
                            : ($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram)
                        : (auth()->user()->isAgentKAP()
                            ? ($prod->products->prod_cat != 3
                                ? ($prod->products->item->marketPrice->price - $prod->commission->agent_rate)
                                : ((($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) - $prod->commission->agent_rate) * $prod->prod_gram))
                            : ($prod->products->prod_cat != 3
                                ? $prod->products->item->marketPrice->price
                                : (($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram))),
                    'status'            => 2,
                    'spot_gold'         => ($prod->products->prod_cat == 3 ? 1 : 0),
                    'snapNPayFlag'      => 2, // 2 for pending, 0 processed, 1 success , 3 failed
                    'financing_flag'    => Session::has('buying_for_customer_id') ? 1 : 0,
                    'created_by'        => auth()->user()->id,
                    'updated_by'        => auth()->user()->id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // update available gold bar weight

                $currentGoldbar = Goldbar::where('id', $goldbar->id)->first();

                $currentGoldbar->weight_on_hold += $prod->products->prod_weight;
                $currentGoldbar->weight_vacant -= $prod->products->prod_weight;
                $currentGoldbar->save();
            }
            $prod->delete();
        }
        session()->flash('agency', 'SR-00774');
        session()->flash('refNo', $refPayment);
        // session()->flash('amount', (auth()->user()->isAgentKAP()) ? (($total - $comm)) : ($total));
        session()->flash('amount', Session::has('buying_for_customer_id')
            ? ($total)
            : (auth()->user()->isAgentKAP() ? (($total - $comm)) : ($total)));
        session()->flash('email', Session::has('buying_for_customer_id') ? User::find(Session::get('buying_for_customer_id'))->email : auth()->user()->email);

        // Clear the session after successful purchase
        Session::forget(['buying_for_customer_id', 'buying_for_customer_name']);

        return redirect('snapBuy');
    }

    public function buyFiuu()
    {
        $products = Session::has('buying_for_customer_id')
            ? InvCartKoop::with('item.promotions')->where('user_id', Session::get('buying_for_customer_id'))->get()
            : InvCart::with('item.promotions')->where('user_id', auth()->user()->id)->get();
        $total = 1.0; //Total is RM1 because of FPX Payment
        $comm = 0.0;
        $currentDate = date('Y-m-d');
        $currentDate = date('Y-m-d', strtotime($currentDate));

        foreach ($products as $prod) {
            $comm += $prod->commission->agent_rate * $prod->prod_qty;

            if ($prod->products->item->promotions != NULL && ($currentDate >= $prod->products->item->promotions->start_date) && ($currentDate <= $prod->products->item->promotions->end_date)) {
                $total += $prod->products->item->promotions->promo_price * $prod->prod_qty;
            } elseif ($prod->products->prod_cat == 3) {
                $total += ($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram;
            } else {
                $total += $prod->products->item->marketPrice->price * $prod->prod_qty;
            }
        }
        $refPayment = (string) Str::uuid();

        // if promo code applied
        if ($this->apply_code) {
            $cart = $products->map(function ($item, $key) {
                return [
                    'item_id' => $item->item_id,
                    'prod_qty' => $item->prod_qty
                ];
            });

            $prod_json = [];
            $prod_json = $cart->toArray();

            CommissionPromotion::create([
                'user_id' => auth()->user()->id,
                'product' => json_encode($prod_json),
                'promo_code' => $this->apply_code,
                'billCode' => $refPayment
            ]);
        }

        // Calculate final amount
        $finalAmount = Session::has('buying_for_customer_id')
            ? $total
            : (auth()->user()->isAgentKAP() ? ($total - $comm) : $total);

        // Get user details
        $userName = Session::has('buying_for_customer_id') 
            ? User::find(Session::get('buying_for_customer_id'))->name 
            : auth()->user()->name;
        
        $userEmail = Session::has('buying_for_customer_id') 
            ? User::find(Session::get('buying_for_customer_id'))->email 
            : auth()->user()->email;
        
        $userPhone = Session::has('buying_for_customer_id')
            ? (User::find(Session::get('buying_for_customer_id'))->role == 3
                ? User::find(Session::get('buying_for_customer_id'))->profile->phone1
                : User::find(Session::get('buying_for_customer_id'))->phone_no)
            : (auth()->user()->role == 3 ? auth()->user()->profile->phone1 : auth()->user()->phone_no);

        $merchantId = config('fiuu.merchantId');
        $verifyKey = config('fiuu.key');
        $verifySecret = config('fiuu.secret');
        $subMerchantId = '';

        // Prepare payment data
        $amount = number_format($finalAmount, 2, '.', ''); // Format: 100.00
        $orderid = $refPayment; // Your unique order ID
        $bill_name = $userName;
        $bill_email = $userEmail;
        $bill_mobile = $userPhone;
        $bill_desc = 'Digital Gold Purchase - Kasih AP Digital';
        $country = 'MY'; // Malaysia
        $currency = 'MYR';
        $recordType = 'T';
        $vcode = md5($amount.$merchantId.$orderid.$verifyKey); // Generate vcode
        $verificationCode = [
            'amount' => $amount,
            'merchantId' => $merchantId,
            'orderid' => $orderid,
            'verifyKey' => $verifyKey,
            'vcode' => $vcode
        ];
        Log::info('verificationCode : '.json_encode($verificationCode));

        // Store transaction in database
        FiuuBills::create([
            'ref_no' => $refPayment,
            'amount' => $finalAmount,
            'status' => 22, // 22 = Pending, 00 = Success, 11 = Failed --- The status here is retrieved from Fiuu documentation see: https://github.com/FiuuPayment/Documentation-Fiuu_API_Spec/blob/main/Fiuu%20Recurring%20API%20v7.1.4.pdf
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($products as $prod) { //Filling all these request to Goldbar Ownership Pending
            // Search for available gold bar to be filled
            $goldbar = Goldbar::where('weight_vacant', '>=', $prod->products->prod_weight)->first();
            for ($i = 0; $i < $prod->prod_qty; $i++) {
                GoldbarOwnershipPending::create([
                    'referenceNumber'   => $refPayment,
                    'gold_id'           => $goldbar->id,
                    'user_id'           => Session::has('buying_for_customer_id') ? Session::get('buying_for_customer_id') : auth()->user()->id,
                    'item_id'           => $prod->item_id,
                    'weight'            => ($prod->products->prod_cat == 3 ? $prod->prod_gram : $prod->products->prod_weight),
                    'bought_price' => Session::has('buying_for_customer_id')
                        ? ($prod->products->prod_cat != 3
                            ? $prod->products->item->marketPrice->price
                            : ($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram)
                        : (auth()->user()->isAgentKAP()
                            ? ($prod->products->prod_cat != 3
                                ? ($prod->products->item->marketPrice->price - $prod->commission->agent_rate)
                                : ((($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) - $prod->commission->agent_rate) * $prod->prod_gram))
                            : ($prod->products->prod_cat != 3
                                ? $prod->products->item->marketPrice->price
                                : (($prod->products->item->marketPrice->price + round(($prod->products->item->marketPrice->price * $prod->percentage()), 2)) * $prod->prod_gram))),
                    'status'            => 2,
                    'spot_gold'         => ($prod->products->prod_cat == 3 ? 1 : 0),
                    'financing_flag'    => Session::has('buying_for_customer_id') ? 1 : 0,
                    'created_by'        => auth()->user()->id,
                    'updated_by'        => auth()->user()->id,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // update available gold bar weight
                $currentGoldbar = Goldbar::where('id', $goldbar->id)->first();
                $currentGoldbar->weight_on_hold += $prod->products->prod_weight;
                $currentGoldbar->weight_vacant -= $prod->products->prod_weight;
                $currentGoldbar->save();
            }
        }

        // Prepare payment form data
        $paymentData = [
            'merchant_id' => $merchantId,
            'orderid' => $orderid,
            'currency' => $currency,
            'amount' => $amount,
            'bill_name' => $bill_name,
            'bill_email' => $bill_email,
            'bill_mobile' => $bill_mobile,
            'bill_desc' => $bill_desc,
            'country' => $country,
            'vcode' => $vcode,
            'returnurl' => route('fiuu-return'), // Your return URL
            'callbackurl' => route('fiuu-callback'), // Your callback URL
            'cancelurl' => route('fiuu-cancel'), // Your cancel URL
            
            // Optional parameters
            'channel' => '', // Leave empty for all channels, or specify: FPX, credit, etc.
            'langcode' => 'en', // en, zh-cn, zh-tw, etc.
        ];

        FiuuBills::where('ref_no', $refPayment)->update([
            'data' => json_encode($paymentData)
        ]);

        session()->flash('refNo', $refPayment);

        // Redirect to Fiuu payment page
        return redirect()->route('fiuu-redirect');
    }

    public function render()
    {
        $products = Session::has('buying_for_customer_id')
            ? InvCartKoop::where('exit_type', NULL)->with('item.promotions')->where('user_id', Session::get('buying_for_customer_id'))->get()
            : InvCart::where('exit_type', NULL)->with('item.promotions')->where('user_id', auth()->user()->id)->get();
        $tProducts = 0;
        foreach ($products as $prod) {
            $tProducts += $prod->prod_qty;
        }

        return view('livewire.page.shop.product-buy', [
            'products' => $products,
            'tprod' => $tProducts,
        ]);
    }
}
