<?php

namespace App\Http\Livewire\Page\Setting;

use App\Models\ReferralCode;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Session;

class FinancingAgent extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingUserId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    function random_strings($length_of_string)
    {
        $str_result = '1234567890abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str_result), 0, $length_of_string);
    }

    public function confirmToggleFinancingRole($userId)
    {
        $this->confirmingUserId = $userId;
    }

    public function toggleFinancingRole()
    {
        $user = User::find($this->confirmingUserId);
        if ($user) {
            $newRole = $user->financing_role == 0 ? 1 : 0;
            $user->financing_role = $newRole;
            $user->save();
            $status = $newRole == 1 ? 'enabled' : 'disabled';
            session()->flash('success', "Financing role has been {$status} successfully.");
        }
        $this->confirmingUserId = null;
    }

    public function render()
    {
        $activeUsers = User::where('role', 3)->where('active', 1)->paginate(10);

        return view('livewire.page.setting.financing-agent', [
            'activeUser' => $activeUsers,
        ]);
    }
}
