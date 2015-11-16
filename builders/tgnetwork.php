<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists gnetwork";
	$result = mysqli_query($con, $query);
	if (!$result){
		echo "drop table fail";
	}

	$query = "create table gnetwork ("
		. "id int(5) not null,"
		. "gid int(4) not null,"
		. "role bit not null,"
		. "primary key (id, gid),"
		
		. "foreign key (id) references eyeds (id) on delete cascade,"
		. "foreign key (gid) references groups (gid) on delete cascade,"

		. "index(id),"
		. "index(gid)"
		. ")";
	
	$result = mysqli_query($con, $query);
	if ($result){
		echo "Success";
	} else {
		echo "Fail";
		echo $query;
		echo mysqli_error($con);
	}
?>