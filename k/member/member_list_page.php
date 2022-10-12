<?
include "../lib.php";

$m_member = new M_member();
$m_pretty = new M_pretty();
$r_state = array(_("승인"),_("미승인"),_("차단"));
### 회원그룹 추출
$r_grp = getMemberGrp();

$search_data = $_POST[search][value];
if ($search_data) {
   $addwhere .= " and (name like '%$search_data%' or mid like '%$search_data%')";
}

$order_column_arr = array("","regdt", "mid", "name", "", "", "", "", "", "", "");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$totalCnt = count($m_pretty -> getMemberListA($cid));

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_pretty -> getMemberListA($cid, $addwhere, $orderby, $limit);

foreach ($list as $key => $value) {
   $pdata = array();
   $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[mid]\">";
   $pdata[] = substr($value[regdt],0,10);
   
   $sns = "";
   $sns_data = $m_member->getLogSnsLogin($cid,$value[sns_id]);
   if($sns_data) {
   	$sns = "&nbsp;&nbsp;&nbsp;<img src='../img/$sns_data[sns_type].png' height='20px' alt='$sns_data[sns_id]' />";
   }   
   
   $pdata[] = $value[mid].$sns;
   $pdata[] = $value[name];
   $pdata[] = ($value[mobile])?$value[mobile]:$value[phone];
   $pdata[] = $r_grp[$value[grpno]];
   //$pdata[] = "<u onclick=\"popup('orderlist.p.php?mid=$value[mid]',630,420)\" class=\"hand\">".number_format($value[totpayprice])._("원")."</u>";
   $pdata[] = "<u onclick=\"popup('member_detail_popup.php?mode=member_modify&mid=$value[mid]',1100,800)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[totpayprice])._("원")."</u>";
   
   $pdata[] = $r_state[$value[state]];

   if ($value[member_type] == "FIX") 
      $pdata[] = "<font color=red>"._("정액")."</b>";
   else
      $pdata[] = _("일반");

   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"location.href='member_modify.php?mid=$value[mid]';\">수정</button>";
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\">삭제</button>";

   $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>