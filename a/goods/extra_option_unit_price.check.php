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

$adminExtraOption = new M_extra_option();

$go_url = "extra_option_unit_price.php?goodsno=$_GET[goodsno]";
$go_url_script = "location.href='$go_url'";
//master 정보에서 실제 옵션 가격 테이블로 생성한다.
//가격 테이블에 데이타가 없을 경우만 처리

$opt_data = $adminExtraOption -> getOptionUnitPrice($cid, $cfg_center[center_cid], $_GET[goodsno]);
//debug($opt_data);

//tb_extra_option_order_cnt unit_cnt_rule 조회
$data = $adminExtraOption -> getOrderCntList($cid, $cfg_center[center_cid], $_GET[goodsno], "OCNT");
//debug($data);

$bPriceInsertFlag = false;
if ($opt_data) {
	$admin_max_date = $opt_data[regist_date];
	$admin_max_update_date = $opt_data[update_date];

	//debug("admin_max_date=".$admin_max_date);
	//debug("admin_max_update_date=".$admin_max_update_date);

	if ($data[unit_cnt_rule] != $opt_data[unit_cnt_rule] && $admin_max_update_date > $admin_max_date) {
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