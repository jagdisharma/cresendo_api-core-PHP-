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

$sql = "INSERT INTO users (name, email) VALUES ('John', 'john@example.com')";

if (mysqli_query($conn, $sql)) {
	//mail('jagdishchandra@enacteservices.com','test','from admin');
    echo "New record created successfully";
} else {
	//mail('jagdishchandra@enacteservices.com','test else','from admin else');
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
//echo"<pre>";print_r($_SERVER);


mysqli_close($conn);

	// $current_time = date("Y-m-d H:i:s");
	// echo $current_time.'<br>';
	// $new_time = date("2018-11-21 13:30:02", strtotime('+24 hours'));
	// //$new_time = date("Y-m-d H:i:s", strtotime('+24 hours'));
	// echo $new_time.'<br>';
	// $video_file = '';

	// if($new_time > $current_time && $video_file != '')
	// {
	// 	echo "Do not send notification";
	// }else if($new_time > $current_time && $video_file == ''){
	// 	echo "User have not added video yet";
	// }
	// else if($new_time < $current_time && $video_file == ''){
	// 	if(mail('jagdishchandra@enacteservices.com','text','from admin')){
	// 		echo "mail sent";
	// 	}else{
	// 		echo "not sent";
	// 	}
	// }
?>