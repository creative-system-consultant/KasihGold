<?php

namespace App\Http\Livewire\Page\PhysicalGold;

use App\Models\Banks;
use Livewire\Component;
use App\Models\GoldbarOwnership;
use App\Models\GoldMinting;
use App\Models\GoldMintingRecords;
use App\Models\InvCart;
use App\Models\MarketPrice;
use App\Models\MintingGoldPrice;
use App\Models\outrightSG;
use App\Models\outrightSGRecords;
use App\Models\Profile_bank_info;
use Illuminate\Support\Str;
use App\Models\States;
use App\Models\ToyyibBills;
use Illuminate\Support\Facades\Http;


class OutrightCheckoutSG extends Component
{

    public $bankId, $swiftCode, $accNo, $accHolderName, $bankAccId, $membership_id;

    public $states, $banks;
    public $GoldMint, $GoldMintGram, $MintingCost, $total, $spotPrice;

    public function mount()
    {
        $bankInfo = Profile_bank_info::where('user_id', auth()->user()->id)->first();

        $this->states = States::all();
        $this->banks = Banks::all();
        $this->spotPrice = MarketPrice::where('item_id', 12)->first();


        $this->membership_id = auth()->user()->profile->membership_id ?? "";

        $this->bankId = $bankInfo->bank_id ?? "";
        $this->swiftCode = $bankInfo->swift_code ?? "";
        $this->accNo = $bankInfo->acc_no ?? "";
        $this->accHolderName = $bankInfo->acc_holder_name ?? "";
        $this->bankAccId = $bankInfo->acc_id ?? "";



        $this->GoldMint = InvCart::where('user_id', auth()->user()->id)->where('exit_type', 4)->first();
        // dd($this->GoldMint->products->prod_cat);
        $this->total = $this->GoldMint->prod_gram * $this->spotPrice->price;

        if (!$this->GoldMint) {
            redirect('home');
        } else {
            $this->GoldMintGram = $this->GoldMint->prod_gram;
        }
    }

    public function submit()
    {

        $current_grammage =  $this->GoldMintGram;
        $gold_ownId = array();

        $totalGoldbar = GoldbarOwnership::where('user_id', auth()->user()->id)->where('active_ownership', 1)->where('spot_gold', 1)->where('available_weight', '!=', 0)->orderBy('id')->get();

        foreach ($totalGoldbar as $item) {
            $buffer_grammage = $current_grammage;

            if (round($buffer_grammage, 4) > 0) {
                if ($current_grammage > $item->available_weight) {
                    $current_grammage -= $item->available_weight;
                    $buffer_grammage = round($item->available_weight, 4);
                } else {
                    $buffer_grammage = $current_grammage;
                    $current_grammage = 0;
                }


                $item->available_weight -= $buffer_grammage;
                $item->save();

                outrightSGRecords::create([
                    'status'        => 0,
                    'grammage'      => $buffer_grammage,
                    'gold_ids'      => $item->id,
                    'created_by'    => auth()->user()->id,
                    'updated_by'    => auth()->user()->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                array_push($gold_ownId, $item->id);
            }
        }

        $outright = outrightSG::create([
            'user_id'                   => auth()->user()->id,
            'status'                    => 0,
            'total_grammage'            => $this->GoldMintGram,
            'surrendered_amount'        => $this->total,
            'updated_by'                => auth()->user()->id,
            'created_by'                => auth()->user()->id,
            'created_at'                => now(),
            'updated_at'                => now(),
        ]);

        $outrightId = $outright->id;

        $this->GoldMint->delete();



        $GoldUsed = GoldbarOwnership::where('user_id', auth()->user()->id)
            ->whereIn('id', $gold_ownId)
            ->orderBy('id')
            ->get();

        foreach ($GoldUsed as $item) {
            $item->ex_flag = 8; //8, 9, 10 for Outright Spot Gold
            $item->ex_id = $outrightId;

            if ($item->available_weight == 0)
                $item->active_ownership = 0;

            $item->save();

            $OutrightRecords = outrightSGRecords::where('created_by', auth()->user()->id)->where('status', 0)->where('gold_ids', $item->id)->first();

            $OutrightRecords->status = 3;
            $OutrightRecords->exit_id = $outrightId;
            $OutrightRecords->updated_at = now();

            $OutrightRecords->save();
        }

        session()->flash('message', 'Your Outright request has successfully submitted');
        session()->flash('success');
        session()->flash('title', 'Success!');
        return redirect('home');
    }


    public function render()
    {
        return view('livewire.page.physical-gold.outright-checkout-s-g')->extends('default.default');
    }
}
