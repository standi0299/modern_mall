<?
include "../lib.php";

$m_goods = new M_goods();

$r_brand = get_brand();
$r_rid = get_release();

$getData = json_decode(base64_decode($_GET[postData]),1);

$order_column_arr = array("", "goodsno", "", "goodsnm", "", "", "", "", "regdt", "", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$search_data = $_POST[search][value];
$addWhere = "";
if ($search_data) {
   $addWhere .= " and (a.goodsno like '%$search_data%' or a.goodsnm like '%$search_data%')";
}

if ($getData[start_date]) {
   $addWhere .= " and regdt >= '$getData[start_date] 00:00:00'";
}

if($getData[end_date]){
   $addWhere .= " and regdt <= '$getData[end_date] 23:59:59' ";
}

if($getData[catno]){
  //$addWhere .= " and c.catno = '{$getData[catno]}' ";
	$addWhere .= "and (
	select
		goodsno
	from
		exm_goods_link
	where	
    cid = '$cid'
		and catno = '{$getData[catno]}'
		and goodsno = a.goodsno limit 1
	)";
}

if($getData[state]){
   $addWhere .= " and state = '{$getData[state]}' ";
}


$limit = "limit $_POST[start], $_POST[length]";
$list = $m_goods->getAdminSelfGoodsList($cid, $addWhere, $orderby, $limit);
$list_cnt = $m_goods->getAdminSelfGoodsList($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
	//debug($value);
	
	//$catnm = getCatnm($value[catno]);
	$r_catnm = array();
	$catnm = "";
	$catdata = $m_goods->getLinkCategoryDataList($cid, $value[goodsno]);
	//debug($catdata);
    foreach ($catdata as $k => $v) {
        $r_catnm[] = $v[catnm];
    }
    if ($r_catnm) $catnm = implode(" > ",$r_catnm);    

    $pdata = array();
    $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[goodsno]\">";

    $goodsno_data = $value[goodsno];
    if ($value[shop_id])
        $goodsno_data."<div class=\"green\">".$value[shop_code]."</div>";
    if ($value[state])
        $goodsno_data."<div class=\"small\" style=\"color:".$r_state_color[$value[state]]."\">[".$r_goods_state[$value[state]]."]</div>";

    $pdata[] = $goodsno_data;

    //$pdata[] = goodsListImg($value[goodsno],50,"border:1px solid #CCCCCC",$cid);
    $pdata[] = "<a href=\"../../goods/view.php?goodsno=$value[goodsno]\" target=\"_blank\">".goodsListImg($value[goodsno],50,"border:1px solid #CCCCCC",$cid)."</a>";

    $goodsnm_data = "<a href=\"self_goods_modify.php?goodsno=$value[goodsno]\">$value[goodsnm]</a>";
    if ($cid=="phob" && $value[goodsnm_deco])
        $goodsnm_data .= "<div style=\"margin-top:3px;\">$value[goodsnm_deco]</div>";
    if ($catnm)
        $goodsnm_data .= "<div><$catnm</div>";
    if ($goods_bid)
        $goodsnm_data .= "<div>"._("노출그룹")." : <$goods_bid</div>";

    if ($value[icon_filename]) {
   	    $goodsnm_data .= "<div>".getIcon($cid, $value[icon_filename])."</div>";
    }

    $pdata[] = $goodsnm_data;

    $pdata[] = "<div style=\"margin:4px 0;\">".$r_rid[$value[rid]]."</div><div style=\"margin:4px 0;\" class=\"green\">".$r_brand[$value[brandno]]."</div>";

    $price_data = "";
    if ($value[mall_cprice] && $value[mall_cprice] > $value[price])
        $price_data .= "<s>".number_format($value[mall_cprice])."</s>";

    $price_data .= "<div><b style=\"color:#28a5f9;\">".number_format($value[price])."</b></div>";

    $pdata[] = $price_data;

    $pdata[] = substr($value[regdt],0,10);
    $pdata[] = "<a href=\"javascript:popup('popup_set_option_img.php?goodsno=$value[goodsno]', 800, 800)\"><button type=\"button\" class=\"btn btn-xs btn-primary\">"._("등록")."</button></a>";
    $pdata[] = "<a href=\"indb.php?mode=copyGoods&goodsno=$value[goodsno]\" onclick=\"return confirm('"._("이미지를 제외한 데이터가 복사됩니다.")."\n"._("상품 복사를 진행하시겠습니까?")."')\">
                  <span class=\"btn btn-warning btn-icon btn-circle\"><i class=\"fa fa-plus\"></i></span>
               </a>";

    $mod_button = "<a href=\"self_goods_modify.php?goodsno=$value[goodsno]\">
                  <button type=\"button\" class=\"btn btn-xs btn-primary\">"._("수정")."</button>
               </a>";

    //견적관리 링크 추가.
    if ($value[goods_group_code] == "60") {
        $alink = $r_est_print_product_admin_link[$value[extra_option]];
           $mod_button .= "<br><a href=\"$alink?goodsno=$value[goodsno]\">
                  <button type=\"button\" class=\"btn btn-xs btn-info\">"._("견적항목수정")."</button>
               </a>";
    }

    $pdata[] = $mod_button; 

    $pdata[] = "<a href=\"indb.php?mode=delselfGoods&goodsno=$value[goodsno]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\">
                  <button type=\"button\" class=\"btn btn-xs btn-danger\">"._("삭제")."</button>
               </a>";

    $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>