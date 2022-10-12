<?
/*
 * 명세서 / 견적서
 * 주문리스트에서 호출 가능 payno 를 받아서 처리
 * parameter
 * -payno : 주문번호
 * return value
 * -html 형식의 거래명세서 내용
*/

include "../lib/library.php";
include "../lib/class.cart.php";

$m_goods = new M_goods();
$m_order = new M_order();

try {
	# cookie check
	//if (!$_COOKIE[cartkey] && !$sess[mid] && !$sess_admin[mid]) 
	//	throw new Exception("Error cookie", 1);
	
	# payno check
	if (!$_REQUEST[payno]) 
		throw new Exception("Error Parameter [payno]", 1);
	
	# payno,paystep check
	$paystepArr = array("2", "3", "4", "5", "92");
	$data = $m_order->getPayInfo($_REQUEST[payno]);
	
	if (!$data[payno])
		throw new Exception("Error Payno [not payno]", 1);
	
	if (!in_array($data[paystep], $paystepArr))
		throw new Exception("Error Paystep [not complete payment]", 1);

	# 견적서 관리 정보
	if ($cfg[bill_vat_yn] == "") $cfg[bill_vat_yn] = "1";
	if ($cfg[bill_nameComp] == "") $cfg[bill_nameComp] = $cfg[nameComp];
	if ($cfg[bill_typeBiz] == "") $cfg[bill_typeBiz] = $cfg[typeBiz];
	if ($cfg[bill_itemBiz] == "") $cfg[bill_itemBiz] = $cfg[itemBiz];
	if ($cfg[bill_regnumBiz] == "") $cfg[bill_regnumBiz] = $cfg[regnumBiz];
	if ($cfg[bill_nameCeo] == "") $cfg[bill_nameCeo] = $cfg[nameCeo];
	if ($cfg[bill_address] == "") $cfg[bill_address] = $cfg[address];
	if ($cfg[bill_phoneComp] == "") $cfg[bill_phoneComp] = $cfg[phoneComp];
	if ($cfg[bill_faxComp] == "") $cfg[bill_faxComp] = $cfg[faxComp];
	
	# 가격 확인
	$t_supply_shipprice = 0;
	$t_tax_shipprice = 0;
	$t_shipprice = 0;
	$t_supply_dcprice = 0;
	$t_tax_dcprice = 0;
	$t_dcprice = 0;
	$t_supply_emoneyprice = 0;
	$t_tax_emoneyprice = 0;
	$t_emoneyprice = 0;
	$t_supply_price = 0;
	$t_tax_price = 0;
	$t_price = 0;
	
	# 주문 정보 조회
	$ord = $m_order->getOrdList($data[payno]);
	
	foreach ($ord as $k=>$v) {
		$data['ord'][$v[ordno]] = $ord;
		$ord2 = $m_order->getOrderViewList($data[payno], $v[ordno]);
		$item = array();
		
		foreach ($ord2 as $item) {
			if ($item[est_order_option_desc]) $item[est_order_option_desc_str] = str_replace(",", "<br>", $item[est_order_option_desc]);
			if ($item[addopt]) $item[addopt] = unserialize($item[addopt]);
			if ($item[printopt]) $item[printopt] = unserialize($item[printopt]);
			
			$data['ord'][$v[ordno]]['item'][$item[ordseq]] = $item;
		}
	}
	
	//배송비
	$t_shipprice = $data[shipprice];
	
	//할인금액
	$t_dcprice = ($data[dc_member] + $data[dc_coupon]);
	
	//포인트사용
	$t_emoneyprice = $data[dc_emoney];
	
	//합계
	$t_price = $data[payprice];
	
	//부가세여부
	if ($cfg[bill_vat_yn]) {
		$vat_yn = _("VAT포함");
		
		$t_supply_shipprice = ($t_shipprice / 1.1);
		$t_tax_shipprice = ($t_supply_shipprice * 0.1);
		$t_supply_dcprice = ($t_dcprice / 1.1);
		$t_tax_dcprice = ($t_supply_dcprice * 0.1);
		$t_supply_emoneyprice = ($t_emoneyprice / 1.1);
		$t_tax_emoneyprice = ($t_supply_emoneyprice * 0.1);
		$t_supply_price = ($t_price / 1.1);
		$t_tax_price = ($t_supply_price * 0.1);
	} else {
		$vat_yn = _("VAT별도");
		
		$t_supply_shipprice = $t_shipprice;
		$t_tax_shipprice = ($t_supply_shipprice * 0.1);
		$t_supply_dcprice = $t_dcprice;
		$t_tax_dcprice = ($t_supply_dcprice * 0.1);
		$t_supply_emoneyprice = $t_emoneyprice;
		$t_tax_emoneyprice = ($t_supply_emoneyprice * 0.1);
		$t_supply_price = $t_price;
		$t_tax_price = ($t_supply_price * 0.1);
	}
	
	# 명세서/견적서 폼 생성
	if ($cfg[skin_theme] == "P1") {
		$html = "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ko\">
		<head>
		<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
		<script type=\"text/javascript\" src=\"../js/jquery-1.9.1.min.js\"></script>
		</head>
		<body>
		<div id=\"bill\">
		<div style=\"font-size:30px; width:150px; margin:auto; height:60px;\"> "._("거래명세서")." </div>
		<div style=\"width:700px; margin:auto; margin-bottom:10px;\">
			<div style=\"width:200px; height:194px; text-align:center; float:left; border:1px solid #ddd; font-size:14px;\">
				<div style=\"margin:60px auto;\">
					<span style=\"display:block;\">"._("주문번호")." : ".$data[payno]."</span>
					<span style=\"display:block;\">"._("일자")." : ".date("Y-m-d")."</span>
					<span style=\"display:block; margin-top:10px;\">".$data[orderer_name]." "._("귀하")."</span>
				</div>
			</div>
			<div style=\"width:30px;height:194px; text-align:center; float:left; border-top:1px solid #ddd; border-bottom:1px solid #ddd; margin:auto;\">
				<span style=\"position:relative; top:60px;\">"._("공 급 자")."</span>
			</div>
			<table style=\"border-collapse:collapse;\">
				<tr>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:32px; font-size:13px;\">"._("등록번호")."</th>
					<td colspan=\"3\" style=\"border:1px solid #ddd;width:360px; text-align:center; font-size:13px;\">".$cfg[bill_regnumBiz]."</td>
				</tr>
				<tr>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:32px; font-size:13px;\">"._("상호")."</th>
					<td style=\"border:1px solid #ddd;width:360px; text-align:center; font-size:13px;\">".$cfg[bill_nameComp]."</td>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:32px; font-size:13px;\">"._("성명")."</th>
					<td style=\"border:1px solid #ddd;width:360px; text-align:center; font-size:13px; height:52px;\">
						<span style=\"position:relative; top:17px;\">".$cfg[bill_nameCeo]."</span>";
						
						if (is_file("../data/bill/".$cid."/bill_seal.png")) {
							$html .= "<img src=\"http://".$_SERVER[HTTP_HOST]."/data/bill/".$cid."/bill_seal.png\" style=\"float:right;\">";
						}
						
					$html .= "</td>
				</tr>
				<tr>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:32px; font-size:13px;\">"._("주소")."</th>
					<td colspan=\"3\" style=\"border:1px solid #ddd;width:360px; text-align:center; font-size:13px;\">".$cfg[bill_address]."</td>
				</tr>
				<tr>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:32px; font-size:13px;\">"._("업태")."</th>
					<td style=\"border:1px solid #ddd;width:360px; text-align:center; font-size:13px;\">".$cfg[bill_typeBiz]."</td>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:32px; font-size:13px;\">"._("업종")."</th>
					<td style=\"border:1px solid #ddd;width:360px; text-align:center; font-size:13px;\">".$cfg[bill_itemBiz]."</td>
				</tr>
				<tr>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:32px; font-size:13px;\">"._("전화")."</th>
					<td style=\"border:1px solid #ddd;width:360px; text-align:center; font-size:13px;\">".$cfg[bill_phoneComp]."</td>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:32px; font-size:13px;\">"._("팩스")."</th>
					<td style=\"border:1px solid #ddd;width:360px; text-align:center; font-size:13px;\">".$cfg[bill_faxComp]."</td>
				</tr>
			</table>
			<table style=\"border-collapse:collapse; margin-top:10px;\">
				<tr>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:35px; font-size:13px;\">"._("거 래 금 액")."<br>(".$vat_yn.")</th>
					<td style=\"text-align:right; padding:20px;border:1px solid #ddd;width:360px; font-size:13px;\">&#8361;".number_format($t_price)." "._("원정")."</td>
				</tr>
			</table>
			<table style=\"border-collapse:collapse; margin-top:10px;\">
				<tr>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:35px; font-size:13px;\">"._("번호")."</th>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:35px; font-size:13px;\">"._("품명")."</th>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:35px; font-size:13px;\">"._("수량")."</th>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:35px; font-size:13px;\">"._("단가")."</th>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:35px; font-size:13px;\">"._("금액")."</th>
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:35px; font-size:13px;\">"._("비고")."</th>
				</tr>";
				
				$i = 0;
				
				foreach ($data[ord] as $k=>$v) {
					foreach ($v[item] as $k2=>$v2) {
						//상품명
						$goodsnm = "<b>".$v2[goodsnm]."</b>";
						
						//카테고리 정보 조회
						$categoryArr = $m_goods->getGoodsCategoryInfo($cid, $v2[goodsno]);
						foreach ($categoryArr as $cat_k=>$cat_v) {
							switch (strlen($cat_v[catno])) {
								case "3":
									$v2[catno1] = $cat_v[catno];
									$v2[catnm1] = $cat_v[catnm];
									break;
								case "6":
									$v2[catnm2] = $cat_v[catnm];
									break;
								case "9":
									$v2[catnm3] = $cat_v[catnm];
									break;
								case "12":
									$v2[catnm4] = $cat_v[catnm];
									break;
							}
						}
						
						//페이지수 정보 조회
						if ($v2[storageid]) {
							$podsApi = new PODStation('20');
							$ret = $podsApi->GetMultiOrderInfoResultAllData($v2[storageid]);
							
							if ($ret[DATA]) {
								$ret[DATA] = str_replace("[", "", $ret[DATA]);
								$ret[DATA] = str_replace("]", "", $ret[DATA]);
								$ret[DATA] = explode(",", $ret[DATA]);
								
								if (is_array($ret[DATA])) {
									foreach ($ret[DATA] as $ret_k=>$ret_v) {
										$ret_v = explode("=", $ret_v);
										
										if ($ret_v[0] == "pagecount")
											$pagecount = $ret_v[1];
										if ($ret_v[0] == "editorbase")
											$basecount = $ret_v[1];
										if ($ret_v[0] == "inc")
											$inc = $ret_v[1];
										if ($ret_v[0] == "per")
											$per = $ret_v[1];
										if ($ret_v[0] == "totalcount")
											$totalcount = $ret_v[1];
									}
								}
								
								if (is_numeric($totalcount)) $v2[totalcount] = $totalcount;
							}
						}
						
						if ($v2[catno1] == "002" || $v2[catno1] == "014") {
							if ($v2[opt] || $v2[totalcount]) {
								$opt = "";
								
								if ($v2[opt]) $opt .= $v2[opt];
								
								if ($v2[totalcount]) {
									if ($opt) $opt .= " / ".$v2[totalcount]."p";
									else $opt .= $v2[totalcount]."p";
								}
								
								$goodsnm .= "<div style=\"margin-top:5px;\">".$opt."</div>";
							}
							
							if ($v2[addopt]) {
								$addopt = "";
								
								foreach ($v2[addopt] as $k3=>$v3) {
									$addopt .= $v3[addopt_bundle_name].":".$v3[addoptnm]." / ";
								}
								
								if ($addopt) $addopt = substr($addopt, 0, -3);
								
								$goodsnm .= "<div style=\"margin-top:5px;\">".$addopt."</div>";
							}
						} else if ($v2[catno1] == "006") {
							if ($v2[printopt]) {
								$goodsnm .= "<div style=\"margin-top:5px;\">";
								
								foreach ($v2[printopt] as $k3=>$v3) {
									$goodsnm .= "<div>"._("사이즈").":".$v3[printoptnm]." / "._("주문수량").":".$v3[ea]._("매")."</div>";
								}
								
								$goodsnm .= "</div>";
							}
						} else if ($v2[catno1] == "016" || $v2[catno1] == "019" || $v2[catno1] == "023" || $v2[catno1] == "020") {
							if ($v2[opt]) {
								$goodsnm .= "<div style=\"margin-top:5px;\">".$v2[opt]."</div>";
							}
							
							if ($v2[addopt]) {
								$addopt = "";
								
								foreach ($v2[addopt] as $k3=>$v3) {
									$addopt .= $v3[addopt_bundle_name].":".$v3[addoptnm]." / ";
								}
								
								if ($addopt) $addopt = substr($addopt, 0, -3);
								
								$goodsnm .= "<div style=\"margin-top:5px;\">".$addopt."</div>";
							}
						} else if ($v2[catno1] == "018") {
							if ($v2[catnm2]) {
								$goodsnm .= "<div style=\"margin-top:5px;\">".$v2[catnm2]."</div>";
							}
						} else if ($v2[catno1] == "025") {
							
						} else {
							if ($v2[opt] || $v2[totalcount]) {
								$opt = "";
								
								if ($v2[opt]) $opt .= $v2[opt];
								
								if ($v2[totalcount]) {
									if ($opt) $opt .= " / ".$v2[totalcount]."p";
									else $opt .= $v2[totalcount]."p";
								}
								
								$goodsnm .= "<div style=\"margin-top:5px;\">".$opt."</div>";
							}
							
							if ($v2[addopt]) {
								$addopt = "";
								
								foreach ($v2[addopt] as $k3=>$v3) {
									$addopt .= $v3[addopt_bundle_name].":".$v3[addoptnm]." / ";
								}
								
								if ($addopt) $addopt = substr($addopt, 0, -3);
								
								$goodsnm .= "<div style=\"margin-top:5px;\">".$addopt."</div>";
							}
							
							if ($v2[printopt]) {
								$goodsnm .= "<div style=\"margin-top:5px;\">";
								
								foreach ($v2[printopt] as $k3=>$v3) {
									$goodsnm .= "<div>"._("사이즈").":".$v3[printoptnm]." / "._("주문수량").":".$v3[ea]._("매")."</div>";
								}
								
								$goodsnm .= "</div>";
							}
						}						
						
						//수량
						$ea = $v2[ea];
						
						//판매가
						$pay_price = $v2[payprice];
						
						//공급가(=판매가)
						$supply_price = $pay_price;
						
						//세액(공급가 * 0.1)
						$tax_price = ($supply_price * 0.1);
						
						//합계금액(공급가 + 세액)
						$total_price = ($supply_price + $tax_price);
						
						//부가세포함
						if ($cfg[bill_vat_yn]) {
							$supply_price = ($supply_price / 1.1);
							$tax_price = ($supply_price * 0.1);
							$total_price = ($supply_price + $tax_price);
						}
						
						$i++;
						
						$html .= "<tr>
							<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".$i."</td>
							<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">".$goodsnm."</td>
							<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".$ea."</td>
							<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($pay_price / $ea)."</td>
							<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($total_price)."</td>
							<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
						</tr>";
					}
				}
				
				$html .= "<tr>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
					<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("배송비")."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_shipprice)."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_supply_shipprice + $t_tax_shipprice)."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
				</tr>
				<tr>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
					<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("포인트사용")."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_emoneyprice)."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_supply_emoneyprice + $t_tax_emoneyprice)."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
				</tr>";
				
			$html .= "</table>
			<table style=\"border-collapse:collapse; margin-top:10px;\">
				<tr style=\"border-top:2px solid #999\">
					<td style=\"width:70%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("총금액")."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_supply_price + $t_tax_price)."</td>
				</tr>
				<tr>
					<td style=\"width:70%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("공급가액")."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_supply_price)."</td>
				</tr>
				<tr>
					<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("부가가치세")."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_tax_price)."</td>
				</tr>
			</table>
		</div>
		</div>
		</body>
		</html>";
	} else {
		$html = "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ko\">
		<head>
		<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
		<script type=\"text/javascript\" src=\"../js/jquery-1.9.1.min.js\"></script>
		</head>
		<body style=\"margin:0; padding:0; font-size:12px; line-height:150%; color:#4d4d4d; font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
		<div id=\"bill\">
		<div id=\"pop\" style=\"margin:0;padding:0;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;position:relative;\">
		<div class=\"estimate\" style=\"margin:0;padding:10px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
			<h1 style=\"margin:0;padding:0 0 30px;font-size:40px;line-height:60px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;position:relative;height:33px;font-weight:bold;\">"._("거래명세서")."</h1>
			<table style=\"border-collapse:collapse; width:100%;\">
				<tr>
					<td style=\"margin:0;padding:0 10px 0 0;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
						<table class=\"date non\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
							<thead>
							<tr>
								<th colspan=\"4\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;\">"._("공급 받는자")."</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<th width=\"80\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("주문번호")."</th>
								<td width=\"130\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif; border-bottom:1px solid #000000;\">".$data[payno]."</td>
							</tr>
							<tr>
								<th width=\"80\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("사업자번호")."</th>
								<td width=\"130\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif; border-bottom:1px solid #000000;\"></td>
							</tr>
							<tr>
								<th width=\"80\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("상호(법인)명")."</th>
								<td width=\"130\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif; border-bottom:1px solid #000000;\"></td>
							</tr>
							<tr>
								<th width=\"80\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("성명")."</th>
								<td width=\"130\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif; border-bottom:1px solid #000000;\"></td>
							</tr>
							<tr>
								<th width=\"80\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("사업자주소")."</th>
								<td width=\"130\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif; border-bottom:1px solid #000000;\"></td>
							</tr>
							<tr>
								<th width=\"80\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("연락처")."</th>
								<td width=\"130\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif; border-bottom:1px solid #000000;\"></td>
							</tr>
							</tbody>
						</table>
					</td>
					<td style=\"margin:0;padding:0;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
						<table class=\"date supplier\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
							<thead>
							<tr>
								<th colspan=\"5\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;\">"._("공급 하는자")."</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("사업자번호")."</th>
								<td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_regnumBiz]."</td>
								<td width=\"60\" rowspan=\"3\">
									<div style=\"border:1px solid #E6E6E6;position:relative;width:60px;height:60px;\">";
									
									if (is_file("../data/bill/".$cid."/bill_seal.png")) {
										$html .= "<img src=\"http://".$_SERVER[HTTP_HOST]."/data/bill/".$cid."/bill_seal.png\" alt=\"\" style=\"position:absolute;top:0;right:0;bottom:0;left:0;margin:auto;max-width:100%;max-height:100%;\" />";
									}
									
					  				$html .= 
					  				"</div>
					  			</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("상호")."</th>
								<td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_nameComp]."</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("대표자명")."</th>
								<td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_nameCeo]."</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("사업의종류")."</th>
								<td colspan=\"4\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">| "._("업태")."_".$cfg[bill_typeBiz]." | "._("종목")."_".$cfg[bill_itemBiz]." |</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("주소")."</th>
								<td colspan=\"4\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_address]."</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("전화번호")."</th>
								<td colspan=\"4\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_phoneComp]." ( Fax ".$cfg[bill_faxComp]." )</td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</table>";
	
			$html .= "
			<div class=\"title\" style=\"margin:30px 0 15px;padding:15px 5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;overflow:hidden;text-align:right;background:#f2f2f2;\">
				<div class=\"tit\" style=\"margin:0;padding:0;font-size:20px;line-height:30px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;float:left;font-weight:bold;\">"._("거래금액")."<span style=\"font-size:15px;\">(".$vat_yn.")</span></div>
		 		<div class=\"won\" style=\"margin:0;padding:0;font-size:20px;line-height:30px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;\">&#8361;".number_format($t_price)."</div>
		 	</div>";
		 
			$html .= "
			<table class=\"date\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
		 		<thead>
					<tr>
						<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;width:7%;text-align:center;\">"._("순번")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;width:10%;text-align:center;\">"._("상품코드")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;width:36%;text-align:center;\">"._("품명")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;width:7%;text-align:center;\">"._("수량")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;width:10%;text-align:center;\">"._("판매가")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;width:10%;text-align:center;\">"._("공급가")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;width:10%;text-align:center;\">"._("세액")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;width:10%;text-align:center;\">"._("합계금액")."</th>
					</tr>
					</thead>
					<tbody>";
					
			$i = 0;
			
			foreach ($data[ord] as $k=>$v) {
				foreach ($v[item] as $k2=>$v2) {
					//상품명
					$goodsnm = "<b>".$v2[goodsnm]."</b>";
					
					if ($v2[title]) {
						$goodsnm .= "<div> - ".$v2[title]."</div>";
					}
					
					if ($v2[addpage]) {
						$goodsnm .= "<div style=\"margin-top:5px;\">"._("페이지 추가")." : ".$v2[addpage]."p</div>";
					}
					
					if ($v2[opt]) {
						$goodsnm .= "<div style=\"margin-top:5px;\">".$v2[opt]."</div>";
					}
					
					if ($v2[addopt]) {
						$goodsnm .= "<div style=\"margin-top:5px;\">";
						
						foreach ($v2[addopt] as $k3=>$v3) {
							$goodsnm .= "<div>".$v3[addopt_bundle_name]." : ".$v3[addoptnm]."</div>";
						}
						
						$goodsnm .= "</div>";
					}
					
					if ($v2[printopt]) {
						$goodsnm .= "<div style=\"margin-top:5px;\">";
						
						foreach ($v2[printopt] as $k3=>$v3) {
							$goodsnm .= "<div>".$v3[printoptnm]." : ".$v3[ea]."</div>";
						}
						
						$goodsnm .= "</div>";
					}
					
					//수량
					$ea = $v2[ea];
					
					//판매가
					$pay_price = $v2[payprice];
					
					//공급가(=판매가)
					$supply_price = $pay_price;
					
					//세액(공급가 * 0.1)
					$tax_price = ($supply_price * 0.1);
					
					//합계금액(공급가 + 세액)
					$total_price = ($supply_price + $tax_price);
					
					//부가세포함
					if ($cfg[bill_vat_yn]) {
						$supply_price = ($supply_price / 1.1);
						$tax_price = ($supply_price * 0.1);
						$total_price = ($supply_price + $tax_price);
					}
					
					$i++;
					
					$html .= " 
				 	<tr>
						<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;width:7%;text-align:center;\">".$i."</td>
						<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;width:10%;text-align:center;\">".$v2[goodsno]."</td>
						<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;width:36%;text-align:left;\">".$goodsnm."</td>
						<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;width:7%;text-align:center;\">".$ea."</td>
						<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;width:10%;text-align:right;\">".number_format($pay_price)."</td>
						<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;width:10%;text-align:right;\">".number_format($supply_price)."</td>
						<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;width:10%;text-align:right;\">".number_format($tax_price)."</td>
						<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;width:10%;text-align:right;\">".number_format($total_price)."</td>
					</tr>";
				}
			}
		
			$html .= "
			</tbody>
			<tfoot>
			<tr>
				<td colspan=\"7\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;width:90%;text-align:center;\">"._("배송비")."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_shipprice)."</td>
			</tr>
			<tr>
				<td colspan=\"7\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;width:90%;text-align:center;\">"._("할인금액")."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_dcprice)."</td>
			</tr>
			<tr>
				<td colspan=\"7\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;width:90%;text-align:center;\">"._("포인트사용")."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_emoneyprice)."</td>
			</tr>
			<tr>
				<td colspan=\"7\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;width:90%;text-align:center;\">"._("합계")."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_price)."</td>
			</tr>
			</tfoot>
		</table>
		<table class=\"date etc\" style=\"border-collapse:collapse;width:100%;margin-top:10px;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
			<tbody>
				<tr style=\"border-bottom:1px solid #000000;\">
					<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;width:15%;text-align:center;\">"._("요구사항")."</td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\"></td>
				</tr>
				<tr style=\"border-bottom:1px solid #000000;\">
					<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;width:15%;text-align:center;\">"._("인수자")."</td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">"._("(인)")."</td>
				</tr>
			</tbody>
		</table>
		</div>
		</div>
		</div>
		</body>
		</html>";
	}
	
	echo($html);	
} catch (Exception $e) {
    echo 'Fail: ',  $e->getMessage(), "\n";
}

?>
