<?php

namespace App\Http\Livewire\Page\DigitalGold;


use App\Models\GoldbarOwnership;
use Livewire\Component;

class DigitalGold extends Component
{
    public $goldInfo;
    public $tGold, $tPrice;
    public $goldInfoD;
    public $tGoldD, $tPriceD;
    public $tGoldS, $tPriceS;
    public $tGoldN;
    public $tGoldNL;
    public $tGoldDL;
    public $tGoldSL;


    public function mount()
    {
        $this->tGoldN = 0;
        $this->tGoldD = 0;
        $this->tGoldS = 0;
        $this->tGoldNL = 0;
        $this->tGoldDL = 0;
        $this->tGoldSL = 0;
        $goldInfo = GoldbarOwnership::where('user_id', auth()->user()->id)->where('active_ownership', 1)->where('weight', '<>', '4.25')->where('spot_gold', 0)->get();
        foreach ($goldInfo as $golds) {
            if ($golds->financing_flag == 0) {
                $this->tGoldN += $golds->available_weight;
            } else {
                $this->tGoldNL += $golds->available_weight;
            }
            $this->tGold += $golds->available_weight;
            $this->tPrice += $golds->bought_price;
        }
        $goldInfoD = GoldbarOwnership::where('user_id', auth()->user()->id)->where('active_ownership', 1)->where('weight', '4.25')->get();
        foreach ($goldInfoD as $golds) {
            if ($golds->financing_flag == 0) {
                $this->tGoldD += $golds->available_weight;
            } else {
                $this->tGoldDL += $golds->available_weight;
            }
            $this->tGold += $golds->available_weight;
            $this->tPriceD += $golds->bought_price;
        }

        $goldInfoS = GoldbarOwnership::where('user_id', auth()->user()->id)->where('active_ownership', 1)->where('spot_gold', 1)->get();
        foreach ($goldInfoS as $golds) {
            if ($golds->financing_flag == 0) {
                $this->tGoldS += $golds->available_weight;
            } else {
                $this->tGoldSL += $golds->available_weight;
            }
            $this->tPriceS += $golds->bought_price;
            $this->tGold += $golds->available_weight;
        }
        $this->tGold = number_format($this->tGold, 2);
        $this->tGoldN = number_format($this->tGoldN, 2);
        $this->tGoldD = number_format($this->tGoldD, 2);
        $this->tGoldS = number_format($this->tGoldS, 2);
        $this->tGoldNL = number_format($this->tGoldNL, 2);
        $this->tGoldDL = number_format($this->tGoldDL, 2);
        $this->tGoldSL = number_format($this->tGoldSL, 2);
    }

    public function details()
    {
        return redirect('digital-gold-details');
    }

    public function render()
    {
        return view('livewire.page.digital-gold.digital-gold');
    }
}
