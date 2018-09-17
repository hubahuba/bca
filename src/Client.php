<?php
/**
 * Created by PhpStorm.
 * User: nusatrip
 * Date: 17/09/18
 * Time: 15:22
 */

namespace Ngungut\Bca;


use GuzzleHttp\Exception\ClientException;
use Ngungut\Bca\Exception\BCAException;

class Client extends Bca
{
    /**
     * @param $companyId
     * @param array $accountNumbers
     * @return mixed|string
     * @throws BCAException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBalance($companyId, array $accountNumbers)
    {
        if (!$this->api_key || !$this->api_secret) {
            throw new BCAException('API Key or API Secret not found!');
        }

        $this->setToken();
        $accountNumbers = implode(',', $accountNumbers);
        $endpointUri = sprintf(config('bca.endpoint.get_balance.uri'), $companyId, $accountNumbers);
        $endpointMethod = config('bca.endpoint.get_balance.method');
        $signatureUri = $endpointMethod . ':' . $endpointUri;
        $timestamp = $this->getTime();

        $headers = array(
            'Authorization' => $this->tokenType . ' ' . $this->accessToken,
            'Content-Type' => 'application/json',
            'X-BCA-Key' => $this->api_key,
            'X-BCA-Timestamp' => $timestamp,
            'X-BCA-Signature' => $this->generateSignature($signatureUri, $timestamp)
        );
        $request = [
            'headers' => $headers,
        ];

        try {
            $response = $this->httpClient->request($endpointMethod, $endpointUri, $request);

            $body = $response->getBody();

            return json_decode($body, true);
        } catch (ClientException $e) {
            return $e->getMessage();
        }
    }
}