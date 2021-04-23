<?php

namespace App\Models\Business;

use App\Models\BaseModel;

class ArticleModel extends BaseModel
{

    public $table = "article";

    protected $fillable = ["id","title","desc","content","author","tag",
        "status","created_at","updated_at","is_delete"];

}
