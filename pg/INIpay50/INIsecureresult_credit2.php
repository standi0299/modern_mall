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
$inipay->SetField("inipayhome",$_SERVER[DOCUMENT_ROOT]."/pg/INIpay50"); // 이니페이 홈디렉터리(상점수정 필요)
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

$payno = $inipay->GetResult('MOID');
if (!$payno) $payno = $_POST[oid];
$payprice = $_POST[optionValue];
  
### 로그저장
ob_start();
$fp = fopen("log/xxx/$payno","w");
print_r($inipay);
$ret = ob_get_contents();
fwrite($fp,$ret);
fclose($fp);
ob_end_clean();

//이동 url 고정
$gourl = "/pay/credit_history.php";

$pgcode = $inipay->GetResult('TID');
$pglog = "$payno (".date('Y:m:d H:i:s').")
거래번호 : ".$inipay->GetResult('TID')."
승인금액 : ".$inipay->GetResult('TotPrice');
$namePayer = $inipay->GetResult('VACT_InputName');
$p_date = $inipay->GetResult('$p_date');

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
}

//금액을 결제하는 것이기 때문에 바로 결제 진행
if (!strcmp($inipay->GetResult('ResultCode'),"00") ){	### 카드결제 성공

	### 실데이타 저장
    //결제된 금액만큼 현재 신용거래2 금액 계산
	list($credit_member) = $db->fetch("select credit_member where exm_member where cid = '$cid' and mid = '$_POST[userid]'",1);
    //신용거래2 금액을 결제하는 것이기 때문에 현재 신용거래2 금액과 결제금액을 더해준다.
	$credit_member = $credit_member + $payprice;
    
    //포인트 계산, 예치금 충전시 사용 / 14.10.15 / kjm
    /*
    list($button) = $db->fetch("select value from exm_config where cid = '$cid' and config = 'deposit_list'",1);
    $button = unserialize($button);
    
    $loop = array();
    foreach($button as $key => $value){
        if($button[$key][0] == $payprice) $point = $button[$key][1];
    }
    $point = $payprice * $point / 100;
    */
	
    //카드결제 성공시 현재 신용거래2 금액 업데이트
    //payno는 결제 성공, 실패를 알려주는 문구를 넣어준다.
    //payno에 문구를 넣을지는 아직 미정 / 14.10.10 / kjm
    $db->query("update exm_member set credit_member = '$credit_member' where cid = '$cid' and mid = '$_POST[userid]'");
    
    //내역 테이블 데이터 insert
    $db->query("insert into tb_deposit_history set
                payno = '사용자 미수금 결제 성공',
                cid = '$cid',
                mid = '$_POST[userid]',
                credit2_user_pay = '$payprice',
                regdt = now(),
                memo = '$pglog'
                ");

} else { // 주문실패
    if (!strcmp($inipay->GetResult('ResultCode'),"01")){ ### 결제 취소
        //$gourl = "/mypage/index.php";
    } else {
      	$pglog = "거래번호 : $pgcode
      	[".$inipay->GetResult('ResultCode')."]
      	".$inipay->GetResult('ResultMsg')."
      	".$inipay->GetResult('ResultErrorCode')."
      	";
        
        //결제 실패시 내역 테이블 데이터 insert
        //payno는 결제 성공, 실패를 알려주는 문구를 넣어준다.
      	$db->query("
      	insert into tb_deposit_history set
            payno = '사용자 미수금 결제 실패',
      		cid = '$cid',
      		mid = '$_POST[userid]',
      		regdt = now(),
      		memo = '$pglog'
   		");
        //$gourl = "/mypage/index.php";
    }
}// end of all proc..
?>
<script>
function goResult(url){
    //부모창 replace
    opener.parent.location.replace(url);
    //팝업 창 닫음(수정 가능성 있음)
    self.close();
}
goResult("<?=$gourl?>");
</script>