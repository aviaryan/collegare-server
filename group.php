<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}

	$rarr = array('status' => 0);

	if ($r['action'] == 'create'){


	} else if ($r['action'] == 'add_member'){
		// accept a space separated list of usernames


	} else if ($r['action'] == 'change_role'){



	} else if ($r['action'] == 'kick' || $r['action'] == 'leave'){



	}


?>