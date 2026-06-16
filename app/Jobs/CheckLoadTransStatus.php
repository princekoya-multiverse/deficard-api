<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use GuzzleHttp\Client;

class CheckLoadTransStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tries = 3; // Number of attempts to process the job

    public $paymentId = 0;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 300, 600]; // Retry after 1 minute, 5 minutes, and 10 minutes
    }
    /**
     * Create a new job instance.
     */
    public function __construct($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transaction = Payment::where('id', $this->paymentId)->first();
        logger()->channel('api')->debug('Start API Checking Job: '. now());
        logger()->channel('api')->debug('API: Transaction ID: ' . $this->paymentId);
        if(!$transaction) {
            logger()->channel('api')->debug('API: Checking Transaction not found for ID: ' . $this->paymentId);
            return;
        }
        if(intval($transaction->api_status) === 100) {
            logger()->channel('api')->debug('API: Transaction was loadded manually: ' . $this->paymentId);
            return;
        }
        if(intval($transaction->trans_loaded) === 1) {
            logger()->channel('api')->debug('API: Transaction already loadded: ' . $this->paymentId);
            return;
        }
        //dump($transaction->toArray());
        if($transaction->card->card_type == 'Mastercard')
        {
            //api/ServiceProvider_37/ViewLoadUnloadTransStatus/{AccountId}/{WalletId}/{TransId}
            $uri = '/api/ServiceProvider_37/ViewLoadUnloadTransStatus/%s/%s/%s';
            $account_id = config('app.necard_acc_id_mc');
            $wallet_id = config('app.necard_acc_wallet_mc');
            $base_uri = config('app.necard_api_url_mc');
            $api_user_name = config('app.necard_user_mc');
            $api_password = config('app.necard_password_mc');
        }
        if($transaction->card->card_type == 'Visa')
        {
            //api/ServiceProvider_24/ViewLoadUnloadTransStatus/{AccountId}/{WalletId}/{TransId}
            $uri = '/api/ServiceProvider_24/ViewLoadUnloadTransStatus/%s/%s/%s';
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
        //$amount = intval((floatval($transaction->trans_amount) - floatval($transaction->trans_fee)) * 100); // Convert to cents,
        $uri = sprintf($uri, $account_id, $wallet_id, $transaction->api_trans_id);
        //dd($uri);
        logger()->channel('api')->debug('API: Card ViewLoadUnloadTransStatus Call: ' . $uri);
        $response = $client->request('GET', $uri, [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
        //no try catch here, we are using a job to retry in case of failure
        if(intval($response->getStatusCode()) === 200) {
            $result = $response->getBody()->getContents();
            $data = json_decode($result);
            if(intval($data->errorCode) === 0 && intval($data->transStatus) === 1)
            {
                logger()->channel('api')->debug('API: Success Response: '.print_r($data,true));
                $transaction->api_status = 200;
                $transaction->api_response = $result;
                Payment::create([
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount,
                    'card_id' => $transaction->card_id,
                    'trans_id' => $transaction->id,
                    'card_type' => $transaction->card->card_type,
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
                return; //success, no need to retry
            }
            logger()->channel('api')->debug('API: Failure Response: Code=' . $response->getStatusCode(). ' , Response=' . $result);
            $transaction->api_response = $result;
            if ($this->attempts() >= 3) {
                $transaction->api_trans_id = 0; //reset transaction ID
                $transaction->api_status = 500; //500 is a custom status for API failure
            }
            $transaction->save();
            throw new \Exception('API Response Error: ' . $response->getStatusCode(). ' - ' . (is_array($data->errorMessage) ? $data->errorMessage[0] : $data->errorMessage));
        } else {
            $result = $response->getBody()?->getContents();
            logger()->channel('api')->debug('API: Failure Response: Code=' . $response->getStatusCode(). ' , Response=' . $result);
            $transaction->api_status = $response->getStatusCode();
            $transaction->api_response = $result;
            if ($this->attempts() >= 3) {
                $transaction->api_trans_id = 0; //reset transaction ID
                $transaction->api_status = 500; //500 is a custom status for API failure
            }
            $transaction->save();
            $data = json_decode($result ?? '{errorMessage: "Unknown error"}');
            throw new \Exception('API Response Error: ' . $response->getStatusCode(). ' - ' . (is_array($data->errorMessage) ? $data->errorMessage[0] : $data->errorMessage));
        }
    }
}
