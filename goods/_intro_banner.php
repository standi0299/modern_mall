<?
//기본 인트로 페이지

$goods = new Goods();
$goods->getIntro();

$category = $goods->category;

$m_config = new M_config();
//debug($_GET[catno]);

$tpl->define('tpl', 'goods/intro_banner.htm');
$tpl->assign("catnm",$category[catnm]);
$tpl->print_('tpl');
?>