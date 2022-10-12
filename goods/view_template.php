<?
include "../_header.php";

//템플릿 상품 리스트 (인터프로).
//상품진열시 상품개수(cells x rows).
$cfg[cells] = "3";
$cfg[rows] = "6";

//상품진열시 사용자정의 이미지가로사이즈(width) x 이미지세로사이즈(height) 2016.03.08 by kdk
$cfg[listimg_w] = "264";
$cfg[listimg_h] = "192";

$_GET = $_REQUEST;

//좌측 카테고리, 검색창으로 템플릿 검색 하여 페이지 이동인 경우. option_json 데이타 처리.
if ($_POST[optionjson] && $_POST[optionjson] != "") {
    $_GET[searchjson] = rawurlencode(base64_encode($_POST[optionjson]));
}
else {
    if ($_GET[searchjson] && $_GET[searchjson] != "") {
        $_POST[optionjson] = base64_decode(rawurldecode($_GET[searchjson]));
    }
}

$goods = new Goods();
$goods->getListTemplate();
$editor = $goods->editor;
$category = $goods->category;
$ret = $goods->listTemplateDataRet;
//debug($goods->listData);
//debug($goods->listPage);
//debug($goods->listTemplateData);
//debug($goods->listTemplateDataRet);
//debug($editor);
//debug($category);
//debug($cfg[layout][top]);

if (!$goods->listData[goodsno]){
    msg(_("해당 상품은 준비 중 입니다.")."\\n"._("이용에 불편을 드려 대단히 죄송합니다."));
    //msg(_("해당 상품은 준비 중 입니다.")."\\n"._("이용에 불편을 드려 대단히 죄송합니다."),-1);
}

$selected[page_num][$_GET[page_num]] = "selected";

$mode = "view"; //편집완료 후 페이지 이동 (view : cart.php, order : cart_n_order.php)
//if($cfg[skin] == "pod_group") $mode = "order";

//템플릿 분류
$m_goods = new M_goods();
$catLinkNo = $m_goods->getTemplateCategoryLinkNo($cid, $goods->listData[goodsno]);

//debug($goods->listPage);
//debug($goods->listTemplateData);
//$tpl->define('tpl', 'goods/list_template.htm');

//debug(date("Y-m-d H:i:s",time()));  
### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk
$pod_signed = signatureData($cid, $_SERVER[REQUEST_URI]."&date=".date("Y-m-d H:i:s",time()));
//debug($pod_signed);

$tpl->assign("mode",$mode);
$tpl->assign("pg",$goods->listPage);
$tpl->assign("data",$goods->listData);
$tpl->assign("loop",$goods->listTemplateData);
$tpl->assign("pageurl",$goods->listPage->page[url]);
$tpl->assign("tcatno",$catLinkNo[catno]);
$tpl->print_('tpl');
?>