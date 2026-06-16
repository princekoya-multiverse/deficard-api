<?php

namespace App\Http\Controllers\Front;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\KycVerification;
use App\Http\Controllers\Controller;
//use App\Mail\CardActivation;
use App\Models\CardActivation as CardActivationModel;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function home()
    {
        return view('front.home')->with([
            'testimonials' => [
                0 => ['name' => 'Sarah J. from Vancouver 🇨🇦', 'description' => 'It was simple to use and the funds were deposited promptly into my card.'],
                1 => ['name' => 'Lindsey A. from Vancouver 🇨🇦', 'description' => 'Ease of use, straight forward approach and no nonsense.'],
                2 => ['name' => 'Jasica K. from Toronto 🇨🇦', 'description' => 'Overall the online portal is very easy to use and it’s very convenient!! 😊'],
                3 => ['name' => 'John M. from Ottawa 🇨🇦', 'description' => 'You get your digital card as soon as you sign up so you can use the card.'],
                4 => ['name' => 'Sandra L. from Toronto 🇨🇦', 'description' => 'I was able to get my card activated in less than 24 hours.'],
            ],
        ]);
    }

    public function terms()
    {
        return view('front.terms');
    }

    public function ne_card(Request $request)
    {
        $user = $request->user();
        $kyc_verification = KycVerification::where('user_id', $user->id)->first();
        $kyc = $kyc_verification ?? '';

        $kyc_payment_data = Payment::where(['user_id' => $user->id, 'type' => 'kyc'])->get();
        $kyc_payments = $kyc_payment_data  ?? [];

        $card_payment_data = Payment::where(['user_id' => $user->id, 'type' => 'card'])->where('status', '!=', 'Pending')->orderBy('id', 'DESC')->get();
        $card_payments = $card_payment_data ?? [];

        $pending_card_payment_data = Payment::where(['user_id' => $user->id, 'type' => 'card'])->where('status', '=', 'Pending')->first();
        $pending_card_payment = $pending_card_payment_data ?? [];

        $cards_data = CardActivationModel::where(['user_id' => $user->id, 'status' => 'Approved'])->get();
        $cards = $cards_data ?? [];

        $card_load_data = Payment::where(['user_id' => $user->id])->whereIn('type', ['USDT','load'])->with('user','card')->orderBy('id', 'DESC')->get();
        $card_datas = $card_load_data ?? [];

        $activation_card_data = CardActivationModel::where(['user_id' => $user->id])->with('user')->orderBy('id', 'DESC')->get();
        $activation_card = $activation_card_data ?? [];

        $any_card_active = false;
        $selectedCard = null;
        foreach($activation_card as $card) {
            if($card->status == 'Approved') {
                $any_card_active = true;
                break;
            }
        }
        foreach($cards as $card) {
            if($card->id == $request->card_selected) {
                $selectedCard = $card;
                break;
            }
        }
        //dump($selectedCard?->toArray());
        //------------------------------------------------------------------------
        $countries = DB::table('countries')->get();
        //dump($countries);
        //------------------------------------------------------------------------
        $card_balance = 0;
        if($selectedCard && strtolower(trim($selectedCard->card_type)) == 'visa') {
            $uri = '/api/ServiceProvider_24/ViewCardBalance/%s/%s/%s/%s';
            if(!config('app.debug')) {
                try {
                    $account_id = config('app.necard_acc_id');
                    $wallet_id = config('app.necard_acc_wallet');
                    $client = new Client([
                    'base_uri' => config('app.necard_api_url'),
                    'headers' => [
                                    'Content-Type' => 'application/json',
                                    'Accept' => 'application/json',
                                    'ApiAuthUsername' => config('app.necard_user'),
                                    'ApiAuthPassword' => config('app.necard_password')
                                ],
                    ]);
                    //$query_arr = ['CardHolderId' => $selectedCard->card_holder_id];
                    $uri = sprintf($uri, $account_id, $wallet_id, $selectedCard->card_holder_id, $selectedCard->card_id);
                    //dump($uri);
                    $response = $client->request('GET', $uri, [
                        //'query' => $query_arr,
                        'headers' => [
                            'Accept' => 'application/json',
                        ]
                    ]);
                    if($response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody()->getContents(), true);
                        //dump($data);
                        $card_balance = number_format(floatval($data/100),2,'.','');
                    }
                } catch (\Exception $e) {
                    logger()->error('Exception: Unable to retrieve card balance.', ['error' => $e->getMessage()]);
                }
            }
        }
        //------------------------------------------------------------------------
        if($selectedCard && strtolower(trim($selectedCard->card_type)) == 'mastercard') {
            $card_balance = 0;
            $uri = '/api/ServiceProvider_37/ViewCardBalance/%s/%s/%s/%s';
            if(!config('app.debug')) {
                try {
                    $wallet_id = config('app.necard_acc_wallet_mc');
                    $account_id = config('app.necard_acc_id_mc');
                    $client = new Client([
                    'base_uri' => config('app.necard_api_url_mc'),
                    'headers' => [
                                    'Content-Type' => 'application/json',
                                    'Accept' => 'application/json',
                                    'ApiAuthUsername' => config('app.necard_user_mc'),
                                    'ApiAuthPassword' => config('app.necard_password_mc')
                                ],
                    ]);
                    //dump(config('app.necard_api_url_mc'));
                    $uri = sprintf($uri, $account_id, $wallet_id, $selectedCard->card_holder_id, $selectedCard->card_id);
                    //dump($uri);
                    $response = $client->request('GET', $uri, [
                        'headers' => [
                            'Accept' => 'application/json',
                        ]
                    ]);
                    if($response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody()->getContents());
                        //dump($data);
                        $card_balance = number_format(floatval($data?->availableBalance/100),2,'.',''); // { "availableBalance": 379, "ledgerBalance": 5879, "currency": "840" }
                    }
                } catch (\Exception $e) {
                    logger()->error('Exception: Unable to retrieve card balance.', ['error' => $e->getMessage()]);
                }
            }
        }
        //------------------------------------------------------------------------
        $qr_code_path = url('qr_codes/'.md5('A~'.strval($user->id)).'.svg');
        $gateway_address = $user->gateway_address;
        if($any_card_active)
        {
            try {
                if($user->gateway_address == null) {
                    $gw_address = $this->remote_get_usdt_address($user->id, ['user_id' => $user->id]);
                    if(empty($gw_address) || trim($gw_address)=='') {
                        logger()->error('Remote usdt gateway address empty.');
                        //return response()->json(['success' => false, 'message' => 'System error. Please contact support.']);
                    } else {
                        $qr_code_path = 'qr_codes/'.md5('A~'.strval($user->id)).'.svg';
                        QrCode::format('svg')->size(300)->generate($gw_address, storage_path('app/'.$qr_code_path));
                        $user->update([
                            'gateway_address' => $gw_address
                        ]);
                        $gateway_address = $gw_address;
                    }
                }
            } catch (\Throwable $e) {
                logger()->error('Exception: gateway address: ['.$e->getMessage().']');
            }
        }
        //------------------------------------------------------------------------
        //get the loadded transactions
        $account_balance = Payment::where('user_id', $user->id)->where('type', 'USDT')->selectRaw('SUM(trans_amount) as total_trans_amount, SUM(trans_fee) as total_trans_fee')->first();
        $account_balance = (number_format($account_balance->total_trans_amount, 2,'.','') - number_format($account_balance->total_trans_fee,2,'.','')) ?? 0;
        //------------------------------------------------------------------------
        if($pending_card_payment)
        {
            $card_payment_qr_address = $pending_card_payment->trans_address;
            $card_payment_qr_code_path = url('qr_codes/'.md5('B~'.strval($user->id)).'.svg');
        } else {
            $card_payment_qr_address = '';
            $card_payment_qr_code_path = '';
        }
        //------------------------------------------------------------------------
        //if kyc is in process and there is any payment approved master cards, apply the kyc
        $kyc2 = null;
        foreach($card_payment_data as $card) {
            if($card->status == 'Approved' && $card->card_type == 'Mastercard') {
                $kyc2 = KycVerification::where('user_id', $user->id)->where('status', '!=', 'Approved')->latest()->first();
                if(!$kyc2) { continue; }
                try {
                    $uri = '/api/CardHolderManagement/CardHolder_View/%s/%s';
                    $account_id = config('app.necard_acc_id_mc');
                    $client = new Client([
                        'base_uri' => config('app.necard_api_url_mc'),
                        'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user_mc'),
                                'ApiAuthPassword' => config('app.necard_password_mc')
                            ],
                        ]);
                    //dump(config('app.necard_api_url_mc'));
                    $uri = sprintf($uri, $account_id, $card->card_holder_id);
                    //dump($uri);
                    $response = $client->request('GET', $uri, [
                        'headers' => [
                            'Accept' => 'application/json',
                        ]
                    ]);
                    if($response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody()->getContents());
                        if(!empty($data->sysRes2)) {
                            $kyc2->mastercard_kyc_url = $data->sysRes2;
                            $kyc2->save();
                            if($kyc) {
                                $kyc->refresh();
                                //$kyc = KycVerification::where('user_id', $user->id)->first();
                            }
                        }
                    }
                    //--------------------------------------------------------------------------------------------
                }
                catch (\Exception $e) {
                    logger()->error('Exception: Unable to retrieve cardholder details for KYC.', ['error' => $e->getMessage()]);
                }
            }
        } //end foreach
        //------------------------------------------------------------------------
        $country_iso_3_names = $this->country_names_iso_3();
        //------------------------------------------------------------------------
        return view('front.ne_card', compact('kyc', 'kyc_payments', 'card_payments', 'cards', 'card_datas','activation_card', 'countries', 'card_balance', 'qr_code_path', 'gateway_address', 'account_balance', 'pending_card_payment', 'card_payment_qr_address', 'card_payment_qr_code_path', 'selectedCard', 'kyc2', 'country_iso_3_names'));
    }

    public function necard_transactions(Request $request) {
        $user = $request->user();
        $selectedCard = CardActivationModel::where([['id', $request->selectedCard],['user_id', $user->id]])->firstOrFail();
        if($selectedCard->card_type != 'Visa') {
            return response()->json(['error' => 'Invalid card type.'], 400);
        }
        $account_id = config('app.necard_acc_id');
        $wallet_id = config('app.necard_acc_wallet');
        if($request->month == 'prev') {
            $uri = sprintf('/api/ServiceProvider_24/ViewCardTransactions/%s/%s/%s/%s?StartDate=%s&EndDate=%s',
                            $account_id, $wallet_id, $selectedCard->card_holder_id, $selectedCard->card_id,
                            now()->subMonth()->firstOfMonth()->format('Y-m-d'),
                            now()->subMonth()->endOfMonth()->format('Y-m-d'));
                // /api/ServiceProvider_24/ViewCardTransactions/200/311/9419/9320?StartDate=2025-06-01&EndDate=2025-06-30
                //$query_arr = ['CardHolderId' => $selectedCard->card_holder_id, 'Month' => now()->subMonth()->format('m'), 'Year' => now()->subMonth()->format('Y')];
        }
        if($request->month == 'current') {
            $uri = sprintf('/api/ServiceProvider_24/ViewCardTransactions/%s/%s/%s/%s?StartDate=%s&EndDate=%s',
                            $account_id, $wallet_id, $selectedCard->card_holder_id, $selectedCard->card_id,
                            now()->firstOfMonth()->format('Y-m-d'),
                            now()->endOfMonth()->format('Y-m-d'));
                // /api/CardManagement/CardTransactionsCurrentMonth/200/311/9419/9320?StartDate=2025-06-01&EndDate=2025-06-30
                //$query_arr = ['CardHolderId' => $selectedCard->card_holder_id];
        }
        //
        try {
            logger(config('app.necard_api_url') . $uri);
            //logger(print_r([config('app.necard_user'), config('app.necard_password')], true));
            $client = new Client([
                'base_uri' => config('app.necard_api_url'),
                'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user'),
                                'ApiAuthPassword' => config('app.necard_password')
                            ],
                ]);
            $response = $client->request('GET',  $uri, [
                //'query' => $query_arr,
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
            if($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                //$onlykeys = ['processing_currency', 'description' , 'base_amount', 'base_currency', 'card_number', 'terminal_currency', 'terminal_amount', 'type_of_operation', 'card_id', 'transaction_state', 'time'];
                $data2 = '';
                foreach($data as $key => $transaction) {
                    //$data2[$key][] = array_filter($transaction, function($value) use ($onlykeys) {
                    //    return in_array($value, $onlykeys);
                    //}, ARRAY_FILTER_USE_KEY);
                    //$transaction['base_amount'], $transaction['base_currency'], $transaction['card_number'], processing_currency
                    //$transaction['card_id']
                    $data2 .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>%s',
                                    $transaction['dateCreated'], $transaction['description'],
                                    $transaction['merchantCurrency'], $transaction['merchantAmount'],
                                    $transaction['transType'], isset($transaction['dateSettled']) ? 'Settled' : '', PHP_EOL);
                }
                return response()->json($data2);
            }
        } catch (\Exception $e) {
            logger()->error('Unable to retrieve transaction details.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to retrieve transaction details.', 'details' => $e->getMessage()], 400);
        }
    }

    public function necard_pin_change(Request $request) {
        $user = $request->user();
        $request->validate([
            'card_id' => 'required|integer',
            'new_pin' => 'required|digits:4',
            'confirm_pin' => 'required|digits:4|same:new_pin',
        ]);
        $card_id = intval($request->card_id);
        if(!$card_id) {
            abort(401, 'Card not found.');
        }
        $selectedCard = CardActivationModel::where('user_id', $user->id)->where('id', $card_id)->where('status', 'Approved')->first();
        if(!$selectedCard || $selectedCard->card_type != 'Visa') {
            abort(401, 'Card number not found.');
        }
        //logger()->info('Card: ['.$card_id.'] ['.$card_no.']');
        //$uri = '/api/CardManagement/SetPin/%s/%s?Pin=%s'; //api/CardManagement/SetPin/{AccountId}/{CardId}
        $account_id = config('app.necard_acc_id');
        $wallet_id = config('app.necard_acc_wallet');
        $uri =  sprintf('/api/ServiceProvider_24/SetPIN/%s/%s/%s?pin=%s', $account_id, $wallet_id, $selectedCard->card_id, $request->new_pin);
        try {
            $client = new Client([
                'base_uri' => config('app.necard_api_url'),
                'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user'),
                                'ApiAuthPassword' => config('app.necard_password')
                            ],
                ]);
            //$uri = sprintf($uri, $account_id, $user->card_id, $request->new_pin);
            $response = $client->request('PUT', $uri, [
                //'query' => ['CardId' => $user->card_holder_id, 'NewPin' => $request->new_pin],
                //'form' => [],
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
            if($response->getStatusCode() == 200) {
                //logger($response->getBody()->getContents());
                //$data = json_decode($response->getBody()->getContents(), true);
                $data = 'Pin Changed Successfully.';
                return response()->json($data);
            }
        } catch (\Exception $e) {
            logger()->error('Unable to change pin. User: ['.$user->id.']', ['error' => $e->getMessage()]);
            $msg = '';
            preg_match('/{.*}/isU', $e->getMessage(), $msg);
            return response()->json(['error' => 'Unable to change pin.', 'message' => $msg[0]??$msg], 400);
        }
    }

    public function necard_deactivate(Request $request)
    {
        return $this->necard_toggle_activate($request, 0);
    }

    public function necard_reactivate(Request $request)
    {
        return $this->necard_toggle_activate($request, 1);
    }

    private function necard_toggle_activate(Request $request, $action)
    {
        $user = $request->user();
        if($user->card_id == null) {
            return back()->with('error', 'Card not found.');
        }
        //
        $action = intval($action) >= 1 ? 1 : 0;
        //
        $card_no = preg_replace('/\s+/', '', $request->card_no);
        $card = CardActivationModel::where('user_id', $user->id)->where('id', $card_no)->where('status', ($action ? 'Deactivated' : 'Approved'))->first();
        if(!$card) {
            return back()->with('error', 'Card number not found.');
        }
        //
        $card_holder_id = $user->card_holder_id;
        if(!$card_holder_id) {
            return back()->with('error', 'Card not found.');
        }
        //
        $card_id = $user->card_id;
        if(!$card_id) {
            return back()->with('error', 'Card not found.');
        }
        //logger()->info('Card: ['.$card_id.'] ['.$card_no.']');
        //
        $uri = 'api/CardManagement/ChangeCardStatus/%s/%s/%s?CardAction='.$action; //api/CardManagement/ChangeCardStatus/{AccountId}/{CardHolderId}/{CardId}
        $account_id = config('app.necard_acc_id');
        try {
            $client = new Client([
                'base_uri' => config('app.necard_api_url'),
                'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user'),
                                'ApiAuthPassword' => config('app.necard_password')
                            ],
                ]);
            $uri = sprintf($uri, $account_id, $card_holder_id, $user->card_id);
            //logger($uri);
            $response = $client->request('POST', $uri, [
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
            if($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                $card->status = ($action == 1 ? 'Approved' : 'Deactivated');
                $card->save();
                return back()->with('message', 'Card has been '.($action == 1 ? 'activated' : 'deactivated').'.');
            }
        } catch (\Exception $e) {
            logger()->error('Unable to change card status. User: ['.$user->id.']', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to change card status, please contact support.');
            //preg_match('/{.*}/isU', $e->getMessage(), &$msg);
            //return response()->json(['error' => 'Unable to change card status, please contact support.', 'message' => $msg[0]??''], 400);
        }
    }

    public function necard_purchase($type, Request $request)
    {
        $user = $request->user();
        if(strtolower(trim($type)) == 'visa') {
            return back()->with('error', 'Visa card purchase is currently unavailable.');
            //return response()->json(['success' => false, 'message' => 'Visa card purchase is currently unavailable.']);
        }
        try {
            $gw_purchase_address = $this->remote_get_usdt_purchase_address($user->id, ['user_id' => $user->id]);
            if(empty($gw_purchase_address) || trim($gw_purchase_address)=='')
            {
                logger()->error('Remote usdt gateway address empty.');
                return back()->with('error', 'System error. Please contact support.');
                //return response()->json(['success' => false, 'message' => 'System error. Please contact support.']);
            }
            $qr_code_path = 'qr_codes/'.md5('B~'.strval($user->id)).'.svg';
            QrCode::format('svg')->size(300)->generate($gw_purchase_address, storage_path('app/'.$qr_code_path));
            Payment::create([
                'user_id' => $user->id,
                'type' => 'card',
                'file' => '',
                'tx_id' => '',
                'status' => 'Pending',
                'card_type' => $type,
                'trans_address' => $gw_purchase_address,
            ]);
            return back()->with('message', 'New Card Purchase transaction initiated.');
        } catch (\Throwable $e) {
            //logger()->error('Exception: Unable to retrieve purchase gateway address: ['.$e->getMessage().']');
            logger()->error('Unable to purchase card. User: ['.$user->id.']', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to initiate purchase card, please contact support.');
        }
    }

    public function necard_transactions_mc(Request $request)
    {
        $user = $request->user();
        $uri = '/api/ServiceProvider_37/ViewCardTransactions/%s/%s/%s/%s';
        $selectedCard = CardActivationModel::where([['id', $request->selectedCard],['user_id', $user->id]])->firstOrFail();
        if($selectedCard->card_type != 'Mastercard') {
            return response()->json(['error' => 'Invalid card type.'], 400);
        }
        if($request->month == 'prev') {
            $query_arr = ['StartDate' => now()->subMonth()->firstOfMonth()->format('Y-m-d'), 'EndDate' => now()->subMonth()->lastOfMonth()->format('Y-m-d')];
        }
        if($request->month == 'current') {
            $query_arr = ['StartDate' => now()->firstOfMonth()->format('Y-m-d'), 'EndDate' => now()->lastOfMonth()->format('Y-m-d')];
        }
        $account_id = config('app.necard_acc_id_mc');
        $wallet_id = config('app.necard_acc_wallet_mc');
        try {
            //logger(print_r([config('app.necard_user'), config('app.necard_password')], true));
            $client = new Client([
                'base_uri' => config('app.necard_api_url_mc'),
                'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user_mc'),
                                'ApiAuthPassword' => config('app.necard_password_mc')
                            ],
                ]);
            $uri = sprintf($uri, $account_id, $wallet_id, $selectedCard->card_holder_id, $selectedCard->card_id);
            //logger($uri);
            //logger($query_arr);
            $response = $client->request('GET', $uri, [
                'query' => $query_arr,
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
            if($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                $data2 = '';
                foreach($data as $key => $transaction) {
                    $data2 .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>%s',
                                    $transaction['dateCreated'], $transaction['description'],
                                    $transaction['merchantCurrency'], $transaction['merchantAmount'],
                                    $transaction['transType'], isset($transaction['dateSettled']) ? 'Settled' : '', PHP_EOL);
                }
                return response()->json($data2);
            }
        } catch (\Exception $e) {
            logger()->error('Unable to retrieve transaction details.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to retrieve transaction details.', 'details' => $e->getMessage()], 400);
        }
    }

    public function necard_pin_change_mc(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'card_id' => 'required|integer',
            'old_pin' => 'required|digits:4',
            'new_pin' => 'required|digits:4',
            'confirm_pin' => 'required|digits:4|same:new_pin',
        ]);
        $card_id = intval($request->card_id);
        if(!$card_id) {
            abort(401, 'Card not found.');
        }
        $card = CardActivationModel::where('user_id', $user->id)->where('id', $card_id)->where('status', 'Approved')->first();
        if(!$card) {
            abort(401, 'Card number not found.');
        }
        //logger()->info('Card: ['.$card_id.'] ['.$card_no.']');
        $uri = '/api/ServiceProvider_37/ChangeCardPIN/%s/%s/%s'; //api/ServiceProvider_37/ChangeCardPIN/{AccountId}/{WalletId}/{CardId}
        $account_id = config('app.necard_acc_id_mc');
        $wallet_id = config('app.necard_acc_wallet_mc');
        try {
            $client = new Client([
                'base_uri' => config('app.necard_api_url_mc'),
                'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user_mc'),
                                'ApiAuthPassword' => config('app.necard_password_mc')
                            ],
                ]);
            $uri = sprintf($uri, $account_id, $wallet_id, $card->card_id, $request->new_pin);
            $body = ['oldPIN' => $request->old_pin, 'newPIN' => $request->new_pin];
            $response = $client->request('PUT', $uri, [
                'json' => $body,
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
            if($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                return response()->json($data);
            }
        } catch (\GuzzleHttp\Exception\ClientException | \GuzzleHttp\Exception\ServerException $e) {
            //{"errorCode":1,"errorMessage":["11 Invalid current PIN"]}
            $response = json_decode($e->getResponse()->getBody()->getContents());
            logger()->error('Unable to change pin. User: ['.$user->id.']', ['error' => $response]);
            return response()->json(['error' => 'Unable to change pin.', 'message' => $response->errorMessage??''], 400);
        } catch (\GuzzleHttp\Exception\ConnectException | \GuzzleHttp\Exception\RequestException $e) {
            logger()->error('Unable to change pin. User: ['.$user->id.']', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to change pin.', 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            logger()->error('Unable to change pin. User: ['.$user->id.']', ['error' => $e->getMessage()]);
            $msg = '';
            preg_match('/{.*}/isU', $e->getMessage(), $msg);
            return response()->json(['error' => 'Unable to change pin.', 'message' => $msg[0]??$msg], 400);
        }
    }

    public function necard_deactivate_mc(Request $request)
    {
        return $this->necard_toggle_activate_mc($request, 0);
    }

    public function necard_reactivate_mc(Request $request)
    {
        return $this->necard_toggle_activate_mc($request, 1);
    }

    private function necard_toggle_activate_mc(Request $request, $action)
    {
        $user = $request->user();
        $selectedCard = CardActivationModel::where([['id', $request->card_no],['user_id', $user->id]])->first();
        if(!$selectedCard || empty($selectedCard->id)) {
            return back()->with('error', 'Card not found.');
        }
        //newStatus: Pending = 0, Active = 1, OnHold = 2, Closed = 3
        $action = intval($action) === 0 ? 2 : 1;
        //
        $card_holder_id = $selectedCard->card_holder_id;
        if(!$card_holder_id) {
            return back()->with('error', 'Card holder not found.');
        }
        //
        $card_id = $selectedCard->card_id;
        if(!$card_id) {
            return back()->with('error', 'Card not found.');
        }
        //logger()->info('Card: ['.$card_id.'] ['.$card_no.']');
        // //newStatus: Pending = 0, Active = 1, OnHold = 2, Closed = 3
        $uri = '/api/ServiceProvider_37/ChangeCardStatus/%s/%s/%s'; ///api/ServiceProvider_37/ChangeCardStatus/200/1468/110010?newStatus=2
        $account_id = config('app.necard_acc_id_mc');
        $wallet_id = config('app.necard_acc_wallet_mc');
        $query_arr = ['newStatus' => $action];
        try {
            $client = new Client([
                'base_uri' => config('app.necard_api_url_mc'),
                'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user_mc'),
                                'ApiAuthPassword' => config('app.necard_password_mc')
                            ],
                ]);
            $uri = sprintf($uri, $account_id, $wallet_id, $selectedCard->card_id);
            //logger($uri);
            //logger($query_arr);
            $response = $client->request('PUT', $uri, [
                'query' => $query_arr,
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
            if($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                $selectedCard->status = ($action == 1 ? 'Approved' : 'Deactivated');
                $selectedCard->save();
                return back()->with('message', 'Card has been '.($action == 1 ? 'activated' : 'deactivated').'.');
            }
        } catch (\Exception $e) {
            logger()->error('Unable to change card status. User: ['.$user->id.']', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to change card status, please contact support.');
            //$msg = '';
            //preg_match('/{.*}/isU', $e->getMessage(), $msg);
            //return response()->json(['error' => 'Unable to change card status, please contact support.', 'message' => $msg[0]??$msg], 400);
        }
    }

}
