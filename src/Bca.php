<?php

namespace Ngungut\Bca;

use GuzzleHttp;
use Illuminate\Support\Facades\Storage;
use Ngungut\Bca\Exception\BCAException;

class Bca
{
    /**
     * BCA Client ID
     *
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $client_id;

    /**
     * BCA Client Secret
     *
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $client_secret;

    /**
     * BCA API Key
     *
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $api_key;

    /**
     * BCA API Secret
     *
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $api_secret;

    /**
     * GuzzleHttp Client class
     *
     * @var GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * BCA oauth Access Token
     *
     * @var string
     */
    protected $accessToken;

    /**
     * BCA oauth token type
     *
     * @var string
     */
    protected $tokenType;

    public function __construct()
    {
        $apiUrl = config('bca.api_url');
        $this->client_id = config('bca.client_id');
        $this->client_secret = config('bca.client_secret');
        $this->api_key = config('bca.api_key');
        $this->api_secret = config('bca.api_secret');
        $this->httpClient = new GuzzleHttp\Client(['base_uri' => $apiUrl]);
    }

    /**
     * Set access token from saved file or new request
     *
     * @throws BCAException
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    protected function setToken()
    {
        if (Storage::exists('bca-token.json')) {
            $file = Storage::get('bca-token.json');
            $jsonData = json_decode($file, true);
            $now = time();
            if (!empty($jsonData['expires_in'])) {
                if ($now > $jsonData['expires_in']) {
                    $this->auth();
                } else {
                    $this->accessToken = $jsonData['access_token'];
                    $this->tokenType = $jsonData['token_type'];
                }
            } else {
                $this->auth();
            }
        } else {
            $this->auth();
        }
    }

    /**
     * BCA oauth action
     *
     * @throws BCAException
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    protected function auth()
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
        $response = $this->httpClient->request(config('bca.endpoint.auth.method'), config('bca.endpoint.auth.uri'), $request);

        $body = $response->getBody();
        $data = json_decode($body, true);
        if ($data) {
            if (!empty($data['access_token'])) {
                $this->accessToken = $data['access_token'];
                $this->tokenType = $data['token_type'];
                $now = time();
                $savedData = [
                    'access_token' => $data['access_token'],
                    'token_type' => $data['token_type'],
                    'expires_in' => $now + $data['expires_in'], // convert to timestamp
                    'scope' => $data['scope'],
                ];
                if (Storage::exists('bca-token.json')) {
                    Storage::delete('bca-token.json');
                }

                Storage::put('bca-token.json', json_encode($savedData));
            }
        }
    }

    /**
     * Generate ISO 8601 date time format for request
     *
     * @return string
     */
    protected function getTime()
    {
        $date = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
        $dateTime = $date->format('Y-m-d\TH:i:s');
        $gmt = $date->format('P');
        $microTime = $date->format('u');
        return sprintf("$dateTime.%s%s", substr($microTime, 0, 3), $gmt);
    }

    /**
     * Generate signature for request
     *
     * @param $signatureUri string signature URI with method format HTTPMethod:RelativeUrl e.g GET:/banking/v3/corporates
     * @param $time string ISO 8601 BCA format time
     * @param $request array|string request body or blank string
     * @return string
     */
    protected function generateSignature($signatureUri, $time, $request = '')
    {
        $hash = null;
        if (is_array($request)) {
            $data = json_encode($request, JSON_UNESCAPED_SLASHES);
            $hash = hash('sha256', $data);
        } else {
            $hash = hash('sha256', $request);
        }
        $signatureString = $signatureUri . ':' . $this->accessToken . ':' . $hash . ':' . $time;
        return hash_hmac('sha256', $signatureString, $this->api_secret);
    }
}