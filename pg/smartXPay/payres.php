<?php
	include_once "../../lib/library.php";
    /*
     * [최종결제요청 페이지(STEP2-2)]
     *
     * LG유플러스으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
     */
	
	/* ※ 중요
	* 환경설정 파일의 경우 반드시 외부에서 접근이 가능한 경로에 두시면 안됩니다.
	* 해당 환경파일이 외부에 노출이 되는 경우 해킹의 위험이 존재하므로 반드시 외부에서 접근이 불가능한 경로에 두시기 바랍니다. 
	* 예) [Window 계열] C:\inetpub\wwwroot\lgdacom ==> 절대불가(웹 디렉토리)
	*/
	
	//$configPath = "C:/lgdacom"; //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정. 
	$configPath 				= dirname(__FILE__) ."/lgdacom"; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

  /*
   *************************************************
   * 1.최종결제 요청 - BEGIN
   *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
   *************************************************
  */
  $CST_PLATFORM               = $HTTP_POST_VARS["CST_PLATFORM"];
  $CST_MID                    = $HTTP_POST_VARS["CST_MID"];
  $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
  $LGD_PAYKEY                 = $HTTP_POST_VARS["LGD_PAYKEY"];

  require_once("./lgdacom/XPayClient.php");
  $xpay = &new XPayClient($configPath, $CST_PLATFORM);
  $xpay->Init_TX($LGD_MID);    
    
  $xpay->Set("LGD_TXNAME", "PaymentByKey");
  $xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
    
  //금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
	//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
	//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
	//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);
	    
  /*
   *************************************************
   * 1.최종결제 요청(수정하지 마세요) - END
   *************************************************
   */

	/*
   * 2. 최종결제 요청 결과처리
   *
   * 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
   */
     
	$bSucc = false; 
  if ($xpay->TX()) {
  	//1)결제결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
    //echo "결제요청이 완료되었습니다.  <br>";
    //echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
    //echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
        
    //echo "거래번호 : " . $xpay->Response("LGD_TID",0) . "<br>";
    //echo "상점아이디 : " . $xpay->Response("LGD_MID",0) . "<br>";
    //echo "상점주문번호 : " . $xpay->Response("LGD_OID",0) . "<br>";
    //echo "결제금액 : " . $xpay->Response("LGD_AMOUNT",0) . "<br>";
    //echo "결과코드 : " . $xpay->Response("LGD_RESPCODE",0) . "<br>";
    //echo "결과메세지 : " . $xpay->Response("LGD_RESPMSG",0) . "<p>";
        
    $keys = $xpay->Response_Names();
    foreach($keys as $name) {
    	//echo $name . " = " . $xpay->Response($name, 0) . "<br>";
    }
      
    
		$xpayCode = $xpay->Response_Code();				
    $xpayMsg = $xpay->Response_Msg();
        
    $xpayLGD_TID = $xpay->Response("LGD_TID",0);
    $xpayLGD_MID = $xpay->Response("LGD_MID",0);
    $xpayLGD_OID = $xpay->Response("LGD_OID",0);
    $xpayLGD_AMOUNT = $xpay->Response("LGD_AMOUNT",0);
    $xpayLGD_RESPCODE = $xpay->Response("LGD_RESPCODE",0);
    $xpayLGD_RESPMSG = $xpay->Response("LGD_RESPMSG",0);
    
		$ordr_idxx = $xpayLGD_OID;			//주문번호
		
       
    if( $xpayCode == "0000" ) 
    {
			$bSucc = true;			
     	//최종결제요청 결과 성공 DB처리
      //echo "최종결제요청 결과 성공 DB처리하시기 바랍니다.<br>";

      //최종결제요청 결과 성공 DB처리 실패시 Rollback 처리
    	$isDBOK = true; //DB처리 실패시 false로 변경해 주세요.
    	if( !$isDBOK ) 
    	{
     		echo "<p>";
     		$xpay->Rollback("상점 DB처리 실패로 인하여 Rollback 처리 [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");            		            		
      		
          //echo "TX Rollback Response_code = " . $xpay->Response_Code() . "<br>";
          //echo "TX Rollback Response_msg = " . $xpay->Response_Msg() . "<p>";
      		
          if( "0000" == $xpay->Response_Code() ) {
            	//echo "자동취소가 정상적으로 완료 되었습니다.<br>";
          }else{
    				//echo "자동취소가 정상적으로 처리되지 않았습니다.<br>";
          }
    	}
    } else {
      	//최종결제요청 결과 실패 DB처리
     	//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";            	            
    }
	} else {
		$xpayCode = $xpay->Response_Code();				
    $xpayMsg = $xpay->Response_Msg();
    //2)API 요청실패 화면처리
    //echo "결제요청이 실패하였습니다.  <br>";
    //echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
    //echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
        
    //최종결제요청 결과 실패 DB처리
    //echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
	}
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>*** SmartXPay ***</title>
<meta name="viewport" content="width=device-width, user-scalable=1.0, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/skin/m_default/css/m.css" rel="stylesheet">
<script type="text/javascript">
  var controlCss = "css/style_mobile.css";
  var isMobile = {
    Android: function() {
      return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
      return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
      return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
      return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
      return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
      return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
  };

  if( isMobile.any() )
    document.getElementById("cssLink").setAttribute("href", controlCss);
    
	function goCart()
	{
		opner.parent.location.href='/order/cart.php';
		window.close();		
	}    
	
	function goMypage()
	{
		opner.parent.location.href='/order/cart.php';
		window.close();		
	}
</script>
</head>

<body>
<form name="cancel" method="post" style="width: 100%; height: 100%;">
 <section id="wrap">
  <section class="contents bgArea">		
   <div class="orderArea">
    <div class="part">
     <p class="tit">결제완료</p>		
<?
    
        /* ============================================================================== */
        /* =   01-1. 업체 DB 처리 정상(bSucc값이 false가 아닌 경우)                     = */
        /* = -------------------------------------------------------------------------- = */
        if ( $bSucc )
        {
            /* ============================================================================== */
            /* =   01-1-1. 정상 결제시 결제 결과 출력 ( res_cd값이 0000인 경우)             = */
            /* = -------------------------------------------------------------------------- = */
            if ( $xpayCode == "0000" )
            {
            	//20150907 / minks / 모바일 제작기간, 배송기간 안내 문구 출력
    					$data = $db->fetch("select * from exm_pay where payno = '$ordr_idxx'");
    
    					$query = "select * from exm_ord where payno = '$data[payno]'";
							$res = $db->query($query);
							while ($ord = $db->fetch($res)){
								$data['ord'][$ord[ordno]] = $ord;
								$query = "select * from exm_ord_item where payno = '$data[payno]' and ordno = '$ord[ordno]'";
								$res2 = $db->query($query);
								while ($item = $db->fetch($res2)){
									list($item[leadtime]) = $db->fetch("select leadtime from exm_goods where goodsno='$item[goodsno]'",1);
									$item[leadtime] = str_split(preg_replace("/[^0-9]*/s", "", $item[leadtime]));
									
									if (!$data[leadtime] || $item[leadtime][sizeof($item[leadtime])-1] > $data[leadtime][sizeof($data[leadtime])-1]) 
									{
										$data[leadtime] = $item[leadtime];
										$data[deliverytime] = $data[leadtime][sizeof($data[leadtime])-1]+2;
									}
      
									$data['ord'][$ord[ordno]][item][$item[ordseq]] = $item;
									$db->fetch("delete from exm_cart where cartno = '$item[cartno]'");
								}
							}
	
							$data[leadtime] = ($data[leadtime][1]) ? $data[leadtime][0]."~".$data[leadtime][1] : $data[leadtime][0];
?>
				<div class="paymentResult">
					<strong>주문하신 상품이 성공적으로 결제 되었습니다.</strong><br />
					<span>상품 배송기간은 제작기간(<?=$data[leadtime]?>일)과<br />배송기간을 포함하여<br />약 <?=$data[deliverytime]?>일 정도 소요될 예정입니다.</span><br />
					<a class="pgPaymentBtn" href="../../../mypage/orderlist.php?guest=1&mobile_type=Y" target="_parent">주문확인</a>
				</div>	
<?
            }
            /* = -------------------------------------------------------------------------- = */
            /* =   01-1-1. 정상 결제시 결제 결과 출력 END                                   = */
            /* ============================================================================== */
			else
			{
?>
				<div class="paymentResult">
					<strong>오류코드 : <?=$xpayCode?></strong><br />
					<span><?=$xpayMsg?></span><br />
					<a class="pgPaymentBtn" href="javascript:goCart();" target="_parent">다시결제</a>
				</div>
<?
			}
   	}
        /* = -------------------------------------------------------------------------- = */
        /* =   01-1. 업체 DB 처리 정상 END                                              = */
        /* ============================================================================== */
		else
		{
?>
			<div class="paymentResult">
				<strong>오류코드 : <?=$xpayCode?></strong><br />
				<span><?=$xpayMsg?></span><br />
				<a class="pgPaymentBtn" href="javascript:goCart();" target="_parent">다시결제</a>
			</div>
<?
		}    
    /* = -------------------------------------------------------------------------- = */
    /* =   01. 결제 결과 출력 END                                                   = */
    /* ============================================================================== */
	
?>
            
    </div>
   </section>
  </section>
 </form>
</body>
</html>
