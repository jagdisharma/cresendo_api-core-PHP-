<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//including the required files
require_once '../include/DbOperation.php';
require '../libs/Slim/Slim.php';
 
 
\Slim\Slim::registerAutoloader();
 
//Creating a slim instance
$app = new \Slim\Slim();

$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/streak/uploads';
 
//Method to display response
function echoResponse($status_code, $response)
{
    //Getting app instance
    $app = \Slim\Slim::getInstance();
 
    //Setting Http response code
    $app->status($status_code);

    /*$app->setCharacterEncoding("UTF8");*/
 
    //setting response content type to json
    $app->contentType('application/json');
 
    //displaying the response in json format
    echo json_encode($response);

} //echoResponse
 
 
function verifyRequiredParams($required_fields)
{
    //Assuming there is no error
    $error = false;
 
    //Error fields are blank
    $error_fields = "";
 
    //Getting the request parameters
    $request_params = $_REQUEST;

    //echo "<pre>"; print_r($_FILES); exit;
 
    //Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        //Getting the app instance
        $app = \Slim\Slim::getInstance();
 
        //Getting put parameters in request params variable
        parse_str($app->request()->getBody(), $request_params);
    }

 
    //Looping through all the parameters
    foreach ($required_fields as $field) {
 
        //if any requred parameter is missing
        if ((!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0)) {
            //error is true
            $error = true;
 
            //Concatnating the missing parameters in error fields
            $error_fields .= $field . ', ';
        }
    }
 
    //if there is a parameter missing then error is true
    if ($error) {
        //Creating response array
        $response = array();
 
        //Getting app instance
        $app = \Slim\Slim::getInstance();
 
        //Adding values to response array
        $response["status"] = 0;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
 
        //Displaying response with error code 400
        echoResponse(400, $response);
 
        //Stopping the app
        $app->stop();
    }
} //verifyRequiredParams


/************ function to check if valid Emial is passed ************/
function isValidEmail($email){ 
    //return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? 1 : 0;
} //isValidEmail
 
