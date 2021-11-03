<?php

namespace App\Listeners;

use App\Events\FriendRequestAcceptedEvent;
use App\Models\Friend;

class AddFriendsAfterFriendRequestAccepted
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FriendRequestAcceptedEvent  $event
     * @return void
     */
    public function handle(FriendRequestAcceptedEvent $event)
    {
        $friendRequest = $event->friendRequest;
        $friend = new Friend();
        $friend->user_id = $friendRequest->user_id;
        $friend->friend_user_id = $friendRequest->friend_user_id;
        $friend->type = Friend::TYPE_FRIEND;
        $friend->save();

        $reverseFriend = new Friend();
        $reverseFriend->user_id = $friendRequest->friend_user_id;
        $reverseFriend->friend_user_id = $friendRequest->user_id;
        $reverseFriend->type = Friend::TYPE_FRIEND;
        $reverseFriend->save();

        // TODO: Send Notification to
        //          $friendRequest->user_id that
        //          $friendRequest->friend_user_id has accepted his friend request

    }
}
