<?
include "../lib.php";

$m_goods = new M_goods();

$r_brand = get_brand();
$r_rid = get_release();

$getData = json_decode(base64_decode($_GET[postData]),1);

$order_column_arr = array("goodsno", "", "goodsnm", "", "", "", "", "", "", "");
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

if($getData[end_date]){
   if($addWhere) $and = "and";
   $addWhere .= " $and regdt <= '$getData[end_date] 23:59:59' ";
}

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_goods->getAdminGoodsList($cid, $addWhere, $orderby, $limit);

$list_cnt = $m_goods->getAdminGoodsList($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   
   $catnm = getCatnm($value[catno]);
   $goods_bid = getBidGoods($value[goodsno]);

   $pdata = array();
   //$pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[goodsno]\">";
   
   $goodsno_data = $value[goodsno];
   if ($value[shop_id])
      $goodsno_data."<div class=\"green\">".$value[shop_code]."</div>";
   if ($value[state]) 
      $goodsno_data."<div class=\"small\" style=\"color:".$r_state_color[$value[state]]."\">[".$r_goods_state[$value[state]]."]</div>";

   $pdata[] = $goodsno_data;
   
   $pdata[] = goodsListImg($value[goodsno],50,"border:1px solid #CCCCCC",$cid);

   $goodsnm_data = "<a href=\"goods_modify.php?goodsno=$value[goodsno]\">$value[goodsnm]</a>";
   if ($cid=="phob" && $value[goodsnm_deco])
      $goodsnm_data .= "<div style=\"margin-top:3px;\">".$value[goodsnm_deco]."</div>";
   if ($catnm)
      $goodsnm_data .= "<div>".$catnm."</div>";
   if ($goods_bid)
      $goodsnm_data .= "<div>"._("노출그룹")." : ".$goods_bid."</div>";
   
   if ($value[icon_filename]) {
   	  $goodsnm_data .= "<div>".getIcon($cid, $value[icon_filename])."</div>";
   }
   
   $pdata[] = $goodsnm_data;
   
   $pdata[] = "<div style=\"margin:4px 0;\">".$r_rid[$value[rid]]."</div><div style=\"margin:4px 0;\">".$r_brand[$value[brandno]]."</div>";
   
   $price_data = "";
   if ($value[mall_cprice] && $value[mall_cprice] > $value[price])
      $price_data = "<s>".number_format($value[mall_cprice])."</s>";
   $price_data .= "<div><b style=\"color:#28a5f9;\">".number_format($value[price])."</b></div>";
   
   $pdata[] = $price_data;
   
   $pdata[] = number_format($value[reserve]);
   $pdata[] = number_format($value[shipprice]);
   $pdata[] = substr($value[regdt],0,10);
   //$pdata[] = "<a href=\"price.p.php?goodsno=$value[goodsno]\"><span class=\"btn btn-warning btn-icon btn-circle\"><i class=\"fa fa-check\"></i></span>";
   $pdata[] = "<a href=\"goods_modify.php?goodsno=$value[goodsno]\"><button type=\"button\" class=\"btn btn-xs btn-primary\">수정</button>";
   $pdata[] = "<a href=\"indb.php?mode=delGoods&goodsno=$value[goodsno]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\"><button type=\"button\" class=\"btn btn-xs btn-danger\">"._("삭제")."</button>";
   

   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>