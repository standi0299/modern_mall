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


$selected[page_num][$_GET[page_num]] = "selected";
//debug($goods->listPage);

$cnt = count($goods->listData);

$tpl->define('tpl', 'goods/list_sub_category_no.htm');
$tpl->assign("catnm",$category[catnm]);
$tpl->assign("pg",$goods->listPage);
$tpl->assign("loop",$goods->listData);
$tpl->assign("cnt",$cnt);

$tpl->assign("sub_cate",$all_sub_catetory);
$tpl->print_('tpl');
?>