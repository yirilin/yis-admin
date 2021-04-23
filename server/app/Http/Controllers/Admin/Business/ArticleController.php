<?php

namespace App\Http\Controllers\Admin\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Business\ArticleService;

class ArticleController extends Controller
{
    protected $server;

    public function __construct(ArticleService $server)
    {
        $this->server = $server;
    }
}
