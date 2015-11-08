<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}

	$rarr = array('status' => 0);

	if ( $r['action'] == 'upvote' || $r['action'] == 'downvote' ){
		// upvote/downvote a post
		// if already voted, it changes the vote to upvote/downvote
		if (!tokenvalid($r['id'], $r['token']))
			makeError(3);

		$query = "select vote from vts where postid={$r['postid']} and id={$r['id']}";
		$result = mysqli_query($con, $query);
		$vote = ($r['action'] == 'upvote') ? 1 : -1;

		if ( mysqli_num_rows($result) > 0 ){
			$curVote = mysqli_fetch_row($result)[0];
			if ($curVote == $vote) // alerady voted same
				die(json_encode($rarr));
			// reverse vote
			$weight_change = ($vote==1) ? 2 : -2;
			execQuery("update vts set vote={$vote} where id={$r['id']} and postid={$r['postid']}", 5);
			if ($vote == 1)
				$pQuery = "weight=weight+2,upcount=upcount+1,downcount=downcount-1";
			else
				$pQuery = "weight=weight-2,upcount=upcount-1,downcount=downcount+1";
			execQuery("update posts set {$pQuery} where postid={$r['postid']}", 2); // db con err
			die(json_encode($rarr));
		} else {
			execQuery("insert into vts values ({$r['id']}, {$r['postid']}, {$vote})", 2);
			if ($vote == 1)
				execQuery("update posts set weight=weight+1,upcount=upcount+1 where postid={$r['postid']}");
			else
				execQuery("update posts set weight=weight-1,downcount=downcount+1 where postid={$r['postid']}");
			die(json_encode($rarr));
		}
	} else {
		makeError(1);
	}
?>