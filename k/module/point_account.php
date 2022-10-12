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
		$formAction = "/pg/kcp_ilark/order.php";
}
?>


<script type="text/javascript" src="/js/extra_option/goods.extra.option.js"></script>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("포인트 결제")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("포인트 결제")?></h4>
         </div>
         <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" method="post" action="<?=$formAction?>" name="frm" onsubmit="return point_check();">
            <input type="hidden" name="account_type" value="P">
               <div class="form-group">
                  <label class="col-md-3 control-label"><?=_("결제 금액 [부가세 포함]")?></label>
                  <div class="col-md-9" id="card_div" style="display:none;">
                     <select name="account_point">
                     <?
                     $r_point_price = $r_pretty_point_price;
                     if ($cfg_center[service_kind] == "printgroup") {
                     	$r_point_price = $r_printgroup_point_price;
					 }
					 	
                        foreach ($r_point_price as $key => $value) {
                           $showkey = number_format($key);
                           $showvalue = number_format($value);
                           echo "<option value='$key'>$showkey Point - $showvalue "._("원")."</option>";
                        }
                     ?>
                     </select>
                  </div>
                  
                  <div class="col-md-9" id="vbank_div">
                     <input type="text" name="account_point_vbank" value="50000" class="form-control" onkeyPress="InpuOnlyNumber(this);" >
                     		가상계좌 이체 서비스는 5만원이상 가능하며 원하는 금액을 직접 입력해주세요.
                  </div>
                  
               </div>
               
               

               <div class="form-group">
               <label class="col-md-3 control-label"><?=_("결제 수단")?></label>
                  <div class="col-md-9">
                     
                     <?	if ($language_locale == "zh_CN")	{	?>
                     	<input type="radio" name="account_pay_method" value="c" checked="true">Wechat
                     <?	} else {	?>
                     <input type="radio" name="account_pay_method" value="v" checked="true" onclick="vbank_display();"><?=_("가상 계좌입금")?>
                     <input type="radio" name="account_pay_method" value="c" onclick="card_display();"><?=_("신용카드")?>                     
                     <?	}	?>
                     
                  </div>
               </div>

               <div class="form-group">
               <label class="col-md-3 control-label"></label>
                  <div class="col-md-9">
                     <button type="submit" class="btn btn-sm btn-success"><?=_("결제")?></button>
                     <button type="button" class="btn btn-sm btn-default" onclick="window.close();"><?=_("닫  기")?></button>
                  </div>
               </div>
            </form>
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
					alert("5만원 이상 가능합니다.");
					return false;
				}
			} else {
				alert("숫자만 입력해주세요.");
				document.frm.account_point_vbank.value = '';
				return false;
			}
		}
	}

</script>      
      
<? include_once "../_pfooter.php"; ?>