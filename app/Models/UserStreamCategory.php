<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * This is the model class for table "user_stream_categories".
 *
 * @property int $id
 * @property int $stream_id
 * @property int $category_id
 *
 * @property \App\Models\UserStream $stream
 * @property \App\Models\StreamCategory $category
 */
class UserStreamCategory extends Model
{
    public $timestamps = false;
    protected $fillable = ['stream_id','category_id'];
    protected $visible = ['stream_id','category_id'];
}
