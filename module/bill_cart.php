<?
/*
 * 20190124 / minks / 주문번호로 견적서 조회 추가
*/
/*
 * 20180622 / minks / 배송비 추가
*/

/*
 * 명세서 / 견적서
 * 장바구니에서 호출 가능 cartno 를 받아서 처리 (복수 ","로 구분)
 * parameter
 * -cartno : 장바구니번호 (여러개일 경우 ","로 묶음)
 * return value
 * -html 형식의 견적서 내용
*/

include "../lib/library.php";
include "../lib/class.cart.php";

$m_goods = new M_goods();
$m_member = new M_member();
$m_order = new M_order();
$m_cart = new Cart();

try {
	# cookie check
	//if (!$_COOKIE[cartkey] && !$sess[mid] && !$sess_admin[mid]) 
	//	throw new Exception("Error cookie", 1);
	
	# cartno check
	if (!$_REQUEST[cartno] && !$_REQUEST[payno]) 
		throw new Exception("Error Parameter [cartno or payno]", 1);

	# 견적서 관리 정보
	if ($cfg[bill_vat_yn] == "") $cfg[bill_vat_yn] = "1";
	if ($cfg[bill_nameComp] == "") $cfg[bill_nameComp] = $cfg[nameComp];
	if ($cfg[bill_typeBiz] == "") $cfg[bill_typeBiz] = $cfg[typeBiz];
	if ($cfg[bill_itemBiz] == "") $cfg[bill_itemBiz] = $cfg[itemBiz];
	if ($cfg[bill_regnumBiz] == "") $cfg[bill_regnumBiz] = $cfg[regnumBiz];
	if ($cfg[bill_nameCeo] == "") $cfg[bill_nameCeo] = $cfg[nameCeo];
	if ($cfg[bill_managerInfo] == "") $cfg[bill_managerInfo] = $cfg[managerInfo];
	if ($cfg[bill_address] == "") $cfg[bill_address] = $cfg[address];
	if ($cfg[bill_phoneComp] == "") $cfg[bill_phoneComp] = $cfg[phoneComp];
	if ($cfg[bill_faxComp] == "") $cfg[bill_faxComp] = $cfg[faxComp];

	# 절사 정보 (부가세 미포함시 처리)
	$cutmoney_cfg = $m_cart->setCuttingCfg();
	$cutmoney_use = $cutmoney_cfg[c_use][value];    	//절사 여부 (1:사용 2:미사용)
	$cutmoney_type = $cutmoney_cfg[c_type][value]; 	 	//절사금액 (1:1의자리 ,2:10의자리, 3:100의자리)
	$cutmoney_op = $cutmoney_cfg[c_op][value];    	 	//절사방식 (F:버림처리,C:올림처리,R:반올림처리)
	
	# 가격 확인
	$t_ea = 0;
	$t_supply_price = 0;
	$t_tax_price = 0;
	$t_price = 0;
	$t_supply_shipprice = 0;
	$t_tax_shipprice = 0;
	$t_shipprice = 0;
	$t_supply_dcprice = 0;
	$t_tax_dcprice = 0;
	$t_dcprice = 0;
	$t_supply_emoneyprice = 0;
	$t_tax_emoneyprice = 0;
	$t_emoneyprice = 0;
	$t_supply_goods_price = 0;
	$t_supply_ship_price = 0;
	$t_sum_supply_price = 0;
	$t_sum_tax_price = 0;
	
	# 회원 정보 조회
	$member = $m_member->getInfo($cid, $sess[mid]);
	
	// 장바구니 > 견적서
	if ($_REQUEST[cartno]) {
		# cartno 배열화
		$cartno = explode(",", $_REQUEST[cartno]);
		$cart = new Cart($cartno);
		
		# rid 체크
		$ridArr = array();
		
		if ($_REQUEST[name]) $member[name] = $_REQUEST[name];
		
		foreach ($cart->item as $k=>$v) {
			foreach ($v as $k2=>$v2) {		
				if ($v2[package_flag] == "2" && $v2[package_parent_cartno] != "0") {
					$ea = "";
					$price = 0;
					$supply_price = 0;
					$tax_price = 0;
				} else {
					//수량
					$ea = $v2[ea];
					
					//단가
					$price = ($v2[price] + $v2[aprice] + $v2[addopt_aprice] + $v2[print_aprice] + $v2[addpage_price]);
					
					if ($v2[grpdc]) {
						$price = ($price - $v2[grpdc]);
					}
					
					//공급가액(단가 * 수량)
					$supply_price = ($price * $ea);
					
					//세액
					$tax_price = ($supply_price * 0.1);
				
					$supply_price = round($supply_price);
					$tax_price = round($tax_price);
					
					//합계수량
					$t_ea += $ea;
					
					//합계공급가액
					$t_supply_price = $v2[payprice];
					//합계세액
					$t_tax_price = $v2[payprice]*0.1;
					
					//부가세 미포함시 세액 절사처리
					if(!$cfg[bill_vat_yn] && $cutmoney_use==1){
						
						$pow=pow(10,(int)$cutmoney_type);

						switch($cutmoney_op){
							case "F" :
								$t_tax_price = (int)(floor( (int)$t_tax_price  / $pow) * $pow); 
							   	break;
							case "C" :
								$t_tax_price = (int)(ceil( (int)$t_tax_price  / $pow) * $pow); 
							   	break;
							case "R" :
								$t_tax_price = (int)(round( (int)$t_tax_price / $pow ) * $pow); 
							   	break;
							default : 
								$t_tax_price = $t_tax_price;
							   	break;
						}
					}

					//부가세포함
					if ($cfg[bill_vat_yn]) {
						$supply_price = ($supply_price / 1.1);
						$tax_price = ($supply_price * 0.1);

						$t_supply_price = ($t_supply_price / 1.1);
						$t_tax_price = ($t_supply_price * 0.1);
					}

					$t_sum_supply_price += $t_supply_price;
					$t_sum_tax_price += $t_tax_price;

					//상품 견적금액(공급가액 단가+세액) = 합계공급가액 + 합계세액
					$t_supply_goods_price += $t_supply_price+$t_tax_price;

					//rid
					if (!in_array($v2[rid], $ridArr)) $ridArr[] = $v2[rid];
				}
			}
		}

		//배송비
		foreach ($ridArr as $k=>$v) {
			$t_shipprice += $cart->shipprice[$v];
		}
		
		if ($t_shipprice > 0) {
			$t_supply_shipprice = $t_shipprice;
			$t_tax_shipprice = ($t_supply_shipprice * 0.1);
			
			if ($cfg[bill_vat_yn]) {
				$t_supply_shipprice = ($t_supply_shipprice / 1.1);
				$t_tax_shipprice = ($t_supply_shipprice * 0.1);
			}
			
			$t_supply_shipprice = round($t_supply_shipprice);
			$t_tax_shipprice = round($t_tax_shipprice);
			$t_shipprice = ($t_supply_shipprice + $t_tax_shipprice);
			
			// $t_supply_price += $t_supply_shipprice;
			// $t_tax_price += $t_tax_shipprice;
			$t_sum_supply_price += $t_supply_shipprice;
			$t_sum_tax_price += $t_tax_shipprice;
			// 배송비 견적금액(공급가액 단가+세액)
			$t_supply_ship_price +=  $t_supply_shipprice + $t_tax_shipprice;
			
		}
		
		//총 견적금액(상품금액 + 배송금액)
		$t_price = ($t_supply_goods_price + $t_supply_ship_price);

	// 주문조회 > 견적서
	} else if ($_REQUEST[payno]) {
		$data = $m_order->getPayInfo($_REQUEST[payno]);		
		
		if (!$data[payno])
			throw new Exception("Error Payno [not payno]", 1);
		
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
				
				//합계수량
				$t_ea += $item[ea];
				
				$data['ord'][$v[ordno]]['item'][$item[ordseq]] = $item;
			}
		}
		
		//배송비
		$t_supply_shipprice = $data[shipprice];
		$t_tax_shipprice = ($t_supply_shipprice * 0.1);
		
		//할인금액
		$t_supply_dcprice = ($data[dc_member] + $data[dc_coupon]);
		$t_tax_dcprice = ($t_supply_dcprice * 0.1);
		
		//포인트사용
		$t_supply_emoneyprice = $data[dc_emoney];
		$t_tax_emoneyprice = ($t_supply_emoneyprice * 0.1);
		
		//합계
		$t_supply_price = $data[payprice];
		$t_tax_price = ($t_supply_price * 0.1);

		//부가세 미포함시 세액 절사처리
		if(!$cfg[bill_vat_yn] && $cutmoney_use==1){
									
			$pow=pow(10,(int)$cutmoney_type);

			switch($cutmoney_op){
				case "F" :
					$t_tax_price = (int)(floor( (int)$t_tax_price  / $pow) * $pow); 
					break;
				case "C" :
					$t_tax_price = (int)(ceil( (int)$t_tax_price  / $pow) * $pow); 
					break;
				case "R" :
					$t_tax_price = (int)(round( (int)$t_tax_price / $pow ) * $pow); 
					break;
				default : 
					$t_tax_price = $t_tax_price;
					break;
			}
		}

		if ($cfg[bill_vat_yn]) {
			$t_supply_shipprice = ($t_supply_shipprice / 1.1);
			$t_tax_shipprice = ($t_supply_shipprice * 0.1);
			
			$t_supply_dcprice = ($t_supply_dcprice / 1.1);
			$t_tax_dcprice = ($t_supply_dcprice * 0.1);
			
			$t_supply_emoneyprice = ($t_supply_emoneyprice / 1.1);
			$t_tax_emoneyprice = ($t_supply_emoneyprice * 0.1);
			
			$t_supply_price = ($t_supply_price / 1.1);
			$t_tax_price = ($t_supply_price * 0.1);
		}
		
		$t_supply_shipprice = round($t_supply_shipprice);
		$t_tax_shipprice = round($t_tax_shipprice);
		$t_shipprice = ($t_supply_shipprice + $t_tax_shipprice);
		
		$t_supply_dcprice = round($t_supply_dcprice);
		$t_tax_dcprice = round($t_tax_dcprice);
		$t_dcprice = ($t_supply_dcprice + $t_tax_dcprice);
		
		$t_supply_emoneyprice = round($t_supply_emoneyprice);
		$t_tax_emoneyprice = round($t_tax_emoneyprice);
		$t_emoneyprice = ($t_supply_emoneyprice + $t_tax_emoneyprice);
		
		$t_supply_price = round($t_supply_price);
		$t_tax_price = round($t_tax_price);
		$t_price = ($t_supply_price + $t_tax_price);
		
	}


	//nowdt, billno 생성
	$nowdt = date("Y-m-d H:i:s");
	$billno = date("Y-m-d-His", strtotime($nowdt))."-".rand(1000,9999);
	
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
		<div style=\"font-size:30px; width:150px; margin:auto; height:60px;\"> "._("견 적 서")." </div>
		<div style=\"width:700px; margin:auto; margin-bottom:10px;\">
			<div style=\"width:200px; height:194px; text-align:center; float:left; border:1px solid #ddd; font-size:14px;\">
				<div style=\"margin:60px auto;\">
					<span style=\"display:block;\">"._("견적번호")." : ".$billno."</span>
					<span style=\"display:block;\">"._("일자")." : ".date("Y-m-d")."</span>
					<span style=\"display:block; margin-top:10px;\">".$member[name]." "._("귀하")."</span>
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
					<th style=\"border:1px solid #ddd;width:300px; text-align:center; height:35px; font-size:13px;\">"._("견 적 금 액")."<br>"._("(공급가액+세액)")."</th>
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
				
				if ($_REQUEST[cartno]) {
					foreach ($cart->item as $k=>$v) {
						foreach ($v as $k2=>$v2) {
							//상품명
							if ($v2[package_flag] == "2") {
								$goodsnm = "<b>["._("패키지")."]".$v2[goodsnm]."</b>";
							} else {
								$goodsnm = "<b>".$v2[goodsnm]."</b>";
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
							
							if ($v2[package_flag] == "2" && $v2[package_parent_cartno] != "0") {
								$v_ea = "";
								$v_price = 0;
								$v_supply_price = 0;
								$v_tax_price = 0;
							} else {
								//수량
								$v_ea = $v2[ea];
								
								//단가
								$v_price = ($v2[price] + $v2[aprice] + $v2[addopt_aprice] + $v2[print_aprice] + $v2[addpage_price]);
								
								if ($v2[grpdc]) {
									$v_price = ($v_price - $v2[grpdc]);
								}
								
								/*
								//공급가액(단가 * 수량)
								$v_supply_price = ($v_price * $v_ea);
								
								//세액
								$v_tax_price = ($v_supply_price * 0.1);
								*/
								$v_supply_price = $v2[payprice];
								$v_tax_price = ($v_supply_price * 0.1);
								
								//부가세포함
								if ($cfg[bill_vat_yn]) {
									$v_supply_price = ($v_supply_price / 1.1);
									$v_tax_price = ($v_supply_price * 0.1);
								}

								//부가세 미포함시 세액 절사처리
								if(!$cfg[bill_vat_yn] && $cutmoney_use==1){
									
									$pow=pow(10,(int)$cutmoney_type);

									switch($cutmoney_op){
										case "F" :
											$v_tax_price = (int)(floor( (int)$v_tax_price  / $pow) * $pow); 
											break;
										case "C" :
											$v_tax_price = (int)(ceil( (int)$v_tax_price  / $pow) * $pow); 
											break;
										case "R" :
											$v_tax_price = (int)(round( (int)$v_tax_price / $pow ) * $pow); 
											break;
										default : 
											$v_tax_price = $v_tax_price;
											break;
									}
								}
							}
							
							$i++;
							
							$html .= "<tr>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".$i."</td>
								<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">".$goodsnm."</td>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".$v_ea."</td>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($v_price)."</td>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($v_supply_price+$v_tax_price)."</td>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
							</tr>";
						}
					}
				} else if ($_REQUEST[payno]) {
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
							$v_ea = $v2[ea];
							
							//단가(공급가액 / 수량)
							//$v_price = ($v2[payprice] / $v_ea);
							$v_price = $v2[goods_price];

							//공급가액(=판매가액)
							$v_supply_price = $v2[payprice];
							
							//세액
							$v_tax_price = ($v_supply_price * 0.1);
							
							//부가세포함
							if ($cfg[bill_vat_yn]) {
								$v_supply_price = ($v_supply_price / 1.1);
								$v_tax_price = ($v_supply_price * 0.1);
							}
							
							//부가세 미포함시 세액 절사처리
							if(!$cfg[bill_vat_yn] && $cutmoney_use==1){
									
								$pow=pow(10,(int)$cutmoney_type);

								switch($cutmoney_op){
									case "F" :
										$v_tax_price = (int)(floor( (int)$v_tax_price  / $pow) * $pow); 
										break;
									case "C" :
										$v_tax_price = (int)(ceil( (int)$v_tax_price  / $pow) * $pow); 
										break;
									case "R" :
										$v_tax_price = (int)(round( (int)$v_tax_price / $pow ) * $pow); 
										break;
									default : 
										$v_tax_price = $v_tax_price;
										break;
								}
							}
							
							
							$i++;
							
							$html .= "<tr>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".$i."</td>
								<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">".$goodsnm."</td>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".$v_ea."</td>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($v_price)."</td>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($v_supply_price + $v_tax_price)."</td>
								<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
							</tr>";
						}
					}
				}
				
				$html .= "<tr>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
					<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("배송비")."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".($cfg[bill_vat_yn] ? number_format($t_shipprice) : number_format($t_supply_shipprice))."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_supply_shipprice + $t_tax_shipprice)."</td>
					<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
				</tr>";
				
				if ($_REQUEST[payno]) {
					$html .= "<tr>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
						<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("포인트사용")."</td>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".($cfg[bill_vat_yn] ? number_format($t_emoneyprice) : number_format($t_supply_emoneyprice))."</td>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_supply_emoneyprice + $t_tax_emoneyprice)."</td>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\"></td>
					</tr>";
				}
				
			if ($_REQUEST[cartno]) {
				$html .= "</table>
				<table style=\"border-collapse:collapse; margin-top:10px;\">
					<tr style=\"border-top:2px solid #999\">
						<td style=\"width:70%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("총금액")."</td>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_price)."</td>
					</tr>
					<tr>
						<td style=\"width:70%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("공급가액")."</td>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_sum_supply_price)."</td>
					</tr>
					<tr>
						<td style=\"width:50%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("부가가치세")."</td>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_sum_tax_price)."</td>
					</tr>
				</table>
			</div>
			</div>
			</body>
			</html>";
			}else{
				$html .= "</table>
				<table style=\"border-collapse:collapse; margin-top:10px;\">
					<tr style=\"border-top:2px solid #999\">
						<td style=\"width:70%;border:1px solid #ddd;height:40px; text-align:center; font-size:13px;\">"._("총금액")."</td>
						<td style=\"border:1px solid #ddd;height:40px;width:360px; text-align:center; font-size:13px;\">".number_format($t_price)."</td>
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
			}
	} else {
	// P1 스킨이 아닌 버전
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
			<h1 style=\"margin:0;padding:0 0 30px;font-size:40px;line-height:60px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;position:relative;height:33px;font-weight:bold;\">"._("견적서")."</h1>
			<table style=\"border-collapse:collapse; width:100%;\">
				<tr>
					<td style=\"margin:0;padding:0;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
						<h2 style=\"margin:0;padding:0 0 20px;font-size:30px;line-height:50px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;\">".$member[cust_name]."</h2>
						<table class=\"date non\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;border:none;\">
							<tbody>
							<tr>
								<th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("견적번호")."</th>
								<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$billno."</td>
							</tr>
							<tr>
								<th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("견적일시")."</th>
								<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$nowdt."</td>
							</tr>
							<tr>
								<th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("담당자")."</th>
								<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$member[name]."</td>
							</tr>
							</tbody>
						</table>
					</td>
					<td style=\"margin:0;padding:0;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
						<table class=\"date supplier\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
							<thead>
							<tr>
								<th colspan=\"4\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;\">"._("공급자")."</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("상호")."</th>
								<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_nameComp]."</td>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("대표자")."</th>
								<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".$cfg[bill_nameCeo];
									
								if (is_file("../data/bill/".$cid."/bill_seal.png")) {
									$html .= "<img src=\"http://".$_SERVER[HTTP_HOST]."/data/bill/".$cid."/bill_seal.png\" alt=\"\" style=\"vertical-align:middle;border:none;\" />";
								}
								
					  			$html .= "
					  			</td>
					   		</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("등록번호")."</th>
								<td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_regnumBiz]."</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("주소")."</th>
								<td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_address]."</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("담당자")."</th>
								<td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_managerInfo]."</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("업태/종목")."</th>
								<td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_typeBiz]."/".$cfg[bill_itemBiz]."</td>
							</tr>
							<tr>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("전화")."</th>
								<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_phoneComp]."</td>
								<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("팩스")."</th>
								<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_faxComp]."</td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</table>";
	
			$html .= "
			<div class=\"title\" style=\"margin:30px 0 15px;padding:15px 5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;overflow:hidden;text-align:right;background:#f2f2f2;\">
				<div class=\"tit\" style=\"margin:0;padding:0;font-size:20px;line-height:30px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;float:left;font-weight:bold;\">"._("견적금액")."<span style=\"font-size:15px;\">("._("공급가액 + 세액").")</span></div>
		 		<div class=\"won\" style=\"margin:0;padding:0;font-size:20px;line-height:30px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;\">&#8361;".number_format($t_price)."</div>
		 	</div>";
		 
			$html .= "
			<table class=\"date\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
		 		<thead>
					<tr>
						<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;\">"._("품명")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">"._("수량")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">"._("공급가액")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">"._("세액")."</th>
						<th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">"._("단가")."</th>
					</tr>
					</thead>
					<tbody>";
			
			if ($_REQUEST[cartno]) {
				foreach ($cart->item as $k=>$v) {
					foreach ($v as $k2=>$v2) {
						//상품명
						if ($v2[package_flag] == "2") {
							$goodsnm = "<b>["._("패키지")."]".$v2[goodsnm]."</b>";
						} else {
							$goodsnm = "<b>".$v2[goodsnm]."</b>";
						}
						
						if ($v2[title]) {
							$goodsnm .= "<div> - ".$v2[title]."</div>";
						}
						
						if ($v2[est_order_option_desc_str]) {
							$goodsnm .= "<div style=\"margin-top:5px;\">";
							
							$goodsnm .= "<div>".$v2[est_order_option_desc_str]."</div>";
							
							if ($v2[files]) {
								$goodsnm .= "<div>"._("첨부 파일")." : ".$v2[files]."</div>";
							}
							
							if ($v2[est_order_memo]) {
								$goodsnm .= "<div>"._("주문 메모")." : ".$v2[est_order_memo]."</div>";
							}
							
							$goodsnm .= "</div>";
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
						
						if ($v2[cover_range_data]) {
							$goodsnm .= "<div style=\"margin-top:5px;\">".$v2[cover_range_data]."</div>";
						}
						
						
						if ($v2[package_flag] == "2" && $v2[package_parent_cartno] != "0") {
							$v_ea = "";
							$v_price = 0;
							$v_supply_price = 0;
							$v_tax_price = 0;
						} else {
							//수량
							$v_ea = $v2[ea];
							
							//단가
							$v_price = ($v2[price] + $v2[aprice] + $v2[addopt_aprice] + $v2[print_aprice] + $v2[addpage_price]);
							
							if ($v2[grpdc]) {
								$v_price = ($v_price - $v2[grpdc]);
							}
							
							/*
							//공급가액(단가 * 수량)
							$v_supply_price = ($v_price * $v_ea);
							*/

							$v_supply_price = $v2[payprice];

							//세액
							$v_tax_price = ($v_supply_price * 0.1);
							//부가세포함
							if ($cfg[bill_vat_yn]) {
								$v_supply_price = ($v_supply_price / 1.1);
								$v_tax_price = ($v_supply_price * 0.1);
							}

							//부가세 미포함시 세액 절사처리
							if(!$cfg[bill_vat_yn] && $cutmoney_use==1){
									
								$pow=pow(10,(int)$cutmoney_type);

								switch($cutmoney_op){
									case "F" :
										$v_tax_price = (int)(floor( (int)$v_tax_price  / $pow) * $pow); 
										break;
									case "C" :
										$v_tax_price = (int)(ceil( (int)$v_tax_price  / $pow) * $pow); 
										break;
									case "R" :
										$v_tax_price = (int)(round( (int)$v_tax_price / $pow ) * $pow); 
										break;
									default : 
										$v_tax_price = $v_tax_price;
										break;
								}
							}

						}
						
						$html .= " 
					 	<tr>
							<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$goodsnm."</td>
							<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".$v_ea."</td>
							<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_supply_price)."</td>
							<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_tax_price)."</td>
							<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_price)."</td>
						</tr>";
					}
				}
			} else if ($_REQUEST[payno]) {
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
						$v_ea = $v2[ea];
						
						//단가(공급가액 / 수량)
						$v_price = ($v2[goods_price]);
						
						//공급가액(=판매가액)
						$v_supply_price = $v2[payprice];
						
						//세액
						$v_tax_price = ($v_supply_price * 0.1);
						
						//부가세포함
						if ($cfg[bill_vat_yn]) {
							$v_supply_price = ($v_supply_price / 1.1);
							$v_tax_price = ($v_supply_price * 0.1);
						}
						//부가세 미포함시 세액 절사처리
						if(!$cfg[bill_vat_yn] && $cutmoney_use==1){
									
							$pow=pow(10,(int)$cutmoney_type);

							switch($cutmoney_op){
								case "F" :
									$v_tax_price = (int)(floor( (int)$v_tax_price  / $pow) * $pow); 
									break;
								case "C" :
									$v_tax_price = (int)(ceil( (int)$v_tax_price  / $pow) * $pow); 
									break;
								case "R" :
									$v_tax_price = (int)(round( (int)$v_tax_price / $pow ) * $pow); 
									break;
								default : 
									$v_tax_price = $v_tax_price;
									break;
							}
						}
						
						
						$html .= " 
					 	<tr>
							<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$goodsnm."</td>
							<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".$v_ea."</td>
							<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_supply_price)."</td>
							<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_tax_price)."</td>
							<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_price)."</td>
						</tr>";
					}
				}
			}
		
			$html .= "
			</tbody>
			<tfoot>
			<tr>
				<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;\">"._("배송비")."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\"></td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_supply_shipprice)."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_tax_shipprice)."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_shipprice)."</td>
			</tr>";
			
			if ($_REQUEST[payno]) {
				$html .= "
				<tr>
					<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;\">"._("할인금액")."</td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\"></td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_supply_dcprice)."</td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_tax_dcprice)."</td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_dcprice)."</td>
				</tr>
				<tr>
					<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;\">"._("포인트사용")."</td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\"></td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_supply_emoneyprice)."</td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_tax_emoneyprice)."</td>
					<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_emoneyprice)."</td>
				</tr>";
			}
			
			$html .= "
			<tr>
				<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;\">"._("합계")."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".$t_ea."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_supply_price)."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_tax_price)."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_price)."</td>
			</tr>
			</tfoot>
		</table>
		<table class=\"date etc\" style=\"border-collapse:collapse;width:100%;margin-top:10px;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
			<thead>
			<tr>
				<th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;\">"._("기타")."</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">";
			
		$strEtc = "";
		$i = 1;
		
		//기타
		if ($cfg[bill_Etc]) $strEtc .= $cfg[bill_Etc] . "\n";
	 	$strEtc = explode("\n", $strEtc); //"\n" 분리
	
		foreach ($strEtc as $k => $v) {
			if ($i == 4) $html .= $v;
			else $html .= $v."<br />";
			
			$i++;
		}
	
		for ($j=$i; $j < 5; $j++) {
			if($j == 4) $html .= "&nbsp;"; 
			else $html .= "<br />";
		}
		 
					$html .= "
					</td>
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
