<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use DB;
use Input;

class Notification extends Model
{

    protected $table = 'user_devices';
    protected $fillable = ['device_name','ud_id','user_id','platform','created_at','updated_at'];
    protected $visible = ['id','device_name','ud_id','user_id','platform','created_at','updated_at'];
    
    public function getUserDevices($user_id,$ud_id,$registration_id){
        
           $device_data=$stream_data = DB::table('user_devices')
            ->where('user_id',"=",$user_id)
            ->where('ud_id',"=",$ud_id)
            ->first();
           return $device_data;
    }
    public function setUserDevices($user_id,$ud_id,$registration_id){
        DB::insert('insert into `user_devices` (ud_id,user_id,registration_id) values(?,?,?)',[$ud_id,$user_id,$registration_id]);
        return array("ud_id"=>$ud_id,"user_id"=>$user_id,"registration_id"=>$registration_id);
    }
    public function updateUserDevice($user_id,$ud_id,$registration_id){
        DB::table('user_devices')
            ->where('user_id', $user_id)
            ->where('ud_id',$ud_id)
            ->update(["registration_id"=>$registration_id]);
       return array("ud_id"=>$ud_id,"user_id"=>$user_id,"registration_id"=>$registration_id);
    }
    public function send_notification($registrationID,$title,$body){
         ini_set('max_execution_time', 500);
           // define( 'NOTIFICATION_KEY', 'AAAA0CqMLEY:APA91bEG5J_-iMc2eprEIa81mTULX_zmB0TLlZaQot3pBGlkPtx7wcthEszpmdEkM0xqMYpF3A89HfIR1oErbYHobPHxVsPDLPb44MbB7umf4qaxR1BMtx2-gmM7gNOYdfbU0G0WgBQu');
           $notification_key='AAAA0CqMLEY:APA91bEG5J_-iMc2eprEIa81mTULX_zmB0TLlZaQot3pBGlkPtx7wcthEszpmdEkM0xqMYpF3A89HfIR1oErbYHobPHxVsPDLPb44MbB7umf4qaxR1BMtx2-gmM7gNOYdfbU0G0WgBQu';
            $registrationIds = $registrationID;
        #prep the bundle
             $msg = array
                  (
        		'body' 	=> $body,
        		'title'	=> $title
                  );
        	$fields = array
        			(
        				'to'		=> $registrationIds,
        				'notification'	=> $msg
        			);
        	
        	
        	$headers = array
        			(
        				'Authorization: key=' . $notification_key,
        				'Content-Type: application/json'
        			);
        #Send Reponse To FireBase Server	
        		$ch = curl_init();
        		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        		curl_setopt( $ch,CURLOPT_POST, true );
        		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        	
                $result = curl_exec($ch);
        		curl_close( $ch );
        #Echo Result Of FireBase Server
        return $result;
     }
    
     public function ios_notification($registrationID,$title,$body){
        
        // Provide the Host Information.
        //$tHost = 'gateway.sandbox.push.apple.com';
        $tHost = 'gateway.sandbox.push.apple.com';
        $tPort = 2195;
        // Provide the Certificate and Key Data.
        $tCert = public_path() . '\certificate\apns-dev.pem';
        //$tCert = 'http://35.160.175.165/streamix/app/Http/Controllers/Api/certificate/apns-dev.pem';
        // Provide the Private Key Passphrase (alternatively you can keep this secrete
        // and enter the key manually on the terminal -> remove relevant line from code).
        // Replace XXXXX with your Passphrase
        $tPassphrase = '';
        // Provide the Device Identifier (Ensure that the Identifier does not have spaces in it).
        // Replace this token with the token of the iOS device that is to receive the notification.
        //$tToken = 'b3d7a96d5bfc73f96d5bfc73f96d5bfc73f7a06c3b0101296d5bfc73f38311b4';
        //$tToken = '0a32cbcc8464ec05ac3389429813119b6febca1cd567939b2f54892cd1dcb134';
        $tToken= $registrationID;//"679d2bcb058f30b1c47a36b9fc48c8aac45427597fc14a2483d22fd3e0e53de5";
        //0a32cbcc8464ec05ac3389429813119b6febca1cd567939b2f54892cd1dcb134
        // The message that is to appear on the dialog.
        $tAlert = $title;//'You have a LiveCode APNS Message';
        // The Badge Number for the Application Icon (integer >=0).
        $tBadge = 8;
        // Audible Notification Option.
        $tSound = 'default';
        // The content that is returned by the LiveCode "pushNotificationReceived" message.
        $tPayload =$body;// 'APNS Message Handled by LiveCode';
        // Create the message content that is to be sent to the device.
        $tBody['aps'] = array (
        'alert' => $tAlert,
        'badge' => $tBadge,
        'sound' => $tSound,
        );
        $tBody ['payload'] = $tPayload;
        // Encode the body to JSON.
        $tBody = json_encode ($tBody);
        // Create the Socket Stream.
        $tContext = stream_context_create([
                'ssl' => [
                    'verify_peer'      => true,
                    'verify_peer_name' => true,
                    'cafile'           => public_path() . '\certificate\entrust_2048_ca.cer',
                ]
            ]);
        stream_context_set_option ($tContext, 'ssl', 'local_cert', $tCert);
        
        // Remove this line if you would like to enter the Private Key Passphrase manually.
        stream_context_set_option ($tContext, 'ssl', 'passphrase', $tPassphrase);
        // Open the Connection to the APNS Server.
        $tSocket = stream_socket_client ('ssl://'.$tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
        // Check if we were able to open a socket.
        if (!$tSocket)
        exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);
        // Build the Binary Notification.
        $tMsg = chr (0) . chr (0) . chr (32) . pack ('H*', $tToken) . pack ('n', strlen ($tBody)) . $tBody;
        // Send the Notification to the Server.
        $tResult = fwrite ($tSocket, $tMsg, strlen ($tMsg));
        if ($tResult)
        echo 'Delivered Message to APNS' . PHP_EOL;
        else
        echo 'Could not Deliver Message to APNS' . PHP_EOL;
        // Close the Connection to the Server.
        fclose ($tSocket);
        
        
     }
    
    
     public function friendRequest_notification($user_id,$friend_user_id){
           $device_data= DB::table('users')
            ->join('user_devices', 'users.id','user_devices.user_id')
            ->where("users.id","=",$user_id)
            ->select("users.username","users.email","user_devices.*")
            ->get();
           $device_data1= DB::table('users')
            ->join('user_devices', 'users.id','user_devices.user_id')
            ->where("users.id","=",$friend_user_id)
            ->select("users.username","users.email","users.full_name","user_devices.*")
            ->first(); 
            
            foreach($device_data as $data){
                $title="Your Friend Request Approved";
                $body=$device_data1->full_name." Accepted Your Friend Request";
               if($data->device_name==0){
              $this->send_notification($data->registration_id,$title,$body);
                }else{
              $this->ios_notification($data->registration_id,$title,$body);     
                }
            } 
       return $device_data; 
     
     }
    
    
}
