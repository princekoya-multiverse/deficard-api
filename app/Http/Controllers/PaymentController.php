<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\KycVerification;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tx_id' => 'required',
            'type' => 'required',
            'file' => 'required|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt', // Explicitly allow safe types',
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.ne_card', ['step' => $request->step])->withInput()->with('error', 'All Fields are required')->withErrors($validator);
        }

        $kyc = KycVerification::where('user_id', auth()->user()->id)->first();
        if ($request->type == "card") {
            /*
            if (!empty($kyc)) {
                if ($kyc->status != "Approved") {
                    return redirect()->route('front.ne_card', ['step' => $request->step])->withInput()->with('error', 'Your Kyc Is not verified')->withErrors($validator);
                }
            } else {
                return redirect()->route('front.ne_card', ['step' => $request->step])->withInput()->with('error', 'Your Kyc Is not verified')->withErrors($validator);
            }
            */
        }

        $payment = new Payment();
        $payment->tx_id = $request->tx_id;
        $payment->type = $request->type;
        $payment->user_id = auth()->user()->id;
        $file = $request->file;
        $file_name = time() . "." . $file->getClientOriginalName();
        $file->move(public_path() . "/uploads/payment_files", $file_name);
        $payment->file = $file_name;
        $payment->status = "In Process";
        $payment->save();
        return redirect()->route('front.ne_card', ['step' => $request->step])->with('message', 'Form Submit Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
