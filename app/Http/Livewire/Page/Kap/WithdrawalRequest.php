<?php

namespace App\Http\Livewire\Page\Kap;

use App\Mail\PhysicalDetails\PhysicalGoldExchange;
use App\Models\BuyBack;
use App\Models\Goldbar;
use App\Models\GoldbarOwnership;
use App\Models\GoldMinting;
use App\Models\GoldMintingRecords;
use App\Models\OutrightSell;
use App\Models\outrightSG;
use App\Models\outrightSGRecords;
use App\Models\PhysicalConvert;
use App\Models\ToyyibBills;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class WithdrawalRequest extends Component
{
    use WithFileUploads;

    public $appid, $proofdoc;
    public $showModal = false;
    public $activeModal = null;

    public function showModal($id)
    {
        $this->activeModal = $id;
    }

    public function closeModal()
    {
        $this->activeModal = null;
    }

    public function outApp($appid)
    {
        $this->validate([
            'proofdoc' => 'required|file|max:4096', // 4MB Max

        ]);

        $outright = OutrightSell::where('id', $appid)->first();

        $outright->status = 1;
        $outright->doc_1 = $this->proofdoc->storeAs('public/exit', $outright->id . '-Outright-ProofOfTransfer.jpg');;
        $outright->save();

        $goldOwnership = GoldbarOwnership::where('ex_id', $outright->id)->where('user_id', $outright->user_id)->get();

        foreach ($goldOwnership as $ownership) {

            $goldBar = Goldbar::where('id', $ownership->gold_id)->first();
            $goldBar->weight_occupied -= $ownership->weight;
            $goldBar->weight_vacant += $ownership->weight;
            $goldBar->save();
            if ($ownership->financing_flag == 2) {
                $ownership->financing_flag = 3;
            }
            $ownership->ex_flag = 1;
            $ownership->save();
        }
        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'success',
            'message' => 'Outright Sell has successfully approved!'
        ]);
    }

    public function outDec($appid)
    {
        $outright = OutrightSell::where('id', $appid)->first();

        $outright->status = 2;
        $outright->save();

        $goldOwnership = GoldbarOwnership::where('ex_id', $outright->id)->where('user_id', $outright->user_id)->get();

        foreach ($goldOwnership as $ownership) {
            if ($ownership->financing_flag == 2) {
                $ownership->financing_flag = 1;
            }
            $ownership->ex_flag = 2;
            $ownership->active_ownership = 1;
            $ownership->save();
        }

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'warning',
            'message' => 'Outright Sell has been declined and the gold is returned back to their inventory!'
        ]);
    }

    public function bbApp($appid)
    {
        $this->validate([
            'proofdoc' => 'required|file|max:4096', // 4MB Max

        ]);

        $buyback = BuyBack::where('id', $appid)->first();

        $buyback->status = 1;
        $buyback->doc_1 = $this->proofdoc->storeAs('public/exit', $buyback->id . '-Buyback-ProofOfTransfer.jpg');
        $buyback->save();

        $goldOwnership = GoldbarOwnership::where('ex_id', $buyback->id)->where('user_id', $buyback->user_id)->get();

        foreach ($goldOwnership as $ownership) {

            $goldBar = Goldbar::where('id', $ownership->gold_id)->first();
            $goldBar->weight_occupied -= $ownership->weight;
            $goldBar->weight_vacant += $ownership->weight;
            $goldBar->save();

            $ownership->ex_flag = 1;
            $ownership->save();
        }

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'success',
            'message' => 'Buyback has successfully approved!'
        ]);
    }

    public function bbDec($appid)
    {


        $buyback = BuyBack::where('id', $appid)->first();

        $buyback->status = 2;
        $buyback->save();

        $goldOwnership = GoldbarOwnership::where('ex_id', $buyback->id)->where('user_id', $buyback->user_id)->get();

        foreach ($goldOwnership as $ownership) {
            $ownership->ex_flag = 2;
            $ownership->active_ownership = 1;
            $ownership->save();
        }

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'warning',
            'message' => 'Buyback has been declined and the gold is returned back to their inventory!'
        ]);
    }

    public function pConvApp($appid)
    {

        $phyConv = PhysicalConvert::where('id', $appid)->first();
        $toyyibBill = ToyyibBills::where('bill_code', $phyConv->ref_payment)->first();
        $phyConv->status = 1;
        $phyConv->save();


        $goldOwnership = GoldbarOwnership::where('ex_id', $phyConv->id)->where('user_id', $phyConv->user_id)->get();

        foreach ($goldOwnership as $ownership) {

            $goldBar = Goldbar::where('id', $ownership->gold_id)->first();
            $goldBar->weight_occupied -= $ownership->weight;
            $goldBar->weight_vacant += $ownership->weight;
            $goldBar->save();

            $ownership->ex_flag = 1;
            $ownership->save();
        }

        Mail::to("hadikasihgold@gmail.com")->send(new PhysicalGoldExchange($phyConv, $toyyibBill));

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'success',
            'message' => 'Physical Conversion has successfully approved and will reach at their doorstep soon!'
        ]);
    }

    public function pConvDec($appid)
    {

        $phyConv = PhysicalConvert::where('id', $appid)->first();
        $toyyibBill = ToyyibBills::where('bill_code', $phyConv->ref_payment)->first();

        $phyConv->status = 2;
        $phyConv->save();


        $goldOwnership = GoldbarOwnership::where('ex_id', $phyConv->id)->where('user_id', $phyConv->user_id)->get();

        foreach ($goldOwnership as $ownership) {
            $ownership->ex_flag = 2;
            $ownership->active_ownership = 1;
            $ownership->save();
        }

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'warning',
            'message' => 'Physical Conversion has been declined and the gold is returned back to their inventory!'
        ]);
    }

    public function gMintApp($appid)
    {

        $goldMint = GoldMinting::where('id', $appid)->first();
        $toyyibBill = ToyyibBills::where('bill_code', $goldMint->bill_code)->first();
        $goldMint->status = 1;
        $goldMint->save();

        $goldMintRecord = GoldMintingRecords::where('status', 3)->where('bill_code', $goldMint->bill_code)->get();




        foreach ($goldMintRecord as $ownership) {

            $goldOwnership = GoldbarOwnership::where('id', $ownership->gold_ids)->where('ex_id', $goldMint->id)->where('user_id', $goldMint->user_id)->where('ex_flag', 5)->first();


            $goldBar = Goldbar::where('id', $goldOwnership->gold_id)->first();
            $goldBar->weight_occupied -= $ownership->grammage;
            $goldBar->weight_vacant += $ownership->grammage;
            $goldBar->save();

            $goldOwnership->ex_flag = 1; //success flag for Gold Minting Exit
            $ownership->save();
        }

        Mail::to("mehmediskandar7@gmail.com")->send(new PhysicalGoldExchange($goldMint, $toyyibBill));

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'success',
            'message' => 'Gold Minting has successfully approved and will reach at their doorstep soon!'
        ]);
    }

    public function gMintDec($appid)
    {

        $goldMint = GoldMinting::where('id', $appid)->first();
        $toyyibBill = ToyyibBills::where('bill_code', $goldMint->bill_code)->first();

        $goldMint->status = 2;
        $goldMint->save();


        $goldOwnership = GoldbarOwnership::where('ex_id', $goldMint->id)->where('user_id', $goldMint->user_id)->where('ex_flag', 5)->get();

        foreach ($goldOwnership as $ownership) {
            $goldMintRecord = GoldMintingRecords::where('gold_ids', $ownership->id)->where('status', 3)->where('bill_code', $goldMint->bill_code)->first();
            $ownership->available_weight += $goldMintRecord->grammage;
            $ownership->ex_flag = 7; //fail flag for Gold Minting Exit
            if ($ownership->active_ownership == 0) {
                $ownership->active_ownership = 1;
            }
            $goldMintRecord->update(['status' => 2]);
            $ownership->save();
        }

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'warning',
            'message' => 'Gold Minting Request has been declined and the gold is returned back to their inventory!'
        ]);
    }

    public function SGOutApp($appid)
    {

        $this->validate([
            'proofdoc' => 'required|file|max:4096', // 4MB Max

        ]);

        $goldMint = outrightSG::where('id', $appid)->first();
        $goldMint->status = 1;
        $goldMint->doc_1 = $this->proofdoc->storeAs('public/exit', $goldMint->id . '-Outright-ProofOfTransfer.jpg');

        $goldMint->save();

        $goldMintRecord = outrightSGRecords::where('status', 3)->where('exit_id', $goldMint->id)->get();




        foreach ($goldMintRecord as $ownership) {

            $goldOwnership = GoldbarOwnership::where('id', $ownership->gold_ids)->where('ex_id', $goldMint->id)->where('user_id', $goldMint->user_id)->where('ex_flag', 8)->first();


            $goldBar = Goldbar::where('id', $goldOwnership->gold_id)->first();
            $goldBar->weight_occupied -= $ownership->grammage;
            $goldBar->weight_vacant += $ownership->grammage;
            $goldBar->save();

            $ownership->status = 1; //success flag for Gold Minting Exit
            $ownership->save();
        }

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'success',
            'message' => 'Spot Gold Outright Request has successfully approved!'
        ]);
    }

    public function SGOutDec($appid)
    {

        $goldMint = outrightSG::where('id', $appid)->first();
        $goldMint->status = 2;
        $goldMint->save();


        $goldOwnership = GoldbarOwnership::where('ex_id', $goldMint->id)->where('user_id', $goldMint->user_id)->where('ex_flag', 8)->get();

        foreach ($goldOwnership as $ownership) {
            $goldMintRecord = outrightSGRecords::where('gold_ids', $ownership->id)->where('status', 3)->where('exit_id', $goldMint->id)->first();
            $ownership->available_weight += $goldMintRecord->grammage;
            $ownership->status = 10; //fail flag for Gold Minting Exit
            if ($ownership->active_ownership == 0) {
                $ownership->active_ownership = 1;
            }
            $goldMintRecord->update(['status' => 2]);
            $ownership->save();
        }

        $this->closeModal();
        $this->refreshComponent();

        $this->emit('message', [
            'type' => 'warning',
            'message' => 'Spot Gold Outright Request has been declined and the gold is returned back to their inventory!'
        ]);
    }

    public function refreshComponent()
    {
        $this->emit('refreshComponent');
    }

    public function render()
    {
        $outright = OutrightSell::where('status', 0)->paginate(5);
        $buybacks = BuyBack::where('status', 0)->paginate(5);
        $physical = PhysicalConvert::where('status', 0)->paginate(5);
        $spotgold = GoldMinting::where('status', 0)->paginate(5);
        $spotgoldO = outrightSG::where('status', 0)->paginate(5);

        return view('livewire.page.kap.withdrawal-request', [
            'outright' => $outright,
            'buybacks' => $buybacks,
            'physical' => $physical,
            'spotgold' => $spotgold,
            'spotgoldO' => $spotgoldO,
            'outrightCount' => OutrightSell::where('status', 0)->count(),
            'buybacksCount' => BuyBack::where('status', 0)->count(),
            'physicalCount' => PhysicalConvert::where('status', 0)->count(),
            'spotgoldCount' => GoldMinting::where('status', 0)->count(),
            'spotgoldOCount' => outrightSG::where('status', 0)->count(),
        ]);
    }
}
