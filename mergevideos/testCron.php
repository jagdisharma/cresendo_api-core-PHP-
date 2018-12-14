<?php

$servername = "localhost";
$username = "root";
$password = "Lbim2201";
$dbname = "cresendo";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/*$currentTime   = date('Y-m-d H:i:s');
$nominatedTime = '2018-11-28 12:38:04';
echo 'cT -- '.$currentTime.'</br>';
$hourdiff = round((strtotime($currentTime) - strtotime($nominatedTime))/3600, 1);
echo 'hourdiff -- '.$hourdiff.'</br>';*/




$url = "https://fcm.googleapis.com/fcm/send";
$serverKey = '';
$title = "Title";

/************** Fetch all data of STREAK Table **************/

$getstreaks = "SELECT * from `streak`";
$getstreakquery = mysqli_query($conn,$getstreaks);
$result = mysqli_fetch_all($getstreakquery,MYSQLI_ASSOC);
//echo '<pre>'; print_r($result); 

$hourdiff = '';

if(count($result)>0){
	foreach($result as $_result){	

		$streakId            = $_result['streak_id'];
		$streakName          = $_result['streak_name'];
		$deviceToken         = $_result['deviceToken'];
		$nominationRequested = $_result['nominationRequested'];

		//echo $_result['streak_id'].'</br>';		

		if( ($nominationRequested != '' || $nominationRequested != 'null' || $nominationRequested != null) && $deviceToken != '' ) {
			
			$currentTime   = date('Y-m-d H:i:s');
			$nominatedTime = $nominationRequested;

			$hourdiff = round((strtotime($currentTime) - strtotime($nominatedTime))/3600, 1);

			//echo 'hourdiff -- '.$hourdiff.'</br>';	

			//echo $streakId.'</br>';

			if( $hourdiff >= 24){

				//echo $streakId.'nomination expired'.'</br>';

				/*******************[ PUSH NOTIFICATION WORKING CODE ]********************************/

				// change device token with dynamic one. Currently I am using staic device token for TEST PURPOSE 

				// Put your device token here (without spaces):
				$deviceToken = '9F381C66CEB93579EAE98D1095390EABCA32A170CC091A379DE080386F5EBC6B';
				// Put your private key's passphrase here:
				$passphrase = 'Lbim2201';
				// Put your alert message here:
				$message = 'A push notification has been sent CRESENDO!';
				////////////////////////////////////////////////////////////////////////////////
				$ctx = stream_context_create();
				stream_context_set_option($ctx, 'ssl', 'local_cert', 'pushcert.pem');
				stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
				// Open a connection to the APNS server
				$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
				if (!$fp)
					exit("Failed to connect: $err $errstr" . PHP_EOL);
				echo 'Connected to APNS' . PHP_EOL;
				// Create the payload body
				$body['aps'] = array(
					'alert' => array(
				        'body' => $message,
						'action-loc-key' => 'Bango App',
				    ),
				    'badge' => 2,
					'sound' => 'oven.caf',
					);
				// Encode the payload as JSON
				$payload = json_encode($body);
				// Build the binary notification
				$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
				// Send it to the server
				$result = fwrite($fp, $msg, strlen($msg));
				if (!$result)
					echo 'Message not delivered' . PHP_EOL;
				else
					echo 'Message successfully delivered' . PHP_EOL;
				// Close the connection to the server
				fclose($fp);



			}//if
		} //if

	}//foreach
} //if

//http://www.assafelovic.com/blog/php-mobile-pus-notifications


mysqli_close($conn);

/*
Array
(
[0] => Array
    (
        [streak_id] => 1
        [streak_name] => streak1
        [video_merged_path] => 
        [deviceToken] => 
        [nominationRequested] => 2018-11-28 11:23:39
        [created_at] => 1539777008
        [updated_at] => 1539777008
    )

    https://www.cumulations.com/blogs/87/how-to-send-push-notifications-in-php-to-ios-devices-using-fcm
*/



?>