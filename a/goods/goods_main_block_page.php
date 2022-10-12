<?
include "../lib.php";

###메인화면 상품 블럭 설정###

$m_goods = new M_goods();

$r_brand = get_brand();
$r_rid = get_release();

$r_nodp = array(_("진열"), _("미진열"));

$getData = json_decode(base64_decode($_GET[postData]),1);
//debug($getData);

//$order_column_arr = array("","seq", "", "", "", "regdt", "", "");
$order_column_arr = array("seq", "", "", "", "regdt", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$addWhere = " where a.cid='$cid' ";
if ($getData[block_code]){
	$addWhere .= " and a.dpno='$getData[block_code]'";
}
else {
	$addWhere .= " and a.dpno='main_block_01'";
}

if ($getData[sword]) {
   	$addWhere .= " and (c.goodsno like '%$getData[sword]%' or c.goodsnm like '%$getData[sword]%')";
}

if ($getData[state]){
	$addWhere .= " and state = '$getData[state]'";
}

if (is_numeric($getData[isdp])) {
	$addWhere .= " and nodp = '$getData[isdp]'";
}

if (array_notnull($getData[price])){
	if ($getData[price][0]+0) {
		$addWhere .= " and if(b.price is null,c.price,b.price)>='{$getData[price][0]}'";
	}
	
	if ($getData[price][1]+0) {
		$addWhere .= " and if(b.price is null,c.price,b.price)<='{$getData[price][1]}'";
	}
}

if ($getData[start_date]) {
   $addWhere .= " and c.regdt >= '$getData[start_date] 00:00:00'";
}

if($getData[end_date]){
   $addWhere .= " and c.regdt <= '$getData[end_date] 23:59:59' ";
}
//debug($addWhere);


$limit = "limit $_POST[start], $_POST[length]";
$list = $m_goods->getAdminMainBlockGoodsList($cid, $addWhere, $orderby, $limit);

$list_cnt = $m_goods->getAdminMainBlockGoodsList($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   
   $catnm = getCatnm($value[catno]);
   $goods_bid = getBidGoods($value[goodsno]);

   $pdata = array();
   //$pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[goodsno]\">";

   $pdata[] = $value[seq];

   $pdata[] = goodsListImg($value[goodsno],50,"border:1px solid #CCCCCC",$cid);

   $goodsnm_data = "<a href=\"goods_modify.php?goodsno=$value[goodsno]\">$value[goodsnm]</a>";
   if ($cid=="phob" && $value[goodsnm_deco])
      $goodsnm_data .= "<div style=\"margin-top:3px;\">".$value[goodsnm_deco]."</div>";
   if ($catnm)
      $goodsnm_data .= "<div>".$catnm."</div>";
   if ($goods_bid)
      $goodsnm_data .= "<div>"._("노출그룹")." : ".$goods_bid."</div>";

   $goodsno_data = "<div>( ".$value[goodsno]." )</div>";
   if ($value[shop_id])
      $goodsno_data."<div class=\"green\">".$value[shop_code]."</div>";
   if ($value[state]) 
      $goodsno_data."<div class=\"small\" style=\"color:".$r_state_color[$value[state]]."\">[".$r_goods_state[$value[state]]."]</div>";

   $pdata[] = $goodsnm_data.$goodsno_data;

   $price_data = "";
   if ($value[mall_cprice] && $value[mall_cprice] > $value[price])
      $price_data = "<s>".number_format($value[mall_cprice])."</s>";
   $price_data .= "<div><b style=\"color:#28a5f9;\">".number_format($value[price])."</b></div>";

   $pdata[] = $price_data;

   $pdata[] = substr($value[regdt],0,10);

   $pdata[] = $r_nodp[$value[nodp]];
   $pdata[] = $r_goods_state[$value[state]];
   
   //$pdata[] = "<a href=\"goods_modify.php?goodsno=$value[goodsno]\"><button type=\"button\" class=\"btn btn-xs btn-primary\">수정</button>";
   //$pdata[] = "<a href=\"indb.php?mode=delGoods&goodsno=$value[goodsno]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\"><button type=\"button\" class=\"btn btn-xs btn-danger\">"._("삭제")."</button>";

   $psublist[] = $pdata;
   
}

$plist[data] = $psublist;

echo json_encode($plist)
?>