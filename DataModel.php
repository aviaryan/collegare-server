<?php
	
	class DataModel {

		var $projections = [];
		var $tablename = '';
		var $selections = [];
		var $inserts = [];
		var $more = '';
		var $con;

		function __construct(){
			global $con;
			$this->con = $con;
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
		 * HELPER FUNCTIONS
		 * NOT DIRECTLY RELATED TO QUERIES
		 */

		function arrayToStr($arr, $left='', $right='', $divider=','){
			$str = '';
			foreach ($arr as $value) {
				$str .= $left . $value . $right . $divider;
			}
			return substr($str, 0, -1 * strlen($divider));
		}

		function makeError($code){
			global $rarr;
			$rarr['status'] = $code;
			if ($code == 1)
				$rarr['error'] = "Invalid Option";
			else if ($code == 2)
				$rarr['error'] = "Database connection error";
			else if ($code == 3)
				$rarr['error'] = "Problem while authenticating the user";
			else if ($code == 4)
				$rarr['error'] = "Wrong username or password";
			else if ($code == 5)
				$rarr['error'] = "Invalid parameters passed";
			else if ($code == 6)
				$rarr['error'] = 'Non-existant user';
			else if ($code == 7)
				$rarr['error'] = 'Some problem occured';
			else if ($code == 8)
				$rarr['error'] = 'User already exists';
			else if ($code == 11)
				$rarr['error'] = 'Post doesn\'t exist.';
			die(json_encode($rarr));
		}

		function isTokenValid($id, $token){
			$query = "select token from auths where id={$id}";
			$result = $this->doQuery($query, 2);
			if (mysqli_num_rows($result) > 0){
				$intoken = mysqli_fetch_row($result)[0];
				if ($intoken == $token)
					return true;
				else
					return false;
			} else {
				return false;
			}
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