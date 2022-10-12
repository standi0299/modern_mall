<?
include "../lib.php";

$m_goods = new M_goods();

$r_brand = get_brand();
$r_rid = get_release();

$getData = json_decode(base64_decode($_GET[postData]),1);

$order_column_arr = array("", "goodsno", "", "goodsnm", "", "", "", "", "", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$search_data = $_POST[search][value];
$addWhere = "";
if ($getData[sword]) {
   $addWhere .= " concat(goodsnm,a.goodsno,goodsnm_deco,goodsnm_deco_out) like '%$getData[sword]%'";
}

if ($getData[catno]){
   if (is_numeric($getData[catno])){
      if($addWhere) $and = "and";
      if (is_numeric($getData[catno])) $addWhere .= ($getData[category_like_off]) ? " $and c.catno = '$getData[catno]'":" $and c.catno like '$getData[catno]%'";
   }
}

if ($getData[iscat]){
   if($addWhere) $and = "and";
   $addWhere .= " $and (c.catno is null or c.catno = '')";
}

if ($getData[rid]){
   if($addWhere) $and = "and";
   $addWhere .= " $and rid = '$getData[rid]'";
}

if ($getData[brandno]){
   if($addWhere) $and = "and";
   $addWhere .= " $and brandno = '$getData[brandno]'";
}

if (array_notnull($getData[price])){
   if($addWhere) $and = "and";
   if ($getData[price][0]+0) $addWhere .= " $and if(b.price is null,a.price,b.price)>='{$getData[price][0]}'";
   if ($getData[price][1]+0) $addWhere .= " $and if(b.price is null,a.price,b.price)<='{$getData[price][1]}'";
}

if (is_numeric($getData[state])){
   if($addWhere) $and = "and";
   $addWhere .= " $and state = $getData[state]";
}

if ($getData[isdp]){
   if($addWhere) $and = "and";
   $addWhere .= " $and nodp = '$getData[isdp]'";
}

if (array_notnull($getData[totstock])){
   if($addWhere) $and = "and";
   if ($getData[totstock][0]+0) $addWhere .= " $and totstock>='{$getData[totstock][0]}'";
   if ($getData[totstock][1]+0) $addWhere .= " $and totstock<='{$getData[totstock][1]}'";
}

if ($getData[shiptype]){
   if($addWhere) $and = "and";
   $addWhere .= " $and shiptype = '$getData[shiptype]'";
}

if ($getData[start_date]) {
   if($addWhere) $and = "and";
   $addWhere .= " $and regdt >= '$getData[start_date] 00:00:00'";
}

if($getData[end_date]) {
   if($addWhere) $and = "and";
   $addWhere .= " $and regdt <= '$getData[end_date] 23:59:59' ";
}

$limit = "limit $_POST[start], $_POST[length]";
$list = $m_goods->getAdminGoodsList($cid, $addWhere, $orderby, $limit);

$list_cnt = $m_goods->getAdminGoodsList($cid, $addWhere);
$totalCnt = count($list_cnt);

$query = "select a.*, b.cid from 
          exm_goods a 
          left join exm_goods_cid b on b.goodsno = a.goodsno and b.cid = '$cid'
          where b.cid is null 
          $addWhere";

if ($addWhere)
    $addWhere = "where " . $addWhere;

$query = "select a.*,b.price,b.nodp,if(b.price is null,a.price,b.price) price, b.reserve, b.clistimg, b.csummary, c.catno, b.goodsnm_deco, b.mall_cprice, b.icon_filename, d.priority";
$query .= " from exm_goods a 
  inner join exm_goods_cid b on b.cid = '$cid' and a.goodsno=b.goodsno 
  left join exm_goods_link c on c.cid = '$cid' and a.goodsno=c.goodsno
  left join md_goods_sort d on d.cid = '$cid' and d.goodsno = a.goodsno and d.sort = '$sort'";
$query .= " $addWhere group by a.goodsno";

$query = base64_encode($query);

//echo ($query);
//exit;

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;


foreach ($list as $key => $value) {
   
   $catnm = getCatnm($value[catno]);
   $goods_bid = getBidGoods($value[goodsno]);

   $pdata = array();
   $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[goodsno]\"><input type=\"hidden\" name=\"query[]\" value=\"$query\">";
   
   $goodsno_data = $value[goodsno];
   if ($value[shop_id])
      $goodsno_data."<div class=\"green\">".$value[shop_code]."</div>";
   if ($value[state]) 
      $goodsno_data."<div class=\"small\" style=\"color:".$r_state_color[$value[state]]."\">[".$r_goods_state[$value[state]]."]</div>";

   $pdata[] = $goodsno_data;
   
   //$pdata[] = goodsListImg($value[goodsno],50,"border:1px solid #CCCCCC",$cid);
   $pdata[] = "<a href=\"../../goods/view.php?goodsno=$value[goodsno]\" target=\"_blank\">".goodsListImg($value[goodsno],50,"border:1px solid #CCCCCC",$cid)."</a>";

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
   
   //$pdata[] = "<a href=\"javascript:popup('popup_set_option_img.php?goodsno=$value[goodsno]', 800, 800)\"><button type=\"button\" class=\"btn btn-xs btn-primary\">"._("등록")."</button></a>";
   $pdata[] = "<a href=\"goods_modify.php?goodsno=$value[goodsno]\"><button type=\"button\" class=\"btn btn-xs btn-primary\">"._("수정")."</button></a>";
   $pdata[] = "<a href=\"indb.php?mode=delGoods&goodsno=$value[goodsno]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\"><button type=\"button\" class=\"btn btn-xs btn-danger\">"._("삭제")."</button></a>";
   

   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>