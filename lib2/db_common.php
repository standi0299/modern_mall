<?php

//공통적 DB 처리를 위한 함수 모음

function SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag = false) {
	$m_common = new M_common();
	return $m_common -> Info($tableName, $selectArr, $whereArr, $bRegistFlag);
}

function SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag = false, $orderBy = "", $limit = "") {
	$m_common = new M_common();
	return $m_common -> Select($tableName, $selectArr, $whereArr, $bRegistFlag, $orderBy, $limit);
}

function InsertTable($tableName, $insertArr, $bRegistFlag = false) {
	$m_common = new M_common();

	return $m_common -> Insert($tableName, $insertArr, $bRegistFlag);
}

function UpdateTable($tableName, $updateArr, $whereArr, $bRegistFlag = false) {
	$m_common = new M_common();
	$m_common -> Update($tableName, $updateArr, $whereArr);
}

function DeleteTable($tableName, $whereArr, $bRegistFlag = false) {
	$m_common = new M_common();
	$m_common -> Delete($tableName, $whereArr);
}
?>