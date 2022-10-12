<?
include "../library.php";
include "../lib_const.php";
//chkMember();
//$db->query("set names utf8");

//$this->center_id = $this->cfg_center[center_cid];
//$this->cid  = $GLOBALS[cid];
//$this->center_id = $GLOBALS[cid];

try {

	$imode = $_POST[imode];
	$goodsno = $_POST[goodsno];
	$option_kind_index = $_POST[option_kind_index];
	$item_value_1 = $_POST[f_item_name];
	$item_value_2 = $_POST[f_item_name2];
	$extra_data1 = $_POST[extra_data1];
	$extra_data2 = $_POST[extra_data2];

	$option_group_type = $_POST[option_group_type];
	$same_price_item_name = $_POST[f_same_item];
	$display_name = $_POST[display_name];
	$use_flag = $_POST[use_flag];
	$item_price_type = $_POST[item_price_type];

	//2차 옵션
	$sub_option_kind_index = $_POST[sub_option_kind_index];

	//부모 아이템 이름
	$parent_item_name = $_POST[parent_item_name];

	//프리셋
	$preset = $_POST[preset];
	$option_kind_code = $_POST[option_kind_code];

	$classExtraOption = new M_extra_option();

	//탭,엔터키 제거 2014.06.26 by kdk
	$item_value_1 = strDelEntTab($item_value_1);
	$item_value_2 = strDelEntTab($item_value_2);

	$item_value_1_arr = split(',', $item_value_1);
	$item_value_2_arr = split(',', $item_value_2);

	//3차 옵션
	if ($_POST[f_item_name3]) {
		$sub_sub_option_kind_index = $sub_option_kind_index + 1;

		$item_value_3 = $_POST[f_item_name3];
		$item_value_3 = strDelEntTab($item_value_3);
		$item_value_3_arr = split(',', $item_value_3);
	}

	//후가공 옵션 처음 추가
	if ($imode == 'AFTER_NEW') {
		$option_kind_index = $classExtraOption -> getMaxExtraOptionMasterKindIndex($cid, $cfg_center[center_cid], $goodsno) + 1;
	}

	$main_option_item_index = $classExtraOption -> getMaxExtraOptionMasterItemIndexS3($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $parent_item_name, $option_kind_code);
	//$sub_option_item_index = $classExtraOption->getMaxExtraOptionMasterItemIndex($cid, $cfg_center[center_cid], $goodsno, $option_kind_index + 1);
	//$sub_option_item_index = 1;

	//1차 항목에 대해서 같은 값이 가진 item_name이 있을 경우는 추가하지 않는다.
	$bSameItemNameCheck = FALSE;
	foreach ($item_value_1_arr as $key => $value) {
		$value = trim($value);
		if ($value) {
			$item_data = $classExtraOption -> getSameItemNameS3($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $value, $parent_item_name, $option_kind_code);
			if ($item_data) {
				$bSameItemNameCheck = TRUE;

				echo("'$value' "._("항목이 이미 등록 되어 있습니다."));
				//break;
				exit ;
			}
		}
	}

	foreach ($item_value_1_arr as $key => $value) {
		$value = trim($value);
		if ($value) {
			$main_option_item_index++;
			$classExtraOption -> InsertExtraOptionMasterS3($cid, $cfg_center[center_cid], $goodsno, $option_kind_index, $value, $main_option_item_index, $parent_item_name, $same_price_item_name, $extra_data1, $extra_data2, $option_group_type, $display_name, $item_price_type, $use_flag, $preset, $option_kind_code);
		}

		$sub_option_item_index = 1;
		foreach ($item_value_2_arr as $subKey => $subValue) {
			$subValue = trim($subValue);
			if ($subValue) {
				//$sub_option_item_index++;
				
				$classExtraOption -> InsertExtraOptionMasterS3($cid, $cfg_center[center_cid], $goodsno, $sub_option_kind_index, $subValue, $sub_option_item_index, $value, $same_price_item_name, $extra_data1, $extra_data2, $option_group_type, $display_name, $item_price_type,"Y",$preset,$option_kind_code);

				//3차 옵션
				if ($item_value_3_arr) {
					$sub_sub_option_item_index = 1;

					foreach ($item_value_3_arr as $subsubKey => $subsubValue) {
						$subsubValue = trim($subsubValue);
						if ($subsubValue) {
							$classExtraOption -> InsertExtraOptionMasterS3($cid, $cfg_center[center_cid], $goodsno, $sub_sub_option_kind_index, $subsubValue, $sub_sub_option_item_index, $subValue, $same_price_item_name, $extra_data1, $extra_data2, $option_group_type, $display_name, $item_price_type,"Y",$preset,$option_kind_code);

							$sub_sub_option_item_index++;
						}
					}
				}

				$sub_option_item_index++;
			}
		}
	}

	//$db->end_transaction();

} catch(Exception $ex) {
	msg("$ex->getMessage()", -1);
	exit ;
}

echo("OK");
exit;
?>