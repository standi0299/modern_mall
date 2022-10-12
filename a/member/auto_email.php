<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";

//게시판별 auto_msg 체크 및 등록 210517 jtkim
if($cfg[skin] == 'modern' && !$cfg[skin_theme]) setAutoMailByBoard();

$m_etc = new M_etc();
$m_board = new M_board();
$autoMsgList = $m_etc->getAutoMsgList($cid, 'mail_board');

if (!$_GET[type]) $_GET[type] = "register";
if (!$_GET[catnm]) $_GET[catnm] = "mail";

$tableName = "exm_automsg";
$bRegistFlag = false;
$selectArr = "*";
$whereArr = array("cid" => "$cid", "catnm" => "$_GET[catnm]", "type" => "$_GET[type]");

$data = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);

$selectData = array(
  array(
    "name" => "register",
    "catnm" => "mail",
    "title" => "회원가입시",
  ),
  array(
    "name" => "order",
    "catnm" => "mail",
    "title" => "주문접수시",
  ),
  array(
    "name" => "payment",
    "catnm" => "mail",
    "title" => "입금확인시",
  ),
  array(
    "name" => "shipping",
    "catnm" => "mail",
    "title" => "발송완료시",
  ),
  array(
    "name" => "reminderpw",
    "catnm" => "mail",
    "title" => "비밀번호찾기시",
  ),
  array(
    "name" => "order_canc",
    "catnm" => "mail",
    "title" => "주문취소시",
  ),
  array(
    "name" => "quiescence_account_notice",
    "catnm" => "mail",
    "title" => "휴면계정전환안내",
  ),
  array(
    "name" => "quiescence_account",
    "catnm" => "mail",
    "title" => "휴면계정전환",
  ),
array(
    "name" => "restore_number",
    "catnm" => "mail",
    "title" => "휴면해지인증번호안내",
  ),
);

if(count($autoMsgList) > 0){
  forEach($autoMsgList as $k){
    $boardData = $m_board->getBoardSetInfo($cid,$k["type"]);
	  array_push($selectData,array("name" => $k['type'], "catnm" => $k['catnm'] , "title" => "[게시판]".$boardData['board_name']." 작성시"));
  }
}

if (!$data[send]) $selected[send][0] = "checked";

$selected[type][$_GET[type]] = "selected";
$selected[send][$data[send]] = "checked";
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("자동메일 관리")?></li>
   </ol>
   <h1 class="page-header"><?=_("자동메일 관리")?> <small><?=_("소비자에게 유형별 메일을 자동으로 보내실 수 있습니다.")?></small></h1>

   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
   	<input type="hidden" name="mode" value="automail"/>
    <input type="hidden" name="catnm" value="<?=$_GET[catnm]?>"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("자동메일 관리")?></h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("구분")?></label>
               <div class="col-md-3">
                  <select name="type" class="form-control" onchange="selectMail(this)">
                    <?php forEach($selectData as $k => $v){ ?>
                      <option id="<?=$v['catnm']?>" value="<?=$v['name']?>" <?=$selected[type][$v['name']]?> ><?=$v['title']?></option>
                    <?php } ?>
                  </select>
                  <?=_("보내고자 하는 유형을 선택해주세요.")?>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("발송여부")?></label>
               <div class="col-md-4">
                  <label class="radio-inline">
                     <input type="radio" name="send" value="1" <?=$selected[send][1]?>/> <?=_("발송")?>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="send" value="0" <?=$selected[send][0]?>/> <?=_("미발송")?>
                  </label>
               </div>
            </div>

           <?php if($_GET[catnm] == 'mail_board'){ ?>
           <div class="form-group">
             <label class="col-md-2 control-label"><?=_("추가 수신 메일 주소")?></label>
             <div class="col-md-4">
               <input type="text" name="send_add_admin" class="form-control" value="<?=$data[send_add_admin]?>"/>
             </div>
           </div>
           <?php } ?>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("제목")?></label>
               <div class="col-md-4">
                  <input type="text" name="subject" class="form-control" value="<?=$data[subject]?>"/>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("내용")?></label>
               <div class="col-md-10">
                  <textarea name="content" id="contents" style="width:100%;height: 500px"><?=$data[msg1]?></textarea>
               </div>
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-md-11">
            <p class="pull-right">
   	    	   <button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("등록")?></button>
               <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
   	      </p>
         </div>
      </div>
   </form>

   <div class="panel panel-inverse">
      <div class="panel-body panel-form">
         <div class="form-group">
            <div class="col-md-12">
               <br>
               - <?=_("자동메일은 기본셋팅된 디자인으로 전송됩니다. 하단의 로고는 업체에 맞는 로고로 변경해주세요.")?><br><br>

               <?=_("[주의] 이미지경로는 반드시 http://사이트주소/ 로 시작하는 절대경로로 기입하셔야 합니다.")?><br><br>

               <?=_("[주의] {name} 과 같은 치환코드는 반드시 샘플에 있는 코드를 사용해야 이메일 전송시 자동으로 해당 정보가 들어갑니다.")?>
               <br><br>
            </div>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>


<script type="text/javascript">
var oEditors = [];
smartEditorInit("contents", true, "goods", true);

function submitContents(formObj) {
   if (sendContents("contents", false)) {
      try {
         formObj.contents.value = Base64.encode(formObj.contents.value);
         return form_chk(formObj);
      } catch(e) {return false;}
   }
   return false;
}
</script>

<script>
function selectMail(_this){
   if (_this.value && _this.options[_this.selectedIndex].id) location.replace("auto_email.php?type=" + _this.value + "&catnm=" + _this.options[_this.selectedIndex].id);
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>
