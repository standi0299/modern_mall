<?
$viewpage = true;
$podspage = true;
include "../_header.php";
include "../lib/class.page.php";

$editorLayer = 1;

$bid_where = ($sess[bid]) ? "(bid.bid is null or bid.bid = '$sess[bid]')" : "bid.bid is null";

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
if (!$data[goodsno]) {
	msg(_("상품이 존재하지 않습니다."), -1);
	exit;
}

if (!$_GET[cartno]) {
	msg(_("장바구니 정보가 없습니다."), -1);
	exit;
}
  
//장바구니에서 수정 모드로 상품상세로 넘어 왔을 경우.
list($est_title, $orderOptionData, $storageid, $est_order_memo) = $db->fetch("select title, est_order_data, storageid, est_order_memo from exm_cart where cartno = '$_GET[cartno]'", 1);
//$orderOptionData = str_replace("\r\n", "", $orderOptionData);
$orderOptionData = strDelEntTab($orderOptionData);

if (!$orderOptionData) {
	msg(_("옵션 정보가 없습니다."), -1);
	exit;
}

$data[cartno] = $_GET[cartno];
$data[storageid] = $storageid;
$data[est_title] = $est_title;
$data[est_order_memo] = $est_order_memo;

//상품정보에서 자동견적옵션 조회
//debug($data);
$extra_option = explode('|', $data[extra_option]); //항목 분리
if (count($extra_option) > 0) {
	$data[goodskind] = $extra_option[0];
	$data[preset] = $extra_option[1];
}

//debug($data);
//debug($orderOptionData);
$orderOptionDataJson = json_decode($orderOptionData);
//debug($orderOptionDataJson);

//자동견적 사용자 화면을 관리자 화면 프리셋 방식으로 2015.03.31 by kdk
$extraOption = new ExtraOption();
$extraOption->SetGoodsKind($data[goodskind]);
$extraOption->SetPreset($data[preset]);

$optionKindCodeArr = $extraOption->getOptionKind($_GET[goodsno]); 
//$afterOptionKindIndex = $extraOption->getAfterOptionKindIndex();
$afterOptionSelectArr = $extraOption->GetAfterOptionData();
$extraOption->getOrderMemoUse($_GET[goodsno]);
$extraOption->getExtraTblKindCode();
$extraOption->getOptionGroupType();

if ($extraOption->Preset == "100112") {
	$_GET[optmode] = "detail";
}

//debug($optionKindCodeArr);
//debug($extraOption->DocumentSizeScriptTag);
//debug($afterOptionKindIndex);
//debug($extraOption->Preset);
//debug($extraOption->GoodsKind);
//debug($extraOption->OrderMemoUse);
//debug($extraOption->ExtraTblKindCodeArr);
//debug($extraOption->OptionGroupTypeArr);

if ($extraOption->Preset == "100114") {
	$displayNameArrTag = "";
	if ($extraOption->OptionUseData) {
		foreach ($extraOption->OptionUseData as $key => $value) {
			if ($value == "Y")
				$displayNameArrTag .= "\"" . $key . '":"' . $extraOption->getDisplayName($key) . '",';
		}
	}
    //debug($displayNameArrTag);

	$cntDisplayName = $extraOption->MakeOrderCntDisplayName($_GET[goodsno], "OCNT");
    //debug($cntDisplayName);
}

$javascriptArrayTag = "";
if ($extraOption->ExtraTblKindCodeArr) {
	foreach ($extraOption->ExtraTblKindCodeArr as $key => $value)
		$javascriptArrayTag .= "\"" . $key . "\",";
}
//debug($javascriptArrayTag);

$tpl->assign("javascriptArrayTag", $javascriptArrayTag); //옵션 코드 정보 (자바스크립트에서 사용)
$tpl->assign("DocumentSizeArrTag", $extraOption->DocumentSizeScriptTag); //규격정보  (자바스크립트에서 사용)
$tpl->assign("PageSizeArrTag", $extraOption->PageSizeScriptTag); //제본방식에 따른 최소,최대 페이지 정보   (자바스크립트에서 사용)
$tpl->assign("OrderMemoArr", $extraOption->OrderMemoUse); //주문제목, 주문메모 사용여부  (자바스크립트에서 사용)

if ($extraOption->Preset == "100102") { //낱장
	$inc_file = "/order/cart_option_100102.htm";
} else if ($extraOption->Preset == "100104") { //책자
	$inc_file = "/order/cart_option_100104.htm";
} else if ($extraOption->Preset == "100106") { //낱장(책자-프리셋2)
	$inc_file = "/order/cart_option_100106.htm";
} else if ($extraOption->Preset == "100108") { //책자
	$inc_file = "/order/cart_option_100108.htm";
} else if ($extraOption->Preset == "100110") { //책자 스튜디오 견적 프리셋1
	$inc_file = "/order/cart_option_100110.htm";
} else if ($extraOption->Preset == "100112") { //책자 견적 프리셋3
	$inc_file = "/order/cart_option_100112.htm";
} else if ($extraOption->Preset == "100114") { //낱장 견적 기본 프리셋
	$inc_file = "/order/cart_option_100114.htm";
}
//debug($inc_file);

if ($data["podskind"]) $data[order_type] = "EDITOR";
else $data[order_type] = "UPLOAD";

//debug($afterOptionSelectArr);
//debug($orderOptionData);
//debug($extraOption->OrderMemoUse);
//exit;

$tpl->define('header', 'layout/header.popup.htm');
$tpl->assign('adminExtraOption', $extraOption);
$tpl->assign('afterOptionSelectArr', $afterOptionSelectArr);
$tpl->assign('orderOptionData', $orderOptionData);
$tpl->assign($data);
$tpl->print_('tpl');
?>