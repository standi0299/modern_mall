<?

include "../_header.php";
include "../_left_menu.php";

$cfg[main_page] = getCfg('main_page');
$cfg[main_page_popup] = getCfg('main_page_popup');

if (!$cfg[main_page_popup]) $checked[main_page_popup][0] = "checked";

$checked[main_page_popup][$cfg[main_page_popup]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="main_page" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("메인페이지 접근제어")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("메인페이지 접근제어")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="main_page" value="<?=$cfg[main_page]?>" size="40">
      	 		<div><span class="warning">[주의]</span> 도메인을 제외한 URL를 입력해주시기 바랍니다.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
            <label class="col-md-2 control-label"><?=_("팝업 사용여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="main_page_popup" value="1" <?=$checked[main_page_popup][1]?>>
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