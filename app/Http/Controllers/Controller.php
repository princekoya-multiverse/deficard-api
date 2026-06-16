<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function arrayToCsv($data)
    {
        $output = fopen('php://temp', 'w');

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    function remote_get_usdt_address($label, $meta = [])
    {
        $data = ['label' => $label, 'meta' => $meta];
        $client = new \GuzzleHttp\Client([
            'base_uri' => config('app.usdt-api-url'),
            'headers' => [
                'X-API-KEY' => config('app.usdt-api-key'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            //'verify' => false,
            //'debug' => true,
            //'http_errors' => false,
            'timeout' => 10,
        ]);
        $prepend = config('app.usdt-api-url-prepend');
        try {
            $response = $client->post($prepend . '/get_new_address', [
                'json' => $data,
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return null;
        }
        return $data['data'] ?? null;
    }

    function remote_get_usdt_purchase_address($label, $meta = [])
    {
        $data = ['label' => $label, 'meta' => $meta];
        $client = new \GuzzleHttp\Client([
            'base_uri' => config('app.usdt-api-url'),
            'headers' => [
                'X-API-KEY' => config('app.usdt-api-key'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            //'verify' => false,
            //'debug' => true,
            //'http_errors' => false,
            'timeout' => 10,
        ]);
        $prepend = config('app.usdt-api-url-prepend');
        try {
            $response = $client->post($prepend . '/get_new_purchase_address', [
                'json' => $data,
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return null;
        }
        return $data['data'] ?? null;
    }

    function country_names_iso_3() {
        return \Illuminate\Support\Facades\DB::table('countries')->get()->mapWithKeys(function ($item) {
            return [$item->iso_code_3 => $item->country_name];
        })->toArray();
    }

    function country_calling_codes() {
        return \Illuminate\Support\Facades\DB::table('countries')->get()->mapWithKeys(function ($item) {
            return [$item->iso_code_3 => $item->calling_code];
        })->toArray();
    }

    function country_iso_2_codes() {
        return \Illuminate\Support\Facades\DB::table('countries')->get()->mapWithKeys(function ($item) {
            return [$item->iso_code_3 => $item->iso_code];
        })->toArray();
    }
}
