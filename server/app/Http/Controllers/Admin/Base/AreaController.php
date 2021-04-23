<?php

namespace App\Http\Controllers\Admin\Base;

use App\Http\Controllers\Controller;
use App\Services\Base\AreaService;

class AreaController extends Controller
{
    protected $server;

    public function __construct(AreaService $server)
    {
        $this->server = $server;
    }
}
