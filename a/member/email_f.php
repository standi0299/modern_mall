<?
   if(strpos($_SERVER['REMOTE_ADDR'], "210.96.184.229") > -1){

include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";

### 회원그룹 추출
$r_grp = getMemberGrp();
?>

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("메일발송")?></li>
   </ol>
   <h1 class="page-header"><?=_("메일발송")?> <small><?=_("회원 그룹별 메일을 발송할 수 있습니다.")?></small></h1>
   
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" target="procIfrm" encformid="multipart/form-data" onsubmit="return submitContents(this);">
   	<input type="hidden" name="mode" value="sendmail"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("메일발송")?></h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("받는사람")?></label>
               <div class="col-md-2">
                  <select name="vtype" class="form-control" onchange="on_vtype(this.value)">
					<option value="to">주소직접입력</option>
					<option value="member">회원에게 보내기</option>
                  </select>
               </div>
            </div>
			<div class="form-group" id="z_to">
               <label class="col-md-2 control-label"><?=_("주소직접입력")?></label>
               <div class="col-md-8">
                  <input type="text" name="to" style="width:69%"><br/>
                  <?=_("주소직접입력을 선택하신경우 각메일을 ; 로 구분하여 입력해주세요.")?>
               </div>
            </div>
			<div id="srch_address" style="position:absolute;border:1px solid #cccccc;z-index:100;display:none;margin-left:250px;">
				<iframe id="ifrmSearch" frameborder="0" style="width:400px;height:490px"></iframe>
			</div>
			<div class="form-group" style="display:none;" id="z_member">
               <label class="col-md-2 control-label"><?=_("그룹선택")?></label>
               <div class="col-md-6">
                  <select name="member">
					<option value="">전체회원
					<? foreach($r_grp as $k=>$v) { ?>
					<option value="<?=$k?>"><?=$v?></option>
					<? } ?>
				   </select>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("제목")?></label>
               <div class="col-md-10">
                  <input type="text" name="subject" style="width:55%" required label="제목" value="<?=$data[subject]?>">
               </div>
            </div>

			<div class="form-group">
               <label class="col-md-2 control-label"></label>
			   <div class="col-md-10">
                <iframe name="procIfrm" frameborder="0" style="width:100%;height:90px;overflow:auto"></iframe>  
               </div>
            </div>

            

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("내용")?></label>
               <div class="col-md-10">
                  <textarea name="contents" id="contents" style="width:60%;height:150px"><?=$data[msg1]?></textarea>
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
               - <?=_("[주의] 이메일에 이미지를 추가 할경우 해당 경로를 반드시 이미지의 절대경로 URL로 사용하셔야, 전송된메일에서 정상적으로 보여지게 됩니다.")?><br><br>

               - <?=_("[확인] 이메일보내기 기능은 보내는 메일에 사용한도가 있습니다.10,000통 이상의 메일은 이메일 전문서비스를 이용하시길 권합니다.")?><br><br>
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

function on_vtype(v)
{	
    _ID('z_to').style.display = _ID('z_member').style.display = "none";
    _ID('z_'+v).style.display = "block";
}
</script>


<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>

<? }else{ ?>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <script>
         alert("해당 서비스는 준비중입니다.");
         history.go(-1);
      </script>
   </head>
<? } ?>