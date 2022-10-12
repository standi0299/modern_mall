<?

$podspage = true;
$posnm = _("주문완료");
include "../_header.php";

$r_rid = get_release();
$m_order = new M_order();
$data = $db->fetch("select * from exm_pay where payno = '$_GET[payno]'");

//debug($data);&
$query = "select * from exm_ord where payno = '$data[payno]'";
$res = $db->query($query);
while ($ord = $db->fetch($res)){

    if ($_GET[mobile_type] != "Y" && ($cfg[skin_theme] == "P1" || $cfg[skin_theme] == "B1")) { //B1 추가 / 20190208 / kdk.
        $data[order_shiptype] = $ord[order_shiptype];
    }

	$data['ord'][$ord[ordno]] = $ord;
	$query = "select * from exm_ord_item where payno = '$data[payno]' and ordno = '$ord[ordno]'";
	$res2 = $db->query($query);
	while ($item = $db->fetch($res2)){
      //20150907 / minks / 모바일 제작기간, 배송기간 안내 문구 출력
		/*list($item[leadtime]) = $db->fetch("select leadtime from exm_goods where goodsno='$item[goodsno]'",1);
		$item[leadtime] = str_split(preg_replace("/[^0-9]*\/s", "", $item[leadtime]));
		if (!$data[leadtime] || $item[leadtime][sizeof($item[leadtime])-1] > $data[leadtime][sizeof($data[leadtime])-1]) {
         $data[leadtime] = $item[leadtime];
			$data[deliverytime] = $data[leadtime][sizeof($data[leadtime])-1]+2;
		}*/
      
		if ($item[est_order_option_desc]) $item[est_order_option_desc_str] = $item[est_order_option_desc]; //str_replace(",","<br/>",$item[est_order_option_desc]);
		if ($item[addopt]) $item[addopt] = unserialize($item[addopt]);
		if ($item[printopt]) $item[printopt] = unserialize($item[printopt]);
      
      $ext_data = $m_order->getEditorJsonData($item[storageid]);
      $ext_data = json_decode($ext_data,1);
      $cover_range_data = $db->fetch("select * from md_cover_range_option where cover_id = '$ext_data[cover_range_id]'");
      $item[cover_range_data] = $cover_range_data[cover_range]."/".$r_cover_type[$cover_range_data[cover_type]]."/".$cover_range_data[cover_paper_name]."/".$cover_range_data[cover_coating_name];

		$data['ord'][$ord[ordno]][item][$item[ordseq]] = $item;

		$db->query("delete from exm_cart where cartno = '$item[cartno]'");
	}
}

//각 데이터들간의 간격을 띄운다. 미오디오 이창훈 이사 요청 / 19.02.27 / kjm
if($data[bankinfo])
   $data[bankinfo] = str_replace("/", " / ", $data[bankinfo]);

//$data[leadtime] = ($data[leadtime][1]) ? $data[leadtime][0]."~".$data[leadtime][1] : $data[leadtime][0];

//포토큐브 네이버프리미엄로그분석용.
$acecounter_mode = "payend";

	//payment_script > {payprice} 치환 	20.01.02 jtkim
	if($cfg[payment_script] && strpos($cfg[payment_script], "{payprice}") !== false)
		$cfg[payment_script] = str_replace("{payprice}",$data[payprice],$cfg[payment_script]);

$tpl->assign($data);
$tpl->print_('tpl');
?>