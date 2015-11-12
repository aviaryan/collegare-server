<?php

	function tokenvalid($id, $token){
		global $con;
		$query = "select token from auths where id={$id}";
		$result = mysqli_query($con, $query);
		if ( mysqli_num_rows($result) > 0 ){
			$intoken = mysqli_fetch_row( $result );
			if ($intoken[0] == $token)
				return 1;
			else
				return 0;
		} else {
			return 0;
		}
	}

	function getUsername($id){
		global $con;
		$query = "select username from eyeds where id={$id}";
		$result = mysqli_query($con, $query);
		if ( mysqli_num_rows($result) > 0 ){
			$row = mysqli_fetch_row( $result );
			return $row[0];
		} else {
			return 0;
		}
	}


	function makeError($code){
		global $rarr;
		$rarr['status'] = $code;
		if ($code == 3)
			$rarr['error'] = "Problem while authenticating the user";
		else if ($code == 1)
			$rarr['error'] = "Invalid Option";
		else if ($code == 2)
			$rarr['error'] = "Database connection error";
		else if ($code == 4)
			$rarr['error'] = "Wrong username or password";
		else if ($code == 5)
			$rarr['error'] = "Invalid parameters passed";
		die( json_encode($rarr) );
	}

?>

<?php
	// Language based functions. Independent of the application

	function makeSQLInsert($r){
		$cols = ''; 
		$vals = '';
		foreach ($r as $k => $v) {
			if ($k == 'token' || $k == 'do' || $k == 'action')
				continue;
			$cols .= $k . ',';
			$vals .= "'" . $v . "'" . ",";
		}
		$ret = array('cols' => substr($cols, 0, -1), 'vals' => substr($vals, 0, -1));
		return $ret;
	}

	function StrToArr($str){
		$str = trim($str);
		$tok = strtok($str, ' ');
		$arr = array();
		while ($tok !== false)
			$arr[$tok] = 1;
		return $arr;
	}

	function ArrToLike($arr, $tc=1){
		// if tc is 1, trailing comma is returned
		$s = "";
		foreach ($arr as $k => $v)
			$s .= "'{$k}',";
		if ($tc == 0)
			$s = substr($s, 0, -1);
		return $s;
	}
	
	function execQuery($query, $error=1){
		global $con;
		$result = mysqli_query($con, $query);
		if ($result)
			return $result;
		else
			makeError($error);
	}
?>