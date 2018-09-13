<?php

namespace Ngungut\Bca;

use GuzzleHttp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Ngungut\Bca\Exception\BCAException;

class Bca
{
    protected $client_id;

    protected $client_secret;

    protected $httpClient;

    protected $accessToken;

    public function __construct()
    {
        $this->client_id = config('bca.client_id');
        $this->client_secret = config('bca.client_secret');
        $apiUrl = config('bca.api_url');
        $this->httpClient = new GuzzleHttp\Client(['base_uri' => $apiUrl]);
    }

    protected function setToken()
    {
        if (Storage::disk('bca')->exists('token.json')) {
            $file = Storage::disk('bca')->get('token.json');
            $jsonData = json_decode($file, true);
            $now = time();
            if (!empty($jsonData['expires_in'])) {
                if ($now > $jsonData['expires_in']) {
                    $this->auth();
                } else {
                    $this->accessToken = $jsonData['access_token'];
                }
            } else {
                $this->auth();
            }
        } else {
            $this->auth();
        }
    }

    public function getToken()
    {
        return $this->accessToken;
    }

    public function auth()
    {
        if (!$this->client_id || !$this->client_secret) {
            throw new BCAException('Client ID or Client Secret not found!');
        }

        $authorization = base64_encode($this->client_id . ':' . $this->client_secret);
        $headers = array(
            'Authorization' => 'Basic ' . $authorization,
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

        $request = [
            'headers' => $headers,
            'form_params' => [
                'grant_type' => 'client_credentials'
            ],
        ];
        Log::channel('bca')->info(json_encode($request));
        $response = $this->httpClient->request('POST', config('bca.uri.auth'), $request);

        $body = $response->getBody();
        $data = json_decode($body, true);
        if ($data) {
            if (!empty($data['access_token'])) {
                $this->accessToken = $data['access_token'];
                $now = time();
                $savedData = [
                    'access_token' => $data['access_token'],
                    'token_type' => $data['token_type'],
                    'expires_in' => $now + $data['expires_in'], // convert to timestamp
                    'scope' => $data['scope'],
                ];
                if (Storage::disk('bca')->exists('token.json')) {
                    Storage::disk('bca')->delete('token.json');
                }

                Storage::disk('bca')->put('token.json', json_encode($savedData));
            }

            Log::channel('bca')->info(json_encode($data));
        }
    }

    protected function getTime()
    {
        $date = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
        $dateTime = $date->format('Y-m-d\TH:i:s');
        $gmt = $date->format('P');
        return sprintf("$dateTime.%s%s", substr(microtime(), 2, 3), $gmt);
    }
}