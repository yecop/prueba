<?php

namespace App\Services\PowerBI;

class Utils
{
    public static function getAuthHeader($accessToken)
    {
        return 'Bearer ' . $accessToken;
    }

    public static function validateConfig()
    {
        $config = config('powerbi');

        if (!$config['authenticationMode']) {
            return "AuthenticationMode is empty. Please choose MasterUser or ServicePrincipal in config.php.";
        }

        if (strtolower($config['authenticationMode']) !== 'masteruser' && strtolower($config['authenticationMode']) !== 'serviceprincipal') {
            return "AuthenticationMode is wrong. Please choose MasterUser or ServicePrincipal in config.php";
        }

        if (!$config['clientId']) {
            return "ClientId is empty. Please register your application as Native app in https://dev.powerbi.com/apps and fill Client Id in config.php.";
        }

        if (!self::guid_is_guid($config['clientId'])) {
            return "ClientId must be a Guid object. Please register your application as Native app in https://dev.powerbi.com/apps and fill Client Id in config.php.";
        }

        if (!$config['reportId']) {
            return "ReportId is empty. Please select a report you own and fill its Id in config.php.";
        }

        if (!self::guid_is_guid($config['reportId'])) {
            return "ReportId must be a Guid object. Please select a report you own and fill its Id in config.php.";
        }

        if (!$config['workspaceId']) {
            return "WorkspaceId is empty. Please select a group you own and fill its Id in config.php.";
        }

        if (!self::guid_is_guid($config['workspaceId'])) {
            return "WorkspaceId must be a Guid object. Please select a workspace you own and fill its Id in config.php.";
        }

        if (!$config['authorityUrl']) {
            return "AuthorityUrl is empty. Please fill valid AuthorityUrl in config.php.";
        }

        if (strtolower($config['authenticationMode']) === 'masteruser') {
            if (!$config['pbiUsername'] || !trim($config['pbiUsername'])) {
                return "PbiUsername is empty. Please fill Power BI username in config.php.";
            }

            if (!$config['pbiPassword'] || !trim($config['pbiPassword'])) {
                return "PbiPassword is empty. Please fill password of Power BI username in config.php.";
            }
        } elseif (strtolower($config['authenticationMode']) === 'serviceprincipal') {
            if (!$config['clientSecret'] || !trim($config['clientSecret'])) {
                return "ClientSecret is empty. Please fill Power BI ServicePrincipal ClientSecret in config.php.";
            }

            if (!$config['tenantId']) {
                return "TenantId is empty. Please fill the TenantId in config.php.";
            }

            if (!self::guid_is_guid($config['tenantId'])) {
                return "TenantId must be a Guid object. Please select a workspace you own and fill its Id in config.php.";
            }
        }
    }

    public static function guid_is_guid($guid)
    {
        return preg_match('/^\{?[A-F0-9]{8}\-[A-F0-9]{4}\-[A-F0-9]{4}\-[A-F0-9]{4}\-[A-F0-9]{12}\}?$/i', $guid);
    }
}
