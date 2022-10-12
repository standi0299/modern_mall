<?
	$login_check_flag = "N"; 							//k 관리자 체크 안하기.
?>
<? include_once "../_pheader.php"; ?>

<?
if ($language_locale == "zh_CN")
	$formAction = "/pg/WxpayAPIv3/order.php";
else {
	if (isMobile())
		$formAction = "/pg/kcp_ilark/mobile/order.php";
	else
		//$formAction = "/pg/kcp_ilark/order.php";
        $formAction = "../kcp_api_page.php";
}
// 주문번호 생성하기.
$micro = explode(" ", microtime());
$payno = date("YmdHis", $micro[1]) . sprintf("%03d", floor($micro[0] * 1000));

?>

    <script type="text/javascript">
        /****************************************************************/
        /* m_Completepayment  설명                                      */
        /****************************************************************/
        /* 인증완료시 재귀 함수                                         */
        /* 해당 함수명은 절대 변경하면 안됩니다.                        */
        /* 해당 함수의 위치는 payplus.js 보다먼저 선언되어여 합니다.    */
        /* Web 방식의 경우 리턴 값이 form 으로 넘어옴                   */
        /****************************************************************/
        function m_Completepayment( FormOrJson, closeEvent )
        {
            var frm = document.order_info;

            /********************************************************************/
            /* FormOrJson은 가맹점 임의 활용 금지                               */
            /* frm 값에 FormOrJson 값이 설정 됨 frm 값으로 활용 하셔야 됩니다.  */
            /* FormOrJson 값을 활용 하시려면 기술지원팀으로 문의바랍니다.       */
            /********************************************************************/
            GetField( frm, FormOrJson );


            if( frm.res_cd.value == "0000" )
            {
                alert("결제 승인 요청 전,\n\n반드시 결제창에서 고객님이 결제 인증 완료 후\n\n리턴 받은 ordr_chk 와 업체 측 주문정보를\n\n다시 한번 검증 후 결제 승인 요청하시기 바랍니다."); //업체 연동 시 필수 확인 사항.
                /*
                    가맹점 리턴값 처리 영역
                */
                $formAction = "../kcp_api_page.php";
                //frm.submit();
            }
            else
            {
                alert( "[" + frm.res_cd.value + "] " + frm.res_msg.value );
                alert("결제를 취소 하였습니다.");

                closeEvent();
            }
        }
    </script>
    <script type="text/javascript" src="https://testpay.kcp.co.kr/plugin/payplus_web.jsp"></script>
    <script type="text/javascript">
        /* 표준웹 실행 */
        function jsf__pay( form )
        {
            form.pay_method.value="100000000000"; //신용카드
            try
            {
                KCP_Pay_Execute( form );
            }
            catch (e)
            {
                /* IE 에서 결제 정상종료시 throw로 스크립트 종료 */
            }
        }
    </script>


