<?

include "../_header.php";
include "../_left_menu.php";

if (!$cfg[member_system][quiescence_account]) $checked[quiescence_account][N] = "checked";
if (!$cfg[member_system]['system']) $checked['system'][open] = "checked";
if (!$cfg[member_system][order_system]) $checked[order_system][open] = "checked";

$checked[quiescence_account][$cfg[member_system][quiescence_account]] = "checked";
$checked['system'][$cfg[member_system]['system']] = "checked";
$checked[order_system][$cfg[member_system][order_system]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="member_system" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("회원운영관리")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("휴면계정설정")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="quiescence_account" value="N" <?=$checked[quiescence_account][N]?>> 사용안함
      	 		<input type="radio" class="radio-inline" name="quiescence_account" value="Y" <?=$checked[quiescence_account][Y]?>> 사용
      	 		<div><span class="notice">[설명]</span> 최근 접속일이 1년이 넘은 회원은 휴면계정으로 전환됩니다.</div>
      	 		<div><span class="notice">[설명]</span> 휴면계정으로 전환된 회원의 개인정보 데이터는 기존 데이터들과 분리, 이관되어 보관됩니다.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("사이트접근권한")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="system" value="open" <?=$checked['system'][open]?>> 일반
      	 		<input type="radio" class="radio-inline" name="system" value="close" <?=$checked['system'][close]?>> 회원제<p>
      	 		<div id="redirect_url" class="form-inline notView">강제이동 페이지 <input type="text" class="form-control" name="redirect_url" value="<?=$cfg[member_system][redirect_url]?>" size="40"></div>
      	 		<div><span class="notice">[설명]</span> 로그인 및 회원가입, 아이디 및 패스워드 찾기 페이지를 제외한 모든 곳은 접근이 불가하며, 로그인 페이지로 강제 리다이렉팅이 됩니다.</div>
      	 		<div><span class="notice">[설명]</span> 회원제 - 강제이동 페이지 설정시 로그인페이지가 아닌 설정된 페이지로 이동됩니다.</div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("사이트주문제한")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="order_system" value="open" <?=$checked[order_system][open]?>> 비회원 주문가능
      	 		<input type="radio" class="radio-inline" name="order_system" value="close" <?=$checked[order_system][close]?>> 회원만 주문가능
      	 		<input type="radio" class="radio-inline" name="order_system" value="edit_close" <?=$checked[order_system][edit_close]?>> 회원만 주문가능 (편집기도 구동 제한)
      	 		<div><span class="notice">[설명]</span> 사이트접근권한이 "회원제"인경우 주문제한 설정은 "회원만 주문가능" 설정으로 변경됩니다.</div>
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

<script type="text/javascript">
$j(function() {
	$j("[name=system][value=<?=$cfg[member_system]['system']?>]").trigger("click");
});

$j("[name=system]").click(function() {
	if ($j(this).val() == "close") {
		$j('#redirect_url').show();
	} else {
		$j('#redirect_url').hide();
	}
});
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>