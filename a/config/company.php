<?

include "../_header.php";
include "../_left_menu.php";

$cfg[company] = getCfg('company');

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="company" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("회사소개")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<textarea id="company" name="company" type="editor" style="width:100%;height:500px;"><?=$cfg[company]?></textarea>
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

<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
var oEditors = [];
smartEditorInit("company", true, "editor", true);

function submitContents(formObj) {
	if (sendContents("company", false)) {
    	try {
    		formObj.company.value = Base64.encode(formObj.company.value);
            return form_chk(formObj);
        } catch(e) {
        	alert(e.message);
        	return false;
        }
    }
    return false;
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>