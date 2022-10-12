<?

include "../_header.php";
include "../_left_menu.php";

$cfg[policy] = getCfg('policy');
$cfg[agreement2] = getCfg('agreement2');
$cfg[agreement_thirdparty] = getCfg('agreement_thirdparty');
$cfg[nonmember_agreement] = getCfg('nonmember_agreement');
$cfg[privacy_agreement] = getCfg('privacy_agreement');

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
   <input type="hidden" name="mode" value="policy" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("개인정보 취급방침")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("개인정보파일")?></label>
            <div class="col-md-10 form-inline">
               <input type="file" class="form-control" name="file1" size="40">
               <? if (is_file("../../w3c/p3p.xml")) { ?>p3p.xml 파일이 존재합니다.<? } else { ?>p3p.xml 파일이 존재하지 않습니다.<? } ?><p><p>
               <input type="file" class="form-control" name="file2" size="40">
               <? if (is_file("../../w3c/p3policy.xml")) { ?>p3policy.xml 파일이 존재합니다.<? } else { ?>p3policy.xml 파일이 존재하지 않습니다.<? } ?>
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("개인정보 취급방침")?></label>
            <div class="col-md-10">
      	 		<textarea id="policy" class="form-control" name="policy"><?=$cfg[policy]?></textarea>
      	 		<div><span class="warning">[주의]</span> 개인정보 취급방침은 예시이며, 반드시 해당 몰의 방침에 맞게 전자적표시를 작성하시기 바랍니다.</div>
      	 		<div>
      	 			<span class="notice">[설명]</span> 개인정보 취급방침 전자적표시 적용하기<br />
      	 			<div class="textIndent">1. <a href="http://www.privacy.go.kr/a3sc/per/inf/perInfStep01.do" target="_blank"><b>"개인정보 취급방침 전자적표시 작성하기"</b></a>를 클릭하고, 사용자방침파일을 작성합니다.</div>
      	 			<div class="textIndent">2. 작성된 파일을 다운로드 받아 위의 업로드창에 각 파일을 업로드 합니다.</div>
      	 		</div>
      	 		<div><span class="notice">[설명]</span> <a href="http://www.checkprivacy.co.kr/make/manual.pdf" target="_blank"><b>전자적표시 소프트웨어 메뉴얼보기 (바로가기)</b></a></div>
      	 		<div><span class="orange">[기타]</span> 모바일 페이지 주소 http://<?=$_SERVER[HTTP_HOST]?>/service/policy_mobile.php <button type="button" class="btn btn-sm btn-default" style="margin-top:-5px;" onclick="location.href='indb.php?mode=policy_mobile'">주소복사</button></div>
      	 	</div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("개인정보 수집동의")?></label>
            <div class="col-md-10">
      	 		<textarea id="agreement2" class="form-control" name="agreement2"><?=$cfg[agreement2]?></textarea>
      	 		<div><span class="notice">[설명]</span> 개인정보 수집동의는 비회원구매시 개인정보수집 동의를 명시하는 부분에 나오게 됩니다.</div>
      	 	</div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("제3자 정보제공동의")?></label>
            <div class="col-md-10">
      	 		<textarea id="agreement_thirdparty" class="form-control" name="agreement_thirdparty"><?=$cfg[agreement_thirdparty]?></textarea>
      	 	</div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("비회원구매시 노출되는 개인정보 취급방침")?></label>
            <div class="col-md-10">
      	 		<textarea id="nonmember_agreement" class="form-control" name="nonmember_agreement"><?=$cfg[nonmember_agreement]?></textarea>
      	 	</div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("개인정보 마케팅활용동의")?></label>
            <div class="col-md-10">
      	 		<textarea id="privacy_agreement" class="form-control" name="privacy_agreement"><?=$cfg[privacy_agreement]?></textarea>
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

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>