<?

include "../_header.php";
include "../_left_menu.php";


$data = getCfg("", "cash_supp");

if (!$data[cash_supp_use]) $data[cash_supp_use] = "0";
if (!$data[cash_supp_co_kind]) $data[cash_supp_co_kind] = "0";
if (!$data[cash_supp_price_policy]) $data[cash_supp_price_policy] = "R";
if (!$data[cash_supp_limit_day]) $data[cash_supp_limit_day] = "0";

$checked[cash_supp_use][$data[cash_supp_use]] = "checked";
$checked[cash_supp_co_kind][$data[cash_supp_co_kind]] = "checked";
$checked[cash_supp_price_policy][$data[cash_supp_price_policy]] = "checked";
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="cash_supp" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("현금영수증 발행 설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 
				<div class="form-group">
        	<label class="col-md-2 control-label"><?=_("현금영수증 발행")?></label>
          <div class="col-md-10">
          	<input type="checkbox" data-render="switchery" data-theme="blue" name="cash_supp_use" value="1" <?=$checked[cash_supp_use][1]?>>               
            <div><span class="notice">[<?=_("설명")?>]</span> <?=_("현금영수증 발생 버튼 노출여부를 설정합니다. 사용하는 PG 사 모듈을 이용합니다.")?></div>
         	</div>
				</div>
         
         
         <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("영수증 발행 업체명")?></label>
      	 	<div class="col-md-10">
      	 		<input type="text" class="form-control" name="cash_supp_co_name" value="<?=$data[cash_supp_co_name]?>">
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("영수증 발행 업체 사업자번호")?></label>
      	 	<div class="col-md-10">
      	 		<input type="text" class="form-control" name="cash_supp_co_num" value="<?=$data[cash_supp_co_num]?>">
      	 	</div>
      	 </div>
      	 
      	    
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("사업장 형태")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="cash_supp_co_kind" value="1" <?=$checked[cash_supp_co_kind]['1']?>> <?=_("면세")?>
      	 		<input type="radio" class="radio-inline" name="cash_supp_co_kind" value="0" <?=$checked[cash_supp_co_kind]['0']?>> <?=_("과세")?>      	 		
      	 	</div>
      	 </div>
      	 
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("공급가액 소수점이하 처리")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="cash_supp_price_policy" value="R" <?=$checked[cash_supp_price_policy]['R']?>> <?=_("반올림")?>
      	 		<input type="radio" class="radio-inline" name="cash_supp_price_policy" value="F" <?=$checked[cash_supp_price_policy]['F']?>> <?=_("버림")?>
      	 		<input type="radio" class="radio-inline" name="cash_supp_price_policy" value="C" <?=$checked[cash_supp_price_policy]['C']?>> <?=_("올림")?>
      	 		
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("공급가액 = 주문금액 / 1.1(세율 10% 기준), 세액 = 주문금액 - 공급가액")?></div>      	 		
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("영수증 발행신청 기간제한")?></label>
      	 	<div class="col-md-10">
      	 		<?=_("발송완료후")?><input type="text" name="cash_supp_limit_day" value="<?=$data[cash_supp_limit_day]?>" size="2"> <?=_("일이내 신청가능")?>      	 		
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("기간 이외에는 영수증 신청 버튼이 표시되지 않습니다. '0'은 무제한.")?></div>      	 		
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
<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
});

</script>




<? include "../_footer_app_exec.php"; ?>