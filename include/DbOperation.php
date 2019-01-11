<?php
 
class DbOperation
{
    //Database connection link
    private $con;
 
    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';
 
        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();
 
        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();
    }

    /****** Method will create a new streak ******/
    public function createstreak($name,$image,$video,$created_at,$updated_at){

        $streakdata = array();
       
        //Crating an statement
        $stmt = $this->con->prepare("INSERT INTO streak(streak_name,created_at,updated_at) values(?, ?, ?)");

        //Binding the parameters
        $stmt->bind_param("sii", $name, $created_at, $updated_at);

        //Executing the statment
        $result = $stmt->execute();

        $streakId = $stmt->insert_id;

        //Closing the statment
        $stmt->close();

        //If statment executed successfully
        if ($result) {

              $owner = 1;
              //Crating an statement
              $stmt1 = $this->con->prepare("INSERT INTO streak_videos(video_streakid, video_name, image_name, created_at, updated_at) values(?, ?, ?, ?, ?)");
              //Binding the parameters
              $stmt1->bind_param("issii", $streakId, $video, $image, $created_at, $updated_at);
              //Executing the statment
              $result1 = $stmt1->execute();
   
              //Closing the statment
              $stmt1->close();

            //Returning 0 means streak inserted successfully
            //return 0;

            $streakdata['streakid']  = $streakId;
            $streakdata['videoname'] = $video;
            $streakdata['imagename'] = $image;

            return $streakdata;

        } else {
            //Returning 1 means failed to create streak
            //return 1;
          return $streakdata;
        }

    } //createstreak

    /****** Method will append a video to existing streak ******/
    public function appendtostreak($id,$image,$video,$created_at,$updated_at){

        $streakdata = array();
       
          $owner = 1;
          //Crating an statement
          $stmt1 = $this->con->prepare("INSERT INTO streak_videos(video_streakid, video_name, image_name, created_at, updated_at) values(?, ?, ?, ?, ?)");
          //Binding the parameters
          $stmt1->bind_param("issii", $id, $video, $image, $created_at, $updated_at);
          //Executing the statment
          $result1 = $stmt1->execute();

          //Closing the statment
          $stmt1->close();

          $streakdata['streakid']  = $id;
          $streakdata['videoname'] = $video;
          $streakdata['imagename'] = $image;
           
        if ($result1) {
           return $streakdata;
        } else {
          return $streakdata;
        }

    } //createstreak

    //This method will return streak videos
    public function getStreakVideos($streakId){

        if( $streakId != '' ){

          $parameters = array();
          $arr_results = array();           

          //$stmt = $this->con->prepare('SELECT video_id as id,video_name as name FROM streak_videos WHERE video_streakid=?');
          //$stmt = $this->con->prepare('SELECT s.streak_name as sname, v.video_id as id, v.video_name as name FROM streak s LEFT JOIN streak_videos v ON s.streak_id = v.video_streakid  WHERE video_streakid=?');
          $stmt = $this->con->prepare('SELECT s.streak_name as sname, v.video_name as name, v.image_name as thumb, s.video_merged_path as finalStreak, s.state as state FROM streak s LEFT JOIN streak_videos v ON s.streak_id = v.video_streakid  WHERE video_streakid=?');
          $stmt->bind_param("i", $streakId);
          $stmt->execute();

          $meta = $stmt->result_metadata();

          while ( $rows = $meta->fetch_field() ) {
           $parameters[] = &$row[$rows->name]; 
          }
          //echo"<pre>";print_r($parameters);

          call_user_func_array(array($stmt, 'bind_result'), $parameters);

          while ( $stmt->fetch() ) {
            $x = array();
            $streakname = '';
            foreach( $row as $key => $val ) {

               if($key == 'name'){
                  $x['videopath'] = HOST.$val;
                  //$x = HOST.$val;
               }if($key == 'thumb'){

                  if($val == null || $val == 'null'){
                    $x['thumbnailpath'] = HOST_THUMB.'noImage.png';
                  }else{
                    $x['thumbnailpath'] = HOST_THUMB.$val;
                  }

                  
                  //$x = HOST.$val;
               }elseif($key == 'sname' ){
                  $streakname = $val;
               }elseif($key == 'finalStreak'){
                  //echo $val;
                  if($val === ''){
                    $merge_video_name='';
                  }else{
                    $merge_video_name = HOST.$val;
                  }
               }elseif($key == 'state'){
                  $state = $val;
               }

            }
            $arr_results['vidarr'][] = $x;
            $arr_results['streak'] = $streakname;
            $arr_results['mergeVideo'] = $merge_video_name;
            $arr_results['state'] = $state;
          } //while

        }else{              
               $arr_results = array();                      
        }

        //returning the streak
        return $arr_results;
        
    } //getStreak

    // this method will check if STREAK exist in Database
    public function isstreakexist($sid){

    //Creating query
        $stmt1 = $this->con->prepare("SELECT `streak_name` FROM `streak` WHERE `streak_id`= ?");
        //binding the parameters
        $stmt1->bind_param("i",$sid);
        //executing the query
        $stmt1->execute();
        //Storing result
        $stmt1->store_result();
        //Getting the result
        $num_rows = $stmt1->num_rows;
        //closing the statment
        $stmt1->close();
       
        if($num_rows > 0){
          //echo 'parent'; exit;
          return 1;
        }else{
          return 0;
        }

    } //isstreakexist

