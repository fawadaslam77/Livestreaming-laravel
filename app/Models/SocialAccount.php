<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "social_accounts".
 *
 * @property int $id
 * @property int $user_id
 * @property string $platform
 * @property string $client_id
 * @property string $properties
 * @property string $token
 * @property string $code
 * @property string $email
 * @property string $username
 * @property string $expires_at
 * @property string created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User $user
 */
class SocialAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','platform','client_id','token','email','username','expires_at'
    ];

    protected $visible = [
        'user_id','platform','client_id','token','email','username','expires_at','created_at','updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
