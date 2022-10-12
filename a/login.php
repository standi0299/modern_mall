<?
//include dirname(__FILE__) . "/../lib/lib_const.php";
//include dirname(__FILE__) . "/../lib/lib.common.php";
include_once dirname(__FILE__) . "/../lib/library.php";
include_once dirname(__FILE__) ."/_inc_service_config.php";
if ($ici_admin) {
    go("main/");
}

$file_rand = mt_rand(1, 6);

?>
<!DOCTYPE html>
<? if ($language_locale == "zh_CN") { ?>
<html lang="zh">
<?	} else {	?>
<html lang="ko">
<?	} ?>	
   <head>
      <meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
		<meta content="printhome" name="description" />
		<meta content="ilark communication" name="author" />
		<title><?=_("블루팟 A Admin")?> | Login Page</title>

		<!-- ================== BEGIN BASE CSS STYLE ================== -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
		<link href="assets/plugins/jquery-ui-1.10.4/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
		<link href="assets/plugins/bootstrap-3.2.0/css/bootstrap.min.css" rel="stylesheet" />
		<link href="assets/plugins/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" />

		<link href="assets/css/animate.min.css" rel="stylesheet" />
		<link href="assets/css/color-admin-v1.2-style.min.css" rel="stylesheet" />
		<link href="assets/css/style-responsive.min.css" rel="stylesheet" />
		<link href="assets/css/theme/default.css" rel="stylesheet" id="theme" />
		<!-- ================== END BASE CSS STYLE ================== -->

		<script type="text/javascript" src="assets/plugins/jquery-1.10.2.min.js"></script>
		<script>
			var $j = jQuery.noConflict();
		</script>
		<script src="/js/common.php"></script>
		<script src="/js/common.defer.php"></script>
		<script src="/js/form_chk.php"></script>
		<script src="/js/zmSpamFreeAjax.php"></script>
	</head>

	<body>
      <!-- begin #page-loader -->
		<div id="page-loader" class="fade in">
         <span class="spinner"></span>
		</div>
		<!-- end #page-loader -->

		<div class="login-cover">
			<div class="login-cover-image"><img src="assets/img/login-bg/bg-<?=$file_rand?>.jpg" data-id="login-cover-image" alt="" />
			</div>
			<div class="login-cover-bg"></div>
		</div>

		<!-- begin #page-container -->
		<div id="page-container" class="fade">
			<!-- begin login -->
			<div class="login login-v2" data-pageload-addclass="animated flipInX">
				<!-- begin brand -->
				<div class="login-header">
					<div class="brand">
						<i class="fa <?=$admin_config[main_icon]?>"> <?=$admin_config[main_title]?></i>
						<small><?=_("관리자 메뉴 로그인 화면")?></small>
					</div>
					<div class="icon">
						<i class="fa fa-sign-in"></i>
					</div>
				</div>
				<!-- end brand -->
				<div class="login-content">
					<form method="post" action="../module/indb.php" class="margin-bottom-0" onsubmit="return form_chk(this)">
						<input type="hidden" name="mode" value="login">
						<input type="hidden" name="skin_type" value="kids">
						<div class="form-group m-b-20">
							<?=_("아이디")?>
							<input type="text" class="form-control input-lg" name="mid" placeholder='<?=_("아이디")?>' required msg='<?=_("아이디를 입력하세요")?>' />
						</div>
						<div class="form-group m-b-20">
							<?=_("비밀번호")?>
							<input type="password" class="form-control input-lg" name="password" placeholder='<?=_("비밀번호")?>' required msg='<?=_("비밀번호를 입력하세요")?>' />
						</div>

                  <? if ($_COOKIE[admin_login_fail_cnt] > ADMIN_LOGIN_FAIL_COUNT) { ?>
						<div class="form-group m-b-20"><?=_("인증보안코드")?>
						   <div class="row">
  						       <div class="col-md-4">
  						          <img id="zsfImg" src="/lib/zmSpamFree/zmSpamFree.php?zsfimg&re&cfg=zsfCfgAdmin" style="border: none; cursor: pointer" onclick="this.src='/lib/zmSpamFree/zmSpamFree.php?cfg=zsfCfgAdmin&re&amp;zsfimg='+new Date().getTime()" />
  						       </div>
  						       <div class="col-md-4">
                           <input type="text" class="form-control input-sm" name="zsfCode" id="zsfCode" onchange="chkZsf(this);" required placeholder='<?=_("보안코드")?>' required msg='<?=_("보안코드를 입력하세요")?>' />
                        </div>
                        <div class="col-md-4" id="rslt"><?=_("이미지를 누르시면 새로고침됩니다.")?></div>
                     </div>
                  </div>
                  <? } ?>

						<div class="form-group m-b-20">
							<!--<label class="checkbox m-b-20">-->
							<input type="checkbox" name="rememberid" />
							<?=_("아이디 기억하기")?> <!--</label>-->
						</div>
						<div class="login-buttons">
							<button type="submit" class="btn btn-success btn-block btn-lg">
								<?=_("로그인")?>
							</button>
						</div>
					</form>
				</div>
			</div>
			<!-- end login -->
		</div>
		<!-- end page container -->

		<!-- ================== BEGIN BASE JS ================== -->
		<script src="assets/plugins/jquery-1.8.2/jquery-1.8.2.min.js"></script>
		<script src="assets/plugins/jquery-ui-1.10.4/ui/minified/jquery-ui.min.js"></script>
		<script src="assets/plugins/bootstrap-3.2.0/js/bootstrap.min.js"></script>
		<!--[if lt IE 9]>
		<script src="assets/crossbrowserjs/html5shiv.js"></script>
		<script src="assets/crossbrowserjs/respond.min.js"></script>
		<script src="assets/crossbrowserjs/excanvas.min.js"></script>
		<![endif]-->
		<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

		<!-- ================== END BASE JS ================== -->

		<!-- ================== BEGIN PAGE LEVEL JS ================== -->
		<script src="assets/js/apps.1.2.min.js"></script>
		<!-- ================== END PAGE LEVEL JS ================== -->

		<script>
			$(document).ready(function() {
				App.init();

			});
		</script>
	</body>
</html>