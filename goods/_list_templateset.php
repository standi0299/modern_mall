<?
//템플릿셋 상품 리스트.

$bTempSet = TRUE;
$goods = new Goods();
$goods->getListTemplate($bTempSet);
$editor = $goods->editor;
$category = $goods->category;

//Hot 상품 조회.
$hotitem = $goods->getHotItem($_GET[catno]);
//debug($hotitem);

//debug($goods->listData);
//debug($goods->listPage);
//debug($goods->listTemplateData);
//debug($editor);
//debug($category);

if (!$goods->listData[goodsno]){
    msg(_("해당 상품은 준비 중 입니다.")."\\n"._("이용에 불편을 드려 대단히 죄송합니다."));
    //msg(_("해당 상품은 준비 중 입니다.")."\\n"._("이용에 불편을 드려 대단히 죄송합니다."),-1);
}

$selected[page_num][$_GET[page_num]] = "selected";

$mode = "view"; //편집완료 후 페이지 이동 (view : cart.php, order : cart_n_order.php)
//if($cfg[skin] == "pod_group") $mode = "order";

//상품진열시 상품개수(cells x rows).
if ($category[cells] && $category[rows]) {
	$cfg[cells] = $category[cells];
	$cfg[rows] = $category[rows];
}

//상품진열시 사용자정의 이미지가로사이즈(width) x 이미지세로사이즈(height) 2016.03.08 by kdk
if ($category[listimg_w]){
	//debug($category[listimg_w]);
	$cfg[listimg_w] = $category[listimg_w];
}
if ($category[listimg_h]){
	//debug($category[listimg_h]);
	$cfg[listimg_h] = $category[listimg_h];
}

$tpl->define('tpl', 'goods/list_templateset.htm');
$tpl->assign("mode",$mode);
$tpl->assign("pg",$goods->listPage);
$tpl->assign("data",$goods->listData);
$tpl->assign("loop",$goods->listTemplateData);
$tpl->assign("pageurl",$goods->listPage->page[url]);
$tpl->assign("sub_cate",$all_sub_catetory);
$tpl->print_('tpl');
?>