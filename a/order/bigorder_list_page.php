<?
include "../lib.php";

$m_order = new M_order();

$addWhere = "";
$postData = json_decode(base64_decode($_GET[postData]), 1);

if ($postData) {
	$startDate = str_replace("-", "", $postData[start]);
	$endDate = str_replace("-", "", $postData['end']);
	if ($startDate) $addWhere .= " and a.regdt > '{$startDate}'";
	if ($endDate) $addWhere .= " and a.regdt < adddate('{$endDate}',interval 1 day)";	
	
	if ($postData[searchValue] != "") {
		$addWhere .= " and (a.goodsnm like '%$postData[searchValue]%' or a.request_company like '%$postData[searchValue]%' or a.request_name like '%$postData[searchValue]%')";
	}
	
	if ($postData[category] != "") {
		$addWhere .= " and a.category = '$postData[category]'";
	}
}

$addQuery = " order by a.regdt desc";
$addQuery .= " limit $_POST[start], $_POST[length]";

$list = $m_order->getBigorderList($cid, $addWhere, $addQuery);
$totalCnt = $m_order->getBigorderListCnt($cid, $addWhere);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
	$pdata = array();
	
	$view_button = "<a href=\"javascript:;\" onclick=\"popup('bigorder_popup.php?no=$value[no]',650,650)\"><span class=\"btn btn-xs btn-default\">보기</span></a>";
	
	$pdata[] = $r_bigorder_type[$value[category]];
	$pdata[] = $value[goodsnm];
	$pdata[] = $value[request_company];
	$pdata[] = $value[request_name];
	$pdata[] = $value[regdt];
	$pdata[] = $view_button;
	
	$psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>