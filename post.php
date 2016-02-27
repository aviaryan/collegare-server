<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");
	include("DataModel.php");

	/**
	 * Posts Class
	 * responsible for querying the posts database
	 */
	class Posts extends DataModel {
		var $limit;

		function __construct(){
			parent::__construct();
			$this->tablename = 'posts';
			$this->addProjection('*');
			$this->limit = 20;
			$this->more = 'order by postid desc limit 20';
		}

		function getPostFeed($id){
			return $this->doQuery( "select p.*, IfNull(vote,0) as vote from ("
					. "(" . $this->getQueryStr() . ") as p "
					. "left join "
					. "(select * from vts) as v "
					. "on v.postid = p.postid and v.id = $id"
				. ") order by weight desc" );
		}
	}


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
		$postObj = new Posts();

		if ( array_key_exists("gid", $r) ){
			// limit by post id
			if (array_key_exists("after", $r))
				$postObj->addSelection("postid > {$r['after']}");
			else if (array_key_exists("before", $r))
				$postObj->addSelection("postid < {$r['before']}");
			// get posts from group
			$postObj->addSelection("gid={$r['gid']}");
			$res = $postObj->getPostFeed($r['id']);
			$rarr['posts'] = array();
			while ($row = mysqli_fetch_assoc($res)){
				$rarr['posts'][] = $row;
			}
			die ( json_encode($rarr) );
		} else {
			// get posts for a user
			// check for missing id
			if ( !array_key_exists("id", $r) ){
				makeError(1);
			}
			// get user groups
			$q = "select * from eyeds where id={$r['id']}";
			$res = mysqli_query($con, $q);
			$row = mysqli_fetch_assoc($res);
			$groups = ArrToLike(StrToArr($row['groups']),0);
			if ($groups != '')
				$gq = "gid in ({$groups}) or";
			else
				$gq = '';
			// limit by post id
			if (array_key_exists("after", $r))
				$postObj->addSelection("postid > {$r['after']}");
			else if (array_key_exists("before", $r))
				$postObj->addSelection("postid < {$r['before']}");
			// get posts for user
			$postObj->addSelection("{$gq} gid=1");
			$res = $postObj->getPostFeed($r['id']);
			$rarr['posts'] = array();
			while ($row = mysqli_fetch_assoc($res)){
				$rarr['posts'][] = $row;
			}
			die ( json_encode($rarr) );
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