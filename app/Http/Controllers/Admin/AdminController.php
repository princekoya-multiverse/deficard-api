<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CardActivationExport;
use App\Exports\CardLoadExport;
use App\Exports\KycPaymentExport;
use App\Exports\KycVerificationExport;
use App\Exports\PaymentExport;
use App\Exports\UsersExport;
use App\Models\User;
use App\Models\Payment;
use App\Models\CardActivation;
use App\Models\KycVerification;
use App\Http\Controllers\Controller;
//use App\Mail\KycApplication;
use App\Mail\SupportTicketEmail;
//use App\Mail\CardActivation as MailCardActivation;
//use App\Mail\CardApplication;
//use App\Mail\CardLoadEmail;
use App\Models\SupportTicket;
use GuzzleHttp\Client;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        $users = User::count();
        $kyc_applications = KycVerification::count();
        $card_applications = Payment::where('type', 'card')->count();
        $card_activations = CardActivation::count();
        $card_loads = Payment::where('type', 'load')->count();
        $support_tickets = SupportTicket::count();
        return view('admin.dashboard', compact('users', 'kyc_applications', 'card_applications', 'card_activations', 'card_loads', 'support_tickets'));
    }
    public function admin_kyc(Request $request)
    {
        $search = $request->get('search');
        $kyc = KycVerification::orderBy('id', 'DESC');
        if ($search) {
            $kyc = $kyc->where(function ($query) use ($search) {
                $query->orWhere('id', 'LIKE', "%{$search}%")
                    ->orWhere('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
                    //->orWhere('birthday', 'LIKE', "%{$search}%")
                    //->orWhere('phone', 'LIKE', "%{$search}%")
                    //->orWhere('city', 'LIKE', "%{$search}%")
                    //->orWhere('street_address', 'LIKE', "%{$search}%")
                    //->orWhere('street_address_2', 'LIKE', "%{$search}%")
                    //->orWhere('region_state_province', 'LIKE', "%{$search}%")
                    //->orWhere('zipcode', 'LIKE', "%{$search}%")
                    //->orWhere('country', 'LIKE', "%{$search}%")
                    //->orWhere('file1', 'LIKE', "%{$search}%")
                    //->orWhere('file2', 'LIKE', "%{$search}%")
                    //->orWhere('user_id', 'LIKE', "%{$search}%")
                    //->orWhere('created_at', 'LIKE', "%{$search}%")
                    //->orWhere('updated_at', 'LIKE', "%{$search}%");
            });
        }

        if ($request->status) {
            $kyc = $kyc->where('status', $request->status);
        }

        $kyc = $kyc->paginate(10);
        $status = KycVerification::get()->pluck('status')->unique()->values()->toArray();
        return view('admin.kyc', compact('kyc', 'status'));
    }
    public function users(Request $request)
    {
        $search = $request->get('search');
        $status = $request->status;
        $progress_status = $request->progress_status;
        $per_page = $request->get('per_page', 10);
        $orderBy = ['id', 'DESC'];
        $users = User::leftJoin('kyc_verifications', 'users.id', '=', 'kyc_verifications.user_id')
                    ->select('users.*', 'kyc_verifications.status as kyc_status')
                    ->where('is_admin', false);
        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('users.first_name', 'like', "%{$search}%")
                    ->orWhere('users.last_name', 'like', "%{$search}%")
                    //->orWhere('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
            $orderBy = ['first_name', 'ASC'];
        }
        // Check if status filter is provided
        if (!empty($status)) {
            if($status == 'None') {
                $users->whereNull('kyc_verifications.status');
            } else {
                $users->where('kyc_verifications.status', $status);
            }
            $orderBy = ['id', 'ASC'];
        }
        if($progress_status) {
            $users->leftJoin('user_progress', 'users.id', '=', 'user_progress.user_id')
                ->where('user_progress.progress_status', $progress_status);
            $orderBy = ['id', 'ASC'];
        }
        //dump($users->toRawSQL());
        $users = $users->orderBy(...$orderBy)->paginate($per_page);
        $status = ['None', 'Approved', 'In Process', 'Retry', 'Pending', 'Rejected'];
        //$progressStatuses = ['KYC Started', 'KYC Rejected', 'KYC Approved', 'KYC Retried', 'Card Issued', 'Card Mailed', 'Card Returned', 'Card Lost'];
        $progressStatuses = ['Card Issued', 'Card Mailed', 'Card Returned', 'Card Lost'];
        return view('admin.users', compact('users','status', 'progressStatuses'));
    }

    public function user_profile(User $id, Request $request)
    {
        $user = $id;
        $kyc = KycVerification::where('user_id', $user->id)->first();
        $cards = Payment::where('user_id', $id->id)->where('type', 'card')->orderBy('id', 'desc')->get();
        $card_activations = CardActivation::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        $card_loads = Payment::where('user_id', $id->id)->whereIn('type', ['load','USDT'])->orderBy('id', 'desc')->get();
        $user_progress = \App\Models\UserProgress::where('user_id', $id->id)->orderBy('id', 'desc')->get();
        //$support_tickets = SupportTicket::where('user_id', $id->id)->orderBy('id', 'desc')->get();
        $country_iso_3_names = $this->country_names_iso_3();
        return view('admin.user_profile', compact('user','kyc','card_activations','cards','user_progress','card_loads', 'country_iso_3_names'));
    }

    public function update_payment_card_holder_id(Request $request)
    {
        $id = intval($request->id);
        $chid = intval($request->chid);
        try {
            if($chid >= 0) {
                Payment::where('id', $id)->update(['card_holder_id' => $chid]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        return response()->json(['status' => 'success', 'message', 'Card Holder ID updated']);
    }

    public function update_card_ids(Request $request)
    {
        $id = intval($request->id);
        $cid = intval($request->cid);
        $chid = intval($request->chid);
        try {
            if($cid > 0) {
                CardActivation::where('id', $id)->update(['card_id' => $cid]);
            }
            if($chid > 0) {
                CardActivation::where('id', $id)->update(['card_holder_id' => $chid]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        return response()->json(['status' => 'success', 'message', 'ID updated']);
    }

    public function update_user_email(User $id, Request $request)
    {
        $valid = validator()->make($request->all(), [
            'new_email' => 'required|email|unique:users,email',
        ]);
        if($valid->errors() && !empty($valid->errors()->first())) {
            return back()->with('error', $valid->errors()->first());
        }
        $id->email = $request->new_email;
        $id->save();
        //$args = ['per_page' => $request->per_page, 'page' => $request->page, 'search' => $request->search];
        $args = ['id' => $id->id];
        return redirect()->route('admin.user.profile', $args)->with('message', 'User email was updated successfully.');
    }

    public function admin_kyc_status($id, $status)
    {
        $kyc = KycVerification::with('user')->find($id);
        if ($kyc->status == $status) {
            //return redirect()->route('admin.kyc')->with('error', 'Status is already ' . $status);
            return back()->with('error', 'Status is already ' . $status);
        }
        $kyc->status = $status;
        $kyc->save();
        //Mail::to($kyc->user->email)->send(new KycApplication($kyc));
        //return redirect()->route('admin.kyc')->with('message', 'Status is updated');
        return back()->with('message', 'Status is updated');
    }

    public function update_kyc_message(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|string|max:255',
        ]);
        $kyc = KycVerification::find($request->id);
        if($kyc) {
            $kyc->status_message = trim($request->message);
            $kyc->save();
            return response()->json(['message', 'KYC message updated successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'KYC record not found.'], 404);
    }

    public function email_kyc_message(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|string|max:255',
        ]);
        $kyc = KycVerification::find($request->id);
        if($kyc) {
            $kyc->status_message = trim($request->message);
            $kyc->save();
            Mail::to($kyc->user->email)->send(new \App\Mail\KycMessageEmail($kyc));
            return response()->json(['message', 'KYC message updated successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'KYC record not found.'], 404);
    }


    public function admin_card(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $perPage = $request->get('per_page', 10);

        $cards = Payment::where('type', 'card')
            ->where(function ($qq) use ($search) {
                // Check if search term is provided
                if (!empty($search)) {
                    $qq->where('tx_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where(function ($query) use ($search) {
                                $query->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                        });
                }
            })
            ->orderBy('id', 'desc')
            ->with('user');

        // Check if status filter is provided
        if (!empty($status)) {
            $cards->where('status', $status);
        }

        // Paginate the results
        $cards = $cards->paginate($perPage);

        $status = ['Approved', 'In Process', 'Rejected', 'Pending']; //Payment::where('type', 'card')->get()->pluck('status')->unique()->values()->toArray();
        return view('admin.card', compact('cards', 'status'));
    }

    public function admin_card_update_progress($user_id, Request $request)
    {
        $progress = trim($request->status);
        //$payment = Payment::where('user_id', intval($user_id))->findOrFail();
        //$payment->card_progress = $progress;
        //$payment->save();
        \App\Models\UserProgress::create([
            'user_id' => intval($user_id),
            'progress_status' => $request->progress_status,
            'details' => $request->progress_details,
        ]);
        if($request->notify_user) {
            $user = User::where('id', intval($user_id))->first();
            $data = [
                'name' => $user->first_name . ' ' . $user->last_name,
                'status' => trim($request->progress_status),
                'details' => $request->progress_details,
            ];
            Mail::to($user->email)->send(new \App\Mail\ProgressEmail($data));
        }
        return back()->with('message', 'Progress status is updated.');
    }

    public function admin_card_update($id, $status)
    {
        $payment = Payment::with('user')->find($id);
        if ($payment->status == $status) {
            //return redirect()->route('admin.card')->with('error', 'Status is already ' . $status);
            return back()->with('error', 'Status is already ' . $status);
        }
        if($status == 'Approved') {
            if($payment->trans_amount <= floatval(config('app.usdt-gateway-card-onetime-fee', 199) - 10)) {
                return back()->with('error', 'There is no payment approved for card application.');
            }
        }
        $payment->status = $status;
        $payment->save();
        //Mail::to($payment->user->email)->send(new CardApplication($payment));

        //return redirect()->route('admin.card')->with('message', 'Status is updated');
        return back()->with('message', 'Status is updated');
    }

    public function admin_card_activation(Request $request)
    {
        $card_activations = CardActivation::orderBy('id', 'desc')->with('user')->whereHas('user', function ($q) use ($request) {
            $search = $request->search;
            if ($request->search) {
                $q->where('number', 'like', "%{$search}%");
                $q->orWhere('kit_number', 'like', "%{$search}%");
                $q->orWhere(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        // ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }
        });
        if ($request->status) {
            $card_activations = $card_activations->where('status', $request->status);
        }
        $card_activations = $card_activations->paginate($request->get('per_page', 10));

        $status = CardActivation::get()->pluck('status')->unique()->values()->toArray();


        return view('admin.card_activation', compact('card_activations', 'status'));
    }

    public function admin_card_activation_update($id, $status)
    {
        $payment = CardActivation::with('user')->find($id);
        if ($payment->status == $status) {
            //return redirect()->route('admin.card.activation')->with('error', 'Status is already ' . $status);
            return back()->with('error', 'Status is already ' . $status);
        }
        $payment->status = $status;
        $payment->save();
        //Mail::to($payment->user->email)->send(new MailCardActivation($payment));

        //return redirect()->route('admin.card.activation')->with('message', 'Status is updated');
        return back()->with('message', 'Status is updated');
    }

    public function admin_card_load(Request $request)
    {
        $card_loads = Payment::whereIn('type', ['load','USDT'])->orderBy('id', 'desc')->with('user')->with('card')->whereHas('user', function ($q) use ($request) {
            $search = $request->search;

            if ($request->search) {
                $q->where('tx_id', 'like', "%{$search}%");
                $q->orWhere(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }
        });

        if ($request->status) {
            $card_loads = $card_loads->where('status', $request->status);
        }

        $card_loads  = $card_loads->paginate($request->get('per_page', 10));
        $status = Payment::whereIn('type', ['USDT','load'])->get()->pluck('status')->unique()->values()->toArray();
        //dd($card_loads);
        return view('admin.admin_card_load', compact('card_loads', 'status'));
    }

    public function admin_card_load_done(Request $request)
    {
        try {
            $transaction = Payment::where('id', $request->id)->firstOrFail();
            throw_if(intval($transaction->trans_loaded) == 1, \Exception::class, 'This Card load payment is alredy done.');
            throw_if(intval($transaction->card_id) <= 0, \Exception::class, 'User had not select any card to load.');
            throw_if(trim($transaction->status) != 'Approved', \Exception::class, 'This transaction is not approved yet.');
            throw_if((empty($transaction->card) || intval($transaction->card?->card_holder_id) <= 0 || intval($transaction->card?->card_id) <= 0), \Exception::class, 'This Card does not have valid Card ID Or Card Holder ID.');

            if($transaction->type != 'load' && $transaction->type != 'USDT') {
                throw new \Exception('This is not a card load transaction.');
            }
            //$selected_card = CardActivation::where('user_id', $transaction->user_id)->where('status', 'Approved')->firstOrFail();
            //$active_card_id = $selected_card->id;
            //----------------------------------------------------------------------------------------
            if(!isset($request->manual) || intval($request->manual) <= 0)
            {
                //call wallet load api
                //logger($transaction->toArray());
                //dump($transaction->toArray());
                //dd('Wallet API Card Load');
                if($transaction->card->card_type == 'Mastercard') {
                    $uri = '/api/ServiceProvider_37/LoadCard/%s/%s/%s/%s?AmountToLoad=%d&Description=%s';
                    $account_id = config('app.necard_acc_id_mc');
                    $wallet_id = config('app.necard_acc_wallet_mc');
                    $base_uri = config('app.necard_api_url_mc');
                    $api_user_name = config('app.necard_user_mc');
                    $api_password = config('app.necard_password_mc');
                }
                if($transaction->card->card_type == 'Visa') {
                    $uri = '/api/ServiceProvider_24/LoadCard/%s/%s/%s/%s?AmountToLoad=%d&Description=%s';
                    $account_id = config('app.necard_acc_id');
                    $wallet_id = config('app.necard_acc_wallet');
                    $base_uri = config('app.necard_api_url');
                    $api_user_name = config('app.necard_user');
                    $api_password = config('app.necard_password');
                }
                $client = new Client([
                    'base_uri' => $base_uri,
                    'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'ApiAuthUsername' => $api_user_name,
                            'ApiAuthPassword' => $api_password,
                        ],
                    ]);
                //$wallet_id = 40;
                $amount = intval((floatval($transaction->trans_amount) - floatval($transaction->trans_fee)) * 100); // Convert to cents,
                $uri = sprintf($uri, $account_id, $wallet_id, $transaction->card->card_holder_id, $transaction->card->card_id, $amount, 'Load Transaction '.$transaction->id);
                //dd($uri);
                try {
                    logger()->channel('api')->debug('Wallet API Card Load Call: ' . $uri);
                    $response = $client->request('POST', $uri, [
                        'headers' => [
                            'Accept' => 'application/json',
                        ]
                    ]);
                    if($response->getStatusCode() == 200) {
                        $result = $response->getBody()->getContents();
                        $data = json_decode($result);
                        logger()->channel('api')->debug('Success Response: '.print_r($data,true));
                        $transaction->api_trans_id = intval($data->transId);
                        $transaction->api_status = 200;
                        $transaction->api_response = $result;
                        if(intval($data->errorCode) === 0 && intval($data->transStatus) === 1) {
                            //No need to launch a backend job to verify the transfer api response
                            Payment::create([
                                'user_id' => $transaction->user_id,
                                'amount' => $transaction->amount,
                                'card_id' => $transaction->card_id,
                                'card_type' => $transaction->card->card_type,
                                'trans_id' => $transaction->id,
                                'trans_amount' => 0 - (floatval($transaction->trans_amount) - floatval($transaction->trans_fee)),
                                'trans_status' => 'confirmed',
                                'trans_loaded' => 1,
                                'type' => 'USDT',
                                'tx_id' => 'Card Load',
                                'status' => 'Approved',
                                'file' => '',
                            ]);
                            //set transaction is loaded done
                            $transaction->trans_loaded = 1;
                            $transaction->save();
                        } else {
                            throw new \Exception('API returned an error: ' . (is_array($data->errorMessage) ? $data->errorMessage[0] : $data->errorMessage));
                        }
                        $vars = ['per_page' => $request->per_page, 'status' => $request->status, 'search' => $request->search];
                        if($request->user_id) {
                            return redirect()->route('admin.user.profile', ['id' => $request->user_id])->with('message', 'Card Load Done!');
                        } else {
                            return redirect()->route('admin.card.load', $vars)->with('message', 'Card Load Done!');
                        }
                        //---------------------------------------------------------------------------
                    }
                    if($response->getStatusCode() == 400) {
                        $result = $response->getBody()->getContents();
                        $data = json_decode($result);
                        logger()->channel('api')->debug('Error Response: '.print_r($data,true));
                        $transaction->api_trans_id = intval($data->transId);
                        $transaction->api_status = 400;
                        $transaction->api_response = $result;
                        $transaction->save();
                        //launch a backend job to verify the transfer api response
                        \App\Jobs\CheckLoadTransStatus::dispatch($transaction->id)->delay(now()->addMinutes(2));
                    }
                } catch(\GuzzleHttp\Exception\RequestException $ex) {
                    $code = $response = $result = $data = null;
                    if($ex->hasResponse()) {
                        $code = $ex->getCode();
                        $response = $ex->getResponse();
                        $result = $response->getBody()->getContents();
                        $data = json_decode($result);
                        logger()->channel('api')->debug('Request 400 Response: '.print_r($data,true));
                    } else {
                        logger()->channel('api')->debug('Request Exception Response: ' . $ex->getMessage());
                    }
                    //logger()->channel('api')->debug([$code, '~~~~~',$response,'~~~~', $result,'~~~~~~', $data]);
                    // launch a backend job to verify the transfer api response
                    if(intval($code) == 400) {
                        //logger()->channel('api')->debug('Data:'. print_r($data,true));
                        //throw_if(intval($data->errorCode) === 1, throw new \Exception('API returned a 400 with error: ' .
                        //                is_array($data->errorMessage) ? $data->errorMessage[0] : $data->errorMessage)
                        //);
                        //If the API returns a 400 error, we still want to save the transaction to check later again via backend job
                        //if no transaction ID is returned, we throw an exception, its a "required" field
                        if(intval($data->transId) <= 0) { throw new \Exception('API returned a 400 error without a transaction ID.'); }
                        //
                        //launch a backend job to verify the transfer api response
                        $transaction->api_trans_id = $data->transId;
                        $transaction->api_status = 400;
                        $transaction->api_response = $result ?? '';
                        $transaction->save();
                        //launch a backend job to verify the transfer api response
                        \App\Jobs\CheckLoadTransStatus::dispatch($transaction->id)->delay(now()->addMinutes(1));
                        //
                        $vars = ['per_page' => $request->per_page, 'status' => $request->status, 'search' => $request->search];
                        if($request->user_id) {
                            return redirect()->route('admin.user.profile', ['id' => $request->user_id])->with('message', 'Card Load Done!');
                        } else {
                            return redirect()->route('admin.card.load', $vars)->with('message', 'Card Load Done!');
                        }
                    } else {
                        //every thing else is an error, we throw an exception
                        throw new \Exception('API Request Error: ' . $ex->getCode(). ' - ' . $ex->getMessage());
                    }
                } catch (\Throwable $e) {
                    logger()->channel('api')->debug('General Exception: '.$e->getMessage());
                    //every thing else is an error, we throw an exception
                    throw new \Exception('API Request Error: ' . $e->getCode(). ' - ' . $e->getMessage());
                }
            }
            //----------------------------------------------------------------------------------------
            if(isset($request->manual) || intval($request->manual) >= 1)
            {
                //call wallet load api
                //logger($transaction->toArray());
                //dump($transaction->toArray());
                //dd('Manual Card Load');
                Payment::create([
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount,
                    'card_id' => $transaction->card_id,
                    'card_type' => $transaction->card->card_type,
                    'trans_id' => $transaction->id,
                    'trans_amount' => 0 - (floatval($transaction->trans_amount) - floatval($transaction->trans_fee)),
                    'trans_status' => 'confirmed',
                    'trans_loaded' => 1,
                    'type' => 'USDT',
                    'tx_id' => 'Card Load',
                    'status' => 'Approved',
                    'file' => '',
                    'api_response' => json_encode(['Manual Card Load' => 1, 'loaded_at' => now()]),
                    'api_status' => 100, //100 is a custom status for manual card load, Continue
                ]);
                //set transaction is loaded done
                $transaction->trans_loaded = 1;
                $transaction->save();
                $vars = ['per_page' => $request->per_page, 'status' => $request->status, 'search' => $request->search];
                if($request->user_id) {
                    return redirect()->route('admin.user.profile', ['id' => $request->user_id])->with('message', 'Card Load Done!');
                } else {
                    return redirect()->route('admin.card.load', $vars)->with('message', 'Card Load Done!');
                }
            }

        } catch(\Exception $e) {
            //logger($e->getMessage());
            //exit;
            $vars = ['per_page' => $request->per_page, 'status' => $request->status, 'search' => $request->search];
            if($request->user_id) {
                return redirect()->route('admin.user.profile', ['id' => $request->user_id])->with('error', $e->getMessage());
            }
            return redirect()->route('admin.card.load', $vars)->with('error', $e->getMessage());
            //return redirect()->route('admin.card.load', $vars)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function admin_card_load_update($id, $status)
    {
        //$payment = Payment::with('user')->find($id);
        //if ($payment->status == $status) {
        //    return redirect()->route('admin.card')->with('error', 'Status is already ' . $status);
        //}
        $payment = Payment::find($id);
        $payment->status = $status;
        $payment->save();
        //Mail::to($payment->user->email)->send(new CardLoadEmail($payment));

        //return redirect()->route('admin.card.load')->with('message', 'Status is updated.');
        return back()->with('message', 'Status is updated.');
    }

    public function admin_support_ticket(Request $request)
    {
        $tickets = SupportTicket::orderBy('id', 'desc')->with('user')->whereHas('user', function ($q) use ($request) {
            if ($request->search) {
                $q->where('message', 'like', '%'.$request->search.'%');
                $search = $request->search;
                $q->orWhere(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }
        });
        if ($request->status) {
            $tickets = $tickets->where('status', $request->status);
        }
        $tickets = $tickets->paginate($request->get('per_page', 10));
        $status = SupportTicket::all()->pluck('status')->unique()->values()->toArray();
        return view('admin.admin_support_ticket', compact('tickets', 'status'));
    }

    public function admin_support_ticket_update($id, $status)
    {
        //$ticket = SupportTicket::with('user')->find($id);
        $ticket = SupportTicket::find($id);
        $ticket->status = $status;
        $ticket->save();
        //Mail::to($ticket->user->email)->send(new SupportTicketEmail($ticket));

        return redirect()->route('admin.support_ticket')->with('message', 'Status is updated');;
    }
    /**
     * Show the form for creating a new resource.
     */

    public function admin_kyc_payment(Request $request)
    {
        $kyc_payments = Payment::where('type', 'kyc')->orderBy('id', 'desc')->with('user')->whereHas('user', function ($q) use ($request) {
            if ($request->search) {
                $q->where('tx_id', $request->search);
                $search = $request->search;
                $q->orWhere(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        // ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                    //   ->orWhere('phone', 'like', "%{$search}%");
                });
            }
        });

        if ($request->status) {
            $kyc_payments = $kyc_payments->where('status', $request->status);
        }
        $kyc_payments  = $kyc_payments->paginate($request->get('per_page', 10));
        $status = Payment::where('type', 'kyc')->get()->pluck('status')->unique()->values()->toArray();

        return view('admin.kyc_payment', compact('kyc_payments', 'status'));
    }

    public function admin_kyc_payment_update($id, $status)
    {
        //$kyc_payments = Payment::with('user')->find($id);
        //if ($kyc_payments->status == $status) {
        //   return redirect()->route('admin.kyc.payment')->with('error', 'Status is already ' . $status);
        //}
        $kyc_payments = Payment::find($id);
        $kyc_payments->status = $status;
        $kyc_payments->save();
        //return redirect()->route('admin.kyc.payment')->with('message', 'Status is updated');
        return back()->with('message', 'Status is updated');
    }

    public function change_password(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        if ($request->isMethod('POST')) {
            if (Hash::check($request->old_password, auth()->user()->getAuthPassword())) {
                $request->validate([
                    'old_password' => ['required', 'string'],
                    'password' => ['required', 'string', 'min:6', 'confirmed'],
                    'password_confirmation' => ['required'],
                ]);
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect()->route('admin.password.change')->with('message', 'Password changed successfully.');
            } else {
                return back()->withErrors(['old_password' => 'Current password did not match.']);
            }
        }

        return view('admin.change_password');
    }

    public function usersExport()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function admin_kycExport()
    {
        return Excel::download(new KycVerificationExport, 'kyc.xlsx');
    }

    public function admin_cardExport()
    {
        return Excel::download(new PaymentExport, 'card.xlsx');
    }
    public function admin_card_activationExport()
    {
        return Excel::download(new CardActivationExport, 'card-activation.xlsx');
    }

    public function admin_card_loadExport()
    {
        return Excel::download(new CardLoadExport, 'card-load.xlsx');
    }

    public function admin_kyc_paymentExport()
    {
        return Excel::download(new KycPaymentExport, 'kyc-payment.xlsx');
    }
}
