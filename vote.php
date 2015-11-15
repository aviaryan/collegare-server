<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}

	$rarr = array('status' => 0);

	if ( $r['action'] == 'upvote' || $r['action'] == 'downvote' || $r['action'] == 'none' ){
		// upvote/downvote a post
		// if already voted, it changes the vote to upvote/downvote
		if (!tokenvalid($r['id'], $r['token']))
			makeError(3);

		$query = "select vote from vts where postid={$r['postid']} and id={$r['id']}";
		$result = mysqli_query($con, $query);
		$vote = ($r['action'] == 'upvote') ? 1 : ( ($r['action'] == 'downvote') ? -1 : 0 );

		if ( mysqli_num_rows($result) > 0 ){
			// already voted
			$curVote = mysqli_fetch_row($result)[0];
			if ($curVote == $vote) // alerady voted same
				die(json_encode($rarr));
			// change vote
			if ($vote == 0)
				removeVote($r['id'], $r['postid']);
			else
				execQuery("update vts set vote={$vote} where id={$r['id']} and postid={$r['postid']}", 5);
			// update counts
			if ($vote == 1)
				$pQuery = genCountQuery(2,1,-1);
			else if ($vote == -1)
				$pQuery = genCountQuery(-2,-1,1);
			else {
				if ($curVote == 1)
					$pQuery = genCountQuery(-1,-1,0); 
				else
					$pQuery = genCountQuery(1,0,-1);
			}
			execQuery("update posts set {$pQuery} where postid={$r['postid']}", 2); // db con err
			die(json_encode($rarr));
		} else {
			// not voted yet
			if ($vote != 0){
				execQuery("insert into vts values ({$r['id']}, {$r['postid']}, {$vote})", 2);
				if ($vote == 1)
					execQuery("update posts set weight=weight+1,upcount=upcount+1 where postid={$r['postid']}");
				else
					execQuery("update posts set weight=weight-1,downcount=downcount+1 where postid={$r['postid']}");
			}
			die(json_encode($rarr));
		}
	} else {
		// invalid option
		makeError(1);
	}


	function removeVote($id, $postid){
		$res = execQuery("delete from vts where id={$id} and postid={$postid}");
	}

	function genCountQuery($wt, $up, $dn){
		$str = '';
		if ($wt != 0)
			$str .= "weight=weight" . prefixSign($wt) . ',';
		if ($up != 0)
			$str .= "upcount=upcount" . prefixSign($up) . ',';
		if ($dn != 0)
			$str .= "downcount=downcount" . prefixSign($dn) . ',';
		return substr($str, 0, -1);
	}
?>