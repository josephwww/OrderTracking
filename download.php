<?php
$filename = basename($_GET['file']);
$realname = $_GET['filename'];
// Specify file path.
$path = './uploads/'; // '/uplods/'

$i=0;
$allowed = array( 'png', 'pdf', 'jpg', 'jpeg' );
for($i=0;$i<4;$i++) {
    $temp_filename = $filename.".".$allowed[$i];
    $download_file =  $path.$temp_filename;
    if(!empty($temp_filename)){
    // Check file is exists on given path.
    if(file_exists($download_file))
    {
      header('Content-Disposition: attachment; filename=' . $realname.".".$allowed[$i]);  
      readfile($download_file); 
      exit;
    }
 }
}
