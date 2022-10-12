<?
/**
 * Extra Option Class (자동견적 클래스)
 *
 * class.extra.option.php +
 * class.admin.extra.option.php(삭제예정) +
 * class.admin.extra.option.s2.php(삭제예정) +
 * class.admin.extra.option.s3.php(삭제예정) +
 *
 * 사용자와 관리자 그리고 프리셋이 추가 시 생성된 클래스 파일을 하나로 통합.
 * 2017.01.24 ~
 * kdk
 */

class ExtraOption {

	var $db;
	var $cid;
	var $center_id;
	var $sess;
	var $cfg_center;

	var $optionKindCodeArr;

	var $OptionData;
	var $OptionUseData;

	var $GoodsNo;
	var $GoodsKind;
	var $Preset;
	//프리셋코드

	var $m_option;
	//DB models.
	var $DocumentSizeScriptTag;

	var $MaxOptionKindIndex;

	var $OrderMemoUse;
	//주문제목, 주문 메모 사용여부 체크

	var $OptionGroupType;
	//책자상품 그룹에 사용
	var $OptionGroupTypeArr;
	//option_group_type
	var $ExtraTblKindCodeArr;
	// extra_tbl_kind_code

	var $InsertItemArr = array();
	var $SamePriceItemArr = array();
	var $InsertItemTblExtraIndexMappingArr = array();
	//실제 추가된 옵션과 tbl_extra_index 간의 맵핑정보를 저장한다.

	function ExtraOption($orderOptionData = '', $bPreSet = false) {
		include_once dirname(__FILE__) . "/../models/m_extra_option.php";
		$this -> m_option = new M_extra_option();

		$this -> db = $GLOBALS[db];
		$this -> sess = $GLOBALS[sess];
		$this -> cfg_center = $GLOBALS[cfg_center];

		$this -> PrintCntRuleArr = array();
		$this -> PrintPageCntRuleArr = array();

		$this -> InsertItemArr[0][] = array('11' => '11');

		//신규 프리셋인경우.
		if ($bPreSet) {
			$this -> center_id = 'ilark';
			$this -> cid = 'preset';
		} else {
			$this -> center_id = $this -> cfg_center[center_cid];
			$this -> cid = $GLOBALS[cid];
		}

		$this -> optionKindCodeArr = array();
	}

	function SetPreset($preset) {
		if ($preset)
			$this -> Preset = $preset;
	}

	function SetGoodsKind($goodskind) {
		if ($goodskind)
			$this -> GoodsKind = $goodskind;
	}

	function SetOptionGroupType($optiongrouptype) {
		if ($optiongrouptype)
			$this -> OptionGroupType = $optiongrouptype;
	}
	
	//책자 프리셋3(100112) 표지 필수,선택 후가공 수량은 표지수량을 사용한다. / 2017.05.11 / kdk.
	function SetCoverPrintCntRule($goodsno) {
		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, "C-OCNT");
		
