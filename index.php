<?php 
	header('Content-type:text/html; charset=utf-8');
	// open Session
	session_start();
    
	if (isset($_SESSION['login_user'])) {
		if($_SESSION['isAdmin']) {
            header("location:TrackingProgress.php");
        }
        else {
            header("location: EmployeeView.php");
        }

	} else {
		// if notlogged in
		echo "if you have not logged in, please <a href='login.html'>log in</a>";
	}
 ?>