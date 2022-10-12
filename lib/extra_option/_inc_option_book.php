<?
//$_GET[mode]가 있으면 관리자(가격처리)에서 사용하고 없다면 사용자페이지에서 사용한다.
if ($_GET[mode]) {
	//관리자페이지

	//tb_extra_option_order_cnt 테이블 option_kind_code
	switch ($_GET[mode]) {
		case 'FIX' :
			$optionKindCode = "OCNT";
			$optionGroupType = "FIXOPTION";
			break;
		case 'SEL' :
			$optionKindCode = "OCNT";
			$optionGroupType = "SELOPTION";
			break;
		case 'AFTER' :
			$optionKindCode = $_GET[kind];
			$optionGroupType = "AFTEROPTION";
			break;

		case 'C-FIX' :
			$optionKindCode = "C-OCNT";
			$optionGroupType = "C-FIXOPTION";
			break;
		case 'C-SEL' :
			//BOOK 100108 표지 인쇄 옵션
			$optionKindCode = "C-OCNT";
			$optionGroupType = "C-SELOPTION";
			break;

		case 'M-FIX' :
			$optionKindCode = "M-OCNT";
			$optionGroupType = "M-FIXOPTION";
			break;
		case 'M-SEL' :
			//BOOK 100108 면지 인쇄 옵션
			$optionKindCode = "M-OCNT";
			$optionGroupType = "M-SELOPTION";
			break;

		case 'G-FIX' :
			$optionKindCode = "G-OCNT";
			$optionGroupType = "G-FIXOPTION";
			break;
		case 'G-SEL' :
			//BOOK 100108 간지 인쇄 옵션
			$optionKindCode = "G-OCNT";
			$optionGroupType = "G-SELOPTION";
			break;

		default :
			$optionKindCode = "OCNT";
			$optionGroupType = "FIXOPTION";
			break;
	}
	//debug($_GET[mode]);
	//debug($optionGroupType);
	//debug($optionKindCode);

	//주문 수량 select 만들기
	$selectOrderCnt = $extraOption -> MakeBookOrderCnt($_GET[goodsno], $optionKindCode);
	//필수 옵션
	$OptionDisplayData = array();
	//후가공 옵션
	$AfterOptionDisplayData = array();
	
	$debug_data .= "3 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";

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

} else {
	//사용자페이지

	//주문 건수 select 만들기
	$selectUnitCnt = $extraOption -> MakeBookUnitCntBook($_GET[goodsno]);
	//debug($selectUnitCnt);
			
	//주문 수량 select 만들기
	if ($data[preset] == "100104" || $data[preset] == "100108" || $data[preset] == "100112")//책자 프리셋1,2 일 경우...
	{
		$selectOrderCntPage = $extraOption -> MakeBookOrderCntBook($_GET[goodsno], "OCNT", "PP", "order_cnt_select_PP", "forwardOrderCntAction", "false");
		
		$selectOrderCntCover = $extraOption -> MakeBookOrderCntBook($_GET[goodsno], "C-OCNT", "C-PP", "order_cnt_select_C-PP");

		if ($extraOption -> OrderMemoUse[96] == "Y")//면지를 사용하면...
			$selectOrderCntMpage = $extraOption -> MakeBookOrderCntBook($_GET[goodsno], "M-OCNT", "M-PP", "order_cnt_select_M-PP");

		if ($extraOption -> OrderMemoUse[97] == "Y")//간지를 사용하면...
			$selectOrderCntGpage = $extraOption -> MakeBookOrderCntBook($_GET[goodsno], "G-OCNT", "G-PP", "order_cnt_select_G-PP");

	} else {
		$selectOrderCntPage = $extraOption -> MakeBookOrderCntBook($_GET[goodsno], "OCNT", "PP", "order_cnt_select_PP");
	}

	//debug($selectOrderCntCover);
	//debug($selectOrderCntPage);
	//debug($selectOrderCntMpage);
	//debug($selectOrderCntGpage);
	//exit;

	//표지 필수 옵션 //100108 종이 //표지 필수 옵션 //100112 종이
	$OptionDisplayDataCover = array();
	
	//표지 옵션 //100108 인쇄
	$OptionDisplayDataCoverSel = array();
	
	//내지 필수 옵션 //100108 종이 //내지 필수 옵션 //100112 종이
	$OptionDisplayDataPage = array();
	
	//내지 옵션 //100108 인쇄
	$OptionDisplayDataPageSel = array();
	
	//면지 필수 옵션 //100108 종이 //면지 필수 옵션 //100112 종이
	$OptionDisplayDataMpage = array();
	
	//면지 옵션 //100108 인쇄
	$OptionDisplayDataMpageSel = array();
	
	//간지 필수 옵션 //100108 종이 //간지 필수 옵션 //100112 종이
	$OptionDisplayDataGpage = array();
	
	//간지 옵션 //100108 인쇄
	$OptionDisplayDataGpageSel = array();
	
	//옵션 필수 옵션 //100104 인쇄	//제본 필수 옵션 //100112 제본 (afteroption option_kind_index : 2,3)
	$OptionDisplayDataPrint = array();
	
	//후가공 옵션
	$AfterOptionDisplayData = array();


	$debug_data .= "3 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";

	if ($data[preset] != "100106")//책자(낱장) 프리셋2이 아니면...
	{
		//표지 필수 옵션 가져오기
		$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "C-FIXOPTION");
		$optionSelectArr = $extraOption -> MakeSelectOption("", "", "C-FIXOPTION");
		//debug($optionSelectArr);
		foreach ($optionSelectArr as $key => $value) {
			if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
				$OptionDisplayDataCover[$value[name]] = $value;
				$firstOptionName = $value[name];
			} else {
				//이전 옵션 배열에 넣어야 한다.
				CheckTagsData($value, $OptionDisplayDataCover, $firstOptionName);
			}

			if ($value[sub_option]) {
				MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataCover, $value[name]);
			}
		}
		//debug($OptionDisplayDataCover);exit;
	}

	//내지 필수 옵션 가져오기
	$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "FIXOPTION");
	$optionSelectArr = $extraOption -> MakeSelectOption("", "", "FIXOPTION");
	//debug($optionSelectArr);
	foreach ($optionSelectArr as $key => $value) {
		if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
			$OptionDisplayDataPage[$value[name]] = $value;
			$firstOptionName = $value[name];
		} else {
			//이전 옵션 배열에 넣어야 한다.
			CheckTagsData($value, $OptionDisplayDataPage, $firstOptionName);
		}

		if ($value[sub_option]) {
			MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataPage, $value[name]);
		}
	}
	//debug($OptionDisplayDataPage);exit;

	if ($data[preset] != "100106")//책자(낱장) 프리셋2이 아니면...
	{
		//면지 필수 옵션 가져오기
		if ($extraOption -> OrderMemoUse[96] == "Y") {//면지를 사용하면...
			$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "M-FIXOPTION");
			$optionSelectArr = $extraOption -> MakeSelectOption("", "", "M-FIXOPTION");
			//debug($optionSelectArr);
			foreach ($optionSelectArr as $key => $value) {
				if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
					$OptionDisplayDataMpage[$value[name]] = $value;
					$firstOptionName = $value[name];
				} else {
					//이전 옵션 배열에 넣어야 한다.
					CheckTagsData($value, $OptionDisplayDataMpage, $firstOptionName);
				}

				if ($value[sub_option]) {
					MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataMpage, $value[name]);
				}
			}
			//debug($OptionDisplayDataMpage);exit;
		}

		//간지 필수 옵션 가져오기
		if ($extraOption -> OrderMemoUse[97] == "Y") {//간지를 사용하면...
			$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "G-FIXOPTION");
			$optionSelectArr = $extraOption -> MakeSelectOption("", "", "G-FIXOPTION");
			//debug($optionSelectArr);
			foreach ($optionSelectArr as $key => $value) {
				if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
					$OptionDisplayDataGpage[$value[name]] = $value;
					$firstOptionName = $value[name];
				} else {
					//이전 옵션 배열에 넣어야 한다.
					CheckTagsData($value, $OptionDisplayDataGpage, $firstOptionName);
				}

				if ($value[sub_option]) {
					MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataGpage, $value[name]);
				}
			}
			//debug($OptionDisplayDataGpage);
		}
	}

	if ($data[preset] == "100108")//책자 프리셋2 일 경우...
	{
		//표지 100108 인쇄 옵션 가져오기
		$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "C-SELOPTION");
		$optionSelectArr = $extraOption -> MakeSelectOption("", "", "C-SELOPTION");
		//debug($optionSelectArr);
		foreach ($optionSelectArr as $key => $value) {
			if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
				$OptionDisplayDataCoverSel[$value[name]] = $value;
				$firstOptionName = $value[name];
			} else {
				//이전 옵션 배열에 넣어야 한다.
				CheckTagsData($value, $OptionDisplayDataCoverSel, $firstOptionName);
			}

			if ($value[sub_option]) {
				MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataCoverSel, $value[name]);
			}
		}
		//debug($OptionDisplayDataCoverSel);

		//내지 100108 인쇄 옵션 가져오기
		$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "SELOPTION");
		$optionSelectArr = $extraOption -> MakeSelectOption("", "", "SELOPTION");
		//debug($optionSelectArr);
		foreach ($optionSelectArr as $key => $value) {
			if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
				$OptionDisplayDataPageSel[$value[name]] = $value;
				$firstOptionName = $value[name];
			} else {
				//이전 옵션 배열에 넣어야 한다.
				CheckTagsData($value, $OptionDisplayDataPageSel, $firstOptionName);
			}

			if ($value[sub_option]) {
				MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataPageSel, $value[name]);
			}
		}
		//debug($OptionDisplayDataPageSel);

		//면지 100108 인쇄 옵션 가져오기
		$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "M-SELOPTION");
		$optionSelectArr = $extraOption -> MakeSelectOption("", "", "M-SELOPTION");
		//debug($optionSelectArr);
		foreach ($optionSelectArr as $key => $value) {
			if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
				$OptionDisplayDataMpageSel[$value[name]] = $value;
				$firstOptionName = $value[name];
			} else {
				//이전 옵션 배열에 넣어야 한다.
				CheckTagsData($value, $OptionDisplayDataMpageSel, $firstOptionName);
			}

			if ($value[sub_option]) {
				MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataMpageSel, $value[name]);
			}
		}
		//debug($OptionDisplayDataMpageSel);

		//간지 100108 인쇄 옵션 가져오기
		$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "G-SELOPTION");
		$optionSelectArr = $extraOption -> MakeSelectOption("", "", "G-SELOPTION");
		//debug($optionSelectArr);
		foreach ($optionSelectArr as $key => $value) {
			if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
				$OptionDisplayDataGpageSel[$value[name]] = $value;
				$firstOptionName = $value[name];
			} else {
				//이전 옵션 배열에 넣어야 한다.
				CheckTagsData($value, $OptionDisplayDataGpageSel, $firstOptionName);
			}

			if ($value[sub_option]) {
				MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataGpageSel, $value[name]);
			}
		}
		//debug($OptionDisplayDataGpageSel);
	}

	if ($data[preset] == "100106")//책자(낱장) 프리셋2 일 경우...
	{
		//옵션 필수 옵션 가져오기
		$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "SELOPTION");
		$optionSelectArr = $extraOption -> MakeSelectOption("", "", "SELOPTION");
		//debug($optionSelectArr);
		foreach ($optionSelectArr as $key => $value) {
			if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '')) {
				$OptionDisplayDataPrint[$value[name]] = $value;
				$firstOptionName = $value[name];
			} else {
				//이전 옵션 배열에 넣어야 한다.
				CheckTagsData($value, $OptionDisplayDataPrint, $firstOptionName);
			}

			if ($value[sub_option]) {
				MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataPrint, $value[name]);
			}
		}
		//debug($OptionDisplayDataPrint);
	}

	//후가공 필수 옵션 가져오기
	$optionKindCodeArr = $extraOption -> getOptionKind($_GET[goodsno], "AFTEROPTION");
	$optionSelectArr = $extraOption -> MakeSelectOption("", "", "AFTEROPTION");
	//debug($optionKindCodeArr);
	//debug($optionSelectArr);
	//exit;
	if ($data[preset] == "100112")//책자 프리셋3 일 경우...
	{
		//후가공 필수 (제본방식,제본형식)
		foreach ($optionSelectArr as $key => $value) {		
			if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '') && ($data[option_kind_index] == '2' || $data[option_kind_index] == '3')) {
				$OptionDisplayDataPrint[$value[name]] = $value;
				$firstOptionName = $value[name];
			} else {
				//이전 옵션 배열에 넣어야 한다.
				CheckTagsData($value, $OptionDisplayDataPrint, $firstOptionName);
			}
	
			if ($value[sub_option]) {
				MakeSubOptionSelectTag($value[sub_option], $OptionDisplayDataPrint, $value[name]);
			}
		}
		//debug($OptionDisplayDataPrint);	
		
		//후가공 선택 (제본방식,제본형식 제외)
		foreach ($optionSelectArr as $key => $value) {
			if ($value[display_name] && (($value[parent_option_item_ID] == '0' || $value[parent_option_item_ID] == '') && $value[parent_option_item_ID] != '') && ($data[option_kind_index] != '2' && $data[option_kind_index] != '3')) {
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
		//debug($AfterOptionDisplayData);
	}
	else 
	{
		foreach ($optionSelectArr as $key => $value) {
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
		//debug($AfterOptionDisplayData);			
	}

	$debug_data .= "4 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
	//debug($debug_data);

	//exit;

}
?>