<?php

namespace App\Services\Base;

use App\Services\Service;
use App\Utils\ResultHelper;
use App\Models\Base\AreaModel;

class AreaService extends Service
{
    use ResultHelper;

    protected $model;

    /**
     * 查询配置
     */
    protected $search = array(
        'and'=>array(
            'short_name'=>'Like',
            'pinyin'=>'Like',
            'first'=>'='
        ),
        'or'=>array()
    );

    protected $sort = ['id'=>'ASC'];

    public function __construct(AreaModel $model)
    {
        $this->model = $model;
    }
}
