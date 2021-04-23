<?php

namespace App\Models\Limits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $dateFormat = 'U';

    public $table = "users";
    protected $primaryKey = "id";
    protected $hidden = ['password',"created_at","is_delete"];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ["id","uuid","username","nickname","password","header","authority_id",
        "status","created_at","updated_at","is_delete"
    ];

    protected $casts = [
    ];

    //YisNet Co.,Ltd
    protected function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5('a08a0e3'.$value.'a7255583d');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    //关联角色表
    public function authority()
    {
        return $this->hasOne('App\Models\Limits\AuthorityModel','id','authority_id');
    }
}
