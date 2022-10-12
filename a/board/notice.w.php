<?
include_once "../_header.php";
include_once "../_left_menu.php";

$m_pretty = new M_pretty();

if (!$_GET[mode]) $_GET[mode]="write";

//게시판 세팅 데이터
$board = $m_pretty->getBoardSetting($cid, $_GET[board_id]);

//게시판 데이터
if($_GET[no]) {
   $addQuery = "and no = $_GET[no]";
   $data = $m_pretty->getNoticeBoard($cid, $_GET[board_id], $addQuery);
}

if($_GET[mode] == "modify"){
   $title = _("글 수정");

   $r_file = $m_pretty->getFileData($cid, $_GET[no], $_GET[board_id]);
   $data[r_file] = $r_file;

} else if($_GET[mode] == "view") {
   $title = _("글 보기");
   $r_file = $m_pretty->getFileData($cid, $_GET[no], $_GET[board_id]);
   $data[r_file] = $r_file;

} else if($_GET[mode] == "write"){
   $title = _("글 쓰기");
}
//debug($data[r_file]);
//debug($board[on_file]);
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse">
      <div class="panel-heading">
         <h4 class="panel-title"><?=_($title)?></h4>
      </div>

      <div class="panel-body panel-form">
         <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php"  enctype="multipart/form-data" onsubmit="return submitContents(this);">
            <input type="hidden" name="mode" value="<?=$_GET[mode]?>">
            <input type="hidden" name="board_id" value="<?=$_GET[board_id]?>">
            <input type="hidden" name="no" value="<?=$_GET[no]?>">

            <div class="form-group">
               <label class="col-md-1 control-label"><?=_("제목")?></label>
               <div class="col-md-5">
                  <input type="text" class="form-control" name="subject" value="<?=$data[subject]?>" required/>
               </div>

               <label class="col-md-1 control-label"><?=_("작성자")?></label>
               <div class="col-md-5">
                  <input type="text" class="form-control" name="name" value="<?=$data[name]?>" required/>
               </div>
            </div>

            <? if($_GET[mode] == "view") { ?>
               <? if($board[on_file] && $r_file) { ?>
               <div class="form-group">
                  <label class="col-md-1 control-label"><?=_("첨부파일")?></label>
                  <div class="col-md-11">
                     <? foreach($r_file as $key => $val) { ?>
                     <a href="download.php?board_id=<?=$board[board_id]?>&fileno=<?=$val[fileno]?>"><?=$val[filename]?> (<?=round($val[filesize]/1024,2)?> KBytes)</a><br>
                     <? } ?>
                  </div>
               </div>
               <? } ?>
            <? } ?>

            <div class="form-group">
               <label class="col-md-1 control-label"><?=_("내용")?></label>
               <div class="col-md-11">
                  <textarea name="content" id="contents" style="width:100%;height: 400px"><?=$data[content]?></textarea>
               </div>
            </div>

            <? if($_GET[mode] == "write" || $_GET[mode] == "modify") { ?>

               <? if($board[on_file] ) { ?>
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("파일 업로드")?></label>
                     <div class="col-md-11">
                        <? for($i=0; $i<$board[limit_filecount]; $i++) { ?>

                           <? if($_GET[mode] == "modify" && $data[r_file][$i]) { ?>
                           <?=_("기존 파일")?> :
                           <?=$data[r_file][$i][filename]?>
                           <?=round($data[r_file][$i][filesize]/1024,2)?> KByte
                           <input type="checkbox" name="delfile[<?=$i?>]" value="1"> delfile..
                           <? } ?>

                           <input type="file" class="form-control" name="file[]" style="width: 300px;"><br>
                        <? } ?>
                     </div>
                  </div>
               <? } ?>

               <? if($board[limit_filesize]) { ?>
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("파일제한")?></label>
                     <div class="col-md-11">
                        <?=_("하나의 파일은")?> <b><?=number_format($board[limit_filesize])?></b> <?=_("KByte까지 허용되며, 초과시 첨부가 되지 않습니다.")?>
                     </div>
                  </div>
               <? } ?>

               <div class="form-group">
                  <div class="col-md-11">
                     <button type="submit" class="btn btn-sm btn-warning"><?=_("확인")?></button>
                  </div>
               </div>
            <? } ?>

               <div class="form-group">
                  <div class="col-md-11">
                     <button type="button" class="btn btn-sm btn-success" onClick="history.back();"><?=_("목록")?></button>
                  </div>
               </div>
         </form>
      </div>
   </div>
</div>

<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script type="text/javascript">
var oEditors = [];
smartEditorInit("contents", true, "editor", true);

function submitContents(formObj) {
   if (sendContents("contents", true))
   {
      try {
         return form_chk(formObj);
      } catch(e) {return false;}
   }
   return false;
}

</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>