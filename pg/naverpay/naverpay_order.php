<?php
include_once(dirname(__FILE__).'/../../lib/library.php');
include_once(dirname(__FILE__).'/../../lib/class.cart.php');
//include_once(dirname(__FILE__).'/naverpay.inc.php');
include_once(dirname(__FILE__).'/naverpay.config.php');
include_once(dirname(__FILE__).'/naverpay.lib.php');


//테스트 데이타
//$_POST['cartno'] = array("4096");

$is_collect = false;    //착불체크 변수 초기화
$is_prepay = false;     //선불체크 변수 초기화


if($_POST['mode'] == 'npay_order') {
	if(!count($_POST['cartno']))
  	return_error2json('구매하실 상품을 하나이상 선택해 주십시오.');
	
	//MALL_MANAGE_CODE 파라미터 자리수 (300) 때문에 상품 최대 10개까지 (보관함 코드가 22자리.)
	if(count($_POST['cartno']) > 10)
		return_error2json('최대 10개 상품만 주문할 수 있습니다.');	
	
	$cart = new Cart($_POST['cartno']);	 
		
	//debug($cart);
	//DB와 장바구니에서 상품 정보를 얻어 온다.
	if($cart->item) 
	{
		$queryString = 'SHOP_ID='.urlencode($shopId);
		$queryString .= '&CERTI_KEY='.urlencode($certiKey);
		//debug($queryString);
		$totalMoney = 0;	
		$shippingType = "";
		$shippingPrice = 0;
		$teimqueryString = "";
		$item_rid = "";		//출고처
		
		$storageIds = ""; 
		foreach ($cart->item as $k => $v) 
		{
	  //debug($k);
	  //debug($v);
	
			foreach ($v as $key => $val)
			{
				//debug($val);
				//구매 불가
			  if ($val[error] > 0 && $val[error] < 3 ) {
			  	//$val[errmsg]
					continue;
				}
				
				//네이버페이 주문시 출고처 한개만 가능하므로.
				if ($item_rid != "" && $item_rid != $val[rid])
				{
					return_error2json('다른 출고처 상품을 동시 주문할 수 없습니다.');
				}
				
				if($val[error] == 1)
        	return_error2json('상품정보가 존재하지 않습니다.');

    		if($val[error] == 3 || $val[error] == 6)
        	return_error2json($it['it_name'].' 는(은) 구매할 수 없거나 제고가 부족합니다.');
				
				if($val[error] == 7)
        	return_error2json('편집이 완료되지 않아 구매할 수 없습니다.');
		
				if($val[error])
        	return_error2json($val[goodsnm].'상품은 주문이 불가능합니다.('.$val[error]. ')');
				
				if ($val[payprice] < 1)
					return_error2json('구매금액이 음수 또는 0원인 상품은 구매할 수 없습니다.');
					
				if ($val[state] == 1) 
					return_error2json('품절인 상품이 있습니다. 품절 상품은 삭제해 주세요.');	
				
				$storageIds .= $val[storageid]."/";
				
				$id = $val[goodsno];
				$name = $val[goodsnm];
				
				//$uprice = $val[price];			//개별 상품 단가				
				//상품 단가를 모든 옵션을 더한 가격으로 해야 하나????		모든 옵션을 더한것을 단가로 한다. 
				$uprice = $val[price] + $val[aprice] + $val[addopt_aprice] + $val[print_aprice] + $val[addpage_price];
				$count = $val[ea];				
				$tprice = $val[saleprice];		//해당 상품 총 가격.
				
				if ($val[opt])
					$option = $val[opt]."(".$val[aprice].")/";
				
				if ($val[addopt])
				{
					foreach ($val[addopt] as $optkey => $optvalue) {
						$option .= $optvalue[addopt_bundle_name] .":". $optvalue[addoptnm]."(".$optvalue[addopt_aprice].")/";
					} 
				}
				
				if ($val[printopt]) {
					foreach ($val[printopt] as $optkey => $optvalue) {
						$option .= $optvalue[printoptnm] .":". $optvalue[ea]."(".$optvalue[print_price].")/";
					}
				}
				
				if ($val[addpage])
					$option .= $val[addpage]."(".$val[addpage_price].")/";

				$item = new ItemStack($id, $name, $tprice, $uprice, $option, $count, $val[storageid]);
				//debug($item); 
				$totalMoney += $tprice; 
				$queryString .= '&'.$item->makeQueryString();	  
			  
				if (!$key)
				{
					//착불 배송 설정.
					if ($val[order_shiptype] == "4")	
						$shippingType = 'ONDELIVERY';
					else {				
						if ($cart->shipprice[$val[rid]])
						{
							$shippingPrice += $cart->shipprice[$val[rid]];
							
							if ($shippingPrice > 0)
								$shippingType = 'PAYED';
							else
								$shippingType = 'FREE';			
						}
					}
				}
				
				$item_rid = $val[rid];			//출고처 저장
			}
		}

		if (strlen($storageIds) > 300)
			return_error2json("MANAGE_CODE의 허용범위가 넘었습니다.서비스 관리자에게 문의주세요.");
		 
		$queryString .= '&MALL_MANAGE_CODE='.$storageIds;			//보관함 코드들. 네이버페이 결제후 이코드를 찾아서 pods승인처리해야함.
		
		$totalPrice = (int)$totalMoney + (int)$shippingPrice;
		$queryString .= '&TOTAL_PRICE='.$totalPrice;		
		$queryString .= '&SHIPPING_TYPE='.$shippingType;
		$queryString .= '&SHIPPING_PRICE='.$shippingPrice;
		$queryString .= '&RESERVE1=&RESERVE2=&RESERVE3=&RESERVE4=&RESERVE5=';
		$queryString .= '&BACK_URL='.$backUrl;
		
		$queryString .= '&SHIPPING_ADDITIONAL_PRICE='.$cfg['pg']['npay_addshipping_text'];				
		$queryString .= '&SA_CLICK_ID='.$_COOKIE["NVADID"]; //CTS
		// CPA 스크립트 가이드 설치 업체는 해당 값 전달
		$queryString .= '&CPA_INFLOW_CODE='.urlencode($_COOKIE["CPAValidator"]);
		$queryString .= '&NAVER_INFLOW_CODE='.urlencode($_COOKIE["NA_CO"]);
	} 
	
	//착불인 상품과 선불인 상품을 주문할수 없게 하려면	
	//if( $is_prepay && $is_collect ){
  //  return_error2json("배송비 착불인 상품과 선불인 상품을 동시에 주문할수 없습니다.\n\n장바구니에서 착불 또는 선불 중 한가지를 선택하여 상품들을 주문해 주세요.");
	//}
	
//debug($req_addr);
//debug($queryString);
//exit;

	$nc_sock = @fsockopen($req_addr, $req_port, $errno, $errstr);
	if ($nc_sock) {
    fwrite($nc_sock, $buy_req_url."\r\n" );
    fwrite($nc_sock, "Host: ".$req_host.":".$req_port."\r\n" );
    fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n");
    fwrite($nc_sock, "Content-length: ".strlen($queryString)."\r\n");
    fwrite($nc_sock, "Accept: */*\r\n");
    fwrite($nc_sock, "\r\n");
    fwrite($nc_sock, $queryString."\r\n");
    fwrite($nc_sock, "\r\n");

    // get header
    while(!feof($nc_sock)) {
        $header=fgets($nc_sock,4096);
        if($header=="\r\n") {
            break;
        } else {
            $headers .= $header;
        }
    }
    // get body
    while(!feof($nc_sock)) {
        $bodys.=fgets($nc_sock,4096);
    }

    fclose($nc_sock);
		//debug($headers);
    $resultCode = substr($headers,9,3);
    if ($resultCode == 200) {
        // success
        $orderId = $bodys;
    } else {
        // fail
        return_error2json($bodys);
       // debug($bodys);
    }
	} else {
    //echo "$errstr ($errno)<br>\n";
    return_error2json($errstr($errno));
    exit(-1);
    //에러처리
	}

	if($resultCode == 200)
	{
    die(json_encode(array('error'=>'', 'ORDER_URL'=>$orderUrl, 'ORDER_ID'=>$orderId, 'SHOP_ID'=>$shopId, 'TOTAL_PRICE'=>$totalPrice)));
	}
	
}	else {
	return_error2json('네이버 페이는 장바구에서만 구매가능 합니다.');
}

?>

