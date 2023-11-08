<?php

namespace App\Traits;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Laravel\Socialite\Facades\Socialite;

trait InteractsWithFacebookGraphApi
{
    private Client $client;
    private array $config;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://graph.facebook.com'
        ]);
        $this->config = config('services.facebook');
    }

    /**
     * Initialize Facebook login flow.
     *
     * @param string $userAccessToken
     *
     * @return string
     * @throws Exception|GuzzleException
     */
    public function initFacebookLoginFlow(string $userAccessToken): string
    {
        $appAccessToken = $this->getAppAccessToken();

        $response = $this->verifyAccessToken($userAccessToken, $appAccessToken);

        return Socialite::driver('facebook')->stateless()->user();

    }

    /**
     * Get app access token.
     *
     * @return string
     * @throws JsonException|GuzzleException
     */
    public function getAppAccessToken(): string
    {
        $response = $this->client->get("/oauth/access_token?client_id={$this->config['client_id']}&client_secret={$this->config['client_secret']}&grant_type=client_credentials");

        $response = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

        return $response->access_token;
    }

    /**
     * Verify an access token.
     *
     * @throws JsonException|GuzzleException
     */
    public function verifyAccessToken(string $accessToken, string $appAccessToken): object
    {
        $response = $this->client->get("/debug_token?input_token={$accessToken}&access_token={$appAccessToken}");

        return json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
    }
}
