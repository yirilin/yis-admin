<?php

namespace App\Models\Limits;

use App\Models\BaseModel;

class AuthorityModel extends BaseModel
{

    public $table = "authorities";

    protected $fillable = ["id","pid","name","menu_ids",
        "status","created_at","updated_at","is_delete"
    ];

    protected $casts = [
        'menu_ids' => 'array',
    ];

}
