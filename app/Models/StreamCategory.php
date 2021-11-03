<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "stream_categories".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User $user
 * @property \App\Models\UserStreamCategory[] $userStreamCategory
 * @property \App\Models\UserStream[] $streams
 */
class StreamCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [ 'name', 'description' ];
    protected $visible = [ 'name', 'description' ,'created_at','updated_at'];

    // stream category has many streams
    public function userStreamCategory()
    {
        return $this->hasMany('App\Models\UserStreamCategory','category_id');
    }

    // stream category has many streams
    public function streams()
    {
        return $this->belongsToMany('App\Models\UserStream', 'user_stream_categories');
    }
}
