<?php
	global $con;
	
	$dbhost = "localhost";
	$dbuser = "DBUSER";
	$dbpass = "DBPASSWORD";
	$dbname = "DBNAME";

	$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	if (mysqli_connect_errno()){
		die("Database connection failed" . mysqli_connect_error());
	}
?>