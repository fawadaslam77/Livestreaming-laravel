<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "cms_pages".
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $body
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class CmsPage extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'title','body', 'meta_keywords', 'meta_description'];
    protected $visible = ['name', 'title','body', 'meta_keywords', 'meta_description', 'created_at','updated_at'];
}
