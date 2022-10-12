<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###휴면계정복원###
$search_data = $_POST[search][value];
if ($search_data)
{
  $addwhere = " and (name like '%$search_data%' or mid like '%$search_data%')";
}
$order_column_arr = array("mid","", "lastlogin", "restdt", "");
$order_data = $_POST[order][0];
$orderby = $addwhere. " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$tableName = "exm_member";
$bRegistFlag = false;
$selectArr = "mid, name, lastlogin, restdt";
$whereArr = array("cid" => "$cid", "rest_flag" => '1');

$totalCnt = count(SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby));

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = " limit $_POST[start], $_POST[length]";
$list = SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby, $limit);

foreach ($list as $key => $value) {
   $pdata = array();
   $pdata[] = $value[mid];
   $pdata[] = $value[name];
   $pdata[] = ($value[lastlogin] == "0000-00-00 00:00:00") ? "-" : $value[lastlogin];
   $pdata[] = ($value[restdt] == "0000-00-00 00:00:00") ? "-" : $value[restdt];
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"restore('".$value[mid]."');\">"._("복원하기")."</button>";

   $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>