<script type="text/javascript" src="/js/extra_option/goods.extra.option.js"></script>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.history.back();" class="navbar-brand"><span class="navbar-logo"></span><?=_("포인트 결제")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("포인트 결제")?></h4>
         </div>
         <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" method="post" action="<?=$formAction?>" name="order_info" ">
            <input type="hidden" name="account_type" value="P">
               <div class="form-group">
                  <label class="col-md-3 control-label"><?=_("결제 금액 [부가세 포함]")?> 주문번호 : <?=$payno?></label>
                  <div class="col-md-9" id="card_div" style="display:none;">
                     <select name="good_mny">
                     <?
                     $r_point_price = $r_printgroup_point_price;					 	
                        foreach ($r_point_price as $key => $value) {
                           $showkey = number_format($key);
                           $showvalue = number_format($value);
                           echo "<option value='$key'>$showkey Point - $showvalue "._("원")."</option>";
                        }
                     ?>
                     </select>
                  </div>
                  
                  <div class="col-md-9" id="vbank_div">
                     <input type="text" name="good_mny" value="10000" class="form-control" onkeyPress="InpuOnlyNumber(this);" >
                     		<?=_("가상계좌 이체 서비스는 5만원이상 가능하며 원하는 금액을 직접 입력해주세요.")?>
                  </div>
                  
               </div>

               <div class="form-group">
               <label class="col-md-3 control-label"><?=_("결제 수단")?></label>
                  <div class="col-md-9">
                     
                     <?	if ($language_locale == "zh_CN")	{	?>
                     	<input type="radio" name="account_pay_method" value="c" checked="true">Wechat
                     <?	} else {	?>
                     <input type="radio" name="pay_method" value="100000000000" checked="true" onclick="vbank_display();"><?=_("가상 계좌입금")?>
                     <input type="radio" name="pay_method" value="100000000000" onclick="card_display();"><?=_("신용카드")?>
                     <?	}	?>

                  </div>
               </div>

               <div class="form-group">
               <label class="col-md-3 control-label"></label>
                  <div class="col-md-9">

                      <a href="#none"  class="btn btn-sm btn-success" onclick="jsf__pay(document.order_info);" ><?=_("결 제")?></a>
                      <!--<button type="button" class="btn btn-sm btn-default" onclick="window.history.back();"><?=_("닫  기")?></button>-->
                      <button type="button" class="btn btn-sm btn-default" onclick="window.close();"><?=_("닫  기")?></button>
                  </div>
               </div>
             <!-- 가맹점 정보 설정-->
             <input type="hidden" name="site_cd"         value="T0000" /> <!-- N5072-->
             <input type="hidden" name="site_name"       value="TEST SITE" /> <!-- 아이락-->
             <!--
             ※필수 항목
             표준웹에서 값을 설정하는 부분으로 반드시 포함되어야 합니다값을 설정하지 마십시오
             -->
             <input type="hidden" name="res_cd"          value=""/>
             <input type="hidden" name="res_msg"         value=""/>
             <input type="hidden" name="enc_info"        value=""/>
             <input type="hidden" name="enc_data"        value=""/>
             <input type="hidden" name="ret_pay_method"  value=""/>
             <input type="hidden" name="tran_cd"         value=""/>
             <input type="hidden" name="use_pay_method"  value=""/>

             <!-- 주문번호(ordr_idxx)
             <input type="hidden" name="ordr_idxx"  value="TEST1234567890"/>-->
             <input type="hidden" name="ordr_idxx"  value="<?=$payno?>"/>

             <!-- 상품명(good_name) -->
             <input type="hidden" name="good_name"  value="포인트 결제"/>

             <!-- 주문자명(buyr_name) -->
             <input type="hidden" name="buyr_name"  value="홍길동"/>

             <!-- 주문자 연락처1(buyr_tel1) -->
             <input type="hidden" name="buyr_tel1"  value="02-9876-5432"/>

             <!-- 휴대폰번호(buyr_tel2) -->
             <input type="hidden" name="buyr_tel2"  value="010-1234-5678"/>

             <!-- 주문자 E-mail(buyr_mail) -->
             <input type="hidden" name="buyr_mail"  value="test@test.co.k"/>


            </form>
         </div>
      </div>
   </div>
</div>
<script>
	
	function card_display()
	{
		$("#card_div").show();
		$("#vbank_div").hide();
	}
	
	function vbank_display()
	{
		$("#card_div").hide();
		$("#vbank_div").show();
	}
	
	function InpuOnlyNumber(obj) 
	{
    if (event.keyCode >= 48 && event.keyCode <= 57) { //숫자키만 입력
        return true;
    } else {
        event.returnValue = false;
    }
	}

	// 포인트 체크하는 함수/ 사용안함.
	function point_check(frm)
	{
		var cnt = document.frm.account_point_vbank.value;		
		var pay_method = $(":input:radio[name=account_pay_method]:checked").val(); //document.frm.account_pay_method.value;
		
		if (pay_method == 'v')
		{	
			//숫자만 입력인지 체크한다.		
			if ($.isNumeric(cnt))		
			{		
				var min = 50000;			
						
				if(cnt >= min) {
					return true;
				}
				else {
					alert('<?=_("5만원 이상 가능합니다.")?>');
					return false;
				}
			} else {
				alert('<?=_("숫자만 입력해주세요.")?>');
				document.frm.account_point_vbank.value = '';
				return false;
			}
		}
	}

</script>      
      
<? include_once "../_pfooter.php"; ?>