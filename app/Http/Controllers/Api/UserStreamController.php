<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateStreamUserActionRequest;
use App\Http\Requests\Api\CreateUserStreamRequest;
use App\Http\Traits\JWTUserTrait;
use App\Models\StreamUserAction;
use App\Models\UserStream;
use App\Models\Notification;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\UserExistVerificationRequest;
use Input;
use DB;




class UserStreamController extends Controller
{
    public $collectionEnvelope = "streams";
    public $modelClass = UserStream::class;
    public $createRequest = CreateUserStreamRequest::class;
    //public $updateRequest = UpdateUserStreamRequest::class;
    public $defaultSortAttribute = false;

    /**
     * Additional Actions
     */

    /**
     * End a User Stream.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function endStream(Request $request, $id){

        //$friend=new Friend();
        $stream_time=Input::get("stream_time");
        if(!$this->checkAccess("end")){
            return $this->response(403, "You do not have access to perform this action", []);
        }
        $friend->getFriendsDevices($id,"New Notification Added","New Notification Is Added Into Your Streams");
        $userStream = $this->findModel($id);
        if(!$userStream){
            return $this->response(404, "Resource not Found!", []);
        }
        // TODO: Check userid of the requester with stream tagged user_ids and stream owner user_ids to decide: end the stream or shift the stream
        $userStream->status = UserStream::STATUS_ENDED;
        $userStream->stream_app = 'vod';
        $userStream->start_time=$stream_time;
        $userStream->save();

        $responseArray = [];
        $responseArray[$this->collectionEnvelope] = [$userStream];
        return $this->response(202, "Resource Updated Successfully!", $responseArray);
    }

    protected function indexQuery(Request $request){

        // TODO: Disable filters for the time being.
        /*$user = JWTUserTrait::getUserInstance();
        // Will not check if this is a user because we will be already testing the token in checkAccess.
        $query = UserStream::query()->select('user_streams.*')->liveOrAvailableLater()->publicOnly()->orderByStatus()->popular();
        // TODO: Add Nearby Scope if lng and lat is provided in request.
        if($user->role_id != 1){
            $query->sharedByFriendOrFollowed($user->id);
            $query->privateStreamsSharedTo($user->id);
            $query->notBlockedByUser($user->id);
        }
        $query->latestFirst();
        //dd($query->toSql());*/
        $query = UserStream::query()->orderBy("created_at", "desc");
        return $query;
    }

    public function StreamActions(CreateStreamUserActionRequest $request){
        $result=StreamUserAction::is_already($request->input("stream_id"),$request->input("user_id"),$request->input("type"));

        if($result>0){
            $errors=array("stream"=>["You have already added this stream, please try another one."]);
            $message = "You have already added this stream, please try another one.";
            $message = $errors['stream'][0];
            return Controller::returnResponse(400, $message,['errors'=>$errors]);
        }else{
          $result= StreamUserAction::create($request->all());
            return $this->response(200, "Success", []);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexByType(Request $request, $type)
    {

        if(!$this->checkAccess("index-by-type")){
            return $this->response(403, "You do not have access to perform this action", []);
        }

        $offset     = $request->input('offset', 0);
        $limit      = $request->input('limit', 10);
        $user = JWTUserTrait::getUserInstance();
        if(($typeKey = array_search(strtolower($type), array_map(function($val) { return str_replace(' ', '-', $val); } ,array_map('strtolower', StreamUserAction::$TYPES))))===false){
            return $this->response(500, "Something Went Wrong!", []);
        }
        if(!in_array($typeKey, [StreamUserAction::TYPE_BLOCK, StreamUserAction::TYPE_REPORT, StreamUserAction::TYPE_FAVORITE, StreamUserAction::TYPE_WATCH_LATER, StreamUserAction::TYPE_SAVE])){
            return $this->response(403, "Type not allowed", []);
        }

        $query = StreamUserAction::query()->byUser($user->id)->byType($typeKey);
        //$query = UserStream::query()->byActionType($type,$user->id);
        $totalRecords = $query->count();

        if($limit != 0 ){
            $query = $query->offset($offset)->limit($limit);
        }

        $records = $query->get();

        if($records) {
            $records = $records->toArray();
        }
        $responseArray = [];
        $responseArray[$type]      = $records;
        $responseArray['total_records']  = $totalRecords;
        return $this->response(200, "Records Found!", $responseArray);
    }
    public function indexByUserID(UserExistVerificationRequest $request){
        $id=Input::get("user_id");
        $offset=Input::get("offset");
        $limit=Input::get("limit");
               
        if(!$id){
            return $this->response(403, "User ID Required", []);
        } 
        if(!$this->checkAccess("show")){
            return $this->response(403, "You do not have access to perform this action", []);
        }
        $userStream = new UserStream();
        $stream_data=$userStream->StreamById($id,$offset,$limit);
        if(count($stream_data)==0){
            return $this->response(200, "No Record Found", []);
        }
        
        $message="User Stream Found";
        return $this->response(200, $message,  ['user_streams'=>$stream_data,"total_records"=>count($stream_data)]);
        
    }
    
    public function searchStream(Request $request){
        $offset=Input::get("offset");
        $limit=Input::get("limit");
        $stream_name=Input::get("stream_name");
        if($offset=="0" && $limit=="0"){
            $offset="0";
            $limit="1000";
        }
        //$stream_name=$request->input("stream_name");
        $user=JWTUserTrait::getUserInstance();
        if(!$stream_name){
            return $this->response(403, "Stream Name Required", []);
        } 
        if(!$this->checkAccess("show")){
            return $this->response(403, "You do not have access to perform this action", []);
        }
       
       $userStream = new UserStream();
       $result=$userStream->StreamByName($user->id,$stream_name,$offset,$limit);
        //dd($result);

       if(count($result)==0){
        $message="User Stream Not Found";
        return $this->response(200, $message,  []);
       }else{
        $message="User Stream Found";
        return $this->response(200, $message,  ['user_streams'=>$result,"total_records"=>count($result)]);
       }
       
        
    }
    public function stream_filter(Request $request){
        $offset=$request->input("offset");
        $limit=$request->input("limit");
        if(!$this->checkAccess("show")){
            return $this->response(403, "You do not have access to perform this action", []);
        }
        
        $stream_filter=$request->input("stream_filter");
        $user=JWTUserTrait::getUserInstance();
        
        $userStream=new UserStream();
      
        $stream_data=$userStream->StreamByFilter($stream_filter,$user->id,$offset,$limit);

        $message="User Required Streams Found";
        return $this->response(200, $message,  ['user_streams'=>$stream_data["stream_data"],"total_records"=>$stream_data["total_count"]]);
        
    }
    public function popular_streams(){
        $user = JWTUserTrait::getUserInstance();
        $userStream=new UserStream();
        $streams_data=$userStream->StreamByViewers($user->id);

        $message="Streams Record Found";
        return $this->response(200, $message,  ['user_streams'=>$streams_data,"total_records"=>count($streams_data)]);
    }
    public function checkUserStatus(){//$user_id,$friend_user_id
       $user_id=Input::get("id");
       $friend_user_id=Input::get("friend_id");
      $userStream=new UserStream();
      $userResult=$userStream->userStatus($user_id,$friend_user_id);
       return $userResult;
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
        if($action == "index" || $action == "index-by-type" || $action == "store" || $action == "end" || $action=="show" ) {
            // $action == "show" || $action == "update"  || $action == "delete"
            return true;
        }
        return false;
    }
}
