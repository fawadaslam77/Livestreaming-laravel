<?php

namespace App\Http\Controllers\Api;

use App\Models\Friend;
use App\Models\UserFriendRequest;
use Illuminate\Http\Request;
use App\Http\Traits\JWTUserTrait;
use App\Http\Requests\Api\UserExistVerificationRequest;
use App\Models\Users;
use Input;


class FriendActionController extends Controller
{
    
    public function user_friend_list(){
        $offset=Input::get("offset");
        $limit=Input::get("limit");
        
//        $limit=$request->input("limit");
//        $offset=$request->input("offset");
        $user = JWTUserTrait::getUserInstance();
        $friend=new Friend();
        $result=$friend->user_friend_list($user->id,"10",$offset,$limit);
        
        if(count($result)==0){
           $message="No Result Found";
           return $this->response(200, $message,  []); 
        }
        $message="Searched Friend Found";
        $count=count($result);
        return $this->response(200, $message,  ['friends'=>$result,"total_records"=>count($result)]);
    }
    
    
    
    public function search_friend(){
        $search_string=Input::get("search_string");
        $friend=new Friend();
        $user = JWTUserTrait::getUserInstance();
        //if(empty($search_string)||$search_string==""){
//
//            $result=$friend->user_friend_list($user->id,"10","0","1000");
//                if(count($result)!=0){
//                     $message="Searched Users Record Found";
//                     return $this->response(200, $message,  ['user_details'=>$result]); 
//                }else{
//                     $message="Searched Users Record Not Found";
//                     return $this->response(200, $message,  []);
//                }    
//           
//        }
        
        $result=$friend->search_friend($user->id,$search_string);
        if(count($result)==0){
           $message="Searched Users Record Not Found";
           return $this->response(200, $message,  []); 
        }
        
        $message="Searched Users Record Found";
        return $this->response(200, $message,  ['user_details'=>$result]);
        
        
    }
    public function user_follow(){
        
        $follow_user=Input::get("follow_user_id");
        
        $user = JWTUserTrait::getUserInstance(); // user who is following
        //$follow_user=Users::find($follow_user);  // user to follow
        
      
       if( (empty($follow_user)||!isset($follow_user) ) || (empty($user)||!isset($user) )  ){
           $message="User Record Not Found";
           return $this->response(200, $message,  []); 
       }
      
       if($user->id==$follow_user){
           $message="You Cannot Follow Yourself";
           return $this->response(400, $message,  []);
       }
       
       $friend=new Friend();
       
           $already_friend_data=$friend->get_friendTableData($user->id,$follow_user); // what if data is already in table but type is unfollowed or null or blocked then we have to update already existing data
                
           if(count($already_friend_data)==0){
                $friend->friend_action($user->id,$follow_user,"20");
                $message="User Followed Successfully";
                return $this->response(200, $message,  ["follow_status"=>true]); 
           }else{  
               
               if($already_friend_data[0]->type==0 || $already_friend_data[0]->type==10 || $already_friend_data[0]->type==20 ){
                   $message="You Cannot Follow This User Either Blocked Or Already In Friend / Followers List";
                   return $this->response(400, $message,  ['follow_status'=>""]);
               }
          }  
    }
    
    public function unfollow_user(){
        $unfollow_user=Input::get("unfollow_user");
        $user = JWTUserTrait::getUserInstance(); // user who is unfollowing
       // $unfollow_user=Users::find($unfollow_user);  // user to unfollow
       $friend=new Friend();
       if(empty($unfollow_user)){
           $message="Unfollow User Key Required";
           return $this->response(200, $message,  []); 
       }
       $result=$friend->update_friend_action($unfollow_user,NULL); // unfollow user 
           if($result){
             $message="User Unfollowed Successfully";
             return $this->response(200, $message,  ["user"=>$unfollow_user]);
           }else{
             $message="Something Went Wrong";
             return $this->response(400, $message,  []);
           }
           
    }
    public function block_user(){
        $block_user=Input::get("block_user");
        
        $current_user=JWTUserTrait::getUserInstance();
        $friend=new Friend();
        $user=new Users();
        
        
        
        
        
        $already_data=$friend->get_friendTableData($current_user->id,$block_user);
        
            if(count($already_data)==0){
                
                $result=$friend->friend_action($current_user->id,$block_user,"0");
                $message="User Blocked Successfully";
                return $this->response(200, $message,  ["user_details"=>array("id"=>$result)]);
             }else{        
              
              $result=$friend->update_friend_by_both($current_user->id,$block_user,"0");
              $message="User Blocked Successfully";
              return $this->response(200, $message,  ["user_details"=>array("id"=>$result)]);
            }
        
    }
    public function unblock_user(){
        $unblock_user=Input::get("id");
        $current_user=JWTUserTrait::getUserInstance();
        $friend=new Friend();
        $user=new Users();
          
          $result=$friend->update_friend_action($unblock_user,NULL);
          $message="User Unblocked Successfully";
          return $this->response(200, $message,  ["user_details"=>array("id"=>$result)]);  
    }
    public function get_blocked_users(Request $request){
        $offset=$request->input("offset");
        $limit=$request->input("limit");
    
        $user = JWTUserTrait::getUserInstance();
        $friend=new Friend();
        
        $block_list=$friend->blocked_list_user($user->id,"0",$offset,$limit);
          
            if(count($block_list)==0){
                $message="No Blocked User Found";
                return $this->response(200, $message,  ["users"=>"","total_records"=>0]);
            }else{
                 $message="Blocked User List Found";
                return $this->response(200, $message,  ["users"=>$block_list,"total_records"=>count($block_list)]); 
            }
        
    }
    public function get_following_users(){
        $offset=Input::get("offset");
        $limit=Input::get("limit");
        
       $user = JWTUserTrait::getUserInstance();
       $friend=new Friend();
       $followed_users=$friend->user_following_list($user->id,"20",$offset,$limit); 
       if(count($followed_users)==0){
                 $message="No Following Users Found";
                return $this->response(200, $message,  []);
       }else{
                $message="Following Users Found";
                return $this->response(200, $message,  ["users"=>$followed_users,"total_records"=>count($followed_users)]);
       } 
    }
    
     public function get_followed_users(){
        $offset=Input::get("offset");
        $limit=Input::get("limit");
        
       $user = JWTUserTrait::getUserInstance();
       $friend=new Friend();
       $followed_users=$friend->user_followed_list($user->id,"20",$offset,$limit); 
       if(count($followed_users)==0){
                 $message="No Followed Users Found";
                return $this->response(200, $message,  []);
       }else{
                $message="Followed Users Found";
                return $this->response(200, $message,  ["users"=>$followed_users,"total_records"=>count($followed_users)]);
       } 
    }
    public function unfriend_user(){
        
        $user = JWTUserTrait::getUserInstance();
        $friend_user_id=Input::get("id");
        
        $friend=new Friend();
        
        $message=$friend->unfriend($user->id,$friend_user_id);
        return Controller::returnResponse(200, $message, ["Result"=>array("user_id"=>$friend_user_id)]);        
       
    }
    public function notification_tofriends(){
        
        
    }
    
   

 
    
    
}