/************************Check if cresendo exist or not************************/
public function getState($streak_id){
    $stmt1 = $this->con->prepare("SELECT `state` FROM `streak` WHERE `streak_id`= ?");
    // echo"<pre>";print_r($stmt1);exit();
        //binding the parameters
    $stmt1->bind_param("i",$streak_id);
    //executing the query
    $stmt1->execute();
    
    $res = $stmt1->get_result();
    //getting the data in a varibale
    $data = $res->fetch_all();
    //closing the statment
    $stmt1->close();
    //echo"<pre>";print_r($data);exit();

    if(count($data) > 0){
      $state = $data[0][0];
      return $state;
    }else{
      return 0;
    }

}

/*********************** Fetch videos acording to streak ************************/

public function getVideos($streakId){
      $streakdata = array();
      $str = array();

      //fetching all videos path from database
      $stmt = $this->con->prepare('SELECT video_id as vid, video_name as name FROM `streak_videos` WHERE video_streakid = ? ORDER BY video_id ASC');
      // In binding param "i" is used because video_streakid is an integer
      $stmt->bind_param("i", $streakId);
      
      $stmt->execute();
      
      $res = $stmt->get_result();
      //getting the data in a varibale
      $data = $res->fetch_all();
     // echo"<pre>";print_r($data);exit();
      $total_videos = count($data);
      //echo "total_videos------>".$total_videos;exit();
      $stmt->close();

      if($total_videos){ 
        for($i=0; $i<$total_videos; $i++){        
          $str[] = '-i /var/www/html/uploads/'.$data[$i][1];
        }
        //echo"<pre>";print_r($str);exit();
        $streakdata['count'] = $total_videos;
        $streakdata['string'] = implode(' ', $str);
        //echo"<pre>";print_r($streakdata);exit();
        return $streakdata;
      }else{
        return $streakdata;
      }
       //echo"<pre>";print_r($str);
       //exit();
}
/********************** save merge path to database ******************************/
  public function saveMergePath($id, $video){
    $streakdata = array();

    $stmt = $this->con->prepare('UPDATE streak SET video_merged_path=? WHERE streak_id=?');

    $stmt->bind_param("si", $video, $id);
    //Executing the statment
    $result = $stmt->execute();
    //Closing the statment
    $stmt->close();

    if ($result) {
      //add final video path and streak_id to an array
      $streakdata['id']  = $id;
      $streakdata['video']  = HOST.$video;

      return $streakdata;
    } else {
      return $streakdata;
    }
  }

/************************************** save devicetoken to database ******************************/

  public function updateStreakData($devToken,$id){
    $streakdata = array();
    //echo $id.' -> '.$video;exit();
    $stmt = $this->con->prepare('UPDATE streak SET deviceToken =? WHERE streak_id=?');

    $stmt->bind_param("si", $devToken, $id);
    //Executing the statment
    $result = $stmt->execute();
    //Closing the statment
    $stmt->close();

    if ($result) {
      $streakdata['id']  = (int)$id;
      //echo $streakdata['video'];exit();
      return $streakdata;
    } else {
      return $streakdata;
    }
  }
/************************************ end Streak ********************************************/

public function endStreak($id){
  $streakdata = array();
    //echo $id.' -> '.$video;exit();
    $stmt = $this->con->prepare('UPDATE streak SET state =1 WHERE streak_id=?');

    $stmt->bind_param("i", $id);
    //Executing the statment
    $result = $stmt->execute();
    //Closing the statment
    $stmt->close();

    if ($result) {
        $streakdata['id'] = (int)$id;
        return $streakdata;
    } else {
      return $streakdata;
    }
    //echo"<pre>";print_r($streakdata);exit();
}
/************************************ Update Streak Name********************************************/
public function updateStreakName($streak_name, $streak_id){
    $streakdata = array();
    $arraydata = array();
    //echo $id.' -> '.$video;exit();
    $stmt = $this->con->prepare('UPDATE streak SET streak_name=? WHERE streak_id=?');

    $stmt->bind_param("si", $streak_name,$streak_id);
    //Executing the statment
    $result = $stmt->execute();
    //Closing the statment
    $stmt->close();

    //check if result is 1 or not
    if($result){
      //add streak_id and streak_name to array streakdata declared above
      $streakdata['id'] = (int)$streak_id;
      $streakdata['name'] = $streak_name;

      return $streakdata;
    }else{
     return $streakdata;
    }
}

/*********************function for adding usersinto database*****************************/
//DEMO for Adding users into database

    /*public function insertuser($name, $email){
        $streakdata = array();

        $stmt1 = $this->con->prepare("INSERT INTO users(name, email) values(?, ?)");
       //Binding the parameters
        $stmt1->bind_param("ss", $name, $email);
        //Executing the statment
        $result1 = $stmt1->execute();

        $result = $stmt1->insert_id;
        //Closing the statment
        $stmt1->close();

        if ($result) {
            $streakdata['id']  = $result;
            $str = explode('@', $email);
            $streakdata['name']  = $name;
            $streakdata['email'] = $str[0];

            return $streakdata;
        } else {
          return $streakdata;
        }
    }*/

 } //end of class