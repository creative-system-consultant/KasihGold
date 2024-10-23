<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'              => 'required|string',
            'email'             => 'required|email|unique:users',
            'password'          => 'required|min:8|confirmed',
            'phone_no'          => 'required|string|min:10',
            'referral_code'     => 'required|min:6|exists:App\Models\ReferralCode,referral_code',
            'tnc'               => 'required',
        ];
    }
}
