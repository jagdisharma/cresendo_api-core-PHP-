<?php
	error_reporting();
	ini_set('display_errors',1);

	//shell_exec( "ffmpeg/ffmpeg -f concat -safe 0 -i videos/concatenate.txt -c copy mergeurl/mergesvideos.mp4" );
	//exec('mkdir /var/www/html/testdemo');
	// echo "<h1>Start</h1>";

	// $mergefilename = "mergevid/mergephp23.mp4";

	// exec("ffmpeg/ffmpeg -i videos/1.mp4 -i videos/2.mp4 -i videos/3.mp4 -i videos/4.mp4 -filter_complex '[0]scale=640x640,setdar=16/9[a];[1]scale=640x640,setdar=16/9[b];[2]scale=640x640,setdar=16/9[c];[3]scale=640x640,setdar=16/9[d]; [a][0:a][b][1:a][c][2:a][d][3:a]  concat=n=4:v=1:a=1[v][a]' -map '[v]' -map '[a]' -strict -2 $mergefilename");


	$videos = "-i videos/1.mp4 -i videos/2.mp4 -i videos/3.mp4 -i videos/8.mov";

	$mergefilename = 'mergevid/merge_mov_and_mp4.mp4';

	// if (file_exists($mergefilename)) {
	//     echo "<h2>The file $mergefilename already exists.</h2>";
	// } else {
	    exec("ffmpeg -i /var/www/html/uploads/streak-154117773315.mp4 -i /var/www/html/uploads/streak-15411780718.mp4 -i /var/www/html/uploads/streak-154117839330.mp4 -i /var/www/html/uploads/streak-154117900335.mp4 -i /var/www/html/uploads/streak-154117933583.mp4 -i /var/www/html/uploads/streak-154118064513.mp4 -i /var/www/html/uploads/streak-154118086742.mp4 -i /var/www/html/uploads/streak-154118204796.mp4 -filter_complex '[0]scale=640x640,setdar=16/9[a];[1]scale=640x640,setdar=16/9[b];[2]scale=640x640,setdar=16/9[c];[3]scale=640x640,setdar=16/9[d];[4]scale=640x640,setdar=16/9[e];[5]scale=640x640,setdar=16/9[f];[6]scale=640x640,setdar=16/9[g];[7]scale=640x640,setdar=16/9[h]; [a][0:a][b][1:a][c][2:a][d][3:a][e][4:a][f][5:a][g][6:a][h][7:a]  concat=n=8:v=1:a=1[v][a]' -map '[v]' -map '[a]' -strict -2 /var/www/html/merge/merge181.mp4");
	    echo "<h1>Done</h1>";
	//}

	/*ffmpeg -i videos/1.mp4 -i videos/2.mp4 -i videos/3.mp4 -i videos/4.mp4 -i videos/5.mp4 -i videos/6.mp4 -i videos/7.mp4 -i videos/8.mov -filter_complex '[0]scale=640x640,setdar=16/9[a];[1]scale=640x640,setdar=16/9[b];[2]scale=640x640,setdar=16/9[c];[3]scale=640x640,setdar=16/9[d];[4]scale=640x640,setdar=16/9[e];[5]scale=640x640,setdar=16/9[f];[6]scale=640x640,setdar=16/9[g];[7]scale=640x640,setdar=16/9[h]; [a][0:a][b][1:a][c][2:a][d][3:a][e][4:a][f][5:a][g][6:a][h][7:a]  concat=n=8:v=1:a=1[v][a]' -map '[v]' -map '[a]' -strict -2 merge/merge8.mp4



	ffmpeg -i /var/www/html/uploads/streak-154117773315.mp4 -i /var/www/html/uploads/streak-15411780718.mp4 -i /var/www/html/uploads/streak-154117839330.mp4 -i /var/www/html/uploads/streak-154117900335.mp4 -i /var/www/html/uploads/streak-154117933583.mp4 -i /var/www/html/uploads/streak-154118064513.mp4 -i /var/www/html/uploads/streak-154118086742.mp4 -i /var/www/html/uploads/streak-154118204796.mp4  -filter_complex '[0]scale=640x640,setdar=16/9[a];[1]scale=640x640,setdar=16/9[b];[2]scale=640x640,setdar=16/9[c];[3]scale=640x640,setdar=16/9[d];[4]scale=640x640,setdar=16/9[e];[5]scale=640x640,setdar=16/9[f];[6]scale=640x640,setdar=16/9[g];[7]scale=640x640,setdar=16/9[h]; [a][0:a][b][1:a][c][2:a][d][3:a][e][4:a][f][5:a][g][6:a][h][7:a]  concat=n=8:v=1:a=1[v][a]' -map '[v]' -map '[a]' -strict -2 /var/www/html/merge/merge18.mp4


	// exec("ffmpeg -i videos/1.mp4 -i videos/2.mp4 -i videos/3.mp4 -i videos/4.mp4 -filter_complex '[0]scale=640x640,setdar=16/9[a];[1]scale=640x640,setdar=16/9[b];[2]scale=640x640,setdar=16/9[c];[3]scale=640x640,setdar=16/9[d]; [a][0:a][b][1:a][c][2:a][d][3:a]  concat=n=4:v=1:a=1[v][a]' -map '[v]' -map '[a]' -strict -2 mergevid/mergephpnew.mp4");

	//echo shell_exec("which ffmpeg");
	//print_r($result);



/*
 ffmpeg/ffmpeg -i videos/1.mp4 -i videos/2.mp4 -i videos/3.mp4 -i videos/4.mp4 -filter_complex "[0]scale=640x640,setdar=16/9[a];[1]scale=640x640,setdar=16/9[b];[2]scale=640x640,setdar=16/9[c];[3]scale=640x640,setdar=16/9[d]; [a][b][c][d] concat=n=4:v=1" mergeurl/output.mp4  -->

 1. ffmpeg -i videos/1.mp4 -i videos/2.mp4 -i videos/3.mp4 -i videos/4.mp4 -filter_complex '[0]scale=640x640,setdar=16/9[a];[1]scale=640x640,setdar=16/9[b];[2]scale=640x640,setdar=16/9[c];[3]scale=640x640,setdar=16/9[d]; [a][b][c][d] concat=n=4:v=1' -strict -2 mergevid/merge.mp4

combine two videos by split in single screen not audio
2- ffmpeg \
  -i videos/1.mp4 \
  -i videos/2.mp4 \
  -filter_complex '[0:v]pad=iw*2:ih[int];[int][1:v]overlay=W/2:0[vid]' \
  -map [vid] \
  -c:v libx264 \
  -crf 23 \
  -preset veryfast \
  mergevid/outputt.mp4



  3. ffmpeg -i videos/1.mp4 -i videos/1.mp4 -i videos/1.mp4 \
  -filter_complex "[0:v] [0:a] [1:v] [1:a] [2:v] [2:a] concat=n=3:v=1:a=1 [v] [a]" \
  -map "[v]" -map "[a]" -strict -2 mergevid/output3.mp4

// Working with audio video merging

  4. ffmpeg -i videos/1.mp4 -i videos/2.mp4 -i videos/3.mp4 -i videos/4.mp4 -filter_complex '[0]scale=640x640,setdar=16/9[a];[1]scale=640x640,setdar=16/9[b];[2]scale=640x640,setdar=16/9[c];[3]scale=640x640,setdar=16/9[d]; [a][0:a][b][1:a][c][2:a][d][3:a]  concat=n=4:v=1:a=1[v][a]' -map "[v]" -map "[a]" -strict -2 mergevid/merge.mp4  */

?>

