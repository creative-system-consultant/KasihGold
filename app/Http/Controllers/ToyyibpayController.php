<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BuyBack;
use App\Models\CommissionDetailKap;
use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\GoldbarOwnershipPending;
use App\Models\GoldMinting;
use App\Models\GoldMintingRecords;
use App\Models\InvInfo;
use App\Models\PhysicalConvert;
use App\Models\ToyyibBills;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibpayController extends Controller
{

    public function paymentStatusConv()
    {
        $response = request()->all(['status_id', 'billcode', 'order_id']);


        if ($response['status_id'] == 1) {
            session()->flash('message', 'The Physical Gold Conversion Request Has Successfully Submitted.');

            session()->flash('success');
            session()->flash('title', 'Success!');
        } elseif ($response['status_id'] == 3) {


            session()->flash('message', 'The Physical Gold Conversion Request is Rejected.');

            session()->flash('error');
            session()->flash('title', 'Error!');
        }

        return redirect('home');
        // return $response;
    }


    public function paymentStatusBuy()
    {
        $response = request()->all(['status_id', 'billcode', 'order_id']);

        if ($response['status_id'] == 1) {
            session()->flash('message', 'Your Digital Gold Purchase is Successful.');

            session()->flash('success');
            session()->flash('title', 'Success!');
        } elseif ($response['status_id'] == 3) {
            session()->flash('message', 'Your Digital Gold Purchase is Unsuccessful.');

            session()->flash('error');
            session()->flash('title', 'Error!');
        }

        return redirect('home');
        // return $response;
    }

    public function callback()
    {
        $response = request()->all(['refno', 'status', 'billcode']);
        $toyyibBill = ToyyibBills::where('bill_code', $response['billcode'])->first();

        if ($response['status'] == 1 && $toyyibBill->status != 1) {

            $gold = GoldbarOwnershipPending::where('referenceNumber', $response['billcode'])
                ->where('status', 2)
                ->get();

            $toyyibBill->status = 1;
            $toyyibBill->save();

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
                    'referenceNumber'   => $response['billcode'],
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
        } elseif ($response['status'] == 3 && $toyyibBill->status != 3) {
            $gold = GoldbarOwnershipPending::where('referenceNumber', $response['billcode'])
                ->where('status', 2)
                ->get();

            $toyyibBill->status = 3;
            $toyyibBill->save();

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
    }



    public function paymentStatusMint()
    {
        $response = request()->all(['status_id', 'billcode', 'order_id']);

        if ($response['status_id'] == 1) {
            session()->flash('message', 'The Gold Minting Request Has Successfully Submitted.');

            session()->flash('success');
            session()->flash('title', 'Success!');
        } elseif ($response['status_id'] == 3) {
            session()->flash('message', 'The Gold Minting Request is Rejected.');

            session()->flash('error');
            session()->flash('title', 'Error!');
        }

        return redirect('home');
        // return $response;
    }

    public function callbackMint()
    {
        $response = request()->all(['refno', 'status', 'billcode']);
        $toyyibBill = ToyyibBills::where('bill_code', $response['billcode'])->first();

        if ($response['status'] == 1 && $toyyibBill->status != 1) {

            $gold = GoldMintingRecords::where('bill_code', $response['billcode'])
                ->where('status', 3)
                ->get();
            $toyyibBill->status = 1;
            $toyyibBill->save();

            foreach ($gold as $golds) {
                //Change the gold pending to successful payment
                $golds->status = 1;
                $golds->save();
            }

            $exit_id = $gold->first()->exit_id;

            $goldMinting = GoldMinting::where('id', $exit_id)->where('status', 3)->first();
            $goldMinting->status = 0;
            $goldMinting->save();
        } elseif ($response['status'] == 3 && $toyyibBill->status != 3) {
            $gold = GoldMintingRecords::where('bill_code', $response['billcode'])
                ->where('status', 3)
                ->get();

            $toyyibBill->status = 3;
            $toyyibBill->save();

            foreach ($gold as $golds) {
                //Nullifies the gold pending because of failed payment
                $golds->update(['status' => 2]);
            }
            $exit_id = $gold->first->exit_id;

            $goldMinting = GoldMinting::where('id', $golds->exit_id)->where('status', 3)->first();

            $goldMinting->update(['status' => 2]);
        }
    }


    public function callbackConv()
    {

        $response = request()->all(['status_id', 'billcode', 'order_id']);
        $toyyibBill = ToyyibBills::where('bill_code', $response['billcode'])
            ->first();

        if ($response['status_id'] == 1 && $toyyibBill->status != 1) {

            $toyyibBill->update(['status' => 1]);

            $phyConv = PhysicalConvert::where('user_id', $toyyibBill->created_by)
                ->where('ref_payment', $response['billcode'])
                ->where('status', 2)
                ->first();

            if ($phyConv) {
                $phyConv->update(['status' => 0]); //0 is in process, 1 is successful, 2 is pending, 3 is failure

                $goldOwnership = GoldbarOwnership::where('user_id', $toyyibBill->created_by)
                    ->where('ex_id', $phyConv->id)
                    ->where('active_ownership', 0)
                    ->get();

                foreach ($goldOwnership as $golds) {
                    $golds->update(['ex_flag' => 2]); //0 is in process, 1 is successful, 2 is pending, 3 is failure
                }
            }
        } elseif ($response['status_id'] == 3 && $toyyibBill->status != 3) {
            $toyyibBill->update(['status' => 3]);

            $phyConv = PhysicalConvert::where('user_id', $toyyibBill->created_by)
                ->where('ref_payment', $response['billcode'])
                ->where('status', 2)
                ->first();

            if ($phyConv) {
                $phyConv->update(['status' => 3]);

                $goldOwnership = GoldbarOwnership::where('user_id', $toyyibBill->created_by)
                    ->where('ex_id', $phyConv->id)
                    ->where('active_ownership', 0)
                    ->get();
            }
        }
    }
}
