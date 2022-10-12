<?

include "../_header.php";
include "../_left_menu.php";

if (!$cfg[self_deliv]) $checked[self_deliv][0] = "checked";
if (!$cfg[self_shiptype]) $checked[self_shiptype][0] = "checked";
if (!$cfg[order_shiptype]) $checked[order_shiptype][0] = "checked";

$checked[self_deliv][$cfg[self_deliv]] = "checked";
$checked[self_shiptype][$cfg[self_shiptype]] = "checked";
$checked[order_shiptype][$cfg[order_shiptype]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return confirm('수정사항은 쇼핑몰 운영에 즉시 반영됩니다.\r\n적용하시겠습니까?')">
   <input type="hidden" name="mode" value="self_deliv" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("자체배송비설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("자체배송비설정")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="self_deliv" value="0" <?=$checked[self_deliv][0]?>> 사용안함
      	 		<input type="radio" class="radio-inline" name="self_deliv" value="1" <?=$checked[self_deliv][1]?>> 사용
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group notView" id="self_deliv_div">
      	 	<label class="col-md-2 control-label"><?=_("배송비")?></label>
      	 	<div class="col-md-10">
      	 		<?=makeShiptypeRadioTag("self_shiptype", $checked[self_shiptype], "2")?><p>
      	 		<div id="shipping_0" class="form-inline notView">배송비 <input type="text" class="form-control" name="self_shipprice" value="<?=$cfg[self_shipprice]?>" disabled type2="number"> 원</div>			 
				<div id="shipping_1" class="notView">배송비가 <b>무료</b>입니다. (판매자부담)</div>
				<div id="shipping_3" class="form-inline notView">주문금액이 <input type="text" class="form-control" name="self_shipconditional" value="<?=$cfg[self_shipconditional]?>" disabled type2="number"> 원 미만일 경우 배송비 <input type="text" class="form-control" name="self_shipprice" value="<?=$cfg[self_shipprice]?>" disabled type2="number"> 원</div>
				<div id="shipping_4" class="notView">배송비가 <b>무료</b>입니다. (구매자부담)</div>  
			    <div id="shipping_9" class="notView">배송비가 <b>무료</b>입니다.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
            <label class="col-md-2 control-label"><?=_("착불배송설정")?></label>
            <div class="col-md-10">
            	<input type="radio" class="radio-inline" name="order_shiptype" value="0" <?=$checked[order_shiptype][0]?>> 사용안함
      	 		<input type="radio" class="radio-inline" name="order_shiptype" value="1" <?=$checked[order_shiptype][1]?>> 사용
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

<script type="text/javascript">
$j(function() {
	$j("[name=self_deliv][value=<?=$cfg[self_deliv]+0?>]").trigger("click");
	
	$j("[name=self_shiptype]:radio").each(function() {
		if ($j(this).val() == "<?=$cfg[self_shiptype]+0?>"){
			$j(this).trigger("click");
		}
	});
	
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});

$j("[name=self_deliv]").click(function() {
	if ($j(this).val() == 1) {
		$j("#self_deliv_div").slideDown();
	} else {
		$j("#self_deliv_div").slideUp();
	}
});

var oldshipping = "<?=$cfg[self_shiptype]?>";
$j("[name=self_shiptype]:radio").click(function() {
	$j("#shipping_" + oldshipping).css("display", "none");
	$j("input","#shipping_" + oldshipping).attr("disabled", true);
		
	oldshipping = $j(this).val();
	$j("#shipping_" + oldshipping).css("display", "block");
	$j("input","#shipping_" + oldshipping).attr("disabled", false);
});
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>