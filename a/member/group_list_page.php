<?
include "../lib.php";

### 그룹별 회원수 추출
$m_member = new M_member();
$cnt = $m_member->getGrpGroupByCount($cid);

$order_column_arr = array("grpno","grpnm", "", "", "grplv", "", "", "");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$totalCnt = count($m_member -> getGrpList($cid));

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_member -> getGrpList($cid, $addwhere, $orderby, $limit);

foreach ($list as $key => $value) {
   $pdata = array();
   $pdata[] = $value[grpno];
   $pdata[] = $value[grpnm];
   $pdata[] = $value[grpdc];
   $pdata[] = $value[grpsc];
   $pdata[] = $value[grplv];
   $pdata[] = ($cnt[$value[grpno]]) ? $cnt[$value[grpno]]." "._("명") : "0 "._("명");

   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"group_popup('".$value[grpno]."');\">"._("수정")."</button>";
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"group_delete('".$value[grpno]."');\">"._("삭제")."</button>";

   $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>