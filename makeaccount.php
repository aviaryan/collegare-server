<?php
	include("dbconnect.php");
	include("functions/login_functions.php");
?>

<?php
	// make params getting dynamic - not required currently but future wants it
	$rarr = array('status' => 0);

	$firstname = mysqli_real_escape_string( $con, $_POST["firstname"] ); 
	$lastname = mysqli_real_escape_string( $con, $_POST["lastname"] ); 
	$username = mysqli_real_escape_string( $con, $_POST["username"] );
	$hash = mysqli_real_escape_string( $con, $_POST["phash"] );

	$query = "select username from eyeds where username='{$username}'";
	$result = mysqli_query($con, $query);
	if (mysqli_num_rows($result)>0){
		$rarr['status'] = 2;
		$rarr['error'] = 'User exists';
		die( json_encode($rarr) );
	}

	$query = "insert into eyeds (firstname,lastname,username,phash) values ('{$firstname}', '{$lastname}', '{$username}', '{$hash}')";
	$result = mysqli_query($con, $query);

	if ($result){
		die( json_encode($rarr) );
	} else {
		$rarr['status'] = 1;
		$rarr['error'] = 'Something wrong happened when registering the user';
		die ( json_encode($rarr) );
	}
?>