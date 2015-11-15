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
		$res = execQuery("select (sum(upcount)*5)-(sum(downcount)*7),count(*) from posts where id={$row['id']}"); // 2/3 ratio
		$postStats = mysqli_fetch_row($res);
		$row['holiness'] = $postStats[0];
		$row['postcount'] = $postStats[1];
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
			execQuery("update eyeds set " . $updtStmt . " where id={$id}", 5);
		die( json_encode($rarr) );

	} else if ($r['action'] == 'setpic'){
		// SET USER PIC
		// file name should be $ID
		if (!tokenvalid($r['id'], $r['token']))
			makeError(3);
		foreach ($_FILES as $key => $value) {
			$upfile = $key;
			break;
		}
		$uploaddir = 'uploads/';

		savePic($_FILES[$upfile]['tmp_name'], $uploaddir . $r['id']);
		die( json_encode($rarr) );

	} else if ($r['action'] == 'getpic' || $r['action'] == 'getfullpic'){
		// GET USER PIC
		// BOTH TYPES
		$id = getId($r['username']);
		$url = 'uploads/' . $id . ($r['action'] == 'getpic' ? '_thumb' : '') . '.jpg';
		if (file_exists($url))
			$rarr['url'] = $url;
		else
			$rarr['url'] = '';
		die( json_encode($rarr) );

	} else {
		// invalid option
		makeError(1);
	}


	function savePic($img, $dst){
		if (($img_info = getimagesize($img)) === FALSE)
			die("Image not found or not an image");

		$width = $img_info[0];
		$height = $img_info[1];

		switch ($img_info[2]) {
			case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);  break;
			case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); break;
			case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);  break;
			default : die("Unknown filetype");
		}

		$tmp = imagecreatetruecolor(256, 256);
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, 256, 256, $width, $height);
		imagejpeg($tmp, $dst.".jpg", 75);

		$tmp = imagecreatetruecolor(64, 64);
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, 64, 64, $width, $height);
		imagejpeg($tmp, $dst."_thumb.jpg", 50);
	}

?>