<?

include "../_header.php";
include "../_left_menu.php";

$cfg[intro] = getCfg('intro');
$cfg[apply_intro] = getCfg('apply_intro');

if (!$cfg[apply_intro]) $checked[apply_intro][0] = "checked";

$checked[apply_intro][$cfg[apply_intro]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="intro" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("인트로관리")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
            <label class="col-md-2 control-label"><?=_("인트로 사용여부")?></label>
            <div class="col-md-10 form-inline">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="apply_intro" value="1" <?=$checked[apply_intro][1]?>>
               <div><span class="warning">[<?=_("주의")?>]</span> <?=_("인트로페이지 사용시 쇼핑몰의 메인화면이 바뀌게 됩니다.")?></div>
               <div><span class="notice">[<?=_("설명")?>]</span> <?=_("인트로페이지는 쇼핑몰의 첫화면을 변경하고 싶을 때 설정합니다.")?></div>
            </div>
         </div>
      	
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("내용")?></label>
      	 	<div class="col-md-10">
      	 		<textarea id="intro" name="intro" type="editor" style="width:100%;height:500px;"><?=$cfg[intro]?></textarea>
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
smartEditorInit("intro", true, "editor", true);

function submitContents(formObj) {
	if (sendContents("intro", false)) {
    	try {
    		formObj.intro.value = Base64.encode(formObj.intro.value);
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