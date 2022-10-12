<?
include "../library.php";
include "./extra_option_price_proc.php";
$db -> query("set names utf8");

$return_data = "";
$addWhere = "";

$this_time = get_time();

//센터와 몰을 구분하여 쿼리를 가져온다.
$classExtraOption = new M_extra_option();

if ($_GET[option_ID]) { //옵션태그
	if ($_GET[option_group_type] && $_GET[option_group_type] != "")
		$addWhere = "and option_group_type = '$_GET[option_group_type]'";

	if ($_GET[option_kind_code] && $_GET[option_kind_code] != "")
		$addWhere = "and option_kind_code = '$_GET[option_kind_code]'";

	$search_option_ID = $_GET[option_ID];
	//id=>value 로 변경

	$data = $classExtraOption -> getOptionChildItemListOnlyVisibleS2($cid, $cfg_center[center_cid], $_GET[goodsno], $search_option_ID, $addWhere);
	//debug($data);

	$return_data = "";
	if (sizeof($data) > 0) {
		foreach ($data as $key => $value) {
			$return_data .= "<option value='$value[item_name]' option_kind_code='$value[option_kind_code]' gap='3'>$value[item_name]</option>\r\n";
		}
	}
	//debug(debug_time($this_time, "1"));
} else { //가격
	//$search_option_item = $_GET[option_item]; //id=>value 로 변경
	//$search_option_item = substr($search_option_item, 0, -1);
	$search_option_item = "";

	//책자 프리셋3 JB,HS 예외 처리.(김이사님 요청 제본가격 추가)
	if ($_GET[preset] == "100112" && $_GET[option_group_type] == "AFTEROPTION" && $_GET[option_kind_code] == "D-OP") {
		foreach ($_GET as $ItemKey => $ItemValue) {
			if (strpos($ItemKey, "item_select_JB") !== false) {
				$_GET[option_kind_code] = "JB";
				break;
			}
			else if (strpos($ItemKey, "item_select_HS") !== false) {
				$_GET[option_kind_code] = "HS";
				break;
			}
		}		
	}

	if ($_GET[option_group_type] && $_GET[option_group_type] != "") {
		$group = str_replace("OPTION", "", $_GET[option_group_type]);
		$addWhere = "and option_group_type = '$group'";
		
		if ($group == "AFTER") {//후가공 옵션이면...
			$group = $_GET[option_kind_code];
			$addWhere = "and option_group_type = '$group'";
		}
	}
	//debug(debug_time($this_time, "2"));

	//select item 을 option_kind_index 순서로 "|" 조합한다.
	$itemSelectData = array();
	foreach ($_GET as $ItemKey => $ItemValue) {
		if (strpos($ItemKey, "item_select_") !== false) {
			$optionKey = explode('_', $ItemKey);
			// "_" 분리
			$itemSelectData[$optionKey[3]] = $ItemValue;

			//같은 가격 설정이 있는지 조회.
		}
	}
	//debug(debug_time($this_time, "3"));
	ksort($itemSelectData);
	//debug($itemSelectData);
	//debug(debug_time($this_time, "4"));
	//같은 가격 설정을 찾기 위해 code,option_group_type을 조합한다.
	//code_type_PP_1_PP_FIXOPTION
	$codeSelectData = array();
	$typeSelectData = array();
	foreach ($_GET as $ItemKey => $ItemValue) {
		if (strpos($ItemKey, "code_type_") !== false) {
			$optionKey = explode('_', $ItemKey);
			// "_" 분리
			$codeSelectData[$optionKey[3]] = $optionKey[4];
			$typeSelectData[$optionKey[3]] = $optionKey[5];
		}
	}
	//debug(debug_time($this_time, "5"));
	ksort($codeSelectData);
	ksort($typeSelectData);
	//debug($codeSelectData);
	//debug($typeSelectData);
	//exit;
	//debug(debug_time($this_time, "6"));
	//후가공 옵션일 경우 tb_extra_option_master_use.option_kind_index = 101 docUse 조회하여 "Y"면 규격을 포함하여 조회
	if ($_GET[option_group_type] == "AFTEROPTION") {
		$use_flag = getOptionMasterUse($cid, $cfg_center[center_cid], $_GET[goodsno], "101");

		if ($use_flag == "Y") {
			$search_option_item = $_GET[document] . "|";
		}
	} else {
		//규격 설정은 프리셋 별로
		if ($_GET[preset] == "100104" || $_GET[preset] == "100106" || $_GET[preset] == "100108" || $_GET[preset] == "100112") {
			$search_option_item = $_GET[document] . "|";

			//같은 가격 설정이 있는지 조회.
			$same_option_item = $classExtraOption -> getSamePriceOptionItem($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[document], " and option_group_type='DOCUMENT'");

			//debug("same_option_item = ".$same_option_item);
			if ($same_option_item && $same_option_item != '') {
				$search_option_item = $same_option_item . "|";
			}
		}
	}
	//debug(debug_time($this_time, "7"));
	foreach ($itemSelectData as $key => $val) {
		if ($val != "") {
			//debug($val);
			//$search_option_item .= $val ."|";

			//같은 가격 설정이 있는지 조회.
			$same_option_item = $classExtraOption -> getSamePriceOptionItem($cid, $cfg_center[center_cid], $_GET[goodsno], $val, " and option_kind_code='$codeSelectData[$key]' and option_group_type='$typeSelectData[$key]'");

			//debug("same_option_item = ".$same_option_item);
			if ($same_option_item && $same_option_item != '') {
				$search_option_item = $same_option_item . "|";
			} else {
				$search_option_item .= $val . "|";
			}
		}
	}
	//debug(debug_time($this_time, "8"));
	$search_option_item = substr($search_option_item, 0, -1);

	//debug($search_option_item);
	//exit;

	$m_goods = new M_goods();
	$data = $m_goods -> getInfo($_GET[goodsno]);
	//debug($data);
	if ($data) {
		//상품정보에서 자동견적옵션 조회
		//debug($data[extra_option]);
		$extra_option = explode('|', $data[extra_option]);
		//항목 분리
		if (count($extra_option) > 0) {
			$extra_price_type = $extra_option[2];
		}
	}

	//상품정보에 면적으로 계산 여부 추가하여 사용함. / 16.11.07 / kdk
	//옵션에 설정된 가격 계산 방식은 무시함.
	//후가공 옵션은 면적으로 계산하지 않고 후가공 견적방식으로 처리. / 17.05.16 / kdk
	if ($extra_price_type == "SIZE" && $_GET[option_group_type] != "AFTEROPTION") {
		$item_price_type = $extra_price_type;
	} else {
		$item_price_type = getOptionItemPriceTypeS2($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[option_group_type]);
	}

	//debug($item_price_type);

	//debug(debug_time($this_time, "9"));
	$return_data = getOptionPriceS2($cid, $cfg_center[center_cid], $_GET[goodsno], $search_option_item, $_GET[order_cnt], $_GET[order_cnt_page], $_GET[document_x], $_GET[document_y], $_GET[print_x], $_GET[print_y], $addWhere, $item_price_type, $_GET[option_group_type]);
	//echo $return_data;
}
//debug(debug_time($this_time, "10"));
if ($return_data) {
	echo $return_data;
}
?>