<?
include "../lib.php";


$getData = json_decode(base64_decode($_GET[postData]),1);


$m_config = new M_config();

$search_data = $_POST[search][value];
$addWhere = "";
if ($search_data) $addWhere .= " and (zipcode like '%$search_data%' or address like '%$search_data%')";

$orderby = "order by zipcode desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_config->getShippingExtraInfo($getData[rid], '', $addWhere, $orderby, $limit);
$list_cnt = $m_config->getShippingExtraInfo($getData[rid], '', $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();

   $pdata[] = $totalCnt-$key;
   $pdata[] = $value[zipcode];
   $pdata[] = $value[address];
   $pdata[] = $value[add_price];
   $pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"popup('shipping_extra_popup.php?rid={$getData[rid]}&zipcode=$value[zipcode]', 630, 405)\">"._("수정")."</button>";
   $pdata[] = "<a href=\"indb.php?mode=del_shipping_extra&rid={$getData[rid]}&zipcode=$value[zipcode]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\">
   			   <span class=\"btn btn-xs btn-danger\">삭제</span>
   			   </a>";
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>