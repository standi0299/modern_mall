<?
include "../_header.php";
include "../_left_menu.php";

$data[discount_hidden_flag] = getCfg('discount_hidden_flag');
if (!$data[discount_hidden_flag]) $data[discount_hidden_flag] = "";

$data[account_hidden_flag] = getCfg('account_hidden_flag');
if (!$data[account_hidden_flag]) $data[account_hidden_flag] = "";

$data[order_request2_hide] = getCfg('order_request2_hide');
if(!$data[order_request2_hide]) $data[order_request2_hide] = 0;
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="display_config" />

   <div class="panel panel-inverse">
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("기타 화면 설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	<div class="form-group">
            <label class="col-md-2 control-label"><?=_("할인 정보 숨김 설정")?><br>(<?=_("대상 : M1")?>)</label>
            <div class="col-md-10 form-inline">
               <input type="checkbox" name="discount_hidden_flag" value="Y" <?if($data[discount_hidden_flag] == "Y"){?>checked<?}?>/> <?=_("숨김")?>

               <div><span class="warning">[<?=_("주의")?>]</span> <?=_("숨김 적용 시 쇼핑몰의 주문서 작성 화면이 바뀌게 됩니다.")?></div>
               <div><span class="warning">[<?=_("주의")?>]</span> <?=_("(그룹할인, 적립금, 상품할인쿠폰, 프로모션코드, T멤버십 할인)")?></div>
            </div>
         </div>

      	<div class="form-group">
            <label class="col-md-2 control-label"><?=_("가격 정보 숨김 설정")?><br>(<?=_("대상 : M1")?>)</label>
            <div class="col-md-10 form-inline">
               <input type="checkbox" name="account_hidden_flag" value="Y" <?if($data[account_hidden_flag] == "Y"){?>checked<?}?>/> <?=_("숨김")?>

               <div><span class="warning">[<?=_("주의")?>]</span> <?=_("숨김 적용 시 쇼핑몰의 상품상세 화면이 바뀌게 됩니다.")?></div>
            </div>
         </div>

        <div class="form-group">
          <label class="col-md-2 control-label"><?=_("주문서 추가메모 숨김 설정")?><br>(<?=_("대상 : M1,P1")?>)</label>
          <div class="col-md-10 form-inline">
            <input type="checkbox" name="order_request2_hide" value=1 <?if($data[order_request2_hide] == 1){?>checked<?}?>/> <?=_("숨김")?>

            <div><span class="warning">[<?=_("주의")?>]</span> <?=_("숨김 적용 시 쇼핑몰의 주문서 작성 화면이 바뀌게 됩니다.")?></div>
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
