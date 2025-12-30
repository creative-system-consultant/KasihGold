<?php

namespace App\Http\Livewire\Page\Payment;

use Livewire\Component;

class RedirectFiuu extends Component
{
    public $paymentData;
    
    public function mount($paymentData)
    {
        $this->paymentData = $paymentData;
    }
    
    public function render()
    {
        return view('livewire.page.payment.redirect-fiuu');
    }
}
