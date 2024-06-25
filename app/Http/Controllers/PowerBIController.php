<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PowerBI\EmbedConfig;
use App\Services\PowerBI\PowerBiReportDetails;
use App\Services\PowerBI\Utils;
use App\Services\PowerBI\Authentication;
use App\Services\PowerBI\EmbedConfigService;

class PowerBIController extends Controller
{
    public function index()
    {
        $configCheckResult = Utils::validateConfig();
        if ($configCheckResult) {
            return response()->json(['error' => $configCheckResult], 400);
        }

        $embedInfo = EmbedConfigService::getEmbedInfo();
        if ($embedInfo['status'] != 200) {
            return response()->json(['error' => $embedInfo['error']], $embedInfo['status']);
        }

        // Extract the necessary details from $embedInfo for embedding the report
        $accessToken = $embedInfo['accessToken'];
        $embedUrl = $embedInfo['embedUrl'][0]->embedUrl;
        $expiry = $embedInfo['expiry'];

        return view('powerbi.index', compact('accessToken', 'embedUrl', 'expiry'));
    }
}
