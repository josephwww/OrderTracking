<?php
if(session_id() == '') { session_start(); }

function login()
{   
    include "func.php";
	$error=''; // Variable To Store Error Message
	if (!isset($_POST['submit'])) { return; }
    if (empty($_POST['username']) || empty($_POST['password'])) {
            $error = "Username or Password is Invalid";
            header('Location: http://www.sustech.net.au/log-in/?fail=0');
            echo $error;
            return;
    }
    // Define $username and $password
    $username=$_POST['username'];
    $password=$_POST['password'];
    // Establishing Connection with Server by passing server_name, user_id and password as a parameter
	if(verifyPassword($username, $password))
	{
		$conn = getConn();
        $username = stripslashes($username);
        $username = mysqli_real_escape_string($conn, $username);
     //    $_SESSION['login_user'] = mysqli_real_escape_string($conn, stripslashes($username)); // Initializing Session
        $_SESSION['login_user'] = $username; // Initializing Session
        $sql = sprintf("SELECT firstName, isAdmin FROM employees WHERE email='%s'", $username);
        $query = mysqli_query($conn, $sql);
        $rowrow = mysqli_fetch_row($query);
        print_r($rowrow);
        $name = $rowrow[0];
        $isAdmin = $rowrow[1];
        $_SESSION['firstname']=$name;
        $_SESSION['isAdmin']=$isAdmin;
        // echo $name."\n";
        // echo $username."\n";
        // echo $isAdmin."\n";
        //header("location: http://www.sustech.net.au/home-php"); // Redirecting To Other Page
        if($_SESSION['isAdmin']) {
            header("location:TrackingProgress.php");
        }
        else {
            header("location: EmployeeView.php");
        }
        
	} else {
        $error = "Username or Password is invalid.";
        echo $error;
        //header("location: http://www.sustech.net.au/log-in/?fail=1");
    }
}

function verifyPassword($user, $pass)
{ if($user == "" || $pass == "") return false; // Trivial Case

	// Database connection
	// $username = mysqli_real_escape_string($conn, stripslashes($user));
	// $password = mysqli_real_escape_string($conn, stripslashes($pass));
	$servername = "localhost";
    $use = "root";
    $dbpass = "cits3200";
    $dbname = "pot";
    $conn = new mysqli($servername, $use, $dbpass, $dbname);
    if ($conn->connect_error) { echo("Connection failed: " . $conn->connect_error); return false; }
    $db = mysqli_select_db($conn, $dbname);


    // Password verify
	$query = "SELECT user, salt, hash FROM users WHERE user='".$user."'";
	// echo $query."\n";
	$result = mysqli_query($conn, $query);
	if($result===false){ echo "query error"; return false; }
	// echo "Rows: ".mysqli_num_rows($result)."\n";
	if(mysqli_num_rows($result) != 1){ return false; }
	if(mysqli_num_rows($result) > 1) { return false; }
	$row = mysqli_fetch_row($result);
	$user = $row[0];
	$salt = $row[1];
	$check = strtolower($row[2]);

	// echo "salt: ".$salt."\n";

	for($i = 43; $i < 115; $i++)
	{	// Pepper
		$pepper = chr($i);
		$hash =  strtolower(hash('sha512', $pass.$pepper.$salt));
		// $hash =  hash('sha512', $pass.$salt);
		if($hash == $check)
		{
			// echo "\n\tpass: ".$pass."\n\tcheck: ".$check."\n\thash: ".$hash."\n";
			return true;
		}
	}
	return false;
}

login();
?>