<?php

namespace App\Http\Controllers\Api;

use App\Events\FriendRequestAcceptedEvent;
use App\Http\Requests\Api\CreateUserFriendRequest;
use App\Http\Traits\JWTUserTrait;
use App\Models\UserFriendRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Input;
use DB;

class UserFriendRequestController extends Controller
{
    public $collectionEnvelope = "friend_requests";
    public $modelClass = UserFriendRequest::class;
    public $createRequest = CreateUserFriendRequest::class;
    //public $updateRequest = UpdateUserStreamRequest::class;


    protected function indexQuery(Request $request){
        
        
        $user = JWTUserTrait::getUserInstance();

        return UserFriendRequest::query()->sentTo($user->id)->pendingOnly();
       
      
    }
    public function notification_test(){
        $notification=new Notification();
        $user_id=Input::get("uid");
        $friend_user_id=Input::get("fid");
        
        $device_data= $notification->friendRequest_notification($user_id,$friend_user_id);
        return $device_data;
    }

    /**
     * Accepts a Friend Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function acceptRequest(Request $request, $id){
        $notification=new Notification();
        if(!$this->checkAccess("accept-request")){
            return $this->response(403, "You do not have access to perform this action", []);
        }
      
        $friendRequest = $this->findModel($id);
        $notify=DB::table("user_friend_requests")
            ->where("id","=",$id)
            ->first();
        
       
        $notification->friendRequest_notification($notify->user_id,$notify->friend_user_id);
        
        if(!$friendRequest){
            return $this->response(404, "Resource not Found!", []);
        }
        
        $user = JWTUserTrait::getUserInstance();
        if($friendRequest->friend_user_id != $user->id){
            return $this->response(403, "You are not authorized to perform this action", []);
        }
        if($friendRequest->status != UserFriendRequest::STATUS_PENDING){
            $currentStatus = UserFriendRequest::$STATUSES[$friendRequest->status];
            return $this->response(400, "Friend request already " . $currentStatus, []);
        }
        
        $friendRequest->status = UserFriendRequest::STATUS_ACCEPTED;
        $friendRequest->save();
        
        // Dispatch Event to Add Friends and Notifications
        event(new FriendRequestAcceptedEvent($friendRequest));

        $responseArray = [];
        $responseArray[$this->collectionEnvelope] = [$friendRequest];
        return $this->response(202, "Resource Updated Successfully!", $responseArray);
    }

    /**
     * Rejects a Friend Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function rejectRequest(Request $request, $id){

        if(!$this->checkAccess("reject-request")){
            return $this->response(403, "You do not have access to perform this action", []);
        }

        $friendRequest = $this->findModel($id);
        if(!$friendRequest){
            return $this->response(404, "Resource not Found!", []);
        }

        $user = JWTUserTrait::getUserInstance();
        if($friendRequest->friend_user_id != $user->id){
            return $this->response(403, "You are not authorized to perform this action", []);
        }

        if($friendRequest->status != UserFriendRequest::STATUS_PENDING){
            $currentStatus = UserFriendRequest::$STATUSES[$friendRequest->status];
            return $this->response(400, "Friend request already " . $currentStatus, []);
        }

        $friendRequest->status = UserFriendRequest::STATUS_REJECTED;
        $friendRequest->save();

        $responseArray = [];
        $responseArray[$this->collectionEnvelope] = [$friendRequest];
        return $this->response(202, "Resource Updated Successfully!", $responseArray);
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
        if($action == "index" || $action == "store" || $action == "delete" || $action == "accept-request" || $action == "reject-request") {
            // $action == "show" || $action == "update"
            return true;
        }
        return false;
    }
}
