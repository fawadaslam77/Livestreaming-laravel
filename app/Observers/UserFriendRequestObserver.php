<?php
namespace App\Observers;

use App\Models\FriendRequestMedium;
use App\Models\StreamUserAction;
use App\Models\UserFriendRequest;

class UserFriendRequestObserver
{
    public function creating(UserFriendRequest $model)
    {
        $userFriendRequest = UserFriendRequest::query()->where('user_id', $model->user_id)->where('friend_user_id',$model->friend_user_id)->first();
        if($userFriendRequest){
            $model->setAttribute('id',$userFriendRequest->id);
            $userFriendRequest->medium = $model->medium;
            $this->addMedium($userFriendRequest);
            return false;
        }
        return true;
    }

    public function created(UserFriendRequest $model)
    {
        $this->addMedium($model);
    }

    private function addMedium(UserFriendRequest $model){
        if(in_array($model->medium, [FriendRequestMedium::MEDIUM_APP_REQUEST, FriendRequestMedium::MEDIUM_PHONEBOOK, FriendRequestMedium::MEDIUM_EMAIL, FriendRequestMedium::MEDIUM_FACEBOOK, FriendRequestMedium::MEDIUM_TWITTER, FriendRequestMedium::MEDIUM_GOOGLE_PLUS])){
            $friendRequestMedium = FriendRequestMedium::query()->where('friend_requests_id',$model->id)->where('medium', $model->medium)->first();
            if(!$friendRequestMedium) {
                $model->mediums()->create(['medium' => $model->medium]);
            }
        }
    }
}