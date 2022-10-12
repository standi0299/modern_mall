<?

include "../../lib/library.php";

$r_bank = array(
	'02'	=> '한국산업은행',
	'03'	=> '기업은행',
	'04'	=> '국민은행',
	'05'	=> '외환은행',
	'06'	=> '주택은행',
	'07'	=> '수협중앙회',
	'11'	=> '농협중앙회',
	'12'	=> '단위농협',
	'16'	=> '축협중앙회',
	'20'	=> '우리은행',
	'21'	=> '조흥은행',
	'22'	=> '상업은행',
	'23'	=> '제일은행',
	'24'	=> '한일은행',
	'25'	=> '서울은행',
	'26'	=> '신한은행',
	'27'	=> '한미은행',
	'31'	=> '대구은행',
	'32'	=> '부산은행',
	'34'	=> '광주은행',
	'35'	=> '제주은행',
	'37'	=> '전북은행',
	'38'	=> '강원은행',
	'39'	=> '경남은행',
	'41'	=> '비씨카드',
	'53'	=> '씨티은행',
	'54'	=> '홍콩상하이은행',
	'71'	=> '우체국',
	'81'	=> '하나은행',
	'83'	=> '평화은행',
	'87'	=> '신세계',
	'88'	=> '신한은행',
	);

$r_cards = array(
	'01'	=> '외환',
	'03'	=> '롯데',
	'04'	=> '현대',
	'06'	=> '국민',
	'11'	=> 'BC',
	'12'	=> '삼성',
	'13'	=> 'LG',
	'14'	=> '신한',
	'21'	=> '해외비자',
	'22'	=> '해외마스터',
	'23'	=> 'JCB',
	'24'	=> '해외아멕스',
	'25'	=> '해외다이너스',
	);

/* INIsecurepay.php
*
* 이니페이 플러그인을 통해 요청된 지불을 처리한다.
* 지불 요청을 처리한다.
* 코드에 대한 자세한 설명은 매뉴얼을 참조하십시오.
* <주의> 구매자의 세션을 반드시 체크하도록하여 부정거래를 방지하여 주십시요.
*  
* http://www.inicis.com
* Copyright (C) 2006 Inicis Co., Ltd. All rights reserved.
*/

/****************************
* 0. 세션 시작             *
****************************/
session_start();

/**************************
* 1. 라이브러리 인클루드 *
**************************/
require("libs/INILib.php");
	
/***************************************
* 2. INIpay50 클래스의 인스턴스 생성 *
***************************************/
$inipay = new INIpay50;

/*********************
* 3. 지불 정보 설정 *
*********************/
$inipay->SetField("inipayhome",$_SERVER['DOCUMENT_ROOT']."/pg/INIpay50"); // 이니페이 홈디렉터리(상점수정 필요)
$inipay->SetField("type", "securepay");                     // 고정 (절대 수정 불가)
$inipay->SetField("pgid", "INIphp".$pgid);                  // 고정 (절대 수정 불가)
$inipay->SetField("subpgip","203.238.3.10");                // 고정 (절대 수정 불가)

$inipay->SetField("admin", $_SESSION['INI_ADMIN']);			// 키패스워드(상점아이디에 따라 변경)
$inipay->SetField("debug", "true");                         // 로그모드("true"로 설정하면 상세로그가 생성됨.)
$inipay->SetField("uid", $uid);                             // INIpay User ID (절대 수정 불가)
$inipay->SetField("uip", getenv("REMOTE_ADDR"));            // 고정 (절대 수정 불가)
$inipay->SetField("goodname",iconv("UTF-8","EUC-KR",$goodname));		// 상품명
$inipay->SetField("currency", $currency);					// 화폐단위

$inipay->SetField("mid", $_SESSION['INI_MID']);				// 상점아이디
$inipay->SetField("rn", $_SESSION['INI_RN']);				// 웹페이지 위변조용 RN값
$inipay->SetField("price", $_SESSION['INI_PRICE']);			// 가격
$inipay->SetField("enctype", $_SESSION['INI_ENCTYPE']);		// 고정 (절대 수정 불가)

