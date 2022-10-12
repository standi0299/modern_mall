<?
include "../lib.php";

if ($_GET[goodsno]) {
	//$data = $db -> fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
	$mGoods = new M_goods();
	$data = $mGoods->getInfo($_GET[goodsno]);
	if (!$data) {
		msg(_("상품데이터가 존재하지 않습니다."), -1);
		exit ;
	}
	$fkey = $data[goodsno];
}

if ($data) {
	$extra_option = explode('|', $data[extra_option]);
	//항목 분리
	if (count($extra_option) > 0) {
		$extra_product = $extra_option[0];
		$extra_preset = $extra_option[1];
		$extra_price_type = $extra_option[2];
	}
}

$m_extra_option = new M_extra_option();
$option_group_type_where = $_GET[mode];
$addWhere = " and option_group_type = '" . $_GET[mode] . "OPTION' ";

//후가공이면...
if ($_GET[mode] == "AFTER") {
	$option_group_type_where = $_GET[kind];
	//후가공 코드가 있으면...
	if ($_GET[kind])
		$addWhere1 = " and option_kind_code = '$_GET[kind]' ";

	//getExtraOptionMasterKindIndex
	$optKindIndex = $m_extra_option -> getExtraOptionMasterKindIndex($cid, $cfg_center[center_cid], $_GET[goodsno], $addWhere . $addWhere1);
	//debug("optKindIndex=".$optKindIndex);
	if ($optKindIndex) {
		$addWhere2 = " and option_kind_index = $optKindIndex ";
	}
	//debug($addWhere2);
}
//debug($addWhere);
//debug($addWhere1);

//$_GET[ptType]; //가격 테이블 타입 기본: x(수량) , y(옵션/가격)  _reverse : (옵션/가격) , y(수량)
//$_GET[docUse]; //후가공(규격포함) 여부
//debug($option_group_type_where);

$go_url = "extra_option_price.php?goodsno=$_GET[goodsno]&mode=$_GET[mode]&kind=$_GET[kind]&filename=$_GET[filename]&docUse=$_GET[docUse]&ptType=$_GET[ptType]";
$go_url_script = "location.href='$go_url'";
//master 정보에서 실제 옵션 가격 테이블로 생성한다.
//가격 테이블에 데이타가 없을 경우만 처리

$opt_data = $m_extra_option -> checkOptionPriceListS3($cid, $cfg_center[center_cid], $_GET[goodsno], $option_group_type_where);
//debug($opt_data);
//debug("aaa");
//exit;

$bPriceInsertFlag = false;
if ($opt_data) {
	$price_max_date = $m_extra_option -> getOptionListMaxDateS3($cid, $cfg_center[center_cid], $_GET[goodsno], $option_group_type_where);
	$use_max_date = $m_extra_option -> getAdminOptionUseFlagMaxDate($cid, $cfg_center[center_cid], $_GET[goodsno], $addWhere . $addWhere2);
	$admin_max_date = $m_extra_option -> getAdminOptionListMaxDate($cid, $cfg_center[center_cid], $_GET[goodsno], $addWhere . $addWhere1);
	$admin_max_update_date = $m_extra_option -> getAdminOptionListMaxUpdateDate($cid, $cfg_center[center_cid], $_GET[goodsno], $addWhere . $addWhere1);

	//debug("price_max_date=".$price_max_date);
	//debug("use_max_date=".$use_max_date);
	//debug("admin_max_date=".$admin_max_date);
	//debug("admin_max_update_date=".$admin_max_update_date);

	//옵션에서 분리된 규격 항목에 추가(또는 변경)된 사항 체크
	if ($_GET[mode] != "AFTER") {
		if ($admin_max_update_date == "" || $admin_max_update_date < $price_max_date) {
			$admin_max_update_date = $m_extra_option -> getAdminOptionListMaxUpdateDate($cid, $cfg_center[center_cid], $_GET[goodsno], " and option_group_type = 'DOCUMENT' ");
			//debug("admin_max_update_date=".$admin_max_update_date);
		}
	} else {
		//후가공이면 규격 포함이 추가 되면서 확인이 필요함. tb_extra_option_master_use.option_kind_index = 101
		$use_max_date2 = $m_extra_option -> getAdminOptionUseFlagMaxDate($cid, $cfg_center[center_cid], $_GET[goodsno], " and option_kind_index='101'");

		//debug($price_max_date);
		//debug($use_max_date2);
		//exit;
	}
	//debug("use_max_date2=".$use_max_date2);
	//if ($admin_max_date > $price_max_date) echo "string1";
	//if ($use_max_date > $price_max_date) echo "string2";
	//if ($admin_max_update_date > $price_max_date) echo "string3";
	//if ($use_max_date2 > $price_max_date) echo "string4";
	//exit;
	if ($admin_max_date > $price_max_date || $use_max_date > $price_max_date || $admin_max_update_date > $price_max_date || $use_max_date2 > $price_max_date) {
		msg_confirm(_("가격정보 입력 이후 추가(또는 변경)된 옵션 항목이 있습니다. 기존 가격정보를 삭제하고 다시 설정하시겠습니까? [취소]를 선택할 경우 기존 가격정보를 불러옵니다."), "location.href='$go_url&all_delete=Y'", $go_url_script);
		exit ;
	} else {
		go($go_url);
		exit ;
	}
} else {
	go($go_url);
	exit ;
}
?>