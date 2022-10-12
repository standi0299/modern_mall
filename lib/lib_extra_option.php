<?
/**
 * Extra Option Library (자동견적 함수)
 *
 * lib_extra_option.php +
 * lib_extra_option_admin.php(삭제예정)
 *
 * 사용자와 관리자 그리고 프리셋이 추가 시 생성된 함수 파일을 하나로 통합.
 * 2017.01.24 ~
 * kdk
 */

//옵션값 합치기
function CheckTagsData($sourceSelect, &$targetSelect, $optionName) {
	$targetSelect[$optionName][display_tag] .= $sourceSelect[display_tag];

	if (!$targetSelect[$optionName][price_tag])
		$targetSelect[$optionName][price_tag] = $sourceSelect[price_tag];
}

//하위 옶션을 하나의 배열로 만든다    //재귀호출
function MakeSubOptionSelectTag($optionSelect, &$OptionDisplayData, $currentOptionName) {
	//debug($optionSelect[parent_id]);

	$displayTagOptionName = $optionSelect[name];
	if ($optionSelect[display_name] && ($optionSelect[parent_option_item_ID] != '-1' && $optionSelect[parent_option_item_ID] != '0' && $optionSelect[parent_option_item_ID] != '')) {
		$OptionDisplayData[$optionSelect[name]] = $optionSelect;
		//debug($OptionDisplayData[$optionSelect[name]]);
	} else {
		//이전 옵션 배열에 넣어야 한다.
		CheckTagsData($optionSelect, $OptionDisplayData, $currentOptionName);
		$displayTagOptionName = $currentOptionName;
		//옵션 제목 이 없을 경우는 이전 옵션의 이름의 display_tag 에 포함해야 한다.
	}

	if ($optionSelect[sub_option]) {
		MakeSubOptionSelectTag($optionSelect[sub_option], $OptionDisplayData, $displayTagOptionName);
	}

	//return $OptionDisplayData;
}

