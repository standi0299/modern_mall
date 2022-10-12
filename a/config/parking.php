<?

include "../_header.php";
include "../_left_menu.php";

$cfg[parking] = getCfg('parking');
$cfg[apply_parking] = getCfg('apply_parking');

if (!$cfg[apply_parking]) $checked[apply_parking][0] = "checked";

$checked[apply_parking][$cfg[apply_parking]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="parking" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("parking관리")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
            <label class="col-md-2 control-label"><?=_("parking 사용여부")?></label>
            <div class="col-md-10 form-inline">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="apply_parking" value="1" <?=$checked[apply_parking][1]?>>
               <div><span class="warning">[<?=_("주의")?>]</span> <?=_("parking페이지 사용시 운영중인 쇼핑몰의 접근이 차단될 수 있으니 사용여부를 반드시 확인해 주시기 바랍니다.")?></div>
               <div><span class="notice">[<?=_("설명")?>]</span> <?=_("parking페이지는 점검중 공지와 같이 쇼핑몰 운영을 중단하고 싶을 때 설정합니다.")?></div>
            </div>
         </div>
      	
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("내용")?></label>
      	 	<div class="col-md-10">
      	 		<textarea id="parking" name="parking" type="editor" style="width:100%;height:500px;"><?=$cfg[parking]?></textarea>
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
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
});

var oEditors = [];
smartEditorInit("parking", true, "editor", true);

function submitContents(formObj) {
	if (sendContents("parking", false)) {
    	try {
    		formObj.parking.value = Base64.encode(formObj.parking.value);
            return form_chk(formObj);
        } catch(e) {
        	alert(e.message);
        	return false;
        }
    }
    return false;
}
</script>

<? include "../_footer_app_exec.php"; ?>