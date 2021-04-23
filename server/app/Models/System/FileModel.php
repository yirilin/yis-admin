<?php

namespace App\Models\System;

use App\Models\BaseModel;

class FileModel extends BaseModel
{

    public $table = "files";

    protected $fillable = ["id","name","path","url","tag","status","created_at","updated_at","is_delete"];
}
