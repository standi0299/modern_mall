<?
include "../lib.php";

$getData = json_decode(base64_decode($_GET[postData]),1);

if ($getData[start])
    $addwhere .= " and regist_date >= '$getData[start] 00:00:00'";
if ($getData[end])
    $addwhere .= " and regist_date <= '$getData[end] 23:59:59'";

if($getData[select])
    $addwhere .= " and account_flag = '$getData[select]'";

$order_column_arr = array("regist_date","account_flag", "account_point", "account_point", "mall_point", "m_name", "payno");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$addwhere .= $orderby;

$sql = "select * from tb_pretty_account_point_history where cid='$cid' $addwhere limit $_POST[start], $_POST[length]";
$res = $db -> query($sql);
while ($data = $db -> fetch($res)) {
    $list[] = $data;
}

list($totalCnt) = $db->fetch("select count(*) as cnt from tb_pretty_account_point_history where cid='$cid' $addwhere",1);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

//debug($list);
//$self = $_SERVER["HTTP_HOST"];

foreach ($list as $key => $value) {

   $pdata = array();
   $pdata[] = substr($value[regist_date],0,10);
   $pdata[] = $r_pretty_point_account_flag[$value[account_flag]];
   
   if(!in_array($value[account_flag], $r_pretty_point_list_chk_flag))
   {
      $pdata[] = number_format($value[account_point]);
      $pdata[] = "-";
   }
   else
   {
      $pdata[] = "-";
      $pdata[] = number_format($value[account_point]);
   }
   
   $pdata[] = number_format($value[mall_point]);
   
   $pdata[] = $value[memo].$value[m_name];
   
   if ($value[account_flag] < 20)
      $pdata[] = "<a href=\"javascript:popup('order_detail_popup.php?payno=$value[payno]',1200,750);\">$value[payno]</a>";
   else
      $pdata[] = "<a href=\"javascript:popup('popup_charge_detail.php?tno=$value[tno]&mid=$value[mid]',300,250);\">$value[tno]</a>";

   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>