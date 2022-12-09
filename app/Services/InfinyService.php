<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InfinyService
{
    private string $clientId;

    private string $clientSecret;

    private Client $guzzleClient;

    public function __construct()
    {
        $this->clientId = config('infiny.client_id');
        $this->clientSecret = config('infiny.client_secret');
        $this->guzzleClient = app('GuzzleClient')(['base_uri' => config('infiny.url')]);
    }

    /**
     * Obtaining an access token
     *
     * @return null|string
     */
    private function getAccessToken(): ?string
    {
        if (!Cache::has('infiny_access_token')) {
            $path = 'oauth2/access-token';
            $requestParams = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ];

            if (Cache::has('infiny_refresh_token')) {
                $path = 'oauth2/refresh-token';
                $requestParams = array_merge($requestParams, [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => Cache::get('infiny_refresh_token')
                ]);
            }

            try {
                $response = $this->guzzleClient->post($path, [
                    'headers' => ['Accept' => 'application/vnd.cloudlx.v1+json'],
                    'json' => $requestParams
                ]);

                $responseData = json_decode($response->getBody());

                $expiresAt = Carbon::now()->addSeconds($responseData->expires_in);
                Cache::put('infiny_access_token', $responseData->access_token, $expiresAt);
                Cache::forever('infiny_refresh_token', $responseData->refresh_token);
            } catch (GuzzleException $guzzleException) {
                Log::error($guzzleException);
            }
        }

        return Cache::get('infiny_access_token');
    }

    /**
     * Get all services
     *
     * @return array|null
     */
    public function getAllServices(): ?array
    {
        try {
            $response = $this->guzzleClient->get('services', [
                'headers' => [
                    'Accept' => 'application/vnd.cloudlx.v1+json',
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::error($e);
        }

        return null;
    }

    /**
     * Get service details
     *
     * @param int $serviceId
     * @return array|null
     */
    public function getServiceDetails(int $serviceId): ?array
    {
        try {
            $response = $this->guzzleClient->get('services/' . $serviceId . '/service', [
                'headers' => [
                    'Accept' => 'application/vnd.cloudlx.v1+json',
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::error($e);
        }

        return null;
    }
}
