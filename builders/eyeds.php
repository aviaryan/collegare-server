<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists eyeds";
	$result = mysqli_query($con, $query);
	if (!$result){
		echo "drop table fail";
	}

	$query = "create table eyeds ("
		. "firstname Varchar(30) not null ,"
		. "lastname Varchar(30) ,"
		. "username Varchar(30) Unique not null ,"
		. "email Varchar(50) NOT NULL ,"
		. "sex Varchar(1) ,"
		. "groups Varchar(200) ,"
		. "dob date ,"
		. "adminof Varchar(200) ,"
		. "id INT(5) auto_increment primary key,"
		. "phash varchar(65),"
		
		. "index (username)"
		. ") ENGINE=InnoDB";
	
	$result = mysqli_query($con, $query);
	if ($result){
		echo "Success";
	} else {
		echo "Fail";
		echo $query;
		echo mysqli_error($con);
	}
?>