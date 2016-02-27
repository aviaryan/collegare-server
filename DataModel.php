<?php
	
	class DataModel {

		var $projections = [];
		var $tablename = '';
		var $selections = [];
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
		function query(){
			$result = mysqli_query($this->con, $this->getQueryStr());
			return $result;
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

		function doQuery($q){
			//echo $q;
			$result = mysqli_query($this->con, $q);
			return $result;
		}

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


		function arrayToStr($arr, $left, $right, $divider){
			$str = '';
			foreach ($arr as $value) {
				$str .= $left . $value . $right . $divider;
			}
			return substr($str, 0, -1 * strlen($divider));
		}

	}
?>