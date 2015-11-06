<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists auths";
	$result = mysqli_query($con, $query);
	if ($result){
		echo "drop table successful";
	} else {
		echo "drop table fail";
	}

	$query = "create table auths ("
		. "id int(5) PRIMARY KEY,"
		. "token char(100) unique "
		. ")";
	
	echo $query;
	$result = mysqli_query($con, $query);
	if ($result){
		echo "Success";
	} else {
		echo "Fail";
		echo mysqli_error($con);
	}
?>