<?php

namespace App\Http\Controllers;

use App\Models\CommissionDetailKap;
use App\Models\FiuuBills;
use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\GoldbarOwnershipPending;
use App\Models\InvCart;
use App\Models\InvCartKoop;
use App\Models\InvInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class FiuuController extends Controller
{
    public function fiuuReturn(Request $request)
    {
        // This is called when customer returns from payment page
        $orderid = $request->input('orderid');
        $amount = $request->input('amount');
        $appcode = $request->input('appcode');
        $tranID = $request->input('tranID');
        $domain = $request->input('domain');
        $status = $request->input('status');
        $currency = $request->input('currency');
        $paydate = $request->input('paydate');
        $channel = $request->input('channel');
        $skey = $request->input('skey');

        // Prepare data for the view (including IPN iframe)
        $merchantId = config('fiuu.merchantId');
        $ipnUrl = config('fiuu.api_url');

        // Verify signature
        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . config('fiuu.secret_key'));

        if ($skey != $key1) {
            return redirect()->route('payment.failed')->with('error', 'Invalid payment signature');
        }

        // Check payment status
        if ($status == '00') {
            // Payment successful
            $payment = FiuuBills::where('ref_no', $orderid)->first();
            if ($payment) {
                $payment->update([
                    'status' => 1,
                    'transaction_id' => $tranID,
                    'updated_at' => now()
                ]);
            }

            // Payment successful
            return view('pages.payment.fiuu-success', [
                'orderid' => $orderid,
                'tranID' => $tranID,
                'amount' => $amount,
                'channel' => $channel,
                'merchantId' => $merchantId,
                'ipnUrl' => $ipnUrl
            ]);

            // return redirect()->route('payment.success')->with('success', 'Payment successful!');
        } else {
            // Payment failed or pending
            return view('pages.payment.fiuu-failed', [
                'message' => 'Payment failed or cancelled',
                'orderid' => $orderid,
                'status' => $status,
                'merchantId' => $merchantId,
                'ipnUrl' => $ipnUrl
            ]);
            // return redirect()->route('payment.failed')->with('error', 'Payment failed or cancelled');
        }
    }

    public function fiuuCallback(Request $request)
    {
        // This is called by Fiuu server (backend notification)
        // Same verification logic as return, but this is more reliable
        $orderid = $request->input('orderid');
        $amount = $request->input('amount');
        $appcode = $request->input('appcode');
        $tranID = $request->input('tranID');
        $domain = $request->input('domain');
        $status = $request->input('status');
        $currency = $request->input('currency');
        $paydate = $request->input('paydate');
        $channel = $request->input('channel');
        $skey = $request->input('skey');

        // Verify signature
        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . config('fiuu.secret_key'));
        $payment = FiuuBills::where('ref_no', $orderid)->first();

        if ($skey == $key1 && $status == '00') {
            if ($payment && $payment->status != '00') {
                $payment->update([
                    'status' => '00',
                    'transaction_id' => $tranID,
                    'updated_at' => now()
                ]);

                $gold = GoldbarOwnershipPending::where('referenceNumber', $orderid)
                ->where('status', 2)
                ->get();

                $payment->status = 1;
                $payment->save();

                foreach ($gold as $golds) {
                    //Change the gold pending to successful payment
                    $golds->update(['status' => 1]);

                    GoldbarOwnership::create([
                        'gold_id'           => $golds->gold_id,
                        'user_id'           => $golds->user_id,
                        'item_id'           => $golds->item_id,
                        'ouid'              => (string) Str::uuid(),
                        'weight'            => $golds->weight,
                        'available_weight'  => $golds->weight,
                        'bought_price'      => $golds->bought_price,
                        'active_ownership'  => 1,
                        'spot_gold'         => $golds->spot_gold,
                        'financing_flag'    => $golds->financing_flag,
                        'referenceNumber'   => $orderid,
                        'created_by'        => $golds->user_id,
                        'updated_by'        => $golds->user_id,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                        'split'             => 0,
                    ]);

                    //Remove weight on hold and replaces it with weight occupied


                    $currentGoldbar = Goldbar::where('id', $golds->gold_id)->first();

                    $currentGoldbar->weight_on_hold -= $golds->weight;
                    $currentGoldbar->weight_occupied += $golds->weight;
                    $currentGoldbar->save();

                    $gold_info = InvInfo::where('item_id', $golds->item_id)
                        ->first();

                    // distribute commission/cashback to the upline user
                    if ($golds->user->isUserKAP()) {
                        $commission = $gold_info->item->commissionKAP->agent_rate;
                        $upline_id = $golds->user->upline->user->id;

                        if ($golds->spot_gold == 1) {
                            $commission = $golds->bought_price * ($gold_info->item->commissionKAP->agent_rate / 100);
                        }
                        CommissionDetailKap::create([
                            'user_id'           => $upline_id,
                            'item_id'           => $gold_info->item_id,
                            'bought_id'         => $golds->user_id,
                            'commission'        => $commission,
                            'created_by'        => $golds->user_id,
                            'updated_by'        => $golds->user_id,
                            'created_at'        => now(),
                            'updated_at'        => now(),
                        ]);
                    }

                    // Delete the cart items after payment is confirmed
                    $userId = $payment->created_by;
                    
                    // Check if it was a customer purchase or regular user
                    if (Session::has('buying_for_customer_id')) {
                        InvCartKoop::where('user_id', Session::get('buying_for_customer_id'))->delete();
                    } else {
                        InvCart::where('user_id', $userId)->delete();
                    }
                }
            }
        } elseif ($status == '11' && $payment->status != 11) {
            $gold = GoldbarOwnershipPending::where('referenceNumber', $orderid)
                ->where('status', 2)
                ->get();

            $payment->status = 11;
            $payment->save();

            foreach ($gold as $golds) {
                //Nullifies the gold pending because of failed payment
                $golds->update(['status' => 3]);

                //Remove weight on hold and replaces it with weight occupied
                $currentGoldbar = Goldbar::where('id', $golds->gold_id)->first();

                $currentGoldbar->weight_on_hold -= $golds->weight;
                $currentGoldbar->weight_vacant += $golds->weight;
                $currentGoldbar->save();
            }
        }

        return response('CBTOKEN:MPSTATOK', 200);
    }

    public function fiuuCancel(Request $request)
    {
        return redirect()->route('cart.index')->with('info', 'Payment cancelled');
    }

    public function fiuuRedirect()
    {
        $bill_info = FiuuBills::where('ref_no', request()->session()->get('refNo'))->first();
        $payment_data = json_decode($bill_info->data, true);

        return view('pages.payment.fiuu-redirect', ['paymentData' => $payment_data]);
    }
}
