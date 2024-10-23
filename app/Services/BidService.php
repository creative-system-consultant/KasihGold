<?php

namespace App\Services;

use App\Models\ArrahnuAuctionList;
use App\Models\AuctionBidderList;
use Illuminate\Support\Facades\DB;
use Livewire\TemporaryUploadedFile;

class BidService
{
    protected $selectedSiri;
    protected $bids;
    protected $file;

    const BID_STEP = 1;
    const BID_FLAG = 20;

    public function __construct($selectedSiri, $bids, TemporaryUploadedFile $file = null)
    {
        $this->selectedSiri = $selectedSiri;
        $this->bids = $bids;
        $this->file = $file;
    }

    public function processBids()
    {
        $uniqueId = uniqid();
        $user = auth()->user();
        $cif = $this->getCustomerId($user);
        $filename = FileUploadService::upload($this->file, "{$user->id}/lelongan/{$uniqueId}");

        return $this->storeBids($cif, $uniqueId, $filename, $user);
    }

    protected function getCustomerId($user): int
    {
        $result = DB::select("EXEC arrahnu_kap_uat.ARRAHNU.sp_ar_insert_cust_kap_to_cif '" . config('app.client_id') . "', '" . $user->id . "'");
        return $result[0]->id;
    }

    protected function storeBids($cif, $uniqueId, $filename, $user)
    {
        $storedLelongans = [];

        foreach ($this->bids as $siri => $value) {
            if ($value) {
                $amtRezab = ArrahnuAuctionList::where('SIRI_NO', $siri)->value('AMT_REZAB');
                $lelongan = AuctionBidderList::create([
                    'CIF_NO' => $cif,
                    'IDENTITY_NO' => $user->profile->ic,
                    'STEP' => self::BID_STEP,
                    'FLAG' => self::BID_FLAG,
                    'BID_ID' => $uniqueId,
                    'SIRI_NO' => $siri,
                    'BID_AMT' => $value,
                    'REZAB_PRICE' => $amtRezab,
                    'FILES' => $filename,
                    'BID_AT' => now(),
                    'BRANCH_CODE' => substr($siri, 4, 1),
                    'CLIENT_ID' => config('app.client_id'),
                ]);

                $storedLelongans[] = $lelongan;
            }
        }

        return $storedLelongans;
    }
}
