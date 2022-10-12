<?

include "../_header.php";
include "../_left_menu.php";

if ($_GET[popupno]) {
    $m_config = new M_config();
    
    $addWhere = "where cid='$cid' and popupno='$_GET[popupno]'";
    $data = $m_config->getPopupInfo($cid, $_GET[popupno], $addWhere);
}

//기본값
if (!$data[skintype]) $data[skintype] = "pc|mobile";
if (!$data[width]) $data[width] = "0";
if (!$data[height]) $data[height] = "0";
if (!$data[top]) $data[top] = "0";
if (!$data[left]) $data[left] = "0";

$checked[state][$data[state]+0] = "checked";

//적용할 스킨
$r_skintype = explode("|", $data[skintype]);
foreach ($r_skintype as $val) {
	$checked[skintype][$val] = "checked";
}

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="popup" />
   <input type="hidden" name="popupno" value="<?=$data[popupno]?>" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("팝업추가/수정")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("타이틀")?></label>
            <div class="col-md-10">
               <input type="text" class="form-control" name="title" value="<?=$data[title]?>" required>
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("사용여부")?></label>
            <div class="col-md-10">
               <input type="radio" class="radio-inline" name="state" value="0" <?=$checked[state][0]?>> <b class="red">OFF</b>
               <input type="radio" class="radio-inline" name="state" value="1" <?=$checked[state][1]?>> <b class="notice">ON</b>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("적용할 스킨")?></label>
            <div class="col-md-10 form-inline">
               <label class="checkbox-inline">
               	  <input type="checkbox" name="skintype[]" value="pc" <?=$checked[skintype][pc]?>> <?=_("pc용 스킨")?>
               </label>
               <label class="checkbox-inline">
                  <input type="checkbox" name="skintype[]" value="mobile" <?=$checked[skintype][mobile]?>> <?=_("모바일용 스킨")?>
               </label>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("기간")?></label>
            <div class="col-md-3">
               <div class="input-group input-daterange">
                  <input type="text" class="form-control" name="sdt" placeholder="Date Start" value="<?=$data[sdt]?>" required>
                  <span class="input-group-addon"> ~ </span>
                  <input type="text" class="form-control" name="edt" placeholder="Date End" value="<?=$data[edt]?>" required>
               </div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("사이즈")?></label>
            <div class="col-md-10 form-inline">
               <input type="text" class="form-control" name="width" value="<?=$data[width]?>" size="4" maxlength="4" required type2="number"> px x 
               <input type="text" class="form-control" name="height" value="<?=$data[height]?>" size="4" maxlength="4" required type2="number"> px
               &nbsp;&nbsp;&nbsp;(<?=_("너비 × 높이 : 단위 픽셀(pixel)")?>)
               &nbsp;&nbsp;&nbsp; - <?=_("모바일용 스킨에는 적용되지 않습니다.")?>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("위치")?></label>
            <div class="col-md-10 form-inline">
               <input type="text" class="form-control" name="top" value="<?=$data[top]?>" size="4" maxlength="4" required type2="number"> px / 
               <input type="text" class="form-control" name="left" value="<?=$data[left]?>" size="4" maxlength="4" required type2="number"> px
               &nbsp;&nbsp;&nbsp;(<?=_("위로부터 / 좌로부터 : 단위 픽셀(pixel)")?>)
               &nbsp;&nbsp;&nbsp; - <?=_("모바일용 스킨에는 적용되지 않습니다.")?>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("내용")?></label>
            <div class="col-md-10">
               <textarea id="contents" name="contents" type="editor" style="width:100%;height:500px;"><?=$data[content]?></textarea>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"></label>
            <div class="col-md-10">
                <button type="submit" class="btn btn-sm btn-success" onclick="return chkSkintype();"><?=_("저장")?></button>
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

function chkSkintype() {
	var rtnFlag = false;
	
	$j("input[name=skintype[]]:checked").each(function() {
		rtnFlag = true;
	});
	
	if (!rtnFlag) {
		alert('<?=_("적용할 스킨을 선택해 주시기 바랍니다.")?>');
	}
	
	return rtnFlag;
}
</script>

<? include "../_footer_app_exec.php"; ?>