<?

include "../_header.php";
include "../_left_menu.php";

if ($cfg[sns_login]) $cfg[sns_login] = unserialize($cfg[sns_login]);

if (!$cfg[sns_login][sns_login_use]) $cfg[sns_login][sns_login_use] = "N";
if (!$cfg[sns_login][naver_login_use]) $cfg[sns_login][naver_login_use] = "N";
if (!$cfg[sns_login][kakao_login_use]) $cfg[sns_login][kakao_login_use] = "N";
if (!$cfg[sns_login][facebook_login_use]) $cfg[sns_login][facebook_login_use] = "N";
// 모던 학술 센터의 경우 kidsnote 회원 연동 설정 기능 추가 jtkim 210823
if (!$cfg[sns_login][kidsnote_login_use]) $cfg[sns_login][kidsnote_login_use] = "N";

$checked[sns_login_use][$cfg[sns_login][sns_login_use]] = "checked";
$checked[sns_naver_login_use][$cfg[sns_login][naver_login_use]] = "checked";
$checked[sns_kakao_login_use][$cfg[sns_login][kakao_login_use]] = "checked";
$checked[sns_facebook_login_use][$cfg[sns_login][facebook_login_use]] = "checked";
$checked[sns_kidsnote_login_use][$cfg[sns_login][kidsnote_login_use]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="sns_login" />

   <div class="panel panel-inverse">
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("SNS로그인 연동설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("SNS로그인 사용여부")?></label>
      	 	<div class="col-md-10">
      	 		<input type="radio" class="radio-inline" name="sns_login_use" value="N" <?=$checked[sns_login_use][N]?>> <?=_("사용안함")?>
      	 		<input type="radio" class="radio-inline" name="sns_login_use" value="Y" <?=$checked[sns_login_use][Y]?>> <?=_("사용")?>
      	 	</div>
      	 </div>

      	 <div class="notView" id="sns_login_use_div">
	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("네이버계정로그인")?><br><?=_("사용여부")?></label>
	      	 	<div class="col-md-10">
	      	 		<input type="radio" class="radio-inline" name="naver_login_use" value="N" <?=$checked[naver_login_use][N]?> onclick="divHide(this);"> <?=_("사용안함")?>
      	 			<input type="radio" class="radio-inline" name="naver_login_use" value="Y" <?=$checked[naver_login_use][Y]?> onclick="divShow(this);"> <?=_("사용")?>
	      	 	</div>
	      	 </div>

	      	 <div class="form-group notView" id="naver_login_use_div">
	      	 	<label class="col-md-2 control-label"><?=_("네이버계정로그인 설정")?></label>
	      	 	<div class="col-md-10 form-inline">
	      	 		Client ID : <input type="text" id="naver_client_id" class="form-control" name="naver_client_id" value="<?=$cfg[sns_login][naver_client_id]?>" size="40"><p><p>
	      	 		Client Secret : <input type="text" id="naver_client_secret" class="form-control" name="naver_client_secret" value="<?=$cfg[sns_login][naver_client_secret]?>" size="40"><p>
	      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("네이버계정로그인 연동설정시 네이버 개발자센터")?>(<a href="https://developers.naver.com/main" target="_blank"><b>https://developers.naver.com/main</b></a>)<?=_("를 통해 발급받은 Client ID / Client Secret를 등록해야 정상적으로 연동이 됩니다.")?></div>
	      	 		<div>
	      	 			<span class="notice">[<?=_("설명")?>]</span> <?=_("네이버 개발자센터에서 서비스 URL과 Callback URL을 확인해주세요.")?><br>
	      	 			<div class="textIndent"><?=_("서비스 URL")?> : http://<?=$_SERVER['SERVER_NAME']?></div>
	      	 			<div class="textIndent">Callback URL : http://<?=$_SERVER['SERVER_NAME']?>/_oauth/callback_naver.php</div>
	      	 		</div>
	      	 	</div>
	      	 </div>

	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("카카오계정로그인")?><br><?=_("사용여부")?></label>
	      	 	<div class="col-md-10">
	      	 		<input type="radio" class="radio-inline" name="kakao_login_use" value="N" <?=$checked[kakao_login_use][N]?> onclick="divHide(this);"> <?=_("사용안함")?>
      	 			<input type="radio" class="radio-inline" name="kakao_login_use" value="Y" <?=$checked[kakao_login_use][Y]?> onclick="divShow(this);"> <?=_("사용")?>
	      	 	</div>
	      	 </div>

	      	 <div class="form-group notView" id="kakao_login_use_div">
	      	 	<label class="col-md-2 control-label"><?=_("카카오계정로그인 설정")?></label>
	      	 	<div class="col-md-10 form-inline">
	      	 		<?=_("Rest API 키")?> : <input type="text" id="kakao_rest_api_key" class="form-control" name="kakao_rest_api_key" value="<?=$cfg[sns_login][kakao_rest_api_key]?>" size="40"><p><p>
	      	 		<?=_("JavaScript 키")?> : <input type="text" id="kakao_javascript_key" class="form-control" name="kakao_javascript_key" value="<?=$cfg[sns_login][kakao_javascript_key]?>" size="40"><p>
	      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("카카오계정로그인 연동설정시 카카오 개발자센터")?>(<a href="https://developers.kakao.com" target="_blank"><b>https://developers.kakao.com</b></a>)<?=_("를 통해 발급받은 Rest API 키 / JavaScript 키를 등록해야 정상적으로 연동이 됩니다.")?></div>
	      	 	</div>
	      	 </div>

	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("페이스북계정로그인")?><br><?=_("사용여부")?></label>
	      	 	<div class="col-md-10">
	      	 		<input type="radio" class="radio-inline" name="facebook_login_use" value="N" <?=$checked[facebook_login_use][N]?> onclick="divHide(this);"> <?=_("사용안함")?>
      	 			<input type="radio" class="radio-inline" name="facebook_login_use" value="Y" <?=$checked[facebook_login_use][Y]?> onclick="divShow(this);"> <?=_("사용")?>
	      	 	</div>
	      	 </div>

	      	 <div class="form-group notView" id="facebook_login_use_div">
	      	 	<label class="col-md-2 control-label"><?=_("페이스북계정로그인 설정")?></label>
	      	 	<div class="col-md-10 form-inline">
	      	 		APP ID : <input type="text" id="facebook_app_id" class="form-control" name="facebook_app_id" value="<?=$cfg[sns_login][facebook_app_id]?>" size="40"><p>
	      	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("페이스북계정로그인 연동설정시 페이스북 개발자센터")?>(<a href="http://developers.facebook.com/apps" target="_blank"><b>http://developers.facebook.com/apps</b></a>)<?=_("를 통해 발급받은 APP ID를 등록해야 정상적으로 연동이 됩니다.")?></div>
	      	 	</div>
	      	 </div>

           <? if($cfg_center[cid] == 'mksipod') { ?>
           <div class="form-group">
             <label class="col-md-2 control-label"><?=_("키즈노트 로그인 설정")?><br><?=_("사용여부")?></label>
             <div class="col-md-10">
               <input type="radio" class="radio-inline" name="kidsnote_login_use" value="N" <?=$checked[kidsnote_login_use][N]?> onclick="divHide(this);"> <?=_("사용안함")?>
               <input type="radio" class="radio-inline" name="kidsnote_login_use" value="Y" <?=$checked[kidsnote_login_use][Y]?> onclick="divShow(this);"> <?=_("사용")?>
             </div>
           </div>

           <div class="form-group notView" id="kidsnote_login_use_div">
             <label class="col-md-2 control-label"><?=_("키즈노트계정 로그인 설정")?></label>
             <div class="col-md-10 form-inline">
               OAuth2 URL : <input type="text" id="kidsnote_client_url" class="form-control" name="kidsnote_client_url" value="<?=$cfg[sns_login][kidsnote_client_url]?>" size="40"><p><p>
               Client ID : <input type="text" id="kidsnote_client_id" class="form-control" name="kidsnote_client_id" value="<?=$cfg[sns_login][kidsnote_client_id]?>" size="40"><p><p>
               Client Secret: <input type="text" id="kidsnote_client_secret" class="form-control" name="kidsnote_client_secret" value="<?=$cfg[sns_login][kidsnote_client_secret]?>" size="40"><p><p>
               <div><span class="notice">[<?=_("설명")?>]</span> <?=_("키즈노트계정 연동설정 OAuth2 URL")?><br>
		             <?=_("KidsNote OAuth2 개발 서버 URL : ")?> <b>https://openapi-partner-dev.kidsnote.com</b><br>
		             <?=_("KidsNote OAuth2 실 서버 URL : ")?> <b>https://openapi.kidsnote.com</b></div><br>
               <div>
                 <span class="notice">[<?=_("설명")?>]</span> <?=_("사전에 계약된 KidsNote 서비스 URL과 Callback URL은 다음과 같습니다.")?><br>
                 <div class="textIndent"><?=_("서비스 URL")?> : http://<?=$_SERVER['SERVER_NAME']?></div>
                 <div class="textIndent">Callback URL : http://<?=$_SERVER['SERVER_NAME']?>/_oauth/callback_kidsnote.php</div>
               </div>
             </div>
           </div>
            <? } ?>

	      	 <div class="form-group"></div>
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
	$j("[name=sns_login_use][value=<?=$cfg[sns_login][sns_login_use]?>]").trigger("click");
});

$j("[name=sns_login_use]").click(function() {
	if ($j(this).val() == "Y") {
		$j("#sns_login_use_div").slideDown();
		$j('input','#sns_login_use_div').attr('disabled', false);

		$j("[name=naver_login_use][value=<?=$cfg[sns_login][naver_login_use]?>]").trigger("click");
		$j("[name=kakao_login_use][value=<?=$cfg[sns_login][kakao_login_use]?>]").trigger("click");
		$j("[name=facebook_login_use][value=<?=$cfg[sns_login][facebook_login_use]?>]").trigger("click");
    $j("[name=kidsnote_login_use][value=<?=$cfg[sns_login][kidsnote_login_use]?>]").trigger("click");
	} else {
		$j("#sns_login_use_div").slideUp();
		$j('input','#sns_login_use_div').attr('disabled', true);
	}
});

function divShow(obj) {
	$j("#" + obj.name + "_div").slideDown();
	$j('input','#' + obj.name + '_div').attr('disabled', false);
}

function divHide(obj) {
	$j("#" + obj.name + "_div").slideUp();
	$j('input','#' + obj.name + '_div').attr('disabled', true);
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>
