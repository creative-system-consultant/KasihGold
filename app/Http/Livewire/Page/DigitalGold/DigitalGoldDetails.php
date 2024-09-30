<?php

namespace App\Http\Livewire\Page\DigitalGold;

use App\Models\GoldbarOwnership;
use Livewire\Component;
use Livewire\WithPagination;

class DigitalGoldDetails extends Component
{
    use WithPagination;

    public $total, $tPrice, $paidCount, $lockedCount;

    public function mount()
    {
        $goldInfo = GoldbarOwnership::where('user_id', auth()->user()->id)->get();

        $this->total = 0;
        $this->tPrice = 0;
        $this->paidCount = 0;
        $this->lockedCount = 0;

        foreach ($goldInfo as $gold) {
            if ($gold->active_ownership) {
                $this->total += $gold->weight;
                $this->tPrice += $gold->bought_price;
            }
            if ($gold->financing_flag == 0) {
                $this->paidCount++;
            } else {
                $this->lockedCount++;
            }
        }
    }

    public function render()
    {
        return view('livewire.page.digital-gold.digital-gold-details', [
            'details' => GoldbarOwnership::where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(5),
        ]);
    }
}
