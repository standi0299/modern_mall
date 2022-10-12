<?

include "../_header.php";

if (!$_GET[guest]) chkMember();
$m_member = new M_member();
$m_order = new M_order();
$m_cash = new M_cash_receipt();

$r_rid["|self|"] = $cfg[nameSite];
$r_rid_x = get_release();
$r_rid = array_merge($r_rid, $r_rid_x);
### 배송업체정보추출
$r_shipcomp = get_shipcomp();

if (!$_GET[payno] && $_GET[order_code]) {
	$tableName = "exm_ord_upload";
	$addWhere = "where upload_order_code = '$_GET[order_code]'";
	$order_data = $m_order->getOrderInfo($cid, $tableName, $addWhere); 
	$_GET[payno] = $order_data[payno];
}

$tableName2 = "exm_pay";
$addWhere2 = "where payno = '$_GET[payno]'";
$data = $m_order->getOrderInfo($cid, $tableName2, $addWhere2);

if (!$data[payno]) {
	//msg(_("잘못된 접근입니다."), -1);
}

if ($_GET[mobile_type] != "Y" && $cfg[skin_theme] == "P1") {
	$data[receiver_mobile] = explode("-", $data[receiver_mobile]);
	$selected[receiver_mobile][$data[receiver_mobile][0]] = "selected";
}

$query = "select * from exm_ord where payno = '$data[payno]'";
$db->query("set names utf8");
$res = $db->query($query);

while ($ord = $db->fetch($res)) {
	$data['ord'][$ord[ordno]] = $ord;
	$item = array();
	
	if ($_GET[mobile_type] != "Y" && ($cfg[skin_theme] == "P1" || $cfg[skin_theme] == "B1")) { //B1 추가 / 20190208 / kdk.
		$data[order_shiptype] = $ord[order_shiptype];
	}
	
    //paymethod를 가져오기 위해 join 추가 / 14.12.29 / kjm
    $data2 = $m_order->getOrderViewList($data[payno], $ord[ordno]);
	foreach ($data2 as $item) {
		if ($item[est_order_option_desc]) $item[est_order_option_desc_str] = str_replace(",", "<br/>", $item[est_order_option_desc]);
		if ($item[addopt]) $item[addopt] = unserialize($item[addopt]);
		if ($item[printopt]) $item[printopt] = unserialize($item[printopt]);
		
		if ($item[dc_couponsetno]) {
			$item[dc_coupon_name] = $m_member->getUseCouponInfo($item[dc_couponsetno]);
		}
		
		if ($item[coupon_areservesetno]) {
			$item[reserve_coupon_name] = $m_member->getUseCouponInfo($item[coupon_areservesetno]);
		}
		
		if ($item[shipcode] && $item[itemstep] >= 5 && $item[itemstep] < 10) {
		  if($cfg[skin_theme] == "M2") { //kkwon  20.08.26 비회원 배송 추적
			$item[strshipcode] = "<a href='".$r_shipcomp[$item[shipcomp]][url].$item[shipcode]."' target='_blank' class='btn_tracking'>배송추적</a>";
			$item[strshipcode_url] = $r_shipcomp[$item[shipcomp]][url].$item[shipcode];
			$item[strshipcode_compnm] = $r_shipcomp[$item[shipcomp]][compnm];
		  }else{
			$item[strshipcode] = "<div class='col'>".$r_shipcomp[$item[shipcomp]][compnm]."<br><a href='".$r_shipcomp[$item[shipcomp]][url].$item[shipcode]."' target='_blank'>".$item[shipcode]."</a></div>";
			$item[strshipcode_url] = $r_shipcomp[$item[shipcomp]][url].$item[shipcode];
			$item[strshipcode_compnm] = $r_shipcomp[$item[shipcomp]][compnm];  
		  }
		}

		if ($_GET[mobile_type] == "Y") {
			//20160211 / minks / 옵션값만 추출
			if ($item[opt]) {
				$item[opt] = explode(" / ", $item[opt]);
				if ($item[opt][0]) {
					$item[opt][0] = explode(":", $item[opt][0]);
					$item[mobile_opt] = $item[opt][0][1];
				}
				if ($item[opt][1]) {
					$item[opt][1] = explode(":", $item[opt][1]);
					$item[mobile_opt] .= " / ".$item[opt][1][1];
				}
			}

			//20150824 / minks / 요일 조회
			$week = array(_("일"),_("월"),_("화"),_("수"),_("목"),_("금"),_("토"));
			$data[week] = $week[date('w',strtotime(substr($data[orddt], 0, 10)))];
		}
		
		if ($_GET[mobile_type] != "Y" && $cfg[skin_theme] == "P1") {
			$m_goods = new M_goods();
			$categoryArr = $m_goods->getGoodsCategoryInfo($cid, $item[goodsno]);
			foreach ($categoryArr as $cat_k=>$cat_v) {
				switch (strlen($cat_v[catno])) {
					case "3":
						$item[catno1] = $cat_v[catno];
						$item[catnm1] = $cat_v[catnm];
						break;
					case "6":
						$item[catnm2] = $cat_v[catnm];
						break;
					case "9":
						$item[catnm3] = $cat_v[catnm];
						break;
					case "12":
						$item[catnm4] = $cat_v[catnm];
						break;
				}
			}
			
			if ($item[opt]) {
				$item[opt] = explode(" / ", $item[opt]);
				if ($item[opt][0]) {
					$item[opt][0] = explode(":", $item[opt][0]);
					$item[mobile_opt] = $item[opt][0][1];
				}
				if ($item[opt][1]) {
					$item[opt][1] = explode(":", $item[opt][1]);
					$item[mobile_opt] .= " / ".$item[opt][1][1];
				}
			}
		}
		
		if ($item[est_order_data]) {
			$est_arr = json_decode($item[est_order_data], true);
			$item[order_cnt_select] = $est_arr[order_cnt_select];
			$item[unit_order_cnt] = $est_arr[unit_order_cnt];
		}

		$data['ord'][$ord[ordno]][item][$item[ordseq]] = $item;
		
		if ($item[goodsno] == "-2") {
			$order_data2 = $m_order->getOrdUploadItemInfo($item[payno], $item[ordno], $item[ordseq]);
			$upload_err_code = $order_data2[upload_err_code];
			
			if ($upload_err_code) {
				$data[studio_pay_err] = 1;
			}
			
			$studio_pay = true;
			$upload_order_code = $item[upload_order_code];
		}
		
		###편집정보 미리보기 이미지###
		$previewLink = $m_order->getEditorPreviewLink($item[storageid]);

		if ($previewLink == "") {
			$clsPods = new PODStation("20");
         $loop = $clsPods->GetPreViewImg($item[storageid]);
		} else {
			$loop = explode("|", $previewLink);
			$loop = array_notnull($loop);
		}

		if ($loop[0] && $loop[0] != "") {
			$attr_width = "width='100'";
			//$attr_height = "height='$height'";
			$attr_style = "style='border:1px solid #dedede'";

			$img = "<img src='$loop[0]' $attr_width $attr_height $attr_style onerror='this.src=\"/data/noimg.png\"'/>";

			//편집정보 미리보기 이미지
			$data['ord'][$ord[ordno]]['item'][$item[ordseq]][img] = $img;
		}

		###상품명 링크###
		$optionData = json_decode($item[est_order_data], true);
		//$optionData = orderJsonParse2($val[est_order_data]);

		$link = getViewLinkWithTemplate($item[goodsno], $optionData[templateSetIdx], $optionData[templateIdx]);

		//상품명 링크
		$data['ord'][$ord[ordno]]['item'][$item[ordseq]][link] = $link;

		$data['ord'][$ord[ordno]]['item'][$item[ordseq]][set_emoney] = calcuOrderTotalEmonay($item['payprice'], $item['saleprice']);
		  
		$data['ord'][$ord[ordno]]['item'][$item[ordseq]][return_msg] = $data['return_msg'];
		
	}
}

