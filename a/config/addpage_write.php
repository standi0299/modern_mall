<?

include "../_header.php";
include "../_left_menu.php";

if ($_GET[type]) {
    $m_config = new M_config();

    $addWhere = "where cid='$cid' and type='$_GET[type]'";
    $data = $m_config->getAddpageInfo($cid, $_GET[type], $addWhere);
}

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="addpage" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("추가페이지 추가/수정")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("타입")?></label>
            <div class="col-md-2">
               <input type="text" class="form-control" name="type" value="<?=$data[type]?>" required>
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("설명")?></label>
            <div class="col-md-10">
               <input type="text" class="form-control" name="memo" value="<?=$data[memo]?>" required>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("내용")?></label>
            <div class="col-md-10">
               <textarea id="contents" name="contents" type="editor" style="width:100%;height:500px;"><?=$data[msg1]?></textarea>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"></label>
            <div class="col-md-10">
                <button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
                <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
            </div>
         </div> 
      </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
$j(function() {
    $j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
        if (event.which && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});

var handleDatepicker = function() {
    $('.input-daterange').datepicker({
        language : 'kor',
        todayHighlight : true,
        autoclose : true,
        todayBtn : true,
        format : 'yyyy-mm-dd',
    });
};

handleDatepicker();

var oEditors = [];
smartEditorInit("contents", true, "editor", true);

function submitContents(formObj) {
    if (sendContents("contents", false)) {
        try {
            formObj.contents.value = Base64.encode(formObj.contents.value);
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