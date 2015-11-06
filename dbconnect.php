<?php
	global $con;
	
	$dbhost = "localhost";
	$dbuser = "DB_USER";
	$dbpass = "DB_PASSWORD";
	$dbname = "DB_NAME";

	$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	if (mysqli_connect_errno()){
		die("Database connection failed" . mysqli_connect_error());
	}
?>