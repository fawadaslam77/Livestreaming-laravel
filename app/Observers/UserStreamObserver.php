<?php
namespace App\Observers;

use App\Helpers\Helper;
use App\Models\UserStream;
use Illuminate\Support\Facades\DB;

class UserStreamObserver
{
    public function creating(UserStream $model)
    {
        // Call API to Create Stream Credentails.
        // Save Stream URL to URL Attribute
        //$response = Helper::curlRequest("http://35.160.175.165:8087/v2/servers/_defaultServer_/publishers", "POST", json_encode([
        $response = Helper::curlRequest("http://127.0.0.1:8087/v2/servers/_defaultServer_/publishers", "POST", json_encode([
            'name'=>$model->streamUsername,
            'password'=> $model->streamPassword
        ]));
        if($response && isset($response['headers']) && isset($response['headers']['status-code']) && ($response['headers']['status-code'] == 201 || $response['headers']['status-code'] == 409)) {
            //$model->setAttribute('url', 'http://35.160.175.165:1935/live');
            $model->setAttribute('stream_ip', '35.160.175.165');
            $model->setAttribute('stream_port', '1935');
            $model->setAttribute('stream_app', 'live');
            $model->setAttribute('uuid', DB::Raw('uuid()'));
            $model->setAttribute('privacy_setting', $model->user->privacy_setting);
            return true;
        } else {
            if(env('APP_DEBUG',false)){
                print_r($response);
            }
        }
        return false; // If we cannot create stream credentials we will not allow stream to be created.
    }
}