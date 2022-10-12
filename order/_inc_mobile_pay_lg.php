<?php
	session_start();
    /*
     * [결제 인증요청 페이지(STEP2-1)]
     *
     * 샘플페이지에서는 기본 파라미터만 예시되어 있으며, 별도로 필요하신 파라미터는 연동메뉴얼을 참고하시어 추가 하시기 바랍니다.     
     */

    /*
     * 1. 기본결제 인증요청 정보 변경
     * 
     * 기본정보를 변경하여 주시기 바랍니다.(파라미터 전달시 POST를 사용하세요)
     */
    $CST_PLATFORM               = "service";      //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
    //$CST_PLATFORM               = "test";      //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
    $CST_MID                    = $cfg[pg][lgd_mid];           //상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
                                                                        //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
    $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)
    $LGD_OID                    = $_POST[payno];           //주문번호(상점정의 유니크한 주문번호를 입력하세요)
    $LGD_AMOUNT                 = $_POST[payprice];        //결제금액("," 를 제외한 결제금액을 입력하세요)
    //$LGD_BUYER                  = $_POST[orderer_name];         //구매자명
    $LGD_BUYER                  = $_POST[receiver_name];         //구매자명				//모바일은 비회원이라 구매자명이 배송받는사람 이름이다.			20160108		chunter
    
    $LGD_PRODUCTINFO            = $pg_goodsnm;   //상품명
    $LGD_BUYEREMAIL             = $_POST[orderer_email];    //구매자 이메일
        
    
    if ($_POST[paymethod] =='c')  $_POST[LGD_CUSTOM_FIRSTPAY] = "SC0010";			//<!-- 신용카드 -->
    if ($_POST[paymethod] =='o')  $_POST[LGD_CUSTOM_FIRSTPAY] = "SC0030";			//<!-- 계좌이체 -->
    if ($_POST[paymethod] =='v')  $_POST[LGD_CUSTOM_FIRSTPAY] = "SC0040";			//<!-- 가상계좌(무통장) -->
    if ($_POST[paymethod] =='h')  $_POST[LGD_CUSTOM_FIRSTPAY] = "SC0060";			//<!-- 휴대폰 -->
    
    $LGD_CUSTOM_FIRSTPAY        = $_POST[LGD_CUSTOM_FIRSTPAY];    //상점정의 초기결제수단
    $LGD_TIMESTAMP              = date(YmdHms);                         //타임스탬프
    $LGD_TIMESTAMP              = $_POST[lgd_timestamp];                         //타임스탬프
    
    $LGD_CUSTOM_SKIN            = "red";                        //상점정의 결제창 스킨
    
		//$configPath 				= "C:/lgdacom"; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정. 	    
		$configPath 				= dirname(__FILE__) ."/../pg/smartXPay/lgdacom"; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.
    
    /*
     * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다. 
     */    
    //$LGD_CASNOTEURL				= "http://상점URL/cas_noteurl.php";
		$LGD_CASNOTEURL				= "http://$_SERVER[HTTP_HOST]/pg/smartXPay/SmartXPayVbankSave.php";    
		//$LGD_CASNOTEURL				= "http://$_SERVER[HTTP_HOST]/pg/smartXPay/note_url.php";

    /*
     * LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
     */    
    //$LGD_RETURNURL				= "http://상점URL/returnurl.php";
		$LGD_RETURNURL				= "http://$_SERVER[HTTP_HOST]/pg/smartXPay/SmartXPayResult.php";
		
		
	
    /*
     * ISP 카드결제 연동중 모바일ISP방식(고객세션을 유지하지않는 비동기방식)의 경우, LGD_KVPMISPNOTEURL/LGD_KVPMISPWAPURL/LGD_KVPMISPCANCELURL를 설정하여 주시기 바랍니다. 
     */    
    //$LGD_KVPMISPNOTEURL       	= "http://상점URL/note_url.php";
    //$LGD_KVPMISPWAPURL			= "http://상점URL/mispwapurl.php?LGD_OID=".$LGD_OID;   //ISP 카드 결제시, URL 대신 앱명 입력시, 앱호출함 
    //$LGD_KVPMISPCANCELURL     	= "http://상점URL/cancel_url.php";
		
		$LGD_KVPMISPNOTEURL       	= "http://$_SERVER[HTTP_HOST]/pg/smartXPay/SmartXPaySave.php";
    $LGD_KVPMISPWAPURL			= "http://$_SERVER[HTTP_HOST]/pg/smartXPay/SmartXPayISPWAP.php?LGD_OID=".$LGD_OID;   //ISP 카드 결제시, URL 대신 앱명 입력시, 앱호출함 
    $LGD_KVPMISPCANCELURL     	= "http://$_SERVER[HTTP_HOST]/pg/smartXPay/SmartXPayCancelUrl.php";
    
    /*
     *************************************************
     * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
     * 
     * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
     *************************************************
     *
     * 해쉬 암호화 적용( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
     * LGD_MID          : 상점아이디
     * LGD_OID          : 주문번호
     * LGD_AMOUNT       : 금액
     * LGD_TIMESTAMP    : 타임스탬프
     * LGD_MERTKEY      : 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
     *
     * MD5 해쉬데이터 암호화 검증을 위해
     * LG유플러스에서 발급한 상점키(MertKey)를 환경설정 파일(lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
     */
    require_once("../pg/smartXPay/lgdacom/XPayClient.php");
    $xpay = &new XPayClient($configPath, $CST_PLATFORM);
   	$xpay->Init_TX($LGD_MID);
    $LGD_HASHDATA = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$xpay->config[$LGD_MID]);
		$payReqMap['LGD_HASHDATA_KEY']           = $LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$xpay->config[$LGD_MID];              // MD5 해쉬암호값
    $LGD_CUSTOM_PROCESSTYPE = "TWOTR";
    /*
     *************************************************
     * 2. MD5 해쉬암호화 (수정하지 마세요) - END
     *************************************************
     */
    // 아이폰 앱 일경우 window type을 submit으로 바꿈 (원래 submit)
    if($_POST[mobile_type] == "Y"){
      $CST_WINDOW_TYPE = "submit";                                       // 수정불가
    } else {                                    
      $CST_WINDOW_TYPE = "iframe";                                       // 수정불가
    }
    //$CST_WINDOW_TYPE = "popup";                                       // 수정불가
    
    $payReqMap['CST_PLATFORM']           = $CST_PLATFORM;              // 테스트, 서비스 구분
    $payReqMap['CST_WINDOW_TYPE']        = $CST_WINDOW_TYPE;           // 수정불가
    $payReqMap['CST_MID']                = $CST_MID;                   // 상점아이디
    $payReqMap['LGD_MID']                = $LGD_MID;                   // 상점아이디
    $payReqMap['LGD_OID']                = $LGD_OID;                   // 주문번호
    $payReqMap['LGD_BUYER']              = $LGD_BUYER;            	   // 구매자
    $payReqMap['LGD_PRODUCTINFO']        = $LGD_PRODUCTINFO;     	   // 상품정보
    $payReqMap['LGD_AMOUNT']             = $LGD_AMOUNT;                // 결제금액
    $payReqMap['LGD_BUYEREMAIL']         = $LGD_BUYEREMAIL;            // 구매자 이메일
    $payReqMap['LGD_CUSTOM_SKIN']        = $LGD_CUSTOM_SKIN;           // 결제창 SKIN
    $payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;    // 트랜잭션 처리방식
    $payReqMap['LGD_TIMESTAMP']          = $LGD_TIMESTAMP;             // 타임스탬프
    $payReqMap['LGD_HASHDATA']           = $LGD_HASHDATA;              // MD5 해쉬암호값
    $payReqMap['LGD_RETURNURL']   		 = $LGD_RETURNURL;      	   // 응답수신페이지
    $payReqMap['LGD_VERSION']         	 = "PHP_SmartXPay_1.0";		   // 버전정보 (삭제하지 마세요)
    $payReqMap['LGD_CUSTOM_FIRSTPAY']  	 = $LGD_CUSTOM_FIRSTPAY;	   // 디폴트 결제수단
		$payReqMap['LGD_CUSTOM_SWITCHINGTYPE']  = "SUBMIT";	       // 신용카드 카드사 인증 페이지 연동 방식
		$payReqMap['LGD_ENCODING']  = "UTF-8";	       // 결제창 호출문자 인코딩방식   (기본값: EUC-KR) 
		$payReqMap['LGD_ENCODING_NOTEURL']  = "UTF-8";	       // 결제창 호출문자 인코딩방식   (기본값: EUC-KR)
    $payReqMap['LGD_ENCODING_RETURNURL']  = "UTF-8";	       // 결제창 호출문자 인코딩방식   (기본값: EUC-KR)
    
    $payReqMap['LGD_CUSTOM_USABLEPAY']  	 = $LGD_CUSTOM_FIRSTPAY;	   // 상점 정의 결제수단
    $payReqMap['LGD_ESCROW_USEYN']  	 = "N";	   // 에스크로 적용 여부 Y : 에스크로 적용, N : 에스크로 미적용
    
    
	
    /*
    ****************************************************
    * 안드로이드폰 신용카드 ISP(국민/BC)결제에만 적용 (시작)*
    ****************************************************

    (주의)LGD_CUSTOM_ROLLBACK 의 값을  "Y"로 넘길 경우, LG U+ 전자결제에서 보낸 ISP(국민/비씨) 승인정보를 고객서버의 note_url에서 수신시  "OK" 리턴이 안되면  해당 트랜잭션은  무조건 롤백(자동취소)처리되고,
    LGD_CUSTOM_ROLLBACK 의 값 을 "C"로 넘길 경우, 고객서버의 note_url에서 "ROLLBACK" 리턴이 될 때만 해당 트랜잭션은  롤백처리되며  그외의 값이 리턴되면 정상 승인완료 처리됩니다.
    만일, LGD_CUSTOM_ROLLBACK 의 값이 "N" 이거나 null 인 경우, 고객서버의 note_url에서  "OK" 리턴이  안될시, "OK" 리턴이 될 때까지 3분간격으로 2시간동안  승인결과를 재전송합니다.
    */

    $payReqMap['LGD_CUSTOM_ROLLBACK']    = "Y";			   	   				     // 비동기 ISP에서 트랜잭션 처리여부
    $payReqMap['LGD_KVPMISPNOTEURL']  	 = $LGD_KVPMISPNOTEURL;			   // 비동기 ISP(ex. 안드로이드) 승인결과를 받는 URL
    $payReqMap['LGD_KVPMISPWAPURL']  		 = $LGD_KVPMISPWAPURL;			   // 비동기 ISP(ex. 안드로이드) 승인완료후 사용자에게 보여지는 승인완료 URL
    $payReqMap['LGD_KVPMISPCANCELURL']   = $LGD_KVPMISPCANCELURL;		   // ISP 앱에서 취소시 사용자에게 보여지는 취소 URL
    
    
    

    /*
    ****************************************************
    * 안드로이드폰 신용카드 ISP(국민/BC)결제에만 적용    (끝) *
    ****************************************************
    */

    // 안드로이드 에서 신용카드 적용  ISP(국민/BC)결제에만 적용 (선택)
    // $payReqMap['LGD_KVPMISPAUTOAPPYN'] = "Y";
    // Y: 안드로이드에서 ISP신용카드 결제시, 고객사에서 'App To App' 방식으로 국민, BC카드사에서 받은 결제 승인을 받고 고객사의 앱을 실행하고자 할때 사용

    // 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 .
    $payReqMap['LGD_CASNOTEURL'] = $LGD_CASNOTEURL;               // 가상계좌 NOTEURL

    //Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
    $payReqMap['LGD_RESPCODE']           = "";
    $payReqMap['LGD_RESPMSG']            = "";
    $payReqMap['LGD_PAYKEY']             = "";

    $_SESSION['PAYREQ_MAP'] = $payReqMap;
?>