//json 주문 데이타를 기본 주문 데이타로 변환
//사용자 "/order/cart~.php,/yellowkit/module_preview.php"에서 사용.
function OrderJsonParse($orderJson) {
	global $r_est_option;
	$result = array();

	$orderJson = str_replace('\"', '"', $orderJson);
	$orderJson = str_replace('\\\\', '\\', $orderJson);
	//$orderJson=preg_replace('/,\s*([\]}])/m', '$1', utf8_encode($orderJson));

	$oderData = json_decode($orderJson, true);
	//debug($oderData);

	//일반 인쇄
	//{"order_cnt_select":"100","item_select_JD_0":"55","item_select_PP_0":"1","item_select_PP_1":"5","item_select_PP_2":"9","item_select_PP_3":"11","item_select_DM_0":"58","item_select_FL_0":"61","item_select_FL_1":"64","item_select_TG_0":"70","item_select_TG_1":"74",
	//"option_price_JD":"500","option_price_PP":"10000",
	//"chk_item_select_DM_0":"N","chk_item_select_FL_0":"N","chk_item_select_TG_0":"N",
	//"print_x_DM":"","print_y_DM":"","option_price_DM":"","option_price_FL":"4000","option_price_TG":"3000"}

	//제본 인쇄
	//{"order_cnt_select":"10","item_select_B_DS_0":"152","item_select_C_BB_0":"136","item_select_C_PP_0":"86","item_select_C_PP_1":"90","item_select_C_PP_2":"94","item_select_C_PP_3":"96",
	//"order_cnt_page_select":"4","item_select_P_PP_0":"81","item_select_P_PP_1":"138","item_select_P_PP_2":"142","item_select_P_PP_3":"144","item_select_DM_0":"158","item_select_FL_0":"161","item_select_FL_1":"164","item_select_TG_0":"170","item_select_TG_1":"174",
	//"option_price_C_BB":"10000","option_price_C_PP":"1000","option_price_P_PP":"4000","chk_item_select_DM_0":"N",
	//"chk_item_select_FL_0":"N","chk_item_select_TG_0":"N",
	//"print_x_DM":"","print_y_DM":"","option_price_DM":"","option_price_FL":"4000","option_price_TG":"3000"}

	$result[est_order_data] = $orderJson;

	foreach ($oderData as $key => $value) {
		//출력 수량 수량.
		if ($key == "order_cnt_select") {
			$result["order_cnt"] = $value;
		}

		//주문 부수 (또는 주문 명수)
		if ($key == "unit_order_cnt") {
			$result["unit_order_cnt"] = $value;
		}

		//내지 주문 수량
		else if ($key == "order_cnt_page_select") {
			$result["order_cnt_page"] = $value;
		}

		//선택한 text 값 설정.
		else if (strpos($key, "text_item_select_") === 0) {
			$subString = str_replace("text_item_select_", "", $key);
			$optionCode = substr($subString, 0, strpos($subString, "_"));
			$optionIndex = substr($subString, strrpos($subString, "_") + 1);

			$result[$optionCode][$optionIndex]["option_text"] = $value;
			if ($result[$optionCode]["use_flag"] != 'N')
				$result[$optionCode]["use_flag"] = "Y";
			$result[$optionCode][$optionIndex]["option_name"] = $r_est_option[$optionCode];
		}

		//선택된 후가공 옵션정보들
		else if (strpos($key, "item_select_") === 0) {
			$subString = str_replace("item_select_", "", $key);
			$optionCode = substr($subString, 0, strpos($subString, "_"));
			$optionIndex = substr($subString, strrpos($subString, "_") + 1);

			$result[$optionCode][$optionIndex]["option_item_ID"] = $value;
			if ($result[$optionCode]["use_flag"] != 'N')
				$result[$optionCode]["use_flag"] = "Y";
			$result[$optionCode][$optionIndex]["option_name"] = $r_est_option[$optionCode];
		}

		//원가 가격 정보
		else if (strpos($key, "option_supply_price_") === 0) {
			//$result[str_replace("option_price_", "", $key)] = $value;
			$result[str_replace("option_supply_price_", "", $key)]["supply_price"] = $value;
		}

		//판매 가격 정보
		else if (strpos($key, "option_price_") === 0) {
			//$result[str_replace("option_price_", "", $key)] = $value;
			$result[str_replace("option_price_", "", $key)]["price"] = $value;
		}

		//후가공 옵션 사용여부
		else if (strpos($key, "chk_item_select_") === 0) {
			$subString = str_replace("chk_item_select_", "", $key);
			$optionCode = substr($subString, 0, strpos($subString, "_"));
			$result[$optionCode]["use_flag"] = $value;
		}

		//후가공 옵션중 x,y 값이 있는경우
		else if (strpos($key, "print_x_") === 0) {
			$result[str_replace("print_x_", "", $key)]["print_x"] = $value;
		} else if (strpos($key, "print_y_") === 0) {
			$result[str_replace("print_y_", "", $key)]["print_y"] = $value;
		}
	}

	//좀더 필요한 옵션항목들을 구성한다.
	$result["est_order_cnt"] = 0;
	//주문수량
	$result["est_supply_price"] = 0;
	$result["est_price"] = 0;
	$result["est_order_option_desc"] = '';
	foreach ($result as $key => $value) {
		if ($key == "order_cnt") {
			$result["est_order_option_desc"] .= _("출력수량(부수)").":$value, ";

			if ($result["est_order_cnt"] == 0)
				$result["est_order_cnt"] = $value;
		}
		if ($key == "unit_order_cnt") {
			$result["est_order_option_desc"] .= _("인원수").":$value, ";
			$result["est_order_cnt"] = $value;
		}
		if ($key == "order_cnt_page")
			$result["est_order_option_desc"] .= _("내지출력수량").":$value, ";
		else {
			//옵션이 사용한것들만
			if ($value[use_flag] == "Y") {

				$optionText = array();
				if (is_array($value)) {
					foreach ($value as $itemkey => $itemvalue) {
						if (is_array($itemvalue)) {
							//echo "$itemvalue[option_name]:$itemvalue[option_text], <BR> ";
							$optionText[$itemvalue[option_name]] .= $itemvalue[option_text] . ", ";
						}
					}
				}

				//옵션별 데이타 text 를 구분하여 보여준다.
				foreach ($optionText as $text => $textValue) {
					$result["est_order_option_desc"] .= "$text:$textValue";
				}

				$result["est_supply_price"] += (int)$value[supply_price];
				//원가
				$result["est_price"] += (int)$value[price];
			}
		}
	}

	return $result;
}

//한글깨짐 문제.
function han ($s) { return reset(json_decode('{"s":"'.$s.'"}')); }
function to_han ($str) { return preg_replace('/(\\\u[a-f0-9]+)+/e','han("$0")',$str); }
//한글깨짐 문제.

