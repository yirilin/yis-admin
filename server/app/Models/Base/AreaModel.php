<?php

namespace App\Models\Base;

use App\Models\BaseModel;

class AreaModel extends BaseModel
{

    public $table = "area";

    protected $fillable = ["id","pid","name","short_name","merger_name","level","pinyin","code","zip_code","first",
        "lng","lat","status","created_at","updated_at","is_delete"];

}
