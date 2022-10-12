<?

include "../_header.php";
include "../_left_menu.php";

if ($cfg[naver_relation_config]) $cfg[naver_relation_config] = unserialize($cfg[naver_relation_config]);

if (!$cfg[naver_relation_config][naver_relation_use]) $cfg[naver_relation_config][naver_relation_use] = "N";

$checked[naver_relation_use][$cfg[naver_relation_config][naver_relation_use]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
      <input type="hidden" name="mode" value="naver_relation_config" />
   
      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("네이버 연관채널 연동설정")?></h4>
         </div>
   
         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("네이버 연관채널 사용여부")?></label>
               <div class="col-md-10">
               	  <input type="radio" class="radio-inline" name="naver_relation_use" value="N" <?=$checked[naver_relation_use][N]?>> <?=_("사용안함")?>
               	  <input type="radio" class="radio-inline" name="naver_relation_use" value="Y" <?=$checked[naver_relation_use][Y]?>> <?=_("사용")?>
               </div>
            </div>
   
            <div class="form-group" id="naver_repation_use_div">
               <label class="col-md-2 control-label">
               	  <?=_("사이트와 연관된 채널 URL")?><br>
               	  <button type="button" class="btn btn-sm btn-info" onclick="add();"><?=_("추가")?></button>
               </label>
               <div class="col-md-10 form-inline">
               	  <div id="sameAs_div">
               	  	<? if (count($cfg[naver_relation_config][naver_relation_sameAs]) > 0) { ?>
               	  		<? foreach ($cfg[naver_relation_config][naver_relation_sameAs] as $k=>$v) { ?>
               	  			<div style="margin-bottom:5px;">
               	  				<input type="text" class="form-control" name="naver_relation_sameAs[]" value="<?=urldecode($v)?>" size="100">
               	  				<button type="button" class="btn btn-xs btn-danger" onclick="remove(this)" style="margin-bottom:10px;"><?=_("삭제")?></button>
               	  			</div>
               	  		<? } ?>
               	  	<? } else { ?>
               	  		<div style="margin-bottom:5px;">
               	  			<input type="text" class="form-control" name="naver_relation_sameAs[]" value="" size="100">
               	  			<button type="button" class="btn btn-xs btn-danger" onclick="remove(this)" style="margin-bottom:10px;"><?=_("삭제")?></button>
               	  		</div>
               	  	<? } ?>
                  </div>
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

<!--카피용-->
<div id="sameAs_copy_div" style="display:none">
	<div style="margin-bottom:5px;">
		<input type="text" class="form-control" name="naver_relation_sameAs[]" value="" size="100">
		<button type="button" class="btn btn-xs btn-danger" onclick="remove(this)" style="margin-bottom:10px;"><?=_("삭제")?></button>
	</div>
</div>

<script type="text/javascript">
$j(function() {
	$j("[name=naver_relation_use][value=<?=$cfg[naver_relation_config][naver_relation_use]?>]").trigger("click");
});

$j("[name=naver_relation_use]").click(function() {
	if ($j(this).val() == "Y") {
		$j("#naver_repation_use_div").slideDown();
		$j('input','#naver_repation_use_div').attr('disabled', false);
	} else {
		$j("#naver_repation_use_div").slideUp();
		$j('input','#naver_repation_use_div').attr('disabled', true);
	}
});

function add() {
	var target = $j("#sameAs_div");
	var inner = $j("#sameAs_copy_div").html();
	
	$j(inner).clone().appendTo(target);
}
function remove(obj){
	$j(obj).parent().remove();
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>