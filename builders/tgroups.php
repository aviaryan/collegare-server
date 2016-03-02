<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists groups";
	$result = mysqli_query($con, $query);
	if (!$result){
		echo "drop table fail";
	}

	$query = "create table groups ("
		. "name Varchar(50) not null ,"
		. "gusername Varchar(30) Unique not null ,"
		. "bio Varchar(500) ,"
		. "gid INT(4) auto_increment primary key"
		
		. ") ENGINE=InnoDB";
	
	$result = mysqli_query($con, $query);

	if ($result){
		echo "Success";
		$result = mysqli_query($con, "insert into groups (name, gusername) values ('default', 'default')");
		if (!$result)
			echo "default group (1) creation failed";
	} else {
		echo "Fail";
		echo $query;
		echo mysqli_error($con);
	}
?>