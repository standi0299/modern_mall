<?

include "../_header.php";
include "../_left_menu.php";

if (!$cfg[mobile_member_use]) $checked[mobile_member_use][N] = "checked";

$checked[mobile_member_use][$cfg[mobile_member_use]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="mobile_member_use" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("모바일 회원연동설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("모바일 회원연동")?><br><?=_("사용여부")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="mobile_member_use" value="N" <?=$checked[mobile_member_use][N]?>> <?=_("사용안함")?>
      	 		<input type="radio" class="radio-inline" name="mobile_member_use" value="Y" <?=$checked[mobile_member_use][Y]?>> <?=_("사용")?>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("모바일 스킨에서 회원연동과 관련된 기능을 사용할지 여부를 설정합니다.")?></div>
      	 		<div>
      	 			<span class="warning">[<?=_("주의")?>]</span> <?=_("서비스 도중에 설정을 변경할 경우 아래와 같은 문제가 발생하오니 자제해주시기 바랍니다.")?><br>
      	 			<div class="textIndent">1) <?=_("사용에서 사용안함으로 변경할 경우 이미 로그인된 회원은 로그아웃을 할 수 없습니다.")?></div>
      	 			<div class="textIndent">2) <?=_("사용에서 사용안함으로 변경할 경우 기존에 사용하던 회원연동 기능을 사용할 수 없습니다. (ex:적립금,쿠폰 등...)")?></div>
      	 		</div>
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