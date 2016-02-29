<?php
	
	include("base.php");

	class DataModel {

		var $projections = [];
		var $tablename = '';
		var $selections = [];
		var $inserts = [];
		var $more = '';
		var $r;
		var $con;

		function __construct($r = []){
			global $con;
			$this->con = $con;
			$this->r = $r;
		}

		/**
		 * query table with the given projection and selections
		 * @return resultSet result of query
		 */
		function query($err = 1){
			return $this->doQuery($this->getQueryStr(), $err);
		}

		function getQueryStr(){
			$q = "select "
				. $this->getProjectionStr()
				. " from " 
				. $this->tablename
				. " where " 
				. $this->getSelectionStr()
				. " $this->more";
			return $q;
		}

		function doQuery($q, $err=1){
			//echo $q;
			$result = mysqli_query($this->con, $q);
			return ($result) ? $result : $this->makeError($err);
		}

		/**
		 * INSERT OPS
		 * FUNCTIONS FOR INSERTING TO TABLE
		 */

		function insert($err = 2){
			$result = mysqli_query($this->con, $this->getInsertStr());
			if ($result)
				return $result;
			else
				$this->makeError($err);
		}

		function getInsertStr(){
			$q = "insert into "
				. $this->tablename
				. " (" . $this->arrayToStr(array_keys($this->inserts)) . ")"
				. " values ("
				. $this->arrayToStr(array_values($this->inserts), "\"", "\"") . ")";
			return $q;
		}

		function addInsert($key, $value){
			$this->inserts[$key] = $value;
		}

		function addInsertsFromArray($arr, $allowed_keys = []){
			foreach ($allowed_keys as $value) {
				if (array_key_exists($value, $arr))
					$this->addInsert($value, $arr[$value]);
			}
		}

		/**
		 * UPDATE OPS
		 * FUNCTIONS FOR UPDATING TABLE
		 */
		
		function update($err = 5){
			return $this->customUpdate($this->makeSQLUpdate($this->inserts), $this->getSelectionStr(), $this->more, $err);
		}

		function getUpdateStr($changes, $selectionStr, $more){
			$q = "update "
				. $this->tablename
				. " set "
				. $changes
				. " where "
				. $selectionStr
				. " $more";
			return $q;
		}

		function customUpdate($changes, $selectionStr, $more, $err = 5){
			$q = $this->getUpdateStr($changes, $selectionStr, $more);
			$result = mysqli_query($this->con, $q);
			if ($result)
				return $result;
			else
				$this->makeError($err);
		}

		/**
		 * QUERY FUNCTIONS
		 * HELPERS
		 */

		function addProjection($col){
			if (!in_array($col, $this->projections))
				$this->projections[] = $col;
		}

		function addSelection($col){
			if ($col == '')
				return;
			if (!in_array($col, $this->selections))
				$this->selections[] = $col;
		}

		function getSelectionStr(){
			return $this->arrayToStr($this->selections, '(', ')', ' and ');
		}

		function getProjectionStr(){
			return $this->arrayToStr($this->projections, '', '', ',');
		}

		function makeSQLUpdate($r){
			$str = '';
			foreach ($r as $k => $v)
				$str .= "{$k}=\"{$v}\",";
			return substr($str, 0, -1);
		}

		/**
		 * QUERY HELPER FUNCTIONS
		 * NOT DIRECTLY RELATED TO QUERIES
		 */

		function arrayToStr($arr, $left='', $right='', $divider=','){
			$str = '';
			foreach ($arr as $value) {
				$str .= $left . $value . $right . $divider;
			}
			return substr($str, 0, -1 * strlen($divider));
		}

		/**
		 * BASE FUNCTIONS
		 * DO THE MOST BASE OPERATIONS OF THE APPLICATION
		 */

		function makeError($code){
			global $rarr;
			$rarr['status'] = $code;
			if ($code == ERR_NOACTION)
				$rarr['error'] = "Invalid Action";
			else if ($code == ERR_DBCONN)
				$rarr['error'] = "Database connection error";
			else if ($code == ERR_AUTH)
				$rarr['error'] = "Problem while authenticating the user";
			else if ($code == ERR_LOGINFAIL)
				$rarr['error'] = "Wrong username or password";
			else if ($code == ERR_ARGS)
				$rarr['error'] = "Invalid parameters passed or required parameters missing";
			else if ($code == ERR_NOUSER)
				$rarr['error'] = 'Non-existant user';
			else if ($code == ERR_ERR)
				$rarr['error'] = 'Some problem occured';
			else if ($code == ERR_USER_EXISTS)
				$rarr['error'] = 'User already exists';
			else if ($code == ERR_NOPOST)
				$rarr['error'] = 'Post doesn\'t exist.';
			die(json_encode($rarr));
		}

		function isTokenValid(){
			$this->checkInputHas(['id', 'token']); // check if keys exist
			$query = "select token from auths where id={$this->r['id']}";
			$result = $this->doQuery($query, 2);
			if (mysqli_num_rows($result) > 0){
				$intoken = mysqli_fetch_row($result)[0];
				if ($intoken == $this->r['token'])
					return true;
				else
					return false;
			} else {
				return false;
			}
		}

		function checkTokenValid(){
			return ($this->isTokenValid()) ? true : makeError(3);
		}

		function checkInputHas($manParams){
			foreach ($manParams as $param){
				if (!array_key_exists($param, $this->r))
					makeError(5);
			}
		}

		function inputHas($params){
			foreach ($params as $param){
				if (!array_key_exists($param, $this->r))
					return false;
			}
			return true;
		}

		/**
		 * GETTER
		 * SETTER
		 * STUFF
		 */

		function clearInserts(){
			$this->inserts = [];
		}
	}
?>