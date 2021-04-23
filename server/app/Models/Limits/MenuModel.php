<?php

namespace App\Models\Limits;

use App\Models\BaseModel;

class MenuModel extends BaseModel
{

    public $table = "menus";

    protected $fillable = ["id","pid","name","path","meta","component","icon","sort","level","hidden","parameters",
        "status","created_at","updated_at","is_delete"
    ];

    protected $casts = [
        'meta' => 'array',
        'parameters'=>'array',
        'hidden' => 'boolean',
    ];
}
