<?php
	
	include("dbconnect.php");

	include("functions/login_functions.php");
	include("functions/commonfunctions.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}

	$rarr = array('status' => 0);

	if (array_key_exists("token", $r)){
		// authenticate with token
		if ( tokenvalid($r['id'], $r['token']) ){
			$token = generateToken();
			$res = setToken($r['id'], $token);
			if ($res){
				$res = execQuery("select * from eyeds where id={$r['id']}");
				$userarr = mysqli_fetch_assoc($res);
				unset($userarr['phash']);
				$rarr['token'] = $token;
				die( json_encode(array_merge($rarr, $userarr)) );
			} else
				makeError(4);
		} else
			makeError(2);

	} else if (array_key_exists("password", $r)){
		// login with password
		$queryuser = "select * from eyeds where username='{$r['username']}' AND phash='{$r['password']}'";
		$resultuser = mysqli_query($con, $queryuser);
		if ( mysqli_num_rows($resultuser) > 0 ){ // pass correct
			// ALL USER DATA
			$userarr = mysqli_fetch_assoc( $resultuser );
			unset($userarr['phash']);
			$randstr = generateToken();
			$result = setToken($userarr['id'], $randstr);

			if ($result){
				$rarr['token'] = $randstr;
				$rarr = array_merge($rarr, $userarr);
				die( json_encode($rarr) );
			} else
				makeError(2); // db err
		} else
			makeError(4); // wrong cred

	} else {
		makeError(1); // wrong option
	}


	function setToken($id, $token){
		global $con;
		$query = "select * from auths where id='{$id}'";
		$result = mysqli_query($con, $query);
		
		if ( mysqli_num_rows($result) > 0 ){ // already token
			$query = "update auths set token='{$token}' where id='{$id}'";
			$result = mysqli_query($con, $query);
		} else { // new token
			$query = "insert into auths values ('{$id}', '{$token}')";
			$result = mysqli_query($con, $query);
		}
		return $result;
	}

?>