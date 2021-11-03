<?php
namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Http\Request;
use App\Http\Traits\JWTUserTrait;

class BlameableObserver
{
    public function getUserId(){
        if(Auth::check()){
            return Auth::user()->id;
        }
        elseif(($user = JWTUserTrait::getUserInstance(JWTUserTrait::extractToken()))!==false){
            return $user->id;
        }
    }

    public function creating(Model $model)
    {
        $created_by = property_exists($model ,'CREATED_BY') ? $model->CREATED_BY : "created_by";
        if($model->isFillable($created_by)){
            $model->setAttribute($created_by, $this->getUserId());
        }
    }

    public function updating(Model $model)
    {
        $updated_by = property_exists($model ,'UPDATED_BY') ? $model->UPDATED_BY : "updated_by";
        /* // SOFTDELETE CHECK
        if(($model->getOriginal('deleted_at') == null)){ //  && $model->deleted_at != null
            $this->deleting($model);
        } else
        */
        if($model->isFillable($updated_by)){
            $model->setAttribute($updated_by, $this->getUserId());
        }
    }

    public function deleting(Model $model)
    {
        $deleted_by = property_exists($model ,'DELETED_BY') ? $model->DELETED_BY : "deleted_by";
        if($model->isFillable($deleted_by)){
            $model->setAttribute($deleted_by, $this->getUserId());
        }
    }
}