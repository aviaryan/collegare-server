<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}

	$rarr = array('status' => 0);

	if ( $r['action'] == 'get' ){
		// get a single post
		// with all the fucking comments
		$query = "select * from posts where postid={$r['postid']}";
		$result = mysqli_query($con, $query);

		if ($result){
			$postarr = mysqli_fetch_assoc($result);
			$postarr['vote'] = getUserVote($r['id'], $r['postid']);
			// get comments
			$query = "select * from cmnts where postid={$r['postid']} order by commentid desc";
			$result = mysqli_query($con, $query);
			$postarr['comments'] = array();
			while ($row = mysqli_fetch_assoc($result)){
				unset($row['postid']);
				$postarr['comments'][] = $row;
			}
			$rarr = array_merge($postarr, $rarr);
			die( json_encode($rarr) );
		} else {
			$rarr['status'] = 11;
			$rarr['error'] = 'Post doesn\'t exist.';
			die( json_encode($rarr) );
		}

	} else if ( $r['action'] == 'set' ){
		// create a new post
		// send token, userid, content, [groupid, pollid]
		if ( tokenvalid($r['id'], $r['token']) ){
			$username = getUsername($r['id']);
			$sqlIns = makeSQLInsert($r);
			$cdate = date('Y-m-d H:i:s');
			$query = "insert into posts ( {$sqlIns['cols']} , username, doc ) values ( {$sqlIns['vals']} , '$username', '$cdate' )";
			$result = mysqli_query($con, $query);
			if ( $result ){
				$query = "update posts set weight=postid where id={$r['id']} order by postid desc limit 1"; // add weight=posts to the last post
				// id = r[id] is a safety belt in case of parallel requests
				$result = mysqli_query($con, $query);
				die( json_encode($rarr) );
			} else
				makeError(2);

		} else {
			makeError(3);
		}

	} else if ( $r['action'] == 'feed' ){
		// get feed for a user or a group
		// userid, groupid
		if ( array_key_exists("gid", $r) ){
			// get posts from group
			$q = "select * from posts where gid={$r['gid']} order by weight desc limit 20";
			$res = mysqli_query($con, $q);
			$rarr['posts'] = array();
			while ($row = mysqli_fetch_assoc($res)){
				$row['vote'] = getUserVote($r['id'], $row['postid']);
				$rarr['posts'][] = $row;
			}
			die ( json_encode($rarr) );
		} else {
			// get posts for a user
			if ( array_key_exists("id", $r) ){
				$q = "select * from eyeds where id={$r['id']}";
				$res = mysqli_query($con, $q);
				$row = mysqli_fetch_assoc($res);
				$groups = ArrToLike(StrToArr($row['groups']),0);
				if ($groups != '')
					$gq = "gid in ({$groups}) or";
				else
					$gq = '';
				$q = "select * from posts where {$gq} gid=1 order by weight desc, postid desc limit 20";
				$res = mysqli_query($con, $q);
				$rarr['posts'] = array();
				while ($row = mysqli_fetch_assoc($res)){
					$row['vote'] = getUserVote($r['id'], $row['postid']);
					$rarr['posts'][] = $row;
				}
				die ( json_encode($rarr) );

			} else {
				makeError(1);
			}
		}

	} else if ( $r['action'] == 'comment' ){
		// comment on a post
		if (!tokenvalid($r['id'], $r['token']))
			makeError(3);
		$username = getUsername($r['id']);
		$sqlIns = makeSQLInsert($r);
		$query = "insert into cmnts ( {$sqlIns['cols']},"
			. "doc,"
			. "username)"
			. " values ( {$sqlIns['vals']},"
			. "'" . date('Y-m-d H:i:s') . "',"
			. "'" . getUsername($r['id']) . "')";
		$result = mysqli_query($con, $query);
		if ($result){
			execQuery("update posts set commentcount=commentcount+1 where postid={$r['postid']}");
			die( json_encode($rarr) );
		} else
			makeError(2);
	} else {
		makeError(1);
	}


	function getUserVote($id, $postid){
		$res = execQuery("select vote from vts where id={$id} and postid={$postid}");
		if (mysqli_num_rows($res) > 0)
			return mysqli_fetch_row($res)[0];
		else
			return 0;
	}
	
?>