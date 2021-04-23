<?php

namespace App\Services\System;

use App\Services\Service;
use App\Utils\ResultHelper;
use App\Models\System\AccessLogModel;

class AccessLogService extends Service
{
    use ResultHelper;

    protected $model;

    /**
     * 查询配置
     */
    protected $search = array(
        'and'=>array(
            'username'=>'Like',
            'path'=>'Like',
            'status'=>'='
        ),
        'or'=>array()
    );

    public function __construct(AccessLogModel $model)
    {
        $this->model = $model;
    }
}
