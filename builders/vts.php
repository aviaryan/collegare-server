<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists vts";
	$result = mysqli_query($con, $query);
	if ($result){
		echo "drop table successful";
	} else {
		echo "drop table fail";
	}

	$query = "create table vts ("
		. "id int(5) not null,"
		. "postid int(6) not null,"
		. "vote tinyint(2) not null,"
		. "primary key (id, postid),"
		. "foreign key (id) references eyeds (id) on delete cascade,"
		. "foreign key (postid) references posts (postid) on delete cascade,"
		. "index(id)"
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