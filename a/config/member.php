<?

include "../_header.php";
include "../_left_menu.php";

if (!$cfg[member_system][quiescence_account]) $checked[quiescence_account][N] = "checked";
if (!$cfg[member_system]['system']) $checked['system'][open] = "checked";
if (!$cfg[member_system][order_system]) $checked[order_system][open] = "checked";
if (!$cfg[member_system][order_data_delete]) $checked[order_data_delete][N] = "checked";
if (!$cfg[member_system][login_system]) $checked[login_system][N] = "checked";

$checked[quiescence_account][$cfg[member_system][quiescence_account]] = "checked";
$checked['system'][$cfg[member_system]['system']] = "checked";
$checked[order_system][$cfg[member_system][order_system]] = "checked";
$checked[order_data_delete][$cfg[member_system][order_data_delete]] = "checked";
$checked[login_system][$cfg[member_system][login_system]] = "checked";

$order_data_delete_year = $cfg[member_system][order_data_delete_year];


//외부 회원 연동 처리 설정정보			20180822		chunter
$out_login_param_login_msg = base64_decode($cfg[member_system][out_login_param_login_msg]);
$out_login_param_login_url = base64_decode($cfg[member_system][out_login_param_login_url]);
$out_login_param_logout_msg = base64_decode($cfg[member_system][out_login_param_logout_msg]);
$out_login_param_logout_url = base64_decode($cfg[member_system][out_login_param_logout_url]);
$out_login_param_regist_msg = base64_decode($cfg[member_system][out_login_param_regist_msg]);
$out_login_param_regist_url = base64_decode($cfg[member_system][out_login_param_regist_url]);


