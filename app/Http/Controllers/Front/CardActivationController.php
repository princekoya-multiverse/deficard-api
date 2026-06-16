<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Models\CardActivation;
use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use Illuminate\Support\Facades\Validator;

class CardActivationController extends Controller
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
            'number' => 'required',
            'kit_number' => 'required_if:card_type,Visa',
            'card_type' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('front.ne_card', ['step' => 3])->withInput()->with('error', 'All Fields are required')->withErrors($validator);
        }

        $kyc = KycVerification::where('user_id', auth()->user()->id)->first();
        if (!empty($kyc)) {
            if ($kyc->status != "Approved") {
                return redirect()->route('front.ne_card', ['step' => 3])->withInput()->with('error', 'Your Kyc is not verified')->withErrors($validator);
            }
        } else {
            return redirect()->route('front.ne_card', ['step' => 3])->withInput()->with('error', 'Your Kyc is not verified')->withErrors($validator);
        }

        $card_activation = new CardActivation();
        $card_activation->number = $request->number;
        $card_activation->kit_number = $request->kit_number;
        $card_activation->card_type = $request->card_type;
        $card_activation->user_id = auth()->user()->id;
        $card_activation->save();
        return redirect()->route('front.ne_card', ['step' => 3])->with('message', 'Card Activation applied successfully. Please wait for approval.');
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
