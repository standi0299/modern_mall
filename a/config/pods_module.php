<?

include "../_header.php";
include "../_left_menu.php";

if($cfg[pod_module_intro_use] == "Y")
   $pod_module_intro_use_chk[Y] = "checked";
else
   $pod_module_intro_use_chk[N] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
   <input type="hidden" name="mode" value="pods_module" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("POD모듈 편집기호출사이즈")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("호출사이즈")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<input type="text" class="form-control" name="pods_size" value="<?=$cfg[pods_size]?>" size="40">
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("x,y,width,height^maximize 또는 normal와 같이 설정해주시면 됩니다. (예:0,0,1024,680^normal)")?></div>
      	 	</div>
      	 </div>
      </div>
   </div>
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("POD모듈 INTRO관리")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("내용")?></label>
      	 	<div class="col-md-10">
               POD모듈INTRO 사용 여부
               <input type="radio" name="pod_module_intro_use" value="Y" <?=$pod_module_intro_use_chk[Y]?>>사용
               <input type="radio" name="pod_module_intro_use" value="N" <?=$pod_module_intro_use_chk[N]?>>미사용
      	 	   <br><br>
      	 		<div style="font-size:14px;"><b>width : 1011px</b>,<b>height : 586px</b></div>
      	 		<textarea id="pod_intro" name="pod_intro" type="editor" style="width:100%;height:500px;"><?=$cfg[pod_intro]?></textarea>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("기본적으로 출력되는 INTRO로 카테고리 INTRO를 사용할 경우 해당 INTRO는 출력되지 않습니다.")?></div>
      	 		<div>
      	 			<span class="notice">[<?=_("설명")?>]</span> <?=_("정보치환코드")?><br />
	      	 	    <div class="textIndent"><?=_("총판매금액")?> : {price_total}</div>
	      	 	    <div class="textIndent"><?=_("총적립금")?> : {reserve_total}</div>
	      	 	    <div class="textIndent"><?=_("상품판매가격")?> : {price}</div>
	      	 	    <div class="textIndent"><?=_("상품적립금")?> : {reserve}</div>
	      	 	    <div class="textIndent"><?=_("기본옵션판매가격")?> : {price_opt}</div>
	      	 	    <div class="textIndent"><?=_("기본옵션적립금")?> : {reserve_opt}</div>
	      	 	    <div class="textIndent"><?=_("추가옵션판매가격")?> : {price_addopt}</div>
	      	 	    <div class="textIndent"><?=_("추가옵션적립금")?> : {reserve_addopt}</div>
	      	 	    <div class="textIndent"><?=_("제조사")?> : {release}</div>
	      	 	    <div class="textIndent"><?=_("브랜드")?> : {brand}</div>
	      	 	    <div class="textIndent"><?=_("배송비")?> : {shipprice}</div>
	      	 	    <div class="textIndent"><?=_("수량")?> : {ea}</div>
	      	 	    <div class="textIndent"><?=_("제작기간")?> : {leadtime}</div>
      	 		</div>
      	 	</div>
      	 </div>
      </div>
   </div>
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("POD모듈 편집기로고")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("로고이미지")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<? if (is_file("../../data/pod/$cid/pod_logo.png")) { ?><img src="../../data/pod/<?=$cid?>/pod_logo.png" /><? } ?>
               	<input type="file" class="form-control" name="pod_logo" size="40">
			   	<? if (is_file("../../data/pod/$cid/pod_logo.png")) { ?><input type="checkbox" class="form-control" name="d[pod_logo]/"> <?=_("로고 삭제")?><? } ?>
			   	<div><span class="warning">[<?=_("주의")?>]</span> <b>157px * 21px</b> <?=_("이하 사이즈의")?> <b>png</b> <?=_("파일로 업로드해주세요.")?></div>
      	 	</div>
      	 </div>
      </div>
   </div>
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("POD모듈 편집기타이틀")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("타이틀이미지")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<? if (is_file("../../data/pod/$cid/pod_title.png")) { ?><img src="../../data/pod/<?=$cid?>/pod_title.png" /><? } ?>
               	<input type="file" class="form-control" name="pod_title" size="40">
			   	<? if (is_file("../../data/pod/$cid/pod_title.png")) { ?><input type="checkbox" class="form-control" name="d[pod_title]/"> <?=_("타이틀 삭제")?><? } ?>
			   	<div><span class="warning">[<?=_("주의")?>]</span> <b>60px * 11px</b> <?=_("이하 사이즈의")?> <b>png</b> <?=_("파일로 업로드해주세요.")?></div>
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

<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
var oEditors = [];
smartEditorInit("pod_intro", true, "editor", true);

function submitContents(formObj) {
	if (sendContents("pod_intro", false)) {
    	try {
    		formObj.pod_intro.value = Base64.encode(formObj.pod_intro.value);
            return form_chk(formObj);
        } catch(e) {
        	alert(e.message);
        	return false;
        }
    }
    return false;
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>