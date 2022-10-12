<?php

if(!$is_admin && !$default['de_card_test'] && $default['de_naverpay_test']) {
    if($default['de_naverpay_mb_id'] && ($is_guest || $member['mb_id'] != $default['de_naverpay_mb_id']))
        return;
}

if(!$cfg['pg']['naverpay_cert_key'] || !$cfg['pg']['naverpay_button_key'])
	return;

if(basename($_SERVER['SCRIPT_NAME']) == 'view.php') {
		
	//if(!$is_orderable)
  //	return;
}

$naverpay_button_js = '';

if($is_mobile_order) 
{
	if($cfg['pg']['naverpay_test'])
		$naverpay_button_js_url = 'https://test-pay.naver.com/customer/js/mobile/naverPayButton.js';
	else
		$naverpay_button_js_url = 'https://pay.naver.com/customer/js/mobile/naverPayButton.js';

	$naverpay_button_js = '<script type="text/javascript" src="'.$naverpay_button_js_url.'" charset="UTF-8"></script>
    <script type="text/javascript" >//<![CDATA[
    naver.NaverPayButton.apply({
    BUTTON_KEY: "'.$cfg['pg']['naverpay_button_key'].'", // 네이버페이에서 제공받은 버튼 인증 키 입력
    TYPE: "MA", // 버튼 모음 종류 설정
    COLOR: 1, // 버튼 모음의 색 설정
    COUNT: '.$naverpay_button_count.', // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
    ENABLE: "'.$naverpay_button_enable.'", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
    BUY_BUTTON_HANDLER : buy_nc, // 구매하기 버튼 이벤트 Handler 함수 등록, 품절인 경우 not_buy_nc 함수 사용
    WISHLIST_BUTTON_HANDLER : wishlist_nc, // 찜하기 버튼 이벤트 Handler 함수 등록
    "":""
    });
    //]]></script>'.PHP_EOL;
} else {
	$naverpay_button_js = '<script type="text/javascript" src="https://pay.naver.com/customer/js/naverPayButton.js" charset="UTF-8"></script>
    <script type="text/javascript" >//<![CDATA[
    naver.NaverPayButton.apply({
    BUTTON_KEY: "'.$cfg['pg']['naverpay_button_key'].'", // 페이에서 제공받은 버튼 인증 키 입력
    TYPE: "A", // 버튼 모음 종류 설정
    COLOR: 1, // 버튼 모음의 색 설정
    COUNT: '.$naverpay_button_count.', // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
    ENABLE: "'.$naverpay_button_enable.'", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
    BUY_BUTTON_HANDLER : buy_nc, // 구매하기 버튼 이벤트 Handler 함수 등록, 품절인 경우 not_buy_nc 함수 사용
    WISHLIST_BUTTON_HANDLER : wishlist_nc, // 찜하기 버튼 이벤트 Handler 함수 등록
    "":""
    });
    //]]></script>'.PHP_EOL;
}

$naverpay_button_js .= '<input type="hidden" name="naverpay_form" value="'.basename($_SERVER['SCRIPT_NAME']).'">'.PHP_EOL;

define('SHIPPING_ADDITIONAL_PRICE', $cfg['pg']['naverpay_sendcost']);

$naverpay_request_js = '<script type="text/javascript" >//<![CDATA[
	function buy_nc(url)
	{
    var f = $(this).closest("form").get(0);

    var check = fsubmit_check(f);
    if ( check ) {
        //네이버페이로 주문 정보를 등록하는 가맹점 페이지로 이동.
        //해당 페이지에서 주문 정보 등록 후 네이버페이 주문서 페이지로 이동.
        //location.href=url;

        //var win_buy_nc = window.open("_blank", "win_buy_nc", "scrollbars=yes,width=900,height=700,top=10,left=10");
        //f.action = "'.SERVER_DOMAIN.'/pg/naverpay/naverpay_order.php";
        //f.target = "win_buy_nc";
        //f.submit();
        //return false;

        $.ajax({
            url : "'.SERVER_DOMAIN.'/pg/naverpay/naverpay_order.php",
            type : "POST",
            data : $(f).serialize(),
            async : false,
            cache : false,
            dataType : "json",
            success : function(data) {
                if(data.error) {
                    alert(data.error);
                    return false;
                }

                document.location.href = "'.$orderUrl.'?ORDER_ID="+data.ORDER_ID+"&SHOP_ID="+data.SHOP_ID+"&TOTAL_PRICE="+data.TOTAL_PRICE;
            }
        });
    }

    return false;
}
function wishlist_nc(url)
{
    var f = $(this).closest("form").get(0);

    // 네이버페이로 찜 정보를 등록하는 가맹점 페이지 팝업 창 생성.
    // 해당 페이지에서 찜 정보 등록 후 네이버페이 찜 페이지로 이동.
    '.($is_mobile_order ? '' : 'var win_wishlist_nc = window.open(url,"win_wishlist_nc","scrollbars=yes,width=400,height=267");'.PHP_EOL.'f.target = "win_wishlist_nc";').'
    f.action = "'.SERVER_DOMAIN.'/pg/naverpay/naverpay_wish.php";
    f.submit();

    return false;
}
function not_buy_nc()
{
    alert("죄송합니다. 네이버페이로 구매가 불가한 상품입니다.");
    return false;
}
//]]></script>'.PHP_EOL;
?>