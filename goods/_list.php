<?
//일반,포토북/앨범 상품 리스트.

$goods = new Goods();
$goods->getList();

$category = $goods->category;
//debug($category);
//debug($goods->listPage);
//debug($goods->listData);

//Hot 상품 조회.
$hotitem = $goods->getHotItem($_GET[catno]);
//debug($hotitem);

$selected[page_num][$_GET[page_num]] = "selected";
//debug($goods->listPage);


if ($cfg[skin_theme] == "P1")
{
	//포토북, 인화, 앨범은 1차 레벨 텍스트
	$rootCateNo = substr($_GET[catno], 0, 3);
	if (in_array($rootCateNo, array('002','006','014', '023')))
	{
		$rootct = $mg->getCategoryInfo($cid, $rootCateNo);
		$rootctName = $rootct[catnm];
	} else 
	{
		//3단계의 경우 2단계 텍스트 출력
		if (strlen($_GET[catno]) > 6)
		{
			$rootct = $mg->getCategoryInfo($cid, substr($_GET[catno], 0, 6));
			$rootctName = $rootct[catnm];
		}
		else 
			$rootctName = $category[catnm];
	} 
	
	$tpl->assign("rootCateName",$rootctName);
}
//debug($category[catno]);
$tpl->define('tpl', 'goods/list.htm');
$tpl->assign("catnm",$category[catnm]);
$tpl->assign("catno_I1",$category[catno]);
$tpl->assign("pg",$goods->listPage);
$tpl->assign("loop",$goods->listData);
//debug($all_sub_catetory);
$tpl->assign("sub_cate",$all_sub_catetory);
$tpl->print_('tpl');
?>