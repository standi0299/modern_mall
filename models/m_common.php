<?php

class M_common {
	var $db;
	function M_common() {
		$this -> db = $GLOBALS[db];
	}

	function Info($tableName, $selectArr, $whereArr, $bRegistFlag = false) {
		if (is_array($selectArr))
			$selectField = implode(",", $selectArr);
		else
			$selectField = $selectArr;

		$whereStr = "";
		if (is_array($whereArr)) {
			$whereStr = " where ";
			$index = 0;
			foreach ($whereArr as $key => $value) {
				if ($index > 0)
					$whereStr .= " and ";

				$whereStr .= $key . " ='" . $value . "'";
				$index++;
			}
		}

		if ($bRegistFlag) {
			if ($whereStr)
				$whereStr .= " and ";
			$whereStr .= " regist_flag='Y'";
		}

		$sql = "select " . $selectField . " from " . $tableName . $whereStr;
		//debug($sql);
		return $this -> db -> fetch($sql);
	}

	function Select($tableName, $selectArr, $whereArr, $bRegistFlag = false, $orderBy = "", $limit = "") {
		if (is_array($selectArr))
			$selectField = implode(",", $selectArr);
		else
			$selectField = $selectArr;

		$whereStr = "";
		if (is_array($whereArr)) {
			$whereStr = " where ";
			$index = 0;
			foreach ($whereArr as $key => $value) {
				if ($index > 0)
					$whereStr .= " and ";

				$whereStr .= $key . " ='" . $value . "'";
				$index++;
			}
		}
		if ($bRegistFlag) {
			if ($whereStr)
				$whereStr .= " and ";
			$whereStr .= " regist_flag='Y'";
		}

		$sql = "select " . $selectField . " from " . $tableName . $whereStr;

		if ($orderBy)
			$sql .= $orderBy;
		if ($limit)
			$sql .= $limit;

		//debug($sql);
		return $this -> db -> listArray($sql);
	}

	function Insert($tableName, $insertArr, $bRegistFlag = false) {
		$sql = "insert into $tableName set ";

		$index = 0;
		foreach ($insertArr as $key => $value) {
			if ($index > 0)
				$sql .= ", ";
			$sql .= "$key = '$value'";

			$index++;
		}

		//regist_flag, date 등록여부
		if ($bRegistFlag) {
			$sql .= ", regist_flag = 'Y', regist_date = now()";
		}

		$this -> db -> query($sql);
		return $this -> db -> insert_id;
	}

	function Update($tableName, $updateArr, $whereArr, $bRegistFlag = false) {
		$whereStr = "";
		if (is_array($whereArr)) {
			$whereStr = " where ";
			$index = 0;
			foreach ($whereArr as $key => $value) {
				if ($index > 0)
					$whereStr .= " and ";

				$whereStr .= $key . " ='" . $value . "'";
				$index++;
			}
		}

		if ($bRegistFlag) {
			if ($whereStr)
				$whereStr .= " and ";
			$whereStr .= " regist_flag='Y'";
		}

		$sql = "update $tableName set ";

		$index = 0;
		foreach ($updateArr as $key => $value) {
			if ($index > 0)
				$sql .= ", ";
			$sql .= "$key = '$value'";

			$index++;
		}

		$sql .= $whereStr;

		$this -> db -> query($sql);
	}

	function Delete($tableName, $whereArr, $bRegistFlag = false) {
		$whereStr = "";
		if (is_array($whereArr)) {
			$whereStr = " where ";
			$index = 0;
			foreach ($whereArr as $key => $value) {
				if ($index > 0)
					$whereStr .= " and ";

				$whereStr .= $key . " ='" . $value . "'";
				$index++;
			}
		}

		if ($bRegistFlag) {
			if ($whereStr)
				$whereStr .= " and ";
			$whereStr .= " regist_flag='Y'";
		}

		$sql = "delete from $tableName $whereStr";
		$this -> db -> query($sql);
	}

}
?>