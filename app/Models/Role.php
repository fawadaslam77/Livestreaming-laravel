<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property \App\User[] $users
 * @property \App\Models\Permission[] $permissions
 * @property \App\Models\RolePermission[] $rolePermission
 */
class Role extends Model
{
    use SoftDeletes;

    protected $visible = [
        'id','name','slug','parent_ids','is_admin','parents', 'permits', 'permission_ids','created_at','updated_at'
    ];

    protected $fillable = [
        'name','slug','parent_ids','is_admin', 'permission_ids'
    ];

    protected $appends = ['parents', 'permits', 'permission_ids'];

    // role has many users
    public function users()
    {
        return $this->hasMany('App\User','role_id');
    }

    // role has many permissions
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission', 'role_permissions');
    }

    public function rolePermission(){
        return $this->hasMany('App\Models\RolePermission', 'role_id');
    }

    public function nestedPermissions(){
        $permissions = [];
        // Loop through the parent roles;
        foreach (explode(',' , $this->parent_ids) as $parent_id) {
            if($parent_id<1){
                continue;
            }
            $parent_id = trim($parent_id);
            $role = Role::find($parent_id);
            $nestedPermissions = $role->nestedPermissions();
            $permissions = array_merge($permissions , $nestedPermissions);
        }
        $permissions = array_merge($permissions, $this->permissions->toArray());
        return $permissions;
    }

    public function checkPermissionByName($name){
        $permissions = $this->nestedPermissions();
        foreach ($permissions as $permission) {
            if($permission['name']==$name){
                return true;
            }
        }
        return false;
    }

    public function parentRoles(){
        $roles = [];
        foreach (explode(',' , $this->parent_ids) as $parent_id) {
            if($parent_id<1){
                continue;
            }
            $parent_id = trim($parent_id);
            $roles[] = Role::find($parent_id);
        }
        return $roles;
    }

    public function parentRoleNames(){
        $names = [];
        $roles = $this->parentRoles();
        foreach ($roles as $role) {
            $names[] = $role->name;
        }
        return implode(', ',$names);
    }



    public function getParentsAttribute(){
        return $this->parentRoleNames();
    }

    public function getPermitsAttribute(){
        return $this->permissions;
    }

    public function getPermissionIdsAttribute(){
        $perm_ids = [];
        $permissions = $this->permissions;
        foreach ($permissions as $permission) {
            if(is_array($permission)){
                $perm_ids[] = $permission['id'];
            } else{
                $perm_ids[] = $permission->id;
            }

        }
        return $perm_ids;
    }

    public function setPermissionIdsAttribute($permission_ids){
        // ONLY ON UPDATE
        if($this->exists) {
            // Delete all existing permits
            foreach ($this->rolePermission as $permission) {
                $this->permissions()->detach($permission);
            }

            // Insert all new permits
            foreach (explode(',', $permission_ids) as $permission_id) {
                if(!empty($permission_id)) {
                    $this->permissions()->attach($permission_id);
                }
            }
        }
    }
}
