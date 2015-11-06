<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists eyeds";
	$result = mysqli_query($con, $query);
	if ($result){
		echo "drop table successful";
	} else {
		echo "drop table fail";
	}

	$query = "create table eyeds ("
		. "firstname Varchar(30) ,"
		. "lastname Varchar(30) ,"
		. "username Varchar(30) Unique ,"
		. "email Varchar(50) NOT NULL ,"
		. "sex Varchar(1) ,"
		. "groups Varchar(200) ,"
		. "dob date ,"
		. "adminof Varchar(200) ,"
		. "id INT(5) auto_increment primary key,"
		. "phash varchar(65)"
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