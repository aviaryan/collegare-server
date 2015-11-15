<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists cmnts";
	$result = mysqli_query($con, $query);
	if ($result){
		echo "drop table successful";
	} else {
		echo "drop table fail";
	}

	$query = "create table cmnts ("
		. "commentid INT(9) auto_increment primary key,"
		. "postid INT(6),"
		. "content varchar(1000),"
		. "id INT(5),"
		. "doc timestamp,"
		. "username varchar(30),"

		. "index (postid)"
		// no need for id for now
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