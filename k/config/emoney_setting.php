<?
include "../_header.php";
include "../_left_menu.php";
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
      <input type="hidden" name="mode" value="emoney_setting" />
      
      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("적립금 설정")?></h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("유효기간 설정")?></label>
               <div class="col-md-2 form-inline">
                  적립일로부터 <input type="text" class="form-control" name="validity_date" value="<?=$cfg[validity_date]?>" size="1">일
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("최고/최저 결제 금액")?></label>
               <div class="col-md-10 form-inline">
                  최고
                  <input type="text" class="form-control" name="max_price" value="<?=number_format($cfg[max_price])?>">원
                  <br>
                  최저
                  <input type="text" class="form-control" name="min_price" value="<?=number_format($cfg[min_price])?>">원
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("최고/최저 적립금 금액")?></label>
               <div class="col-md-10 form-inline">
                  최고
                  <input type="text" class="form-control" name="max_emoney" value="<?=number_format($cfg[max_emoney])?>">원
                  <br>
                  최저
                  <input type="text" class="form-control" name="min_emoney" value="<?=number_format($cfg[min_emoney])?>">원
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