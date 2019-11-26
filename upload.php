<?php
        include "func.php";
        session_start();
        date_default_timezone_set('Australia/Perth');

        global $FILE_MAX_SIZE, $UPLOAD_FOLDER;
        $UPLOAD_FOLDER= "./uploads/";
        $FILE_MAX_SIZE = 51547644;
        // Connect to DB
        $deliverable_id=$_POST['deliverable_id'];
        $employee_id = $_POST['employee_id'];
        $submission_id=$_POST['submission_id'];
        $username = $_SESSION['login_user'];     
        if(!$username) { http_response_code(401); die("bad user"); }

        $conn = getConn();
        if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 
        $db = mysqli_select_db($conn, $dbname); //activating the hourglass database
        /* File Storage Path */
        $fileStore = $UPLOAD_FOLDER;
        chdir($fileStore);
        $fileID;
        // print_r($_FILES);
        foreach ($_FILES["file"]["error"] as $key => $error)
        {
            if(!($error == UPLOAD_ERR_OK)) {	http_response_code(405); echo "Failed to upload file.\n";	}
            // File elements
            $file_path=$_FILES["file"]["tmp_name"][$key];
            $file_type=$_FILES["file"]['type'][$key];
            $file_size=$_FILES["file"]['size'][$key];
            $name=$_FILES["file"]['name'][$key];

            $file_name = explode('.', $name)[0];
            $file_ext = strtolower( end( explode('.', $name) ) );

            // Check file details
            $file = mysqli_real_escape_string($conn, file_get_contents($file_path));
            /* Check file name	*/	if($file_name == ""){ http_response_code(400); die( "File Name not detected"); return; }
            /* Check filesize	*/	if($file_size >= $FILE_MAX_SIZE) { http_response_code(400); die( "File Exceeded maximum size"); return; }
            /* Check fileytype	*/ $allowed = array( 'png', 'pdf', 'jpg', 'jpeg' );
            if(false)	{	if( !in_array($file_ext, $allowed) ){ http_response_code(400); die("Bad file extension"); }	}

            $fp = end( explode("/", $file_path) );
            $i = 0; $filepath = $fp;
            while( file_exists( $filepath ) ) { $filepath = $fp."_".$i++; }
            $fp = $fp.".".$file_ext;
            // die(getcwd() );

            // Upload File
            // $filesteam = fopen( $filepath, "w" );
            // echo "Filepath: ".$filepath;

            move_uploaded_file($file_path, $fp);
            // print_r($fp);

            // Get File Id
            $query = "SELECT MAX(fileId) FROM files";
            $result = mysqli_query($conn, $query); if($result === false ) { http_response_code(500); die($query."\nError description: " . mysqli_error($conn)); }
            $fileID = 1;
            if( mysqli_num_rows($result) == 0 ){ $fileID=1; }
            else{ $fileID = mysqli_fetch_row($result)[0] + 1; }
            $query = "INSERT INTO files( fileId, filename, filepath, filetype, uploadedBy, dateUploaded ) VALUES ( $fileID, '".$file_name."', '".$filepath."', '".$file_type."', '$username', '".date("Y-m-d")."' )";
            $result = mysqli_query($conn, $query); 
            if($result === false ) { http_response_code(500); die($query."\nError description: " . mysqli_error($conn)); }
            $query = "SELECT submissionID from files AS f JOIN submissions AS s ON f.fileId=s.FileID WHERE deliverableID=$deliverable_id AND employeeID='$employee_id' AND filename='$file_name'";
            $result = mysqli_query($conn, $query);
            $toUpdate = array();
            if(mysqli_num_rows($result) >0) {
                while($row = mysqli_fetch_row($result)) {
                    array_push($toUpdate,$row[0]);
//                    $query = "UPDATE submissions SET latest=latest+1 WHERE submissionId=$sID";
//                    $result = mysqli_query($conn, $query); if($result === false ) { http_response_code(500); 
                    }
                }
                
                for($i=0;$i<sizeof($toUpdate);$i++) {
                    $query = "UPDATE submissions SET latest=latest+1 WHERE submissionId=$toUpdate[$i]";
                    $result = mysqli_query($conn, $query); if($result === false ) { http_response_code(500); 
                }
            }        
            $timestamp = date("Y-m-d H:i:s");
            $query = "UPDATE submissions SET fileID=$fileID,uploadTime='$timestamp',latest=1 WHERE submissionID=$submission_id";
            $result = mysqli_query($conn, $query); if($result === false ) { http_response_code(500); die($query."\nError description: " . mysqli_error($conn)); }
        }
        http_response_code(200);
        header("Location:milestone.php?deliverable_id=$deliverable_id&employee_id=$employee_id&submission_id=$submission_id");
        die(json_encode($fileID));

?>