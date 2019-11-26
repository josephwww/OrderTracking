<?php
/*
PHP file for creating new submission
Params:
	$_POST['new_submission']:the form input to make sure the new submission is created through http request
	$_GET['deliverable_id']:passing the deliverable_id
	$_GET['employee_id']:passing the employee_id
*/
include 'func.php';
if(!isset($_POST['new_submission'])) die("illegal");
$deliverable=$_GET['deliverable_id'];
$employee=$_GET['employee_id'];
$submission=getNewSubmissionID($deliverable,$employee);
header("location: milestone.php?deliverable_id=$deliverable&employee_id=$employee&submission_id=$submission");
?>