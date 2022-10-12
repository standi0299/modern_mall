<?php

 	$_POST[P_NOTI] = "";  // 기타주문정보 , 800byte 이내 , 사용법: 변수명=변수값,변수명=변수값

 	//$_POST[P_MID] = "INIpayTest";    //상점ID  ,  테스트: INIpayTest
 	//$_POST[P_MNAME] = "해피정닷컴";  // 상점이름
 	$_POST[P_MNAME] = $_SERVER[HTTP_HOST];  // 상점이름
 	
 	// $P_NEXT_URL : VISA3D (가상계좌를 제외한 기타지불수단) , 인증결과를 브라우저에서 해당 URL로 POST 합니다.
	// $P_NEXT_URL ="https://mobile.inicis.com/smart/testmall/next_url_test.php";
	$_POST[P_NEXT_URL] ="http://$_SERVER[HTTP_HOST]/pg/INIPayMobile/INIpayMobile_PaySave.php";

	// $P_NOTI_URL : 가상계좌, ISP 인증 및 결제 후 상점의 결제 수신 서버URL로 결제 결과를 통보합니다.
 	// $P_NOTI_URL = "http://ts.inicis.com/~esjeong/mobile_rnoti/rnoti.php";
 	$_POST[P_NOTI_URL] = "http://$_SERVER[HTTP_HOST]/pg/INIPayMobile/INIpayMobile_rnoti.php";

 	// $P_RETURN_URL : ISP 인증 및 결제 완료 후 상점으로 이동하기 위한 APP URL 또는 상점 홈페이지 URL
 	// $P_RETURN_URL = "http://ts.inicis.com/~esjeong/mobile_rnoti/rnoti.php";
 	$_POST[P_RETURN_URL] = "http://$_SERVER[HTTP_HOST]/pg/INIPayMobile/result_default.php";
 
?>
