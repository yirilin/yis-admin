<?php

namespace App\Services\{{nameSpace}};

use App\Services\Service;
use App\Utils\ResultHelper;
use App\Models\{{nameSpace}}\{{Template}}Model;

class {{Template}}Service extends Service
{
    use ResultHelper;

    protected $model;

    /**
     * 查询配置
     */
    protected $search = array(
        'and'=>{{search}},
        'or'=>array()
    );

    public function __construct({{Template}}Model $model)
    {
        $this->model = $model;
    }
}
