<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use App\Http\Requests\Api\CreatePackageRequest;
use App\Http\Requests\Api\UpdatePackageRequest;

class PackageController extends Controller
{
    public $collectionEnvelope = "packages";
    public $modelClass = Package::class;
    public $createRequest = CreatePackageRequest::class;
    public $updateRequest = UpdatePackageRequest::class;

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @return bool
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if($action == "index" || $action == "store" || $action == "update" || $action == "show" || $action == "delete") {
            return true;
        }
        return false;
    }
}
