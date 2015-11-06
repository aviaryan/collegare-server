<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists msgs";
	$result = mysqli_query($con, $query);
	if ($result){
		echo "drop table successful";
	} else {
		echo "drop table fail";
	}

	$query = "create table msgs ("
		. "msgid INT(6) auto_increment primary key,"
		. "content varchar(1000),"
		. "doc timestamp,"
		. "id INT(5),"
		. "recid INT(5),"
		. "username varchar(30),"
		. "username_rec varchar(30),"

		. "index (id) ,"
		. "index (recid)"
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