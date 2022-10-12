<?
include "../lib.php";

$m_config = new M_config();

$search_data = $_POST[search][value];
$addWhere = "where a.cid='$cid'";
if ($search_data) $addWhere .= " and a.mid like '%$search_data%'";

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
	if ($postData[start]) $addWhere .= " and conndt > '{$postData[start]}'";
	if ($postData['end']) $addWhere .= " and conndt < adddate('{$postData[end]}',interval 1 day)";
}

$orderby = "order by no desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_config->getAdminLog($cid, $addWhere, $orderby, $limit);
$list_cnt = $m_config->getAdminLog($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();

   $pdata[] = $totalCnt-$key;
   $pdata[] = $value[conndt];
   $pdata[] = $value[mid];
   $pdata[] = $value[name];
   $pdata[] = substr($value[password], 0, 16);
   $pdata[] = $value[ip];
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>