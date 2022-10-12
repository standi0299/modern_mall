<?
/*
 * 명세서 / 견적서
 * 견적의뢰 장바구니와 주문내역에서 호출 가능 cartno 또는 storageid 를 받아서 처리 (복수 ","로 구분)
 * tb_extra_cart 저장 없이 상품 정보 페이지(자동견적 프리셋 100112)에서 호출 가능 (mode:preview, no:-1, option_json:견적정보) 
 * parameter
 * -mode : view 보기 / commit 발행 / preview 보기
 * -no : cartno 또는 storageid 또는 no:-1
 * -option_json : 견적정보
 * return value
 * - OK / FAIL
 * 2015.06.30 by kdk
 * 2016.01.19 수정 by kdk
 * */

//include "../lib/library.php";

# test
//$_REQUEST[mode] = "cart";
//$_REQUEST[no] = "836,837,838";

//$_REQUEST[mode] = "order";
//$_REQUEST[no] = "1417512029951_0001,1417512029951_0002";

try {

	# cookie check
	//if (!$_COOKIE[cartkey] && !$sess[mid] && !$sess_admin[mid]) 
	//	throw new Exception("Error cookie", 1);
	
	# mode check
	if (!$_REQUEST[mode]) 
		throw new Exception("Error Parameter [mode]", 1);
	
	# no check
	if (!$_REQUEST[no] && !$_REQUEST[storageid]) 
		throw new Exception("Error Parameter [no or storageid]", 1);

	# 견적서 관리 정보
	if ($cfg[bill_vat_yn] == "") $cfg[bill_vat_yn] = "0";
	if ($cfg[bill_nameComp] == "") $cfg[bill_nameComp] = $cfg[nameComp];
	if ($cfg[bill_typeBiz] == "") $cfg[bill_typeBiz] = $cfg[typeBiz];
	if ($cfg[bill_itemBiz] == "") $cfg[bill_itemBiz] = $cfg[itemBiz];
	if ($cfg[bill_regnumBiz] == "") $cfg[bill_regnumBiz] = $cfg[regnumBiz];
	if ($cfg[bill_regnumOnline] == "") $cfg[bill_regnumOnline] = $cfg[regnumOnline];
	if ($cfg[bill_nameCeo] == "") $cfg[bill_nameCeo] = $cfg[nameCeo];
	if ($cfg[bill_managerInfo] == "") $cfg[bill_managerInfo] = $cfg[managerInfo];
	if ($cfg[bill_address] == "") $cfg[bill_address] = $cfg[address];
	if ($cfg[bill_phoneComp] == "") $cfg[bill_phoneComp] = $cfg[phoneComp];
	if ($cfg[bill_faxComp] == "") $cfg[bill_faxComp] = $cfg[faxComp];
	
	if ($cfg[bill_payOpt] == "") $cfg[bill_payOpt] = "";
	if ($cfg[bill_expDt] == "") $cfg[bill_expDt] = "";
	
	# 가격 확인
	$t_ea = 0;
	$t_supply_price = 0;
	$t_tax_price = 0;
	$t_price = 0;
		
	$res = array();
	
	//tb_extra_cart 저장 없이 상품 정보 페이지(자동견적 프리셋 100112)에서 호출 가능 (mode:preview, no:-1, option_json:견적정보)
	if ($_REQUEST[no] == -1) {
		
	    if ($_REQUEST[option_json]){
  			$orderOptionData = str_replace("\r\n", "", $_REQUEST[option_json]);
  			$extraOptionData = orderJsonParse2($orderOptionData);

  			$data[est_order_data] = $extraOptionData[est_order_data];
  			$data[est_order_option_desc] = $extraOptionData[est_order_option_desc];
  			$data[est_supply_price] = $extraOptionData[est_supply_price];
			
			$data[ea] = "1";
  			$data[est_price] = $extraOptionData[est_price];
  			$data[est_order_type] = $_REQUEST[est_order_type];

  			$data[est_order_memo] = $_REQUEST[est_order_memo];     //자동견적 주문 메모    20140326
  			$data[title] = $_REQUEST[est_title];     //자동견적 주문 제목    20140410
	    }
		
		$res[] = $data;
		//debug($res);		
	}
	else {
		
		if ($_REQUEST[no]) {
			$no = explode(",",$_REQUEST[no]);
			$fieldWhere = "cartno";
		}
		else if ($_REQUEST[storageid]) {
			$no = explode(",",$_REQUEST[storageid]);
			$fieldWhere = "storageid";
		}	
		
		foreach ($no as $k=>$v) {
			
			if($v == "") continue;
			
		    $query = "select * ";
		    //list($dummy) = $db->fetch($query,1);
		    //if ($dummy) $addopt[] = $dummy;
			
			$tableWhere = "from tb_extra_cart where $fieldWhere='$v';";
	
			//debug($query.$tableWhere);
			$data = $db->fetch($query.$tableWhere);
		
			# 회원 정보 조회
			//list($name, $cust_name) = $db->fetch("select name, cust_name from exm_member where cid='$cid' and mid='$data[mid]'",1);
			$name = $data[order_name]; 
			$cust_name = $data[order_cname];
			$email = $data[order_email];
	
			$est_bigo = $data[est_bigo];
			
			# 상품 정보 조회
			$query = "select a.goodsno,a.goodsnm,
			    (select catno from exm_goods_link where cid='$cid' and goodsno='$data[goodsno]') as catno,
			    if(b.price is null,a.price,b.price) price,  
			    if(b.reserve is null,a.reserve,b.reserve) reserve
			from
			    exm_goods a
			    inner join exm_goods_cid b on a.goodsno = b.goodsno
			where
			    a.goodsno = '$data[goodsno]'
			    and b.cid = '$cid'
			";
			
			$tmp = $db->fetch($query);
			if($tmp) {
				$data[goodsnm] = $tmp[goodsnm];
			}
			
			//debug($data);
			$res[] = $data;
		}
		//debug($res);
			
	}

	foreach ($res as $k => $v) {
		//debug($v);
	
		//수량
		$ea = $v[ea];
	
		//단가
		$price = $v[est_price]; 
	
		//공급가액(단가 * 수량)	
		$supply_price = ($price * $ea);

		//세액
		$tax_price = $supply_price * 0.1;
	
		//if($cfg[bill_vat_yn]) { //부가세포함
			$supply_price = $supply_price / 1.1;
			$tax_price = $supply_price * 0.1;
		//}
	
		//합계수량
		$t_ea += $ea; 

		//합계단가
		$t_price += $price;
	
		//합계공급가액
		$t_supply_price += $supply_price;
	
		//합계세액
		$t_tax_price += $tax_price;
	}

	//debug("t_ea=".$t_ea);
	//debug("t_price=".$t_price);
	//debug("t_supply_price=".$t_supply_price);
	//debug("t_tax_price=".$t_tax_price);
	//exit;
	
	//견적금액(공급가액 단가+세액) = 합계공급가액 + 합계세액
	$t_price = ($t_supply_price + $t_tax_price);
	//debug($t_price);

	//billno 생성
	$billno = $data[est_order_no];
	if($billno == "") {
		$billno = date("Y-m-d-His")."-".rand(1000,9999);
	}

	# 명세서/견적서 폼 생성
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
					<h2 style=\"margin:0;padding:0 0 20px;font-size:30px;line-height:50px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;\">".$cust_name."</h2>
					<table class=\"date non\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;border:none;\">
						<tbody>
						<tr>
							<th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("견적번호")."</th>
							<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$billno."</td>
						</tr>
						<tr>
							<th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("견적일시")."</th>
							<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$data[est_order_datedt]."</td>
						</tr>
						<tr>
							<th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("담당자")."</th>
							<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$name."</td>
						</tr>";
						
						/*if ($cfg[skin] != "bizcard")
						{
							$html .= "
							<tr>
								<th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">결제조건</th>
								<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_payOpt]."</td>
							</tr>
							<tr>
								<th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">유효기간</th>
								<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_expDt]."</td>
							</tr>";
						}*/
						
						$html .= "
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
								
							if (is_file("../data/bill/".$cid."/bill_seal.png")){
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

		$i = 1;
		foreach ($res as $k => $v) {
			
			//품명
			//$goodsnm = $v[goodsnm];
			//$goodsnm = str_replace("<br>", " ", $data[est_order_option_desc_fix]);
			if($data[est_order_option_desc_fix]) {
				$goodsnm = $data[est_order_option_desc_fix];
			}
			else {
				$goodsnm = $data[est_order_option_desc];
			}

			//의뢰내용
			//$goodsnm = str_replace("::", ":", $goodsnm);
			//$goodsnm = str_replace("[", "", $goodsnm);
			//$goodsnm = str_replace("]", "", $goodsnm);
			//$goodsnm = str_replace("|", " / ", $goodsnm);		
			
			//견적 옵션 문자열 정리. 2015.07.03 by kdk	
			$goodsnm = OptionDesc($goodsnm);

			//수량
			$v_ea = $v[ea];
			
			//단가
			$v_price = $v[est_price]; 
			
			//공급가액(단가 * 수량)	
			$v_supply_price = ($v_price * $v_ea);
		
			//세액
			$v_tax_price = $v_supply_price * 0.1;

			if($cfg[bill_vat_yn]) { //부가세포함
				$v_price = $v_price / 1.1;
				$v_supply_price = $v_supply_price / 1.1;
				$v_tax_price = $v_supply_price * 0.1;
			}
	
			$html .= " 
		 	<tr>
				<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$goodsnm."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".$v_ea."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_supply_price)."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_tax_price)."</td>
				<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".number_format($v_price)."</td>
			</tr>";
	
			$i++;
		}
	
	/*
	for ($j=$i; $j < 13; $j++) { 
		$html .= " 
		<tr>
			<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">&nbsp;</td>
			<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\"></td>
			<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\"></td>
			<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\"></td>
			<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\"></td>
		</tr>";
	}
	*/
	
		$html .= "
		</tbody>
		<tfoot>
		<tr>
			<td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;\">"._("합계")."</td>
			<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".$t_ea."</td>
			<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_supply_price)."</td>
			<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_tax_price)."</td>
			<td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_supply_price+$t_tax_price)."</td>
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

	/*if($est_bigo) {
 		//비고
 		$strEtc = explode("\n", $est_bigo); // "\n" 분리		
	}
	else {
		//기타
		$strEtc = explode("\n", $cfg[bill_Etc]); // "\n" 분리		
	}*/
		
	$strEtc = ""; 
	//debug($est_bigo);
	//debug($cfg[bill_Etc]);

	if($cfg[bill_Etc]) {
		//기타
		$strEtc .= $cfg[bill_Etc] . "\n";
	}	
	if($est_bigo) {
 		//비고
 		$strEtc .= "\n" . $est_bigo;
	}
 	$strEtc = explode("\n", $strEtc); // "\n" 분리

	$i = 1;
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
	
	echo($html);

	$json[html] = $html;
	$jsonEncodeHtml = json_encode($json);
	//debug($jsonEncodeHtml);
	
	//$jsonDecodeHtml = json_decode($jsonEncodeHtml,1);
	//debug($jsonDecodeHtml[html]);
	//echo($jsonDecodeHtml[html]);
	
	/*
	# 명세서/견적서 DB 등록
	$query = "
	insert into tb_bill set
	    billno = '$billno',
	    cid = '$cid',
	    mid = '$sess[mid]',    
	    mode = '$_REQUEST[mode]',
	    payno_cartno = '$_REQUEST[no]',
	    bill_html = '$jsonEncodeHtml',
	    regist_date = now()
	";
	//debug($query);
	$db->query($query);	
	*/
	//echo $_REQUEST[mode];
	if ($_REQUEST[mode] == "commit") { //발행이면...
		# 메일 발송
		include "../lib/class.mail.php";
		
		$mail = new Mail($params);
		$headers['From']    = $cfg[emailAdmin];
		$headers['Name']    = $cfg[nameSite];
		$headers['Subject'] = _("견적서 메일입니다.");
		
		/*
		//회원 메일 주소 조회
		$query = "select email from exm_member where cid = '$cid' and email != '' and mid='$sess[mid]'";
		//debug($query);
		$mem = $db->fetch($query);
		//debug($mem[email]);
		
		$headers['To'] = $mem[email];
		//debug($headers);
		*/
		
		$headers['To'] = $email;

		//메일 발송
		if($headers['To'] != "") { 
			//if(!$mail->send($headers, $html))			//mailSendAsyncWithLogID() 로 대체 			20180928		chunter
			//	throw new Exception("Error Send Mail", 1);
			
			//로그 저장
			$log_mail_no = emailLog(array("to"=>$email,"subject"=>$headers['Subject'],"contents"=>$html));
			mailSendAsyncWithLogID($log_mail_no);
		}
	}

   	//echo "OK";
   		
} catch (Exception $e) {
    echo 'Fail: ',  $e->getMessage(), "\n";
}

