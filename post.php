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

		function __construct($r){
			parent::__construct($r);
			$this->tablename = 'posts';
			$this->addProjection('*');
			$this->limit = 20;
			$this->more = "order by postid desc limit $this->limit";
		}

		function getPostFeed($id){
			return $this->doQuery( "select p.*, IfNull(vote,0) as vote from ("
					. "(" . $this->getQueryStr() . ") as p "
					. "left join "
					. "(select * from vts where id = $id) as v "
					. "on v.postid = p.postid"
				. ") order by weight desc" );
		}

		function getUserGroups(){ // TODO : Change group implementation
			$q = "select groups from eyeds where id={$this->r['id']}";
			$res = $this->doQuery($q);
			$groups = $res->fetch_row()[0];
			return $groups;
		}

		function getUserVote($id, $postid){
			$res = $this->doQuery("select vote from vts where id={$id} and postid={$postid}");
			if (mysqli_num_rows($res) > 0)
				return mysqli_fetch_row($res)[0];
			else
				return 0;
		}
	}

	/**
	 * Comments Class
	 * interacts with Comments table
	 */
	class Comments extends DataModel {
		function __construct($r){
			parent::__construct($r);
			$this->tablename = 'cmnts';
			$this->addProjection('*');
			$this->more = "order by commentid desc";
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
		$obj = new Posts($r);
		$obj->checkInputHas(['postid']);
		$obj->addSelection("postid = {$r['postid']}");
		$result = $obj->query(ERR_NOPOST, true);
		$postarr = mysqli_fetch_assoc($result);
		$postarr['vote'] = $obj->getUserVote($r['id'], $r['postid']);
		// get comments
		$commentObj = new Comments($r);
		$commentObj->addSelection("postid=" . $r['postid']);
		$result = $commentObj->query();
		$postarr['comments'] = array();
		while ($row = mysqli_fetch_assoc($result)){
			unset($row['postid']);
			$postarr['comments'][] = $row;
		}
		$rarr = array_merge($postarr, $rarr);
		die(json_encode($rarr));

	} else if ( $r['action'] == 'set' ){
		// create a new post
		// send token, userid, content, [groupid, pollid]
		$postObj = new Posts($r);
		$postObj->checkTokenValid();
		$postObj->checkInputHas(['content']);
		$postObj->addInsertsFromArray($r, ['id', 'content']);
		$postObj->addInsert('username', getUsername($r['id']));
		$postObj->addInsert('doc', date('Y-m-d H:i:s'));
		$result = $postObj->insert();
		if ($result){
			$result = $postObj->customUpdate("weight = postid", "id = {$r['id']}", "order by postid desc limit 1");
			// add weight=posts to the last post
			// id = r[id] is a safety belt in case of parallel requests
			die(json_encode($rarr));
		}
	} else if ( $r['action'] == 'feed' ){
		// get feed for a user or a group
		// userid, groupid
		$postObj = new Posts($r);

		if ($postObj->inputHas(['gid'])){
			$postObj->checkInputHas(['id']);
			// limit by post id
			if ($postObj->inputHas(['after']))
				$postObj->addSelection("postid > {$r['after']}");
			else if ($postObj->inputHas(['before']))
				$postObj->addSelection("postid < {$r['before']}");
			// get posts from group
			$postObj->addSelection("gid={$r['gid']}");
			$res = $postObj->getPostFeed($r['id']);
			$rarr['posts'] = array();
			while ($row = $res->fetch_assoc())
				$rarr['posts'][] = $row;
			die (json_encode($rarr));
		} else {
			// get posts for a user
			$postObj->checkInputHas(['id']);
			// get user groups
			$groups = $postObj->getUserGroups();
			if ($groups != '')
				$gq = "gid in ({$groups}) or";
			else
				$gq = '';
			// limit by post id
			if ($postObj->inputHas(['after']))
				$postObj->addSelection("postid > {$r['after']}");
			else if ($postObj->inputHas(['before']))
				$postObj->addSelection("postid < {$r['before']}");
			// get posts for user
			$postObj->addSelection("{$gq} gid=1");
			$res = $postObj->getPostFeed($r['id']);
			$rarr['posts'] = array();
			while ($row = $res->fetch_assoc())
				$rarr['posts'][] = $row;
			die (json_encode($rarr));
		}

	} else if ( $r['action'] == 'comment' ){
		// make a comment on a post
		// content, postid
		$comObj = new Comments($r);
		$comObj->checkTokenValid();
		$comObj->checkInputHas(['content', 'postid']);
		$comObj->addInsertsFromArray($r, ['id', 'content', 'postid']);
		$comObj->addInsert('username', getUsername($r['id']));
		$comObj->addInsert('doc', date('Y-m-d H:i:s'));
		$result = $comObj->insert();
		if ($result){
			execQuery("update posts set commentcount=commentcount+1 where postid={$r['postid']}");
			die( json_encode($rarr) );
		}
	} else {
		makeError(ERR_NOACTION);
	}
?>