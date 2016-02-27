<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");

	$r = array();
	foreach ($_GET as $key => $value) {
		if ($key == 'post' || $key == 'posts'){
			echo "Deleted posts";
			var_dump(execQuery("delete from posts"));
		} else if ($key == 'messages' || $key == 'message'){
			echo "Deleted messages";
			var_dump(execQuery("delete from msgs"));
		}
	}
?>