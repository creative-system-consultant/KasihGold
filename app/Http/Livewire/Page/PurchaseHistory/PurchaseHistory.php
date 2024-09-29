<?php

namespace App\Http\Livewire\Page\PurchaseHistory;

use App\Models\GoldbarOwnership;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PurchaseHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $userId;
    public $confirmingId;

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function render()
    {
        $user = User::findOrFail($this->userId);

        $list = GoldbarOwnership::where('user_id', $this->userId)
            ->select(
                'referenceNumber',
                DB::raw('SUM(weight) as total_weight'),
                DB::raw('SUM(bought_price) as total_price'),
                DB::raw('CONVERT(date, created_at) as purchase_date'),
                DB::raw('MAX(financing_flag) as financing_flag')
            )
            ->groupBy('referenceNumber', DB::raw('CONVERT(date, created_at)'))
            ->orderBy('purchase_date', 'desc')
            ->paginate(10);

        return view('livewire.page.purchase-history.purchase-history', [
            'list' => $list,
            'user' => $user,
        ])->extends('default.default')->section('content');
    }

    public function confirmFinancingChange($referenceNumber)
    {
        $this->confirmingId = $referenceNumber;
    }

    public function changeFinancingFlag($referenceNumber)
    {
        GoldbarOwnership::where('referenceNumber', $referenceNumber)
            ->update(['financing_flag' => 0]);

        $this->confirmingId = null;
    }
}
