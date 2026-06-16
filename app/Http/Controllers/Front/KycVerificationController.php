<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Models\KycVerification;
use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class KycVerificationController extends Controller
{
    public function kyc_verification_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            //'middle_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'nationality' => 'required',
            'place_of_birth' => 'required',
            'email' => 'required',
            'birthday' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'street_address' => 'required',
            //'street_address_2' => 'required',
            'region_state_province' => 'required',
            'zipcode' => 'required',
            'country' => 'required',
            //'file1' => 'required',
            //'file2' => 'required',
            'file3' => 'required|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt',
            'file3_lang' => 'required',
            'file3_type' => 'required',
            'file3_issued_by' => 'required',
            'file3_issued_date' => 'required',
        ], [
            //'file1.required' => 'Photo ID Front side file is required.',
            //'file2.required' => 'Photo ID Back side file is required.',
            'file3.required' => 'Govt issued document file is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.ne_card', ['step' => 5])->withInput()->with('error', 'All Fields are required')->withErrors($validator);
        }
        //----------------------------------------------------------------------
        $calling_codes = $this->country_calling_codes();
        $country_iso_2_codes = $this->country_iso_2_codes();
        //----------------------------------------------------------------------
        $card_holder_id = 0; //537429
        $card_holder_file_id = 0;
        $card_holder_data = [
            "firstName" => $request->first_name,
            "midName" => $request->middle_name ?? '',
            "lastName" => $request->last_name,
            "gender" => intval($request->gender),
            "dob" => $request->birthday,
            "adrLine1" => $request->street_address,
            "adrLine2" => $request->street_address_2 ?? '',
            "city" => $request->city,
            "state" => $request->region_state_province,
            "country" => $request->country,
            "zipCode" => $request->zipcode,
            "phoneNum" => (string) preg_replace('/[^\d]+/', '', $request->phone),
            "cellNum" => (string) preg_replace('/[^\d]+/', '', $request->phone),
            "callingCode" => $calling_codes[$request->country] ?? '',
            "countryCallingCode" => $country_iso_2_codes[$request->country] ?? '',
            "emailAdr" => $request->email,
            "nationality" => $request->nationality,
            "placeOfBirth" => $request->place_of_birth,
            "occupation" => 11, //Fixed value
            "employeeID" => "12", //Fixed value
            "cardHolderFirstname" => $request->first_name,
            "cardHolderLastName" => $request->last_name,
        ];
        //dd($card_holder_data);
        if(config('app.debug')) {
            file_put_contents('card_holder_data.json', json_encode($card_holder_data, JSON_PRETTY_PRINT));
        }
        $uri = '/api/CardHolderManagement/CardHolder_CreateNew/%s/%s';
        try {
            $account_id = config('app.necard_acc_id_mc');
            $wallet_id = config('app.necard_acc_wallet_mc');
            $client = new \GuzzleHttp\Client([
                'base_uri' => config('app.necard_api_url_mc'),
                'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user_mc'),
                                'ApiAuthPassword' => config('app.necard_password_mc')
                            ],
                ]);
            $uri = sprintf($uri, $account_id, $wallet_id);
            $response = $client->request('POST', $uri, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($card_holder_data)
            ]);
            if($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(),true);
                if(config('app.debug')) {
                    logger()->debug('Card holder create api response: ' . print_r($data, true));
                }
                $card_holder_id = intval($data);
            }
        } catch(\GuzzleHttp\Exception\ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                logger()->error('Cardholder API client exception with response: ', ['status_code' => $response->getStatusCode(), 'response_body' => $responseBodyAsString]);
                $errorMessage = json_decode($responseBodyAsString)->errorMessage[0] ?? $responseBodyAsString;
                //dd($errorMessage);
                return redirect()
                        ->route('front.ne_card', ['step' => 5])
                        ->withInput()
                        ->with('error', 'KYC Error: ' . $errorMessage);
            } else {
                logger()->error('Cardholder API client exception: ', ['error' => $e->getMessage()]);
                return redirect()
                        ->route('front.ne_card', ['step' => 5])
                        ->withInput()
                        ->with('error', 'KYC Error: ' . $e->getMessage());
            }
        } catch(\GuzzleHttp\Exception\ServerException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                logger()->error('Cardholder API server exception with response: ', ['status_code' => $response->getStatusCode(), 'response_body' => $responseBodyAsString]);
                $errorMessage = json_decode($responseBodyAsString)->errorMessage[0] ?? $responseBodyAsString;
                return redirect()
                        ->route('front.ne_card', ['step' => 5])
                        ->withInput()
                        ->with('error', 'KYC Error: ' . $errorMessage);
            } else {
                logger()->error('Cardholder API server exception: ', ['error' => $e->getMessage()]);
                return redirect()
                        ->route('front.ne_card', ['step' => 5])
                        ->withInput()
                        ->with('error', 'KYC Error: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            logger()->error('Cardholder exception:', ['error' => $e->getMessage()]);
            return redirect()
                    ->route('front.ne_card', ['step' => 5])
                    ->withInput()
                    ->with('error', 'KYC Error: ' . $e->getMessage());
        }
        //----------------------------------------------------------------------
        if($card_holder_id > 0) {
            $file3 = $request->file3;
            $file_ext = strtolower(trim($file3->getClientOriginalExtension()));
            if($file_ext == 'jpg' || $file_ext == 'jpeg') {
                $file_content = file_get_contents($file3->getRealPath());
                $base64_image_front = base64_encode($file_content);
                $image_front_ext = 1;
            } elseif($file_ext == 'png') {
                $file_content = file_get_contents($file3->getRealPath());
                $base64_image_front = base64_encode($file_content);
                $image_front_ext = 3;
            } elseif($file_ext == 'pdf') {
                $file_content = file_get_contents($file3->getRealPath());
                $base64_image_front = base64_encode($file_content);
                $image_front_ext = 2;
            } else {
                return redirect()
                        ->route('front.ne_card', ['step' => 5])
                        ->withInput()
                        ->with('error', 'KYC Error: Unsupported file type for document upload. Only JPG, PNG, and PDF are allowed.');
            }
            $uri = '/api/CardHolderManagement/CardHolder_KycDoc_Add/%s/%s';
            $card_holder_kyc_doc_data = [
                "docType" => $request->file3_type,
                "language" => $request->file3_lang,
                "docNumber" => '',
                "issuedBy" => $request->file3_issued_by,
                "issueDate" => $request->file3_issued_date,
                "expireDate" => '',
                "imageFront" => $base64_image_front,
                "imageFrontExt" => $image_front_ext,
                "imageBack" => '',
                "imageBackExt" => 0
            ];
            $json_data = json_encode($card_holder_kyc_doc_data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
            if(config('app.debug')) {
                file_put_contents('file3_base64.json', $json_data);
            }
            try {
                $account_id = config('app.necard_acc_id_mc');
                $client = new \GuzzleHttp\Client([
                    'base_uri' => config('app.necard_api_url_mc'),
                    'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'text/plain',
                                'ApiAuthUsername' => config('app.necard_user_mc'),
                                'ApiAuthPassword' => config('app.necard_password_mc')
                            ],
                    ]);
                $uri = sprintf($uri, $account_id, $card_holder_id);
                $response = $client->request('POST', $uri, [
                    'body' => $json_data,
                ]);
                if($response->getStatusCode() == 200) {
                    $data = json_decode($response->getBody()->getContents(), true);
                    if(config('app.debug')) {
                        logger()->debug('KYC file upload response: ' . print_r($data, true));
                    }
                    $card_holder_file_id = intval($data);
                }
            } catch(\GuzzleHttp\Exception\ClientException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    logger()->error('KYC file client exception with response: ', ['status_code' => $response->getStatusCode(), 'response_body' => $responseBodyAsString]);
                    $errorMessage = json_decode($responseBodyAsString)->errorMessage[0] ?? $responseBodyAsString;
                    return redirect()
                            ->route('front.ne_card', ['step' => 5])
                            ->withInput()
                            ->with('error', 'KYC Error: ' . $errorMessage);
                } else {
                    logger()->error('KYC file client exception: ', ['error' => $e->getMessage()]);
                    return redirect()
                            ->route('front.ne_card', ['step' => 5])
                            ->withInput()
                            ->with('error', 'KYC Error: ' . $e->getMessage());
                }
            } catch(\GuzzleHttp\Exception\ServerException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $responseBodyAsString = $response->getBody()->getContents();
                    logger()->error('KYC file server exception with response: ', ['status_code' => $response->getStatusCode(), 'response_body' => $responseBodyAsString]);
                    $errorMessage = json_decode($responseBodyAsString)->errorMessage[0] ?? $responseBodyAsString;
                    return redirect()
                            ->route('front.ne_card', ['step' => 5])
                            ->withInput()
                            ->with('error', 'KYC Error: ' . $errorMessage);
                } else {
                    logger()->error('KYC file server exception: ', ['error' => $e->getMessage()]);
                    return redirect()
                            ->route('front.ne_card', ['step' => 5])
                            ->withInput()
                            ->with('error', 'KYC Error: ' . $e->getMessage());
                }
            } catch (\Exception $e) {
                logger()->error('Cardholder exception:', ['error' => $e->getMessage()]);
                return redirect()
                        ->route('front.ne_card', ['step' => 5])
                        ->withInput()
                        ->with('error', 'KYC Error: ' . $e->getMessage());
            }
        }
        //----------------------------------------------------------------------
        //Update all approved card payments with card holder id if there is no prior card holder id
        Payment::where([['user_id', auth()->user()->id], ['type', 'card'], ['status', 'Approved']])
                ->where(fn($qry) => $qry->whereNull('card_holder_id')->orWhere('card_holder_id', 0))
                ->update(['card_holder_id' => $card_holder_id]);
        //----------------------------------------------------------------------
        //get the KYC Review URL via api
        $kyc_verify_url = '';
        $account_id = config('app.necard_acc_id_mc');
        $uri = '/api/CardHolderManagement/CardHolder_SubmitForReview_WithKycService/%s/%s';
        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => config('app.necard_api_url_mc'),
                'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'ApiAuthUsername' => config('app.necard_user_mc'),
                            'ApiAuthPassword' => config('app.necard_password_mc')
                        ],
                ]);
            $uri = sprintf($uri, $account_id, $card_holder_id);
            $response = $client->request('PUT', $uri);
            if($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(), false);
                $kyc_verify_url = $data->applicationLink;
                /*
                {
                    "errorCode": 0,
                    "errorMessage": [],
                    "cardholderID": 537429,
                    "applicationLink": "https://verify.didit.me/session/_mbnXgmBeGTh"
                }
                */
            }
        } catch(\Exception $e) {
            logger()->error('KYC Review Service API exception:', ['error' => $e->getMessage()]);
            /*
            return redirect()
                    ->route('front.ne_card', ['step' => 5])
                    ->withInput()
                    ->with('error', 'KYC Error: ' . $e->getMessage());
            */
        }
        //----------------------------------------------------------------------
        $kycverification = new KycVerification();
        $kycverification->first_name = $request->first_name;
        $kycverification->middle_name = $request->middle_name;
        $kycverification->last_name = $request->last_name;
        $kycverification->gender = $request->gender;
        $kycverification->nationality = $request->nationality;
        $kycverification->place_of_birth = $request->place_of_birth;
        $kycverification->email = $request->email;
        $kycverification->birthday = $request->birthday;
        $kycverification->phone = $request->phone;
        $kycverification->city = $request->city;
        $kycverification->street_address = $request->street_address;
        $kycverification->street_address_2 = $request->street_address_2 ?? '---';
        $kycverification->region_state_province = $request->region_state_province;
        $kycverification->zipcode = $request->zipcode;
        $kycverification->country = $request->country;
        $kycverification->user_id = auth()->user()->id;
        if($request->file1) {
            $file = $request->file1;
            $file_name = time() . "." . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/files", $file_name);
            $kycverification->file1 = $file_name;
        }
        if($request->file2) {
            $file2 = $request->file2;
            $file_name2 = time() . "." . $file2->getClientOriginalName();
            $file2->move(public_path() . "/uploads/files", $file_name2);
            $kycverification->file2 = $file_name2;
        }
        if($request->file3) {
            $file3 = $request->file3;
            $file_name3 = time() . "." . $file3->getClientOriginalName();
            $file3->move(public_path() . "/uploads/files", $file_name3);
            $kycverification->file3 = $file_name3;
            $kycverification->file3_lang = $request->file3_lang;
            $kycverification->file3_type = $request->file3_type;
            $kycverification->file3_issued_by = $request->file3_issued_by;
            $kycverification->file3_issued_date = $request->file3_issued_date;
        }
        $kycverification->card_holder_id = $card_holder_id;
        $kycverification->card_holder_file_id = $card_holder_file_id;
        $kycverification->status_message = $kyc_verify_url;
        $kycverification->save();
        return redirect()->route('front.ne_card', ['step' => 5])->with('message', 'Kyc information saved successfully');
    }


    public function kyc_verification_update(Request $request, $id)
    {
        $kycverification = KycVerification::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'nationality' => 'required',
            'place_of_birth' => 'required',
            'email' => 'required',
            'birthday' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'street_address' => 'required',
            //'street_address_2' => 'required',
            'region_state_province' => 'required',
            'zipcode' => 'required',
            'country' => 'required',
            //'file1' => [\Illuminate\Validation\Rule::requiredIf(function() use ($kycverification) { return empty($kycverification->file1); })],
            //'file2' => [\Illuminate\Validation\Rule::requiredIf(function() use ($kycverification) { return empty($kycverification->file2); })],
            'file3' => [\Illuminate\Validation\Rule::requiredIf(function() use ($kycverification) { return empty($kycverification->file3); })],
            'file3_lang' => 'required',
            'file3_type' => 'required',
            'file3_issued_by' => 'required',
            'file3_issued_date' => 'required',
        ], [
            //'file1.required' => 'Photo ID Front side file is required.',
            //'file2.required' => 'Photo ID Back side file is required.',
            //'file3.required' => 'Govt issued utility bill file is required.',
            'file3.required' => 'Govt issued document file is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.ne_card', ['step' => 5])->withInput()->with('error', 'All Fields are required')->withErrors($validator);
        }

        $kycverification->first_name = $request->first_name;
        $kycverification->middle_name = $request->middle_name;
        $kycverification->last_name = $request->last_name;
        $kycverification->gender = $request->gender;
        $kycverification->nationality = $request->nationality;
        $kycverification->place_of_birth = $request->place_of_birth;
        $kycverification->email = $request->email;
        $kycverification->birthday = $request->birthday;
        $kycverification->phone = $request->phone;
        $kycverification->city = $request->city;
        $kycverification->street_address = $request->street_address;
        $kycverification->street_address_2 = $request->street_address_2 ?? '---';
        $kycverification->region_state_province = $request->region_state_province;
        $kycverification->zipcode = $request->zipcode;
        $kycverification->country = $request->country;
        $kycverification->status = "In Process";
        $kycverification->user_id = auth()->user()->id;

        if ($request->file1) {
            $destination = public_path() . "/uploads/files/" . $kycverification->file1;
            if (File::exists($destination) && File::isFile($destination)) {
                File::delete($destination);
            }
            $file = $request->file1;
            $file_name = time() . "." . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/files", $file_name);
            $kycverification->file1 = $file_name;
        }
        if ($request->file2) {
            $destination2 = public_path() . "/uploads/files/" . $kycverification->file2;
            if (File::exists($destination2) && File::isFile($destination2)) {
                File::delete($destination2);
            }
            $file2 = $request->file2;
            $file_name2 = time() . "." . $file2->getClientOriginalName();
            $file2->move(public_path() . "/uploads/files", $file_name2);
            $kycverification->file2 = $file_name2;
        }
        if ($request->file3) {
            $destination3 = public_path() . "/uploads/files/" . $kycverification->file3;
            if (File::exists($destination3) && File::isFile($destination3)) {
                File::delete($destination3);
            }
            //------------------------------------------------------------------------------------------
            $card_holder_file_id = intval($kycverification->card_holder_file_id);
            $card_holder_id = intval($kycverification->card_holder_id);
            //Delete existing KYC document via API before uploading new one
            if($card_holder_id > 0 && $card_holder_file_id > 0) {
                $uri = '/api/CardHolderManagement/CardHolder_KycDoc_Delete/%s/%s/%s';
                $account_id = config('app.necard_acc_id_mc');
                $client = new \GuzzleHttp\Client([
                    'base_uri' => config('app.necard_api_url_mc'),
                    'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user_mc'),
                                'ApiAuthPassword' => config('app.necard_password_mc')
                            ],
                    ]);
                $uri = sprintf($uri, $account_id, $card_holder_id, $card_holder_file_id);
                try {
                    $response = $client->request('DELETE', $uri);
                    if($response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody()->getContents(), true);
                        if(config('app.debug')) {
                            logger()->debug('KYC file delete response: ' . print_r($data, true));
                        }
                    }
                } catch(\Exception $e) {
                    logger()->error('KYC file delete exception:', ['error' => $e->getMessage()]);
                    //no redirect here, just log the error and continue to upload new document
                }
            }
            //------------------------------------------------------------------------------------------
            //Upload new KYC document via API
            if($card_holder_id > 0) {
                $file3 = $request->file3;
                $file_ext = strtolower(trim($file3->getClientOriginalExtension()));
                if($file_ext == 'jpg' || $file_ext == 'jpeg') {
                    $file_content = file_get_contents($file3->getRealPath());
                    $base64_image_front = base64_encode($file_content);
                    $image_front_ext = 1;
                } elseif($file_ext == 'png') {
                    $file_content = file_get_contents($file3->getRealPath());
                    $base64_image_front = base64_encode($file_content);
                    $image_front_ext = 3;
                } elseif($file_ext == 'pdf') {
                    $file_content = file_get_contents($file3->getRealPath());
                    $base64_image_front = base64_encode($file_content);
                    $image_front_ext = 2;
                } else {
                    return redirect()
                            ->route('front.ne_card', ['step' => 5])
                            ->withInput()
                            ->with('error', 'KYC Error: Unsupported file type for document upload. Only JPG, PNG, and PDF are allowed.');
                }
                $uri = '/api/CardHolderManagement/CardHolder_KycDoc_Add/%s/%s';
                $card_holder_kyc_doc_data = [
                    "docType" => $request->file3_type,
                    "language" => $request->file3_lang,
                    "docNumber" => '',
                    "issuedBy" => $request->file3_issued_by,
                    "issueDate" => $request->file3_issued_date,
                    "expireDate" => '',
                    "imageFront" => $base64_image_front,
                    "imageFrontExt" => $image_front_ext,
                    "imageBack" => '',
                    "imageBackExt" => 0
                ];
                $json_data = json_encode($card_holder_kyc_doc_data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
                if(config('app.debug')) {
                    file_put_contents('file3_base64.json', $json_data);
                }
                try {
                    $account_id = config('app.necard_acc_id_mc');
                    $client = new \GuzzleHttp\Client([
                        'base_uri' => config('app.necard_api_url_mc'),
                        'headers' => [
                                    'Content-Type' => 'application/json',
                                    'Accept' => 'text/plain',
                                    'ApiAuthUsername' => config('app.necard_user_mc'),
                                    'ApiAuthPassword' => config('app.necard_password_mc')
                                ],
                        ]);
                    $uri = sprintf($uri, $account_id, $card_holder_id);
                    $response = $client->request('POST', $uri, [
                        'body' => $json_data,
                    ]);
                    if($response->getStatusCode() == 200) {
                        $data = json_decode($response->getBody()->getContents(), true);
                        if(config('app.debug')) {
                            logger()->debug('KYC file upload response: ' . print_r($data, true));
                        }
                        $card_holder_file_id = intval($data);
                    }
                } catch(\GuzzleHttp\Exception\ClientException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        logger()->error('KYC file client exception with response: ', ['status_code' => $response->getStatusCode(), 'response_body' => $responseBodyAsString]);
                        $errorMessage = json_decode($responseBodyAsString)->errorMessage[0] ?? $responseBodyAsString;
                        return redirect()
                                ->route('front.ne_card', ['step' => 5])
                                ->withInput()
                                ->with('error', 'KYC Error: ' . $errorMessage);
                    } else {
                        logger()->error('KYC file client exception: ', ['error' => $e->getMessage()]);
                        return redirect()
                                ->route('front.ne_card', ['step' => 5])
                                ->withInput()
                                ->with('error', 'KYC Error: ' . $e->getMessage());
                    }
                } catch(\GuzzleHttp\Exception\ServerException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        $responseBodyAsString = $response->getBody()->getContents();
                        logger()->error('KYC file server exception with response: ', ['status_code' => $response->getStatusCode(), 'response_body' => $responseBodyAsString]);
                        $errorMessage = json_decode($responseBodyAsString)->errorMessage[0] ?? $responseBodyAsString;
                        return redirect()
                                ->route('front.ne_card', ['step' => 5])
                                ->withInput()
                                ->with('error', 'KYC Error: ' . $errorMessage);
                    } else {
                        logger()->error('KYC file server exception: ', ['error' => $e->getMessage()]);
                        return redirect()
                                ->route('front.ne_card', ['step' => 5])
                                ->withInput()
                                ->with('error', 'KYC Error: ' . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    logger()->error('Cardholder exception:', ['error' => $e->getMessage()]);
                    return redirect()
                            ->route('front.ne_card', ['step' => 5])
                            ->withInput()
                            ->with('error', 'KYC Error: ' . $e->getMessage());
                }
            }
            //------------------------------------------------------------------------------------------
            $file3 = $request->file3;
            $file_name3 = time() . "." . $file3->getClientOriginalName();
            $file3->move(public_path() . "/uploads/files", $file_name3);
            $kycverification->file3 = $file_name3;
            $kycverification->card_holder_file_id = $card_holder_file_id;
        }
        $kycverification->file3_lang = $request->file3_lang;
        $kycverification->file3_type = $request->file3_type;
        $kycverification->file3_issued_by = $request->file3_issued_by;
        $kycverification->file3_issued_date = $request->file3_issued_date;
        // no update to card_holder_id and status_message on kyc update
        //$kycverification->card_holder_id = $card_holder_id;
        //$kycverification->status_message = $kyc_verify_url;
        //------------------------------------------------------------------------------------------
        //update
        $uri = '/api/CardHolderManagement/CardHolder_Update/%s/%s/%s'; //{AccountId}/{WalletId}/{CardholderId}';
        $account_id = config('app.necard_acc_id_mc');
        $wallet_id = config('app.necard_acc_wallet_mc');
        $calling_codes = $this->country_calling_codes();
        $country_iso_2_codes = $this->country_iso_2_codes();

        $card_holder_data = [
            "firstName" => $request->first_name,
            "midName" => $request->middle_name ?? '',
            "lastName" => $request->last_name,
            "gender" => intval($request->gender),
            "dob" => $request->birthday,
            "adrLine1" => $request->street_address,
            "adrLine2" => $request->street_address_2 ?? '',
            "city" => $request->city,
            "state" => $request->region_state_province,
            "country" => $request->country,
            "zipCode" => $request->zipcode,
            "phoneNum" => (string) preg_replace('/[^\d]+/', '', $request->phone),
            "cellNum" => (string) preg_replace('/[^\d]+/', '', $request->phone),
            "callingCode" => $calling_codes[$request->country] ?? '',
            "countryCallingCode" => $country_iso_2_codes[$request->country] ?? '',
            "emailAdr" => $request->email,
            "nationality" => $request->nationality,
            "placeOfBirth" => $request->place_of_birth,
            "occupation" => 11, //Fixed value
            "employeeID" => "12", //Fixed value
            "cardHolderFirstname" => $request->first_name,
            "cardHolderLastName" => $request->last_name,
        ];
        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => config('app.necard_api_url_mc'),
                'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                                'ApiAuthUsername' => config('app.necard_user_mc'),
                                'ApiAuthPassword' => config('app.necard_password_mc')
                            ],
                ]);
            $uri = sprintf($uri, $account_id, $wallet_id, $kycverification->card_holder_id);
            $response = $client->request('PUT', $uri, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($card_holder_data)
            ]);
            if($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(),true);
                if(config('app.debug')) {
                    logger()->debug('Card holder update api response: ' . print_r($data, true));
                }
            }
        } catch(\Exception $e) {
            logger()->error('Cardholder update exception:', ['error' => $e->getMessage()]);
            return redirect()
                    ->route('front.ne_card', ['step' => 5])
                    ->withInput()
                    ->with('error', 'KYC Error: ' . $e->getMessage());
        }
        //------------------------------------------------------------------------------------------
        $kycverification->update();
        return redirect()->route('front.ne_card', ['step' => 5])->with('message', 'Kyc informated updated successfully');
    }

}
