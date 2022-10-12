<?
include "../library.php";
//chkMember();
include "./extra_option_order_cnt_proc.php";
//$db->query("set names utf8");
  
$return_data = "";

$goodsno = $_POST[goodsno];
$imode = $_POST[imode];
$mode = $_POST[mode];
$rule = $_POST[rule];

$optionKindCode = $_POST[option_kind_code];
$priceType = $_POST[price_type];

$drule = $_POST[d_cnt_rule];

$optionGroupType2 = "";
switch ($optionKindCode) {
    case 'OCNT':
		$optionGroupType = "FIXOPTION";
		$optionGroupType2 = "SELOPTION";
		break;
    case 'C-OCNT':
		$optionGroupType = "C-FIXOPTION";
		$optionGroupType2 = "C-SELOPTION";
		break;
    case 'F-OCNT':
		$optionGroupType = "FIXOPTION";
		$optionGroupType2 = "SELOPTION";
		break;
    case 'M-OCNT':
		$optionGroupType = "M-FIXOPTION";
		$optionGroupType2 = "M-SELOPTION";
		break;
    case 'G-OCNT':
		$optionGroupType = "G-FIXOPTION";
		$optionGroupType2 = "G-SELOPTION";
		break;
	default :
		$optionGroupType = "AFTEROPTION";
		break;
}

//사용자 수량 단위 / 사용자 수량 입력 여부 업데이트 2015.06.03 by kdk
$userCntRuleName = $_POST[user_cnt_rule_name];
$userUnitCntRuleName = $_POST[user_unit_cnt_rule_name];
$userCntInputFlag = $_POST[user_cnt_input_flag];

//if(!$imode) $optionKindCode = "";
//exit;
/*
debug($cfg);
debug($cfg_center);
debug($sess_admin);

debug($cid);
debug($goodsno);

echo('$cid='.$cid."<br/>");
echo('$goodsno='.$goodsno."<br/>");
*/ 
//debug($goodsno);
//debug($imode);
//debug($mode);
//debug($rule);
//debug($optionKindCode);
//debug($priceType);
//debug($optionGroupType);
//exit;

//상품 프리셋 정보 조회.
$m_goods = new M_goods();
$data = $m_goods -> getInfo($goodsno);
if ($data) {
	//상품정보에서 자동견적옵션 조회
	//debug($data[extra_option]);
	$extra_option = explode('|', $data[extra_option]);
	//항목 분리
	if (count($extra_option) > 0) {
		$extra_preset = $extra_option[1];
	}
}

//후가공일 경우 tb_extra_option_order_cnt 체크하여 항목이 없으면 기본값으로 등록한다.
if($imode == "after") {
	$data = getOptionOrderCntRule($cid, $cfg_center[center_cid], $goodsno, $optionKindCode);
	//debug($data);
	if(!$data) {
		setAfterOptionOrderCntRule($cid, $cfg_center[center_cid], $goodsno, $optionKindCode);
	}	
}

//tb_extra_option_order_cnt 수정
function set_tb_extra_option_order_cnt($mode, $cid, $center_id, $goodsno, $rule, $optionKindCode, $drule) {
	if($mode == "cnt") {
		setOptionOrderCntRule($cid, $center_id, $goodsno, $rule, $optionKindCode, $drule);
	}
	else {
		setOptionOrderUnitCntRule($cid, $center_id, $goodsno, $rule, $optionKindCode);
		
		//수량(건수) 가격 테이블에 unit_cnt_rule 업데이트 2014.12.23 by kdk
		setOptionPriceUnitCntRule($cid, $center_id, $goodsno, $rule);
	}
}

//사용자 수량 단위 / 사용자 수량 입력 여부 업데이트 2015.06.03 by kdk
function set_tb_extra_option_order_cnt_user($cid, $center_id, $goodsno, $userCntRuleName, $userUnitCntRuleName, $userCntInputFlag, $optionKindCode = '') {
	setOptionOrderCntRuleUser($cid, $center_id, $goodsno, $userCntRuleName, $userUnitCntRuleName, $userCntInputFlag, $optionKindCode);
}

//tb_extra_option_master / tb_extra_option item_price_type 수정
function set_item_price_type($cid, $center_id, $goodsno, $optionGroupType, $itemPriceType, $optionKindCode = '') {
	setOptionPriceType($cid, $center_id, $goodsno, $optionGroupType, $itemPriceType, $optionKindCode);
}

if($mode) {
	
	if($mode == "displaynameUpdate") {		
		setOptionOrderDisplayName($cid, $cfg_center[center_cid], $goodsno, $_POST[update_name], $optionKindCode);
		echo("OK");
	}
	else {
		$rule = str_replace(',', ';', $rule);
		$rule = str_replace(' ', '', $rule); //공백,탭,엔터 제거 2014.07.11 by kdk
		$rule = strDelEntTab($rule);
		$rule = trim($rule);
		
		$last_rule = substr($rule,-1); //마지막 구분자 추가 2014.08.01 by kdk
		if($last_rule != ";")
			$rule .= ";";
	
		set_tb_extra_option_order_cnt($mode, $cid, $cfg_center[center_cid], $goodsno, $rule, $optionKindCode, $drule);
		
		//사용자 수량 단위 / 사용자 수량 입력 여부 업데이트 2015.06.03 by kdk
		set_tb_extra_option_order_cnt_user($cid, $cfg_center[center_cid], $goodsno, $userCntRuleName, $userUnitCntRuleName, $userCntInputFlag, $optionKindCode);
		
		if($mode == "cnt") {
			if($optionGroupType != "AFTEROPTION") $optionKindCode = "";
			set_item_price_type($cid, $cfg_center[center_cid], $goodsno, $optionGroupType, $priceType, $optionKindCode);
			
			//프리셋2인 경우 인쇄옵션 분리된 경우
			if($optionGroupType2 != "") 
			{
				set_item_price_type($cid, $cfg_center[center_cid], $goodsno, $optionGroupType2, $priceType, $optionKindCode);
			}
			
			//책자 프리셋3인 경우 표지 견적방식으로 (제본방식 JB,제본형식 HS 도 똑같이 적용)
			if($extra_preset == "100112" && $optionGroupType == "C-FIXOPTION") {
				set_item_price_type($cid, $cfg_center[center_cid], $goodsno, "AFTEROPTION", $priceType, "JB");
				set_item_price_type($cid, $cfg_center[center_cid], $goodsno, "AFTEROPTION", $priceType, "HS");
			}
					
		}
		echo("OK");
	}
	
}
else echo("FAIL");
  
?>