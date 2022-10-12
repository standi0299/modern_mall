<?php
	include_once "../../lib/library.php";
	
	include_once "./INIPayUtil.php";

 //*******************************************************************************
 // FILE NAME : mx_rnoti.php
 // FILE DESCRIPTION :
 // 이니시스 smart phone 결제 결과 수신 페이지 샘플
 // 기술문의 : ts@inicis.com
 // HISTORY
 // 2010. 02. 25 최초작성
 // 2010  06. 23 WEB 방식의 가상계좌 사용시 가상계좌 채번 결과 무시 처리 추가(APP 방식은 해당 없음!!)
 // WEB 방식일 경우 이미 P_NEXT_URL 에서 채번 결과를 전달 하였으므로,
 // 이니시스에서 전달하는 가상계좌 채번 결과 내용을 무시 하시기 바랍니다.
 //*******************************************************************************

   $PGIP = $_SERVER['REMOTE_ADDR'];
  
   if($PGIP == "211.219.96.165" || $PGIP == "118.129.210.25" || $PGIP == "183.109.71.153" || $PGIP == "39.115.212.9" || $PGIP == "203.238.37.15")    //PG에서 보냈는지 IP로 체크
   {
     // 이니시스 NOTI 서버에서 받은 Value
			$P_TID;                // 1 거래번호
     	$P_MID;                // 2 상점아이디
     	$P_AUTH_DT;            // 3 승인일자
     	$P_STATUS;            // 4 거래상태 (00:성공, 01:실패)
     	$P_TYPE;            // 5 지불수단
     	$P_OID;                // 6 상점주문번호
     	$P_FN_CD1;            // 7 금융사코드1
     	$P_FN_CD2;            // 8 금융사코드2
     	$P_FN_NM;            // 9 금융사명 (은행명, 카드사명, 이통사명)
     	$P_AMT;                // 10 거래금액
     	$P_UNAME;            // 11 결제고객성명
     	$P_RMESG1;            // 12 결과코드
     	$P_RMESG2;            // 13 결과메시지
     	$P_NOTI;            // 14 노티메시지(상점에서 올린 메시지)
     	$P_AUTH_NO;            // 15 승인번호   

     $P_TID = $_REQUEST[P_TID];
     $P_MID = $_REQUEST[P_MID];
     $P_AUTH_DT = $_REQUEST[P_AUTH_DT];
     $P_STATUS = $_REQUEST[P_STATUS];
     $P_TYPE = $_REQUEST[P_TYPE];
     $P_OID = $_REQUEST[P_OID];
     $P_FN_CD1 = $_REQUEST[P_FN_CD1];
     $P_FN_CD2 = $_REQUEST[P_FN_CD2];
     $P_FN_NM = $_REQUEST[P_FN_NM];
     $P_AMT = $_REQUEST[P_AMT];
     $P_UNAME = $_REQUEST[P_UNAME];
     $P_RMESG1 = $_REQUEST[P_RMESG1];
     $P_RMESG2 = $_REQUEST[P_RMESG2];
     $P_NOTI = $_REQUEST[P_NOTI];
     $P_AUTH_NO = $_REQUEST[P_AUTH_NO];
		 
		 $P_VACCT_NO = $_REQUEST[P_VACCT_NO];		//입금할 계좌 번호  char(20)
	$P_FN_NM = iconv("EUC-KR", "UTF-8", $P_FN_NM);
	$P_UNAME = iconv("EUC-KR", "UTF-8", $P_UNAME);	
	$P_RMESG1 = iconv("EUC-KR", "UTF-8", $P_RMESG1);
	$P_RMESG2 = iconv("EUC-KR", "UTF-8", $P_RMESG2);
					 

			//기본값 설정
  		$step		= 2;
			$orderInputRequest = true;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false 
			$vbankInputFlag = false;				//가상계좌 입금처리
			$payno = $P_OID;     
			$pgcode		= $P_TID;			//거래번호
			$namePayer	= $P_UNAME;
			$escrow		= 0;
			$pglog		= "$payno (".date('Y:m:d H:i:s').")거래번호 : ".$P_TID."승인금액 : ".$P_AMT;
			$PG_Account = $P_AMT;
		
		
			if($P_TYPE == "CARD")    //카드
      {
				$pglog .= "
					결제방법 : 카드결제
					거래번호 : $P_TID
					승인번호 : $P_AUTH_NO
					카드회사 : $P_FN_NM						
					환율적용금액 : $P_AMT						
					";
			}			

     	//WEB 방식의 경우 가상계좌 채번 결과 무시 처리
     	//(APP 방식의 경우 해당 내용을 삭제 또는 주석 처리 하시기 바랍니다.)
      if($P_TYPE == "VBANK")    //결제수단이 가상계좌이며
      {
      	$P_RMESG1_ARR = explode("|", $P_RMESG1);
      	$P_VACT_NUM = str_replace("P_VACCT_NO=", "", $P_RMESG1_ARR[0]);
				$P_EXP_DT = str_replace("P_EXP_DT=", "", $P_RMESG1_ARR[1]);
      	$bankinfo	= $P_FN_NM ." / ". $P_VACT_NUM. " / ".$P_AMT;
				
				//$pglog .= "결제방법 : 가상계좌 - ".$bankinfo;
				        	
				if($P_STATUS == "00" || $P_STATUS == "01") //입금통보 "02" 가 아니면(가상계좌 채번 : 00 또는 01 경우)
        {
       		echo "OK";
          exit;
        }
				
				//가상계좌 입금 통보. 입금통보 "02" 
				if ($P_STATUS == "02"){
					$orderInputRequest = false;					//주문처리 신청 여부, 가상계좌는 입금 확인시 false
					$vbankInputFlag = true;				//가상계좌 입금처리
				}
     	}
			
			
			if ($P_STATUS == "00" || $P_STATUS == "02")
			{
				$PageCall_time = date("H:i:s");

       	$value = array(
       					"file_name" => "INIpayMobile_rnoti.php",
               "PageCall time" => $PageCall_time,
               "P_TID"            => $P_TID,  
               "P_MID"     => $P_MID,  
               "P_AUTH_DT" => $P_AUTH_DT,      
               "P_STATUS"  => $P_STATUS,
               "P_TYPE"    => $P_TYPE,     
               "P_OID"     => $P_OID,  
               "P_FN_CD1"  => $P_FN_CD1,
               "P_FN_CD2"  => $P_FN_CD2,
               "P_FN_NM"   => $P_FN_NM,  
               "P_AMT"     => $P_AMT,  
               "P_UNAME"   => $P_UNAME,  
               "P_RMESG1"  => $P_RMESG1,  
               "P_RMESG2"  => $P_RMESG2,
               "P_NOTI"    => $P_NOTI,  
               "P_AUTH_NO" => $P_AUTH_NO,
               "P_VACT_NUM" => $P_VACT_NUM
               );

            // 결제처리에 관한 로그 기록
        INIPayWriteLog($value, "_rnotiSucess");

       /***********************************************************************************
        ' 위에서 상점 데이터베이스에 등록 성공유무에 따라서 성공시에는 "OK"를 이니시스로 실패시는 "FAIL" 을
        ' 리턴하셔야합니다. 아래 조건에 데이터베이스 성공시 받는 FLAG 변수를 넣으세요
        ' (주의) OK를 리턴하지 않으시면 이니시스 지불 서버는 "OK"를 수신할때까지 계속 재전송을 시도합니다
        ' 기타 다른 형태의 echo "" 는 하지 않으시기 바랍니다
       '***********************************************************************************/
   			include "../pg_pay_seccess.php";					
				
				if ($pgPayReturnMsg == "OK")
				{
					echo "OK";

				} else {						
					echo "FAIL";
					
					//실패 오류를 남기자...
					$value = array(
       			    "P_TID" => $P_TID,  
                    "P_MID" => $P_MID,
                    "MSG" => $pgPayReturnMsg);
					INIPayWriteLog($value, "_rnoti_fail");
				}
			} else {
				$step = -1;					
				$pglog = "거래번호 : $pgcode [" .$P_RMESG1. "/".$P_RMESG2."]";
				
				//실패 오류를 남기자...
				$value = array(
					"file_name" => "INIpayMobile_rnoti_fail.php",
     			"P_TID"            => $P_TID,  
          "P_MID"     => $P_MID,
          "P_AUTH_DT" => $P_AUTH_DT,  
          "MSG" => $pglog);
				INIPayWriteLog($value, "_fail");
		
				//include "../pg_pay_fail.php";					
				echo "FAIL";
			}
         // if(데이터베이스 등록 성공 유무 조건변수 = true)
             //echo "OK"; //절대로 지우지 마세요
         // else
         //     echo "FAIL";
   } else {
   	$value = array(
			"file_name" => "INIpayMobile_rnoti_IP_fail.php",
			"P_AUTH_DT" => makeStorageCode(),
     	"PGIP" => $PGIP,               
    );  	
		INIPayWriteLog($value, "_IP_fail");
			echo "FAIL";
   }	
 ?>
