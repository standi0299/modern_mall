<?
$viewpage = true;
$podspage = true;
include "../_header.php";

$editorLayer = 1;

$bid_where = ($sess[bid]) ? "(bid.bid is null or bid.bid = '$sess[bid]')":"bid.bid is null";

$query = "
	select
	a.*,b.*,if(b.price is null,a.price,b.price) price,
	if(b.`desc` ='',a.`desc`,b.`desc`) `desc`,
	if(b.mall_pageprice is null,a.pageprice,b.mall_pageprice) pageprice
	from
	exm_goods a
	inner join exm_goods_cid b on cid = '$cid' and a.goodsno = b.goodsno
	left join exm_goods_bid bid on bid.cid = '$cid' and bid.goodsno = a.goodsno
	where
	a.goodsno = '$_GET[goodsno]'
	and $bid_where
	";

$data = $db->fetch($query);
//drawquery($query);
if (!$data[goodsno]){
	msg(_("상품이 존재하지 않습니다."),-1);
	exit;
}

if (!$_GET[cartno]){
	msg(_("장바구니 정보가 없습니다."),-1);
	exit;
}
  
//장바구니에서 수정 모드로 상품상세로 넘어 왔을 경우.
list($est_title, $orderOptionData, $storageid, $est_order_memo,$orderOptionDesc) = $db -> fetch("select title, est_order_data, storageid, est_order_memo,est_order_option_desc from exm_cart where cartno = '$_GET[cartno]'", 1);
//$orderOptionData = strDelEntTab($orderOptionData);
$orderOptionDesc = strDelEntTab($orderOptionDesc);
//debug($orderOptionDesc);
if (!$orderOptionDesc) {
	msg(_("옵션 정보가 없습니다."), -1);
	exit ;
}

$data[est_order_option_desc] = optionDescStr($orderOptionDesc);
//debug($data[est_order_option_desc]);
$data[cartno] = $_GET[cartno];
$data[storageid] = $storageid;
$data[est_title] = $est_title;
$data[est_order_memo] = $est_order_memo;

$tpl->define('header', 'layout/header.popup.htm');
$tpl->assign($data);
$tpl->print_('tpl');
?>