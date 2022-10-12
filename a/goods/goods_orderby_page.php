<?
include "../lib.php";

$m_goods = new M_goods();

$r_brand = get_brand();
$r_rid = get_release();

$getData = json_decode(base64_decode($_GET[postData]),1);

$order_column_arr = array("goodsno", "", "", "", "d.priority");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$search_data = $_POST[search][value];
$addWhere = "";
if ($search_data) {
   $addWhere .= "(a.goodsno like '%$search_data%' or a.goodsnm like '%$search_data%')";
}

if ($getData[start_date]) {
   if($addWhere) $and = "and";
   $addWhere .= " $and regdt >= '$getData[start_date] 00:00:00'";
}

if($getData[catno]){
   if($addWhere) $and = "and";
   $addWhere .= " $and c.catno = '$getData[catno]'";
}

if($getData[end_date]){
   if($addWhere) $and = "and";
   $addWhere .= " $and regdt <= '$getData[end_date] 23:59:59'";
}

if(!$getData['sort']) $getData['sort'] = "all";

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_goods->getAdminGoodsList($cid, $addWhere, $orderby, $limit, $getData['sort']);

$list_cnt = $m_goods->getAdminGoodsList($cid, $addWhere, "", "");
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;
//debug($list);
foreach ($list as $key => $value) {
   
   $catnm = getCatnm($value[catno]);
   $goods_bid = getBidGoods($value[goodsno]);

   $pdata = array();

   $goodsno_data = $value[goodsno];
   if ($value[shop_id])
      $goodsno_data."<div class=\"green\">".$value[shop_code]."</div>";
   if ($value[state]) 
      $goodsno_data."<div class=\"small\" style=\"color:".$r_state_color[$value[state]]."\">[".$r_goods_state[$value[state]]."]</div>";

   $pdata[] = $goodsno_data;
   $pdata[] = goodsListImg($value[goodsno],50,"border:1px solid #CCCCCC",$cid);
   $pdata[] = $value[goodsnm];
   $pdata[] = $value[regdt];
   $pdata[] = "<input type=\"text\" class=\"form-control\" name=\"orderby[$value[goodsno]]\" value=\"$value[priority]\">";

   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>