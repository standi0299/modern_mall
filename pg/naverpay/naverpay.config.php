<?php
	//테스트 설정
	$cfg['pg']['naverpay_test'] = "Y";
	
		
	//$shopId = 'kcp_16';			//SHOP ID
	//$certiKey = "C791E239-29FB-43E4-9726-8C96D49A783A";		//가맹점 인증키
	$backUrl = "http://www.naver.com";
	
	$shopId = trim($cfg['pg']['npay_shopid']);
	$certiKey = trim($cfg['pg']['npay_authkey']);
	
		
	//$shopId = 'naver_pay';
	//$certiKey = "naver_pay"; 
	//버튼 인증키: BE83F116-DECB-4D8F-86D6-5BFA6FA915A5
	//네이버공통인증키: s_58588ff5129f
	
	 
	//버튼 인증키: BE83F116-DECB-4D8F-86D6-5BFA6FA915A5
	//네이버공통인증키: s_58588ff5129f
	
	//미오디오 인증키
	//$shopId = 'unicube';

	$is_mobile_order = isMobile();
	$naverpay_button_enable = 'Y';

	//$naverpay_button_count = 2;
	//if(basename($_SERVER['SCRIPT_NAME']) == 'cart.php' || basename($_SERVER['SCRIPT_NAME']) == 'order.php' )
  $naverpay_button_count = 1;			// 버튼 개수 설정. 구매하기 버튼만 있으면(장바구니 페이지) 1, 찜하기 버튼도 있으면(상품 상세 페이지) 2를 입력
	
	if($cfg['pg']['npay_test_flag'] || $cfg['pg']['card_test']) {
		$req_addr     = 'ssl://test-pay.naver.com';
	  $buy_req_url  = 'POST /customer/api/order.nhn HTTP/1.1';
	  $wish_req_url = 'POST /customer/api/wishlist.nhn HTTP/1.1';
	  $req_host     = 'test-pay.naver.com';
		$req_port     = 443;
	  if($is_mobile_order) {
	  	$orderUrl = 'https://test-m.pay.naver.com/mobile/customer/order.nhn';
	    $wishUrl  = 'https://test-m.pay.naver.com/mobile/customer/wishList.nhn';
		} else {
	  	$orderUrl = 'https://test-pay.naver.com/customer/order.nhn';
	    $wishUrl  = 'https://test-pay.naver.com/customer/wishlistPopup.nhn';
		}
	} else {
		$req_addr     = 'ssl://pay.naver.com';
		$buy_req_url  = 'POST /customer/api/order.nhn HTTP/1.1';
		$wish_req_url = 'POST /customer/api/wishlist.nhn HTTP/1.1';
		$req_host     = 'pay.naver.com';
		$req_port     = 443;
		if($is_mobile_order) {
			$orderUrl     = 'https://m.pay.naver.com/mobile/customer/order.nhn';
			$wishUrl      = 'https://m.pay.naver.com/mobile/customer/wishList.nhn';
		} else {
	  	$orderUrl     = 'https://pay.naver.com/customer/order.nhn';
	  	$wishUrl      = 'https://pay.naver.com/customer/wishlistPopup.nhn';
	  }
	}

?>