//Method to authenticate a user 
function authenticateUser()
{
    //Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    //echo "<pre>"; print_r($headers); exit;
 
    //Verifying the headers
    if (isset($headers['Auth-Key'])) {
 
        //Creating a DatabaseOperation boject
        $db = new DbOperation();
 
        //Getting api key from header
        $api_key = $headers['Auth-Key'];
 
        //Validating apikey from database
        if (!$db->isValidUser($api_key)) {
            $response["status"] = 0;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        // api key is missing in header
        $response["status"] = 0;
        $response["message"] = "Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }
}

/***************************************
    //this method will create a STREAK    
    //The method is POST
    PARAMS :
        -name
        -video (filetype)
****************************************/

$app->post('/createStreak', function () use ($app) {

    verifyRequiredParams(array('name')); 
 
    //Creating a response array
    $response  = array();
    $streakArr = array();
    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
    $uploaddirthumb = $_SERVER['DOCUMENT_ROOT'] . '/uploads/thumbs/';
    $varr = time().rand(1,100);
        
        //reading post parameters
        $name = $app->request->post('name');
        $video_name = '';

        if(isset($_FILES['video']) && $_FILES['video'] != "") {
            /*If you want to check whether size of video is less than 200 then uncomment this. Add these lines in appendStreak also.*/
            
            $filesize = ($_FILES['video']['size'])/(1024*1024);
            //echo $filename;exit();

            $type = explode('.',$_FILES['video']['name']);            
            $videFileType = $type[1];
            
            if($videFileType != "mp4" && $videFileType != "avi" && $videFileType != "mov" && $videFileType != "3gp" && $videFileType != "mpeg")
            {
                $response["status"] = 0;
                $response["message"] = "File format not supported";
                
                echoResponse(200, $response);
                exit;
                
                /*If you want to check whether size of video is less than 200 then uncomment this. Add these lines in appendStreak also.*/
            
            }else if($filesize > 200){
                $response["status"] = 0;
                $response["message"] = "Please try to upload File having size less than 200 MB";
                
                echoResponse(200, $response);
                exit;

            }else{
                $video_name = 'streak-'.$varr.'.'.$type[1];
                move_uploaded_file($_FILES['video']['tmp_name'], $uploaddir.$video_name);
                
                // $sizeofFile = (filesize('/var/www/html/uploads/'.$video_name))/(1024*1024);
                // echo $sizeofFile;exit();
            }

        }else{

            $response["status"] = 0;
            $response["message"] = "Required field(s) video is missing or empty";
            
            echoResponse(200, $response);
            exit;
        }

        /**** UPLOAD IMAGE Thumbnail ****/

        if(isset($_FILES['image']) && $_FILES['image'] != "") {

            $type = explode('.',$_FILES['image']['name']);
            $image_name = 'streak-'.$varr.'.'.$type[1];
            move_uploaded_file($_FILES['image']['tmp_name'], $uploaddirthumb.$image_name);

        }else{

            $response["status"] = 0;
            $response["message"] = "Required field(s) image is missing or empty";
            
            echoResponse(200, $response);
            exit;
        }
        
        $utc_time_value = time();
        $created_at     = $utc_time_value;
        $updated_at     = $utc_time_value;

        //Creating a DbOperation object
        $db = new DbOperation();
     
        //Calling the method publishstreak to add streak to the database
        $res = $db->createstreak($name,$image_name,$video_name,$created_at,$updated_at);

        //echo '<pre>'; print_r($res); exit;

        if(!empty($res)){

            $streakArr['id']      = $res['streakid'];
            $streakArr['name']    = $name;
            $streakArr['video'][0]['videopath']     = HOST.$res['videoname'];
            $streakArr['video'][0]['thumbnailpath'] = HOST_THUMB.$res['imagename'];

                $response['status'] = 1;
                $response["message"] = "Streak successfully published";
                $response["data"] = $streakArr;

                echoResponse(200, $response);

        }else{

            $response["status"] = 0;
            $response["message"] = "Oops! An error occurred while creating";
            $response["data"] = $streakArr;

            echoResponse(200, $response);
        }

}); //createStreak

/************************************************************************
    //this method will create a APPEND a video to already created STREAK    
    //The method is POST
    PARAMS :
        -id
        -video (filetype)
*************************************************************************/

$app->post('/appendtoStreak', function () use ($app) {

    //Creating a DbOperation object
    $db = new DbOperation();    

    verifyRequiredParams(array('id')); 
 
    //Creating a response array
    $response  = array();
    $streakArr = array();
    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
    $uploaddirthumb = $_SERVER['DOCUMENT_ROOT'] . '/uploads/thumbs/';
    $varr = time().rand(1,100);
        
        //reading post parameters
        $id = $app->request->post('id');
        $video_name = '';

        /* to check if streak already exist */
        $ress = $db->isstreakexist($id);

        if($ress == 1){


            if((isset($_FILES['video']) && $_FILES['video'] != "" ) && (isset($_FILES['image']) && $_FILES['image'] != "")) {

                if(isset($_FILES['video']) && $_FILES['video'] != "") {
                        $filesize = ($_FILES['video']['size'])/(1024*1024);

                        $type = explode('.',$_FILES['video']['name']);            
                        $videFileType = $type[1];

                        if($videFileType != "mp4" && $videFileType != "avi" && $videFileType != "mov" && $videFileType != "3gp" && $videFileType != "mpeg")
                        {
                            $response["status"] = 0;
                            $response["message"] = "File format not supported";
                            
                            echoResponse(200, $response);
                            exit;
                        }else{
                            $video_name = 'streak-'.$varr.'.'.$type[1];
                            move_uploaded_file($_FILES['video']['tmp_name'], $uploaddir.$video_name);
                        }
                }else if($filesize > 200){
                    $response["status"] = 0;
                    $response["message"] = "Please try to upload File having size less than 200 MB";
                    
                    echoResponse(200, $response);
                    exit;


                }else{

                    $response["status"] = 0;
                    $response["message"] = "Required field(s) video is missing or empty";
                    
                    echoResponse(200, $response);
                    exit;
                }


                /**** UPLOAD IMAGE Thumbnail ****/

                if(isset($_FILES['image']) && $_FILES['image'] != "") {
                    $type = explode('.',$_FILES['image']['name']);
                    $image_name = 'streak-'.$varr.'.'.$type[1];
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploaddirthumb.$image_name);

                }else{
                    $response["status"] = 0;
                    $response["message"] = "Required field(s) image is missing or empty";
                    
                    echoResponse(200, $response);
                    exit;
                }

            }else{
                    $response["status"] = 0;
                    $response["message"] = "Required field(s) video or image are missing or empty";
                    
                    echoResponse(200, $response);
                    exit;
            }

        }else{

            $response["status"] = 0;
            $response["message"] = "Streak doesnot exist.";
            
            echoResponse(200, $response);
            exit;
        }
        
        $utc_time_value = time();
        $created_at     = $utc_time_value;
        $updated_at     = $utc_time_value;
        
        $state = $db->getState($id);
        //echo $state; exit();
        //Calling the method appendtostreak to add video to existing streak to the database

        //Check if state is 0/1 in database
        if($state == 0){
            $res = $db->appendtostreak($id,$image_name,$video_name,$created_at,$updated_at);
            //echo '<pre>'; print_r($res); exit;
            if(!empty($res)){

                $vidArr = array();
                $videos = $db->getStreakVideos($id);

                    $streakArr['id'] = (int)$id;
                    if(count($videos)>0){
                        $streakArr['name']  = $videos['streak'];
                        $streakArr['video'] = $videos['vidarr'];
                    }

                    $response['status'] = 1;
                    $response["state"] = $state;
                    $response["message"] = "Streak successfully appended";
                    $response["data"] = $streakArr;

                    echoResponse(200, $response);

            }else{

                $response["status"] = 0;
                $response["message"] = "Oops! An error occurred while creating";
                $response["data"] = $streakArr;

                echoResponse(200, $response);
            }
        }else{
            $response["status"] = 0;
            $response["state"] = $state;
            $response["message"] = "Sorry, this cresendo is not available anymore. Please check with the invitee.";
            $response["data"] = $streakArr;

            echoResponse(200, $response);
        }
        

}); //appendtoStreak


/************************************************************************
    //this method will FETCH all STREAK Videos of particular STREAK ID
    //The method is GET
    PARAMS :
        -id
*************************************************************************/

$app->get('/viewStreak', function () use ($app) {

    $streakArr = array();
    $db = new DbOperation();    
    $id = isset($_GET['id']) ? $_GET['id'] : '';

        //echo 'idd-'.$id; exit; 
        if($id != ''){

            $vidArr = array();            
            $videos = $db->getStreakVideos($id);
                
                if(count($videos)>0){
                    
                   // $streakArr['final_merged_video'] = $videos['mergeVideo'];
                    if($videos['state'] == 1){
                        $streakArr['id']    = (int)$id;
                        //$streakArr['name']  = $videos['streak'];
                        //$streakArr['state'] = $videos['state'];

                        $response['status'] = 1;
                        $response['state'] = $videos['state'];
                        $response["message"] = "Sorry, this cresendo is not available anymore. Please check with the invitee.";
                        $response["data"] = $streakArr;

                        echoResponse(200, $response);
                    }else{
                        $streakArr['id']    = (int)$id;
                        $streakArr['name']  = $videos['streak'];
                        $streakArr['video'] = $videos['vidarr'];
                        
                        $response['status'] = 1;
                        $response['state'] = $videos['state'];
                        $response["message"] = "Streak Data";
                        $response["data"] = $streakArr;

                        echoResponse(200, $response);
                    }   
                }
        }else{
            $response["status"] = 0;
            $response["message"] = "Field cannot be blank";
            $response["data"] = $streakArr;

            echoResponse(200, $response);
        }


}); //viewStreak

/************************************************************************************************************/

$app->get('/finalizeStreak', function() use ($app){
    $response  = array();
    $streakArr = array();
    $db = new DbOperation();

    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $var = time().rand(1,100);

    if($id != ''){
        // Fetch data of all videos into a variable $res
        $res = $db->getVideos($id);
        if(!empty($res)){
            // Final path of merge video
            $finalVideo = '/var/www/html/uploads/streak-'.$var.'.mp4';
            // Merging videos command
            exec("ffmpeg $res -filter_complex '[0]scale=720x1080,setsar=1/1[a];[1]scale=720x1080,setsar=1/1[b];[2]scale=720x1080,setsar=1/1[c];[3]scale=720x1080,setsar=1/1[d];[4]scale=720x1080,setsar=1/1[e];[5]scale=720x1080,setsar=1/1[f];[6]scale=720x1080,setsar=1/1[g];[7]scale=720x1080,setsar=1/1[h]; [a][0:a][b][1:a][c][2:a][d][3:a][e][4:a][f][5:a][g][6:a][h][7:a]  concat=n=8:v=1:a=1[v][a]' -map '[v]' -map '[a]' -strict -2 $finalVideo");

            // Name of merge video which is saved in database
            $videoname = 'streak-'.$var.'.mp4';
            // saveMergePath into database
            $savePath = $db->saveMergePath($id,$videoname);

            // Print response if successfull
            $response['status'] = 1;
            $response["message"] = "Videos are merged successfully";
            $response["data"] = $savePath;
            echoResponse(200, $response);
        }else{
            // Print response if videos are less or more than 8
            $response["status"] = 0;
            $response["message"] = "Please Upload all Videos";
            $response["data"] = $res;

            echoResponse(200, $response);
        }
    }else{
        // Print response if id is empty
        $response["status"] = 0;
        $response["message"] = "Field cannot be blank";
        $response["data"] = $streakArr;

        echoResponse(200, $response);
    }
}); 


/************************************************************************************************************/
$app->post('/nominationFired', function() use ($app) {

    $response  = array();
    $streakArr = array();
    //Creating a DbOperation object
    $db = new DbOperation();

    //echo"<pre>";print_r($_POST);
    $device_Token = $app->request->post('deviceToken');
    $streakId = $app->request->post('streakId');

    //echo $device_Token.'--'.$streakId; exit;

    $res = $db->updateStreakData($device_Token, $streakId);

    if(!empty($res)){

        $response['status'] = 1;
        $response["message"] = "Nomination successfully fired";
        $response["data"] = $res;

        echoResponse(200, $response);

    }else{

        $response["status"] = 0;
        $response["message"] = "Oops! An error occurred";
        $response["data"] = $streakArr;

        echoResponse(200, $response);
    }
});

$app->get('/test', function() use ($app) {

    echo date('Y-m-d h:i:s');
});

/************************************************************************************************************/
$app->post('/endStreak', function() use ($app){
    $response = array();
    $streakArr = array();

    $db = new DbOperation();

    $id = isset($_POST['id']) ? $_POST['id'] : '';

    $res = $db->endStreak($id);
    //echo"<pre>";print_r($res);exit();
    if($id != ''){

        $response['status'] = 1;
        $response["message"] = "Streak Ended successfully";
        $response["data"] = $res;

        echoResponse(200, $response);

    }else{

        $response["status"] = 0;
        $response["message"] = "Oops! An error occurred";
        $response["data"] = $streakArr;

        echoResponse(200, $response);
    }
    //echo"<pre>";print_r($_POST);

});
/**********************************************************************************************************/
//For Updating the Name of Streak
$app->post('/updateStreak', function() use ($app){
    $response = array();
    $streakArr = array();

    $db = new DbOperation();

    //need to pass these 2 parameters in the body for updating streak
    $id = $app->request->post('id');
    $name = $app->request->post('name');

    //Send parameters to function updateStreakName
    $res = $db->updateStreakName($name, $id);

    //Check if response.id is not equal to zero
    if($res['id'] !=0){
        $response['status'] = 1;
        $response["message"] = "Streak Updated successfully";
        $response["data"] = $res;

        echoResponse(200, $response);

    }else{
        //show error if id is not an integer
        $response["status"] = 0;
        $response["message"] = "Oops! An error occurred";
        $response["data"] = $streakArr;

        echoResponse(200, $response);
    }
});

/**********************************************************************************************************/
    //demo for add users into database
/*$app->post('/addUsers', function() use ($app) {

    $response  = array();
    $streakArr = array();
    //Creating a DbOperation object
    $db = new DbOperation();

    //echo"<pre>";print_r($_POST);
    $name = $app->request->post('name');
    $email = $app->request->post('email');

    $res = $db->insertuser($name,$email);

    if(!empty($res)){

        $response['status'] = 1;
        $response["message"] = "Streak successfully published";
        $response["data"] = $res;

        echoResponse(200, $response);

    }else{

        $response["status"] = 0;
        $response["message"] = "Oops! An error occurred while creating";
        $response["data"] = $streakArr;

        echoResponse(200, $response);
    }
});*/


$app->run();
