<?php
	include("dbconnect.php");
	include("functions/login_functions.php");
	include("DataModel.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}
	$rarr = array('status' => 0);

	/**
	 * Users Class
	 * Interacts with the users table
	 */
	class Users extends DataModel {
		function __construct(){
			parent::__construct();
			$this->tablename = 'eyeds';
		}
	}

	$obj = new Users();
	$obj->addInsertsFromArray($r, ['firstname', 'lastname', 'username', 'phash']);
	$result = $obj->insert(8);
	if ($result){
		die( json_encode($rarr) );
	}
?>