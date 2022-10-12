<?
include "../lib.php";

$m_member = new M_member();

$order_column_arr = array("pushno", "", "", "", "", "", "", "", "");
$order_data = $_POST[order][0];
$orderby = "order by a.".$order_column_arr[$order_data[column]]." ".$order_data[dir];

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_member -> getMobilePushList($cid, $orderby, $limit);
$totalCnt = count($m_member -> getMobilePushList($cid));

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();
	
   $pdata[] = $value[pushno];
   $pdata[] = $value[push_title];
   $pdata[] = $value[push_message];
   $pdata[] = substr($value[regdt], 0, 10);
   $pdata[] = ($value[senddt] > 0) ? $value[senddt] : "";
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-info\" onclick=\"mobile_push_send('".$value[pushno]."');\">"._("발송")."</button>";
   $pdata[] = ($value[resendCnt] > 0) ? "<button type=\"button\" class=\"btn btn-xs btn-info\" onclick=\"mobile_push_resend('".$value[pushno]."');\">"._("재발송")."</button>" : _("없음");
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"mobile_push_popup('".$value[pushno]."');\">"._("수정")."</button>";
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"mobile_push_delete('".$value[pushno]."');\">"._("삭제")."</button>";

   $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>