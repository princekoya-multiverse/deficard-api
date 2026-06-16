<?php

namespace App\Http\Controllers\Front;

use App\Models\CardActivation;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\KycVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Mail\CardLoadEmail;
use App\Mail\CardLoadAdminEmail;
use App\Mail\CardPurchaseAdminEmail;
use App\Mail\CardPurchaseEmail;
use Illuminate\Support\Facades\Mail;

class LoadPaymentController extends Controller
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
            'card_id' => 'required',
            'file' => 'required|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt',
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.ne_card', ['step' => '1'])->withInput()->with('error', 'All Fields are required')->withErrors($validator);
        }

        $kyc = KycVerification::where('user_id', auth()->user()->id)->first();
        if (!empty($kyc)) {
            if ($kyc->status != "Approved") {
                return redirect()->route('front.ne_card', ['step' => 1])->withInput()->with('error', 'Your Kyc Is not verified')->withErrors($validator);
            }
        } else {
            return redirect()->route('front.ne_card', ['step' => 1])->withInput()->with('error', 'Your Kyc Is not verified')->withErrors($validator);
        }
        $payment = new Payment();
        $payment->tx_id = $request->tx_id;
        $payment->type = $request->type;
        $payment->card_id = $request->card_id;
        $payment->user_id = auth()->user()->id;
        $file = $request->file;
        $file_name = time() . "." . $file->getClientOriginalName();
        $file->move(public_path() . "/uploads/payment_files", $file_name);
        $payment->file = $file_name;
        $payment->status = "In Process";
        $payment->save();
        return redirect()->route('front.ne_card', ['step' => 1])->with('message', 'Form Submit Successfully');
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

    public function notify_usdt_payment(Request $request)
    {
        $LOG = env('LOG_LEVEL') == 'debug';
        $content = trim($request->getContent());
        if($LOG) {
            logger('Token: ' . print_r($content, true));
        }
        try {
            $data = json_decode($content);
            $key = config('app.usdt-gateway-pub-key');
            $pub_key = new \Firebase\JWT\Key(file_get_contents(base_path($key)), 'RS256');
            $headers = new \stdClass;
            \Firebase\JWT\JWT::$leeway = 10;
            $decoded = \Firebase\JWT\JWT::decode($data->token, $pub_key, $headers);
            if(isset($decoded->data) && !empty($decoded->data))
            {
                if($LOG) {
                    logger('JWT Verified Decoded: '. PHP_EOL . print_r($decoded->data, true));
                    logger('Callback Status: ' . $decoded->data->status);
                }
                $trans_id = intval($decoded->data->id);
                $trans_status = trim($decoded->data->status);
                $trans_transaction = trim($decoded->data->transaction);
                $trans_address = trim($decoded->data->address);
                //$user_id = intval($decoded->data->meta->user_id ?? 0);
                $user_id = intval($decoded->data->label);
                $trans_amount = floatval($decoded->data->amount);
                $trans_from_address = trim($decoded->data->from);
                $trans_to_address = trim($decoded->data->to);
                $trans_fee = trim($decoded->data->fee);
                //--------------------------------------------------------------------
                $user = \App\Models\User::where('id', $user_id)->firstOrFail();
                //--------------------------------------------------------------------
                if(!empty($trans_address) && trim($trans_address) != '' && $user->gateway_address == $trans_address)
                {
                    if(strtolower($trans_status) == 'confirmed')
                    {
                        if($trans_amount > 0) {
                            $payment = Payment::create([
                                'tx_id' => $trans_transaction,
                                'type' => 'USDT',
                                'card_id' => 0,
                                'card_type' => null,
                                'card_holder_id' => null,
                                'user_id' => $user_id,
                                'file' => '',
                                'status' => 'Approved',
                                'trans_id' => $trans_id,
                                'trans_address' => $trans_address,
                                'trans_amount' => $trans_amount,
                                'trans_fee' => $trans_fee,
                                'trans_status' => $trans_status,
                                'trans_from' => $trans_from_address,
                                'trans_to' => $trans_to_address,
                            ]);
                            try {
                                Mail::to($payment->user->email)->send(new CardLoadEmail($payment));
                                Mail::to('necardload@gmail.com')->send(new CardLoadAdminEmail($payment));
                            } catch (\Exception $e) {
                                logger()->error('Mail Exception: ' . $e->getMessage());
                            }
                            return response('Done');
                        } else {
                            logger()->error('Notification Transaction amount error!');
                            return response('Transaction amount error!');
                        }
                    } else {
                        logger('Notification Status unconfirmed!');
                        return response('Notification Status unconfirmed!');
                    }
                }
                logger()->error('Notification Transaction Address / User ID Error!');
                return response('Notification Transaction Address / User ID Error!');
                //--------------------------------------------------------------------
            } else {
                logger()->error('Notification Transaction Invalid or Empty JWT Decoded Data!');
                return response('Notification Transaction Invalid or Empty JWT Decoded Data!');
            }
        } catch (\Exception $e) {
            logger()->error('JWT/Notification Exception: ' . $e->getMessage());
            logger()->error('Body Contents: ' . PHP_EOL . $content);
            return response('JWT Exception: ' . $e->getMessage());
        }
    }

    public function notify_card_purchase_usdt_payment(Request $request)
    {
        $LOG = env('LOG_LEVEL') == 'debug';
        $content = trim($request->getContent());
        if($LOG) {
            logger('Token: ' . print_r($content, true));
        }
        try {
            $data = json_decode($content);
            $key = config('app.usdt-gateway-pub-key');
            $pub_key = new \Firebase\JWT\Key(file_get_contents(base_path($key)), 'RS256');
            $headers = new \stdClass;
            \Firebase\JWT\JWT::$leeway = 10;
            $decoded = \Firebase\JWT\JWT::decode($data->token, $pub_key, $headers);
            if(isset($decoded->data) && !empty($decoded->data))
            {
                if($LOG) {
                    logger('JWT Verified Decoded: '. PHP_EOL . print_r($decoded->data, true));
                    logger('Callback Status: ' . $decoded->data->status);
                }
                $trans_id = intval($decoded->data->id);
                $trans_status = trim($decoded->data->status);
                $trans_transaction = trim($decoded->data->transaction);
                $trans_address = trim($decoded->data->address);
                //$user_id = intval($decoded->data->meta->user_id ?? 0);
                $user_id = intval($decoded->data->label);
                $trans_amount = floatval($decoded->data->amount);
                $trans_from_address = trim($decoded->data->from);
                $trans_to_address = trim($decoded->data->to);
                $trans_fee = trim($decoded->data->fee);
                //--------------------------------------------------------------------
                $user = \App\Models\User::where('id', $user_id)->firstOrFail();
                //--------------------------------------------------------------------
                if(!empty($trans_address) && trim($trans_address) != '')
                {
                    $payment = Payment::where('status', 'Pending')->where('trans_address', $trans_address)->firstOrFail();
                    if(strtolower($trans_status) == 'confirmed')
                    {
                        if($trans_amount >= floatval(config('app.usdt-gateway-card-onetime-fee'))) {
                            $payment->update([
                                'tx_id' => $trans_transaction,
                                //'type' => 'card',
                                //'card_id' => 0,
                                //'user_id' => $user_id,
                                //'file' => '',
                                'status' => 'Approved',
                                'trans_id' => $trans_id,
                                //'trans_address' => $trans_address,
                                'trans_amount' => $trans_amount,
                                'trans_fee' => $trans_fee,
                                'trans_status' => $trans_status,
                                'trans_from' => $trans_from_address,
                                'trans_to' => $trans_to_address,
                                'card_progress' => 'New',
                            ]);
                            try {
                                Mail::to($payment->user->email)->send(new CardPurchaseEmail($payment));
                                Mail::to('necardload@gmail.com')->send(new CardPurchaseAdminEmail($payment));
                            } catch (\Exception $e) {
                                logger()->error('Mail Exception: ' . $e->getMessage());
                            }
                            return response('Done');
                        } else {
                            logger()->error('Notification Transaction amount error!');
                            return response('Transaction amount error!');
                        }
                    } else {
                        logger('Notification Status unconfirmed!');
                        return response('Notification Status unconfirmed!');
                    }
                }
                logger()->error('Notification Transaction Address / User ID Error!');
                return response('Notification Transaction Address / User ID Error!');
                //--------------------------------------------------------------------
            } else {
                logger()->error('Notification Transaction Invalid or Empty JWT Decoded Data!');
                return response('Notification Transaction Invalid or Empty JWT Decoded Data!');
            }
        } catch (\Exception $e) {
            logger()->error('JWT/Notification Exception: ' . $e->getMessage());
            logger()->error('Body Contents: ' . PHP_EOL . $content);
            return response('JWT Exception: ' . $e->getMessage());
        }
    }

    public function update_usdt_payment_with_card(Payment $id, Request $request)
    {
        $user_id = auth()->user()->id;
        if($id->user_id != $user_id) {
            return response()->json(['error' => 'true', 'message' => 'Unauthorized'], 401);
        }
        //validate card
        $card_activation = CardActivation::where('id', intval($request->card_id))->where('user_id', $user_id)->first();
        if(!isset($card_activation) || empty($card_activation)) {
            return response()->json(['error' => 'true', 'message' => 'Card not found.'], 400);
        }
        if($card_activation?->status != 'Approved') {
            return response()->json(['error' => 'true', 'message' => 'Card not activated.'], 401);
        }
        if(intval($id->trans_loaded) === 1) {
            return response()->json(['error' => 'true', 'message' => 'Transaction already processed.'], 400);
        }
        //dd($request->card_id);
        //
        $id->card_id = intval($card_activation->id); //reference to card activation ID
        $id->card_type = $card_activation->card_type; //visa or mastercard
        //$id->card_holder_id = $card_activation->card_holder_id; //card holder ID
        $id->save();

        $id->save();
        return response()->json(['success' => 'true', 'message' => 'Card selected.']);
    }
}
