<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();

$rid = "_self_".$cid;
$data = $m_etc->getReleaseInfo($rid);
$data[phone] = explode("-",$data[phone]);

if (!$cfg[self_release]) $checked[self_release][0] = "checked";
if (!$data[shiptype]) $checked[shiptype][0] = "checked";

$checked[self_release][$cfg[self_release]] = "checked";
$checked[shiptype][$data[shiptype]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="self_release" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("제체상품관리")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("자체상품설정")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="self_release" value="0" <?=$checked[self_release][0]?>> 사용안함
      	 		<input type="radio" class="radio-inline" name="self_release" value="1" <?=$checked[self_release][1]?>> 사용
      	 	</div>
      	 </div>
      	 
      	 <div class="notView" id="release_div">
	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("자체상품 제작사<br>회사명")?></label>
	      	 	<div class="col-md-10 form-inline">
	      	 		<input type="text" class="form-control" name="compnm" value="<?=$data[compnm]?>" size="40" required>
	      	 		<div><span class="notice">[설명]</span> 내부 관리용 출고처명으로 사용될 명칭입니다.</div>
	      	 	</div>
	      	 </div>
	      	 
	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("자체상품 제작사<br>별칭")?></label>
	      	 	<div class="col-md-10 form-inline">
	      	 		<input type="text" class="form-control" name="nicknm" value="<?=$data[nicknm]?>" size="40" required>
	      	 		<div><span class="notice">[설명]</span> 장바구니와 상품상세페이지에 이용자들에게 보여질 출고처명입니다.</div>
	      	 	</div>
	      	 </div>
	      	 
	      	 <!--<div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("자체상품 제작사<br>대표자명")?></label>
	      	 	<div class="col-md-3">
	      	 		<input type="text" class="form-control" name="name" value="<?=$data[name]?>">
	      	 	</div>
	      	 </div>
	      	 
	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("자체상품 제작사<br>담당자명")?></label>
	      	 	<div class="col-md-3">
	      	 		<input type="text" class="form-control" name="manager" value="<?=$data[manager]?>">
	      	 	</div>
	      	 </div>-->
	      	 
	      	 <!-- 공급사에 sms발송시 사용 -->
	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("자체상품 제작사<br>연락처")?></label>
	      	 	<div class="col-md-3 form-inline">
	      	 		<input type="text" class="form-control" name="phone[]" value="<?=$data[phone][0]?>" size="3" maxlength="3" type2="number"> - 
      	 			<input type="text" class="form-control" name="phone[]" value="<?=$data[phone][1]?>" size="4" maxlength="4" type2="number"> - 
      	 			<input type="text" class="form-control" name="phone[]" value="<?=$data[phone][2]?>" size="4" maxlength="4" type2="number">
	      	 	</div>
	      	 </div>
	      	 
	      	 <!--<div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("자체상품 제작사<br>주소")?></label>
	            <div class="col-md-10 form-inline">
	            	<input type="text" class="form-control" name="zipcode" value="<?=$data[zipcode]?>" readonly>
                    <i class="fa fa-search cursorPointer" onclick="javascript:popupZipcode<?=$language_locale?>()"></i><br><br>
                    <input type="text" class="form-control" name="address" value="<?=$data[address]?>" size="50">
                    <input type="text" class="form-control" name="address_sub" value="<?=$data[address_sub]?>" size="50">
	            </div>
	      	 </div>-->
	      	 
	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("자체상품 제작사<br>배송비")?></label>
	      	 	<div class="col-md-10">
	      	 		<?=makeShiptypeRadioTag("shiptype", $checked[shiptype], "2")?><p>
	      	 		<div id="shipping_0" class="form-inline notView">
	      	 			배송비 <input type="text" class="form-control" name="shipprice" value="<?=$data[shipprice]?>" disabled type2="number"> 원 
	      	 			(배송비원가 <input type="text" class="form-control" name="oshipprice" value="<?=$data[oshipprice]?>" disabled type2="number"> 원)
	      	 		</div>			 
					<div id="shipping_1" class="notView">배송비가 <b>무료</b>입니다. (판매자부담)</div>
					<div id="shipping_3" class="form-inline notView">
						주문금액이 <input type="text" class="form-control" name="shipconditional" value="<?=$data[shipconditional]?>" disabled type2="number"> 원 미만일 경우 
						배송비 <input type="text" class="form-control" name="shipprice" value="<?=$data[shipprice]?>" disabled type2="number"> 원
						(배송비원가 <input type="text" class="form-control" name="oshipprice" value="<?=$data[oshipprice]?>" disabled type2="number"> 원)
					</div>
					<div id="shipping_4" class="notView">배송비가 <b>무료</b>입니다. (구매자부담)</div>  
				    <div id="shipping_9" class="notView">배송비가 <b>무료</b>입니다.</div>
				    <div><span class="notice">[설명]</span> 위의 배송비는 제작사가 해당몰로 설정된 공용상품의 정산시에만 반영됩니다.</div>
	      	 	</div>
	      	 </div>
	      	 
	      	 <div class="form-group"></div> 
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
	$j("[name=self_release][value=<?=$cfg[self_release]+0?>]").trigger("click");
	
	$j("[name=shiptype]:radio").each(function() {
		if ($j(this).val() == "<?=$data[shiptype]+0?>"){
			$j(this).trigger("click");
		}
	});
	
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});

$j("[name=self_release]").click(function() {
	if ($j(this).val() == 1) {
		$j("#release_div").slideDown();
		$j('input','#release_div').attr('disabled', false);
	} else {
		$j("#release_div").slideUp();
		$j('input','#release_div').attr('disabled', true);
	}
});

var oldshipping = "<?=$data[shiptype]?>";
$j("[name=shiptype]:radio").click(function() {
	$j("#shipping_" + oldshipping).css("display", "none");
	$j("input","#shipping_" + oldshipping).attr("disabled", true);
		
	oldshipping = $j(this).val();
	$j("#shipping_" + oldshipping).css("display", "block");
	$j("input","#shipping_" + oldshipping).attr("disabled", false);
});
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>