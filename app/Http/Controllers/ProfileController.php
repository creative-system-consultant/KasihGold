<?php

namespace App\Http\Controllers;

use App\Models\Profile_bank_info;
use App\Models\Profile_nominee;
use App\Models\Profile_personal;
use Illuminate\Http\Request;
use PDF;

class ProfileController extends Controller
{
    public function index()
    {
        return view('pages.profile.profile');
    }

    public function nomineePDF()
    {
        $nomineeList = Profile_nominee::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        $pdf = PDF::loadView('pages.profile.nominee.nomineePDF', compact('nomineeList'))->setPaper('A4','portrait');
        return $pdf->stream();
    }

    public function deleteIcFront($id)
    {
        Profile_personal::where('user_id', $id)->update([
            'ic_front' => NULL,
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function deleteIcBack($id)
    {
        Profile_personal::where('user_id', $id)->update([
            'ic_back' => NULL,
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function deleteBankStatement($id)
    {
        Profile_bank_info::where('user_id', $id)->update([
            'bank_statement' => NULL,
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}