/*----------------------------------------------------------------------------------------
price 등의 중요데이터는
브라우저상의 위변조여부를 반드시 확인하셔야 합니다.

결제 요청페이지에서 요청된 금액과
실제 결제가 이루어질 금액을 반드시 비교하여 처리하십시오.

설치 메뉴얼 2장의 결제 처리페이지 작성부분의 보안경고 부분을 확인하시기 바랍니다.
적용참조문서: 이니시스홈페이지->가맹점기술지원자료실->기타자료실 의
			  '결제 처리 페이지 상에 결제 금액 변조 유무에 대한 체크' 문서를 참조하시기 바랍니다.
예제)
원 상품 가격 변수를 OriginalPrice 하고  원 가격 정보를 리턴하는 함수를 Return_OrgPrice()라 가정하면
다음 같이 적용하여 원가격과 웹브라우저에서 Post되어 넘어온 가격을 비교 한다.

$OriginalPrice = Return_OrgPrice();
$PostPrice = $_SESSION['INI_PRICE']; 
if ( $OriginalPrice != $PostPrice )
{
	//결제 진행을 중단하고  금액 변경 가능성에 대한 메시지 출력 처리
	//처리 종료 
}

----------------------------------------------------------------------------------------*/
$inipay->SetField("buyername", iconv("UTF-8","EUC-KR",$buyername));	// 구매자 명
$inipay->SetField("buyertel",  $buyertel);				// 구매자 연락처(휴대폰 번호 또는 유선전화번호)
$inipay->SetField("buyeremail",$buyeremail);			// 구매자 이메일 주소
$inipay->SetField("paymethod", $paymethod);				// 지불방법 (절대 수정 불가)
$inipay->SetField("encrypted", $encrypted);				// 암호문
$inipay->SetField("sessionkey",$sessionkey);			// 암호문
$inipay->SetField("url","http://".$_SERVER[HTTP_HOST]);	// 실제 서비스되는 상점 SITE URL로 변경할것
$inipay->SetField("cardcode", $cardcode);				// 카드코드 리턴
$inipay->SetField("parentemail", $parentemail);			// 보호자 이메일 주소(핸드폰 , 전화결제시에 14세 미만의 고객이 결제하면  부모 이메일로 결제 내용통보 의무, 다른결제 수단 사용시에 삭제 가능)
	
/*-----------------------------------------------------------------*
 * 수취인 정보 *                                                   *
 *-----------------------------------------------------------------*
 * 실물배송을 하는 상점의 경우에 사용되는 필드들이며               *
 * 아래의 값들은 INIsecurepay.html 페이지에서 포스트 되도록        *
 * 필드를 만들어 주도록 하십시요.                                  *
 * 컨텐츠 제공업체의 경우 삭제하셔도 무방합니다.                   *
 *-----------------------------------------------------------------*/

$inipay->SetField("recvname",$recvname);		// 수취인 명
$inipay->SetField("recvtel",$recvtel);			// 수취인 연락처
$inipay->SetField("recvaddr",$recvaddr);		// 수취인 주소
$inipay->SetField("recvpostnum",$recvpostnum);  // 수취인 우편번호
$inipay->SetField("recvmsg",$recvmsg);			// 전달 메세지

$inipay->SetField("joincard",$joincard);		// 제휴카드코드
$inipay->SetField("joinexpire",$joinexpire);	// 제휴카드유효기간
$inipay->SetField("id_customer",$id_customer);	//user_id

	
/****************
 * 4. 지불 요청 *
 ****************/
$inipay->startAction();
	
