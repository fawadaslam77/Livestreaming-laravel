<?php

namespace App\Http\Controllers\Api;

use App\Models\StreamUserAction;
use App\Http\Requests\Api\CreateStreamUserActionRequest;
use Illuminate\Http\Request;
use App\Http\Traits\JWTUserTrait;
use Input;

use DB;
class StreamUserActionController extends Controller
{
    public $collectionEnvelope = "user_actions";
    public $modelClass = StreamUserAction::class;
    public $createRequest = CreateStreamUserActionRequest::class;
//    public $updateRequest = UpdateStreamUserActionRequest::class;



    protected function indexQuery(Request $request){

       //  return print_r($request->all());
       // StreamUserAction model=new StreamUserAction();
       // $is_already_in_record=StreamUserAction::is_already($request->inpuy("stream_id"),$request->inpuy("user_id"),$request->inpuy("type"));
      //  return print_r(@$is_already_in_record);

        if(!$request->has('stream_id')){
            return $this->response(400, "stream_id parameter is required.", []);
        }
        return StreamUserAction::query()->where('stream_id', $request->get('stream_id'));
    }
    public function delete_byType(){
      $user=JWTUserTrait::getUserInstance();
      $user_id=Input::get("user_id");
      $stream_id=Input::get("stream_id");
      $type=Input::get("type");

      $streamData=DB::table('stream_user_actions')
			->where('user_id','=',$user_id) 
            ->where("stream_id","=",$stream_id)
            ->where("type","=",$type)
        	->delete();
      if(count($streamData)==0){
        $message="User Stream Action Not Found";
        return Controller::returnResponse(200, $message, []);
      }else{
      
       $message="User Stream Action Deleted Successfully";
       return Controller::returnResponse(200, $message, ["stream_id"=>$stream_id,"user_id"=>$user_id]);  
      }
        
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
        if($action == "index" || $action == "store" || $action == "show" || $action == "delete") {
//            || $action == "update"
            return true;
        }
        return false;
    }
}
