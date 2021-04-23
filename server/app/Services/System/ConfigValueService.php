<?php

namespace App\Services\System;

use App\Services\Service;
use App\Utils\ResultHelper;
use App\Models\System\ConfigValueModel;

class ConfigValueService extends Service
{
    use ResultHelper;

    protected $model;

    /**
     * 查询配置
     */
    protected $search = array(
        'and'=>array(
            'config_id'=>'=',
            'label'=>'Like',
            'value'=>'Like',
            'status'=>'='
        ),
        'or'=>array()
    );

    protected $sort = ['sort'=>'ASC'];

    public function __construct(ConfigValueModel $model)
    {
        $this->model = $model;
    }
}