//json 주문 데이타를 기본 주문 데이타로 변환 (클라이언트 스크립트에서 미리 처리함. goods.extra.option.js -> makeOptionJson() )
function orderJsonParse2($orderJson) {
	global $r_est_option;
	global $r_est_option2;
	$result = array();

    // $orderJson = str_replace('"{\"', '{\"', $orderJson);
    // $orderJson = str_replace('\"}"', '\"}', $orderJson);
    // $orderJson = str_replace('}\",', '},', $orderJson);
    
	// $orderJson = str_replace('\"', '"', $orderJson);
	// $orderJson = str_replace('\\\\', '\\', $orderJson);
	// $orderJson = str_replace("\\\"", "\"", $orderJson); 
	
	$orderJson = str_replace('\"', '"', $orderJson);
	$orderJson = str_replace('\\\\', '\\', $orderJson);
    
    $orderJson = str_replace('"{"', '{"', $orderJson);
	$orderJson = str_replace('"}"', '"}', $orderJson);
	
	//est_order_option 2차원 배열 이므로 한번더 replace 200107 jtkim
	$orderJson = str_replace('\"', '"', $orderJson);
	$orderJson = str_replace('"{"', '{"', $orderJson);
	$orderJson = str_replace('"}"', '"}', $orderJson);

	//$orderJson=preg_replace('/,\s*([\]}])/m', '$1', utf8_encode($orderJson));

	$oderData = json_decode($orderJson, true);

	$result[est_order_data] = to_han (json_encode($oderData[est_order_option]));

	//템플릿셋ID 템플릿ID를 저장한다.
	if ($oderData[templateSetIdx] && $oderData[templateIdx]) {
		//$est_order_option = json_decode($oderData[est_order_data], true);
        $est_order_option = $oderData[est_order_option];

		if ($oderData[templateSetIdx])
			$est_order_option[templateSetIdx] = $oderData[templateSetIdx];
		if ($oderData[templateIdx])
			$est_order_option[templateIdx] = $oderData[templateIdx];

		//$result[est_order_data] = json_encode($est_order_option);
        $result[est_order_data] = to_han (json_encode($est_order_option)); //한글깨짐 문제.
	}
    //debug($result[est_order_data]);

	$oderData[est_supply_price] = str_replace(',', '', $oderData[est_supply_price]);
	$oderData[est_price] = str_replace(',', '', $oderData[est_price]);

	//좀더 필요한 옵션항목들을 구성한다.
	$result["est_order_cnt"] = $oderData[est_order_cnt];
	//주문수량
	$result["est_supply_price"] = (int)$oderData[est_supply_price];
	//원가
	$result["est_price"] = (int)$oderData[est_price];
	//판매가
	$result["est_order_option_desc"] = $oderData[est_order_option_desc];

    //상품 카테고리 저장.
    if ($oderData[catno])
        $result["catno"] = $oderData[catno];
    
	//debug($oderData);
	//debug($result);
	//exit;

	return $result;
}

### P관리자에 사용함.###
//옵션에 속한 kind_index 를 넘겨준다. / 16.09.02 / kdk
function getOptionKindIndex($optionData) {
	$result = array();
	foreach ($optionData as $dataKey => $data) {
		if ($data[option_group_type] != 'AFTEROPTION') {
			if (!in_array($data[option_kind_index], $result))
				$result[] = $data[option_kind_index];
		}
	}

	return $result;
}

//후가공에 속한 kind_index 를 넘겨준다. / 16.08.03 / kdk
function getAfterOptionKindIndex($optionData) {
	$result = array();
	foreach ($optionData as $dataKey => $data) {
		if ($data[option_group_type] == 'AFTEROPTION') {
			if (!in_array($data[option_kind_index], $result))
				$result[] = $data[option_kind_index];
		}
	}

	return $result;
}

function getDisplayName($optionData, $optionKindIndex) {
	$result = "";
	if (is_array($optionData)) {
		foreach ($optionData as $dataKey => $data) {
			if ($data[option_kind_index] == ($optionKindIndex)) {
				$result = $data[display_name];
				break;
			}
		}
	}

	return $result;
}

function getOptionKindCode($optionData, $optionKindIndex) {
	$result = "";
	foreach ($optionData as $dataKey => $data) {
		if ($data[option_kind_index] == ($optionKindIndex)) {
			$result = $data[option_kind_code];
			break;
		}
	}

	return $result;
}

function getOptionKindUse($optionUseData, $optionKindIndex) {
	$result = $optionUseData[$optionKindIndex];

	if ($result == '')
		$result = 'Y';
	return $result;
}

### P관리자에 사용함.###

//후가공 옵션 도움말 정보 조회 / 16.08.11 / kdk
function AfterOptionHelpInfo($code) {
	global $cid, $_GET;

	$this_time = get_time();
	$result = "";
	$m_goods = new M_goods();
	$afterOptionHelpData = $m_goods -> getExtraAfterOptionHelpInfo($cid, $_GET[goodsno], $code);
	//debug($afterOptionHelpData);
	if ($afterOptionHelpData) {
		if ($afterOptionHelpData[url] != "") {
			$result = "&nbsp;<a href=\"javascript:;\" onclick=\"afterHelpImgOpenLayer('$afterOptionHelpData[url]')\"><img src=\"/admin/img/bt_qeustion.png\" style=\"vertical-align:middle\" /></a>";
		}
	}
	//debug(debug_time($this_time, "AfterOptionHelpInfo"));
	return $result;
}

##############################################
### lib_extra_option_admin.php 소스
//자동견적 옵션 관련 관리자에서 사용하는 함수 모음

