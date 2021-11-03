<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass;

    /**
     * @var string default sort attribute
     */
    public $defaultSortAttribute = "created_at";

    /**
     * @var string default sort method
     */
    public $defaultSortMethod = "asc";

    /**
     * @var string search attribute
     */
    public $searchAttribute = "name";

    /**
     * @var string collection envelope name
     */
    public $collectionEnvelope = "items";

    /**
     * @var string FormRequest class path to validate when Creating a resource
     */
    public $createRequest = '\Illuminate\Http\Request';

    /**
     * @var string FormRequest class path to validate when Updating a resource
     */
    public $updateRequest = '\Illuminate\Http\Request';


    /**
     * @var \Illuminate\Database\Eloquent\Model Current Model
     */
    protected $model;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if(!$this->checkAccess("index")){
            return $this->response(403, "You do not have access to perform this action", []);
        }

        $offset     = $request->input('offset', 0);
        $limit      = $request->input('limit', 10);
        $search     = $request->input('q', "");
        $sortMethod = $this->defaultSortMethod;

        if($this->defaultSortAttribute!=false) {
            $sortAttribute = $request->input('sort', $this->defaultSortAttribute);
            if ($sortAttribute[0] == "-") { // sort = -name (for Descending)
                $sortMethod = "desc";
                $sort = str_replace("-", "", $sortAttribute);
            }
        }

        $modelClass = $this->modelClass;
        if(method_exists($this, "indexQuery")){
            $query = $this->indexQuery($request);
            if($query instanceof \Illuminate\Http\JsonResponse){
                return $query;
            }
        } else {
            $query = $modelClass::query();
        }

        if($search != "") {
            $query = $query->where($this->searchAttribute, 'like' , '%'.$search.'%');
        }

        $totalRecords = $query->count();

        if($limit != 0 ){
            $query = $query->offset($offset)->limit($limit);
        }

        if($this->defaultSortAttribute!=false) {
            $query = $query->orderBy($sortAttribute, $sortMethod);
        }
        $records = $query->get();

        if($records) {
            $records = $records->toArray();
        }
        $responseArray = [];
        $responseArray[$this->collectionEnvelope]      = $records;
        $responseArray['total_records']  = $totalRecords;
        return $this->response(200, "Records Found!", $responseArray);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        if(!$this->checkAccess("show")){
            return $this->response(403, "You do not have access to perform this action", []);
        }

        $model = $this->findModel($id);
        if(!$model){
            return $this->response(404, "Resource not Found!", []);
        }

        $modelClass = $this->modelClass;
        $totalRecords = $modelClass::all()->count();

        $responseArray = [];
        $responseArray[$this->collectionEnvelope] = [$this->model];
        $responseArray['total_records']  = $totalRecords;
        return $this->response(200, "Resource Found Successfully!", $responseArray);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        if(!$this->checkAccess("store")){
            return $this->response(403, "You do not have access to perform this action", []);
        }
        $request = app($this->createRequest);

        $postData = $request->all();

        $modelClass = $this->modelClass;

        $this->model = $modelClass::create( $postData );

        // Fetch After Creating a record, #Fix for `DB::Raw` queries.
        $this->findModel($this->model->id);
        if(!$this->model){
            return $this->response(500, "Something Went Wrong!", []);
        }
        $this->model->wasRecentlyCreated = true;
        $totalRecords = $modelClass::all()->count();

        $responseArray = [];
        $responseArray[$this->collectionEnvelope] = [$this->model];
        $responseArray['total_records']  = $totalRecords;
        return $this->response(201, "Resource Created Successfully!", $responseArray);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        if(!$this->checkAccess("update")){
            return $this->response(403, "You do not have access to perform this action", []);
        }

        $model = $this->findModel($id);
        if(!$model){
            return $this->response(404, "Resource not Found!", []);
        }

        $request = app($this->updateRequest);

        $postData = $request->all();
        $this->model->update( $postData );

        $modelClass = $this->modelClass;
        $totalRecords = $modelClass::all()->count();

        $responseArray = [];
        $responseArray[$this->collectionEnvelope] = [$this->model];
        $responseArray['total_records']  = $totalRecords;
        return $this->response(202, "Resource Updated Successfully!", $responseArray);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if(!$this->checkAccess("delete")){
            return $this->response(403, "You do not have access to perform this action", []);
        }
        $model = $this->findModel($id);
        if(!$model){
            return $this->response(404, "Resource not found!", []);
        }

        if(!$model->delete()){
            return $this->response(500, "Something went wrong!", []);
        }

        $modelClass = $this->modelClass;
        $totalRecords = $modelClass::all()->count();

        $responseArray = [];
        //$responseArray[$this->collectionEnvelope] = [$this->model];
        $responseArray['total_records']  = $totalRecords;
        return $this->response(202, "Resource Deleted Successfully!", $responseArray);
    }

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
        return false;
    }

    /**
     * finds the resource by id else returns false.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @return \Illuminate\Database\Eloquent\Model|false
     */
    public function findModel($id)
    {
        $modelClass = $this->modelClass;
        $this->model = $modelClass::find($id);
        return ($this->model) ? $this->model : false;
    }


    public function response($code, $message, $data){
        return self::returnResponse($code, $message,$data);
    }

    /**
     * @param $code integer
     * @param $message string
     * @param $data array
     * @return \Illuminate\Http\JsonResponse
     */
    public static function returnResponse($code, $message, $data){
        $response['Message'] = $message;
        $response['Response'] = $code;
        $response['Result'] = (Array) $data;
        return response()->json($response);
    }
}
