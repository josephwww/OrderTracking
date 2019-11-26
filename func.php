<?php
/*
        function:
            getConn()
        Input:
        Output:
            $conn databse connection object
        Return:
            <object>
		Note:
			for database connection
*/
function getConn() {
    $servername = "localhost";/* YOUR SERVER ADDRESS HERE */
    $use = "root";/* YOUR DATABASE USERNAME */
    $pass = "cits3200";/* YOUR DATABASE PASSWORD HERE */
    $dbname = "pot";/* YOUR DATABASE NAME HERE*/
    $conn = new mysqli($servername, $use, $pass, $dbname); if ($conn->connect_error) { return false; } 
    $db = mysqli_select_db($conn, $dbname);
    return $conn;
}

/*
        function:
            isRelevant(<string>,<int>)
        Input:
            $username: current session user
            $deliverable_id: an integer of current deliverable id for this page
        Output:
            true if employee work on this milestone
            false if employee doesnot work on this milestone
        Return:
            <boolean>
*/
function isRelevant($username,$deliverable_id) {
    $conn = getConn();
    //$dbname = "pot";
    //if ($conn->connect_error) { echo("Connection failed: " . $conn->connect_error); return false; }
    //$db = mysqli_select_db($conn, $dbname);
    $query = "SELECT deliverableID,employeeID FROM submissions WHERE deliverableID='".$deliverable_id."'";
    $result = mysqli_query($conn, $query);
    if($result===false){ echo "query error"; return false; }
    // echo "Rows: ".mysqli_num_rows($result)."\n";
    if(mysqli_num_rows($result) < 1) { return false; }
    while($row = mysqli_fetch_row($result)) {
        //terminate when relevant employee found
        $employeeID = $row[1];
        if($employeeID==$username) return true;
    }
    return false;
}

/*
        function:
            getDeliverableTitle(<int>,<string>)
        Input:
            $deliverable_id: an integer of current deliverable id for this page
			$employee_id: an employee email of current deliverable
        Output:
            html code for milstone communication page's title			
*/
function getDeliverableTitle($deliverable_id,$employee_id) {
    $conn = getConn();
    $query = "SELECT name from deliverables where deliverableID='".$deliverable_id."'";
    $result = mysqli_query($conn, $query);
    if($result===false){ echo "query error"; return false; }
    $row = mysqli_fetch_row($result);
    $msName = $row[0];
    echo "Milestone:".$msName."<br>";
    echo "Worker:";
    $query = "SELECT firstname,surname FROM employees where email='".$employee_id."'";
    $result = mysqli_query($conn, $query);
    if($result===false){ echo "query error"; return false; }
    $num = 0;
    $row = mysqli_fetch_assoc($result);
    $firstname = $row["firstname"];
    $lastname = $row["surname"];
    echo $firstname." ".$lastname;
    //mysqli_close($conn);
    }

/*
        function:
            getTextarea(<int>,<string>)
        Input:
            $deliverable_id: an integer of current deliverable id for this page
			$employee_id: an employee email of current deliverable
        Output:
            The submitted text within the deliverableAssigned table
*/
function getTextarea($deliverable_id, $employee_id, $submission_id) {
    $conn = getConn();
    $query = "SELECT submitted_text from submissions where deliverableID='$deliverable_id' and employeeID='$employee_id' and submissionID='$submission_id'";
    $result = $conn->query($query);
    $row = mysqli_fetch_assoc($result);
    $text = $row["submitted_text"];
    echo $text;
    mysqli_close($conn);
}

