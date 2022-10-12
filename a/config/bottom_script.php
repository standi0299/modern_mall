<?

include "../_header.php";
include "../_left_menu.php";

$cfg[bottom_script] = getCfg('bottom_script');
$cfg[payment_script] = getCfg('payment_script');
$cfg[register_ok_script] = getCfg('register_ok_script');
$cfg[in_cart_script] = getCfg('in_cart_script');

?>


<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm_bottom" method="POST" action="indb.php" onsubmit="return submitContents(this,'bottom_script');">
   <input type="hidden" name="mode" value="bottom_script" />
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("공통 스크립트추가")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("내용")?></label>
      	 	<div class="col-md-10">
      	 		<textarea id="bottom_script" name="bottom_script" type="editor" style="width:100%;height:300px;"><?=$cfg[bottom_script]?></textarea>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
      </div>
   </div>

   </form>


   <form class="form-horizontal form-bordered" name="fm_in_cart" method="POST" action="indb.php" onsubmit="return submitContents(this,'in_cart_script');">
   <input type="hidden" name="mode" value="in_cart_script" />
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("장바구니 담기 완료 스크립트추가")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("내용")?></label>
      	 	<div class="col-md-10">
      	 		<textarea id="in_cart_script" name="in_cart_script" type="editor" style="width:100%;height:300px;"><?=$cfg[in_cart_script]?></textarea>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
      </div>
   </div>

   </form>

   <form class="form-horizontal form-bordered" name="fm_payment" method="POST" action="indb.php" onsubmit="return submitContents(this,'payment_script');">
   <input type="hidden" name="mode" value="payment_script" />
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("구매완료 스크립트추가")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("내용")?></label>
      	 	<div class="col-md-10">
				   <textarea id="payment_script" name="payment_script" type="editor" style="width:100%;height:300px;"><?=$cfg[payment_script]?></textarea>
				   <div><span class="notice">[<?=_("설명")?>]</span> <?=_("{payprice} > 결제금액 표시")?></div>  
			</div>			   
      	 </div>
      	
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
		   
      </div>
   </div>
    	 		
   </form>

	<form class="form-horizontal form-bordered" name="fm_register" method="POST" action="indb.php" onsubmit="return submitContents(this,'register_ok_script');">
	<input type="hidden" name="mode" value="register_ok_script" />
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("회원가입 완료 스크립트추가")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("내용")?></label>
      	 	<div class="col-md-10">
      	 		<textarea id="register_ok_script" name="register_ok_script" type="editor" style="width:100%;height:300px;"><?=$cfg[register_ok_script]?></textarea>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
      </div>
   </div>
   </form>

</div>

<? include "../_footer_app_init.php"; ?>

<script type="text/javascript" src="../assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/form-slider-switcher.demo.js"></script>
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
});

// editor 사용안하게끔
function submitContents(formObj,script) {

	var v = eval("formObj."+script+".value");

	try {
		console.log(v);
		var v_e = Base64.encode(v);
		switch(script){
			case "bottom_script" :
				formObj.bottom_script.value = v_e;
				break;
			case "in_cart_script" :
				formObj.in_cart_script.value = v_e;
				break;
			case "payment_script" :
				formObj.payment_script.value = v_e;
				break;
			case "register_ok_script" :
				formObj.register_ok_script.value = v_e;
				break;
			default :
				return false;
				break;
		}
		return form_chk(formObj);
	} catch(e) {
		return false;
	}
	return false;
}
</script>

<? include "../_footer_app_exec.php"; ?>