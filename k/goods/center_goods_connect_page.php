<?
include "../lib.php";

$m_goods = new M_goods();

$r_brand = get_brand();
$r_rid = get_release();

$getData = json_decode(base64_decode($_GET[postData]),1);

$order_column_arr = array("", "goodsno", "", "goodsnm", "", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$search_data = $_POST[search][value];
$addWhere = "";
if ($search_data) {
   $addWhere .= " and (a.goodsno like '%$search_data%' or a.goodsnm like '%$search_data%')";
}

if($getData[catno]) {
   if (is_array($getData[catno])){
      $getData[catno] = array_notnull($getData[catno]);
      list($getData[catno]) = array_slice($getData[catno],-1);
   }
   
   if (is_numeric($getData[catno])){
      $addWhere .= " and (
      select
         goodsno
      from
         exm_goods_link
      where
         cid = '$cfg_center[center_cid]'
         and catno like '$getData[catno]%'
         and goodsno = a.goodsno limit 1
      )
      ";
   }
}

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_goods->getAdminCenterGoodsConnectList($cid, $addWhere, $orderby, $limit);

$list_cnt = $m_goods->getAdminCenterGoodsConnectList($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {

   $pdata = array();
   $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[goodsno]\">";
   
   $goodsno_data = $value[goodsno];
   if ($value[shop_id])
      $goodsno_data."<div>".$value[shop_code]."</div>";
   if ($value[state]) 
      $goodsno_data."<div style=\"color:".$r_state_color[$value[state]]."\">[".$r_goods_state[$value[state]]."]</div>";

   $pdata[] = $goodsno_data;
   
   $pdata[] = goodsListImgCenter($value[goodsno],50,"","border:1px solid #CCCCCC");

   $pdata[] = $value[goodsnm];
   
   $pdata[] = "<div style=\"margin:4px 0;\">".$r_rid[$value[rid]]."</div><div style=\"margin:4px 0;\">".$r_brand[$value[brandno]]."</div>";

   $price_data = "";
   if ($value[cprice] && $value[cprice] > $value[price])
      $price_data .= "<s>".number_format($value[cprice])."</s>";
   $price_data .= "<div><b style=\"color:#28a5f9;\">".number_format($value[price])."</b></div>";
   $price_data .= "<div>(".number_format($value[reserve]).")</div>";

   $pdata[] = $price_data;
   
   $pdata[] = substr($value[regdt],0,10);
   
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>