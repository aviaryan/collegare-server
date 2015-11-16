<?php
	include("../dbconnect.php");

	$arr = array('auths', 'vts', 'cmnts', 'msgs', 'posts', 'gnetwork', 'groups', 'eyeds');
	// keep posts and eyeds at the end, they are main tables and foreign keys for others
	
	foreach ($arr as $value) {
		$query = "drop table if exists {$value}";
		$result = mysqli_query($con, $query);
	}
?>