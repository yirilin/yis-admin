<?php

namespace App\Models\System;

use App\Models\BaseModel;

class ConfigValueModel extends BaseModel
{

    public $table = "config_value";

    protected $fillable = ["id","config_id","label", "value","sort","status","created_at","updated_at","is_delete"];

}
