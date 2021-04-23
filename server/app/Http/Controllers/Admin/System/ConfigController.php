<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\System\ConfigService;

class ConfigController extends Controller
{
    protected $server;

    public function __construct(ConfigService $server)
    {
        $this->server = $server;
    }
}