		if($order_cnt)
			$this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
	}

	//DB 조회한 옵션 정보를 배열로 만든다.
	function setOptionData($data, $nameIndex) {
		$selectTag = array();
		$selectTag[item_id] = $data[id];
		$selectTag[code] = $data[option_kind_code];
		$selectTag[name] = "item_select_$data[option_kind_code]_$nameIndex";
		$selectTag[id] = "item_select_$data[option_kind_code]_$nameIndex";
		$selectTag[item_name] = $data[item_name];
		$selectTag[display_name] = $data[display_name];
		$selectTag[necessary_flag] = $data[necessary_flag];
		$selectTag[extra_data1] = $data[extra_data1];
		$selectTag[extra_data2] = $data[extra_data2];
		$selectTag[option_kind_index] = $data[option_kind_index];
		$selectTag[option_item_index] = $data[option_item_index];

		$selectTag[necessary_flag] = $data[necessary_flag];
		$selectTag[parent_item_name] = $data[parent_item_name];
		$selectTag[have_child] = $data[have_child];
		$selectTag[same_price_item_name] = $data[same_price_item_name];
		$selectTag[item_price_type] = $data[item_price_type];
		$selectTag[option_group_type] = $data[option_group_type];
		$selectTag[extra_tbl_kind_code] = $data[extra_tbl_kind_code];
		$selectTag[extra_tbl_kind_index] = $data[extra_tbl_kind_index];
		$selectTag[display_flag] = $data[display_flag];
		$selectTag[option_kind_code] = $data[option_kind_code];

		return $selectTag;
	}

	//모든 옵션 DB 를 조회한다.
	function GetOptionDataInDB($goodsno, $optionGroupType = '', $goodsKind = '', $kindCode = '') {
		$res = $this -> m_option -> getAdminOptionList($this -> cid, $this -> center_id, $goodsno, "Y", $optionGroupType, $kindCode);

		$selectIndex = 0;
		foreach ($res as $key => $data) {
			$this -> OptionData[$data[ID]] = $this -> setOptionData($data, $selectIndex);
			$selectIndex++;
		}

		$res = $this -> m_option -> getAdminOptionUseList($this -> cid, $this -> center_id, $goodsno);
		foreach ($res as $key => $data) {
			$this -> OptionUseData[$data[option_kind_index]] = $data[use_flag];
		}
	}

	function GetItemPriceType($option_group_type, $option_kind_code) {
		foreach ($this->OptionData as $key => $value) {
			if ($value[option_group_type] == $option_group_type) {
				if ($option_group_type == "AFTEROPTION") {
					if ($value[option_kind_code] == $option_kind_code) {
						return $value[item_price_type];
					}
				} else
					return $value[item_price_type];
			}
		}
	}

	function GetPriceRuleDividePrintCnt($price_rule) {
		$price_arr = explode(";", $price_rule);
		$result = array();

		//debug($price_rule);
		foreach ($price_arr as $key => $value) {
			if ($value) {
				$cnt_arr = explode("~", $value);
				//debug($cnt_arr);

				//구간별 가격 설정이 되어 있는 경우     ex)1~500~100~110;501~1000~80~90;1001~2000~60~50   //시작,종료,원가,판매가;
				if (sizeof($cnt_arr) > 3)
					$result[] = array($cnt_arr[0] . "-" . $cnt_arr[1], $cnt_arr[2], $cnt_arr[3]);

				//구간별 가격 설정이 없고 수량별 가격설정만 되어 있는 경우     ex)100~110~120;200~100~110;300~90;400~80~90;     //수량,원가,판매가
				else if (sizeof($cnt_arr) > 2)
					$result[] = array($cnt_arr[0], $cnt_arr[1], $cnt_arr[2]);

				//가격에 범위가 없을 경우 그냥 판매가격으로 계산      //원가,판매가
				else
					$result[] = array("-", $cnt_arr[0], $cnt_arr[1]);
			}
		}
		return $result;
	}

	#사용여부 확인 필요함.
	function getPriceRuleDividePrintCntNew($price_rule) {
		$price_arr = explode(";", $price_rule);
		$result = array();

		//debug($price_rule);
		foreach ($price_arr as $key => $value) {
			if ($value) {
				$cnt_arr = explode("~", $value);
				//debug($cnt_arr);

				//구간별 가격 설정이 되어 있는 경우     ex)1~500~100~110;501~1000~80~90;1001~2000~60~50   //시작,종료,원가,판매가;
				if (sizeof($cnt_arr) > 3)
					$result[$cnt_arr[0] . "-" . $cnt_arr[1]] = array($cnt_arr[2], $cnt_arr[3]);

				//구간별 가격 설정이 없고 수량별 가격설정만 되어 있는 경우     ex)100~110~120;200~100~110;300~90;400~80~90;     //수량,원가,판매가
				else if (sizeof($cnt_arr) > 2)
					$result[$cnt_arr[0]] = array($cnt_arr[1], $cnt_arr[2]);

				//가격에 범위가 없을 경우 그냥 판매가격으로 계산      //원가,판매가
				else
					$result[] = array($cnt_arr[0], $cnt_arr[1]);
			}
		}
		return $result;
	}

	//가격 관련 전체 테이블 만든다.
	function GetPriceTotalTable($InsertExtraOptionTable) {
		$rowIndex = 0;
		$result = array();
		foreach ($InsertExtraOptionTable as $itemkey => $itemvalue) {
			$price_rule = $itemvalue[option_price];
			$price_arr = explode(";", $price_rule);

			//debug($price_rule);
			foreach ($price_arr as $key => $value) {
				if ($value) {
					$cnt_arr = explode("~", $value);
					//debug($cnt_arr);

					//구간별 가격 설정이 되어 있는 경우     ex)1~500~100~110;501~1000~80~90;1001~2000~60~50   //시작,종료,원가,판매가;
					if (sizeof($cnt_arr) > 3)
						$result[$rowIndex][$cnt_arr[0] . "~" . $cnt_arr[1]] = array($cnt_arr[2], $cnt_arr[3]);
					// "-" => "~" 수정.

					//구간별 가격 설정이 없고 수량별 가격설정만 되어 있는 경우     ex)100~110~120;200~100~110;300~90;400~80~90;     //수량,원가,판매가
					else if (sizeof($cnt_arr) > 2)
						$result[$rowIndex][$cnt_arr[0]] = array($cnt_arr[1], $cnt_arr[2]);

					//가격에 범위가 없을 경우 그냥 판매가격으로 계산      //원가,판매가
					else
						$result[$rowIndex][] = array($cnt_arr[0], $cnt_arr[1]);
				}
			}

			$rowIndex++;
		}
		return $result;
	}

	#사용여부 확인 필요함.
	function getParentInsertExtraOption($parent_name) {
		foreach ($this->OptionData as $key => $value) {
			if ($value[item_name] == $parent_name) {
				return $value[option_kind_index];
			}
		}
		return "";
	}

	//출력할 옵션 분류 코드 분리하기
	function GetOptionKind($goodsno, $optionGroupType = '', $goodsKind = '', $kindCode = '') {
		$this -> GoodsNo = $goodsno;
		$this -> DocumentSizeScriptTag = '';
		$this -> GetOptionDataInDB($goodsno, $optionGroupType, $goodsKind, $kindCode);

		if (is_array($this -> OptionData)) {
			foreach ($this->OptionData as $key => $value) {
				$this -> optionKindCodeArr[$value[code]] = $value[code];

				if ($value[code] == "DOCUMENT") {
					$this -> DocumentSizeArr[$value[item_name]] = $value[extra_data1] . "x" . $value[extra_data2];
					$this -> DocumentSizeScriptTag .= "\"" . $value[item_name] . '":"' . $value[extra_data1] . "x" . $value[extra_data2] . '",';
				}

				$this -> MaxOptionKindIndex = $value[option_kind_index];
			}

			foreach ($this->optionKindCodeArr as $key => $value)
				$this -> javascriptArrayTag .= "\"" . $key . "\",";
		}

		$this -> DocumentSizeScriptTag = substr($this -> DocumentSizeScriptTag, 0, -1);
		return $this -> optionKindCodeArr;
	}

	function GetDisplayName($optionKindIndex) {
		$result = "";
		if (is_array($this -> OptionData)) {
			foreach ($this->OptionData as $dataKey => $data) {
				if ($data[option_kind_index] == ($optionKindIndex)) {
					$result = $data[display_name];
					break;
				}
			}
		}

		return $result;
	}

	//주문 부수 (수량) 조회
	function GetOrderCnt($goodsno, $kind_code = "OCNT", $unitCntFlag = "") {
		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

		if ($unitCntFlag == "Y")
			return $order_cnt[unit_cnt_rule];
		else
			return $order_cnt[cnt_rule];
	}

	//주문 부수 (수량) 한계치 조회 2015.02.10 by kdk
	function GetOrderDisplayCnt($goodsno, $kind_code = "C-OCNT") {
		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

		return $order_cnt[display_cnt_rule];
	}

	//옵션항목을 넘겨준다. //studio uploader에서 xml 생성시 필요하다. 2015.03.19 by kdk
	function GetOptionDataByOptionKindIndex($option_kind_index) {
		$result = array();
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $option_kind_index) {
				$result[] = $data;
			}
		}

		return $result;
	}

	function GetOptionDataByOptionKindIndexAndParentItemName($option_kind_index, $option_kind_code, $parent_item_name) {
		$result = array();
		if (is_array($this -> OptionData)) {
			foreach ($this->OptionData as $dataKey => $data) {
				if ($data[option_kind_index] == $option_kind_index && $data[option_kind_code] == $option_kind_code && $data[parent_item_name] == $parent_item_name) {
					$result[] = $data;
				}
			}
		}

		return $result;
	}

	//사용자 수량(단위) 조회
	function GetUserCntName($goodsno, $kind_code = "OCNT", $unitCntFlag = "") {
		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

		if ($unitCntFlag == "Y")
			return $order_cnt[user_unit_cnt_rule_name];
		else
			return $order_cnt[user_cnt_rule_name];
	}

	//사용자 수량 입력 여부 조회
	function GetUserCntInputFlage($goodsno, $kind_code = "OCNT") {
		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

		return $order_cnt[user_cnt_input_flag];
	}

	function GetOptionKindUse($optionKindIndex) {
		$result = $this -> OptionUseData[$optionKindIndex];

		if ($result == '')
			$result = 'Y';
		return $result;
	}

	function GetOptionItemValue($optionKindIndex, $optionItemIndex) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $optionKindIndex && $data[same_price_item_name] == '') {
				if ($optionItemIndex == $data[option_item_index]) {
					$result = $data[item_name];
					break;
				}
			}
		}

		return $result;
	}

	function GetExtraData1($optionKindIndex) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $optionKindIndex) {
				$result = $data[extra_data1];
				break;
			}
		}

		return $result;
	}

	function GetExtraData2($optionKindIndex) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $optionKindIndex) {
				$result = $data[extra_data2];
				break;
			}
		}

		return $result;
	}

	function GetExtraData1ByItem($itemName, $optionGroupType) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[item_name] == $itemName && $data[option_group_type] == $optionGroupType) {
				$result = $data[extra_data1];
				break;
			}
		}

		return $result;
	}

	function GetExtraData2ByItem($itemName, $optionGroupType) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[item_name] == $itemName && $data[option_group_type] == $optionGroupType) {
				$result = $data[extra_data2];
				break;
			}
		}

		return $result;
	}

	//사용자 화면에서 사용.
	function GetAfterOptionData() {
		$result = array();
		foreach ($this->OptionData as $dataKey => $data) {
			if($this->GetOptionKindUse($data[option_kind_index]) == "Y") { //사용 항목 처리
				if ($this->Preset == "100112") {
					//if ($data[option_group_type] == 'AFTEROPTION' && ($data[option_kind_index] != '2' && $data[option_kind_index] != '3')) {//제본방식,형식 제외
					if ($data[option_group_type] == 'AFTEROPTION' && ($data[extra_tbl_kind_code] != 'D-OP' && $data[extra_tbl_kind_code] != 'F-OP')) {//후가공옵션만 가져외기(extra_tbl_kind_code:OP1,OP2...)
						if (!in_array($data[option_kind_index], $result))
							$result[$data[option_kind_index]] = array(
								'code' => $data[code], 
								'name' => $data[display_name], 
								'optionKindIndex' => $data[option_kind_index],
								'id' => "item_select_$data[code]_0",
								'price' => $data[item_price_type]
							);
					}
				} else {
					if ($data[option_group_type] == 'AFTEROPTION') {
						if (!in_array($data[option_kind_index], $result))
							$result[$data[option_kind_index]] = array(
								'code' => $data[code], 
								'name' => $data[display_name], 
								'optionKindIndex' => $data[option_kind_index],
								'id' => "item_select_$data[code]_0",
								'price' => $data[item_price_type]
							);
					}
				}
			}	
		}

		return $result;
	}

	//후가공에 속한 option_kind_index 를 넘겨준다.    //관리자에서 후가공 동적 생성시 필요하다.
	function GetAfterOptionKindIndex() {
		$result = array();
		foreach ($this->OptionData as $dataKey => $data) {
			if ($this -> Preset == "100112") {
				//if ($data[option_group_type] == 'AFTEROPTION' && ($data[option_kind_index] != '2' && $data[option_kind_index] != '3')) {//제본방식,형식 제외
				if ($data[option_group_type] == 'AFTEROPTION' && ($data[extra_tbl_kind_code] != 'D-OP' && $data[extra_tbl_kind_code] != 'F-OP')) {//후가공옵션만 가져외기(extra_tbl_kind_code:OP1,OP2...)
					if (!in_array($data[option_kind_index], $result))
						$result[] = $data[option_kind_index];
				}
			} else {
				if ($data[option_group_type] == 'AFTEROPTION') {
					if (!in_array($data[option_kind_index], $result))
						$result[] = $data[option_kind_index];
				}
			}
		}

		return $result;
	}

	function GetAfterOptionPriceType($optionKindCode) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_group_type] == 'AFTEROPTION' && $data[option_kind_code] == $optionKindCode) {
				$result = $data[item_price_type];
				break;
			}
		}

		return $result;
	}

	// option_group_type 별로 item_price_type 조회 2014.09.17 by kdk
	function GetOptionPriceType($optionGroupType) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_group_type] == $optionGroupType) {
				$result = $data[item_price_type];
				break;
			}
		}

		return $result;
	}

	//사용자 "/goods/view_option~.php" 에서 사용.
	function GetOrderMemoUse($goodsno) {
		//주문 제목, 메모 책자(면지,간지) 사용여부 조회
		$res = $this -> m_option -> getAdminOptionUseList($this -> cid, $this -> center_id, $goodsno);

		foreach ($res as $key => $data) {
			if ($data[option_kind_index] == '96' || $data[option_kind_index] == '97' || $data[option_kind_index] == '98' || $data[option_kind_index] == '99')
				$this -> OrderMemoUse[$data[option_kind_index]] = $data[use_flag];
		}
	}

	//사용자 "/goods/view_option~.php", "/order/cart_extra_option_update_pop_~.php, order_update_webhard_extra.php" 에서 사용.
	function GetExtraTblKindCode() {
		foreach ($this->OptionData as $dataKey => $data) {
			$this -> ExtraTblKindCodeArr[$data[extra_tbl_kind_code]] = $data[extra_tbl_kind_code];
		}
	}

	//사용자 "/goods/view_option~.php", "/order/cart_extra_option_update_pop_~.php, order_update_webhard_extra.php" 에서 사용.
	function GetOptionGroupType() {
		foreach ($this->OptionData as $dataKey => $data) {
			$this -> OptionGroupTypeArr[$data[option_group_type]] = $data[option_group_type];
		}
	}

	//관리자 "/admin/goods/extra_option_preset_tree.php"에서 사용.
	//용지 3차 구조에서 하위 index를 조회.
	function GetChildOptionKindIndex($data, $index) {
		if ($data) {
			foreach ($data as $key => $value) {
				if ($key)
					return $key;
			}
		}
	}

	function GetOptionKindCode($optionKindIndex) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == ($optionKindIndex)) {
				$result = $data[option_kind_code];
				break;
			}
		}

		return $result;
	}

	function GetExtraTblKindCodeByOptionKindIndex($optionKindIndex) {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == ($optionKindIndex)) {
				$result = $data[extra_tbl_kind_code];
				break;
			}
		}

		return $result;
	}

	function getFirstItemValue($optionKindIndex, $parentItemValue = '') {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $optionKindIndex && $data[same_price_item_name] == '') {
				if ($parentItemValue) {
					if ($parentItemValue == $data[parent_item_name]) {
						$result = $data[item_name];
						break;
					}
				} else {
					$result = $data[item_name];
					break;
				}
			}
		}

		return $result;
	}

	function getFirstItemValueByOptionKindCode($optionKindIndex, $optionKindCode, $parentItemValue = '') {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $optionKindIndex && $data[option_kind_code] == $optionKindCode && $data[same_price_item_name] == '') {
				if ($parentItemValue) {
					if ($parentItemValue == $data[parent_item_name]) {
						$result = $data[item_name];
						break;
					}
				} else {
					$result = $data[item_name];
					break;
				}
			}
		}

		return $result;
	}

	function getFirstOptionGroupTypeValue($optionKindIndex, $parentItemValue = '') {
		$result = "";
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $optionKindIndex && $data[same_price_item_name] == '') {
				if ($parentItemValue) {
					if ($parentItemValue == $data[parent_item_name]) {
						$result = $data[option_group_type];
						break;
					}
				} else {
					$result = $data[option_group_type];
					break;
				}
			}
		}

		return $result;
	}

	//사용자 상품이미지 조회.
	function GetImgData($url, $templateSetIdx, $templateIdx) {
		global $cfg, $editor;
		$url = base64_decode($url);
		//debug($url);

		//주문처리 또는 장바구니 처리시 URL에 포함된 spid가 복수일 경우 누락된 정보가 있음. 어디서 누락된지 모름.
		//복수 편집기 실행 관련 2016.03.16 kdk
		//spring , pod_group 스킨 우선 적용.
		if ($cfg[skin] == "spring" || $cfg[skin] == "pod_group" || $cfg[skin] == "classic") {
			$podsno = "";
			foreach ($editor as $key => $val) {
				if ($val[podsno] && $val[podsno] != "") {
					$podsno .= $val[podsno] . ",";
				}
			}
			$spid = $podsno;
		}

		$mUrl = "";
		$exp = explode('&', $url);
		//항목 분리
		//debug($exp);
		if (count($exp) > 0) {
			foreach ($exp as $key => $val) {
				$pos = strrpos($val, "spid=");
				if ($pos === false) {
					$mUrl .= $val . "&";
				} else {
					$mUrl .= "spid=$spid&";
				}
			}
			$mUrl = substr($mUrl, 0, -1);
		}
		if ($url != $mUrl)
			$url = $mUrl;

		if ($cfg[podsiteid]) {//몰에 podsiteid가 설정된 경우
			$mUrl = "";
			$exp = explode('&', $url);
			//항목 분리
			//debug($exp);
			if (count($exp) > 0) {
				foreach ($exp as $key => $val) {
					$pos = strrpos($val, "p_siteid=");
					if ($pos === false) {
						$mUrl .= $val . "&";
					} else {
						$mUrl .= "p_siteid=$cfg_center[podsiteid]&";
					}
				}
				$mUrl = substr($mUrl, 0, -1);
			}
			if ($url != $mUrl)
				$url = $mUrl;
		}

		$data[templateSetIdx] = $templateSetIdx;
		$data[templateIdx] = $templateIdx;
		//$soapurl = "http://" .PODS20_DOMAIN. "/WebService/tempset_info.aspx?templateSetIdx=".$templateSetIdx;
		//debug($url);
		$soapurl = $url;
		$ret = readUrlWithcurl($soapurl, false);
		$obj = json_decode($ret, TRUE);
		//debug($obj);
		if ($obj[data]) {
			foreach ($obj[data] as $item) {
				//debug($item);
				foreach ($item as $key => $val) {
					//debug($val);
					if ($val[templateData]) {
						if ($val[templateSetIdx] == $templateSetIdx && $val[templateData][0][templateIdx] == $templateIdx) {
							if ($val[templateData][0][templateURL])
								$tempURL .= $val[templateData][0][templateURL] . "||";
							$templateName = $val[templateData][0][templateName];
						}
					} else {
						if ($val[templateSetIdx] == $templateSetIdx && $val[templateIdx] == $templateIdx) {
							if ($val[templateURL])
								$tempURL .= $val[templateURL] . "||";
							$templateName = $val[templateName];
						}
					}
				}
			}

			$template = array();
			foreach ($obj[data] as $item) {
				//debug($item);
				foreach ($item as $key => $val) {
					//debug($val);
					if ($val[templateData]) {
						if ($val[templateData][0][templateName] == $templateName) {
							$templateName = $val[templateData][0][templateName];
							$template[$val[siteProductCode]] = array('templateSetIdx' => $val[templateSetIdx], 'templateIdx' => $val[templateData][0][templateIdx]);
						}
					} else {
						if ($val[templateName] == $templateName) {
							$template[$val[siteProductCode]] = array('templateSetIdx' => $val[templateSetIdx], 'templateIdx' => $val[templateIdx]);
						}
					}
				}
			}

		}

		return array_notnull(explode("||", $tempURL));
	}

	//사용자 상품템플릿 조회.
	function GetTemplateData($url, $templateSetIdx, $templateIdx) {
		global $cfg, $editor;
		$url = base64_decode($url);
		//debug($url);

		//주문처리 또는 장바구니 처리시 URL에 포함된 spid가 복수일 경우 누락된 정보가 있음. 어디서 누락된지 모름.
		//복수 편집기 실행 관련 2016.03.16 kdk
		//spring , pod_group 스킨 우선 적용.
		if ($cfg[skin] == "spring" || $cfg[skin] == "pod_group" || $cfg[skin] == "classic") {
			$podsno = "";
			foreach ($editor as $key => $val) {
				if ($val[podsno] && $val[podsno] != "") {
					$podsno .= $val[podsno] . ",";
				}
			}
			$spid = $podsno;
		}

		$mUrl = "";
		$exp = explode('&', $url);
		//항목 분리
		//debug($exp);
		if (count($exp) > 0) {
			foreach ($exp as $key => $val) {
				$pos = strrpos($val, "spid=");
				if ($pos === false) {
					$mUrl .= $val . "&";
				} else {
					$mUrl .= "spid=$spid&";
				}
			}
			$mUrl = substr($mUrl, 0, -1);
		}
		if ($url != $mUrl)
			$url = $mUrl;

		if ($cfg[podsiteid]) {//몰에 podsiteid가 설정된 경우
			$mUrl = "";
			$exp = explode('&', $url);
			//항목 분리
			//debug($exp);
			if (count($exp) > 0) {
				foreach ($exp as $key => $val) {
					$pos = strrpos($val, "p_siteid=");
					if ($pos === false) {
						$mUrl .= $val . "&";
					} else {
						$mUrl .= "p_siteid=$cfg_center[podsiteid]&";
					}
				}
				$mUrl = substr($mUrl, 0, -1);
			}
			if ($url != $mUrl)
				$url = $mUrl;
		}

		$data[templateSetIdx] = $templateSetIdx;
		$data[templateIdx] = $templateIdx;
		//$soapurl = "http://" .PODS20_DOMAIN. "/WebService/tempset_info.aspx?templateSetIdx=".$templateSetIdx;
		//debug($url);
		$soapurl = $url;
		$ret = readUrlWithcurl($soapurl, false);
		$obj = json_decode($ret, TRUE);
		//debug($obj);
		if ($obj[data]) {
			foreach ($obj[data] as $item) {
				//debug($item);
				foreach ($item as $key => $val) {
					//debug($val);
					if ($val[templateData]) {
						if ($val[templateSetIdx] == $templateSetIdx && $val[templateData][0][templateIdx] == $templateIdx) {
							if ($val[templateData][0][templateURL])
								$tempURL .= $val[templateData][0][templateURL] . "||";
							$templateName = $val[templateData][0][templateName];
						}
					} else {
						if ($val[templateSetIdx] == $templateSetIdx && $val[templateIdx] == $templateIdx) {
							if ($val[templateURL])
								$tempURL .= $val[templateURL] . "||";
							$templateName = $val[templateName];
						}
					}
				}
			}

			$template = array();
			foreach ($obj[data] as $item) {
				//debug($item);
				foreach ($item as $key => $val) {
					//debug($val);
					if ($val[templateData]) {
						if ($val[templateData][0][templateName] == $templateName) {
							$templateName = $val[templateData][0][templateName];
							$template[$val[siteProductCode]] = array('templateSetIdx' => $val[templateSetIdx], 'templateIdx' => $val[templateData][0][templateIdx]);
						}
					} else {
						if ($val[templateName] == $templateName) {
							$template[$val[siteProductCode]] = array('templateSetIdx' => $val[templateSetIdx], 'templateIdx' => $val[templateIdx]);
						}
					}
				}
			}

		}

		return $template;
	}

	function checkInsertExtraOptionIndex($option_insert_index_arr, $checkIndex) {
		$result = true;
		foreach ($option_insert_index_arr as $key => $value) {
			if ($value == $checkIndex)
				$result = false;
		}	
		
		return $result;
	}

	//신규 가격 정보 테이블 만들기. 옵션컬럼, 가격컬럼으로 분리.      20150313
	function InsertExtraOptionPriceTable($PreSetCode, $optionGroupType = "F-FIX") {
		global $r_est_preset_sub_option_group;
		$bFuncInsideHasChild = false;
		//가격 설정시 자식 옵션이 한개라도 있는경우 처리.
		//debug($this->OptionData);
		//exit;
		$FIXOPTION_Data = $r_est_preset_sub_option_group[$PreSetCode][$optionGroupType];
		//$FIXOPTION_Data = array("19" => "1,24,29", "20"=>"2,25,30", "21"=>"3,26,31", "22"=>"4,27,32", "23"=>"5,28,33");
		//$FIXOPTION_Data = array("2" => "1,7,12", "3"=>"2,8,13", "4"=>"3,9,14", "5"=>"4,10,15", "6"=>"5,11,16");
		//debug($FIXOPTION_Data);
		//exit;

		//용지 1,2,3차 중에 사용하는 것만 처리.
		if (is_array($FIXOPTION_Data)) {
			foreach ($FIXOPTION_Data as $optionKey => $optionValue) {
				$option_arr = explode(",", $optionValue);
				$fpageIndex = $option_arr[0];
				for ($i = 1; $i < count($option_arr); $i++) {

					if ($this -> OptionUseData[$option_arr[$i]] == 'Y') { //용지 1,2,3차 중에 사용하는 것만 처리.
						$option_use_data .= $option_arr[$i].",";
					}
				}
			}

			if (substr($option_use_data, -1) == ",") $option_use_data = substr($option_use_data , 0, -1);
			$FIXOPTION_Data[$optionKey] = $fpageIndex.",".$option_use_data;
		}		
		//debug($FIXOPTION_Data);
		//exit;
		
		$option_all_arr = array();
		//모든 옵션을 extra_tbl_kind_index 별 모두 저장한다.
		$option_insert_index_arr = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => "", 10 => "");
		//insert 해야함 옵션 종류별 $option_kind_arr 의 index
		$option_kind_use_flag = array();
		//옵션 분류 사용여부 (모든 옵션을 $option_insert_index_arr 으로 구성했는지 체크)
		foreach ($this->OptionData as $dataKey => $data) {
			if ($this -> OptionUseData[$data[option_kind_index]] == 'Y') {
				if ($data[same_price_item_name] != "") {
					//책자상품에 규격이면...
					if ($this -> GoodsKind == "BOOK" && $this -> OptionGroupType != "AFTEROPTION" && $data[option_group_type] == "DOCUMENT") {
						$data[option_group_type] = $this -> OptionGroupType;
					}

					$this -> SamePriceItemArr[$extra_tbl_kind_index][] = $data;
				} else {
					//$option_all_arr[$data[extra_tbl_kind_index]][] = $data[item_name];
					$option_all_arr[$data[option_kind_index]][] = $data;

					$option_kind_use_flag[$data[option_kind_index]] = 'N';
					//옵션 분류 사용여부
					//$option_kind_arr[$data[option_kind_index]][$data[item_name]] = $data[option_kind_index];
				}
			}
		}
		//debug($option_all_arr);
		//exit;

		$option_kind_index = "";
		$extra_tbl_kind_index = "";
		$option_insert_index_arr_index = 1;

		foreach ($option_all_arr as $key => $value) {
			foreach ($value as $skey => $svalue) {
				$bSkip = false;
				$bHasChild = false;
				//서브 옵션을을 넣기 위한 작업, sub 옵션들은 그냥 skip
				if (is_array($FIXOPTION_Data)) {
					foreach ($FIXOPTION_Data as $optionKey => $optionValue) {
						$option_arr = explode(",", $optionValue);
						if ($svalue[option_kind_index] == $optionKey)
							$bHasChild = true;

						for ($i = 1; $i < count($option_arr); $i++) {
							//sub 옵션들은 그냥 skip
							if ($svalue[option_kind_index] == $option_arr[$i]) {
								//debug($option_arr[$i]);
								$bSkip = true;
								break;
							}
						}
					}
				}
				//debug($option_insert_index_arr);
				//서브 옵션들을 제외한 부모 옵션들만 추가.
				if ($bSkip == false) {

					if ($bHasChild) {
						$option_insert_index_arr[$option_insert_index_arr_index - 1][] = $svalue[option_kind_index];
						$option_insert_index_arr_index++;
						$bFuncInsideHasChild = true;
					}

					if ($svalue[extra_tbl_kind_index] != $extra_tbl_kind_index) {
						if (!$bHasChild) {
							//존재하는 모든 배열에 추가해 준다.
							foreach ($option_insert_index_arr as $sub_key => $sub_value) {
								//if ($this->checkInsertExtraOptionIndex($option_insert_index_arr[$sub_key], $svalue[option_kind_index]))
								$option_insert_index_arr[$sub_key][] = $svalue[option_kind_index];
							}
						}

					}
				}
				//debug($option_insert_index_arr);
				//exit;

				//서브 옵션을을 넣기 위한 작업
				if (is_array($FIXOPTION_Data)) {
					foreach ($FIXOPTION_Data as $optionKey => $optionValue) {
						//서브 옵션들을 모두 추가하자.
						//debug($svalue[option_kind_index]);
						if ($svalue[option_kind_index] == $optionKey) {
							$option_arr = explode(",", $optionValue);
							//debug($option_arr);
							//debug($option_insert_index_arr);
							for ($i = 1; $i < count($option_arr); $i++) {
								if(!$option_arr[$i]) continue;
								foreach ($option_insert_index_arr as $sub_key => $sub_value) {
									if ($sub_value) {
										foreach ($sub_value as $sub_skey => $sub_svalue) {
											if ($sub_svalue == $optionKey) {
												if ($this -> checkInsertExtraOptionIndex($option_insert_index_arr[$sub_key], $option_arr[$i]))
													$option_insert_index_arr[$sub_key][] = $option_arr[$i];
											}
										}
									}
								}
							}
						}
					}
				}

				$option_kind_index = $svalue[option_kind_index];
				$extra_tbl_kind_index = $svalue[extra_tbl_kind_index];
			}
		}

		if ($bFuncInsideHasChild)
			$option_insert_index_arr_index--;

		if ($this -> Preset == "100112") {
			//기본 책자 프리셋과 달리 첫번째 용지가 5개로 분리되어 있지 않음.
			$option_insert_index_arr_index = "1";
		}
		//debug($option_insert_index_arr);
		for ($i = 0; $i < $option_insert_index_arr_index; $i++) {
			$option_indexs_arr[] = $option_insert_index_arr[$i];
		}

		//debug($option_indexs_arr);
		//debug($option_all_arr);
		//debug($option_indexs_arr);
		//exit;
		$insert_option_item = $this -> insertExtraOptionPriceTableASItem($option_all_arr, $option_indexs_arr);
		//debug($insert_option_item);
		//exit;
		return $insert_option_item;

		//$this->m_option->InsertExtraOptionInfoNPrice($this->cid, $this->center_id, $this->GoodsNo, $this->goods_kind,);
	}

	//조건에 option_kind_code 추가됨.
	function insertExtraOptionPriceTableASItem($option_all_arr, $option_indexs_arr) {
		$insert_option_item = array();
		$itemIndex = 0;
		$option_index = 0;

		foreach ($option_indexs_arr as $key => $value) {

			$max_kind_index = count($value);
			$option_index = 0;
//debug($max_kind_index);
			//옵션이 한개인경우 처리
			if ($max_kind_index == 1) {
				foreach ($option_all_arr[$value[$option_index]] as $itemkey => $itemvalue) {
					$insert_option_item[] = array("option_item" => $itemvalue[item_name], "option_price" => "0");
				}

			} else {
				if ($max_kind_index > $option_index) {
					//debug($option_all_arr[$value[$option_index]]);
					foreach ($option_all_arr[$value[$option_index]] as $itemkey => $itemvalue) {
						//$insert_option_item[$itemIndex] .= $itemvalue[item_name] . "|";
						$insert_option_item_str = $itemvalue[item_name] . "|";
						$insert_option_code_str = $itemvalue[code] . "|";
						$option_index_sub = $option_index + 1;
						$this -> insertExtraOptionPriceTableASItemSubFunc($option_all_arr, $value, $insert_option_item, $insert_option_item_str, $option_index_sub, $itemIndex, $insert_option_code_str);
					}
				}
			}

			//debug($insert_option_item_str);
			//exit;

		}

		return $insert_option_item;
	}

	//조건에 option_kind_code 추가됨.
	function insertExtraOptionPriceTableASItemSubFunc($option_all_arr, $option_indexs_arr_value, &$insert_option_item, $insert_option_item_str, $option_index, &$itemIndex, $insert_option_code_str) {
		$max_kind_index = count($option_indexs_arr_value);

		//debug("max_kind_index : ".$max_kind_index);
		//debug("option_index : ".$option_index);
		//debug($insert_option_item);
		//debug("insert_option_item_str : ".$insert_option_item_str);
		//debug("insert_option_code_str : ".$insert_option_code_str);

		$last_item_name_arr = explode("|", $insert_option_item_str);
		//부모 옵션 확인
		foreach ($last_item_name_arr as $key => $val) {
			if ($val) {
				$last_item_name = $val;
			}
		}
		//debug("last_item_name : -".$last_item_name."-");

		$last_code_arr = explode("|", $insert_option_code_str);
		//부모 옵션 코드 확인
		foreach ($last_code_arr as $key => $val) {
			if ($val) {
				$last_code = $val;
			}
		}
		//debug("last_code : -".$last_code."-");

		if ($max_kind_index > $option_index) {
			foreach ($option_all_arr[$option_indexs_arr_value[$option_index]] as $itemkey => $itemvalue) {
				//$insert_option_item[$itemIndex] .= $itemvalue[item_name] . "|";
				//$insert_option_item_str .= $itemvalue[item_name] . "|";
				//debug($itemvalue);

				if (($max_kind_index - 1) == $option_index) {

					if ($itemvalue[parent_item_name]) {
						if ($last_item_name == trim($itemvalue[parent_item_name]) && $last_code == trim($itemvalue[code])) {//부모 옵션 확인
							//debug("parent_item_name2 : -".trim($itemvalue[parent_item_name])."-");
							$insert_option_item[$itemIndex] = array("option_item" => $insert_option_item_str . trim($itemvalue[item_name]), "option_price" => "0");
							$itemIndex = $itemIndex + 1;
							//debug($insert_option_item);
							//exit;
						}
					} else {
						$insert_option_item[$itemIndex] = array("option_item" => $insert_option_item_str . $itemvalue[item_name], "option_price" => "0");
						//$insert_option_item[$itemIndex] = $insert_option_item_str;
						$itemIndex = $itemIndex + 1;
						//break;
						//debug($insert_option_item);
						//exit;
					}
				} else {
					//debug($insert_option_item_str_sub);
					//exit;

					if ($itemvalue[parent_item_name]) {
						if ($last_item_name == trim($itemvalue[parent_item_name]) && $last_code == trim($itemvalue[code])) {//부모 옵션 확인
							//$insert_option_item_str .= $itemvalue[item_name] . "|";
							$insert_option_item_str_sub = $insert_option_item_str . trim($itemvalue[item_name]) . "|";
							$insert_option_code_str_sub = $insert_option_code_str . trim($itemvalue[code]) . "|";
							$option_index_sub = $option_index + 1;
							$this -> insertExtraOptionPriceTableASItemSubFunc($option_all_arr, $option_indexs_arr_value, $insert_option_item, $insert_option_item_str_sub, $option_index_sub, $itemIndex, $insert_option_code_str_sub);
						}
					} else {
						//$insert_option_item_str .= $itemvalue[item_name] . "|";
						$insert_option_item_str_sub = $insert_option_item_str . trim($itemvalue[item_name]) . "|";
						$insert_option_code_str_sub = $insert_option_code_str . trim($itemvalue[code]) . "|";
						$option_index_sub = $option_index + 1;
						$this -> insertExtraOptionPriceTableASItemSubFunc($option_all_arr, $option_indexs_arr_value, $insert_option_item, $insert_option_item_str_sub, $option_index_sub, $itemIndex, $insert_option_code_str_sub);
					}

					/*
					 //$insert_option_item_str .= $itemvalue[item_name] . "|";
					 $insert_option_item_str_sub = $insert_option_item_str . trim($itemvalue[item_name]) . "|";
					 $option_index_sub = $option_index + 1;
					 $this->InsertExtraOptionPriceTableASItemSubFunc($option_all_arr, $option_indexs_arr_value, $insert_option_item, $insert_option_item_str_sub, $option_index_sub, $itemIndex);
					 */
				}
			}
		}
	}

	//신규 가격 정보 테이블에 규격정보를 포함하여 다시 만들기.(후가공 옵션만 해당) 2015.04.07 by kdk
	//관리자 "/goods/extra_option_price_~.php" 에서 사용.
	function InsertExtraOptionPriceTableWithDocument($goodsno, $priceTable) {
		$priceItemArr = array();

		$res = $this -> m_option -> getAdminOptionList($this -> cid, $this -> center_id, $goodsno, "", "", "DOCUMENT");
		//debug($res);

		$selectIndex = 0;
		foreach ($res as $key => $data) {

			foreach ($priceTable as $key1 => $value) {
				$priceItemArr[$selectIndex] = array('option_item' => $data[item_name] . '|' . $value[option_item], 'option_price' => $value[option_price]);

				$selectIndex++;
			}

		}

		//debug($priceItemArr);
		return $priceItemArr;
	}

	//신규 가격 정보 테이블에 표지규격정보를 포함하여 다시 만들기.(스튜디오견적 업로더용 옵션만 해당) 2015.08.04 by kdk
	//관리자 "/goods/extra_option_price_~.php" 에서 사용.
	function InsertExtraOptionPriceTableWithCoverDocument($goodsno, $priceTable) {
		$priceItemArr = array();

		$res = $this -> m_option -> getAdminOptionList($this -> cid, $this -> center_id, $goodsno, "", "C-FIXOPTION", "DOCUMENT");
		//debug($res);

		$selectIndex = 0;
		foreach ($res as $key => $data) {

			foreach ($priceTable as $key1 => $value) {
				$priceItemArr[$selectIndex] = array('option_item' => $value[option_item] . '|' . $data[item_name], 'option_price' => $value[option_price]);

				$selectIndex++;
			}

		}

		//debug($priceItemArr);
		return $priceItemArr;
	}

	//주문 부수 (수량) select 만들기 #동일
	function MakeOrderCntSelect($goodsno, $kind_code = "OCNT", $tagName = "order_cnt_select", $unitCntFlag = "", $changeActionFuncName = "orderCntChange") {
		$selectTag = array();

		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

		if ($unitCntFlag == "false")
			$order_cnt[unit_cnt_rule] = "";

		if ($order_cnt) {
			if ($kind_code == "PCNT") {
				$this -> PrintPageCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintPageCntRuleArr;
			} else {
				$this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintCntRuleArr;
			}

			$selectTag[display_name] = $order_cnt[display_name];
			$selectTag[display_tag] = "<select class=\"selectType\" id=\"$tagName\" name=\"$tagName\" code=\"$kind_code\" option_kind_code=\"$kind_code\" onchange=\"$changeActionFuncName(this)\" >";
			foreach ($OptionPrintCntRuleArr as $key => $value) {
				if ($value != "") {
					$order_sub_arr = explode("~", $value);

					if (sizeof($order_sub_arr) > 1) {
						if ($order_sub_arr[0] == "0")
							$order_sub_arr[0] += $order_sub_arr[2];
						for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
						{
							//고객이 옵션으로 선택하게 되는 수량의 한계치 처리 이하 2015.06.04 by kdk
							if ($order_cnt[display_cnt_rule] && $order_cnt[display_cnt_rule] < $i)
								break;

							$selectTag[display_tag] .= "<option value='$i'>$i</option>";

							$selectTag[option_item][$i] = $i;
						}
					} else {
						//고객이 옵션으로 선택하게 되는 수량의 한계치 처리 이하 2015.06.04 by kdk
						if ($order_cnt[display_cnt_rule] && $order_cnt[display_cnt_rule] < $value)
							break;

						$selectTag[display_tag] .= "<option value='$value'>$value</option>";

						$selectTag[option_item][$value] = $value;
					}
				}
			}
			$selectTag[display_tag] .= "</select>";

			if ($this -> Preset == "100102" || $this -> Preset == "100106" || $this -> Preset == "100110") {
				//입력 표시 2015.06.09 by kdk
				$selectTag[display_tag] .= "<span><input class=\"textType\" type=\"text\" id=\"input_$tagName\" name=\"input_$tagName\" /></span>";

				//단위 표시 2015.06.04 by kdk
				$selectTag[display_tag] .= " <span name=\"OCNT\">" . $this -> getUserCntName($this -> GoodsNo, "OCNT") . "</span>";
			}

			if ($order_cnt[unit_cnt_rule]) {
				$order_arr = explode(";", $order_cnt[unit_cnt_rule]);
				$selectTag[display_tag] .= "<span name=\"x\"> x </span><select class=\"selectType\" id=\"unit_order_cnt\" name=\"unit_order_cnt\" code=\"unit_order_cnt\" option_kind_code=\"$kind_code\" >";

				foreach ($order_arr as $key => $value) {
					if ($value != "") {
						$order_sub_arr = explode("~", $value);

						if (sizeof($order_sub_arr) > 1) {
							if ($order_sub_arr[0] == "0")
								$order_sub_arr[0] += $order_sub_arr[2];
							for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
							{
								$selectTag[display_tag] .= "<option value='$i'>$i</option>";
							}
						} else {//기능 추가 2014.12.23 by kdk
							$selectTag[display_tag] .= "<option value='$value'>$value</option>";
						}
					}
				}
				$selectTag[display_tag] .= "</select>";

				if ($this -> Preset == "100102" || $this -> Preset == "100106" || $this -> Preset == "100110") {
					//입력 표시 2015.06.09 by kdk
					$selectTag[display_tag] .= "<span><input class=\"textType\" type=\"text\" id=\"input_unit_order_cnt\" name=\"input_unit_order_cnt\" /></span>";

					//단위 표시 2015.06.04 by kdk
					$selectTag[display_tag] .= " <span name=\"unit_order_cnt\">" . $this -> getUserCntName($this -> GoodsNo, "OCNT", "Y") . "</span>";
				}
			}
		}

		return $selectTag[display_tag];
	}

	function MakeOrderCntDisplayName($goodsno, $kind_code = "OCNT") {
		$selectTag = array();

		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);
		if ($order_cnt) {
			if ($kind_code == "PCNT") {
				$this -> PrintPageCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintPageCntRuleArr;
			} else {
				$this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintCntRuleArr;
			}
			$selectTag[display_name] = $order_cnt[display_name];
		}

		return $selectTag[display_name];
	}

	//주문 부수 (수량) select 만들기
	function MakeUnitCntSelect($goodsno, $kind_code = "OCNT") {
		$selectTag = array();

		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

		if ($order_cnt) {
			if ($kind_code == "PCNT") {
				$this -> PrintPageCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintPageCntRuleArr;
			} else {
				$this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintCntRuleArr;
			}

			$selectTag[display_name] = $order_cnt[display_name];
			if ($order_cnt[unit_cnt_rule]) {
				$order_arr = explode(";", $order_cnt[unit_cnt_rule]);
				$selectTag[display_tag] .= "<select class=\"selectType inp-w-80\" id=\"unit_order_cnt\" name=\"unit_order_cnt\" code=\"unit_order_cnt\" option_kind_code=\"$kind_code\" >";
				foreach ($order_arr as $key => $value) {
					if ($value != "") {
						$order_sub_arr = explode("~", $value);

						if (sizeof($order_sub_arr) > 1) {
							if ($order_sub_arr[0] == "0")
								$order_sub_arr[0] += $order_sub_arr[2];
							for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
							{
								$selectTag[display_tag] .= "<option value='$i'>$i</option>";
							}
						} else {//기능 추가 2014.12.23 by kdk
							$selectTag[display_tag] .= "<option value='$value'>$value</option>";
						}
					}
				}
				$selectTag[display_tag] .= "</select>";
			}
		}

		return $selectTag[display_tag];
	}

	//옵션 코드 생성 시작.
	function MakeSelectOptionTag($optionKindIndex, $parentItemValue = '', $changeActionFuncName = "forwardAction", $css = '') {
		$selectTag = array();
		$size_tag = array();
		//debug($this->OptionData);exit;
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $optionKindIndex && $data[parent_item_name] == $parentItemValue) {
				if ($data[display_flag] == "N") //사용안함 항목 select에 추가하지 않는다.
					continue;

				//처음 item 경우만 select tag 붙여준다
				if ($data[option_item_index] == "1") {
						
					$code = $data[option_kind_code];
					if ($data[option_group_type] == "SELOPTION" && $data[extra_tbl_kind_code] == "OP") {
						$code = $data[extra_tbl_kind_code];
					}
					
					if($this->Preset == "100112") {//책자 프리셋3 / 오류 수정 / 2017.08.18 / kdk
						$code = $data[extra_tbl_kind_code];
					}					
					
					$selectTag[] = "<select class=\"selectType $css\" name=\"item_select_$data[code]_$optionKindIndex\" id=\"item_select_$data[code]_$optionKindIndex\" code=\"$code\" option_group_type=\"$data[option_group_type]\" onchange=\"$changeActionFuncName(this)\">"; //오류수정 / 2017.05.16 / kdk

					//규격 옵션의 경우 width, height 상자 출력
					if ($data[code] == "DOCUMENT") {
						$size_tag[] = " <input class='textType inp-w-38' type='text' name='document_x' id='document_x' option_group_type=\"$data[option_group_type]\" value='$data[extra_data1]' > mm("._("가로").") x ";
						$size_tag[] = "<input class='textType inp-w-38' type='text' name='document_y' id='document_y' option_group_type=\"$data[option_group_type]\" value='$data[extra_data2]' > mm("._("세로").") ";
					}
				}

				$selectTag[] = "<option value='$data[item_name]'>$data[item_name]</option>";
			}
		}

		//다음 태그 처음 시작인경우 닫아준다.
		if (sizeof($selectTag) > 0)
			$selectTag[] = "</select>";

		$result = "";
		foreach ($selectTag as $key => $value) {
			$result .= $value;
		}

		foreach ($size_tag as $key => $value) {
			$result .= $value;
		}

		return $result;
	}

	//옵션 코드 생성 시작.(추가내지용)
	function MakeSelectOptionTagAddPage($optionKindIndex, $parentItemValue = '', $changeActionFuncName = "forwardAction", $css = '') {
		$selectTag = array();
		$size_tag = array();
		//debug($this->OptionData);exit;
		foreach ($this->OptionData as $dataKey => $data) {
			if ($data[option_kind_index] == $optionKindIndex && $data[parent_item_name] == $parentItemValue) {
				if ($data[display_flag] == "N") //사용안함 항목 select에 추가하지 않는다.
					continue;

				//처음 item 경우만 select tag 붙여준다
				if ($data[option_item_index] == "1") {
					$id_name = $data[code]."_".$optionKindIndex."_".$data[extra_tbl_kind_code];
					$selectTag[] = "<select class=\"selectType $css\" name=\"item_select_$id_name\" id=\"item_select_$id_name\" code=\"$data[extra_tbl_kind_code]\" option_group_type=\"$data[option_group_type]\" onchange=\"$changeActionFuncName(this)\">";

					//규격 옵션의 경우 width, height 상자 출력
					if ($data[code] == "DOCUMENT") {
						$size_tag[] = " <input class='textType inp-w-38' type='text' name='document_x' id='document_x' option_group_type=\"$data[option_group_type]\" value='$data[extra_data1]' > mm("._("가로").") x ";
						$size_tag[] = "<input class='textType inp-w-38' type='text' name='document_y' id='document_y' option_group_type=\"$data[option_group_type]\" value='$data[extra_data2]' > mm("._("세로").") ";
					}
				}

				$selectTag[] = "<option value='$data[item_name]'>$data[item_name]</option>";
			}
		}

		//다음 태그 처음 시작인경우 닫아준다.
		if (sizeof($selectTag) > 0)
			$selectTag[] = "</select>";

		$result = "";
		foreach ($selectTag as $key => $value) {
			$result .= $value;
		}

		foreach ($size_tag as $key => $value) {
			$result .= $value;
		}

		return $result;
	}

	//관리자 사용 /admin/goods/extra_option_preset_100102.php ~ 100108.pnp
	function MakeChildSelectOptionTag($optionKindIndex, $parentKindIndex, $changeActionFuncName = "forwardAction") {
		$selectTag = array();
		$size_tag = array();
		//debug($optionKindIndex);
		//debug($parentKindIndex);
		$parentItemValue = $this -> getFirstItemValue($parentKindIndex);
		//debug($parentItemValue);
		$kindIndex = $parentKindIndex + 1;
		//debug($kindIndex);
		foreach ($this->OptionData as $dataKey => $data) {
			//debug($data);
			if ($data[option_kind_index] == $optionKindIndex && $data[parent_item_name] == $parentItemValue) {
				//처음 item 경우만 select tag 붙여준다
				if ($data[option_item_index] == "1") {
					//$selectTag[] = "<select name=\"item_select_$data[code]_$optionKindIndex\" id=\"item_select_$data[code]_$optionKindIndex\" code=\"$data[code]\" onchange=\"$changeActionFuncName(this)\">";
					
					$selectTag[] = "<select name=\"item_select_$data[code]_$kindIndex\" id=\"item_select_$data[code]_$kindIndex\" code=\"$data[code]\" onchange=\"$changeActionFuncName(this)\">";

					//규격 옵션의 경우 width, height 상자 출력
					if ($data[code] == "DOCUMENT") {
						$size_tag[] = " <input type='text' name='document_x' id='document_x' size='5' value='$data[extra_data1]' > x ";
						$size_tag[] = "<input type='text' name='document_y' id='document_y' size='5' value='$data[extra_data2]' > (mm) ";
					}
				}

				$selectTag[] = "<option value='$data[item_name]'>$data[item_name]</option>";
			}

			//debug($selectTag);
		}

		//다음 태그 처음 시작인경우 닫아준다.
		if (sizeof($selectTag) > 0)
			$selectTag[] = "</select>";

		$result = "";
		foreach ($selectTag as $key => $value) {
			$result .= $value;
		}

		foreach ($size_tag as $key => $value) {
			$result .= $value;
		}

		//debug($result);
		return $result;
	}

	function MakePageSelectOptionTag($optionArr, $changeActionFuncName = "forwardAction") {
		//debug($this->OptionData);
		//debug($optionArr);

		$option_arr = explode("|", $optionArr);
		//debug($option_arr);

		$optionGroupType = $this -> getFirstOptionGroupTypeValue($option_arr[0]);
		$optionKindCode = $this -> GetOptionKindCode($option_arr[0]);
		$extraTblKindCode = $this -> getExtraTblKindCodeByOptionKindIndex($option_arr[0]);

		foreach ($option_arr as $key => $val) {
			//$parentItemValue[$val] = $this -> getFirstItemValue($val);
			$parentItemValue[$val] = $this -> getFirstItemValueByOptionKindCode($val, $optionKindCode);
		}

		//debug($optionGroupType);
		//debug($optionKindCode);
		//debug($extraTblKindCode);
		//debug($parentItemValue);

		//첫번째 selectbox에는 1차 항목만 담고
		if ($this -> getOptionKindUse($option_arr[0]) == "Y") {//사용안함 항목 처리
			foreach ($this->OptionData as $k => $data) {
				if ($data[option_group_type] == $optionGroupType && $data[option_kind_index] == $option_arr[0]) {
					$selectItemTag .= "<option value='$data[item_name]' option_kind_code='$data[option_kind_code]'>$data[item_name]</option>\r\n";
				}
			}
			//debug($selectItemTag);

			$id_name = $extraTblKindCode . "_1";
			$selectTag[display_tag] = "<select class=\"selectType\" name=\"item_select_$id_name\" id=\"item_select_$id_name\" code=\"$extraTblKindCode\" option_group_type=\"$optionGroupType\" onchange=\"$changeActionFuncName(this)\" index=\"1\" >\r\n";
			$selectTag[display_tag] .= $selectItemTag;
			$selectTag[display_tag] .= "</select>";
		}

		//두번째 selectbox에는 첫번째 selectbox 선택 값 기준 하위 값만 담는다.
		if ($this -> getOptionKindUse($option_arr[1]) == "Y") {//사용안함 항목 처리
			foreach ($this->OptionData as $k => $data) {
				if ($data[option_group_type] == $optionGroupType && $data[option_kind_index] == $option_arr[1] && $data[parent_item_name] == $parentItemValue[$option_arr[0]] && $data[option_kind_code] == $optionKindCode) {
					$selectItemTag2 .= "<option value='$data[item_name]' option_kind_code='$data[option_kind_code]'>$data[item_name]</option>\r\n";
				}
			}
			//debug($selectItemTag2);

			if ($parentItemValue[$option_arr[1]]) {
				$id_name = $extraTblKindCode . "_2";
				$selectTag[display_tag] .= " <select class=\"selectType\" name=\"item_select_$id_name\" id=\"item_select_$id_name\" code=\"$extraTblKindCode\" option_group_type=\"$optionGroupType\" onchange=\"$changeActionFuncName(this)\" index=\"2\" >\r\n";
				$selectTag[display_tag] .= $selectItemTag2;
				$selectTag[display_tag] .= "</select>";
			}
		}

		//세번째 selectbox에는 두번째 selectbox 선택 값 기준 하위 값만 담는다.
		if ($this -> getOptionKindUse($option_arr[2]) == "Y") {//사용안함 항목 처리
			foreach ($this->OptionData as $k => $data) {
				if ($data[option_group_type] == $optionGroupType && $data[option_kind_index] == $option_arr[2] && $data[parent_item_name] == $parentItemValue[$option_arr[1]] && $data[option_kind_code] == $optionKindCode) {
					$selectItemTag3 .= "<option value='$data[item_name]' option_kind_code='$data[option_kind_code]'>$data[item_name]</option>\r\n";
				}
			}
			//debug($selectItemTag3);
			if ($parentItemValue[$option_arr[2]]) {
				$id_name = $extraTblKindCode . "_3" . $selectIndex;
				$selectTag[display_tag] .= " <select class=\"selectType\" name=\"item_select_$id_name\" id=\"item_select_$id_name\" code=\"$extraTblKindCode\" option_group_type=\"$optionGroupType\" onchange=\"$changeActionFuncName(this)\" index=\"3\" >\r\n";
				$selectTag[display_tag] .= $selectItemTag3;
				$selectTag[display_tag] .= "</select>";
			}
		}

		//debug($selectTag[display_tag]);
		//debug(debug_time($this_time, "MakePageSelectOption"));
		return $selectTag[display_tag];

	}

	//주문 부수 (수량) select 만들기
	function MakeBookOrderCnt($goodsno, $kind_code = "OCNT", $tagName = "order_cnt_select", $changeActionFuncName = "forwardOrderCntAction") {
		if ($kind_code == "")
			$kind_code = "OCNT";
		$selectTag = array();

		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);
		if ($order_cnt) {

			if ($kind_code == "PCNT") {
				$this -> PrintPageCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintPageCntRuleArr;
			} else {
				$this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintCntRuleArr;
			}

			$selectTag[display_name] = $order_cnt[display_name];
			$selectTag[display_tag] = "<select id=\"$tagName\" name=\"$tagName\" code=\"$tagName\" onchange=\"$changeActionFuncName(this)\">";
			foreach ($OptionPrintCntRuleArr as $key => $value) {
				if ($value != "") {
					$order_sub_arr = explode("~", $value);

					if (sizeof($order_sub_arr) > 1) {
						if ($order_sub_arr[0] == "0")
							$order_sub_arr[0] += $order_sub_arr[2];
						for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
						{
							$selectTag[display_tag] .= "<option value='$i'>$i</option>";

							$selectTag[option_item][$i] = $i;
						}
					} else {
						$selectTag[display_tag] .= "<option value='$value'>$value</option>";

						$selectTag[option_item][$value] = $value;
					}
				}
			}
			$selectTag[display_tag] .= "</select>";

			if ($order_cnt[unit_cnt_rule]) {
				$order_arr = explode(";", $order_cnt[unit_cnt_rule]);
				$selectTag[display_tag] .= " x <select id=\"unit_order_cnt\" name=\"unit_order_cnt\" code=\"unit_order_cnt\" onchange=\"priceSum()\">";
				foreach ($order_arr as $key => $value) {
					if ($value != "") {
						$order_sub_arr = explode("~", $value);

						if (sizeof($order_sub_arr) > 1) {
							if ($order_sub_arr[0] == "0")
								$order_sub_arr[0] += $order_sub_arr[2];
							for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
							{
								$selectTag[display_tag] .= "<option value='$i'>$i</option>";
							}
						} else {
							$selectTag[display_tag] .= "<option value='$value'>$value</option>";
						}
					}
				}
				$selectTag[display_tag] .= "</select>";
			}

		}

		return $selectTag;
	}

	//주문 부수 (수량) select 만들기 (사용자화면)
	function MakeBookOrderCntBook($goodsno, $kind_code = "OCNT", $code = "PP", $tagName = "order_cnt_select", $changeActionFuncName = "forwardOrderCntAction", $unitCntFlag = "") {
		$selectTag = array();

		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

		if ($unitCntFlag == "false")
			$order_cnt[unit_cnt_rule] = "";

		if ($order_cnt) {
			if ($kind_code == "PCNT") {
				$this -> PrintPageCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintPageCntRuleArr;
			} else {
				$this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintCntRuleArr;
			}

			$selectTag[display_name] = $order_cnt[display_name];
			$selectTag[display_tag] = "<select id=\"$tagName\" name=\"$tagName\" code=\"$code\" onchange=\"$changeActionFuncName(this)\">";

			//표지, 면지,간지일 경우 "0" 페이지 생성 2014.12.31 by kdk
			if ($kind_code == "C-OCNT" || $kind_code == "M-OCNT" || $kind_code == "G-OCNT") {
				$selectTag[display_tag] .= "<option value='0'>" . _("선택") . "</option>";
			}

			//책자(표지) 수량의 한계치 관련 2015.02.10 by kdk
			$dCntRuleCheck = FALSE;
			if (!is_null($order_cnt[display_cnt_rule]) && $order_cnt[display_cnt_rule] !== "" && $order_cnt[display_cnt_rule] !== "undefined") {
				$dCntRule = $order_cnt[display_cnt_rule];
				$dCntRuleCheck = TRUE;
			}

			foreach ($OptionPrintCntRuleArr as $key => $value) {
				if ($value != "") {
					$order_sub_arr = explode("~", $value);

					if (sizeof($order_sub_arr) > 1) {
						if ($order_sub_arr[0] == "0")
							$order_sub_arr[0] += $order_sub_arr[2];

						for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])
						//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
						{
							if ($dCntRuleCheck) {
								if ($dCntRule >= $i) {
									$selectTag[display_tag] .= "<option value='$i'>$i</option>";
									$selectTag[option_item][$i] = $i;
								}
							} else {
								$selectTag[display_tag] .= "<option value='$i'>$i</option>";
								$selectTag[option_item][$i] = $i;
							}
						}
					} else {
						if ($dCntRuleCheck) {
							if ($dCntRule >= $value) {
								$selectTag[display_tag] .= "<option value='$value'>$value</option>";
								$selectTag[option_item][$value] = $value;
							}
						} else {
							$selectTag[display_tag] .= "<option value='$value'>$value</option>";
							$selectTag[option_item][$value] = $value;
						}
					}
				}
			}
			$selectTag[display_tag] .= "</select>";

			if ($order_cnt[unit_cnt_rule]) {
				$order_arr = explode(";", $order_cnt[unit_cnt_rule]);
				$selectTag[display_tag] .= " x <select id=\"unit_order_cnt\" name=\"unit_order_cnt\" code=\"$code\" onchange=\"priceSum()\">";
				foreach ($order_arr as $key => $value) {
					if ($value != "") {
						$order_sub_arr = explode("~", $value);

						if (sizeof($order_sub_arr) > 1) {
							if ($order_sub_arr[0] == "0")
								$order_sub_arr[0] += $order_sub_arr[2];
							for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
							{
								$selectTag[display_tag] .= "<option value='$i'>$i</option>";
							}
						} else {
							$selectTag[display_tag] .= "<option value='$value'>$value</option>";
						}
					}
				}
				$selectTag[display_tag] .= "</select>";
			}

		}

		return $selectTag;
	}

	//주문 부수 (수량) select 만들기
	function MakeBookUnitCntBook($goodsno, $kind_code = "OCNT") {
		$selectTag = array();

		$order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);
		if ($order_cnt) {
			if ($kind_code == "PCNT") {
				$this -> PrintPageCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintPageCntRuleArr;
			} else {
				$this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
				$OptionPrintCntRuleArr = $this -> PrintCntRuleArr;
			}

			$selectTag[display_name] = _("수량");
			//$order_cnt[display_name];
			if ($order_cnt[unit_cnt_rule]) {
				$order_arr = explode(";", $order_cnt[unit_cnt_rule]);
				$selectTag[display_tag] .= "<select id=\"unit_order_cnt\" name=\"unit_order_cnt\" code=\"$code\" onchange=\"priceSum()\">";
				foreach ($order_arr as $key => $value) {
					if ($value != "") {
						$order_sub_arr = explode("~", $value);

						if (sizeof($order_sub_arr) > 1) {
							if ($order_sub_arr[0] == "0")
								$order_sub_arr[0] += $order_sub_arr[2];
							for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
							{
								$selectTag[display_tag] .= "<option value='$i'>$i</option>";
							}
						} else {
							$selectTag[display_tag] .= "<option value='$value'>$value</option>";
						}
					}
				}
				$selectTag[display_tag] .= "</select>";
			}

		}

		return $selectTag;
	}

	//옵션 코드 생성 시작.
	function MakeSelectOption($includeGroupCode = "", $changeActionFuncName = "forwardAction", $optionGroupType = "") {
		if ($changeActionFuncName == "")
			$changeActionFuncName = "forwardAction";

		$selectTagArr = array();
		//debug($this->optionKindCodeArr);
		//debug($this->OptionData);
		//exit;
		foreach ($this->optionKindCodeArr as $key => $value) {
			$secondOption = 0;

			$item_price_type = "";
			//옵션 가격 계산 종류
			$option_kind_code = "";
			//옵션 분류 코드
			$select_option_name = "";
			//옵션 이름
			$display_price_flag = "N";
			//가격 입력 상자 출력 여부 (가격설정이 없는건 나오지 않도록 한다)
			$selectIndex = 0;

			$option_group_type = "";
			//옵션 그룹코드

			//특정 그룹만 표시되게 한다.    //북 에서 표지와 내지중 구분해서 출력해야 한다.
			if ($includeGroupCode) {
				if (strpos($value, $includeGroupCode) === false)
					continue;
				//if (strpos($value, "C_") === false) continue;
			}

			//옵션별 처음 한번만 구해오면 된다. (2차 옵션들은 sub_option 으로 들어간다.)
			if ($value == $option_kind_code)
				continue;
			//debug($this->OptionData);exit;

			for ($i = 0; $i < 1; $i++) {
				$selectTag = array();
				//옵션 1개의 모든 정보

				$hidden_tag = "";
				$idx = 0;
				$subOptionCheck = true;

				if (!$this -> OptionData)
					continue;

				foreach ($this->OptionData as $dataKey => $data) {
					//parent_option_item_ID 가 0, -1, -2 3개가 한 옵션으로 표현(종속 관계 없는 2차 옵션)
					if ($data[parent_option_item_ID] == $secondOption && $data[option_kind_code] == $value) {
						unset($selectTagTempArr);
						//debug($secondOption);
						//Item 항목의 첫번째에서 옵션 정보 설정..
						if ($idx == 0 || $subOptionCheck) {

							if ($idx == 0) {
								if ($data[display_flag] == "N") {
									//$hidden_tag = " style=\"display:none;\"";
									//$data[display_name] = "";
									continue;
								}
								if ($data[item_price])
									$display_price_flag = "Y";

								$item_price_type = $data[item_price_type];
								$option_kind_code = $data[option_kind_code];
								$option_group_type = $data[option_group_type];
								$master_option_kind_code = $data[master_option_kind_code];
								//책자 상품 DOCUMENT 구분하기.

								$selectTag = $this -> setOptionData($data, $selectIndex);
								//옵션 정보 설정.

								$select_option_name = $selectTag[name];

								$selectTag[display_tag] = "<select name=\"item_select_$data[option_kind_code]_$selectIndex\" id=\"item_select_$data[option_kind_code]_0\" code=\"$data[option_kind_code]\" group_code=\"$data[option_group_type]\" group_type=\"$data[master_option_kind_code]\" onchange=\"$changeActionFuncName(this)\" $hidden_tag >";
							}

							//debug($selectTag[display_tag]);
							//exit;

							$selectTagTempArr = $this -> makeSelectSubOptionNew($data[option_item_ID], ++$selectIndex, "", $optionGroupType);
							$selectTag[sub_option] = $selectTagTempArr;
							$subOptionCheck = false;

						}

						//각 아이템별 서브옵션 구조를 만든다 (추후 가격 테이블 생성시 필요)
						if (!is_array($selectTagTempArr))
							$selectTagTempArr = $this -> makeSelectSubOptionNew($data[option_item_ID], $selectIndex, "", $optionGroupType);
						//if ($data[option_item_ID] == 4)
						//debug($selectTagArr);
						//각 아이템별 서브옵션 구조를 만든다 (추후 가격 테이블 생성시 필요)
						//$selectTag[display_tag] = '';
						$selectTag['sub_option_' . $data[option_item_ID]] = $selectTagTempArr;

						//각 아이템별 서브옵션 구조를 만든다 (추후 가격 테이블 생성시 필요)  //속도 향상을 위해 위 코드로 변경    20140630  chunter
						//$selectTag['sub_option_' .$data[option_item_ID]] = $this->makeSelectSubOption($data[option_item_ID], $selectIndex, false);
						//$selectTag['sub_option_' .$data[option_item_ID]] = $selectTag[sub_option];

						$selectedText = "";
						if (is_array($this -> OrderSelectedID) && in_array($data[option_item_ID], $this -> OrderSelectedID))
							$selectedText = " selected";

						if ($data[display_flag] == "Y")//사용안함 항목 처리 2014.10.08 by kdk
							$selectTag[display_tag] .= "<option value='$data[option_item_ID]' $selectedText>$data[item_name]</option>";

						//같은 가격 설정 옵션의 경우는 항목으로 넣지 않는다.
						if ($data[same_price_option_item_ID])
							$selectTag[same_price_option_item][$data[same_price_option_item_ID]][$data[option_item_ID]] = $data[item_name];
						else {
							if ($data[display_flag] == 'Y')
								$selectTag[option_item][$data[option_item_ID]] = $data[item_name];
						}
						$idx++;
					}
				}

				//옵션들의 배열을 만든다.
				$selectTag[display_tag] .= "</select>";

				//책자 상품 규격 옵션의 경우 width, height 상자 출력
				if ($this -> GoodsKind == "BOOK" && $master_option_kind_code == "DOCUMENT") {
					$selectTag[display_tag] .= " <input type='text' name='document_x' id='document_x' size='5' value='$selectTag[extra_data1]' group_code='$option_group_type' > x ";
					$selectTag[display_tag] .= "<input type='text' name='document_y' id='document_y' size='5' value='$selectTag[extra_data2]' group_code='$option_group_type' > (mm) ";
				}
				//규격 옵션의 경우 width, height 상자 출력
				else if ($option_group_type == "DOCUMENT") {
					$selectTag[display_tag] .= " <input type='text' name='document_x' id='document_x' size='5' value='$selectTag[extra_data1]' > x ";
					$selectTag[display_tag] .= "<input type='text' name='document_y' id='document_y' size='5' value='$selectTag[extra_data2]' > (mm) ";
				}

				if ($selectTag[sub_option] && $selectTag[item_price_type] && $selectTag[item_price]) {
					$display_price_flag = "Y";
				}

				$secondOption--;

				//옵션 한개를 통째로 넣는다.
				if ($selectTag[name])
					$selectTagArr[$selectTag[name]] = $selectTag;
			}

			if ($item_price_type == "SIZE") {
				$inputTag = array();
				$inputTag[display_printx] = _("가로") . "<input type='text' name='print_x_$option_kind_code' id='print_x_$option_kind_code' code=\"$option_kind_code\" onchange=\"forwardAction(this)\" size='5'>mm ";
				$inputTag[display_printy] = _("세로") . "<input type='text' name='print_y_$option_kind_code' id='print_y_$option_kind_code' code=\"$option_kind_code\" onchange=\"forwardAction(this)\" size='5'>mm ";

				$selectTagArr[$select_option_name] = array_merge($selectTagArr[$select_option_name], $inputTag);
			}

			if ($display_price_flag == "Y") {
				//$price_input_tag = "<input type='hidden' name='option_supply_price_$value' id='option_supply_price_$value'>";
				//$price_input_tag .= "<input type='hidden' name='option_price_$value' id='option_price_$value'>";
				//$price_input_tag .= "<div id='price_text_$value' size='6'>0원</div>";

				$inputTag = array("price_tag" => $this -> makePriceInputTag($value));
				$selectTagArr[$select_option_name] = array_merge($selectTagArr[$select_option_name], $inputTag);

				$display_price_flag = "N";
				//출력후 다음 옵션은 출력 안하는걸로 초기화.
			}

		}
		//debug($selectTagArr);
		return $selectTagArr;
	}

	function makePriceInputTag($option_code) {
		$price_input_tag = "<input type='hidden' name='option_supply_price_$option_code' id='option_supply_price_$option_code'>";
		$price_input_tag .= "<input type='hidden' name='option_price_$option_code' id='option_price_$option_code'>";
		$price_input_tag .= "<div id='price_text_$option_code' size='6'>0원</div>";

		return $price_input_tag;
	}

	//지류 1,2,3차 용 (프리셋  100102,100104,100106,100108,100110)
	function MakePageSelectOption($preset, $mode, $changeActionFuncName = "forwardAction") {
		global $r_est_preset_sub_option_group;
		$r_arr = $r_est_preset_sub_option_group[$preset][$mode];
	
		$this_time = get_time();
	
		//debug($preset);
		//debug($mode);
		//debug($r_est_preset_sub_option_group);
		
		$code = "";
		$group = "";
		$selectIndex = 1;
		$subIndex = "";	
	
		switch ($mode) {
		    case 'FIX':
				$code = "PP";
				$group = "FIXOPTION";
				break;
		    case 'C-FIX':
				$code = "C-PP";
				$group = "C-FIXOPTION";
				break;
		    case 'F-FIX':
				$code = "PP";
				$group = "FIXOPTION";
				break;
		    case 'M-FIX':
				$code = "M-PP";
				$group = "M-FIXOPTION";
				break;
		    case 'G-FIX':
				$code = "G-PP";
				$group = "G-FIXOPTION";
				break;
			default :
				$code = "PP";
				$group = "FIXOPTION";
				break;
		}
		
		//debug($code);
		//debug($group);
	
		//첫번째 selectbox에는 1차 항목 1~5번까지 모두 담고
		foreach ($r_arr as $key => $val) {
			if($this->GetOptionKindUse($key) == "Y") { //사용안함 항목 처리
				if($subIndex == "") $subIndex = $val;
			
				foreach ($this->OptionData as $k => $data) {
					if ($data[option_group_type] == $group && $data[option_kind_index] == $key) {
						$selectItemTag .= "<option value='$data[item_name]' option_kind_code='$data[option_kind_code]'>$data[item_name]</option>\r\n";
					}
				}
			}
		}
	
		$id_name = $code."_".$selectIndex;
	    $selectTag[display_tag] = "<select class=\"selectType\" name=\"item_select_$id_name\" id=\"item_select_$id_name\" code=\"$code\" option_group_type=\"$group\" onchange=\"$changeActionFuncName(this)\" index=\"$selectIndex\" >\r\n";
		$selectTag[display_tag] .= $selectItemTag;
		$selectTag[display_tag] .= "</select>";
	
		//두번째,세번째 selectbox에는 첫번째 selectbox 선택 값 기준 하위 값만 담는다.
		if($subIndex) {
			$sub_arr = explode(',',$subIndex); //항목 분리
	
			if(count($sub_arr) > 0) {
				for ($i=1; $i < count($sub_arr); $i++) {
					$selectIndex++;

					$id_name = $code."_".$selectIndex;
	    			$selectTag[display_tag] .= " <select class=\"selectType\" name=\"item_select_$id_name\" id=\"item_select_$id_name\" code=\"$code\" option_group_type=\"$group\" onchange=\"$changeActionFuncName(this)\" index=\"$selectIndex\" >\r\n";
					
					foreach ($this->OptionData as $key => $data) {
	    				if ($data[option_group_type] == $group && $data[option_kind_index] == $sub_arr[$i]) {
							$selectTag[display_tag] .= "<option value='$data[item_name]' option_kind_code='$data[option_kind_code]'>$data[item_name]</option>\r\n";
						}
					}
					
					$selectTag[display_tag] .= "</select>";
				}			
			}		
		}
	
		//debug($selectTag[display_tag]);
		//debug(debug_time($this_time, "MakePageSelectOption"));
		return $selectTag[display_tag];
	}
	
	//규격 옵션(100102)
	function MakeSelectOptionDocument($code, $optionGroupType, $optionKindIndex, $changeActionFuncName = "") {
		$this_time = get_time();
		
		//debug($code);
		//debug($optionGroupType);
		//debug($optionKindIndex);
	
		if($changeActionFuncName) $changeActionFuncName = $changeActionFuncName."(this)";
		
		$id_name = "item_select_";
		$code."_".$optionKindIndex;
		$selectTag[display_tag] .= "<select class=\"selectType inp-w-80\" name=\"item_select_\" id=\"item_select_\" code=\"$code\" option_group_type=\"$optionGroupType\" onchange=\"$changeActionFuncName\" >\r\n";
		
		foreach ($this->OptionData as $key => $data) {
			if ($data[option_kind_index] == $optionKindIndex) {
				
	            //규격 옵션의 경우 width, height 상자 출력
	            if ($data[code] == "DOCUMENT")
	            {          
	              $size_tag[] = " <input class='textType inp-w-38' type='text' name='document_x' id='document_x' code=\"$code\" option_group_type=\"$data[code]\" value='$data[extra_data1]' > mm("._("가로").") x ";
	              $size_tag[] = "<input class='textType inp-w-38' type='text' name='document_y' id='document_y' code=\"$code\" option_group_type=\"$data[code]\" value='$data[extra_data2]' > mm("._("세로").") ";
	            }			
				
				$selectTag[display_tag] .= "<option value='$data[item_name]'>$data[item_name]</option>\r\n";
				$data_code = $data[code]; 
			}
		}
		
		//debug($data_code);
		$id_name = "item_select_". $data_code . "_". $optionKindIndex;
		$selectTag[display_tag] = str_replace("item_select_", $id_name, $selectTag[display_tag]);
		$selectTag[display_tag] .= "</select>";
	
		if(count($size_tag > 1)) $selectTag[display_tag] .=  $size_tag[0] . $size_tag[1] ;
		
	//debug(debug_time($this_time, "MakeSelectOption"));
		return $selectTag[display_tag];
	}


    //주문 부수 (수량) select 만들기 #동일 #알래스카 #100114
    function MakeOrderCntOnlySelect($goodsno, $kind_code = "OCNT", $tagName = "order_cnt_select", $unitCntFlag = "", $changeActionFuncName = "orderCntChange") {
        $selectTag = array();

        $order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

        if ($order_cnt) {
            if ($kind_code == "PCNT") {
                $this -> PrintPageCntRuleArr = explode(";", $order_cnt[cnt_rule]);
                $OptionPrintCntRuleArr = $this -> PrintPageCntRuleArr;
            } else {
                $this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
                $OptionPrintCntRuleArr = $this -> PrintCntRuleArr;
            }

            $selectTag[display_name] = $order_cnt[display_name];
            $selectTag[display_tag] = "<select class=\"selectType\" id=\"$tagName\" name=\"$tagName\" code=\"$kind_code\" option_kind_code=\"$kind_code\" onchange=\"$changeActionFuncName(this)\" >";
            foreach ($OptionPrintCntRuleArr as $key => $value) {
                if ($value != "") {
                    $order_sub_arr = explode("~", $value);

                    if (sizeof($order_sub_arr) > 1) {
                        if ($order_sub_arr[0] == "0")
                            $order_sub_arr[0] += $order_sub_arr[2];
                        for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
                        {
                            //고객이 옵션으로 선택하게 되는 수량의 한계치 처리 이하 2015.06.04 by kdk
                            if ($order_cnt[display_cnt_rule] && $order_cnt[display_cnt_rule] < $i)
                                break;

                            $selectTag[display_tag] .= "<option value='$i'>$i</option>";

                            $selectTag[option_item][$i] = $i;
                        }
                    } else {
                        //고객이 옵션으로 선택하게 되는 수량의 한계치 처리 이하 2015.06.04 by kdk
                        if ($order_cnt[display_cnt_rule] && $order_cnt[display_cnt_rule] < $value)
                            break;

                        $selectTag[display_tag] .= "<option value='$value'>$value</option>";

                        $selectTag[option_item][$value] = $value;
                    }
                }
            }
            $selectTag[display_tag] .= "</select>";
        }

        return $selectTag[display_tag];
    }


    //주문 부수 (수량) select 만들기 #동일 #알래스카 #100114
    function MakeUnitOrderCntOnlySelect($goodsno, $kind_code = "OCNT", $tagName = "order_cnt_select", $unitCntFlag = "", $changeActionFuncName = "orderCntChange") {
        $selectTag = array();

        $order_cnt = $this -> m_option -> getOrderCntList($this -> cid, $this -> center_id, $goodsno, $kind_code);

        if ($unitCntFlag == "false")
            $order_cnt[unit_cnt_rule] = "";

        if ($order_cnt) {
            if ($kind_code == "PCNT") {
                $this -> PrintPageCntRuleArr = explode(";", $order_cnt[cnt_rule]);
                $OptionPrintCntRuleArr = $this -> PrintPageCntRuleArr;
            } else {
                $this -> PrintCntRuleArr = explode(";", $order_cnt[cnt_rule]);
                $OptionPrintCntRuleArr = $this -> PrintCntRuleArr;
            }

            if ($order_cnt[unit_cnt_rule]) {
                $order_arr = explode(";", $order_cnt[unit_cnt_rule]);
                $selectTag[display_tag] .= "<select class=\"selectType\" id=\"unit_order_cnt\" name=\"unit_order_cnt\" code=\"unit_order_cnt\" option_kind_code=\"$kind_code\" >";

                foreach ($order_arr as $key => $value) {
                    if ($value != "") {
                        $order_sub_arr = explode("~", $value);

                        if (sizeof($order_sub_arr) > 1) {
                            if ($order_sub_arr[0] == "0")
                                $order_sub_arr[0] += $order_sub_arr[2];
                            for ($i = $order_sub_arr[0]; $i <= $order_sub_arr[1]; $i += $order_sub_arr[2])//"$i < $order_sub_arr[1];"를 "$i <= $order_sub_arr[1];"로 수정 2014.07.08 by kdk
                            {
                                $selectTag[display_tag] .= "<option value='$i'>$i</option>";
                            }
                        } else {//기능 추가 2014.12.23 by kdk
                            $selectTag[display_tag] .= "<option value='$value'>$value</option>";
                        }
                    }
                }
                $selectTag[display_tag] .= "</select>";
            }
        }

        return $selectTag[display_tag];
    }

}
?>