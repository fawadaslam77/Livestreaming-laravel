<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "contact_forms".
 *
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $comments
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User $user
 */
class ContactForm extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'email', 'comments'];
    protected $visible = ['user_id', 'email','comments', 'created_at','updated_at'];

    // contact form has a user
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
