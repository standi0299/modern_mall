<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###보낸이메일###
$order_column_arr = array("no","", "", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$tableName = "exm_log_email";
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
    $data[to] = explode(";",$value[to]);
    $cnt = count($data[to])-1;
	$email = $data[to][0];
	if($cnt>0) $email .= "외 $cnt명";
	
   	$pdata = array();
   	$pdata[] = $totalCnt--;
   	$pdata[] = $value[regdt];
   	$pdata[] = $email;
   	$pdata[] = "<a href=\"javascript:;\" onclick=\"logemail_popup('".$value[no]."');\">".$value[subject]."</a>";
   	$pdata[] = $value[cnt];
   	$psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>