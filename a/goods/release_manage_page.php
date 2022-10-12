<?
include "../lib.php";

$m_goods = new M_goods();

$r_brand = get_brand();
$r_rid = get_release();

$getData = json_decode(base64_decode($_GET[postData]),1);

$orderby = "order by regdt desc";

$order_column_arr = array("", "rid", "compnm", "nicknm", "", "", "");
$order_data = $_POST[order][0];
if($order_data)
   $orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$search_data = $_POST[search][value];

if ($search_data) {
   $addWhere = "and (rid like '%$search_data%' or compnm like '%$search_data%' or nicknm like '%$search_data%')";
}

$limit = "limit $_POST[start], $_POST[length]";
$query = "select * from exm_release where cid = '$cid' and hide = 0 $addWhere $orderby $limit";

$list = $db->listArray($query);

$totalCnt = count($list);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;
$i=1;
foreach ($list as $key => $value) {
   $pdata = array();

   $pdata[] = $i;

   $release_rid ="<a href=\"release_modify.php?rid=$value[rid]\">$value[rid]</a>";
   if ($value[mall_release]) {
      $release_rid .= "<div><b>"._("몰 자체제작사")."</b></div>";
   }
   $pdata[] = $release_rid;
   
   $pdata[] = "<a href=\"release_modify.php?rid=$value[rid]\">$value[compnm]</a>";
   $pdata[] = $value[nicknm];
   $pdata[] = $value[phone];
   //$pdata[] = $value[address]." ".$value[address_sub];
   $pdata[] = "<a href=\"release_modify.php?rid=$value[rid]\">
               <button type=\"button\" class=\"btn btn-xs btn-primary\">"._("수정")."</button>
               </a>";
   $pdata[] = "<a href=\"indb.php?mode=delRelease&rid=$value[rid]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\">
               <button type=\"button\" class=\"btn btn-xs btn-danger\">"._("삭제")."</button>
               </a>";
	$pdata[] = "<a href=\"/a/config/shipping_extra.php?rid=$value[rid]\" target='_blank'>
               <button type=\"button\" class=\"btn btn-xs btn-primary\">"._("추가배송비")."</button>
               </a>";
   $psublist[] = $pdata;
   $i++;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>