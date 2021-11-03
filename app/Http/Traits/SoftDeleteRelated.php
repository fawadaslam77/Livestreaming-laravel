<?php

namespace App\Http\Traits;

trait SoftDeleteRelated {

    /**
     * Boot the soft delete related trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeleteRelated()
    {
        static::deleting(function ($item){
            $relations = self::getDeleteRelationsColumn();
            if($relations) {
                foreach ($relations as $relation) {
                    $item->$relation->each(function($related){
                        $related->delete();
                    });
                }
            }
        });
    }

    /**
     * Get the names of the relations to delete.
     *
     * @return string|false
     */
    public static function getDeleteRelationsColumn()
    {
        return defined('static::DELETE_RELATIONS') ? static::DELETE_RELATIONS : false;
    }

}