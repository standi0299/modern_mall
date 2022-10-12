<?

include "../_header.php";
include "../_left_menu.php";
// 주문 및 견적 금액 절사기능 설정 페이지 20190530 jtkim
$cutmoney = new M_config();
$cutmoney_use = $cutmoney->getConfigInfo($cid,"cutmoney_use");
$cutmoney_type = $cutmoney->getConfigInfo($cid,"cutmoney_type");
$cutmoney_op = $cutmoney->getConfigInfo($cid,"cutmoney_op");
$cutmoney_display = $cutmoney->getConfigInfo($cid,"cutmoney_display");
$cutmoney_display_text = $cutmoney->getConfigInfo($cid,"cutmoney_display_text");
$cutmoney_display_money = $cutmoney->getConfigInfo($cid,"cutmoney_display_money");

if(!$cutmoney_use[value]) $checked[cutmoney_use][0] = "checked";
if(!$cutmoney_type[value]) $checked[cutmoney_type][0] = "checked";
if(!$cutmoney_op[value]) $checked[cutmoney_op][0] = "checked";
if(!$cutmoney_display[value]) $checked[cutmoney_display][0] = "checked";
if(!$cutmoney_display_money[value]) $checked[cutmoney_display_money][0] = "checked";

$checked[cutmoney_use][$cutmoney_use[value]] = "checked";
$checked[cutmoney_type][$cutmoney_type[value]] = "checked";
$checked[cutmoney_op][$cutmoney_op[value]] = "checked";
$checked[cutmoney_display][$cutmoney_display[value]] = "checked";
$checked[cutmoney_display_money][$cutmoney_display_money[value]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" id="cutmoney_form" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="cutmoney" />
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("주문금액(장바구니,주문서,견적서) 절사 설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("절사 사용 여부")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="cutmoney_use" value="1" <?=$checked[cutmoney_use][1]?>> <?=_("사용")?>
      	 		<input type="radio" class="radio-inline" name="cutmoney_use" value="0" <?=$checked[cutmoney_use][0]?>> <?=_("사용안함")?>
      	 	</div>
		   </div>
		<div class="form-group notView" id="cutmoney_div">
			<label class="col-md-2 control-label"><?=_("절사 설정")?></label>
				<div class="col-md-10">
						<?=_("절사 금액 표시")?> :	<input type="radio" class="radio-inline" name="cutmoney_display_money" value="1" <?=$checked[cutmoney_display_money][1]?>> <?=_("사용")?>
							<input type="radio" class="radio-inline" name="cutmoney_display_money" value="0" <?=$checked[cutmoney_display_money][0]?>> <?=_("사용안함")?><p><p>
						<?=_("절사 안내문구 표시 (최대50글자)")?> :	<input type="radio" class="radio-inline" name="cutmoney_display" value="1" <?=$checked[cutmoney_display][1]?>> <?=_("사용")?>
							<input type="radio" class="radio-inline" name="cutmoney_display" value="0" <?=$checked[cutmoney_display][0]?>> <?=_("사용안함")?><p><p>
						<input type="text" class="form-control" name="cutmoney_display_text" value="<?=$cutmoney_display_text[value]?>" size="30" maxlength="50" placeholder="고객님의 결제 편의를 위해 1원단위의 금액은 절사(내림)처리 하였음을 알려드립니다."><p><p><p><p>
						<?=_("금액 단위")?> : <input class="radio-inline" type="radio" name="cutmoney_type" value="1" <?=$checked[cutmoney_type][1]?> >1원단위
						<input class="radio-inline" type="radio" name="cutmoney_type" value="2" <?=$checked[cutmoney_type][2]?> >10원단위
						<input class="radio-inline" type="radio" name="cutmoney_type" value="3" <?=$checked[cutmoney_type][3]?> >100원단위 <p><p>
						<?=_("금액 처리")?> : <input class="radio-inline" type="radio" name="cutmoney_op" value="F" <?=$checked[cutmoney_op]["F"]?> >내림
						<input class="radio-inline" type="radio" name="cutmoney_op" value="C" <?=$checked[cutmoney_op]["C"]?> >올림
						<input class="radio-inline" type="radio" name="cutmoney_op" value="R" <?=$checked[cutmoney_op]["R"]?> >반올림
				</div>
		</div>
      </div>
   </div>   
   </form>
   		<div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
				   <button type="submit" class="btn btn-sm btn-success" onClick="check_value()"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
</div>

<script type="text/javascript">

$j(function() {
	$j("[name=cutmoney_use][value=<?=$cutmoney_use[value]+0?>]").trigger("click");
	$j("[name=cutmoney_display][value=<?=$cutmoney_display[value]+0?>]").trigger("click");
});

$j("[name=cutmoney_use]").click(function() {
	if ($j(this).val() == 1) {
		$j("#cutmoney_div").slideDown();
			if($j("[name=cutmoney_display]").val() != 1){
				$j("[name=cutmoney_display_text]").hide();
			}
	} else {
		$j("#cutmoney_div").slideUp();
	}
});

$j("[name=cutmoney_display]").click(function() {
	if ($j(this).val() == 1) {
		$j("[name=cutmoney_display_text]").show();
	} else {
		$j("[name=cutmoney_display_text]").hide();
	}
});

function check_value(){
	var used = $("input[name=cutmoney_use]:checked").val();
	var type = $("input[name=cutmoney_type]").is(":checked");
	var op = $("input[name=cutmoney_op]").is(":checked");
	if(!used){
		//체크 없이 저장시
		alert("절사 사용 여부를 선택해주세요");
		return;
	}else if(used == 1 && (type == false || op == false) ){
		//사용함 체크후 설정갑 미입력시
		alert("금액 단위와 처리 방법을 선택해주세요");
		return;
	}
	if(!confirm("적용하시겠습니까?")) return;
	$('#cutmoney_form')[0].submit();
};
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>