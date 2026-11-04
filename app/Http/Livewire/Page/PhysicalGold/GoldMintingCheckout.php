<?php

namespace App\Http\Livewire\Page\PhysicalGold;

use Livewire\Component;
use App\Models\GoldbarOwnership;
use App\Models\GoldMinting;
use App\Models\GoldMintingRecords;
use App\Models\InvCart;
use App\Models\MintingGoldPrice;
use Illuminate\Support\Str;
use App\Models\States;
use App\Models\ToyyibBills;
use Illuminate\Support\Facades\Http;

class GoldMintingCheckout extends Component
{
    public $name, $phone1, $address1, $address2, $address3, $postcode, $town, $state, $membership_id;
    public $states;
    public $GoldMint, $GoldMintQTY, $MintingCost;


    public function mount()
    {

        $this->states = States::all();

        $this->membership_id = auth()->user()->profile->membership_id ?? "";

        $this->name = auth()->user()->name;
        $this->phone1 = auth()->user()->profile->phone1 ?? "";
        $this->address1 = auth()->user()->profile->address1 ?? "";
        $this->address2 = auth()->user()->profile->address2 ?? "";
        $this->address3 = auth()->user()->profile->address3 ?? "";
        $this->postcode = auth()->user()->profile->postcode ?? "";
        $this->town = auth()->user()->profile->town ?? "";
        $this->state = auth()->user()->profile->state_id ?? 0;



        $this->GoldMint = InvCart::where('user_id', auth()->user()->id)->where('exit_type', 3)->first();
        $MintingCost = MintingGoldPrice::orderBy('id', 'desc')->get();
        $this->MintingCost = 0;

        if (!$this->GoldMint) {
            redirect('home');
        } else {
            $this->GoldMintQTY = $this->GoldMint->prod_qty;

            foreach ($MintingCost as $MC) {
                if ($this->MintingCost == 0) {
                    if ($this->GoldMintQTY >= $MC->range) {
                        $this->MintingCost = $MC->minting_cost;
                    }
                }
            }
        }
    }

    public function submit()
    {

        $current_grammage =  $this->GoldMint->prod_qty;
        $gold_ownId = array();

        $totalGoldbar = GoldbarOwnership::where('user_id', auth()->user()->id)->where('active_ownership', 1)->where('spot_gold', 1)->where('available_weight', '!=', 0)->orderBy('id')->get();

        foreach ($totalGoldbar as $item) {
            $buffer_grammage = $current_grammage;

            if ($buffer_grammage != 0) {
                if ($current_grammage > $item->available_weight) {
                    $current_grammage -= $item->available_weight;
                    $buffer_grammage = $item->available_weight;
                } else {
                    $current_grammage -= $buffer_grammage;
                }


                $item->available_weight -= $buffer_grammage;
                $item->save();

                GoldMintingRecords::create([
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
        $refPayment = (string) Str::uuid();

        $option = array(
            'userSecretKey' => config('toyyibpay.key'),
            'categoryCode' => config('toyyibpay.category'),
            'billName' => 'CSC Digital',
            'billDescription' => 'Digital Gold to Physical Gold Conversion Postage Fee',
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => ($this->MintingCost + 11) * 100, // Dikira dalam bentuk sen. Maksudnya dekat sini RM10
            'billReturnUrl' => route('toyyibpay-status-gMint'),
            'billCallbackUrl' => route('toyyibpay-callback-mint'),
            'billExternalReferenceNo' => $refPayment,
            'billTo' => auth()->user()->name,
            'billEmail' => auth()->user()->email,
            'billPhone' => (auth()->user()->role == 3) ? auth()->user()->profile->phone1 : auth()->user()->phone_no,
            'billSplitPayment' => 0,
            'billSplitPaymentArgs' => '',
            'billPaymentChannel' => '0',
            'billContentEmail' => 'Thank you for purchasing our product!',
            'billChargeToCustomer' => 1
        );

        $url = 'https://dev.toyyibpay.com/index.php/api/createBill';
        $response = Http::asForm()->post($url, $option);
        $billCode = $response[0]['BillCode'];

        ToyyibBills::create([
            'ref_payment'       => $refPayment,
            'bill_code'         => $billCode,
            'bill_amount'       => $this->MintingCost + 11,
            'status'            => 2,
            'created_by'        => auth()->user()->id,
            'updated_by'        => auth()->user()->id,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $goldMinting = GoldMinting::create([
            'user_id'       => auth()->user()->id,
            'status'        => 3,
            'grammage'      => $this->GoldMint->prod_qty,
            'bill_code'     => $billCode,
            'gold_ids'      => implode(',', $gold_ownId),
            'name'          => $this->name,
            'phone1'        => $this->phone1,
            'address1'      => $this->address1,
            'address2'      => $this->address2,
            'address3'      => $this->address3,
            'postcode'      => $this->postcode,
            'town'          => $this->town,
            'state_id'         => $this->state,
            'created_by'    => auth()->user()->id,
            'updated_by'    => auth()->user()->id,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->GoldMint->delete();

        $goldMintingId = $goldMinting->id;


        $GoldUsed = GoldbarOwnership::where('user_id', auth()->user()->id)
            ->whereIn('id', $gold_ownId)
            ->orderBy('id')
            ->get();

        foreach ($GoldUsed as $item) {
            $item->ex_flag = 5; //5 for Gold Mint exit
            $item->ex_id = $goldMintingId;

            if ($item->available_weight == 0)
                $item->active_ownership = 0;

            $item->save();


            $goldMintRecords = GoldMintingRecords::where('created_by', auth()->user()->id)->where('status', 0)->where('gold_ids', $item->id)->first();

            $goldMintRecords->bill_code = $billCode;
            $goldMintRecords->status = 3;
            $goldMintRecords->exit_id = $goldMintingId;
            $goldMintRecords->updated_at = now();

            $goldMintRecords->save();
        }


        return redirect('https://dev.toyyibpay.com/' . $billCode);
    }

    public function render()
    {
        return view('livewire.page.physical-gold.gold-minting-checkout')->extends('default.default');
    }
}
