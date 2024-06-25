<?php

namespace App\Services\PowerBI;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as gp7request;
use Exception;

class EmbedConfigService
{
    public static function getEmbedInfo()
    {
        $config = config('powerbi');

        try {
            $embedParams = self::getEmbedParamsForSingleReport($config['workspaceId'], $config['reportId']);

            return [
                'accessToken' => $embedParams->embedToken['token'],
                'embedUrl' => $embedParams->reportsDetail,
                'expiry' => $embedParams->embedToken['expiration'],
                'status' => 200
            ];
        } catch (Exception $e) {
            return [
                'status' => $e->getCode(),
                'error' => "Error while retrieving report embed details\r\n" . $e->getMessage()
            ];
        }
    }

    private static function getEmbedParamsForSingleReport($workspaceId, $reportId, $additionalDatasetId = null)
    {
        $config = config('powerbi');
        $client = new Client();
        $reportInGroupApi = "https://api.powerbi.com/v1.0/myorg/groups/$workspaceId/reports/$reportId";
        $headers = self::getRequestHeader();

        $request = new gp7request('GET', $reportInGroupApi, $headers);
        $response = $client->send($request);

        if ($response->getStatusCode() !== 200) {
            throw new Exception($response->getReasonPhrase(), $response->getStatusCode());
        }

        $resultJson = json_decode($response->getBody(), true);
        $reportDetails = new PowerBiReportDetails($resultJson['id'], $resultJson['name'], $resultJson['embedUrl']);
        $reportEmbedConfig = new EmbedConfig('report', [$reportDetails], null);

        $datasetIds = [$resultJson['datasetId']];

        if ($additionalDatasetId) {
            $datasetIds[] = $additionalDatasetId;
        }

        $reportEmbedConfig->embedToken = self::getEmbedTokenForSingleReportSingleWorkspace($reportId, $datasetIds, $workspaceId);
        return $reportEmbedConfig;
    }

    private static function getEmbedTokenForSingleReportSingleWorkspace($reportId, $datasetIds, $targetWorkspaceId = null)
    {
        $config = config('powerbi');
        $client = new Client();

        $formData = [
            'reports' => [
                ['id' => $reportId]
            ],
            'datasets' => array_map(function ($datasetId) {
                return ['id' => $datasetId];
            }, $datasetIds)
        ];

        if ($targetWorkspaceId) {
            $formData['targetWorkspaces'] = [['id' => $targetWorkspaceId]];
        }

        $embedTokenApi = "https://api.powerbi.com/v1.0/myorg/GenerateToken";
        $headers = self::getRequestHeader();

        $request = new gp7request('POST', $embedTokenApi, $headers, json_encode($formData));
        $response = $client->send($request);

        if ($response->getStatusCode() !== 200) {
            throw new Exception($response->getReasonPhrase(), $response->getStatusCode());
        }

        return json_decode($response->getBody(), true);
    }

    private static function getRequestHeader()
    {
        $accessToken = Authentication::getAccessToken();
        return [
            'Content-Type' => 'application/json',
            'Authorization' => Utils::getAuthHeader($accessToken)
        ];
    }
}
