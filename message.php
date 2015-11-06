<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}

	$rarr = array('status' => 0);

	if ( $r['action'] == 'send' ){
		// send message
		if ( tokenvalid($r['id'], $r['token']) ){
			$sqlIns = makeSQLInsert($r);
			$query = "insert into msgs ( {$sqlIns['cols']},"
			. "username,"
			. "username_rec,"
			. "doc )"
			. " values ( {$sqlIns['vals']},"
			. "'" . getUsername($r['id']) . "',"
			. "'" . getUsername($r['recid']) . "',"
			. "'" . date('Y-m-d H:i:s') . "')";
			$result = mysqli_query($con, $query);
			if ($result){
				die(json_encode($rarr));
			} else {
				makeError(5);
			}
		} else {
			makeError(3);
		}
	} else if ( $r['action'] == 'feed' ){
		// get feed
		if ( tokenvalid($r['id'], $r['token']) ){
			$query = "select * from msgs where recid={$r['id']} or id={$r['id']} order by msgid desc limit 20";
			$result = mysqli_query($con, $query);
			$rarr['messages'] = array();
			while ($row = mysqli_fetch_assoc($result)){
				$rarr['messages'][] = $row;
			}
			die (json_encode($rarr));
		} else {
			makeError(3);
		}

	} else {
		makeError(1);
	}
?>