function MakeUseflagSelect($tagName, $optionUse) {
	$result = '<select name="' . $tagName . '" id="' . $tagName . '">';
	$result .= '<option value="Y"';
	if ($optionUse == "Y")
		$result .= ' selected ';
	$result .= ' >'._("사용").'</option>';

	$result .= '<option value="N"';
	if ($optionUse == "N")
		$result .= ' selected ';
	$result .= ' >'._("미사용").'</option>';
	$result .= '</select>';
	return $result;

}

function MakeUseflag($optionUse) {
	if ($optionUse == "Y")
		$result = _('사용');
	else if ($optionUse == "N")
		$result = _('미사용');

	return $result;
}

function MakePriceTypeSelect($tagName, $priceType) {
	global $r_est_item_price_type;
	$result = '<select name="' . $tagName . '" id="' . $tagName . '">';

	foreach ($r_est_item_price_type as $k => $v) {
		$result .= '<option value="' . $k . '"';
		if ($priceType == $k)
			$result .= ' selected ';
		$result .= ' >' . $v . '</option>';
	}
	return $result;
}

/*
 function getOptionTableHeader($OptionDisplayDataArr, &$colCount = 0) {
 $tableHeader = "";
 if (!empty($OptionDisplayDataArr)) {
 foreach ($OptionDisplayDataArr as $rootKey => $rootValue) {
 $tableHeader = getAdminOptionListHeaderTag($rootValue, $colCount);
 break;
 }
 }

 return $tableHeader;
 }

 function makeOptionTableContents($OptionDisplayDataArr, $tableCntContent, $colCount, $bExcelImport) {
 $tableContent = "";
 $xlsRowIndex = 0;
 if (!empty($OptionDisplayDataArr)) {
 foreach ($OptionDisplayDataArr as $rootKey => $rootValue) {
 //debug($rootValue[code]);
 //echo "<BR>";
 //첫번째 옵션에 대해서만 항목을 만든다.
 if ($rootValue[name] == "item_select_$rootValue[code]_0") {
 //debug($rootValue);
 foreach ($rootValue[option_item] as $subItemKey => $subItemValue) {
 if ($rootValue[same_price_option_item_ID] != '') {
 continue;
 }

 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 if (sizeof($rootValue[same_price_option_item][$subItemKey]) > 1) {
 foreach ($rootValue[same_price_option_item][$subItemKey] as $sameKey => $sameValue)
 $subItemValue .= ", " . $sameValue;
 }

 $tableContent .= "<tr>\r\n";
 //$currItemTag = "<td>$subItemKey - $subItemValue</td>\r\n";
 $currItemTag = "<td>$subItemValue</td>\r\n";
 if (is_array($rootValue['sub_option_' . $subItemKey]) && sizeof($rootValue['sub_option_' . $subItemKey]) > 0) {
 //debug($rootValue['sub_option_' .$subItemKey]);
 $tableContent .= getAdminOptionListTag($rootValue['sub_option_' . $subItemKey], $currItemTag, 2, $tableCntContent, $colCount, $bExcelImport) . "\r\n";
 } else {
 $blankData = "";
 if ($colCount > 1) {
 for ($k = 1; $k < $colCount; $k++)
 $blankData .= "<td>&nbsp;</td>";
 }

 //엑셀에서 가격정보 읽어온다.
 if ($bExcelImport)
 $tableCntContentFixOption = getOptionPriceInputTagWithExcel($subItemKey, $tableCntContent, $rootValue[option_group_type], "FIX");
 else
 $tableCntContentFixOption = getPriceInputTagValueReplace($subItemKey, $tableCntContent, $rootValue[option_group_type]);

 $xlsRowIndex++;

 $tableContent .= $currItemTag . $blankData . $tableCntContentFixOption . "</tr>";
 }
 }
 }
 }
 }
 return $tableContent;
 }

 //그리드 헤더 만들기
 function getAdminOptionListHeaderTag($optionData, &$colCount, $prefixTag = '') {
 $returnData = "";
 if (is_array($optionData[option_item]) && sizeof($optionData[option_item]) > 0) {
 if ($prefixTag)
 $returnData = $prefixTag;
 else {
 $returnData = "<th style='padding:5px 0'>$optionData[display_name]</th>";
 }
 $colCount++;

 //debug($colCount);
 foreach ($optionData[option_item] as $subItemKey => $subItemValue) {

 //debug($optionData['sub_option_' .$subItemKey]);
 if (is_array($optionData['sub_option_' . $subItemKey]) && sizeof($optionData['sub_option_' . $subItemKey]) > 0) {
 $returnData .= getAdminOptionListHeaderTag($optionData['sub_option_' . $subItemKey], $colCount);
 }
 break;
 }
 }
 return $returnData;
 }

 //그리드 내부 테이블구조 만들기. (2차 옵션 부터..실제 그리드 내부 만드는 구조.)
 function getAdminOptionListTag($optionData, $parentValueTag, $subOptionIndex, $PostContent, $colCount, $bExcelImport = false) {
 global $xlsRowIndex;
 $returnData = "";
 $localData = "";
 $localParentData = "";
 //debug($optionData);

 if (is_array($optionData[option_item]) && sizeof($optionData[option_item]) > 0) {
 foreach ($optionData[option_item] as $subItemKey => $subItemValue) {
 //$returnData = "<td>$subItemValue</td>";

 if (is_array($optionData['sub_option_' . $subItemKey]) && sizeof($optionData['sub_option_' . $subItemKey]) > 0) {

 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 if (sizeof($optionData[same_price_option_item][$subItemKey]) > 1) {
 foreach ($optionData[same_price_option_item][$subItemKey] as $sameKey => $sameValue)
 $subItemValue .= ", " . $sameValue;
 }

 //$localParentData = $parentValueTag . "<td>$subItemKey - $subItemValue</td>";
 $localParentData = $parentValueTag . "<td>$subItemValue</td>";
 $OptionIndex = $subOptionIndex + 1;
 $returnData .= getAdminOptionListTag($optionData['sub_option_' . $subItemKey], $localParentData, $OptionIndex, $PostContent, $colCount, $bExcelImport);
 } else {
 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 if (sizeof($optionData[same_price_option_item][$subItemKey]) > 1) {
 foreach ($optionData[same_price_option_item][$subItemKey] as $sameKey => $sameValue)
 $subItemValue .= ", " . $sameValue;
 }

 //$localData = "<td>$subItemKey - $subItemValue</td>";
 $localData = "<td>$subItemValue</td>";
 if ($subItemValue) {
 //2차,3차 옵션 갯수가 적을경우 빈 공간은 만들어준다.
 // ** 빈<td> 태그 만들어 주는 소스상에 약간의 버그가 있을수 있다.추후 다양한 옵션으로 테스트해 볼것     20140312
 $blankData = "";
 if ($colCount > $subOptionIndex) {
 for ($k = $subOptionIndex + 1; $k < $colCount; $k++)
 $blankData .= "<td>&nbsp;</td>";
 }
 $tableCntData = str_replace("[option_name]", $subItemKey, $PostContent);

 //가격정보 불러오기
 if ($bExcelImport)
 $tableCntData = getOptionPriceInputTagWithExcel($subItemKey, $tableCntData, $optionData[option_group_type], "FIX");
 else
 $tableCntData = getPriceInputTagValueReplace($subItemKey, $tableCntData, $optionData[option_group_type]);

 $xlsRowIndex++;

 $returnData .= $parentValueTag . $localData . $blankData . $tableCntData . "</tr>\r\n";
 //getPriceSection($price_rule, $target_cnt);
 }
 //debug($localData);
 }
 }
 }

 return $returnData;
 }

 //설정된 가격 정보 input 태그의 value 값으로 지정하기.
 function getPriceInputTagValueReplace($subItemKey, $tagData, $optionGroupType) {
 global $extraOption, $selectOrderCnt, $selectOrderCntPage;
 $priceRule = $extraOption -> getOptionItemPriceRule($subItemKey);

 //debug($priceRule);
 //debug($subItemKey);
 //debug($tagData);
 //debug($optionGroupType);
 //exit;

 //$orderCntArr = $selectOrderCnt[option_item];
 if ($optionGroupType == "PAGEOPTION")
 $orderCntArr = $extraOption -> PrintPageCntRuleArr;
 else
 $orderCntArr = $extraOption -> PrintCntRuleArr;

 //arsort($orderCntArr);     //큰순으로 정렬변경
 krsort($orderCntArr);
 //큰순으로 정렬변경(key 정렬) 2014.07.18 kdk

 //debug($orderCntArr);
 foreach ($orderCntArr as $key => $value) {
 if ($value && $value != "") {
 $priceData = getPriceSection($priceRule, $value);

 if ($value == '100') {
 //debug($priceRule);
 //debug($value);
 //debug($tagData);
 //exit;
 }
 if ($priceData[1] < 0) {
 $priceData[0] = '0';
 $priceData[1] = '0';
 }

 //debug($orderCntArr);
 //debug($tagData);

 $tagData = str_replace("[supply_option_price]_$value", $priceData[0], $tagData);
 $tagData = str_replace("[sale_option_price]_$value", $priceData[1], $tagData);
 }
 }
 //debug($tagData);
 //exit;
 return $tagData;
 }

 //설정된 가격 정보  읽어와서 배열 구조로 만들기
 function setOptionPriceWithRule($subItemKey, $arrIndex, $optionGroupType) {
 global $extraOption, $selectOrderCnt, $OptionCnt;
 $priceRule = $extraOption -> getOptionItemPriceRule($subItemKey);

 //$orderCntArr = $selectOrderCnt[option_item];
 if ($optionGroupType == "PAGEOPTION")
 $orderCntArr = $extraOption -> PrintPageCntRuleArr;
 else
 $orderCntArr = $extraOption -> PrintCntRuleArr;

 //arsort($orderCntArr);     //큰순으로 정렬변경
 foreach ($orderCntArr as $key => $value) {
 if ($value && $value != "") {
 $priceData = getPriceSection($priceRule, $value);
 //debug($priceRule);
 //debug($value);
 //debug($priceData);
 if ($priceData[1] < 0) {
 $priceData[0] = '0';
 $priceData[1] = '0';
 }

 $OptionCnt[$arrIndex][$value]['suply'] = $priceData[0];
 $OptionCnt[$arrIndex][$value]['sale'] = $priceData[1];
 }
 }

 }

 //엑셀에서 읽어온 가격 정보 input 태그의 value 값으로 지정하기.
 function getOptionPriceInputTagWithExcel($subItemKey, $tagData, $optionGroupType, $optionKindStepCode = "FIX") {
 global $extraOption, $xlsOptionPrice, $xlsAfterOptionPrice, $xlsRowIndex;

 //일반 옵션의 경우 와 후가공 옵션으로 나누어져서 처리한다.
 if ($optionKindStepCode == "FIX")
 $col = sizeof($xlsOptionPrice[$xlsRowIndex]) - 1;
 else
 $col = sizeof($xlsAfterOptionPrice[$xlsRowIndex]) - 1;

 //$orderCntArr = $selectOrderCnt[option_item];

 if ($optionGroupType == "PAGEOPTION")
 $orderCntArr = $extraOption -> PrintPageCntRuleArr;
 else
 $orderCntArr = $extraOption -> PrintCntRuleArr;

 //debug($optionGroupType);
 //debug($orderCntArr);

 //arsort($orderCntArr);     //큰순으로 정렬변경
 krsort($orderCntArr);
 //큰순으로 정렬변경(key 정렬) 2014.07.18 kdk

 foreach ($orderCntArr as $key => $value) {
 if ($value && $value != "") {
 if ($value == '0~320~4') {
 //debug($xlsOptionPrice[$xlsRowIndex]);
 //debug($xlsAfterOptionPrice[$xlsRowIndex][$col]);
 //exit;
 }

 if ($optionKindStepCode == "FIX") {
 $tagData = str_replace("[supply_option_price]_$value", $xlsOptionPrice[$xlsRowIndex][$col - 1], $tagData);
 $tagData = str_replace("[sale_option_price]_$value", $xlsOptionPrice[$xlsRowIndex][$col], $tagData);
 } else {
 $tagData = str_replace("[supply_option_price]_$value", $xlsAfterOptionPrice[$xlsRowIndex][$col - 1], $tagData);
 $tagData = str_replace("[sale_option_price]_$value", $xlsAfterOptionPrice[$xlsRowIndex][$col], $tagData);
 }
 $col = $col - 2;
 }
 }
 return $tagData;
 }

 //엑셀에서 읽어온 가격 정보 input 태그의 value 값으로 지정하기.
 function getAfterOptionPriceInputTagWithExcel($subItemKey, $tagData, $optionGroupType) {
 global $extraOption, $selectOrderCnt, $xlsAfterOptionPrice, $xlsRowIndex;

 $col = sizeof($xlsAfterOptionPrice[$xlsRowIndex]) - 1;
 //$orderCntArr = $selectOrderCnt[option_item];
 if ($optionGroupType == "PAGEOPTION")
 $orderCntArr = $extraOption -> PrintPageCntRuleArr;
 else
 $orderCntArr = $extraOption -> PrintCntRuleArr;

 //arsort($orderCntArr);     //큰순으로 정렬변경
 krsort($orderCntArr);
 //큰순으로 정렬변경(key 정렬) 2014.07.18 kdk

 foreach ($orderCntArr as $key => $value) {
 if ($value && $value != "") {
 if ($value == '0~320~4') {
 //debug($xlsAfterOptionPrice);
 //debug($xlsAfterOptionPrice[$xlsRowIndex][$col]);
 //exit;
 }
 $tagData = str_replace("[supply_option_price]_$value", $xlsAfterOptionPrice[$xlsRowIndex][$col - 1], $tagData);
 $tagData = str_replace("[sale_option_price]_$value", $xlsAfterOptionPrice[$xlsRowIndex][$col], $tagData);
 $col = $col - 2;
 }
 }
 return $tagData;
 }

 //엑셀파일 헤더 만들기
 function setXlsFileOptionListHeader(&$OptionArr, $optionData, $colIndex) {
 global $rowIndex;
 if (is_array($optionData[option_item]) && sizeof($optionData[option_item]) > 0) {
 $OptionArr[$rowIndex][$colIndex] = $optionData[display_name];
 $OptionArr[$rowIndex + 1][$colIndex] = '';
 //cell merge 를 위한 빈값.

 $colIndex++;
 foreach ($optionData[option_item] as $subItemKey => $subItemValue) {
 if (is_array($optionData['sub_option_' . $subItemKey]) && sizeof($optionData['sub_option_' . $subItemKey]) > 0) {
 setXlsFileOptionListHeader($OptionArr, $optionData['sub_option_' . $subItemKey], $colIndex);
 }
 break;
 }
 }
 }

 //엑셀 파일 내부 테이블구조 만들기.
 function setXlsFileOptionList($optionData, $colIndex) {
 global $extraOption, $FixOption, $rowIndex, $LastOptionIndex;

 if (is_array($optionData[option_item]) && sizeof($optionData[option_item]) > 0) {
 foreach ($optionData[option_item] as $subItemKey => $subItemValue) {
 //$returnData = "<td>$subItemValue</td>";

 if (is_array($optionData['sub_option_' . $subItemKey]) && sizeof($optionData['sub_option_' . $subItemKey]) > 0) {
 //$FixOption[$rowIndex][$colIndex] = "$subItemKey - $subItemValue";
 $FixOption[$rowIndex][$colIndex] = "$subItemValue";

 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 $FixOption[$rowIndex][$colIndex] .= $extraOption -> getSamePriceOptionItemName($subItemKey);

 $colIndexLocal = $colIndex + 1;
 setXlsFileOptionList($optionData['sub_option_' . $subItemKey], $colIndexLocal);

 } else {
 //$FixOption[$rowIndex][$colIndex] = "$subItemKey - $subItemValue";
 $FixOption[$rowIndex][$colIndex] = "$subItemValue";
 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 $FixOption[$rowIndex][$colIndex] .= $extraOption -> getSamePriceOptionItemName($subItemKey);

 setOptionPriceWithRule($subItemKey, $rowIndex, $optionData[option_group_type]);

 //debug($LastOptionIndex);
 if ($LastOptionIndex == $optionData[same_price_option_item][optino_kind_index]) {
 $rowIndex++;
 } else {
 $rowIndex++;
 copyOptionData($rowIndex, $colIndex);
 //$colIndex++;
 }
 }
 }
 }
 }

 function copyOptionData($targetRowIndex, $sourceColIndex) {
 global $FixOption;

 for ($i = 0; $i < $sourceColIndex; $i++) {
 $FixOption[$targetRowIndex][$i] = $FixOption[$targetRowIndex - 1][$i];
 }
 }

 //엑셀저장(x ,y 변경 관련)
 function makeOptionTableContents3($OptionDisplayDataArr, $tableCntContent, $colCount, $bExcelImport) {
 $tableContent = "";
 $xlsRowIndex = 0;
 if (!empty($OptionDisplayDataArr)) {
 foreach ($OptionDisplayDataArr as $rootKey => $rootValue) {
 //첫번째 옵션에 대해서만 항목을 만든다.
 if ($rootValue[name] == "item_select_$rootValue[code]_0") {
 //debug($rootValue);
 foreach ($rootValue[option_item] as $subItemKey => $subItemValue) {
 if ($rootValue[same_price_option_item_ID] != '') {
 continue;
 }

 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 if (sizeof($rootValue[same_price_option_item][$subItemKey]) > 1) {
 foreach ($rootValue[same_price_option_item][$subItemKey] as $sameKey => $sameValue)
 $subItemValue .= ", " . $sameValue;
 }

 $currItemTag = "$subItemValue <br>";
 if (is_array($rootValue['sub_option_' . $subItemKey]) && sizeof($rootValue['sub_option_' . $subItemKey]) > 0) {
 //debug($rootValue['sub_option_' .$subItemKey]);
 $tableContent .= getAdminOptionListTag3($rootValue['sub_option_' . $subItemKey], $currItemTag, 2, $tableCntContent, $colCount, $bExcelImport) . "\r\n";
 } else {
 //$tableCntData = str_replace("[option_name]", $subItemKey, $tableCntContent);
 //debug($tableCntData);
 $tableContent .= $currItemTag . "|" . $subItemKey . "\r\n";
 }
 }
 }
 }
 }

 return $tableContent;
 }

 //그리드 내부 테이블구조 만들기. (2차 옵션 부터..실제 그리드 내부 만드는 구조.)
 function getAdminOptionListTag3($optionData, $parentValueTag, $subOptionIndex, $PostContent, $colCount, $bExcelImport = false) {
 global $xlsRowIndex;
 $returnData = "";
 $localData = "";
 $localParentData = "";
 //debug($optionData);

 if (is_array($optionData[option_item]) && sizeof($optionData[option_item]) > 0) {
 foreach ($optionData[option_item] as $subItemKey => $subItemValue) {
 //$returnData = "<td>$subItemValue</td>";
 if (is_array($optionData['sub_option_' . $subItemKey]) && sizeof($optionData['sub_option_' . $subItemKey]) > 0) {

 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 if (sizeof($optionData[same_price_option_item][$subItemKey]) > 1) {
 foreach ($optionData[same_price_option_item][$subItemKey] as $sameKey => $sameValue)
 $subItemValue .= ", " . $sameValue;
 }

 //$localParentData = $parentValueTag . "<td>$subItemKey - $subItemValue</td>";
 $localParentData = $parentValueTag . "$subItemValue <br>";
 $OptionIndex = $subOptionIndex + 1;
 $returnData .= getAdminOptionListTag3($optionData['sub_option_' . $subItemKey], $localParentData, $OptionIndex, $PostContent, $colCount, $bExcelImport);
 } else {
 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 if (sizeof($optionData[same_price_option_item][$subItemKey]) > 1) {
 foreach ($optionData[same_price_option_item][$subItemKey] as $sameKey => $sameValue)
 $subItemValue .= ", " . $sameValue;
 }

 $localData = "$subItemValue|$subItemKey";
 //$localData = "$subItemValue($subItemKey)|$subItemKey";
 if ($subItemValue) {
 $tableCntData = str_replace("[option_name]", $subItemKey, $PostContent);
 $xlsRowIndex++;

 //$returnData .= $parentValueTag . $localData . $blankData. "</th>\r\n";
 $returnData .= $parentValueTag . $localData . "\r\n";
 }
 //debug($localData);
 //debug($tableCntData);
 }
 }
 }

 return $returnData;
 }

 //설정된 가격 정보  읽어와서 배열 구조로 만들기
 function setOptionPriceWithRule2($subItemKey, $arrIndex, $optionGroupType) {
 global $extraOption, $selectOrderCnt, $OptionCnt;
 $priceRule = $extraOption -> getOptionItemPriceRule($subItemKey);

 //$orderCntArr = $selectOrderCnt[option_item];
 if ($optionGroupType == "PAGEOPTION")
 $orderCntArr = $extraOption -> PrintPageCntRuleArr;
 else
 $orderCntArr = $extraOption -> PrintCntRuleArr;

 //arsort($orderCntArr);     //큰순으로 정렬변경
 foreach ($orderCntArr as $key => $value) {
 if ($value && $value != "") {
 $priceData = getPriceSection($priceRule, $value);
 //debug($priceRule);
 //debug($value);
 //debug($priceData);
 if ($priceData[1] < 0) {
 $priceData[0] = '0';
 $priceData[1] = '0';
 }

 $OptionCnt[$subItemKey][$value]['suply'] = $priceData[0];
 $OptionCnt[$subItemKey][$value]['sale'] = $priceData[1];
 }
 }
 }

 //엑셀 파일 내부 테이블구조 만들기.
 function setXlsFileOptionList2($optionData, $colIndex) {
 global $extraOption, $FixOption, $rowIndex, $LastOptionIndex;

 if (is_array($optionData[option_item]) && sizeof($optionData[option_item]) > 0) {
 foreach ($optionData[option_item] as $subItemKey => $subItemValue) {
 //$returnData = "<td>$subItemValue</td>";

 if (is_array($optionData['sub_option_' . $subItemKey]) && sizeof($optionData['sub_option_' . $subItemKey]) > 0) {
 //$FixOption[$rowIndex][$colIndex] = "$subItemKey - $subItemValue";
 $FixOption[$rowIndex][$colIndex] = "$subItemValue";

 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 $FixOption[$rowIndex][$colIndex] .= $extraOption -> getSamePriceOptionItemName($subItemKey);

 $colIndexLocal = $colIndex + 1;
 setXlsFileOptionList2($optionData['sub_option_' . $subItemKey], $colIndexLocal);

 } else {
 //$FixOption[$rowIndex][$colIndex] = "$subItemKey - $subItemValue";
 $FixOption[$rowIndex][$colIndex] = "$subItemValue";
 //같은가격 설정 옵션이 있을 경우 같이 보여준다.
 $FixOption[$rowIndex][$colIndex] .= $extraOption -> getSamePriceOptionItemName($subItemKey);

 setOptionPriceWithRule2($subItemKey, $rowIndex, $optionData[option_group_type]);

 //debug($LastOptionIndex);
 if ($LastOptionIndex == $optionData[same_price_option_item][optino_kind_index]) {
 $rowIndex++;
 } else {
 $rowIndex++;
 copyOptionData($rowIndex, $colIndex);
 //$colIndex++;
 }
 }
 }
 }
 }

 //엑셀저장(x ,y 변경 관련)
 */
##############################################
?>