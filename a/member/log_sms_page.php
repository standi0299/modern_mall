<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###SMS로그###
$order_column_arr = array("no","", "", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$tableName = "exm_log_sms";
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
	//문자열 나누는 기준 문자열 ; 로 수정 / 14.11.04 / kjm
    $data[number] = explode(";",$value[number]);
    $cnt = count($data[number])-1;
	$number = $data[number][0];
	if($cnt>0) $number .= _("외")." $cnt"._("명");
	
   	$pdata = array();
   	$pdata[] = $totalCnt--;
   	$pdata[] = $value[regdt];
   	$pdata[] = $number;
   	$pdata[] = $value[msg];
   	$pdata[] = $value[call];
   	$psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>