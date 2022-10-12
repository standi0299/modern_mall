<?

include "../_header.php";
include "../_left_menu.php";

$cfg[edking_flag] = getCfg('edking_flag');
$cfg[edking_url] = getCfg('edking_url');

if (!$cfg[edking_flag]) $checked[edking_flag][0] = "checked";

$checked[edking_flag][$cfg[edking_flag]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="edking" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("편집왕관리")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
            <label class="col-md-2 control-label"><?=_("편집왕 사용여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="edking_flag" value="1" <?=$checked[edking_flag][1]?>>
            </div>
         </div>
      	
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("편집왕 연동 URL")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="edking_url" value="<?=$cfg[edking_url]?>" size="40">
      	 		<div><span class="notice">[설명]</span> 편집왕관련 웹서비스를 연동할 URL를 입력해주시기 바랍니다. (예:http://podstation20.ilark.co.kr/commonRef/StationWebService/GetPreViewImg.aspx)</div>
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