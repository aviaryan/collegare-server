<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists posts";
	$result = mysqli_query($con, $query);
	if ($result){
		echo "drop table successful";
	} else {
		echo "drop table fail";
	}

	$query = "create table posts ("
		. "postid INT(9) auto_increment primary key,"
		. "content varchar(1000) ,"
		. "doc timestamp ,"
		. "id INT(5) ,"
		. "username varchar(30) ,"
		. "groupid INT(5) ,"
		. "weight INT(11) ,"
		. "pollid INT(6) ,"
		. "upvotecount INT(5) default 0,"
		. "downvotecount INT(5) default 0,"
		. "commentcount INT(5) default 0,"

		. "index (id) ,"
		. "index (groupid)"
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