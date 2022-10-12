<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###탈퇴회원관리###
$order_column_arr = array("mid", "", "outdt");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$tableName = "exm_member_out";
$bRegistFlag = false;
$selectArr = "*";
$whereArr = array("cid" => "$cid");

$totalCnt = count(SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby));

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = " limit $_POST[start], $_POST[length]";
$list = SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby, $limit);

foreach ($list as $key => $value) {
   $pdata = array();
   $pdata[] = $value[mid];
   $pdata[] = $value[regdt];
   $pdata[] = $value[outdt];
   $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>