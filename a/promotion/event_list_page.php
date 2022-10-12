<?
include "../lib.php";

$m_etc = new M_etc();

$addWhere = "where cid='$cid'";

/*$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {	
	if ($postData[catno]) {
		if (is_numeric($postData[catno])) $addWhere .= " and catno like '$postData[catno]%'";
	}
}*/

$orderby = "order by eventno desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_etc->getEventList($cid, $addWhere, $orderby, $limit);
$list_cnt = $m_etc->getEventList($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();

   if ($value[sdate] == "0000-00-00") $value[sdate] = "";
   if ($value[edate] == "0000-00-00") $value[edate] = "";

   $pdata[] = $totalCnt-$key;
   $pdata[] = "<a href=\"event_write.php?eventno=$value[eventno]\">$value[title]</a>";
   $pdata[] = $value[sdate];
   $pdata[] = $value[edate];
   $pdata[] = "<a href=\"../../goods/event.php?eventno=$value[eventno]\" target=\"_blank\"><img src=\"../img/bt_preview.png\"></a>";
   $pdata[] = "<a href=\"event_write.php?eventno=$value[eventno]\"><span class=\"btn btn-xs btn-primary\">"._("수정")."</span></a>";
   $pdata[] = "<a href=\"indb.php?mode=delEvent&eventno=$value[eventno]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\"><span class=\"btn btn-xs btn-danger\">"._("삭제")."</span></a>";
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>