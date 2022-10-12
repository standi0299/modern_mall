<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();

$rid = "_self_".$cid;


$data = getCfg("", "emoney");

if (!$data[emoney_send_day]) $data[emoney_send_day] = "7";
if (!$data[emoney_round]) $data[emoney_round] = "0";
if (!$data[emoney_send_type]) $data[emoney_send_type] = "G";

if (!$data[emoney_send_ratio]) $data[emoney_send_ratio] = "0";
if (!$data[emoney_new_member]) $data[emoney_new_member] = "0";
if (!$data[emoney_expire_day]) $data[emoney_expire_day] = "0";
if (!$data[emoney_min_orderprice]) $data[emoney_min_orderprice] = "0";
if (!$data[emoney_use_min]) $data[emoney_use_min] = "0";
if (!$data[emoney_use_max]) $data[emoney_use_max] = "0";


$checked[emoney_send_day][$data[emoney_send_day]] = "checked";
$checked[emoney_round][$data[emoney_round]] = "checked";
$checked[emoney_send_type][$data[emoney_send_type]] = "checked";
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="emoney_config" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("적립금설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("적립금 지급 시점")?></label>
      	 	<div class="col-md-10">
      	 		배송완료 후<input type="radio" class="radio-inline" name="emoney_send_day" value="3" <?=$checked[emoney_send_day][3]?>> 3일후
      	 		<input type="radio" class="radio-inline" name="emoney_send_day" value="7" <?=$checked[emoney_send_day][7]?>> 7일후
      	 		<input type="radio" class="radio-inline" name="emoney_send_day" value="14" <?=$checked[emoney_send_day][14]?>> 14일후
      	 		<input type="radio" class="radio-inline" name="emoney_send_day" value="20" <?=$checked[emoney_send_day][20]?>> 20일후
      	 	</div>
      	 </div>
      	 
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("적립금 절사")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="emoney_round" value="0" <?=$checked[emoney_round][0]?>> 절사안함
      	 		<input type="radio" class="radio-inline" name="emoney_round" value="1" <?=$checked[emoney_round][1]?>> 1단위 절사
      	 		<input type="radio" class="radio-inline" name="emoney_round" value="10" <?=$checked[emoney_round][10]?>> 10단위 절사
      	 		
      	 		<div><span class="notice">[설명]</span> 예시) 900원 5% 설정시 적립금 45원, 10원 단위로 내림설정시 0원, 1원 단위로 내림설정시 40원 </div>      	 		
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("적립금 기준")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="emoney_send_type" value="G" <?=$checked[emoney_send_type]['G']?>> 상품 할인 판매가격
      	 		<input type="radio" class="radio-inline" name="emoney_send_type" value="A" <?=$checked[emoney_send_type]['A']?>> 실제 결제금액
      	 		
      	 		<div><span class="notice">[설명]</span> 상품 할인 판매가격은 쿠폰(적립금)사용액을 제외한 금액입니다.</div>      	 		
      	 	</div>
      	 </div>
      	 
      	 
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("기본 적립 비율")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="emoney_send_ratio" value="<?=$data[emoney_send_ratio]?>" size="40" required>
      	 		<div><span class="notice">[설명]</span> 기본 적립 % 비율.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("회원가입 적립금 설정")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="emoney_new_member" value="<?=$data[emoney_new_member]?>" size="40" required>원
      	 		<div><span class="notice">[설명]</span> '0'원으로 설정시 회원가입시 적립금이 지급되지 않습니다.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("적립금 유효기간")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="emoney_expire_day" value="<?=$data[emoney_expire_day]?>" size="40" required>
      	 		<div><span class="notice">[설명]</span> '0'일 설정시 적립금 유효기간은 없습니다.(무제한)</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("최저 상품구매 금액")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="emoney_min_orderprice" value="<?=$data[emoney_min_orderprice]?>" size="40" required>
      	 		<div><span class="notice">[설명]</span> 상품 구매 합계액은 배송비, 할인, 부가결제 금액이 포함되지 않는 상품 판매가 총합계 금액입니다.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("적립금 최소 사용제한")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="emoney_use_min" value="<?=$data[emoney_use_min]?>" size="40" required>
      	 		<div><span class="notice">[설명]</span> 회원에게 누적된 적립금이 설정금액 이상일 때 적립금 사용이 가능합니다.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("적립금 최대 사용제한")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="emoney_use_max" value="<?=$data[emoney_use_max]?>" size="40" required>
      	 		<div><span class="notice">[설명]</span> 최대 사용할 수 있는 금액을 설정합니다.</div>
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
<? include "../_footer_app_exec.php"; ?>