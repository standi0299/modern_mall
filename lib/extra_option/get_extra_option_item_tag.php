<?
include "../library.php";
//chkMember();
//$db->query("set names utf8");

$return_data = "";
$classExtraOption = new M_extra_option();
$res = $classExtraOption -> getAdminOptionItemList($cid, $cfg_center[center_cid], $_POST[goodsno], $_POST[option_kind_index]);
//debug($res);

//<!--자동견적 자체상품 추가 2015.07.02 by kdk-->
list($reg_cid) = $db -> fetch("select reg_cid from exm_goods where goodsno='$_POST[goodsno]' limit 1", 1);
//자체상품 여부
$checkRegCid = FALSE;
if ($reg_cid && $reg_cid = $cid) $checkRegCid = TRUE;
//<!--자동견적 자체상품 추가 2015.07.02 by kdk-->
//debug($checkRegCid);

//itemName을 변경을 위해 서브 옵션을 찾는다.
$subOption = array();
$preset = $_POST[preset];

if ($r_est_preset_sub_option_group[$_POST[preset]]) {
	foreach ($r_est_preset_sub_option_group[$_POST[preset]] as $key => $value) {
		foreach ($value as $k => $v) {
			$vArr = split(',', $v);
			$subOption[$vArr[1]] = $vArr[2];
		}
	}
}
//debug($subOption);

$return_data .= "<li style='padding-top:5px;'>\r\n
      <table style='width: 450px;'>
        <tr>
          <td width='50' align='center'>"._("항목순서")."</td>
          <td align='center'>"._("항목명")."</td>
          <td width='120' align='center'>"._("사용여부")."</td>
          <td width='30' align='center'>"._("삭제")."</td>
        </tr>  
      </table>
    </li>";

foreach ($res as $key => $data) {

	$return_data .= "<li style='padding-top:5px;'>\r\n
      <table style='width: 450px;'>
        <tr>
          <td width='50' align='center'>
            <img src='../img/bt_up.png' class='hand absmiddle' onclick='MoveUpItem(this)'/>
            <img src='../img/bt_down.png' class='hand absmiddle' onclick='MoveDownItem(this)'/>                     
          </td>
          <td>
              <input type='hidden' name='item_name' value='$data[item_name]' />
              <input type='hidden' name='same_price_item_name' value='$data[same_price_item_name]' />
              <input type='hidden' name='option_kind_code' value='$data[option_kind_code]' />
              ";
	//<span>$data[item_name]</span>

	if ($subOption[$data[option_kind_index]]) {
		$subOptionKindIndex = $subOption[$data[option_kind_index]];
		//optionKindIndex, itemIndex, subOptionKindIndex, itemName, obj
		$item_name = "<a href=\"#\" onclick=\"openItemNameUpdate('$data[option_kind_index]','$data[option_item_index]','$subOptionKindIndex','$data[item_name]',this);return false;\">$data[item_name]</a>";

		$return_data .= "<span>$item_name</span>";
	} else {
		//optionKindIndex, optionItemIndex, itemName, obj
		$item_name = "<a href=\"#\" onclick=\"openItemNameUpdateS2('$data[option_kind_index]','$data[option_item_index]','$data[item_name]',this);return false;\">$data[item_name]<a/>";

		if ($preset == "100112" && ($data[option_group_type] == "AFTEROPTION" && $data[option_kind_index] == "2") || $data[option_group_type] == "DOCUMENT") {
			//optionKindIndex, optionItemIndex, itemName, optionGroupType, extData1, extData2, obj
			$item_name = "<a href=\"#\" onclick=\"openItemNameUpdateS3('$data[option_kind_index]','$data[option_item_index]','$data[item_name]','$data[option_group_type]','$data[extra_data1]','$data[extra_data2]',this);return false;\">$data[item_name]<a/>";
		}

		$return_data .= "<span>$item_name</span>";
	}

	if ($data[same_price_item_name] != "") {
		$return_data .= "<div class='green'>("._("같은 가격")." '" . $data[same_price_item_name] . "' "._("설정").")<div>";
	}

	$return_data .= "</td>
          <td width='120' align='center'>
            <span>
            <select name='regist_flag' id='display_flag_$data[option_item_index]'>";
	if ($data[display_flag] == "Y") {
		$return_data .= "<option value='Y' selected>"._("사용")."</option>";
	} else {
		$return_data .= "<option value='Y'>"._("사용")."</option>";
	}

	if ($data[display_flag] == "N") {
		$return_data .= "<option value='N' selected>"._("사용안함")."</option>";
	} else {
		$return_data .= "<option value='N'>"._("사용안함")."</option>";
	}

	$return_data .= "</select>&nbsp;<img src='../img/sbtn_apply.gif' class='hand absmiddle' onclick='saveUseFlag(this)'/>
            </span>
          </td>";

	//if ($cid == $cfg_center[center_cid] || $checkRegCid) {
	$return_data .= "<td width='30' align='center'>
            <img src='../img/bt_del.png' class='hand absmiddle' onclick='saveRegistFlag(this)'/>
          </td>";
	//}
	//else {
	//$return_data .= "<td width='30' align='center'></td>";
	//}

	$return_data .= "</tr>
      </table>
    </li>";
}

$return_data .= "<li style='padding-top:5px;'>\r\n
      <table style='width: 450px;'>
        <tr>
          <td width='50' align='center'>
          <input type='image' src='../img/sbtn_apply.gif' style='vertical-align: middle; width: 27px; height: 17px;' onclick='saveOrderIndex();' />
          </td>
          <td>
          </td>
          <td width='120' align='center'>
          <input type='image' src='../img/sbtn_apply.gif' style='vertical-align: middle; width: 27px; height: 17px;' onclick='batchSaveMasterItemDisplayFlag($_POST[option_kind_index]);' />        
          </td>
          <td width='30' align='center'>
          </td>
        </tr>  
      </table>
    </li>";

echo $return_data;
?>