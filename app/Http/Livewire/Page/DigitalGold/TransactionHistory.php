<?php

namespace App\Http\Livewire\Page\DigitalGold;

use App\Models\FiuuBills;
use App\Models\ToyyibBills;
use Livewire\Component;
use Livewire\WithPagination;


class TransactionHistory extends Component
{
    use WithPagination;
    public $totalGrammage;

    public function mount()
    {
        $this->totalGrammage = 0;
    }

    public function render()
    {
        $toyyib = ToyyibBills::where('created_by', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(5);

        $fiuu = FiuuBills::where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(5);

        // return view('livewire.page.digital-gold.transaction-history', [
        //     'toyyib' => $toyyib,
        //     'fiuu' => $fiuu
        // ]);

        return view('livewire.page.digital-gold.transaction-history', [
            'history' => ToyyibBills::where('created_by', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(5),
        ]);
    }
}