/****************************************************************************************************************
* 5. 결제  결과                  
* 
*  1 모든 결제 수단에 공통되는 결제 결과 데이터
* 	거래번호 : $inipay->GetResult('TID')
* 	결과코드 : $inipay->GetResult('ResultCode') ("00"이면 지불 성공)
* 	결과내용 : $inipay->GetResult('ResultMsg') (지불결과에 대한 설명)
* 	지불방법 : $inipay->GetResult('PayMethod') (매뉴얼 참조)
* 	상점주문번호 : $inipay->GetResult('MOID')
*	결제완료금액 : $inipay->GetResult('TotPrice')
*
* 결제 되는 금액 =>원상품가격과  결제결과금액과 비교하여 금액이 동일하지 않다면
* 결제 금액의 위변조가 의심됨으로 정상적인 처리가 되지않도록 처리 바랍니다. (해당 거래 취소 처리)

*
*  2. 신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제 결과 데이터
*      (무통장입금 , 문화 상품권 포함)
* 	이니시스 승인날짜 : $inipay->GetResult('ApplDate') (YYYYMMDD)
* 	이니시스 승인시각 : $inipay->GetResult('ApplTime') (HHMMSS)
*
*  3. 신용카드 결제 결과 데이터
 *				
* 	신용카드 승인번호 : $inipay->GetResult('ApplNum')
* 	할부기간 : $inipay->GetResult('CARD_Quota')
* 	무이자할부 여부 : $inipay->GetResult('CARD_Interest') ("1"이면 무이자할부)
* 	신용카드사 코드 : $inipay->GetResult('CARD_Code') (매뉴얼 참조)
* 	카드발급사 코드 : $inipay->GetResult('CARD_BankCode') (매뉴얼 참조)
* 	본인인증 수행여부 : $inipay->GetResult('CARD_AuthType') ("00"이면 수행)
*      각종 이벤트 적용 여부 : $inipay->GetResult('EventCode')
*
*      ** 달러결제 시 통화코드와  환률 정보 **
*	해당 통화코드 : $inipay->GetResult('OrgCurrency')
*	환율 : $inipay->GetResult('ExchangeRate')
*
*      아래는 "신용카드 및 OK CASH BAG 복합결제" 또는"신용카드 지불시에 OK CASH BAG적립"시에 추가되는 데이터   
* 	OK Cashbag 적립 승인번호 : $inipay->GetResult('OCB_SaveApplNum')           					
* 	OK Cashbag 사용 승인번호 : $inipay->GetResult('OCB_PayApplNum')            				
* 	OK Cashbag 승인일시 : $inipay->GetResult('OCB_ApplDate') (YYYYMMDDHHMMSS)   		
* 	OCB 카드번호 : $inipay->GetResult('OCB_Num')			   						
* 	OK Cashbag 복합결재시 신용카드 지불금액 : $inipay->GetResult('CARD_ApplPrice')     	
* 	OK Cashbag 복합결재시 포인트 지불금액 : $inipay->GetResult('OCB_PayPrice')       	
*	                                                                                
* 4. 실시간 계좌이체 결제 결과 데이터                                               
*                                                                                 
* 	은행코드 : $inipay->GetResult('ACCT_BankCode')                                
*	현금영수증 발행결과코드 : $inipay->GetResult('CSHR_ResultCode')
*	현금영수증 발행구분코드 : $inipay->GetResult('CSHR_Type') 
*														*
* 5. OK CASH BAG 결제수단을 이용시에만  결제 결과 데이터		
* 	OK Cashbag 적립 승인번호 : $inipay->GetResult('OCB_SaveApplNum')           					
* 	OK Cashbag 사용 승인번호 : $inipay->GetResult('OCB_PayApplNum')            				
* 	OK Cashbag 승인일시 : $inipay->GetResult('OCB_ApplDate') (YYYYMMDDHHMMSS)   		
* 	OCB 카드번호 : $inipay->GetResult('OCB_Num')			   						
*														
 * 6. 무통장 입금 결제 결과 데이터							                        *
* 	가상계좌 채번에 사용된 주민번호 : $inipay->GetResult('VACT_RegNum')              					*
* 	가상계좌 번호 : $inipay->GetResult('VACT_Num')                                					*
* 	입금할 은행 코드 : $inipay->GetResult('VACT_BankCode')                           					*
* 	입금예정일 : $inipay->GetResult('VACT_Date') (YYYYMMDD)                      					*
* 	송금자 명 : $inipay->GetResult('VACT_InputName')                                  					*
* 	예금주 명 : $inipay->GetResult('VACT_Name')                                  					*
*														*	
* 7. 핸드폰, 전화 결제 결과 데이터( "실패 내역 자세히 보기"에서 필요 , 상점에서는 필요없는 정보임)             *
 * 	전화결제 사업자 코드 : $inipay->GetResult('HPP_GWCode')                        					*
*														*	
* 8. 핸드폰 결제 결과 데이터								                        *
* 	휴대폰 번호 : $inipay->GetResult('HPP_Num') (핸드폰 결제에 사용된 휴대폰번호)       					*
*														*
* 9. 전화 결제 결과 데이터								                        *
* 	전화번호 : $inipay->GetResult('ARSB_Num') (전화결제에  사용된 전화번호)      						*
* 														*		
* 10. 문화 상품권 결제 결과 데이터							                        *
* 	컬쳐 랜드 ID : $inipay->GetResult('CULT_UserID')	                           					*
*														*
* 11. K-merce 상품권 결제 결과 데이터 (K-merce ID, 틴캐시 아이디 공통사용)                                     *
*      K-merce ID : $inipay->GetResult('CULT_UserID')                                                                       *
*                                                                                                              *
* 12. 모든 결제 수단에 대해 결제 실패시에만 결제 결과 데이터 							*
* 	에러코드 : $inipay->GetResult('ResultErrorCode')                             					*
* 														*
* 13.현금영수증 발급 결과코드 (은행계좌이체시에만 리턴)							*
*    $inipay->GetResult('CSHR_ResultCode')                                                                                     *
*                                                                                                              *
* 14.틴캐시 잔액 데이터                                							*
*    $inipay->GetResult('TEEN_Remains')                                           	                                *
*	틴캐시 ID : $inipay->GetResult('CULT_UserID')													*
* 15.게임문화 상품권							*
*	사용 카드 갯수 : $inipay->GetResult('GAMG_Cnt')                 					        *
*														*
****************************************************************************************************************/

