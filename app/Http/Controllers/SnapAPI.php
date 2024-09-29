<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CommissionDetailKap;
use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\GoldbarOwnershipPending;
use App\Models\InvInfo;
use App\Models\SnapNPay;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SnapAPI extends Controller
{
    public function index()
    {
        return view('pages.snap-n-pay.index');
    }


    public function callback()
    {
        $response = request()->all(['status', 'orderNo', 'refNo', 'amount', 'fpxTxnId', 'extraData']);
        Log::info($response);

        $toyyibBill = SnapNPay::where('ref_no', $response['refNo'])->first();

        if ($response['status'] == 'success' && $toyyibBill->status == 0) {

            $gold = GoldbarOwnershipPending::where('referenceNumber', $response['refNo'])
                ->where('SnapNPayFlag', 2)
                ->get();

            $toyyibBill->fpxTxnId = $response['fpxTxnId'];
            $toyyibBill->order_no = $response['orderNo'];
            $toyyibBill->status = 1;
            $toyyibBill->save();

            foreach ($gold as $golds) {
                //Change the gold pending to successful payment
                $golds->update(['status' => 1, 'snapNPayFlag' => 1]);

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
                    'referenceNumber'   => $response['refNo'],
                    'snapNPayFlag'      => 1,
                    'financing_flag'    => $golds->financing_flag,
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
            }

            session()->flash('message', 'Your Digital Gold Purchase is Successful.');

            session()->flash('success');
            session()->flash('title', 'Success!');
        } elseif ($response['status'] != 'success' && $toyyibBill->status != 3) {
            $gold = GoldbarOwnershipPending::where('referenceNumber', $response['refNo'])
                ->where('status', 2)
                ->get();

            $toyyibBill->fpxTxnId = $response['fpxTxnId'];
            $toyyibBill->order_no = $response['orderNo'];
            $toyyibBill->status = 3;
            $toyyibBill->save();

            foreach ($gold as $golds) {
                //Nullifies the gold pending because of failed payment
                $golds->update(['status' => 3, 'SnapNPayFlag' => 3]);

                //Remove weight on hold and replaces it with weight occupied
                $currentGoldbar = Goldbar::where('id', $golds->gold_id)->first();

                $currentGoldbar->weight_on_hold -= $golds->weight;
                $currentGoldbar->weight_vacant += $golds->weight;
                $currentGoldbar->save();
            }
            session()->flash('message', 'Your Digital Gold Purchase is Unsuccessful.');

            session()->flash('error');
            session()->flash('title', 'Error!');
        }

        return redirect('home');
    }

    public function snapBuy()
    {
        // $response = request()->all(['status', 'orderNo', 'refNo', 'amount', 'fpxTxnId', 'extraData']);
        // Log::info($response);
        if (!request()->session()->get('agency')) {
            return redirect('home');
        } else {

            $bill_info = SnapNPay::where('ref_no', request()->session()->get('refNo'))->first();

            $bill_info->status = 0;
            $bill_info->save();



            return view('pages.snap-n-pay.load-page');
        }

        // dd($response);
    }
}
