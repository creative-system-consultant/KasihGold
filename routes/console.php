<?php

use App\Models\CommissionDetailKap;
use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\GoldbarOwnershipPending;
use App\Models\InvCart;
use App\Models\InvInfo;
use App\Models\scheduler_interval;
use App\Models\MarketPrice;
use App\Models\SnapNPay;
use App\Models\ToyyibBills;
use App\Models\User;
use App\Models\UserUpline;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('UpdateCompletedProfile', function () {

    $CustLists = DB::connection('sqlsrv')->select(DB::raw("SELECT a.id, a.email, b.completed,c.completed FROM users a
    join profile_personal b on b.user_id = a.id
    join profile_bank_info c on c.user_id = a.id
      where a.profile_c = 0
      and b.completed = 1
      and c.completed =1
    "));

    foreach ($CustLists as $items) {
        $user = User::where('id', $items->id)->first();
        $user->profile_c = 1;
        $user->save();
    }
})->purpose('Display an inspiring quote');


Artisan::command('UpdateSpotPrice', function () {

    $spotGold = InvInfo::where('prod_cat', 3)->first();


    if (($spotGold->item->marketPrice->updated_at <= now()->subMinutes(1440))) {

        $option = array(
            'access_key' => 'vd1ud4ptyr6cnr2o4o97sfii8uxc9yrnybsihvmp9x58g516nd2csi50w9cj',
            'base' => 'MYR',
            'symbols' => 'XAU',
        );

        $url = 'https://www.metals-api.com/api/latest';
        $response = Http::get($url, $option);
        $response = json_decode($response->getBody()->getContents());

        $this->spotPrice = ($response->rates->XAU / 31.1035);
        Log::info($this->spotPrice);
        Log::info('spot price');
        $spotGold->item->marketPrice->update(['price' => number_format($this->spotPrice, 2), 'updated_at' => now()]);
    }
})->purpose('Display an inspiring quote');


Artisan::command('UpdateSpotGoldCart', function () {

    $spotGold = MarketPrice::select('updated_at')->where('item_id', 12)->first();

    $spotGoldCart = InvCart::where('item_id', 12)->whereNull('deleted_at')->first();

    if ($spotGoldCart) {

        if (($spotGoldCart->updated_at < $spotGold->updated_at) && ($spotGoldCart->updated_at <= now()->subMinutes(10))) {
            $spotGoldCart->update(['deleted_at' => now()]);
        }
    }
})->purpose('clear out any spot gold cart that is using older price');


Artisan::command('UpdateSnapNPayPayment', function () {


    $pendingPayment = SnapNPay::where('status', '!=', 1)->where('status', '!=', 3)
        ->get();

    foreach ($pendingPayment as $pendingTrx) {

        if (($pendingTrx->updated_at <= now()->subMinutes(30))) {
            $pendingPayment->update(['status' => 3, 'updated_by' => 0, 'updated_at' => now()]);

            $pendingGold = GoldbarOwnershipPending::where('referenceNumber', $pendingTrx->ref_no)->get();

            foreach ($pendingGold as $pendingG) {
                $pendingG->update(['status' => 3, 'snapNPayFlag' => 3, 'updated_by' => 0, 'updated_at' => now()]);

                $currentGoldbar = Goldbar::where('id', $pendingG->gold_id)->first();
                $currentGoldbar->weight_on_hold -= $pendingG->weight;
                $currentGoldbar->weight_vacant += $pendingG->weight;
                $currentGoldbar->save();
            }
        }
    }
})->purpose('clear out pending snapNPay Payment that is past 30 mins');




Artisan::command('UpdatePayment', function () {

    // scheduler_interval::create([
    //     'created_at'        => now(),
    //     'updated_at'        => now(),
    //     'type'        => "UP",
    // ]);

    $pendingPayment = ToyyibBills::where('status', 2)
        ->get();

    foreach ($pendingPayment as $pendingTrx) {

        $option = array(
            'billCode' => $pendingTrx->bill_code
        );

        $url = 'https://dev.toyyibpay.com/index.php/api/getBillTransactions';
        $response = Http::asForm()->post($url, $option);

        if ($response[0]['billpaymentStatus'] == 1 && $pendingTrx->status != 1) {
            $pendingTrx->update(['status' => 1, 'updated_by' => 0]);

            $pendingGold = GoldbarOwnershipPending::where('referenceNumber', $pendingTrx->bill_code)->get();

            foreach ($pendingGold as $pendingG) {
                $pendingG->update(['status' => 1, 'updated_by' => 0]);

                GoldbarOwnership::create([
                    'gold_id'           => $pendingG->gold_id,
                    'user_id'           => $pendingG->user_id,
                    'item_id'           => $pendingG->item_id,
                    'ouid'              => (string) Str::uuid(),
                    'weight'            => $pendingG->weight,
                    'available_weight'  => $pendingG->weight,
                    'bought_price'      => $pendingG->bought_price,
                    'active_ownership'  => 1,
                    'spot_gold'         => $pendingG->spot_gold,
                    'financing_flag'    => $pendingG->financing_flag,
                    'referenceNumber'   => $pendingG->referenceNumber,
                    'created_by'        => 0, //id 0 meaning that it has been updated by the scheduler
                    'updated_by'        => 0,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                    'split'             => 0,
                ]);

                $currentGoldbar = Goldbar::where('id', $pendingG->gold_id)->first();
                $currentGoldbar->weight_on_hold -= $pendingG->weight;
                $currentGoldbar->weight_occupied += $pendingG->weight;
                $currentGoldbar->save();

                $gold_info = InvInfo::where('prod_weight', $pendingG->weight)
                    ->where('user_id', 10)
                    ->first();

                $user_info = User::where('id', $pendingG->user_id)->first();

                // distribute commission/cashback to the upline user
                if ($user_info->role == 4 && $user_info->client == 2) {
                    $commission = $gold_info->item->commissionKAP->agent_rate;
                    $upline = UserUpline::where('user_id', $pendingG->user_id)->first();

                    CommissionDetailKap::create([
                        'user_id'           => $upline->upline_id,
                        'item_id'           => $gold_info->item->id,
                        'bought_id'         => $pendingG->user_id,
                        'commission'        => $commission,
                        'created_by'        => 0,
                        'updated_by'        => 0,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }
            }
        } elseif (($response[0]['billpaymentStatus'] == 4) && ($pendingTrx->updated_at <= now()->subMinutes(2))) {
            $pendingTrx->update(['status' => 3, 'updated_by' => 0, 'updated_at' => now()]);

            $pendingGold = GoldbarOwnershipPending::where('referenceNumber', $pendingTrx->bill_code)->get();

            foreach ($pendingGold as $pendingG) {
                $pendingG->update(['status' => 3, 'updated_by' => 0]);

                $currentGoldbar = Goldbar::where('id', $pendingG->gold_id)->first();
                $currentGoldbar->weight_on_hold -= $pendingG->weight;
                $currentGoldbar->weight_vacant += $pendingG->weight;
                $currentGoldbar->save();
            }
        }
    }
})->purpose('Update successful payment if the user close without follow the normal flow of purchase and release gold on hold if transaction is more than 30 minutes.');
