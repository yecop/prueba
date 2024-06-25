<?php

namespace App\Services\PowerBI;

class EmbedConfig
{
    public $type;
    public $reportsDetail;
    public $embedToken;

    public function __construct($type, $reportsDetail, $embedToken)
    {
        $this->type = $type;
        $this->reportsDetail = $reportsDetail;
        $this->embedToken = $embedToken;
    }
}
