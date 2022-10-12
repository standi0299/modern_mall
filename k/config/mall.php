<?

include "../_header.php";
include "../_left_menu.php";

$m_mall = new M_mall();

$data = $m_mall->getInfo($cid);
$data[phone] = explode("-", $data[phone]);
$data[source_save_days] = getCfg('source_save_days');
$data[thum_save_days] = getCfg('thum_save_days');

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="mall_info" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("몰정보")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("분양몰아이디")?></label>
      	 	<div class="col-md-3">
      	 		<input type="hidden" name="cid" value="<?=$data[cid]?>">
      	 		<b><?=$data[cid]?></b>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("PODSTATION SITE ID")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="self_podsiteid" value="<?=$data[self_podsiteid]?>" size="40" pt="_pt_podsid"> (소문자, 숫자, 5자이상만 가능합니다.)
      	 		<div><span class="warning">[주의]</span> 몰내의 센터와는 독립된 자체상품 운영시에 입력해주세요.</div>
      	 		<div><span class="notice">[설명]</span> 상품을 소유한 사이트아이디입니다.</div>
      	 	</div>
      	 </div>
      	 
      	 <!--<div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("PODSTATION USER ID")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="self_podsid" value="<?=$data[self_podsid]?>" size="40" pt="_pt_podsid"> (소문자, 숫자, 5자이상만 가능합니다.)
      	 		<div><span class="warning">[주의]</span> 몰내의 센터와는 독립된 자체상품을 운영시에 입력해주세요.</div>
      	 		<div><span class="notice">[설명]</span> PODS1.0에서만 사용하는 값입니다.</div>
      	 	</div>
      	 </div>-->
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("담당자")?></label>
      	 	<div class="col-md-3">
      	 		<input type="text" class="form-control" name="manager" value="<?=$data[manager]?>">
      	 	</div>
      	 	<label class="col-md-2 control-label"><?=_("연락처")?></label>
      	 	<div class="col-md-4 form-inline">
      	 		<input type="text" class="form-control" name="phone[]" value="<?=$data[phone][0]?>" size="3" maxlength="3" type2="number"> - 
      	 		<input type="text" class="form-control" name="phone[]" value="<?=$data[phone][1]?>" size="4" maxlength="4" type2="number"> - 
      	 		<input type="text" class="form-control" name="phone[]" value="<?=$data[phone][2]?>" size="4" maxlength="4" type2="number">
      	 	</div>
      	 </div>
      	 
      	 <!--<div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("정산일")?></label>
      	 	<div class="col-md-3 form-inline">
      	 		매월 <b><?=$data[accday]?></b>일
      	 		<div>정산일 변경은 센터에 문의해주세요.</div>
      	 	</div>
      	 	<label class="col-md-2 control-label"><?=_("정산률")?></label>
      	 	<div class="col-md-3 form-inline">
      	 		<b><?=$data[accper]?></b>%
      	 		<div>정산률 변경은 센터에 문의해주세요.</div>
      	 	</div>
      	 </div>-->
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("원본이미지 보관기간")?></label>
      	 	<div class="col-md-3 form-inline">
      	 		<b><?=$data[source_save_days]?></b>일
      	 	</div>
      	 	<label class="col-md-2 control-label"><?=_("썸네일이미지 보관기간")?></label>
      	 	<div class="col-md-3 form-inline">
      	 		<b><?=$data[thum_save_days]?></b>일
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("등록일")?></label>
      	 	<div class="col-md-3">
      	 		<?=$data[regdt]?>
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
	$j('input[name=cid]').css('ime-mode', 'disabled');
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>