/*
        function:
            getFileTable(<int>,<string>)
        Input:
            $deliverable_id: an integer of current deliverable id for this page
			$employee_id: an employee email of current deliverable
        Output:
            The files for this deliverable
*/
function getFileTable($deliverableID,$employeeID,$submissionID) {
    $conn = getConn();  
    $query = "SELECT f.filename,f.filepath FROM submissions AS s JOIN files AS f ON s.fileID=f.fileId WHERE submissionID=$submissionID";

    $result = mysqli_query($conn, $query);
    if($result===false){ echo "query error"; return false; }
    // echo "Rows: ".mysqli_num_rows($result)."\n";
    if(mysqli_num_rows($result) < 1) 
    { echo 
        "<tr>
            <td colspan=\"2\">no file</td>
         </tr>"; }
    else {
        while($row = mysqli_fetch_assoc($result)) {
            //showing files in each row
            $filename=scrapeOutEscapes($conn, $row["filename"]);
            $filepath=scrapeOutEscapes($conn, $row["filepath"]);
            echo "<tr>
                    <td id=\"file\">".$filename."</td>
                    <td><v-btn small dark color=\"#6495ED\" href=\"/download.php?file=".$filepath."&filename=".$filename."\">View</v-btn></td>
                    </tr>";
        }
    }

 }
/*
        function:
            getTotalTime(<int>)
        Input:
            $deliverable_id: an integer of current deliverable id for this page
        Output:
            The total time for this deliverable
*/
function getTotalTime($deliverable_id) {
    $conn = getConn();  
    $query = "SELECT totalHours FROM jobs AS j, deliverables AS d WHERE d.deliverableID='$deliverable_id' AND d.jobID=j.JobNumber";
    $result = mysqli_query($conn, $query);
    if($result===false){ echo "query error"; return false; }
    $row = mysqli_fetch_assoc($result);
    $time = $row["totalHours"];
    mysqli_close($conn);
    if($time>1) return $time." Hours";
    else return $time." Hour";
}
/*
        function:
            getProgress(<int>)
        Input:
            $deliverable_id: an integer of current deliverable id for this page
        Output:
            The progress percentage for this deliverable
*/
function getProgress($deliverable_id) {
    $conn = getConn();  
    $query = "SELECT percentCompleted FROM deliverables WHERE deliverableID='$deliverable_id'";
    $result = mysqli_query($conn, $query);
    if($result===false){ echo "query error"; return false; }
    $row = mysqli_fetch_assoc($result);
    $percentage = $row["percentCompleted"];
    mysqli_close($conn);
    echo $percentage;
}

/*
    function:
        getDeliverableName(<int>)
    Input:
        $deliverable_id: an integer of current deliverable id for this page
    Return:
        The deliverable name with echo
*/
function getDeliverableName($deliverable_id) {
    $conn = getConn();
    $query = "SELECT name from deliverables where deliverableID='".$deliverable_id."'";
    $result = mysqli_query($conn, $query);
    if($result===false){ echo "query error"; return false; }
    $row = mysqli_fetch_row($result);
    $msName = $row[0];
    echo $msName;
    mysqli_close($conn);
}

/*
    function:
        getSubmissionTable(<int>,<String>,<int>)
    Input:
        $deliverable_id: an integer of current deliverable id for this page
        $employee_id: a string of current employee id for this page
        $history:whether it is for the history table,1 for ture, 0for false
    Return:
        The submission table body
*/
function getSubmissionTable($deliverable_id,$employee_id,$history) {
    $conn=getConn();
    $sql="SELECT firstname,surname FROM employees WHERE email = '".$employee_id."'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_array($result);
    $employee_name=$row["firstname"];
    $sql="SELECT s.employeeID,f.filename, s.submitted_text,s.latest,s.submissionID,s.uploadTime from submissions AS s left join files AS f ON s.fileID=f.fileId WHERE deliverableID=$deliverable_id";
    $result = mysqli_query($conn,$sql);
    if (mysqli_num_rows($result) == 0) {
        $width=$history+4;
        echo "<tr><td colspan=\"$width\">No Submission!</td></tr>"; 
    }
    //within in the while loop, by whether is the history table, two seperate tables
    while($row = mysqli_fetch_array($result)) {
        if($history==0&&$row['latest']<=1) {//latest submission
            $submission = $row['submissionID'];
            echo "<tr>";
            echo "<td>" . $row['employeeID']. "</td>";
            echo "<td>" . $row['filename'] . "</td>";
            if(strlen($row['submitted_text'])<10)
            echo "<td>" . $row['submitted_text'] . "</td>";
            else
                echo "<td title=\"".$row['submitted_text']."\">" . substr($row['submitted_text'],0,11) . "...</td>";
            if($_SESSION['isAdmin']||strcmp($row['employeeID'],$employee_id)==0) {
                echo "<td><v-btn small dark color=\"#6495ED\" href=\"milestone.php/?deliverable_id=$deliverable_id&employee_id=$employee_id&submission_id=$submission\">Revise</v-btn></td>";
            }
            else {
                echo "<td><v-btn small dark color=\"#6495ED\" href=\"milestone.php/?deliverable_id=$deliverable_id&employee_id=".$row['employeeID']."&submission_id=$submission\">View</v-btn></td>";
            };
            echo "</tr>";     
        }
        if($history==1&&$row['latest']>1){//submission history
            $submission = $row['submissionID'];
            echo "<tr>";
            echo "<td>" . $row['employeeID']. "</td>";
            echo "<td>" . $row['uploadTime'] . "</td>";
            echo "<td>" . $row['filename'] . "</td>";
            if(strlen($row['submitted_text'])<10)
                echo "<td>" . $row['submitted_text'] . "</td>";
            else
                echo "<td title=\"".$row['submitted_text']."\">" . substr($row['submitted_text'],0,11) . "...</td>";
            if($_SESSION['isAdmin']||strcmp($row['employeeID'],$employee_id)==0) {
            echo "<td><v-btn small dark color=\"#6495ED\" href=\"milestone.php/?deliverable_id=$deliverable_id&employee_id=$employee_id&submission_id=$submission\">View</v-btn></td>";
            }
            else echo "<td></td>";
            echo "</tr>";   
        }
          
    }
    mysqli_close($conn);

}
/*
    function:
        getNewSubmissionID(<int>,<String>)
    Input:
        $deliverable_id: an integer of current deliverable id for this page
        $employee_id: a string of current employee id for this page
    Return:
        The new created submission Id
*/
function getNewSubmissionID($deliverable_id,$employee_id){
    $conn = getConn();
    $sql="INSERT INTO submissions VALUES (null,$deliverable_id,'$employee_id',null,null,null,null,null)";
    if ($conn->query($sql) === FALSE) {
        die("Error: " . $sql . "<br>" . $conn->error);
    }
    $sql = "SELECT max(submissionID) AS maxID from submissions";
    $result = mysqli_query($conn, $sql);
     if($result===false){ echo "query error"; return false; }
    $row = mysqli_fetch_row($result);
    $maxID=$row[0];
    $conn->close();
    return $maxID;
}
function scrapeOutEscapes( $conn, $string )
{
    $string = trim( $string );
    $ret = "";
    for( $i = 0; $i < strlen($string); $i++)
    {
        $char = $string[$i];
        if($char == '') { $i++; }
        else {  $ret = $ret.$char; }
    }
    $string = inputcleaner( $conn, $string);
    return $ret;
}
function inputCleaner($connection, $variable){
    $variable=str_replace( "'" , "" , $variable);
    $variable=stripslashes($variable);
    $variable=mysqli_real_escape_string($connection, $variable);
    return $variable;
}
/*

        function:

            getTaskName(<int>)

        Input:

            $deliverable_id: an integer of current deliverable id for this page


        Output:

            The name of the task related to that deliverableID.

*/
function getTaskName($deliverable_id) {

    $conn = getConn();

    $query = "SELECT T.name from tasks T inner join deliverables  on taskID=T.Number where deliverableID='$deliverable_id'";

    $result = $conn->query($query);

    $row = mysqli_fetch_assoc($result);

    $text = $row["name"];

    echo $text;

}


/*

        function:

            getTextareaTask(<int>)

        Input:

            $deliverable_id: an integer of current deliverable id for this page

        Output:

            The task outline for that deliverable from the deliverables table 

*/
function getTextareaTask($deliverable_id) {

    $conn = getConn();

    $query = "SELECT taskOutline from deliverables where deliverableID='$deliverable_id'";

    $result = $conn->query($query);

    $row = mysqli_fetch_assoc($result);

    $text = $row["taskOutline"];

    echo $text;

}

/*

        function:

            getTimeAllocated(<int>)

        Input:

            $deliverable_id: an integer of current deliverable id for this page

        Output:

            The total hours allocated for that deliverable's task 

*/
function getTimeAllocated($deliverable_id) {

    $conn = getConn();

    $query = "SELECT sum(hours) as hours from tasksAssigned T inner join deliverables D on D.taskID=T.taskID
	where D.deliverableID='$deliverable_id'";

    $result = $conn->query($query);

    $row = mysqli_fetch_assoc($result);

    $text = $row["hours"];

    echo $text;

}


/*

        function:

            getTimeSpent(<int>)

        Input:

            $deliverable_id: an integer of current deliverable id for this page

        Output:

            The amount of hours employees have worked on that deliverableID's task.

*/
function getTimeSpent($deliverable_id) {

    $conn = getConn();

    $query = "SELECT sum(hours) as hours2 from times T inner join deliverables D on D.taskID=T.task where 
	D.deliverableID='$deliverable_id'";

    $result = $conn->query($query);

    $row = mysqli_fetch_assoc($result);

    $text = $row["hours2"];

    echo $text;

}

/*

        function:

            getVariance(<int>)

        Input:

            $deliverable_id: an integer of current deliverable id for this page

        Output:

            The difference between the total hours allocated and total hours worked on that deliverableID's task.

*/
function getVariance($deliverable_id) {
	$conn = getConn();

    $queryA = "SELECT sum(hours) as hours from tasksAssigned T inner join deliverables D on D.taskID=T.taskID
	where D.deliverableID='$deliverable_id'";

    $resultA = $conn->query($queryA);

    $rowA = mysqli_fetch_assoc($resultA);

    $textA = (int)$rowA["hours"];
	
	$queryS = "SELECT sum(hours) as hours2 from times T inner join deliverables D on D.taskID=T.task where 
	D.deliverableID='$deliverable_id'";

    $resultS = $conn->query($queryS);

    $rowS = mysqli_fetch_assoc($resultS);

    $textS = (int)$rowS["hours2"];
	$textV=$textA-$textS;
	
	if($textV<0){
		echo "<font color=\"red\">$textV</font>";
	}
	else{
		echo $textV;
	}

}
?>
