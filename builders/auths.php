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
		. "id int(5) unique,"
		. "token char(100) unique,"

		. "foreign key (id) references eyeds (id) on delete cascade,"
		. "index (id)"
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