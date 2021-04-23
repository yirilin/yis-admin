<?php

namespace App\Models\System;

use App\Models\BaseModel;

class AccessLogModel extends BaseModel
{

    public $table = 'access_log';

    protected $fillable = ["id","user_id","username","method","path","body","ip","latency","agent","message",
        "status","created_at","updated_at","is_delete"
    ];

    protected $casts = [
        'resp' => 'array',
    ];
}
