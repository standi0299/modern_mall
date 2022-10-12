<?
include "../lib.php";

$m_order = new M_order();

$self = $_SERVER["HTTP_HOST"];

$order_column_arr = array("payno","", "", "", "orddt", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$search_data = $_POST[search][value];
if($search_data) $addWhere = "and payno = '$search_data'";

$totalCnt = $m_order->getNonmemberConnect($cid, $addWhere, "cnt");

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

### 결제방법색상
$r_paymethod_color = array(
   'c' => 'red',
   'b' => 'blue',
   'e' => 'green',
   'v' => 'sky',
   'o' => 'orange',
   'h' => 'pink'
);

$list = $m_order->getNonmemberConnect($cid, $addWhere, '', $orderby, $_POST[start], $_POST[length]);  

foreach ($list as $key => $value) {
   $pdata = array();

   $pdata[] = $value[payno];
   $pdata[] = "<span class=\"".$r_paymethod_color[$value[paymethod]]."\">".$r_paymethod[$value[paymethod]]."</span>";
   $pdata[] = number_format($value[payprice])."원";
   $pdata[] = "[".$value[receiver_zipcode]."]"." ".$value[receiver_addr]." ".$value[receiver_addr_sub];
   $pdata[] = $value[orddt];
   $pdata[] = "<input type=\"text\" class=\"form-control\" name=\"connectName[$value[payno]]\">";

   $psublist[] = $pdata;
}

$plist[data] = $psublist;
//debug($plist);
//exit;
echo json_encode($plist)
?>