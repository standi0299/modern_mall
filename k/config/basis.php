<?

include "../_header.php";
include "../_left_menu.php";

if (!$cfg[deny_robots]) $checked[deny_robots][0] = "checked";
if (!$cfg[AX_editor_use]) $checked[AX_editor_use][Y] = "checked";
if (!$cfg[local_storage_use]) $checked[local_storage_use][N] = "checked";
if (!$cfg[editlist_use]) $checked[editlist_use][N] = "checked";

$checked[deny_robots][$cfg[deny_robots]] = "checked";
$checked[AX_editor_use][$cfg[AX_editor_use]] = "checked";
$checked[local_storage_use][$cfg[local_storage_use]] = "checked";
$checked[editlist_use][$cfg[editlist_use]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="basis" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("사이트정보")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("사이트명")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="nameSite" value="<?=$cfg[nameSite]?>">
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("도메인")?></label>
            <div class="col-md-10 form-inline">
               http://<input type="text" class="form-control" name="urlSite" value="<?=$cfg[urlSite]?>" size="40">
               <div><span class="warning">[주의]</span> 임시도메인은 www를 제외하고 넣으시기 바랍니다.</div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("서비스도메인")?></label>
            <div class="col-md-10 form-inline">
               http://<input type="text" class="form-control" name="urlService" value="<?=$cfg[urlService]?>" size="40">
               <div><span class="warning">[주의]</span> 실제 서비스되는 도메인이며, 모든 도메인은 입력하신 도메인으로 이동됩니다.</div>
               <div><span class="warning">[주의]</span> 스튜디오업로드 사용에 장애가 발생할 수 있으니 주의하여 사용하시기 바랍니다.</div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("관리자메일")?></label>
            <div class="col-md-10 form-inline">
               <input type="text" class="form-control" name="emailAdmin" value="<?=$cfg[emailAdmin]?>" size="40">
               <div><span class="warning">[주의]</span> 관리자메일 미입력시 메일발송이 안됩니다. 주의하세요.</div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("SMS발송번호")?></label>
            <div class="col-md-10 form-inline">
               <input type="text" class="form-control" name="smsAdmin" value="<?=$cfg[smsAdmin]?>" size="40">
               <div><span class="warning">[주의]</span> SMS발송번호 미입력시 SMS발송이 되지 않습니다.</div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("저작권표시방법")?></label>
            <div class="col-md-10">
               <textarea id="copyright" class="form-control" name="copyright"><?=$cfg[copyright]?></textarea>
			   <div>
			   	  <span class="warning">[주의]</span> 년도를 입력하기 위해서는 취환코드를 입력해야 합니다. {YYYY}
			   	  <div class="textIndent">예) <b>{YYYY}</b> © ilark communication. ALL Rights Reserved. => 2017 © ilark communication. ALL Rights Reserved.</div>
			   </div>
            </div>
         </div>
      </div>
   </div>
   
   <!--<div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("회사정보")?></h4><p>
         <div><span class="warning">[주의]</span> 몰에서 사용하시는 택배사의 배송추적 정보를 입력하시면, 주문관리와 연동됩니다.</div>
         <div><span class="warning">[주의]</span> 회사정보는 이메일 양식에 연동되므로 아래 회사정보에서 각 항목의 값이 없을 경우 이메일 양식에도 출력되지 않으니 이점 유의해주시기 바랍니다.</div>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("상호명")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="nameComp" value="<?=$cfg[nameComp]?>">
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("업태")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="typeBiz" value="<?=$cfg[typeBiz]?>">
            </div>
            <label class="col-md-2 control-label"><?=_("종목")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="itemBiz" value="<?=$cfg[itemBiz]?>">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("사업자번호")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="regnumBiz" value="<?=$cfg[regnumBiz]?>">
            </div>
            <label class="col-md-2 control-label"><?=_("통신판매신고번호")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="regnumOnline" value="<?=$cfg[regnumOnline]?>">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("대표자명")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="nameCeo" value="<?=$cfg[nameCeo]?>">
            </div>
            <label class="col-md-2 control-label"><?=_("개인정보관리자")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="managerInfo" value="<?=$cfg[managerInfo]?>">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("주소")?></label>
            <div class="col-md-10 form-inline">
               <input type="text" class="form-control" name="zipcode" value="<?=$cfg[zipcode]?>" readonly>
               <i class="fa fa-search cursorPointer" onclick="javascript:popupZipcode<?=$language_locale?>();"></i><br><br>
               <input type="text" class="form-control" name="address" value="<?=$cfg[address]?>" size="100">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("물류센터")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="addressComp2" value="<?=$cfg[addressComp2]?>">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("전화번호")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="phoneComp" value="<?=$cfg[phoneComp]?>">
            </div>
            <label class="col-md-2 control-label"><?=_("팩스번호")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="faxComp" value="<?=$cfg[faxComp]?>">
            </div>
         </div>
      </div>
   </div>-->
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("출력/검색엔진 관련")?></h4>
      </div>

      <div class="panel-body panel-form">
         <!--<div class="form-group">
            <label class="col-md-2 control-label"><?=_("상단타이틀")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="titleDoc" value="<?=$cfg[titleDoc]?>" onkeyup="previewTitle(this);">
               <div id="titleDoc">
			      <img src="../img/bg_preview_title.gif" />
			      <div id="titlePreview"><?=$cfg[titleDoc]?> - Microsoft Internet Explorer</div>
			   </div>
            </div>
         </div>-->

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("파비콘설정")?></label>
            <div class="col-md-10 form-inline">
               <? if (is_file("../../data/favicon/$cid/$cfg[skin]/favicon.ico")) { ?><img src="../../data/favicon/<?=$cid?>/<?=$cfg[skin]?>/favicon.ico" /><? } ?>
               <input type="file" class="form-control" name="favicon" size="40">
			   <? if (is_file("../../data/favicon/$cid/$cfg[skin]/favicon.ico")) { ?><input type="checkbox" class="form-control" name="d[favicon]/"> 파비콘 삭제<? } ?>
			   <div><span class="warning">[주의]</span> 파일이름을 <b>favicon.ico</b>로 업로드해주세요. (사이즈 16px*16px)</div>
            </div>
         </div>
         
         <!--<div class="form-group">
            <label class="col-md-2 control-label"><?=_("검색엔진키워드")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="keywordsDoc" value="<?=$cfg[keywordsDoc]?>">
            </div>
         </div>-->
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("검색로봇 차단여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="deny_robots" value="1" <?=$checked[deny_robots][1]?>>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("ActiveX 안내창 사용여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="AX_editor_use" value="Y" <?=$checked[AX_editor_use][Y]?>>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("로컬보관함 사용여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="local_storage_use" value="Y" <?=$checked[local_storage_use][Y]?>>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("편집리스트 사용여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="editlist_use" value="Y" <?=$checked[editlist_use][Y]?>>
            </div>
         </div>
      </div>
   </div>
   
   <div class="row">
   	  <div class="col-md-12">
   	  	 <p class="pull-right">
   	  	 	<button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("저장")?></button>
   	  	 	<button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
   	  	 </p>
   	  </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>

<script type="text/javascript" src="../assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/form-slider-switcher.demo.js"></script>
<script type="text/javascript" src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
});
	
function submitContents(formObj) {
	try {
		formObj.copyright.value = Base64.encode(formObj.copyright.value);
		return true;
	} catch(e) {
	    alert(e.message);
	    return false;
	}
}

function previewTitle(obj) {
	$("#titlePreview").text(obj.value + " - Microsoft Internet Explorer");
}
</script>

<? include "../_footer_app_exec.php"; ?>