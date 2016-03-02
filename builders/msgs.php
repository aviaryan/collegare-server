<?php

	include("../dbconnect.php");
	
	$query = "drop table if exists msgs";
	$result = mysqli_query($con, $query);
	if (!$result){
		echo "drop table fail";
	}

	$query = "create table msgs ("
		. "msgid INT(6) auto_increment primary key,"
		. "content varchar(1000) not null,"
		. "doc timestamp,"
		. "id INT(5),"
		. "recid INT(5),"

		. "foreign key (id) references eyeds (id) on delete cascade,"
		. "foreign key (recid) references eyeds (id) on delete cascade,"
	
		. "index (id) ,"
		. "index (recid)"
		. ") ENGINE=InnoDB";
	
	$result = mysqli_query($con, $query);
	if ($result){
		echo "Success";
	} else {
		echo $query;
		echo "Fail";
		echo mysqli_error($con);
	}
?>