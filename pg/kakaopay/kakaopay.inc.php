<?php
	//include_once(dirname(__FILE__).'/../../lib/library.php');
		
	//카카오 페이 테스트 결제 처리	
	//$kakaoPayAdminKey = "0c6a9efd5926e88cbae6819973917d0a";
	//$kakaoPayCID = "TC0ONETIME";	
		
	$kakaoPayAdminKey = $cfg['pg']['kakaopay_adminkey'];
	$kakaoPayCID = $cfg['pg']['kakaopay_cid'];	
	
	//$payApprovalUrl = "http://mdev.bluepod.kr/pg/kakaopay/pay_success.php";
	//$payFailUrl = "http://mdev.bluepod.kr/pg/kakaopay/pay_fail.php";
	//$payCancelUrl = "http://mdev.bluepod.kr/pg/kakaopay/pay_cancel.php";
	
	$payApprovalUrl = "http://".USER_HOST."/pg/kakaopay/pay_success.php";
	$payFailUrl = "http://".USER_HOST."/pg/kakaopay/pay_fail.php";
	$payCancelUrl = "http://".USER_HOST."/pg/kakaopay/pay_cancel.php";
	
		
	function kakaoPayReady($payno, $mid, $goods_name, $qty, $acount_price) 
	{
		global $kakaoPayCID, $payApprovalUrl, $payFailUrl, $payCancelUrl;
		
		$apiUrl = "https://kapi.kakao.com/v1/payment/ready";
		
		$postData[cid] = $kakaoPayCID;
		//$postData[partner_order_id] = "12345679";
		//$postData[partner_user_id] = "user_mid";
		//$postData[item_name] = "초코파이";
		//$postData[quantity] = "1";
		//$postData[total_amount] = "2200";
		//$postData[vat_amount] = "200";
		//$postData[tax_free_amount] = "0";
				
		$postData[partner_order_id] = $payno;
		$postData[partner_user_id] = $mid;
		$postData[item_name] = $goods_name;
		$postData[quantity] = $qty;
		$postData[total_amount] = $acount_price;
		$postData[vat_amount] = round($acount_price / 1.1, 0);
		$postData[tax_free_amount] = "0";
		
		$postData[approval_url] = $payApprovalUrl;
		$postData[fail_url] = $payFailUrl;
		$postData[cancel_url] = $payCancelUrl;
		
		$kResult = kakaoPaySend($apiUrl, $postData);		
		//$payResult = json_decode($result['body'], true);
		return $kResult;
	}
	
	function kakaoPayRequest($tid, $payno, $mid, $pg_token, $payload, $total_amount)
	{
		global $kakaoPayCID;
		$apiUrl = "https://kapi.kakao.com/v1/payment/approve";
		
		$postData[cid] = $kakaoPayCID;
		//$postData[tid] = "12345679";
		//$postData[partner_order_id] = "12345679";
		//$postData[partner_user_id] = "12345679";
		//$postData[pg_token] = "12345679";
		//$postData[payload] = "12345679";
		//$postData[total_amount] = "12345679";
		
		$postData[tid] = $tid;
		$postData[partner_order_id] = $payno;
		$postData[partner_user_id] = $mid;
		$postData[pg_token] = $pg_token;
		$postData[payload] = $payload;
		$postData[total_amount] = $total_amount;
		
		$kResult = kakaoPaySend($apiUrl, $postData);		
		return $kResult;		
	}
	
	
	function kakaoPaySend($apiUrl, $postData)
	{
		global $kakaoPayAdminKey;
		
		$query = http_build_query($postData);

    // construct curl resource
    $curl = curl_init($apiUrl);

    // data will be json encoded
    //$data = json_encode($data);

    // additional options
    curl_setopt_array($curl, array(
        CURLOPT_POST            => true,
        CURLOPT_HEADER          => true,
        CURLOPT_RETURNTRANSFER  => true,
       	CURLOPT_SSL_VERIFYPEER	=> false,
       	CURLOPT_SSL_VERIFYHOST	=> false, 
        CURLOPT_HTTPHEADER      =>  array('Authorization: KakaoAK '.$kakaoPayAdminKey, "Content-type: application/x-www-form-urlencoded;charset=utf-8"),
        CURLOPT_POSTFIELDS      =>  $query
    ));
		//debug($postData);
    // do the call
    $response = curl_exec($curl);
		if (curl_error($curl))  
 		{ 
    	exit('CURL Error('.curl_errno( $curl ).') '. curl_error($curl)); 
 		}
		
		//$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				
		$header_size = curl_getinfo($curl,CURLINFO_HEADER_SIZE);
    $result['header'] = substr($response, 0, $header_size);
    $result['body'] = substr($response, $header_size );
    $result['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $result['last_url'] = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);        
    curl_close($curl);

    // bad request
    //$payResult = json_decode($result['body'], true);
		return $result;		
		//debug($payResult);
	}

?>