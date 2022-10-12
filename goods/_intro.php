<?
//미오디오 인트로 페이지

$goods = new Goods();
$goods->getIntro();

$category = $goods->category;

//600x600 배너 사용여부.
$bCatIntro600 = false;
//debug($cfg[skin]);
$m_config = new M_config();
//debug($_GET[catno]);
for ($i=1; $i<=2; $i++) {
	//category_intro_600x600_1_002
	$banner = $m_config->getBannerInfo($cid, "category_intro_600x600_". $i ."_". $_GET[catno], $cfg[skin]);
	//debug($banner);
	if($banner) {
		if($banner[img]) $bCatIntro600 = true;
	}
}
//debug($bCatIntro600);

$tpl->define('tpl', 'goods/intro.htm');
$tpl->assign("catnm",$category[catnm]);
$tpl->print_('tpl');
?>