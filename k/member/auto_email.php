<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";

if (!$_GET[type]) $_GET[type] = "register";

$tableName = "exm_automsg";
$bRegistFlag = false;
$selectArr = "*";
$whereArr = array("cid" => "$cid", "catnm" => "mail", "type" => "$_GET[type]");

$data = SelectInfoTable($tableName, $selectArr, $whereArr, $bRegistFlag, $orderby);

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

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("자동메일 관리")?></h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("구분")?></label>
               <div class="col-md-3">
                  <select name="type" class="form-control" onchange="selectMail(this.value)">
                  <option value="register" <?=$selected[type][register]?>><?=_("회원가입시")?>
                  <option value="order" <?=$selected[type][order]?>><?=_("주문접수시")?>
                  <option value="payment" <?=$selected[type][payment]?>><?=_("입금확인시")?>
                  <option value="shipping" <?=$selected[type][shipping]?>><?=_("발송완료시")?>
                  <option value="reminderpw" <?=$selected[type][reminderpw]?>><?=_("비밀번호찾기시")?>
                  <option value="order_canc" <?=$selected[type][order_canc]?>><?=_("주문취소시")?>
                  <option value="quiescence_account_notice" <?=$selected[type][quiescence_account_notice]?>><?=_("휴면계정전환안내")?>
                  <option value="quiescence_account" <?=$selected[type][quiescence_account]?>><?=_("휴면계정전환")?>
                  <option value="restore_number" <?=$selected[type][restore_number]?>><?=_("휴면해지인증번호안내")?>
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

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("제목")?></label>
               <div class="col-md-4">
                  <input type="text" name="subject" class="form-control" value="<?=$data[subject]?>"/>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("전화번호")?></label>
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
function selectMail(type){
   if (type) location.replace("auto_email.php?type=" + type);
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>