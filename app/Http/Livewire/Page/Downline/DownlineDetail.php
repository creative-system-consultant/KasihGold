<?php

namespace App\Http\Livewire\Page\Downline;

use App\Models\ReferralCode;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Session;

class DownlineDetail extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    function random_strings($length_of_string)
    {
        $str_result = '1234567890abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str_result), 0, $length_of_string);
    }

    public function generate($userId)
    {
        $user = User::whereId($userId)->first();

        $words = explode(' ', $user->name);
        if (count($words) >= 2) {
            $initial = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        } else {
            preg_match_all('#([A-Z]+)#', $user->name, $capitals);
            if (count($capitals[1]) >= 2) {
                return substr(implode('', $capitals[1]), 0, 2);
            }
            $initial = strtoupper(substr($user->name, 0, 2));
        }

        $randomNo = $this->random_strings(4);
        $referralCode = $initial . $randomNo;

        ReferralCode::updateOrCreate([
            'user_id'       => $userId
        ], [
            'referral_code'      => $referralCode,
        ]);

        session()->flash('success');
        session()->flash('title', 'Success!');
        session()->flash('message', 'Referral code has been successfully generated.');
    }

    public function buyForCustomer($customerId)
    {
        if (auth()->user()->financing_role == 1) {
            $customer = User::find($customerId);
            Session::put('buying_for_customer_id', $customerId);
            Session::put('buying_for_customer_name', $customer->name);
            return redirect()->route('product-view');
        }
    }

    public function render()
    {
        return view('livewire.page.downline.downline-detail', [
            'activeUser' => User::find(auth()->user()->id)->downline()->paginate(10),
        ]);
    }
}
