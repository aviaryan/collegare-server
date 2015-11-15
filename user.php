<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}

	$rarr = array('status' => 0);

	if ($r['action'] == 'get'){
		// GET USER INFO
		$res = execQuery("select * from eyeds where username='{$r['username']}'", 6);
		$row = mysqli_fetch_assoc($res);
		unset($row['phash']);
		$res = execQuery("select (sum(upcount)*2)-(sum(downcount)*3) from posts where id={$row['id']}"); // 2/3 ratio
		$row['holiness'] = mysqli_fetch_row($res)[0];
		die( json_encode( array_merge($rarr, $row)) );
	}
?>