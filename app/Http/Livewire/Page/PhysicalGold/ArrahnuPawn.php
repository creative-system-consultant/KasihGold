<?php

namespace App\Http\Livewire\Page\PhysicalGold;

use App\Models\ArrahnuDailyPrice;
use App\Models\ArrahnuGoldBox;
use App\Models\ArrahnuRefBranch;
use App\Models\ArrahnuRefProductCode;
use App\Models\ArrahnuRefStartEndDay;
use App\Models\GoldbarOwnership;
use App\Models\InvCart;
use App\Models\KoputraCif;
use App\Models\MarketPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ArrahnuPawn extends Component
{
    public $GoldMintGram, $GoldMint, $total, $totalD, $totalWallet, $MintingCost, $spotPrice;
    public $GoldMintGramD, $goldprice, $gold_types, $financing, $total_marhun, $marhun, $prod_code, $hqBranch, $startDay, $flagStartDay, $flagKotak, $client_id;

    public function mount()
    {
        $this->flagStartDay = 0;
        $this->flagKotak = 0;
        $today = now()->toDateString(); // Get today's date in the format 'Y-m-d'
        $this->client_id = config('app.client_id');
        $this->hqBranch = ArrahnuRefBranch::where('HQ_FLAG', 'Y')->where('CLIENT_ID', $this->client_id)->first();

        $this->startDay = ArrahnuRefStartEndDay::whereRaw("CAST(START_DATETIME AS DATE) = ?", [$today])->where('BRANCH_CODE', $this->hqBranch->BRANCH_CODE)->where('CLIENT_ID', $this->client_id)
            ->first();


        if ($this->startDay) {
            $this->flagStartDay = 1;
        } else {
            $this->flagStartDay = 0;
            session()->flash('error');
            session()->flash('title', 'Disabled!');
            session()->flash('message', 'Pawn is unavaible because it is outside of Office Hour');
        }

        if ($this->getKotak())
            $this->flagKotak = 1;
        else {
            $this->flagKotak = 0;
            session()->flash('error');
            session()->flash('title', 'Disabled!');
            session()->flash('message', 'Pawn is unavaible at the current time, please contact support to inquire in regards of this issue');
        }



        $this->goldprice = 0;
        $this->gold_types = ArrahnuDailyPrice::fetchTodayGoldPriceDetails();

        if ($this->gold_types) {
            $this->goldprice = $this->gold_types['1'];
        }

        $this->total = 0;
        $this->totalD = 0;

        $totalGoldbar = GoldbarOwnership::where('user_id', auth()->user()->id)->where('active_ownership', 1)->where('spot_gold', 1)->get();
        $this->spotPrice = MarketPrice::where('item_id', 12)->first();

        foreach ($totalGoldbar as $tGold) {
            $this->total += $tGold->available_weight;
        }

        $totalGoldbarD = GoldbarOwnership::where('user_id', auth()->user()->id)->where('active_ownership', 1)->where('spot_gold', 0)->get();
        foreach ($totalGoldbarD as $tGold) {
            $this->totalD += $tGold->available_weight;
        }

        $this->totalWallet = $this->total + $this->totalD;
        $this->GoldMint = InvCart::where('user_id', auth()->user()->id)->where('exit_type', 4)->first();

        $this->GoldMintGram = 0;
        $this->GoldMintGramD = 0;

        $user_id = auth()->user()->id;
        $existFlag = KoputraCif::where('created_by', $user_id)->first();

        if (!$existFlag) {
            $sql = DB::connection('arrahnudb')->select("EXEC ARRAHNU.sp_ar_insert_cust_kap_to_cif '$this->client_id', '$user_id', 'SML'");
        }
    }

    public function getKotak()
    {
        ArrahnuGoldBox::where('TOT_IN_USE', null)->where('CLIENT_ID', $this->client_id)->update([
            'TOT_IN_USE' => 0
        ]);

        ArrahnuGoldBox::where('CURRENT_COLLECTION', null)->where('CLIENT_ID', $this->client_id)->update([
            'CURRENT_COLLECTION' => 0
        ]);

        return ArrahnuGoldBox::where('CLIENT_ID', $this->client_id)
            ->where('BRANCH_CODE', $this->hqBranch->BRANCH_CODE)
            ->where('BOX_TYPE', 'KAPG')
            ->where('RECORD_STATUS', 'AKTIF')
            ->where('ACTIVE_DAY', 'YA')
            ->whereRaw('TOT_CAPACITY - TOT_IN_USE - CURRENT_COLLECTION > 0')
            ->orderBy('BOX_NO')->first();
    }

    public function calculateFinancing()
    {
        $products = ArrahnuRefProductCode::fetchActiveArrahnuProducts();
        if ($this->gold_types) {
            foreach ($products as $code => $product) {
                $this->financing[trim($code)]['name'] = $product['description'];
                $this->financing[trim($code)]['max_financing'] = Money(MoneyRound(MoneyToFloat($this->marhun) * (MoneyToFloat($product['margin']) / 100)));
                $this->financing[trim($code)]['prod_code'] = $product['prod_code'];
            }
        }
    }

    public function next()
    {
        $data = $this->validate([
            'GoldMintGram'              => ['numeric'],
            'GoldMintGramD'             => ['numeric'],
            'prod_code'                 => ['required'],
        ]);

        if ($this->gold_types) {

            if (($this->GoldMintGram + $this->GoldMintGramD)  >= 1) {
                $totalSpotGold = $this->goldprice['price'] * $this->GoldMintGram;
                $totalDigitalGold = $this->goldprice['price'] * $this->GoldMintGramD;

                $cart = collect([
                    (object)['prod_name' => 'Kasih AP Spot Gold', 'grammage' => $this->GoldMintGram, 'tot_price' => $totalSpotGold, 'spot_gold' => 1],
                    (object)['prod_name' => 'Kasih AP Digital Gold', 'grammage' => $this->GoldMintGramD, 'tot_price' => $totalDigitalGold, 'spot_gold' => 0],
                ]);

                $total =  $totalSpotGold +  $totalDigitalGold;

                $totalW =  $this->GoldMintGram +  $this->GoldMintGramD;

                session()->flash('products', $cart);
                session()->flash('total', $total);
                session()->flash('totalWeight', $totalW);
                session()->flash('prod_code', $this->prod_code);

                return redirect('arrahnu-pawn-checkout');
            } else
                $this->emit('message', 'The total weight must be 1 gram and above!');
        } else {
            session()->flash('error');
            session()->flash('title', 'Disabled!');
            session()->flash('message', 'Pawn is unavaible to be used if there are no daily price setted up');
        }
    }

    public function render()
    {
        if (is_numeric($this->GoldMintGram) && is_numeric($this->GoldMintGramD)) {
            if ($this->gold_types) {
                $this->marhun = ($this->GoldMintGram + $this->GoldMintGramD) * $this->goldprice['price'];
            }
            $this->calculateFinancing();
        }

        return view('livewire.page.physical-gold.arrahnu-pawn')->extends('default.default');
    }
}
