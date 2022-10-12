<?

include "../_header.php";
include "../_left_menu.php";

if ($_GET[mode] == "modify") {
	$m_config = new M_config();
	
	$addWhere = "where cid='$cid' and mid='$_GET[mid]'";
	$data = $m_config->getAdminInfo($cid, $_GET[mid], $addWhere);
}

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="<?=$_GET[mode]?>" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("관리자추가/수정")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("아이디")?></label>
            <div class="col-md-10 form-inline">
               <? if ($_GET[mode] == "modify") { ?>
               	  <input type="hidden" name="mid" value="<?=$data[mid]?>" />
               	  <b><?=$data[mid]?></b>
               <? } else { ?>
               	  <input type="text" class="form-control" name="mid" value="<?=$data[mid]?>" pt="_pt_id" maxlength="20" onchange="chk_mid(this);" required>
               	  <div id="vMid"></div>
               <? } ?>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("비밀번호")?></label>
            <div class="col-md-10 form-inline">
               <input type="password" class="form-control" name="password" size="40" pt="_pt_pw" onchange="chk_password(this);" <? if ($_GET[mode] == "write") { ?>required<? } ?>>
               <div id="vPass1"><span class="notice">[설명]</span> 등록 혹은 비밀번호 변경시에만 입력해주세요.</div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("비밀번호확인")?></label>
            <div class="col-md-10 form-inline">
               <input type="password" class="form-control" name="password2" size="40" pt="_pt_pw" onchange="chk_password(this);" <? if ($_GET[mode] == "write") { ?>required<? } ?>>
               <div id="vPass2"><span class="notice">[설명]</span> 등록 혹은 비밀번호 변경시에만 입력해주세요.</div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("이름")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="name" value="<?=$data[name]?>" required>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("로그인후 이동페이지<br>(관리자)")?></label>
            <div class="col-md-5">
               <input type="text" class="form-control" name="redirect_url" value="<?=$data[redirect_url]?>">
            </div>
         </div>
         
         <? if ($_GET[mode] == "modify") { ?>
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("등록일")?></label>
            <div class="col-md-10">
               <?=$data[regdt]?>
            </div>
         </div>
         <? } ?>
         
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

<script type="text/javascript">
function chk_mid(val) {
	if (!fm.mid.value) {
		vMid.innerHTML = "아이디를 입력해주세요.";
		fm.mid.focus();
		return false;
	} else if (!_pattern(fm.mid)) {
		vMid.innerHTML = "아이디는 영소문자,숫자,-,_ 를 사용하여 4~20자로 입력해주세요.";
		fm.mid.focus();
		fm.mid.value = "";
		return false;
	}
	
	vMid.innerHTML = "";
}

function chk_password(val) {	
	if (!fm.password.value) {
		vPass1.innerHTML = "비밀번호를 입력해주세요.";
		fm.password.focus();
		return false;
	} else if (!_pattern(fm.password)) {
		vPass1.innerHTML = "비밀번호는 영소문자,숫자,-,_ 를 사용하여 6~20자로 입력해주세요.";
		fm.password.focus();
		fm.password.value = "";
		return false;
	}
	
	vPass1.innerHTML = "<span class='notice'>[설명]</span> 등록 혹은 비밀번호 변경시에만 입력해주세요.";
	
	if (fm.password.value != fm.password2.value) {
		vPass2.innerHTML = "비밀번호 불일치";
		fm.password2.focus();
		fm.password2.value = "";
		return false;
	}
	
	vPass2.innerHTML = "비밀번호 일치";
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>