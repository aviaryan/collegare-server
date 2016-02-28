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
		function __construct(){
			parent::__construct();
			$this->tablename = 'msgs';
			$this->addProjection('*');
			$this->more = "order by msgid desc limit 20";
		}
	}

	$msgObj = new Messages();

	if ($r['action'] == 'send'){
		// send message
		// id, token, content, recid
		if ( tokenvalid($r['id'], $r['token']) ){
			$msgObj->addInsertsFromArray($r, ['content', 'id', 'recid']);
			$msgObj->addInsert('username', getUsername($r['id']));
			$msgObj->addInsert('username_rec', getUsername($r['recid']));
			$msgObj->addInsert('doc', date('Y-m-d H:i:s'));
			$result = $msgObj->insert(5);
			if ($result){
				die(json_encode($rarr));
			}
		} else {
			makeError(3);
		}
	} else if ($r['action'] == 'feed' || $r['action'] == 'feedbyuser'){
		// get feed
		// id, token [, recid]
		if ( tokenvalid($r['id'], $r['token']) ){
			if ($r['action'] == 'feed')
				$msgObj->addSelection("recid={$r['id']} or id={$r['id']}");
			else
				$msgObj->addSelection("(id={$r['id']} and recid={$r['recid']}) or (id={$r['recid']} and recid={$r['id']})");
			$result = $msgObj->query();
			$rarr['messages'] = $result->fetch_all(MYSQLI_ASSOC);
			die(json_encode($rarr));
		} else {
			makeError(3);
		}
	} else {
		makeError(1);
	}
?>