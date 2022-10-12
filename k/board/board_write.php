<?
include_once "../_header.php";
include_once "../_left_menu.php";


	$deco[color] = array(
  	"red"   => _("적색"),
    "green" => _("녹색"),
    "pink"  => _("분홍"),
    "blue"  => _("파랑")
	);

	$deco['font-size'] = array(
    "8pt","9pt","10pt","11pt","12pt","13pt","14pt",
	);

$m_pretty = new M_pretty();
$m_board = new M_board();
if (!$_GET[mode]) $_GET[mode]="write";

if (!$_GET[board_id]) $_GET[board_id] = $_GET[board];
//게시판 세팅 데이터
$board = $m_pretty->getBoardSetting($cid, $_GET[board_id]);

if ($board[on_category]){
	$board[category] = array_notnull(explode(",",$board[category]));
  if (!$board[category]) $board[on_category] = 0;
}



	switch ($_GET[mode]) {
		case 'write':
			$title = _("글 쓰기");
			
			$board_set = $m_board->getBoardSetList($cid);
			foreach ($board_set as $key => $value) {
				$r_board[$value[board_id]] = $value[board_name];
			}
			
			$data[secret] = ($board[on_secret]=="1") ? "1":"0";
			$selected[board][$_GET[board]] = "selected";	
			break;
			
		case 'modify':
			$title = _("글 수정");

			
		case 'view':
			if($_GET[no]) 
			{   
				$data = $m_board->getBoardInfo($cid, $_GET[board_id], $_GET[no]);
				$data[subject_deco] = array_notnull(explode(";",$data[subject_deco]));
			
				foreach ($data[subject_deco] as $v){
					$v = explode(":",$v);
					$selected[$v[0]][$v[1]] = "selected";
				}
			}
  		
			$r_file = $m_board->getBoardFileData($cid, $_GET[no], $_GET[board_id]);
   		$data[r_file] = $r_file;
			$category = $data[category];
			
			if ($data[secret]) $checked[secret] = "checked";
			if ($data[notice] == "-1") $checked[notice] = "checked";
			
			break;
		
		
		case 'reply':
			$data = $db->fetch("select * from exm_board where cid = '$cid' and board_id = '$_GET[board_id]' and no = '$_GET[no]'");
    	$data[name] = '';
    	$data[subject] = "[RE] ".$data[subject];
    	$data[content] = "<p>====================================="._("원글")."=====================================<br/>".$data[subject]."<br>".$data[content]."</br>====================================="._("원글")."=====================================</p>";
		
			break;
		
		default:
			
			break;
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
            <input type="hidden" name="rurl" value="<?=$_SERVER[HTTP_REFERER]?>">

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
            
            <?	if ($board[on_email]) {	?>
            <div class="form-group">
               <label class="col-md-1 control-label"><?=_("이메일")?></label>
               <div class="col-md-5">
                  <input type="text" class="form-control" name="email" value="<?=$data[email]?>" required/>
               </div>
            </div>
            <?	}	?>

            
            <? if ($board[on_subject_deco])  {	?>
            <div class="form-group">
            	<label class="col-md-2 control-label"><?=_("제목 꾸미기")?></label>
            	<? if ($board[on_subject_deco] & 1) {	?>
            	<label class="col-md-1 control-label"><?=_("굵기")?></label>
              <div class="col-md-2">
              	<select name="subject_deco[font-weight]" class="form-control">
                  <option value=""><?=_("기본")?></option>                  
									<option value="bold" <?=$selected['font-weight'][bold]?>><?=_("굵기")?></option>									
                </select>
              </div>
              <?	}	?>
              <? if ($board[on_subject_deco] & 2) {	?>
              <label class="col-md-1 control-label"><?=_("색상")?></label>
              <div class="col-md-2">
              	<select name="subject_deco[color]" class="form-control">
                  <option value=""><?=_("기본")?></option>
                  <?foreach($deco[color] as $k=>$v){?>
									<option value="<?=$k?>" style="color:<?=$k?>" <?=$selected[color][$k]?>><?=$v?>
									<?}?>
                </select>
              </div>
              <?	}	?>
              <? if ($board[on_subject_deco] & 4) {	?>
              <label class="col-md-1 control-label"><?=_("크기")?></label>
              <div class="col-md-2">
              	<select name="subject_deco[font-size]" class="form-control">
                  <option value=""><?=_("전체")?></option>
                  <?foreach($deco['font-size'] as $k=>$v){?>
									<option value="<?=$v?>" <?=$selected['font-size'][$v]?>><?=$v?>
									<?}?>
                </select>
              </div>
              <?	}	?>
						</div>
            <?	}	?>
            
            
            <div class="form-group">
            	<?	if ($_GET[mode] == "write")	{	?>
            	<label class="col-md-1 control-label"><?=_("게시판")?></label>
              <div class="col-md-2">
                  <select name="board" class="form-control">
                    <option value=""><?=_("선택하세요")?></option>
                    <?foreach($r_board as $k=>$v){?>
										<option value="<?=$k?>" <?=$selected[board][$k]?>><?=$v?>
										<?}?>
                  </select>
              </div>
              <?	} else {	?>
              <label class="col-md-1 control-label"></label>	
              <?	}	?>
                     
              <div class="col-md-6">              	
              	<? if ($ici_admin)	{	?>
                  <label class="radio-inline">
                      <input type="checkbox" name="notice" value="-1" <?=$checked[notice]?>/> <?=_("알림글로 작성합니다.")?>
                  </label>
              	<?	}	?>
              	
              	<?	if (in_array($board[on_secret] ,array(0,1))) {	?>    
                  <label class="radio-inline">
                      <input type="checkbox" name="secret" value="1" <?=$checked[secret]?> /> <?=_("비밀글로 작성합니다.")?>
                  </label>
                <?	} else if ($board[on_secret] == 3) {	?>
                  <label class="radio-inline">
                      <input type="checkbox" name="secret" value="1" style="display:none" checked /> <?=_("작성된 게시물은 게시판 정책에 의해 비밀글로 저장됩니다.")?>
                  </label>
                <?	}	?>
							</div>
							
							<?	if ($board[on_category]) {	?>
              <label class="col-md-1 control-label"><?=_("분류")?></label>
              <div class="col-md-2">
                  <select name="category" class="form-control">
                    <option value=""><?=_("선택하세요")?></option>
                    <?foreach($board[category] as $k=>$v){?>
										<option value="<?=$v?>" <? if ($category == $v) echo "selected"; ?>><?=$v?>
										<?}?>
                  </select>
              </div>
              <?	}	?>
              
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

            <? if($_GET[mode] != "view") { ?>

               <? if($board[on_file] ) { ?>
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("파일")?></label>
                     <div class="col-md-11">
                        <? for($i=0; $i<$board[limit_filecount]; $i++) { ?>

                           <? if($_GET[mode] == "modify" && $data[r_file][$i]) { ?>
                           <?=_("기존 파일")?> :
                           <?=$data[r_file][$i][filename]?>
                           <?=round($data[r_file][$i][filesize]/1024,2)?> KByte
                           <input type="checkbox" name="delfile[<?=$i?>]" value="1"> delfile..
                           <? } ?>

                           <input type="file" class="form-control" name="file[]"><br>
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
                     <button type="button" class="btn btn-sm btn-default" onclick="history.back();"><?=_("취소")?></button>
                  </div>
               </div>
            <? } ?>
            
            
            <? if($_GET[mode] == "view") { ?>
            	<div class="form-group">
              	<div class="col-md-11">
                	<button type="button" class="btn btn-sm btn-success" onclick="location.href='board_write.php?mode=reply&board_id=<?=$_GET[board_id]?>&no=<?=$_GET[no]?>';" ><?=_("답변")?></button>                     
                  <button type="button" class="btn btn-sm btn-danger" onclick="location.href='indb.php?mode=del&board_id=<?=$_GET[board_id]?>&no=<?=$_GET[no]?>';"><?=_("삭제")?></button>
                  <button type="button" class="btn btn-sm btn-default" onclick="history.back();"><?=_("취소")?></button>                     
             		</div>
							</div>
						<? } ?>
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
		<?	if ($_GET[mode] == "write")	{	?>
			if (formObj.board.value == '') {
				alert('<?=_("게시판을 선택해주세요")?>');
				return false;
			}			
		<?	}	?>
	
		if (formObj.subject.value == '') {
			alert('<?=_("제목을 입력해주세요")?>');
			return false;
		}
			
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