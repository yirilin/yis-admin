<?php

namespace App\Services\Business;

use App\Services\Service;
use App\Utils\ResultHelper;
use App\Models\Business\ArticleModel;

class ArticleService extends Service
{
    use ResultHelper;

    protected $model;

    /**
     * 查询配置
     */
    protected $search = array(
        'and'=>array(
            'title'=>'Like',
            'desc'=>'Like'
        ),
        'or'=>array()
    );

    public function __construct(ArticleModel $model)
    {
        $this->model = $model;
    }
}
