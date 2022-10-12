<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

###쿠폰리스트###
$order_column_arr = array("", "", "", "", "coupon_regdt", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$tableName = "exm_coupon";
$bRegistFlag = false;
$selectArr = "*";
$whereArr = array("cid" => "$cid", "coupon_kind" => "$_GET[kind]");

$totalCnt = count(SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby));

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = " limit $_POST[start], $_POST[length]";
$list = SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby, $limit);

foreach ($list as $key => $value) {
   	$pdata = array();

    $content = $r_coupon_way[$value[coupon_way]];
    	
    if ($value[coupon_way]=="price") { 
	    $content .= " (".number_format($value[coupon_price])._("원").")";
	} 
    else if ($value[coupon_way]=="rate"){ 
	    $content .= " (".number_format($value[coupon_rate])."%)";
	} 
    else if ($value[coupon_way]=="fdate"){
        $fix_extension_date_arr = explode(',', $value[fix_extension_date]);
        $content .= " ".$fix_extension_date_arr[0]._("개월").$fix_extension_date_arr[1]._("일");  
    } 
		
		if ($_GET[kind] == "off")
   		$pdata[] = "<a href='coupon_regist.php?coupon_code={$value[coupon_code]}'>{$value[coupon_code]}</a>";
		else
			$pdata[] = "<a href='coupon_member.php?kind=on&coupon_code={$value[coupon_code]}'>{$value[coupon_code]}</a>";
   	$pdata[] = $value[coupon_name];
   	$pdata[] = $r_coupon_type[$value[coupon_type]];
   	$pdata[] = $content;
   	$pdata[] = substr($value[coupon_regdt],2,14);

    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"coupon_popup('".$value[coupon_kind]."', '".$value[coupon_code]."');\">"._("수정")."</button>";
    $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"coupon_delete('".$value[coupon_code]."');\">"._("삭제")."</button>";

   	$psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>