//환불 총금액 조회
$data[refund_total_price] = 0;
$addWhere3 = "and a.payno='$data[payno]' and a.state='1'";
$refund_data = $m_order->getRefundData($cid, $addWhere3);

if (count($refund_data) > 0) {
	foreach ($refund_data as $refund_k=>$refund_v) {
		$data[refund_total_price] = $data[refund_total_price] + $refund_v[cash] + $refund_v[pg] + $refund_v[emoney] + $refund_v[custom];
	}
}

$cashInfo = $m_cash->getInfo($cid, $payno);
switch ($cfg[pg][module]){
   case "kcp":
      $data[receipt_url] = "http://admin.kcp.co.kr/Modules/Sale/Card/ADSA_CARD_BILL_Receipt.jsp?c_trade_no=".$data[pgcode];
      break;
   case "inicis":
   case "inipaystdweb":
      $data[receipt_url] = "https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid=".$data[pgcode]."&noMethod=1";
      break;
   case "smartxpay" :
      //$data[receipt_url] = "smartxpay";
      $authcode = md5($cfg[pg][lgd_mid].$data[pgcode].$cfg[pg][lgd_mertkey]);
      $tpl->assign('authcode', $authcode);
      break;
}
//debug($data);
$cfg[isMobile] = isMobile();
$tpl->assign("cash_status", $cashInfo[status]);
$tpl->assign("pg_module", $cfg[pg][module]);
$tpl->assign($data);
$tpl->print_('tpl');
?>