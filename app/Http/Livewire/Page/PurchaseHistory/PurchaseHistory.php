<?php

namespace App\Http\Livewire\Page\PurchaseHistory;

use App\Models\GoldbarOwnership;
use App\Models\User;
use App\Models\OutrightSell;
use App\Models\OutrightPrice;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PurchaseHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $userId;
    public $confirmingId;
    public $confirmingDefaultId;
    public $currentOutrightPrice;
    public $selectedWeight;
    public $totalSellAmount;

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

    public function confirmDefault($referenceNumber)
    {
        $this->confirmingDefaultId = $referenceNumber;
        $this->currentOutrightPrice = $this->getCurrentOutrightPrice();
        $this->selectedWeight = GoldbarOwnership::where('referenceNumber', $referenceNumber)
            ->sum('available_weight');
        $this->totalSellAmount = $this->selectedWeight * $this->currentOutrightPrice;
    }

    public function defaultPurchase($referenceNumber)
    {
        $weightBreakdown = $this->breakdownWeight($this->selectedWeight);

        // Create new OutrightSell record
        $outright = OutrightSell::create([
            'user_id' => $this->userId,
            'status' => 0,
            'ouid'   => (string) Str::uuid(),
            'centigram' => $weightBreakdown['centigram'],
            'decigram' => $weightBreakdown['decigram'],
            'quarter_gram' => $weightBreakdown['quarter_gram'],
            'one_gram' => $weightBreakdown['one_gram'],
            'beyond1G' => $weightBreakdown['beyond1G'],
            'surrendered_amount' => $this->totalSellAmount,
            'ref_payment' => $referenceNumber,
            'created_by' => auth()->user()->id,
        ]);

        $outrightId = $outright->id;

        // Update GoldbarOwnership records
        GoldbarOwnership::where('referenceNumber', $referenceNumber)
            ->update([
                'financing_flag' => 2, //pending sell
                'active_ownership' => 0,
                'ex_id' => $outrightId
            ]);

        $this->confirmingDefaultId = null;
    }

    private function getCurrentOutrightPrice()
    {
        $outrightPrice = OutrightPrice::where('item_id', 9)->latest()->first();
        return $outrightPrice ? $outrightPrice->price : 0;
    }

    private function breakdownWeight($totalWeight)
    {
        $breakdown = [
            'centigram' => 0,
            'decigram' => 0,
            'quarter_gram' => 0,
            'one_gram' => 0,
            'beyond1G' => 0,
        ];

        $remainingWeight = $totalWeight;

        // Handle beyond1G (grams beyond 1g)
        $breakdown['beyond1G'] = floor($remainingWeight);
        $remainingWeight -= $breakdown['beyond1G'];

        // Handle one_gram
        $breakdown['one_gram'] = floor($remainingWeight);
        $remainingWeight -= $breakdown['one_gram'];

        // Handle quarter_gram
        $breakdown['quarter_gram'] = floor($remainingWeight / 0.25);
        $remainingWeight -= $breakdown['quarter_gram'] * 0.25;

        // Handle decigram
        $breakdown['decigram'] = floor($remainingWeight / 0.1);
        $remainingWeight -= $breakdown['decigram'] * 0.1;

        // Handle centigram
        $breakdown['centigram'] = round($remainingWeight / 0.01);

        return $breakdown;
    }
}
