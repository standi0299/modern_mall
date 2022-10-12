<?php
require_once(dirname(__FILE__).'/../pg/INIPayStdWeb/libs/INIStdPayUtil.php');
require_once(dirname(__FILE__).'/../pg/INIPayStdWeb/libs/sha256.inc.php');
$SignatureUtil = new INIStdPayUtil();


//############################################
// 1.전문 필드 값 설정(***가맹점 개발수정***)
//############################################
// 여기에 설정된 값은 Form 필드에 동일한 값으로 설정
//$mid = "INIpayTest";  // 가맹점 ID(가맹점 수정후 고정)					
$mid = $cfg[pg][mid];  // 가맹점 ID(가맹점 수정후 고정)

//인증
//$signKey = "SU5JTElURV9UUklQTEVERVNfS0VZU1RS"; // 가맹점에 제공된 키(이니라이트키) (가맹점 수정후 고정) !!!절대!! 전문 데이터로 설정금지
$signKey = $cfg[pg][inipay_sign_key]; // 가맹점에 제공된 키(이니라이트키) (가맹점 수정후 고정) !!!절대!! 전문 데이터로 설정금지

$timestamp = $SignatureUtil->getTimestamp();   // util에 의해서 자동생성
//$orderNumber = $mid . "_" . $timestamp; // 가맹점 주문번호(가맹점에서 직접 설정)
$orderNumber = $_POST[payno]; // 가맹점 주문번호(가맹점에서 직접 설정)

//$price = 1000;        // 상품가격(특수기호 제외, 가맹점에서 직접 설정)
$price = $_POST[payprice];        // 상품가격(특수기호 제외, 가맹점에서 직접 설정)


$r_quotaCard = array();
for ($i=2;$i<=$cfg[pg][quotaopt];$i++) $r_quotaCard[] = $i;
$cfg[pg][quotaCard] = implode(":",$r_quotaCard);
$cfg[pg][quotaCard] = iconv("UTF-8","EUC-KR",$cfg[pg][quotaCard]);

$cardNoInterestQuota = "";  // 카드 무이자 여부 설정(가맹점에서 직접 설정)
//$cardNoInterestQuota = "11-2:3:,34-5:12,14-6:12:24,12-12:36,06-9:12,01-3:4";  // 카드 무이자 여부 설정(가맹점에서 직접 설정)
$cardQuotaBase = "2:3:4:5:6:11:12:24:36";  // 가맹점에서 사용할 할부 개월수 설정
//$cardQuotaBase = $cfg[pg][quotaCard];  // 가맹점에서 사용할 할부 개월수 설정

//
//###################################
// 2. 가맹점 확인을 위한 signKey를 해시값으로 변경 (SHA-256방식 사용)
//###################################
$mKey = hash("sha256", $signKey);

/*
  //*** 위변조 방지체크를 signature 생성 ***
  oid, price, timestamp 3개의 키와 값을
  key=value 형식으로 하여 '&'로 연결한 하여 SHA-256 Hash로 생성 된값
  ex) oid=INIpayTest_1432813606995&price=819000&timestamp=2012-02-01 09:19:04.004
 * key기준 알파벳 정렬
 * timestamp는 반드시 signature생성에 사용한 timestamp 값을 timestamp input에 그데로 사용하여야함
 */
//$params = "oid=" . $orderNumber . "&price=" . $price . "&timestamp=" . $timestamp;
$params = array(
    "oid" => $orderNumber,
    "price" => $price,
    "timestamp" => $timestamp
);

//$sign = $SignatureUtil->makeSignature($params, "sha256");
$sign = $SignatureUtil->makeSignature($params);

/* 기타 */
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	$siteDomain = "https://".USER_HOST."/pg/INIPayStdWeb"; 	//가맹점 도메인 입력
else
	$siteDomain = "http://".USER_HOST."/pg/INIPayStdWeb"; 	//가맹점 도메인 입력


$r_gopaymethod = array(
	'c'		=> 'Card',
	'o'		=> 'DirectBank',
	'oe'	=> 'DirectBank',
	'v'		=> 'VBank',
	've'	=> 'VBank',
	'h'		=> 'HPP',
	/*'b'	=> '무통장입금',*/
	/*'e'	=> '적립금결제',*/
	/*'ve'=> '에스크로(가상계좌)',*/
	/*'oe'=> '에스크로(계좌이체)',*/
	/*'t'	=> '신용거래',*/
	);

$gopaymethod = $r_gopaymethod[substr($post[paymethod],0,1)];

if (!$gopaymethod) $gopaymethod = "Card";


// 페이지 URL에서 고정된 부분을 적는다. 
// Ex) returnURL이 http://localhost:8082/demo/INIpayStdSample/INIStdPayReturn.jsp 라면
//                 http://localhost:8082/demo/INIpayStdSample 까지만 기입한다.


	$_POST[timestamp] = $timestamp;
	$_POST[signature] = $sign;
	$_POST[returnUrl] = "$siteDomain/INIStdPayReturn.php";
	$_POST[mKey] = $mKey;
	$_POST[closeUrl] = "$siteDomain/close.php";
	$_POST[popupUrl] = "$siteDomain/popup.php";
	$_POST[gopaymethod] = $gopaymethod;
	
	$_POST[cardNoInterestQuota] = $cardNoInterestQuota;
?>
