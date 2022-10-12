<?
include "../lib.php";

$m_order = new M_order();

$order_column_arr = array("regist_date","account_flag", "account_point", "account_point", "mall_point", "m_name", "payno");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$search_data = $_POST[search][value];
if ($search_data) {
   $addWhere .= " and (a.payno like '%$search_data%' or b.orderer_name like '%$search_data%')";
}

$addWhere = " and a.state = '$_GET[state]'";
$limit = " limit $_POST[start], $_POST[length]";
$list = $m_order->getRefundData($cid, $addWhere, $limit);

list($totalCnt) = $db->fetch("select count(b.payno)  from exm_refund a inner join exm_pay b on a.payno = b.payno where b.cid = '$cid' and a.state = '$_GET[state]'",1);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

if($_GET[state] == '1')
      $disabled = "disabled";

foreach ($list as $key => $value) {

   $completedt = $value[completedt] ? substr($value[completedt],2,14)." (".$value[complete_admin].")" : "-";
   
   $pdata = array();

   $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[refundno]\" $disabled></td>";
   $pdata[] = "";
   $pdata[] = "<b class=\"red\"><a href=\"javascript:;\" onclick=\"popup('order.p.php?payno=$value[payno]',1000,750)\"><b>".$value[payno]."</b></a></b></td>";
   $pdata[] = $value[orderer_name];
   $pdata[] = number_format($value[cash]+$value[pg]+$value[emoney]+$value[custom]+$value[credit2]);
   $pdata[] = number_format($value[cash]);
   $pdata[] = number_format($value[pg]);
   $pdata[] = number_format($value[emoney]);
   $pdata[] = number_format($value[custom]);
   $pdata[] = substr($value[requestdt],2,14) ."(".$value[request_admin].")
              <div>". $completedt ."</div>";
   $pdata[] = "<a href=\"refund_modify.php?state=$value[state]&refundno=$value[refundno]\"><button type=\"button\" class=\"btn btn-xs btn-danger\">"._("수정")."</button></a>";

   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>