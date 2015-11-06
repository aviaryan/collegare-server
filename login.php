<?php
	
	include("dbconnect.php");

	include("functions/login_functions.php");
	include("functions/commonfunctions.php");

	// dis infect
	$username = mysqli_real_escape_string( $con, $_POST["username"] );
	$password = mysqli_real_escape_string( $con, $_POST["password"] );

	$rarr = array('status' => 0);

	$queryuser = "select * from eyeds where username='{$username}' AND phash='{$password}'";
	$resultuser = mysqli_query($con, $queryuser);
	if ( mysqli_num_rows($resultuser) > 0 ){ // pass correct

		// ALL USER DATA
		$userarr = mysqli_fetch_assoc( $resultuser );
		$userid = $userarr['id'];
		$query = "select * from auths where id='{$userid}'";
		$result = mysqli_query($con, $query);
		$randstr = generateToken();
		
		if ( mysqli_num_rows($result) > 0 ){ // already token
			$query = "update auths set token='{$randstr}' where id='{$userid}'";
			$result = mysqli_query($con, $query);
		} else { // create token
			$query = "insert into auths values ('{$userid}', '{$randstr}')";
			$result = mysqli_query($con, $query);
		}

		if ($result){
			$rarr['token'] = $randstr;
			$rarr = array_merge($rarr, $userarr);
			die( json_encode($rarr) );
		} else {
			makeError(2);
		}

	} else {
		makeError(4);
	}


?>