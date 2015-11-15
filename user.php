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

	} else if ($r['action'] == 'set'){
		// SET USER PROFILE
		// ONLY SOME FIELDS ARE ALLOWED
		if ( !tokenvalid($r['id'], $r['token']) )
			makeError(3);

		$allowed = array("firstname", "lastname", "username", "sex", "dob");
		$id = $r['id'];

		foreach ($r as $key => $value)
			if (!in_array($key, $allowed)){
				unset($r[$key]);
			}
		$updtStmt = makeSQLUpdate($r);
		if (strlen( trim($updtStmt) ) > 0)
			execQuery("update eyeds set " . $updtStmt . " where id={$id}");
		die( json_encode($rarr) );
	}
?>