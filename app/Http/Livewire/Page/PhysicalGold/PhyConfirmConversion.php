<?php

namespace App\Http\Livewire\Page\PhysicalGold;

use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\MarketPrice;
use App\Models\PhysicalConvert;
use App\Models\States;
use App\Models\ToyyibBills;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Illuminate\Support\Str;


class PhyConfirmConversion extends Component
{

    public $name, $phone1, $address1, $address2, $address3, $postcode, $town, $state, $membership_id, $data;
    public $states;

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

        $this->data = request()->session()->get('products');
    }

    public function convert()
    {
        $total_req_gram = ($this->data[0]['qty'] * $this->data[0]['prod_weight']) + ($this->data[1]['qty'] * $this->data[1]['prod_weight']);
        $gold_ownId = array();
        $refPayment = (string) Str::uuid();

        // dd($total_req_gram);

        $golds = GoldbarOwnership::where('user_id', auth()->user()->id)
            ->where('active_ownership', 1)
            ->orderByDesc('weight')
            ->get();

        foreach ($golds as $emas) {
            if ($total_req_gram != 0) {
                if ($emas->weight == 1.00 && $total_req_gram >= 1.00) {
                    $total_req_gram = round($total_req_gram, 2) - round($emas->weight, 2);

                    array_push($gold_ownId, $emas->id);
                } elseif ($emas->weight == 0.25 && $total_req_gram >= 0.25) {

                    $total_req_gram = round($total_req_gram, 2) - round($emas->weight, 2);

                    array_push($gold_ownId, $emas->id);
                } elseif ($emas->weight == 0.10 && $total_req_gram >= 0.10) {

                    $total_req_gram = round($total_req_gram, 2) - round($emas->weight, 2);
                    array_push($gold_ownId, $emas->id);
                } elseif ($emas->weight == 0.01 && $total_req_gram >= 0.01) {

                    $total_req_gram = round($total_req_gram, 2) - round($emas->weight, 2);

                    array_push($gold_ownId, $emas->id);
                }
            }
        }



        $option = array(
            'userSecretKey' => config('toyyibpay.key'),
            'categoryCode' => config('toyyibpay.category'),
            'billName' => 'Kasih AP Digital',
            'billDescription' => 'Digital Gold to Physical Gold Conversion Postage Fee',
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => 1000, // Dikira dalam bentuk sen. Maksudnya dekat sini RM10
            'billReturnUrl' => route('toyyibpay-status-conv'),
            'billCallbackUrl' => route('toyyibpay-callbackConv'),
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
        // $billCode = 'test_20251106_033120';

        ToyyibBills::create([
            'ref_payment'       => $refPayment,
            'bill_code'         => $billCode,
            'bill_amount'       => 10,
            'status'            => 2,
            'created_by'        => auth()->user()->id,
            'updated_by'        => auth()->user()->id,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);


        $physicalConv = PhysicalConvert::create([
            'user_id'       => auth()->user()->id,
            'one_gram'      => $this->data[0]['qty'],
            'quarter_gram'  => $this->data[1]['qty'],
            'ref_payment'   => $billCode,
            'status'        => 2,
            'name'          => $this->name,
            'phone1'        => $this->phone1,
            'address1'      => $this->address1,
            'address2'      => $this->address2,
            'address3'      => $this->address3,
            'postcode'      => $this->postcode,
            'town'          => $this->town,
            'state'         => $this->state,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $physicalId = $physicalConv->id;

        if ($total_req_gram > 0) {
            $gg = GoldbarOwnership::where('user_id', auth()->user()->id)
                ->whereNotIn('id', $gold_ownId)
                ->where('active_ownership', 1)
                ->orderBy('weight')
                ->get();

            foreach ($gg as $reform) {
                if ((round($reform->weight, 2) > $total_req_gram) && ($total_req_gram != 0)) {
                    $reform->active_ownership = 0;
                    $reform->ex_id = $physicalId;
                    $reform->split = 1;
                    $reform->save();
                    $Market = MarketPrice::where('item_id', 6)->first();
                    for ($i = 0; $i < ($reform->weight / 0.01); $i++) {
                        $reformedGoldbar = GoldbarOwnership::create([
                            'gold_id'           => $reform->gold_id,
                            'user_id'           => auth()->user()->id,
                            'ouid'              => (string) Str::uuid(),
                            'weight'            => 0.01,
                            'bought_price'      => number_format($Market->price, 2),
                            'active_ownership'  => 1,
                            'referenceNumber'   => $billCode,
                            'ex_id'             => $physicalId,
                            'created_by'        => auth()->user()->id,
                            'updated_by'        => auth()->user()->id,
                            'created_at'        => now(),
                            'updated_at'        => now(),
                            'split'             => 3,
                        ]);

                        if ($total_req_gram != 0) {
                            array_push($gold_ownId, $reformedGoldbar->id);
                            $total_req_gram = round($total_req_gram, 2) - round($reformedGoldbar->weight, 2);
                        }
                    }
                }
            }

            // dd($gold_ownId);
            // dd($golds);
        }

        foreach ($gold_ownId as $gid) {
            $golds = GoldbarOwnership::where('user_id', auth()->user()->id)
                ->where('id', $gid)
                ->first();

            $golds->active_ownership    = 0;
            $golds->referenceNumber     = $billCode;
            $golds->ex_flag             = 0;
            $golds->ex_id               = $physicalId;
            $golds->save();
        }

        // return redirect('https://dev.toyyibpay.com/' . $billCode);


        // dd(0.10 - 0.10);

        // $option = array(
        //     'userSecretKey' => config('toyyibpay.key'),
        //     'categoryCode' => config('toyyibpay.category'),
        //     'billName' => 'Kasih AP Digital',
        //     'billDescription' => 'Digital Gold to Physical Gold Conversion Postage Fee',
        //     'billPriceSetting' => 1,
        //     'billPayorInfo' => 1,
        //     'billAmount' => 1000, // Dikira dalam bentuk sen. Maksudnya dekat sini RM10
        //     'billReturnUrl' => route('toyyibpay-status-conv'),
        //     'billCallbackUrl' => route('toyyibpay-callback'),
        //     'billExternalReferenceNo' => 'KAP21345411',
        //     'billTo' => auth()->user()->name,
        //     'billEmail' => auth()->user()->email,
        //     'billPhone' => auth()->user()->profile->phone1,
        //     'billSplitPayment' => 0,
        //     'billSplitPaymentArgs' => '',
        //     'billPaymentChannel' => '0',
        //     'billContentEmail' => 'Thank you for purchasing our product!',
        //     'billChargeToCustomer' => 1
        // );

        // $url = 'https://dev.toyyibpay.com/index.php/api/createBill';
        // $response = Http::asForm()->post($url, $option);
        // $billCode = $response[0]['BillCode'];





        // foreach ($this->data as $product) {

        //     if ($product['qty'] != 0) {
        //         for ($i = 0; $i < $product['qty']; $i++) {
        //             $gold = GoldbarOwnership::where('user_id', auth()->user()->id)
        //                 ->where('active_ownership', 1)
        //                 ->first();



        //             if ($gold) {
        //                 $gold->active_ownership = 0;
        //                 $gold->referenceNumber = $billCode;
        //                 $gold->ex_flag = 0;
        //                 $gold->ex_id = $physicalId;
        //                 $gold->save();
        //             }
        //         }
        //     }
        // }

        // return redirect('https://dev.toyyibpay.com/' . $billCode);
    }

    public function render()
    {
        return view('livewire.page.physical-gold.phy-confirm-conversion');
    }
}
