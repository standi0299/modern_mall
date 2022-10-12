<?
//주문 수량 select 만들기

$selectOrderCnt = $extraOption -> MakeBookOrderCnt($_GET[goodsno], $optionKindCode);
//debug($selectOrderCnt);
$OptionDisplayData = array();
//필수 옵션
$AfterOptionDisplayData = array();
//후가공 옵션
$debug_data .= "3 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";

//debug("optionGroupType : ".$optionGroupType);
//debug("optionKindCode : ".$optionKindCode);
//debug("extra_preset : ".$extra_preset);

//옵션 가져오기
$optionSelectArr = $extraOption -> MakeSelectOption("", "", $optionGroupType);
$debug_data .= "4 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
//debug($debug_data);
//debug($optionSelectArr);
//exit;
foreach ($optionSelectArr as $key => $value) {
	//필수 옵션 데이타 항목만 뽑자
	if ($value[necessary_flag] == "Y") {
		if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
			$OptionDisplayData[$value[name]] = $value;
			$firstOptionName = $value[name];
		} else {
			//이전 옵션 배열에 넣어야 한다.
			CheckTagsData($value, $OptionDisplayData, $firstOptionName);
		}

		if ($value[sub_option]) {
			MakeSubOptionSelectTag($value[sub_option], $OptionDisplayData, $value[name]);

		}
	} else if ($value[necessary_flag] == "N") {
		//debug($value);
		if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
			$AfterOptionDisplayData[$value[name]] = $value;
			$firstOptionName = $value[name];
		} else {
			//이전 옵션 배열에 넣어야 한다.
			CheckTagsData($value, $AfterOptionDisplayData, $firstOptionName);
		}

		if ($value[sub_option]) {
			MakeSubOptionSelectTag($value[sub_option], $AfterOptionDisplayData, $value[name]);
		}
	}
}

$debug_data .= "5 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
?>