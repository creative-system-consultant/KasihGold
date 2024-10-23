<?php

namespace App\Http\Livewire\Page\Admin\UserGoldProfile;

use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\InvInfo;
use App\Models\InvItem;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;

class UserDetailedInfo extends Component
{

    use WithPagination;

    public $userId, $nric, $name, $pnum, $emel, $tdg, $tsg, $tdd;
    public $goldType, $digitalType, $transactionType;
    public $price, $weight;
    public $checker;

    public function mount()
    {
        $this->goldType = InvItem::where('item_type_id', NULL)->where('id', '!=', 12)->get();
        $this->userId = 0;
        $this->tdg = 0;
        $this->tsg = 0;
        $this->tdd = 0;

        // dd($this->allgold);
    }

    public function search()
    {
        $this->checker = 0;
        $this->tdg = 0;
        $this->tsg = 0;
        $this->tdd = 0;

        $customer = User::where('email', $this->emel)->first();
        if ($customer) {
            $this->checker = 1;
            $this->nric = $customer->profile->ic;
            $this->userId = $customer->id;
            $this->name = $customer->name;
            $this->pnum = $customer->phone_no;

            foreach ($customer->gold as $item) {
                if ($item->active_ownership == 1) {
                    if ($item->item_id == 10) {
                        $this->tdd += $item->available_weight;
                    } else if ($item->item_id == 12) {
                        $this->tsg += $item->available_weight;
                    } else {
                        $this->tdg += $item->available_weight;
                    }
                }
            }
        } else {
            $this->emit('message', 'User not found.');
        }
    }

    public function addGold()
    {
        if ($this->checker == 1 && $this->digitalType && $this->transactionType) {

            $weight = InvInfo::select('prod_weight')->where('item_id', $this->digitalType)->first();

            $available_goldbar = Goldbar::where('weight_vacant', '>=', $weight->prod_weight)->first();
            if ($available_goldbar) {
                GoldbarOwnership::create([
                    'gold_id'           => $available_goldbar->id,
                    'user_id'           => $this->userId,
                    'item_id'           => $this->digitalType,
                    'ouid'              => (string) Str::uuid(),
                    'weight'            => $weight->prod_weight,
                    'available_weight'  => $weight->prod_weight,
                    'bought_price'      => ($this->transactionType != 'J' ? 0 : str_replace(',', '', $this->price)),
                    'active_ownership'  => 1,
                    'spot_gold'         => ($this->digitalType == 12 ? 1 : 0),
                    'referenceNumber'   => ($this->transactionType != 'J' ? '000000' : 'JOMPAY'),
                    'created_by'        => 0,
                    'updated_by'        => 0,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                    'split'             => 0,
                ]);
            }
        } else {
            $this->emit('message', 'Please pick the necesssary details.');
        }
    }


    public function render()
    {
        $this->price = 0;
        $this->weight = 0;

        if ($this->digitalType) {
            $item_info = InvItem::where('id', $this->digitalType)->first();
            // dd($item_info);
            $this->price = number_format($item_info->marketPrice->price, 2);
            $this->weight = number_format($item_info->info->prod_weight, 2);
        }



        return view('livewire.page.admin.user-gold-profile.user-detailed-info', [
            'gold' => GoldbarOwnership::where('user_id', $this->userId)->paginate(5),
        ])->extends('default.default');
    }
}
