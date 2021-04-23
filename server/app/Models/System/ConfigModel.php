<?php

namespace App\Models\System;

use App\Models\BaseModel;

class ConfigModel extends BaseModel
{

    public $table = "config";

    protected $fillable = ["id","name","title","status","created_at","updated_at","is_delete"];

    //关联子表
    public function configValues()
    {
        return $this->hasMany('App\Models\System\ConfigValueModel','config_id','id');
    }
}
