<?php

namespace App\Services\PowerBI;

use GuzzleHttp\Client;

class Authentication
{
    public static function getAccessToken()
    {
        $config = config('powerbi');

        $client = new Client();
        if (strtolower($config['authenticationMode']) === 'masteruser') {
            $response = $client->post('https://login.microsoftonline.com/' . $config['tenantId'] . '/oauth2/v2.0/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $config['clientId'],
                    'username' => $config['pbiUsername'],
                    'password' => $config['pbiPassword'],
                    'scope' => $config['scopeBase']
                ]
            ]);
        } elseif (strtolower($config['authenticationMode']) === 'serviceprincipal') {
            $response = $client->post('https://login.microsoftonline.com/' . $config['tenantId'] . '/oauth2/v2.0/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $config['clientId'],
                    'client_secret' => $config['clientSecret'],
                    'scope' => $config['scopeBase']
                ]
            ]);
        }

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }
}
