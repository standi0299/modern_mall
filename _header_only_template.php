<?
ob_start();

/* 장바구니 키 부여 */
if (!$_COOKIE[cartkey]){
	setCookie('cartkey',$_SERVER[REMOTE_ADDR]."_".time(),0,'/');
	$_COOKIE[cartkey] = $_SERVER[REMOTE_ADDR]."_".time();
}
if ($_GET[tophidden]){
	setCookie("tophidden","1",0,'/');
	$_COOKIE[tophidden] = $_SERVER[REMOTE_ADDR]."_".time();
}

include dirname(__FILE__)."/lib/template_/Template_.class.php";

//$tpl_file = substr(str_replace(dirname(__FILE__),'',$_SERVER[SCRIPT_FILENAME]),1,-3)."htm";
$tpl_file = substr(str_replace(dirname(__FILE__),'',$_SERVER[SCRIPT_NAME]),1,-3)."htm";

$cfg[skin] = "modern";

$tpl = new Template_;
$tpl->template_dir	= dirname(__FILE__)."/skin";
$tpl->skin = $cfg[skin];
$tpl->compile_dir	= dirname(__FILE__)."/_compile";
$tpl->prefilter		= "adjustPath";

### 레이아웃정보
$f = str_replace("/","_",$tpl_file);
$f = str_replace(".htm",".php",$f);
if (is_file("../skin/{$cfg[skin]}/_conf/$f")){
	include "../skin/{$cfg[skin]}/_conf/$f";
}

$r_layout = array("top","left","right","bottom");
foreach ($r_layout as $v){
	if ($cfg[bd][$v]) $cfg[layout][$v] = $cfg[bd][$v];
	if (!$cfg[layout][$v]) $cfg[layout][$v] = "default";
	if ($cfg[layout][$v]!="hidden") $tpl->define($v,'layout/'.$v.'.'.$cfg[layout][$v].'.htm');
}


$tpl->assign('tpl',$tpl);
$tpl->define(array(
	'tpl'			=> $tpl_file,
	'header'		=> 'layout/layout.htm?header',
	'footer'		=> 'layout/layout.htm?footer',
	'header_popup'	=> 'layout/header.popup.htm',
	'footer_popup'	=> 'layout/footer.popup.htm',
	'category'		=> 'module/category.htm',
	'banner_left'	=> 'layout/banner_left.htm',
	'banner_right'	=> 'layout/banner_right.htm',
	));
?>