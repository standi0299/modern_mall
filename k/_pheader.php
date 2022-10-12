<?
include dirname(__FILE__) . "/lib.php";

if ($login_check_flag != 'N')
{
	if (!$ici_admin) {
	    msg(_("관리자 로그인후 이용하세요!"), "close");
	}
}
?>
<!DOCTYPE html>
<html lang="<?=$languages?>">
<head>
  	<meta charset="utf-8" />  
  	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
  	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
  	<meta content="BluePod" name="description" />
  	<meta content="ilark communication" name="author" />
  	<title><?=_("BluePod K 관리자")?> | <?=$cid ?> </title>
  
  	<script src="/js/popupLayer.js"></script>
  	<script src="/js/common.php"></script>
  	<script src="/js/common.defer.php" defer=defer></script>
  	<script src="/js/chkForm.php" defer=defer></script>
  	<script src="/js/form_chk.php" defer=defer></script>
  
  	<!-- ================== BEGIN BASE CSS STYLE ================== -->
  
  	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  	<link href="../assets/plugins/jquery-ui-1.10.4/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
  	<link href="../assets/plugins/bootstrap-3.1.1/css/bootstrap.min.css" rel="stylesheet" />
  	<link href="../assets/plugins/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" />
  	<link href="../assets/css/animate.min.css" rel="stylesheet" />
  	<link href="../assets/css/style.min.css" rel="stylesheet" />
  	<link href="../assets/css/style-responsive.min.css" rel="stylesheet" />  
  	<!-- ================== END BASE CSS STYLE ================== -->
  
  	<link href="../assets/css/admin.css" rel="stylesheet" />
 
  	<script src="/js/jquery.js"></script>
  
	<script src="/js/ui.widget.js"></script>
	<script src="/js/ui.core.js"></script>
	<script src="/js/ui.tabs.js"></script>
	<script src="/js/ui.mouse.js"></script>
	<script src="/js/ui.sortable.js"></script>  
  
  	<script>var $j = jQuery.noConflict();</script>

  	<!-- wpod 실행시 ie 체크 스크립트-->
  	<script type="text/javascript" src="/js/extra_option/goods.extra.option.call.js"></script>  
</head>

<body>