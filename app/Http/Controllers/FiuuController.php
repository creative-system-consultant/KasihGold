<?php

namespace App\Http\Controllers;

use App\Constant\FiuuStatus;
use App\Models\CommissionDetailKap;
use App\Models\FiuuBills;
use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\GoldbarOwnershipPending;
use App\Models\InvCart;
use App\Models\InvCartKoop;
use App\Models\InvInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FiuuController extends Controller
{
    public function fiuuReturn(Request $request)
    {
        // Log all incoming POST data
        Log::info('Fiuu Return POST Data', $request->all());

        // Get POST parameters (as documented)
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

        // Check if the required data are received
        if (!$orderid || !$status || !$skey) {
            Log::error('Fiuu Return: Missing required parameters', $request->all());
            return redirect()->route('cart')->with('error', 'Invalid payment response');
        }

        // Prepare useful data from Fiuu config file
        $merchantId = config('fiuu.merchantId');
        $ipnUrl = config('fiuu.api_url');

        /************************************************************
         * IPN Backend Acknowledgement (Required by Fiuu)
         * This sends acknowledgement back to Fiuu servers
         ************************************************************/
        try {
            $postData = $request->all();
            $postData['treq'] = 1; // Additional parameter for IPN - REQUIRED. Value always set to 1. Do not change this value
            
            $postString = http_build_query($postData);
            
            $url = $ipnUrl;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6); // TLSv1.2
            
            $result = curl_exec($ch);
            curl_close($ch);
            
            Log::info('Fiuu IPN Acknowledgement Sent', ['result' => $result]);
        } catch (\Exception $e) {
            Log::error('Fiuu IPN Error', ['error' => $e->getMessage()]);
        }

        // Verify Data Integrity (Signature Verification)
        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . config('fiuu.secret'));

        if ($skey != $key1) {
            Log::error('Fiuu Return: Invalid signature', [
                'received_skey' => $skey,
                'calculated_key1' => $key1
            ]);
            
            return view('pages.payment.fiuu-failed', [
                'message' => 'Invalid payment signature',
                'orderid' => $orderid,
                'status' => -1,
                'merchantId' => $merchantId,
                'ipnUrl' => $ipnUrl
            ]);
        }

        // Process Payment Based on Status
        if ($status == "00") {
            // Payment successful
            $payment = FiuuBills::where('ref_no', $orderid)->first();
            
            if ($payment) {
                // Check if amount matches (tak buat pon takpe just to ensure security)
                if (number_format($payment->amount, 2, '.', '') == number_format($amount, 2, '.', '')) {
                    $payment->update([
                        'status' => FiuuStatus::FIUU_STATUS_SUCCESS,
                        'transaction_id' => $tranID,
                        'updated_at' => now()
                    ]);

                    Log::info('Fiuu Payment Success', [
                        'orderid' => $orderid,
                        'tranID' => $tranID,
                        'amount' => $amount
                    ]);

                    return view('pages.payment.fiuu-success', [
                        'orderid' => $orderid,
                        'tranID' => $tranID,
                        'amount' => $amount,
                        'channel' => $channel,
                        'merchantId' => $merchantId,
                        'ipnUrl' => $ipnUrl
                    ]);
                } else {
                    Log::error('Fiuu Amount Mismatch', [
                        'expected' => $payment->amount,
                        'received' => $amount
                    ]);
                }
            }
        }

        // Payment failed or other status
        Log::warning('Fiuu Payment Failed/Pending', [
            'orderid' => $orderid,
            'status' => $status
        ]);

        return view('pages.payment.fiuu-failed', [
            'message' => 'Payment failed or cancelled',
            'orderid' => $orderid,
            'status' => $status,
            'merchantId' => $merchantId,
            'ipnUrl' => $ipnUrl
        ]);
    }

    public function fiuuCallback(Request $request)
    {
        // Log incoming callback data
        Log::info('Fiuu Callback Received', $request->all());

        // Get POST parameters
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
        $key1 = md5($paydate . $domain . $key0 . $appcode . config('fiuu.secret'));

        if ($skey != $key1) {
            Log::error('Fiuu Callback: Invalid signature', [
                'orderid' => $orderid,
                'received_skey' => $skey,
                'calculated_key1' => $key1
            ]);
            return response('CBTOKEN:MPSTATOK', 200);
        }

        $payment = FiuuBills::where('ref_no', $orderid)->first();

        if (!$payment) {
            Log::error('Fiuu Callback: Payment record not found', ['orderid' => $orderid]);
            return response('CBTOKEN:MPSTATOK', 200);
        }

        // Verify amount matches (security check)
        if (number_format($payment->amount, 2, '.', '') != number_format($amount, 2, '.', '')) {
            Log::error('Fiuu Callback: Amount mismatch', [
                'orderid' => $orderid,
                'expected' => $payment->amount,
                'received' => $amount
            ]);
            return response('CBTOKEN:MPSTATOK', 200);
        }

        // Process successful payment (status 00)
        if ($status == '00' && $payment->status != 1) {
            Log::info('Fiuu Callback: Processing successful payment', ['orderid' => $orderid]);

            // Update payment status
            $payment->update([
                'status' => FiuuStatus::FIUU_STATUS_SUCCESS,
                'transaction_id' => $tranID,
                'updated_at' => now()
            ]);

            // Get pending gold ownership records
            $gold = GoldbarOwnershipPending::where('referenceNumber', $orderid)
                ->where('status', 2) // Status 2 = Pending
                ->get();

            if ($gold->isEmpty()) {
                Log::warning('Fiuu Callback: No pending gold records found', ['orderid' => $orderid]);
            }

            foreach ($gold as $golds) {
                // Change the gold pending to successful payment
                $golds->update(['status' => 1]); // Status 1 = Confirmed

                // Create gold ownership record
                GoldbarOwnership::create([
                    'gold_id' => $golds->gold_id,
                    'user_id' => $golds->user_id,
                    'item_id' => $golds->item_id,
                    'ouid' => (string) Str::uuid(),
                    'weight' => $golds->weight,
                    'available_weight' => $golds->weight,
                    'bought_price' => $golds->bought_price,
                    'active_ownership' => 1,
                    'spot_gold' => $golds->spot_gold,
                    'financing_flag' => $golds->financing_flag,
                    'referenceNumber' => $orderid,
                    'created_by' => $golds->user_id,
                    'updated_by' => $golds->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'split' => 0,
                ]);

                // Update goldbar weights (remove from hold, add to occupied)
                $currentGoldbar = Goldbar::where('id', $golds->gold_id)->first();
                if ($currentGoldbar) {
                    $currentGoldbar->weight_on_hold -= $golds->weight;
                    $currentGoldbar->weight_occupied += $golds->weight;
                    $currentGoldbar->save();
                }

                // Distribute commission/cashback to upline user
                $gold_info = InvInfo::where('item_id', $golds->item_id)->first();

                if ($gold_info && $golds->user->isUserKAP()) {
                    $commission = $gold_info->item->commissionKAP->agent_rate;
                    $upline_id = $golds->user->upline->user->id;

                    if ($golds->spot_gold == 1) {
                        $commission = $golds->bought_price * ($gold_info->item->commissionKAP->agent_rate / 100);
                    }

                    CommissionDetailKap::create([
                        'user_id' => $upline_id,
                        'item_id' => $gold_info->item_id,
                        'bought_id' => $golds->user_id,
                        'commission' => $commission,
                        'created_by' => $golds->user_id,
                        'updated_by' => $golds->user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Delete cart items after payment is confirmed
            $userId = $payment->user_id ?? $payment->created_by;
            
            // Check if it was a customer purchase using the flag stored in payment
            if (isset($payment->is_customer_purchase) && $payment->is_customer_purchase == 1) {
                InvCartKoop::where('user_id', $userId)->delete();
                Log::info('Fiuu Callback: Deleted InvCartKoop', ['user_id' => $userId]);
            } else {
                InvCart::where('user_id', $userId)->delete();
                Log::info('Fiuu Callback: Deleted InvCart', ['user_id' => $userId]);
            }

            Log::info('Fiuu Callback: Payment processed successfully', [
                'orderid' => $orderid,
                'tranID' => $tranID,
                'gold_records_processed' => $gold->count()
            ]);

        } 
        // Process failed payment (status 11)
        elseif ($status == '11' && $payment->status != 11) {
            Log::info('Fiuu Callback: Processing failed payment', ['orderid' => $orderid]);

            // Update payment status to failed
            $payment->update([
                'status' => FiuuStatus::FIUU_STATUS_FAILED, // 11 = Failed, 22 = Pending, 00 = Success --- The status here is retrieved from Fiuu documentation see: https://github.com/FiuuPayment/Documentation-Fiuu_API_Spec/blob/main/Fiuu%20Recurring%20API%20v7.1.4.pdf
                'transaction_id' => $tranID,
                'updated_at' => now()
            ]);

            // Get pending gold ownership records
            $gold = GoldbarOwnershipPending::where('referenceNumber', $orderid)
                ->where('status', 2)
                ->get();

            foreach ($gold as $golds) {
                // Nullify the gold pending because of failed payment
                $golds->update(['status' => 3]); // Status 3 = Failed/Cancelled

                // Release the held weight back to vacant
                $currentGoldbar = Goldbar::where('id', $golds->gold_id)->first();
                if ($currentGoldbar) {
                    $currentGoldbar->weight_on_hold -= $golds->weight;
                    $currentGoldbar->weight_vacant += $golds->weight;
                    $currentGoldbar->save();
                }
            }

            Log::info('Fiuu Callback: Failed payment processed', [
                'orderid' => $orderid,
                'gold_records_released' => $gold->count()
            ]);
        }

        // Must return this exact response for Fiuu
        return response('CBTOKEN:MPSTATOK', 200);
    }

    public function fiuuCancel(Request $request)
    {
        return redirect()->route('cart')->with('info', 'Payment cancelled');
    }

    public function fiuuRedirect()
    {
        $bill_info = FiuuBills::where('ref_no', request()->session()->get('refNo'))->first();
        $payment_data = json_decode($bill_info->data, true);

        return view('pages.payment.fiuu-redirect', ['paymentData' => $payment_data]);
    }
}
