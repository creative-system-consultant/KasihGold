<?php

namespace App\Http\Livewire\Page\PhysicalGold;

use App\Models\ArrahnuDailyPrice;
use App\Models\ArrahnuGoldBox;
use App\Models\ArrahnuPawnDetails;
use App\Models\ArrahnuPawnMaster;
use App\Models\ArrahnuPawnRecords;
use App\Models\ArrahnuPayType;
use App\Models\ArrahnuRefBranch;
use App\Models\ArrahnuRefProductCode;
use App\Models\ArrahnuRefStaff;
use App\Models\ArrahnuStaffCash;
use App\Models\ArrahnuSystemGlobalParameter;
use App\Models\Banks;
use Livewire\Component;
use App\Models\GoldbarOwnership;
use App\Models\GoldMinting;
use App\Models\GoldMintingRecords;
use App\Models\InvCart;
use App\Models\KoputraCif;
use App\Models\MarketPrice;
use App\Models\MintingGoldPrice;
use App\Models\outrightSG;
use App\Models\outrightSGRecords;
use App\Models\Profile_bank_info;
use Illuminate\Support\Str;
use App\Models\States;
use App\Models\ToyyibBills;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ArrahnuPawnCheckout extends Component
{




    public $total_weight, $total_pawn, $financeAmt, $maximum_financing, $pay_type, $prod_code, $prod_name;
    public $data, $tot_data, $goldprice, $branch, $chosenBranch, $kap_product, $client_id;
    public $financeMargin, $financeDuration, $profitRules, $minFinancing, $maxFinancing;

    public function mount()
    {
        $this->data = request()->session()->get('products');
        $this->total_weight = request()->session()->get('totalWeight');
        $this->total_pawn = request()->session()->get('total');
        $this->prod_code = request()->session()->get('prod_code');
        $this->client_id = config('app.client_id');
        if ($this->data !== null) {
            $this->tot_data = count($this->data);
        } else {
            $this->tot_data = 0;
        }

        $this->branch = ArrahnuRefBranch::where(['ACTIVE_FLAG' => 'Y', 'CLIENT_ID' => $this->client_id])->get();
        if (!$this->data) {
            redirect('arrahnu-pawn');
        } else {

            $productCode = ArrahnuRefProductCode::where(['PROD_CODE' => $this->prod_code, 'CLIENT_ID' => $this->client_id])->first();

            $this->prod_name = $productCode->PROD_DESC;
            $this->financeMargin = $productCode->MARGIN . '%';
            $this->financeDuration = $productCode->DURATION . ' Months';
            $this->profitRules = $productCode->PROFIT . '%';
            $this->minFinancing = "RM " . Money($productCode->MIN_FIN);
            $this->maxFinancing = "RM " . Money($productCode->MAX_FIN);

            $this->maximum_financing = MoneyToFloat(MoneyRound($this->total_pawn * ($productCode->MARGIN / 100)));
            $this->financeAmt = $this->maximum_financing;


            $goldprice = ArrahnuDailyPrice::fetchTodayGoldPriceDetails();
            $this->goldprice = $goldprice['1'];
        }
    }

    function pawnSiriNoGenerator()
    {
        $header = ArrahnuSystemGlobalParameter::where('CLIENT_ID', $this->client_id)->first()->GADAIKAP_HEADER;

        $branchCode = ($this->chosenBranch) ? $this->chosenBranch : 'SML';
        $branch = ArrahnuRefBranch::where(['BRANCH_CODE' => $branchCode, 'CLIENT_ID' => $this->client_id])->first();
        $branch_id = str_pad($branch->BRANCH_ID, 2, '0', STR_PAD_LEFT);
        $date = now()->format('dmy');

        // $running_no = ArrahnuPawnMaster::where('BRANCH_CODE', $branchCode)
        //                                 ->whereRaw('CAST(PAWN_DATE AS DATE) = ?', [date('Y-m-d')])
        //                                 ->where('PROD_CODE', $this->prod_code)
        //                                 ->whereHas('product', function ($query) {
        //                                     $query->where('PROD_TYPE', 'KAP');
        //                                 })
        //                                 ->count() + 1;

        $running_no  = ArrahnuPawnMaster::with(['product' => function ($query) {
                                                $query->where('PROD_TYPE', 'KAPG');
                                            }])
                                                ->where('BRANCH_CODE', $branchCode)
                                                ->whereRaw('CAST(PAWN_DATE AS DATE) = ?', [date('Y-m-d')])
                                                ->where('CLIENT_ID', $this->client_id)
                                                ->whereHas('product', function ($query) {
                                                    $query->where('PROD_TYPE', 'KAPG');
                                                })
                                                ->count() + 1;

        $running_no = str_pad($running_no, 4, '0', STR_PAD_LEFT);

        return $header . $branch_id . "-" . $date . "-" . $running_no;
    }

    public function getKotak()
    {
        ArrahnuGoldBox::where(['TOT_IN_USE' => null, 'CLIENT_ID' => $this->client_id])->update([
            'TOT_IN_USE' => 0
        ]);

        ArrahnuGoldBox::where(['CURRENT_COLLECTION' => null, 'CLIENT_ID' => $this->client_id])->update([
            'CURRENT_COLLECTION' => 0
        ]);

        return ArrahnuGoldBox::where('CLIENT_ID', $this->client_id)
            ->where('BRANCH_CODE', 'SML')
            ->where('BOX_TYPE', 'KAPG')
            ->where('RECORD_STATUS', 'AKTIF')
            ->where('ACTIVE_DAY', 'YA')
            ->whereRaw('TOT_CAPACITY - TOT_IN_USE - CURRENT_COLLECTION > 0')
            ->orderBy('BOX_NO')->first();
    }

    public function getMaturityDate($date)
    {
        $day_count = 30 * 18;
        return date('Y-m-d', strtotime('+' . $day_count . ' days', strtotime($date)));
    }

    public function submit()
    {
        $user_id = auth()->user()->profile->ic;
        $cif_id = auth()->user()->id;
        $customer_info = KoputraCif::where(['identity_no' => $user_id, 'client_id' => $this->client_id])->first();

        $customer_financing = $this->maximum_financing;
        $profit_percentage = MoneyToFloat(0.096);
        $product_duration = MoneyToFloat(18 / 12);
        $profit = MoneyRound($customer_financing * $profit_percentage * $product_duration);
        $mat_date = $this->getMaturityDate(now());
        $siri_no = $this->pawnSiriNoGenerator();
        $p_date = date('Y-m-d H:i:s', strtotime(now()));

        $kotak = $this->getKotak();
        $kotak->CURRENT_COLLECTION += $this->tot_data;
        $kotak->save();

        $gold_ownId = array();

        $branchCode = ($this->chosenBranch) ? $this->chosenBranch : 'SML';


        // // Add financing to teller collection
        // $staff_cash = ArrahnuStaffCash::where('BIZ_DATE', date('Y-m-d'))->where('STAFF_ID', 45990)->first();
        // $staff_cash->TRX_COLLECTION = $staff_cash->TRX_COLLECTION + MoneyToFloat($this->financeAmt);
        // $staff_cash->save();
        $sql = DB::connection('arrahnudb')->select("EXEC ARRAHNU.sp_ar_update_cust_kap_branch '$this->client_id', '$customer_info->id', '$branchCode'");
        // Save pawn master
        ArrahnuPawnMaster::create([
            'CIF_NO' => $customer_info->id,
            'SIRI_NO' => $siri_no,
            'PROD_CODE' => $this->prod_code,
            'PAWN_DATE' => $p_date,
            'MAT_DATE' => $mat_date,
            'TOT_WEIGHT' => MoneyToFloat($this->total_weight),
            'TOT_VALUE_MARHUN' => MoneyToFloat($this->total_pawn),
            'TOT_PROFIT' => MoneyToFloat($profit),
            'BAL_TOT_PROFIT' => MoneyToFloat($profit),
            'MAX_AMT_FIN' => MoneyToFloat($this->maximum_financing),
            'TOT_AMT_FIN' => MoneyToFloat($this->financeAmt),
            'BAL_OUTSTANDING' => MoneyToFloat($this->financeAmt),
            'PAY_TYPE' => $this->pay_type,
            'STATUS' => 'APPLY',
            'BOX_NO' => $kotak->BOX_NO,
            'STAFF_ID' => $this->getStaffId($branchCode),
            'BRANCH_CODE' => $branchCode,
            'CLIENT_ID' => $this->client_id,
        ]);

        // Save pawn detailed
        foreach ($this->data as $row) {

            if ($row['grammage'] > 0) {
                ArrahnuPawnDetails::create([
                    'SIRI_NO' => $siri_no,
                    'QTY' => 1,
                    'MARHUN_CODE' => 5,
                    'WEIGHT' => $row['grammage'],
                    'KARAT' => MoneyToFloat("24.0"),
                    'PRICE' => MoneyToFloat($row['tot_price']),
                    'REMARKS' => 'GADAIAN KAP',
                    'CERT_NO' => 1,
                    'CLIENT_ID' => $this->client_id,
                ]);
            }

            // Update available gold



            if ($row['spot_gold'] == 1) {
                $golds = GoldbarOwnership::where('user_id', auth()->user()->id)
                    ->where('active_ownership', 1)
                    ->where('spot_gold', 1)
                    ->where('available_weight', '!=', 0)
                    ->orderByDesc('weight')
                    ->get();
            } else if ($row['spot_gold'] == 0) {
                $golds = GoldbarOwnership::where('user_id', auth()->user()->id)
                    ->where('active_ownership', 1)
                    ->where('spot_gold', 0)
                    ->where('available_weight', '!=', 0)
                    ->orderByDesc('weight')
                    ->get();
            }

            foreach ($golds as $item) {
                $buffer_grammage = $row['grammage'];

                if ($buffer_grammage != 0) {
                    if ($row['grammage'] > $item->available_weight) {
                        $row['grammage'] -= round($item->available_weight, 2);
                        $buffer_grammage = round($item->available_weight, 2);
                    } else {
                        $row['grammage'] -= round($buffer_grammage, 2);
                        $row['grammage'] = abs(round($row['grammage'], 2)); // this is to avoid float precision error
                    }


                    $item->available_weight -= round($buffer_grammage, 2);
                    $item->save();

                    ArrahnuPawnRecords::create([
                        'status'        => 2,
                        'grammage'      => $buffer_grammage,
                        'gold_ids'      => $item->id,
                        'SIRI_NO'       => $siri_no,
                        'created_by'    => auth()->user()->id,
                        'updated_by'    => auth()->user()->id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);

                    array_push($gold_ownId, $item->id);
                }
            }

            $GoldUsed = GoldbarOwnership::where('user_id', auth()->user()->id)
                ->whereIn('id', $gold_ownId)
                ->orderBy('id')
                ->get();


            foreach ($GoldUsed as $item) {
                $item->ex_flag = 13; //13 for Arrahnu Pawn

                if ($item->available_weight == 0)
                    $item->active_ownership = 0;

                $item->save();
            }
        }

        session()->flash('message', 'Your Arrahnu Pawn request has successfully submitted');
        session()->flash('success');
        session()->flash('title', 'Success!');
        return redirect('home');
    }

    public function getStaffId($branchCode)
    {
        $staff_id = ArrahnuRefStaff::where('NAME', 'like', '%PENAFSIR SISTEM%')->where('BRANCH_CODE', $branchCode)->where('CLIENT_ID', $this->client_id)->first()->STAFF_ID;

        return $staff_id;
    }

    public function render()
    {
        $lists = $this->data;

        return view('livewire.page.physical-gold.arrahnu-pawn-checkout', [
            'payment_type' => ArrahnuPayType::where('PAY_CODE', '<>', '3')->where('RECORD_STATUS', 'AKTIF')->where('CLIENT_ID', $this->client_id)->get(), 'lists' => $lists
        ])->extends('default.default');
    }
}
