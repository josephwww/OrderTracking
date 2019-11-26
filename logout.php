<?php 
	header('Content-type:text/html; charset=utf-8');
	// after log out
	session_start();
	// clear Session
	$username = $_SESSION['login_user'];  //for later prompt
	$_SESSION = array();
	session_destroy();

 
	// prompt
	echo "See you ".$username.'<br>';
	echo "<a href='login.html'>Log in</a>";
 
 ?>