<?
include "../lib.php";

$m_config = new M_config();
$r_isuse = array(0 => _("사용안함"), 1 => _("사용"));

$search_data = $_POST[search][value];
$addWhere = "where cid='$cid'";
if ($search_data) $addWhere .= " and compnm like '%$search_data%'";

$orderby = "order by shipno desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_config->getShipCompInfo($cid, '', $addWhere, $orderby, $limit);
$list_cnt = $m_config->getShipCompInfo($cid, '', $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();

   $pdata[] = $totalCnt-$key;
   $pdata[] = $value[compnm];
   $pdata[] = $value[url];
   $pdata[] = $r_isuse[$value[isuse]+0];
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"popup('shipcomp_popup.php?shipno=$value[shipno]', 630, 405)\">"._("수정")."</button>";
   $pdata[] = "<a href=\"indb.php?mode=del_shipcomp&shipno=$value[shipno]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\">
   			   <span class=\"btn btn-xs btn-danger\">삭제</span>
   			   </a>";
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>