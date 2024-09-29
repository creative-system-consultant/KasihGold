<?php

namespace App\Http\Livewire\Page\Shop;

use App\Models\InvInfo;
use App\Models\InvItem;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ProductView extends Component
{

    public $digitalGold, $digitalDinar, $premiumGold;

    public function clearBuyingForCustomer()
    {
        Session::forget(['buying_for_customer_id', 'buying_for_customer_name']);
        $this->emit('buyingForCustomerCleared');
    }

    public function mount()
    {
        $this->digitalGold = InvInfo::where('prod_cat', '1')->get();
        $this->digitalDinar = InvInfo::where('prod_cat', '2')->get();
        $this->premiumGold = InvInfo::where('prod_cat', '3')->get();
    }
    public function render()
    {

        return view('livewire.page.shop.product-view');
    }
}
