<?php

namespace App\Http\Controllers\Api;

use App\Models\CmsPage;
use App\Http\Requests\Api\CreateCmsPageRequest;
use App\Http\Requests\Api\UpdateCmsPageRequest;


class CmsPageController extends Controller
{
    public $collectionEnvelope = "cms";
    public $modelClass = CmsPage::class;
    public $createRequest = CreateCmsPageRequest::class;
    public $updateRequest = UpdateCmsPageRequest::class;

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
