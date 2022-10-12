<?
include "../lib.php";

$m_config = new M_config();
$r_state = array(0 => "<b class='red'>OFF</b>", 1 => "<b class='notice'>ON</b>");

$search_data = $_POST[search][value];
$addWhere = "where cid='$cid'";
if ($search_data) $addWhere .= " and title like '%$search_data%'";

$order_column_arr = array("popupno", "", "", "", "", "", "");
$order_data = $_POST[order][0];
$orderby = "order by ".$order_column_arr[$order_data[column]]." ".$order_data[dir];

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_config->getPopupInfo($cid, '', $addWhere, $orderby, $limit);
$list_cnt = $m_config->getPopupInfo($cid, '', $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();

   $pdata[] = $value[popupno];
   $pdata[] = $value[title];
   $pdata[] = $r_state[$value[state]+0];
   $pdata[] = $value[skintype];
   $pdata[] = $value[sdt];
   $pdata[] = $value[edt];
   $pdata[] = "<a href=\"popup_write.php?popupno=$value[popupno]\"><button type=\"button\" class=\"btn btn-xs btn-primary\">"._("수정")."</button></a>";
   $pdata[] = "<a href=\"indb.php?mode=del_popupinfo&popupno=$value[popupno]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\">
   			   <span class=\"btn btn-xs btn-danger\">삭제</span>
   			   </a>";
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>