<?
//인터프로 상품 리스트.

$goods = new Goods();
$goods->getList();

$category = $goods->category;
//debug($category);
//debug($goods->listPage);
//debug($goods->listData);
//debug(count($goods->listData));
if(count($goods->listData) > 4) $css_other = "cont_other";
//debug($css_other);

$selected[page_num][$_GET[page_num]] = "selected";
//debug($goods->listPage);

$tpl->define('tpl', 'goods/list_interpro.htm');
$tpl->assign("catnm",$category[catnm]);
$tpl->assign("pg",$goods->listPage);
$tpl->assign("loop",$goods->listData);
$tpl->print_('tpl');
?>