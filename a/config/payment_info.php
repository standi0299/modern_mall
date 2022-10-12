<?

include "../_header.php";
include "../_left_menu.php";

$selected[paymethod][$_GET[paymethod]] = "selected";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="payment_info" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("결제수단별 안내관리")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("결제수단선택")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<select name="paymethod" class="form-control" id="paymethod_select" required>
      	 			<option value=""><?=_("선택")?></option>
      	 			<? if ($cfg[pg][paymethod]) foreach ($cfg[pg][paymethod] as $k=>$v) { ?>
						<option value="<?=$v?>" <?=$selected[paymethod][$v]?>><?=$r_paymethod[$v]?></option>
					<? } ?>
					<? if ($cfg[pg][e_paymethod]) foreach ($cfg[pg][e_paymethod] as $k=>$v) { ?>
						<option value="<?=$v?>" <?=$selected[paymethod][$v]?>><?=$r_paymethod[$v]?></option>
					<? } ?>
      	 		</select>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
            <label class="col-md-2 control-label"><?=_("내용")?></label>
            <div class="col-md-10">
            	<? if (!$_GET[paymethod]){ ?>
            		<div align="center"><?=_("결제수단을 선택해주세요.")?></div>
				<? } else { ?>
            		<textarea id="paymethod_info" name="paymethod_info" type="editor" style="width:100%;height:500px;"><?=$cfg[paymethod_info][$_GET[paymethod]]?></textarea>
            	<? } ?>
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
$j(function() {
	$j("#paymethod_select").change(function() {
		location.href = "?paymethod=" + $j(this).val();
	});
});

<? if ($_GET[paymethod]) { ?>
	var oEditors = [];
	smartEditorInit("paymethod_info", true, "editor", true);
	
	function submitContents(formObj) {
		if (sendContents("paymethod_info", false)) {
	    	try {
	    		formObj.paymethod_info.value = Base64.encode(formObj.paymethod_info.value);
	            return form_chk(formObj);
	        } catch(e) {
	        	alert(e.message);
	        	return false;
	        }
	    }
	    return false;
	}
<? } ?>
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>