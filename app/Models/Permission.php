<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "permissions".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\Role[] $roles
 */
class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','description'
    ];

    protected $visible = [
        'id','name','description','created_at','updated_at'
    ];

    // permission has many roles
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_permissions');
    }
}