//견적 옵션 문자열 정리. 2015.07.03 by kdk
function OptionDesc($str) {
	
	$opt = "";
	$estOrderOptionDesc = formatEstOrderOptionDesc($str);
	//debug($estOrderOptionDesc);
	
	if($estOrderOptionDesc[_("수량")]){
		$opt .= "<div>"._("수량").":";
			foreach ($estOrderOptionDesc[_("수량")] as $k => $v) {
				if($v) $opt .= $v;
			}
		$opt .= "</div>";
	}
	
	if($estOrderOptionDesc[_("규격")]){
		$opt .= "<div>"._("규격").":";
			foreach ($estOrderOptionDesc[_("규격")] as $k => $v) {
				if($v) $opt .= $v;
			}
		$opt .= "</div>";
	}
	
	if($estOrderOptionDesc[_("표지")]){
		$opt .= "<div>"._("표지").":";
			foreach ($estOrderOptionDesc[_("표지")] as $k => $v) {
				if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
			}
		$opt .= "</div>";
	}

	if($estOrderOptionDesc[_("내지")]){
		$opt .= "<div>"._("내지").":";
			foreach ($estOrderOptionDesc[_("내지")] as $k => $v) {
				if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
			}
		$opt .= "</div>";
	}

	if($estOrderOptionDesc[_("추가내지")]){
		foreach ($estOrderOptionDesc[_("추가내지")] as $k => $v) {
			$opt .= "<div>"._("추가내지").":";
				foreach ($v as $k2 => $v2) {
					if($v2) $opt .= "<div style='padding-left:30px'>".$v2."</div>";
				}
			$opt .= "</div>";
		}
	}

	if($estOrderOptionDesc[_("면지")]){
		$opt .= "<div>"._("면지").":";
			foreach ($estOrderOptionDesc[_("면지")] as $k => $v) {
				if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
			}
		$opt .= "</div>";
	}

	if($estOrderOptionDesc[_("간지")]){
		$opt .= "<div>"._("간지").":";
			foreach ($estOrderOptionDesc[_("간지")] as $k => $v) {
				if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
			}
		$opt .= "</div>";
	}

	if($estOrderOptionDesc[_("옵션")]){
		$opt .= "<div>"._("옵션").":";
			foreach ($estOrderOptionDesc[_("옵션")] as $k => $v) {
				if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
			}
		$opt .= "</div>";
	}

	if($estOrderOptionDesc[_("후가공")]){
		$opt .= "<div>"._("후가공").":";
			foreach ($estOrderOptionDesc[_("후가공")] as $k => $v) {
				if($v) $opt .= "<div style='padding-left:30px'>".$v."</div>";
			}
		$opt .= "</div>";
	}
	
	return $opt;
}
?>