$payno = $inipay->GetResult('MOID');
if (!$payno) $payno = $_POST[oid];

### 로그저장
ob_start();
$fp = fopen("log/xxx/$payno","w");
print_r($inipay);
$ret = ob_get_contents();
fwrite($fp,$ret);
fclose($fp);
ob_end_clean();

$gourl = "../../order/payend.php?payno=$payno";

### 중복결제체크
$data = $db->fetch("select * from exm_pay where payno='$payno'");

/*****/ if ($data[payno] && !$data[paydt]){

$step = 2;
$pgcode = $inipay->GetResult('TID');
$pglog = "$payno (".date('Y:m:d H:i:s').")
거래번호 : ".$inipay->GetResult('TID')."
승인금액 : ".$inipay->GetResult('TotPrice');
$namePayer = $inipay->GetResult('VACT_InputName');

switch ($inipay->GetResult('PayMethod'))
{
	case "Card": case "VCard":	### 카드결제

		$pglog .= "
			결제방법 : 카드결제
			승인번호 : ".$inipay->GetResult('ApplNum')."
			할부기간 : ".$inipay->GetResult('CARD_Quota')."
			무이자 : ".$inipay->GetResult('CARD_Interest')."
			카드회사 : ".$r_cards[$inipay->GetResult('CARD_Code')]."
		";

		break;
	case "DirectBank":			### 계좌이체

		$pglog .= "
			결제방법 : 계좌이체
			계좌이체 : ".$r_bank[$inipay->GetResult('ACCT_BankCode')]." ".$inipay->GetResult('CSHR_ResultCode')." ".$inipay->GetResult('CSHR_Type');

		break;
	case "VBank":				### 가상계좌

		$step = 1;
		$bankinfo = $r_bank[$inipay->GetResult('VACT_BankCode')]." ".$inipay->GetResult('VACT_Num');

		$pglog .= "
		결제방법 : 가상계좌
		가상계좌 : ".$bankinfo;

		break;

	case "HPP":					### 핸드폰

		$pglog .= "
			결제방법 : 핸드폰결제
			결제번호 : {$inipay->GetResult('HPP_Num')}
		";

		break;
	case "BCSH":

		$settlelog .= "
		결제방법 : 상품권결제
		결제번호 : ".$inipay->GetResult('CULT_UserID');

	break;
}

if (!strcmp($inipay->GetResult('ResultCode'),"00") ){	### 카드결제 성공

	if ($step==2) $qr = "paydt = now(),";

	### 실데이타 저장
	$db->query("
	update exm_pay set $qr
		pglog		= '$pglog',
		pgcode		= '$pgcode',
		paystep		= '$step',
		bankinfo	= '$bankinfo'
	where payno='$payno'"
	);

	$db->query("
	update exm_ord_item set
		itemstep		= '$step'
	where payno='$payno'"
	);

	if ($step == 1){
		include_once "../../lib/nusoap/lib/nusoap.php";
		$client = new soapclient("http://podstation.ilark.co.kr/StationWebService/StationWebService.asmx?WSDL",true);
		$client->xml_encoding = "UTF-8";
		$client->soap_defencoding = "UTF-8";
		$client->decode_utf8 = false;

		$query = "select * from exm_ord_item where payno = '$payno'";
		$res = $db->query($query);
		while ($tmp = $db->fetch($res)){
			set_stock($tmp[goodsno],$tmp[optno],$tmp[ea]*-1);
			if ($tmp[storageid]){
				list($podskind)	= $db->fetch("select podskind from exm_goods where goodsno = '$tmp[storageid]'",1);
				if (in_array($podskind,$r_podskind20)){ /* 2.0 상품 */
					$soap_url	= "http://podstation20.ilark.co.kr/CommonRef/StationWebService/StationWebService.asmx?WSDL";
				} else {
					$soap_url	= "http://podstation.ilark.co.kr/StationWebService/StationWebService.asmx?WSDL";
				}
				$client = new soapclient($soap_url,true);
				$ret = $client->call('UpdateStorageDate',array("storageid"=>$tmp[storageid]));
			}
         
         //장바구니 번호를 담는다.
         if ($cfg[skin] == "pretty"){
            $cartno_array[] = $tmp[cartno];

            setPrettyGoodsMappingData($data[mid], $tmp[ea], $tmp[goodsno]);
         }
		}
	}

	if ($step == 2){
		include_once "../../lib/nusoap/lib/nusoap.php";
		$client = new soapclient("http://podstation.ilark.co.kr/StationWebService/StationWebService.asmx?WSDL",true);
		$client->xml_encoding = "UTF-8";
		$client->soap_defencoding = "UTF-8";
		$client->decode_utf8 = false;

		$query = "select * from exm_ord_item where payno = '$payno'";
		$res = $db->query($query);
		while ($tmp = $db->fetch($res)){
		   set_stock($tmp[goodsno],$tmp[optno],$tmp[ea]*-1);
			set_pod_pay($tmp[payno],$tmp[ordno],$tmp[ordseq]);
			set_acc_desc($tmp[payno],$tmp[ordno],$tmp[ordseq],2);
         if($cfg[skin] != "pretty")
			   $db->query("delete from exm_cart where cartno = '$tmp[cartno]'");
         
         //장바구니 번호를 담는다.
         if ($cfg[skin] == "pretty"){
            $cartno_array[] = $tmp[cartno];

            setPrettyGoodsMappingData($data[mid], $tmp[ea], $tmp[goodsno]);
         }
		}
      
		order_sms($payno);
	}
   
   if($cartno_array){
      //mapping 테이블의 상태 변경 / 16.07.08 / kjm
      $cartno_array = implode(", ", $cartno_array);
      setPrettyOrderMappingKind($cartno_array);
   }

	### 적립금 소진
	if ($data[dc_emoney] > 0){
		//set_emoney($data[mid],"상품구입시 사용",-$data[dc_emoney],$payno);
      setPayNUseEmoney($data[cid], $data[mid], $data[dc_emoney], $data[payprice], $data[payno]);
	}

	$paydata = $db->fetch("select * from exm_pay where payno = '$payno'");
	$query = "select * from exm_ord_item where payno = '$payno'";
	$res = $db->query($query);
	while ($tmp = $db->fetch($res)){
		$paydata[item][] = $tmp;
	}
	
	//20190118 / minks / 배송방법 추가
	list($ordershiptype) = $db->fetch("select order_shiptype from exm_ord where payno='$payno' order by ordno limit 1", 1);
	$paydata[ordershiptype] = ($cfg[skin_theme] == "P1" && $ordershiptype == 1) ? $r_order_shiptype[0] : $r_order_shiptype[$ordershiptype];
	
	if ($step==2){
		autoMail("payment",$paydata[orderer_email],$paydata);
      
      //관리자에게 보내기
      autoMailAdmin("admin_payment",$cfg[email1],$paydata);
      
		autoSms("입금확인",$paydata[orderer_mobile],$paydata);
	} else {
		autoMail("order",$paydata[orderer_email],$paydata);
            
      //관리자에게 보내기
      autoMailAdmin("admin_order",$cfg[email1],$data);
      
        if($paydata[paymethod] == "v") {
           $paydata[inicis_pay_limit] = date("Ymd",strtotime(date("Y-m-d")." + 5 days"));
           autoSms("접수내역",$paydata[orderer_mobile],$paydata);
        }   
        else
           autoSms("주문접수",$paydata[orderer_mobile],$paydata);

	}

	$query = "select * from exm_ord_item where payno = '$payno'";
	$res = $db->query($query);
	while ($tmp = $db->fetch($res)){
		if ($tmp[coupon_areservesetno]){
			$db->query("
			update exm_coupon_set set
				coupon_use		= 1,
				payno			= $payno,
				ordno			= $tmp[ordno],
				ordseq			= $tmp[ordseq],
				coupon_usedt	= now()
			where no = '$tmp[coupon_areservesetno]'
			");
		}

		if ($tmp[dc_couponsetno]){
			$db->query("
			update exm_coupon_set set
				coupon_use		= 1,
				payno			= $payno,
				ordno			= $tmp[ordno],
				ordseq			= $tmp[ordseq],
				coupon_usedt	= now()
			where no = '$tmp[dc_couponsetno]'
			");
		}
	}

} else { // 주문실패

	$step = -1;
	$pglog = "거래번호 : $pgcode
	[".$inipay->GetResult('ResultCode')."]
	".$inipay->GetResult('ResultMsg')."
	".$inipay->GetResult('ResultErrorCode')."
	";

	$db->query("
	update exm_pay set
		pglog	= '$pglog',
		paystep	= '$step'
	where payno='$payno' and paydt is null"
	);

	$db->query("
	update exm_ord_item set
		itemstep		= '$step'
	where payno='$payno'"
	);

	$query = "select * from exm_ord_item where payno = '$payno'";
	$res = $db->query($query);
	while ($tmp = $db->fetch($res)){
		if ($tmp[coupon_areservesetno]){
			$db->query("
			update exm_coupon_set set
				coupon_use		= 0,
				payno			= null,
				ordno			= null,
				ordseq			= null,
				coupon_usedt	= null
			where no = '$tmp[coupon_areservesetno]'
			");
		}

		if ($tmp[dc_couponsetno]){
			$db->query("
			update exm_coupon_set set
				coupon_use		= 0,
				payno			= null,
				ordno			= null,
				ordseq			= null,
				coupon_usedt	= null
			where no = '$tmp[dc_couponsetno]'
			");
		}
      
      //블루포토 - 결제 실패 시 맵핑 테이블의 상태를 R로 변경해준다.
      if($cfg[skin] == "pretty")
         $db->query("update tb_pretty_cart_mapping set cart_state = 'R' where cartno = '$tmp[cartno]'");
	}
	
	$gourl = "../../order/payfail.php?payno=$payno";

} // end of all proc..
?>
 
<script>
function goResult(url){
   location.replace(url);
}
goResult("<?=$gourl?>");
</script>

<? } else if ($data[step]==9){ ?>

<script>
function goResult(url){
   location.replace(url);
}
goResult("<?=$gourl?>");
</script>

<? } /*****/ ?>