if (!$cfg[mobile_member_use]) $checked[mobile_member_use][N] = "checked";
$checked[mobile_member_use][$cfg[mobile_member_use]] = "checked";
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
      	 		<input type="radio" class="radio-inline" name="quiescence_account" value="N" <?=$checked[quiescence_account][N]?>> <?=_("사용안함")?>
      	 		<input type="radio" class="radio-inline" name="quiescence_account" value="Y" <?=$checked[quiescence_account][Y]?>> <?=_("사용")?>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("최근 접속일이 1년이 넘은 회원은 휴면계정으로 전환됩니다.")?></div>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("휴면계정으로 전환된 회원의 개인정보 데이터는 기존 데이터들과 분리, 이관되어 보관됩니다.")?></div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("사이트접근권한")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="system" value="open" <?=$checked['system'][open]?>> <?=_("일반")?>
      	 		<input type="radio" class="radio-inline" name="system" value="close" <?=$checked['system'][close]?>> <?=_("회원제")?><p>
      	 		<div id="redirect_url" class="form-inline notView"><?=_("강제이동 페이지")?> <input type="text" class="form-control" name="redirect_url" value="<?=$cfg[member_system][redirect_url]?>" size="40"></div>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("로그인 및 회원가입, 아이디 및 패스워드 찾기 페이지를 제외한 모든 곳은 접근이 불가하며, 로그인 페이지로 강제 리다이렉팅이 됩니다.")?></div>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("회원제 - 강제이동 페이지 설정시 로그인페이지가 아닌 설정된 페이지로 이동됩니다.")?></div>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("사이트주문제한")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="order_system" value="open" <?=$checked[order_system][open]?>> <?=_("비회원 주문가능")?>
      	 		<input type="radio" class="radio-inline" name="order_system" value="close" <?=$checked[order_system][close]?>> <?=_("회원만 주문가능")?>
      	 		<input type="radio" class="radio-inline" name="order_system" value="edit_close" <?=$checked[order_system][edit_close]?>> <?=_("회원만 주문가능 (편집기도 구동 제한)")?>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("사이트접근권한이 '회원제'인경우 주문제한 설정은 '회원만 주문가능' 설정으로 변경됩니다.")?></div>
      	 	</div>
      	 </div>
      	 
      	 
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("주문 배송정보삭제")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="order_data_delete" value="N" <?=$checked[order_data_delete][N]?>> <?=_("삭제하지 않음")?>
      	 		
      	 		<input type="radio" class="radio-inline" name="order_data_delete" value="Y" <?=$checked[order_data_delete][Y]?>> <?=_("삭제처리 함")?>
      	 		<input type="text" class="radio-inline" name="order_data_delete_year" size="1" value="<?=$order_data_delete_year?>"> <?=_("년 이후 삭제")?>
      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("개인정보 보호에 따라 주문 배송정보 삭제 처리.")?></div>
      	 	</div>
      	 </div>
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("회원연동모드")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" name="login_system" value="normal" class="radio-inline" checked="checked" <?=$checked['login_system'][N]?>><b>사용하지 않음</b><br>    	 		
      	 		<!--<input type="radio" class="radio-inline" name="login_system" value="out_login_param" <?=$checked[login_system][out_login_param]?>> 외부몰에서의 로그인,로그아웃 처리-->
      	 		<input type="radio" name="login_system" value="out_cookie" <?=$checked['login_system'][out_cookie]?>> <b>외부연동몰에서 로그인,로그아웃</b><br>
      	 		<input type="radio" name="login_system" value="out_login_cookie" <?=$checked['login_system'][out_login_cookie]?>> <b>외부연동몰로의 로그인,로그아웃 요청 (쿠키)</b> 			
      	 	</div>
      	 </div>
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("상세 설정")?></label>
      	 	<!-- 설정사항 없음 -->
      	 	<div class="col-md-10">
	      	 	<div id="div_normal" class="login_system">설정사항 없음</div>
	      	 	
	      	 	<!-- 외부연동몰에서 로그인, 로그아웃-->
	      	 	<div id="div_out_cookie" class="login_system">
					<table class="tb1">
						<tr>
							<th style="text-align:left;padding-left:5px;">로그인url</th>
							<td>
								<input type="text" name="out_cookie[url][login]" value="<?=$cfg[member_system][out_cookie][url][login]?>" class="w300" req="1"/>
								<div class="desc red">외부몰의 로그인 페이지 url 을 입력해주세요. 로그인이 필요한 페이지에 비회원으로 접근시 이동될 페이지 입니다.</div>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">로그아웃url</th>
							<td>
								<input type="text" name="out_cookie[url][logout]" value="<?=$cfg[member_system][out_cookie][url][logout]?>" class="w300" req="1"/>
								<div class="desc red">외부몰의 로그아웃 url 을 입력해주세요. 몰에서 로그아웃 요청시 해당페이지로 이동 시킵니다.</div>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">쿠키명</th>
							<td>
								<input type="text" name="out_cookie[cookie_name]" value="<?=$cfg[member_system][out_cookie][cookie_name]?>" req="1"/>
								<div class="desc red">외부몰에서 생성할 쿠키변수의 이름을 입력해주세요.</div>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">변수명:아이디</th>
							<td>
								<input type="text" name="out_cookie[cookie_val][mid]" value="<?=$cfg[member_system][out_cookie][cookie_val][mid]?>" req="1"/>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">변수명:이름</th>
							<td><input type="text" name="out_cookie[cookie_val][name]" value="<?=$cfg[member_system][out_cookie][cookie_val][name]?>" req="1"/></td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">변수명:기업그룹아이디</th>
							<td><input type="text" name="out_cookie[cookie_val][bid]" value="<?=$cfg[member_system][out_cookie][cookie_val][bid]?>"/></td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">변수명:그룹번호</th>
							<td><input type="text" name="out_cookie[cookie_val][grpno]" value="<?=$cfg[member_system][out_cookie][cookie_val][grpno]?>"/></td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">GET변수명:return_url</th>
							<td><input type="text" name="out_cookie[val][return_url]" value="<?=$cfg[member_system][out_cookie][val][return_url]?>"/></td>
						</tr>
					</table>
					<div class="desc red">* 변수명 : 쿠키로 생성될 배열변수내의 각 키 값을 입력해주세요.</div>
				</div>
				
				<!-- 외부연동몰로의 로그인,로그아웃 요청 (쿠키)< -->
				<div id="div_out_login_cookie" class="login_system">
					<table class="tb1">
						<tr>
							<th style="text-align:left;padding-left:5px;">로그인 요청 URL</th>
							<td>
								<input type="text" name="out_login_cookie[url][login]" value="<?=$cfg[member_system][out_login_cookie][url][login]?>" class="w300" req="1"/>
								<div class="desc red">외부몰의 로그인 승인 url 을 입력해주세요. 내부몰에서 로그인 시도 시에, 아이디 패스워드를 받아 인증을 처리할 URL 입니다.</div>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">로그인POST변수명:아이디</th>
							<td>
								<input type="text" name="out_login_cookie[val][mid]" value="<?=$cfg[member_system][out_login_cookie][val][mid]?>" req="1"/>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">로그인POST변수명:패스워드</th>
							<td>
								<input type="text" name="out_login_cookie[val][password]" value="<?=$cfg[member_system][out_login_cookie][val][password]?>" req="1"/>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">로그인POST변수명:Return URL</th>
							<td>
								<input type="text" name="out_login_cookie[val][return_url]" value="<?=$cfg[member_system][out_login_cookie][val][return_url]?>" req="1"/>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">쿠키명:아이디</th>
							<td>
								<input type="text" name="out_login_cookie[cookie_val][mid]" value="<?=$cfg[member_system][out_login_cookie][cookie_val][mid]?>" req="1"/>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">쿠키명:이름</th>
							<td><input type="text" name="out_login_cookie[cookie_val][name]" value="<?=$cfg[member_system][out_login_cookie][cookie_val][name]?>"/></td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">쿠키명:기업그룹아이디</th>
							<td><input type="text" name="out_login_cookie[cookie_val][bid]" value="<?=$cfg[member_system][out_login_cookie][cookie_val][bid]?>"/></td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">쿠키명:그룹번호</th>
							<td><input type="text" name="out_login_cookie[cookie_val][grpno]" value="<?=$cfg[member_system][out_login_cookie][cookie_val][grpno]?>"/></td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">로그아웃 요청 URL</th>
							<td>
								<input type="text" name="out_login_cookie[url][logout]" value="<?=$cfg[member_system][out_login_cookie][url][logout]?>" class="w300" req="1"/>
								<div class="desc red">외부몰의 로그아웃 승인 url 을 입력해주세요. 내부몰에서 로그아웃 시도 시에, 아이디를 받아 인증을 처리할 URL 입니다.</div>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">로그아웃GET변수명:Return URL</th>
							<td>
								<input type="text" name="out_login_cookie[val][logout_return_url]" value="<?=$cfg[member_system][out_login_cookie][val][logout_return_url]?>" req="1"/>
								<div class="desc red">미설정시, 로그아웃을 하게 되면, 몰의 메인페이지로 이동합니다.</div>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">회원가입 URL</th>
							<td>
								<input type="text" name="out_login_cookie[url][regist]" value="<?=$cfg[member_system][out_login_cookie][url][regist]?>" class="w300" req="1"/>
								<div class="desc red">외부몰의 회원가입 url 을 입력해주세요. 내부몰에서 회원가입 시도 시에, 옮겨갈 페이지 입니다.</div>
							</td>
						</tr>
						<tr>
							<th style="text-align:left;padding-left:5px;">ID/PW찾기 URL</th>
							<td>
								<input type="text" name="out_login_cookie[url][find]" value="<?=$cfg[member_system][out_login_cookie][url][find]?>" class="w300" req="1"/>
								<div class="desc red">외부몰의 아이디/비밀번호찾기 url 을 입력해주세요. 내부몰에서 아이디/비밀번호찾기 시도 시에, 옮겨갈 페이지 입니다.</div>
							</td>
						</tr>
					</table>		
					<div class="desc red">
					* POST변수명 : 요청 URL 에서 POST 방식으로 전달받을 각 변수명을 입력해주세요.</br>
					</div>
				</div>
			</div>		
      	 </div>
      	 	<!--
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="login_system" value="N" <?=$checked[login_system][N]?>> <?=_("사용하지 않음")?>      	 		
      	 		<input type="radio" class="radio-inline" name="login_system" value="out_login_param" <?=$checked[login_system][out_login_param]?>> 외부몰에서의 로그인,로그아웃 처리
      	 	</div>
      	 	<div class="col-md-10">	
      	 		<?=_("로그인 메세지")?> <input type="text" class="form-control" name="out_login_param_login_msg" value="<?=$out_login_param_login_msg?>"> 
      	 		<?=_("로그인 URL")?> <input type="text" class="form-control" name="out_login_param_login_url" value="<?=$out_login_param_login_url?>"> 
      	 	</br>
      	 		<?=_("로그아웃 메세지")?> <input type="text" class="form-control" name="out_login_param_logout_msg" value="<?=$out_login_param_logout_msg?>"> 
      	 		<?=_("로그아웃 URL")?> <input type="text" class="form-control" name="out_login_param_logout_url" value="<?=$out_login_param_logout_url?>"> 
      	 	</br>	
      	 		<?=_("회원가입 메세지")?> <input type="text" class="form-control" name="out_login_param_regist_msg" value="<?=$out_login_param_regist_msg?>"> 
      	 		<?=_("회원가입 URL")?> <input type="text" class="form-control" name="out_login_param_regist_url" value="<?=$out_login_param_regist_url?>"> 
      	 		
      	 	</div>
      	 </div>
       	-->  	 
         <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
      	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
      </div>
   </div>   
   </form>

   
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
<script>
$j(function(){
	$j("input[name=login_system]").click(function(){
		set_login_system_div($j(this).val());
	});
	$j("input[name=login_system][checked]").trigger("click");
});

function set_login_system_div(val){
	$j(".login_system").hide();
	$j("input[req=1]",".login_system").attr("required",false);
	$j("input",".login_system").attr("disabled",true);
	$j("#div_"+val).show();
	$j("input[req=1]","#div_"+val).attr("required",true);
	$j("input","#div_"+val).attr("disabled",false);

	if(val == "out_open_edit"){
   	var oEditors = [];
      smartEditorInit("contents", true, "editor", true);
   }
}
</script>
<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>