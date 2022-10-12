<?
include dirname(__FILE__) . "/lib.php";

//$cfg_center[service_kind] = "printgroup"; //테스트용!!!
include_once dirname(__FILE__) ."/_inc_service_config.php";
?>
<!DOCTYPE html>
<html lang="<?=$languages?>">	
<head>
  <meta charset="utf-8" />    
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
  <meta content="BluePod" name="description" />
  <meta content="ilark communication" name="author" />
  <title><?=$admin_config[main_title]?> | <?=$cid ?> </title>
  
  <script src="/js/popupLayer.js"></script>
  <script src="/js/common.php"></script>
  <script src="/js/common.defer.php" defer=defer></script>
  <script src="/js/form_chk.php" defer=defer></script>
  
  <!-- ================== BEGIN BASE CSS STYLE ================== -->
  
  <link href="../assets/plugins/jquery-ui-1.10.4/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
  <link href="../assets/plugins/bootstrap-3.1.1/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../assets/plugins/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" />
  <link href="../assets/css/animate.min.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
  <link href="../assets/css/style-responsive.min.css" rel="stylesheet" />
  <link href="../assets/css/admin.css" rel="stylesheet" />  
  <!-- ================== END BASE CSS STYLE ================== -->  	
	<?=$admin_config[css_theme]?>
	
  <script src="/js/jquery.js"></script>
  <script>var $j = jQuery.noConflict();</script>
  
  <!-- wpod 실행시 ie 체크 스크립트-->
  <script type="text/javascript" src="/js/extra_option/goods.extra.option.call.js"></script>
  
  <!-- printgroup 관리자 매뉴얼 다운로드 스크립트-->
  <script>
  	function mDown() {
  		window.open('http://files.ilark.co.kr/down/printgroup_admin_manual.pdf');
  		return;
  	} 
  </script>  
</head>

<body>
  <!-- begin #page-loader -->
  <div id="page-loader" class="fade in"><span class="spinner"></span></div>
  <!-- end #page-loader -->
  
  <!-- begin #page-container -->
  <div id="page-container" class="fade">
    <!-- begin #header -->
    
<? if ($include_header) {	?>    
    <div id="header" class="header navbar <?=$admin_config[navbar_header_class]?> navbar-fixed-top">
      <!-- begin container-fluid -->
      <div class="container-fluid">
        <!-- begin mobile sidebar expand / collapse button -->
        <div class="navbar-header">
          <a href="../main/<?=$admin_config[index_file]?>" class="navbar-brand"><span class="navbar-logo"></span><?=$admin_config[main_title]?></a>
          <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <!-- end mobile sidebar expand / collapse button -->
        
        <!-- begin header navigation right -->
        <ul class="nav navbar-nav navbar-right">
          <li>
            <form class="navbar-form full-width" action="/mng/order/upload_list.php" method="POST">
              <div class="form-group">
                <!--<input type="text" class="form-control" placeholder="주문자 이름 또는 업체명" name="s_search" />-->
                <!--<button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>-->
              </div>
            </form>
          </li>
          
          
          <li class="dropdown navbar-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"><?if ($super_admin == 1){?><?=$cid ?><?} else {?><?=$sess_admin[mid]?><?}?></span> <b class="caret"></b>
            </a>
            <ul class="dropdown-menu animated fadeInLeft">
              <li class="arrow"></li>
              <li><a href="/" target="_blank"><?=_("홈페이지 바로가기")?></a></li>
              <li class="divider"></li>
              <li><a href="../module/logout.php?admin=1&rurl=/k/login.php">Log Out</a></li>
            </ul>
          </li>          
          
        </ul>
        <!-- end header navigation right -->
      </div>
      <!-- end container-fluid -->
    </div>
<?	}	?>    

    <!-- end #header -->