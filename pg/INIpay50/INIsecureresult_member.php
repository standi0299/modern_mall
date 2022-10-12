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
  
  //결제 구분     //상품 결제 이외의 결제 모듈을 하나로 통합하기 위한 구분값      20141219    chunter
  $pgPayKind = $inipay->GetResult('pg_pay_kind');
  if (! $pgPayKind) $pgPayKind = "fix_member";        //정액 회원
  $pgPayPrice = $inipay->GetResult('TotPrice');       //결제 금액

  $payno = $inipay->GetResult('MOID');
  if (!$payno) $payno = $_POST[oid];
  
  ### 로그저장
  ob_start();
  $fp = fopen("log/xxx/$payno","w");
  //debug($inipay);
  $ret = ob_get_contents();
  fwrite($fp,$ret);
  fclose($fp);
  ob_end_clean();

  switch ($pgPayKind) {
    case 'fix_member':        //정액 회원
      $gourl = "/member/payment_result.php";  
      ### 중복결제체크
      $query = "select * from tb_fix_member_history where payno='$payno'";
      break;
    case 'deposit':         //예치금  결제
    case 'credit':         //미수금 결제
      $gourl = "/member/payment_result.php";  
      ### 중복결제체크
      $query = "select * from tb_deposit_history where payno='$payno'";
      break;
  }
  
  
  $gourl = "/member/payment_result.php";

  ### 중복결제체크
  $data = $db->fetch($query);

  
  /*****/ 
  if ($data[payno] && !$data[paydt])
  {
    $step = 2;
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
    	### 실데이타 저장
    	
    	switch ($pgPayKind) {
        case 'fix_member':        //정액 회원
          //유효기간 연장 및 내역 저장
          $m_member = new M_member();
		  
		  //이용기간 명칭 추가에 따른 수정 / 16.09.07 / kdk
		  $p_date = substr($data["process_month_day"], 0, strpos($data["process_month_day"], "/"));
		  
          $m_member->update_fix_date($data[mid], $p_date);
          $m_member->updateFixHistoryFromPG($payno, $pgcode, $pglog, $step, $bankinfo);
          break;

        case 'deposit':         //예치금  결제       20141219    chunter
        case 'credit':         //미수금 결제
          //회원 등급별 예치금 가산 % 후 저장  
          $m_member = new M_member();
          $m_data = $m_member->getInfo($cid, $data[mid]);
          
          if ($m_data[credit_member] > 0)
          {
            //예치금 처리
            $priseadd = getDepositSetPercent($cfg[deposit_list], $pgPayPrice);
            $addDepositiEmoney = ($pgPayPrice * $priseadd / 100);     //추가 적립금

            //$db->query("update exm_member set credit_member += $pgPayPrice, emoney += $addDepositiEmoney where cid='$cid' and mid='$data[mid]'");
            $m_member->updateMemberAccountData($cid, $data[mid], $pgPayPrice, $addDepositiEmoney);
            
            //적립금 내역 저장
            if ($addDepositiEmoney > 0)
              //set_emoney($data[mid], "예치금 결제-추가 적립금", $addDepositiEmoney);
							setAddEmoney($cid, $data[mid], $addDepositiEmoney, "예치금 결제-추가 적립금", "", "", "", "", 3650);		//적립금 결제는 10년 유효기간.
						
          }
          else
          {
            //미수금 처리
            if (abs($m_data[credit_member]) < $pgPayPrice)
            {
              //결제 금액이 미수금보다 클경우.. 미수금을 0으로 만들고 나머지는 예치금으로 추가 적립.
              $DepositiPrice = $pgPayPrice - abs($m_data[credit_member]);

              //$b_data = $m_member->getMemberBusinessInfo($cid, $data[mid]);
              $priseadd = getDepositSetPercent($cfg[deposit_list], $DepositiPrice);
              $addDepositiEmoney = ($DepositiPrice * $priseadd / 100);  //추가 적립금

              //$db->query("update exm_member set credit_member = $DepositiPrice, emoney += $addDepositiEmoney where cid='$cid' and mid='$data[mid]'");
              $m_member->updateMemberAccountData($cid, $data[mid], $pgPayPrice, $addDepositiEmoney);
              //적립금 내역 저장
              if ($addDepositiEmoney > 0)
                //set_emoney($data[mid], "예치금 결제-추가 적립금", $addDepositiEmoney);
								setAddEmoney($cid, $data[mid], $addDepositiEmoney, "예치금 결제-추가 적립금", "", "", "", "", 3650);		//적립금 결제는 10년 유효기간.
            }
            else
            {
              //결제금으로 그냥 미수금 차감 처리
              //$db->query("update exm_member set credit_member += $pgPayPrice where cid='$cid' and mid='$data[mid]'");
              $m_member->updateMemberAccountData($cid, $data[mid], $pgPayPrice);
            }
          }
          
          //내역 저장
          $db->query("
            update tb_deposit_history set 
              paydt = now(),
              memo   = '$pglog'
            where payno='$payno'"
            );
          break;
      }
  
    	
    	
    		
    
    } else { // 주문실패
      
      if (!strcmp($inipay->GetResult('ResultCode'),"01")){ ### 결제 취소
        switch ($pgPayKind) {
          case 'fix_member':
            $gourl = "/member/payment.php";
            break;
            
          case 'deposit':         //예치금  결제
          case 'credit':         //미수금 결제
            $gourl = "/member/deposit_payment.php";
            break;
        }
      } else {      
      	$step = -1;
      	$pglog = "거래번호 : $pgcode
      	[".$inipay->GetResult('ResultCode')."]
      	".$inipay->GetResult('ResultMsg')."
      	".$inipay->GetResult('ResultErrorCode')."
      	";
        
        switch ($pgPayKind) {
          case 'fix_member':
            $db->query("update tb_fix_member_history set
                pglog = '$pglog',
                paystep = '$step'
              where payno='$payno' and paydt is null"
              );
            $gourl = "/member/payfail.php?payno=$payno";
            break;
          
          case 'deposit':         //예치금  결제       20141219    chunter
          case 'credit':         //미수금 결제
            //내역 저장          
            $db->query("update tb_deposit_history set memo = '$pglog' where payno='$payno' and paydt is null");
            $gourl = "/member/deposit_payfail.php?payno=$payno";
            break;
        }
      	
        //echo $inipay->GetResult('ResultCode');
        //exit;       
      	
      }
    
    } // end of all proc..
  } else {
    switch ($pgPayKind) {
      case 'fix_member':
        $gourl = "/member/payfail.php?payno=$payno";
        break;
      
      case 'deposit':         //예치금  결제 
      case 'credit':         //미수금 결제
        $gourl = "/member/deposit_payfail.php?payno=$payno";   
        break;
    }    
  }
?>

<script>
function goResult(url){
    location.replace(url);
}
goResult("<?=$gourl?>");
</script>