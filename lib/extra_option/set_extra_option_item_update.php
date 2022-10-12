<?
include "../library.php";
include "../lib_const.php";
//chkMember();

$adminExtraOption = new M_extra_option();
//$db->query("set names utf8");

$return_data = "";

$mode = $_POST[mode];
$goodsno = $_POST[goodsno];
$use_flag = $_POST[flag];
$option_kind_index = $_POST[option_kind_index];
$item_price_type = $_POST[type];

//$option_kind_index = $_GET[option_kind_index];
//$option_kind_code = $_GET[option_kind_code];
//$option_group_type = $_GET[option_group_type];

switch ($mode) {

//프린트그룹 P관리자에서 카테고리에 설정된 상품에 옵션 항목 사용여부를 일괄 수정한다. / 16.09.05 / kdk
  case 'use_flag_batch_p':
	
	$target_goodsno = $_POST[target_goodsno];
	
	$option_kind_code = $_POST[option_kind_code];
	$option_group_type = $_POST[option_group_type];

    $item_name = $_POST[item_name];
    $display_flag = $_POST[display_flag];

    if(!$option_kind_index) {
	  echo "FAIL|"._("오류입니다.")."[param=option_kind_index]";
      exit;
    }  

    if(!$item_name) {
	  echo "FAIL|"._("오류입니다.")."[param=item_name]";
      exit;
    }

    if(!$display_flag) {
      echo "FAIL|"._("오류입니다.")."[param=display_flag]";
      exit;
    }  

    if(!$target_goodsno) {
      echo "FAIL|"._("오류입니다.")."[param=target_goodsno]";
      exit;
    }  
	
//debug($_POST[target_goodsno]);

    $db->start_transaction();
	try {

    	$item = explode(',',$target_goodsno); //항목 분리
    	//debug($item);
	    foreach($item as $goodsno) {
	    	//debug($goodsno);
      		if($goodsno) {

			    //마스터 옵션 item 출력 여부 업데이트    
			    $adminExtraOption->setUpdateAdminItemDisplayFlag($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $option_group_type, $item_name, $display_flag, $option_kind_code);
				
				//마스터 옵션 하위 item 출력 여부 업데이트
				$adminExtraOption->setUpdateAdminChildItemDisplayFlag($cid, $cfg_center[center_cid], $goodsno, $item_name, $display_flag, $option_kind_code);

				//option_item_index 재정렬 / 16.09.06 / kdk
				$adminItemData = $adminExtraOption->getAdminOptionItemDisplayFlagList($cid, $cfg_center[center_cid], $goodsno, $option_kind_index);
			    $order_index = 1;
			    foreach($adminItemData as $item) 
			    {
			      if($item) 
			      {
			        $adminExtraOption->setUpdateAdminItemOrderIndex($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $item[item_name], $order_index);
			        $order_index++;
			      }
			    }

      		}
    	}

		$db->query("commit");
    } 
    catch(Exception $e) {
    	$db->query("rollback");
    }
	
    echo "OK";
    exit;

    break;

//자동견적 옵션/가격 복사 (일괄 복사) 기능 추가 / 16.07.14 / kdk
  case 'userOptionCopy_batch':    
    if(!$_POST[source_goodsno]) {
      echo "FAIL|"._("오류입니다.")."[param=source_goodsno]";
      exit;
    }

    if(!$_POST[target_goodsno]) {
      echo "FAIL|"._("오류입니다.")."[param=target_goodsno]";
      exit;
    }  
	
//debug($_POST[source_goodsno]);
//debug($_POST[target_goodsno]);

	/* 견적상품 옵션 복사 */
    $db->start_transaction();
	try {

    	$item = explode(',',$_POST[target_goodsno]); //항목 분리
    	//debug($item);
	    foreach($item as $goodsno) {
	    	//debug($goodsno);
      		if($goodsno) {
				//자동견적 옵션/가격 초기화(삭제)
				$adminExtraOption->extraOptionInit($cid, $cfg_center[center_cid], $goodsno);
			
				//자동견적 옵션/가격 복사
				if($cid != $cfg_center[center_cid]) {
					$adminExtraOption->extraOptionCopy($cid, $cfg_center[center_cid], $goodsno, $_POST[source_goodsno]);
				}
				else {
					$adminExtraOption->extraOptionCopyMall($cid, $cfg_center[center_cid], $goodsno, $_POST[source_goodsno]);
				}
      		}
    	}

		$db->query("commit");
    } 
    catch(Exception $e) {
    	$db->query("rollback");
    }
	    
    echo "OK";
    exit;

    break;
	
//자동견적옵션가격 복사 (일괄 복사) 기능 추가 / 16.07.14 / kdk
  case 'userPriceCopy_batch':    
    if(!$_POST[source_goodsno]) {
      echo "FAIL|"._("오류입니다.")."[param=source_goodsno]";
      exit;
    }

    if(!$_POST[target_goodsno]) {
      echo "FAIL|"._("오류입니다.")."[param=target_goodsno]";
      exit;
    }  
	
//debug($_POST[source_goodsno]);
//debug($_POST[target_goodsno]);

	/* 견적상품 가격 복사 */
    $db->start_transaction();
	try {

    	$item = explode(',',$_POST[target_goodsno]); //항목 분리
    	//debug($item);
	    foreach($item as $goodsno) {
	    	//debug($goodsno);
      		if($goodsno) {
				//자동견적 옵션 가격 초기화(삭제) /DeleteExtraOptionPriceS2($cid, $center_id, $goodsno)
				$adminExtraOption->DeleteExtraOptionPriceS2($cid, $cfg_center[center_cid], $goodsno);
		
				//자동견적 옵션 가격 복사
				if($cid != $cfg_center[center_cid]) {
					//자동견적 가격 테이블 시즌2	
					$adminExtraOption->CopyPriceExtraOptionS2Mall($cid, $cfg_center[center_cid], $goodsno, $_POST[source_goodsno]);
					//수량(건수) 가격 정보를 복사한다. 2014.12.23 by kdk
					$adminExtraOption->CopyUnitPriceExtraOptionMall($cid, $cfg_center[center_cid], $goodsno, $_POST[source_goodsno]);
				}
				else {
					//자동견적 가격 테이블 시즌2
					$adminExtraOption->CopyPriceExtraOptionS2($cid, $cfg_center[center_cid], $goodsno, $_POST[source_goodsno]);
					//수량(건수) 가격 정보를 복사한다. 2014.12.23 by kdk
					$adminExtraOption->CopyUnitPriceExtraOption($cid, $cfg_center[center_cid], $goodsno, $_POST[source_goodsno]);
				}
      		}
    	}		

		$db->query("commit");
    } 
    catch(Exception $e) {
    	$db->query("rollback");
    }
	    
    echo "OK";
    exit;

    break;

	//자동견적옵션(초기화) 기능 추가 2016.04.06 by kdk.
	case 'userOption_delete' :
		if (!$_POST[goodsno]) {
			echo "FAIL|"._("오류입니다.")."[param=goodsno]";
			exit ;
		}

		/* 견적상품 옵션/가격 복사 */
		$db -> start_transaction();
		try {

			//자동견적 옵션 초기화(삭제)
			$adminExtraOption -> extraOptionInit($cid, $cfg_center[center_cid], $_POST[goodsno]);

			$db -> query("commit");
		} catch(Exception $e) {
			$db -> query("rollback");
		}

		echo "OK";
		exit ;

		break;

	//자동견적옵션(복사) 기능 추가 2016.04.06 by kdk.
	case 'userOption_copy' :
		if (!$_POST[goodsno]) {
			echo "FAIL|"._("오류입니다.")."[param=goodsno]";
			exit ;
		}

		if (!$_POST[source_goodsno]) {
			echo "FAIL|"._("오류입니다.")."[param=source_goodsno]";
			exit ;
		}

		/* 견적상품 옵션/가격 복사 */
		$db -> start_transaction();
		try {

			//자동견적 옵션 초기화(삭제)
			$adminExtraOption -> extraOptionInit($cid, $cfg_center[center_cid], $_POST[goodsno]);

			//자동견적 옵션 복사
			if ($cid != $cfg_center[center_cid]) {
				$adminExtraOption -> extraOptionCopyMall($cid, $cfg_center[center_cid], $_POST[goodsno], $_POST[source_goodsno]);
			} else {
				$adminExtraOption -> extraOptionCopy($cid, $cfg_center[center_cid], $_POST[goodsno], $_POST[source_goodsno]);
			}

			$db -> query("commit");
		} catch(Exception $e) {
			$db -> query("rollback");
		}

		echo "OK";
		exit ;

		break;

	//사용자 후가공 옵션 삭제처리
	//masterOption_regist_flag
	case 'masterOption_regist_flag' :
		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		//마스터 옵션 삭제
		$adminExtraOption -> setUpdateAdminOptionRegistFlag($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $_POST[flag]);

		//옵션 사용여부 업데이트
		$adminExtraOption -> setUpdateUseFlag($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $_POST[flag]);

		echo "OK";
		exit ;

		break;

	//출력 항목 명칭 변경하기
	case 'itemNameUpdateS2' :
		$update_name = $_POST[update_name];
		$old_item_name = $_POST[old_item_name];
		$option_item_index = $_POST[option_item_index];

		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$option_item_index) {
			msg(_("오류입니다.")."[param=option_item_index]");
			echo "FAIL";
			exit ;
		}

		if (!$update_name) {
			msg(_("오류입니다.")."[param=update_name]");
			echo "FAIL";
			exit ;
		}

		//자신 옵션 및 부모 옵션 명칭도 같이 변경한다.
		$adminExtraOption -> setUpdateAdminOptionItemName($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $option_item_index, $old_item_name, $update_name);

		echo "OK";
		exit ;

		break;

	//책자 프리셋3 (100112)
	case 'itemNameUpdateS3' :
		$update_name = $_POST[update_name];
		$old_item_name = $_POST[old_item_name];
		$option_item_index = $_POST[option_item_index];
		$extra_data1 = $_POST[extra_data1];
		$extra_data2 = $_POST[extra_data2];

		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$option_item_index) {
			msg(_("오류입니다.")."[param=option_item_index]");
			echo "FAIL";
			exit ;
		}

		if (!$update_name) {
			msg(_("오류입니다.")."[param=update_name]");
			echo "FAIL";
			exit ;
		}

		if (!$extra_data1) {
			msg(_("오류입니다.")."[param=extra_data1]");
			echo "FAIL";
			exit ;
		}
		
		if (!$extra_data2) {
			msg(_("오류입니다.")."[param=extra_data2]");
			echo "FAIL";
			exit ;
		}
				
		//자신 옵션 및 부모 옵션 명칭도 같이 변경한다.
		//extra_data도 변경한다.
		$adminExtraOption -> setUpdateAdminOptionItemNameExtraData($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $option_item_index, $old_item_name, $update_name, $extra_data1, $extra_data2);

		echo "OK";
		exit ;

		break;
		
	//사용 여부 일괄처리
	case 'masterItem_display_flag_batch' :
		$option_kind_code = $_POST[option_kind_code];

		$displayFlagData = array();

		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		foreach ($_POST as $ItemKey => $ItemValue) {
			if (strpos($ItemKey, "display_flag_") !== false) {
				$optionKey = str_replace("display_flag_", "", $ItemKey);
				$displayFlagData[$optionKey] = $ItemValue;
			}
		}

		ksort($displayFlagData);
		//debug($displayFlagData);

		foreach ($displayFlagData as $key => $value) {
			//마스터 옵션 item 출력 여부 업데이트
			$adminExtraOption -> setUpdateDisplayFlag($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $key, $value, $option_kind_code);

			//마스터 옵션 하위 item 출력 여부 업데이트
			$adminExtraOption -> setUpdateChildDisplayFlag($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $key, $value, $option_kind_code);
		}

		echo "OK";
		exit ;

		break;

	//노출 여부 일괄처리
	case 'display_flag_batch' :
		$subOption = array();
		$useFlagData = array();

		$preset = $_POST[preset];

		if (!$preset) {
			msg(_("오류입니다.")."[param=preset]");
			echo "FAIL";
			exit ;
		}

		//debug($r_est_preset_sub_option_group[$preset]);
		if ($r_est_preset_sub_option_group[$preset]) {
			foreach ($r_est_preset_sub_option_group[$preset] as $key => $value) {
				foreach ($value as $k => $v) {
					$vArr = split(',', $v);
					$subOption[$k] = array($vArr[1], $vArr[2]);
				}
			}
		}

		foreach ($_POST as $ItemKey => $ItemValue) {
			if (strpos($ItemKey, "use_flag_") !== false) {
				$optionKey = str_replace("use_flag_", "", $ItemKey);

				//하위 서브 옵션일 경우
				if ($subOption[$optionKey]) {
					//debug($subOption[$optionKey]);
					foreach ($subOption[$optionKey] as $key => $val) {
						//debug($val);
						$useFlagData[$val] = $ItemValue;
					}
				}

				$useFlagData[$optionKey] = $ItemValue;
			}
		}

		ksort($useFlagData);
		//debug($useFlagData);

		//사용여부 리스트
		$data = $adminExtraOption -> getAdminOptionUseList($cid, $cfg_center[center_cid], $goodsno);
		//debug($data);

		foreach ($data as $key => $val) {
			if ($useFlagData[$val[option_kind_index]]) {
				if ($useFlagData[$val[option_kind_index]] != $val[use_flag]) {
					//echo $val[option_kind_index]."-".$val[use_flag]."-".$useFlagData[$val[option_kind_index]].",";
					//$val[use_flag];
					$adminExtraOption -> setUpdateUseFlag($cid, $cfg_center[center_cid], $goodsno, $val[option_kind_index], $useFlagData[$val[option_kind_index]]);
				}
			}
		}

		//foreach ($useFlagData as $key => $value)
		//{
		// $adminExtraOption->setUpdateUseFlag($cid, $cfg_center[center_cid], $goodsno, $key, $value);
		//}

		echo "OK";
		exit ;

		break;

	case 'display_flag' :
		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$use_flag) {
			msg(_("오류입니다.")."[param=flag]");
			echo "FAIL";
			exit ;
		}

		$option_kind_index_arr = explode('|', $option_kind_index);
		//항목 분리
		foreach ($option_kind_index_arr as $key => $value) {
			$adminExtraOption -> setUpdateUseFlag($cid, $cfg_center[center_cid], $goodsno, $value, $use_flag);
		}

		echo "OK";
		exit ;

		break;

	case 'displayflagNitemPriceType' :
		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$use_flag) {
			msg(_("오류입니다.")."[param=flag]");
			echo "FAIL";
			exit ;
		}

		if (!$item_price_type) {
			msg(_("오류입니다.")."[param=type]");
			echo "FAIL";
			exit ;
		}

		$option_kind_index_arr = explode('|', $option_kind_index);
		//항목 분리
		foreach ($option_kind_index_arr as $key => $value) {
			$adminExtraOption -> setUpdateUseFlag($cid, $cfg_center[center_cid], $goodsno, $value, $use_flag);
			$adminExtraOption -> setUpdateAdminItemPriceType($cid, $cfg_center[center_cid], $goodsno, $value, $item_price_type);
		}

		echo "OK";
		exit ;

		break;

	case 'order_index' :
		$vals = $_POST[vals];

		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$vals) {
			msg(_("오류입니다.")."[param=vals]");
			echo "FAIL";
			exit ;
		}

		$item = explode('|', $vals);
		//항목 분리
		$order_index = 1;

		//실제 가격 테이블에 해당 데이타가 있을 경우 같이 변경해 준다.
		$opt_data = $adminExtraOption -> checkOptionPriceList($cid, $cfg_center[center_cid], $goodsno);

		foreach ($item as $key) {
			if ($key) {
				$adminExtraOption -> setUpdateAdminItemOrderIndex($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $key, $order_index);

				//실제 가격 테이블 정보 변경.
				if ($opt_data) {
					$adminItemData = $adminExtraOption -> getAdminOptionItemInfo($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $key);
					$adminExtraOption -> setOptionItemIndexUpdate($cid, $cfg_center[center_cid], $goodsno, $adminItemData[extra_tbl_kind_index], $key, $order_index);
				}
				$order_index++;
			}
		}
		echo "OK";
		exit ;

		break;

	case 'use_flag' :
		$val = $_POST[val];
		$flag = $_POST[flag];
		$option_kind_code = $_POST[option_kind_code];

		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$val) {
			msg(_("오류입니다.")."[param=val]");
			echo "FAIL";
			exit ;
		}

		$item_name = $val;
		$display_flag = $flag;

		//실제 가격 테이블에 해당 데이타가 있을 경우 같이 변경해 준다.
		/*$opt_data = $adminExtraOption->checkOptionPriceList($cid, $cfg_center[center_cid], $goodsno);
		 if ($opt_data)
		 {
		 $adminItemData = $adminExtraOption->getAdminOptionItemInfo($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $item_name);
		 $adminExtraOption->setOptionItemDisplayFlagUpdate($cid, $cfg_center[center_cid], $goodsno, $adminItemData[extra_tbl_kind_index], $item_name, $display_flag);
		 }*/

		//마스터 옵션 item 출력 여부 업데이트
		$adminExtraOption -> setUpdateAdminItemDisplayFlag($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $option_group_type, $item_name, $display_flag, $option_kind_code);

		//마스터 옵션 하위 item 출력 여부 업데이트
		$adminExtraOption -> setUpdateAdminChildItemDisplayFlag($cid, $cfg_center[center_cid], $goodsno, $item_name, $display_flag, $option_kind_code);

		echo "OK";
		exit ;

		break;

	case 'regist_flag' :
		$val = $_POST[val];
		$flag = $_POST[flag];
		$option_kind_code = $_POST[option_kind_code];

		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$val) {
			msg(_("오류입니다.")."[param=val]");
			echo "FAIL";
			exit ;
		}
		$item_name = $val;

		//실제 가격 테이블에 해당 데이타가 있을 경우 같이 변경해 준다.
		/*$opt_data = $adminExtraOption->checkOptionPriceList($cid, $cfg_center[center_cid], $goodsno);
		 if ($opt_data)
		 {
		 $adminItemData = $adminExtraOption->getAdminOptionItemInfo($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $item_name);
		 $adminExtraOption->setOptionItemRegistFlagUpdate($cid, $cfg_center[center_cid], $goodsno, $adminItemData[extra_tbl_kind_index], $item_name, 'N');
		 }*/

		$adminExtraOption -> setUpdateAdminItemRegistFlag($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $item_name, "N", $option_kind_code);

		//마스터 옵션 하위 item 삭제 2014.06.26 by kdk -- 오류 발생 확인될때 까지 사용안함.
		$adminExtraOption -> setUpdateAdminChildItemRegistFlag($cid, $cfg_center[center_cid], $goodsno, $item_name, "N", $option_kind_code);

		//option_item_index 재정렬 2014.06.26 by kdk
		$adminItemData = $adminExtraOption -> getAdminOptionItemList($cid, $cfg_center[center_cid], $goodsno, $option_kind_index);

		$order_index = 1;
		foreach ($adminItemData as $item) {
			if ($item) {
				$adminExtraOption -> setUpdateAdminItemOrderIndex($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $item[item_name], $order_index);

				//실제 가격 테이블 정보 변경.
				if ($opt_data) {
					$adminExtraOption -> setOptionItemIndexUpdate($cid, $cfg_center[center_cid], $goodsno, $item[extra_tbl_kind_index], $item[item_name], $order_index);
				}
				$order_index++;
			}
		}

		echo "OK";
		exit ;

		break;

	//출력 옵션 그룹 명칭 변경하기
	case 'displaynameUpdate' :
		$update_name = $_POST[update_name];
		$old_display_name = $_POST[old_display_name];

		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$update_name) {
			msg(_("오류입니다.")."[param=update_name]");
			echo "FAIL";
			exit ;
		}

		//실제 가격 테이블에 해당 데이타가 있을 경우 같이 변경해 준다.
		$opt_data = $adminExtraOption -> checkOptionPriceList($cid, $cfg_center[center_cid], $goodsno);

		$option_kind_index_arr = explode('|', $option_kind_index);
		//항목 분리
		foreach ($option_kind_index_arr as $key => $value) {
			if ($opt_data) {
				$adminItemData = $adminExtraOption -> getAdminOptionItemInfo($cid, $cfg_center[center_cid], $goodsno, $value);
				$adminExtraOption -> setOptionItemDisplayNameUpdate($cid, $cfg_center[center_cid], $goodsno, $adminItemData[extra_tbl_kind_index], $old_display_name, $update_name);
			}
			$adminExtraOption -> setUpdateAdminDisplayName($cid, $cfg_center[center_cid], $goodsno, $value, $update_name);
		}

		echo "OK";
		exit ;

		break;

	//출력 항목 명칭 변경하기
	case 'itemNameUpdate' :
		$update_name = $_POST[update_name];
		$old_item_name = $_POST[old_item_name];
		$item_index = $_POST[option_item_index];
		$sub_kind_index = $_POST[sub_kind_index];

		if (!$option_kind_index) {
			msg(_("오류입니다.")."[param=option_kind_index]");
			echo "FAIL";
			exit ;
		}

		if (!$update_name) {
			msg(_("오류입니다.")."[param=update_name]");
			echo "FAIL";
			exit ;
		}

		//실제 가격 테이블에 해당 데이타가 있을 경우 같이 변경해 준다.
		$opt_data = $adminExtraOption -> checkOptionPriceList($cid, $cfg_center[center_cid], $goodsno);
		if ($opt_data) {
			$adminItemData = $adminExtraOption -> getAdminOptionItemInfo($cid, $cfg_center[center_cid], $goodsno, $option_kind_index);
			$adminExtraOption -> setOptionItemNameUpdate($cid, $cfg_center[center_cid], $goodsno, $adminItemData[extra_tbl_kind_index], $old_item_name, $update_name);
		}
		//자신 옵션 및 부모 옵션 명칭도 같이 변경한다.
		$adminExtraOption -> setUpdateAdminOptionItemName($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $item_index, $old_item_name, $update_name);
		$adminExtraOption -> setUpdateAdminOptionParentItemName($cid, $cfg_center[center_cid], $goodsno, $sub_kind_index, $old_item_name, $update_name);

		echo "OK";
		exit ;

		break;

	case 'order_indexS3' :
		$vals = $_POST[vals];

		if (!$_POST[goodsno]) {
			echo "FAIL|"._("오류입니다.")."[param=goodsno]";
			exit ;
		}

		$order_index = 1;
		//$order_index = 1;
		//$order_index = 1;
		foreach ($_POST[sort] as $ItemKey => $ItemValue) {
			$postArr = split("[|]", $ItemValue); //특수기호 오류??? [] 를 사용함.
			
			//option_kind_index|option_kind_code|parent_item_name|item_name	

			if ($postArr[2] == "") { //부모 아이템이 없을 경우는 1차.
				//$postArrData[$postArr[0]][$postArr[1]][$postArr[2]][] = $postArr[3];
				$arrData[$postArr[0]][] = array(
					'option_kind_index' => $postArr[0], 
					'option_kind_code' => $postArr[1], 
					'item_name' => $postArr[3], 
					'option_item_index' => $order_index
				);
				
				$order_index++;
			}
			else {
				$postArrData[$postArr[0]][$postArr[1]][$postArr[2]][] = $postArr[3];
			}
		}

		foreach ($postArrData as $optionKindIndex => $optionKindIndexVal) {//option_kind_index			
			foreach ($optionKindIndexVal as $optionKindCode => $optionKindCodeVal) { //option_kind_code
				foreach ($optionKindCodeVal as $parentItemName => $parentItemNameVal) { //parent_item_name					
					$order_index = 1;	
					foreach ($parentItemNameVal as $itemName => $itemNameVal) { //item_name
						$arrData[$optionKindIndex][] = array(
							'option_kind_index' => $optionKindIndex, 
							'option_kind_code' => $optionKindCode,
							'parent_item_name' => $parentItemName, 
							'item_name' => $itemNameVal, 
							'option_item_index' => $order_index
						);
						
						$order_index++;
					}					
				}				
			}
		}	

//debug($arrData);		
	
		foreach ($arrData as $key => $val) {
			foreach ($val as $item) {
				if ($item) {
					$adminExtraOption -> setUpdateAdminItemOrderIndexS3($cid, $cfg_center[center_cid], $goodsno, 
						$item[option_kind_index], 
						$item[option_kind_code], 
						$item[parent_item_name],
						$item[item_name],
						$item[option_item_index]
					);
				}
			}			
		}

		go($_SERVER[HTTP_REFERER]);
		echo "OK";
		exit ;

		break;

	default :
		break;
}
?>