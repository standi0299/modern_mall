<?

include "../_header.php";
include "../_left_menu.php";

$cfg[policy] = getCfg('policy');
$cfg[nonmember_agreement] = getCfg('nonmember_agreement');
$cfg[personal_data_collect_use_choice_nonmember] = getCfg('personal_data_collect_use_choice_nonmember');
$cfg[nonmember_agreement] = getCfg('nonmember_agreement');
$cfg[personal_data_referral_nonmember] = getCfg('personal_data_referral_nonmember');

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
   <input type="hidden" name="mode" value="policy_nonmember" />

   <div class="panel panel-inverse">
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("개인정보 취급방침")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("개인정보 수집·이용 동의(필수)")?><BR>[주문서 작성, 견적의뢰]</label>
            <div class="col-md-10">
      	 		<textarea id="agreement2" class="form-control" name="nonmember_agreement"><?=$cfg[nonmember_agreement]?></textarea>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("개인정보 수집동의는 비회원구매시 개인정보수집 동의를 명시하는 부분에 나오게 됩니다.")?></div>
      	 	</div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("개인정보 수집·이용 동의(선택)")?><BR>[주문서 작성]</label>
            <div class="col-md-10">
      	 		<textarea id="agreement_thirdparty" class="form-control" name="personal_data_collect_use_choice_nonmember"><?=$cfg[personal_data_collect_use_choice_nonmember]?></textarea>
      	 	</div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("개인정보 처리위탁")?><BR>[주문서 작성]</label>
            <div class="col-md-10">
      	 		<textarea id="nonmember_agreement" class="form-control" name="personal_data_referral_nonmember"><?=$cfg[personal_data_referral_nonmember]?></textarea>
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