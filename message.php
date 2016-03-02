<?php

	include("dbconnect.php");
	include("functions/commonfunctions.php");
	include("DataModel.php");

	$r = array();
	foreach ($_POST as $key => $value) {
		$r[$key] = mysqli_real_escape_string( $con, $value );
	}

	$rarr = array('status' => 0);


	/**
	 * Messages Class
	 * interacts with Messages table
	 */
	class Messages extends DataModel {
		function __construct($r){
			parent::__construct($r);
			$this->tablename = 'msgs';
			$this->addProjection('*');
			$this->more = "order by msgid desc limit 20";
		}

		function getMessageFeed(){
			return $this->doQuery( "select m.*, u1.username as username, u2.username as username_rec from ("
					. "(" . $this->getQueryStr() . ") as m "
					. "join "
					. "(select username, id from eyeds) as u1 "
					. "on u1.id = m.id "
					. "join "
					. "(select username, id from eyeds) as u2 "
					. "on u2.id = m.recid"
				. ") order by msgid desc" );
		}
	}

	$msgObj = new Messages($r);
	$msgObj->checkTokenValid();

	if ($r['action'] == 'send'){
		// send message
		// id, token, content, recid
		$msgObj->checkInputHas(['content', 'recid']);
		$msgObj->addInsertsFromArray($r, ['content', 'id', 'recid']);
		$msgObj->addInsert('doc', date('Y-m-d H:i:s'));
		$result = $msgObj->insert(ERR_NOUSER);
		die(json_encode($rarr));

	} else if ($r['action'] == 'feed'){
		// get feed
		// id, token
		$msgObj->addSelection("recid={$r['id']} or id={$r['id']}");
		$result = $msgObj->getMessageFeed();
		$rarr['messages'] = array();
		while ($row = $result->fetch_assoc())
			$rarr['messages'][] = $row;
		die(json_encode($rarr));

	} else if ($r['action'] == 'feedbyuser'){
		// get chat feed with a particular user
		// id, token, recid
		$msgObj->checkInputHas(['recid']);
		$msgObj->addSelection("(id={$r['id']} and recid={$r['recid']}) or (id={$r['recid']} and recid={$r['id']})");
		$result = $msgObj->getMessageFeed();
		$rarr['messages'] = array();
		while ($row = $result->fetch_assoc())
			$rarr['messages'][] = $row;
		die(json_encode($rarr));

	} else {
		$msgObj->makeError(ERR_NOACTION);
	}
?>