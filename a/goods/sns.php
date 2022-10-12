<?
include "../_header.php";
include "../_left_menu.php";
?>

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("SNS 상품퍼가기 설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("SNS 상품퍼가기 설정")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("SNS 상품퍼가기 설정")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
                  <input type="hidden" name="mode" value="sns">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("SNS 상품퍼가기 설정")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="radio" name="sns" value="0" <? if(!$cfg[sns_goods]) {?>checked<?}?>> <?=_("사용")?>
                        <input type="radio" name="sns" value="1" <? if($cfg[sns_goods] == 1) {?>checked<?}?>> <?=_("미사용")?>
                     </div>
                  </div>

                  <div class="form-group"> 
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("등록")?></button>
                    </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>

<? include "goods_r.js.php"; ?>