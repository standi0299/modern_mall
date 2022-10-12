<?
/*
* @date : 20180807
* @author : kdk
* @brief : 템플릿 리스트.(편집기 )
* @request : 
* @desc : 알래스카프린트 디자인 추가.
* @todo :
*/

include "../_header.php";
$_GET = $_REQUEST;

//$cfg[layout][top] = "alaskaprint";

$goods = new Goods();
$goods->getListTemplate();
$editor = $goods->editor;
$category = $goods->category;
$ret = $goods->listTemplateDataRet;
//debug($goods->listData);
//debug($goods->listPage);

//debug($goods->listTemplateDataRet);
//debug($editor);
//debug($category);
//debug($cfg[layout][top]);

if (!$goods->listData[goodsno]){
    msg(_("해당 상품은 준비 중 입니다.")."\\n"._("이용에 불편을 드려 대단히 죄송합니다."));
    //msg(_("해당 상품은 준비 중 입니다.")."\\n"._("이용에 불편을 드려 대단히 죄송합니다."),-1);
}

$selected[page_num][$_GET[page_num]] = "selected";

//좌측 카테고리, 검색창으로 템플릿 검색 하여 페이지 이동인 경우. option_json 데이타 처리.
if ($_POST[optionjson] && $_POST[optionjson] != "") {
    $_GET[searchjson] = rawurlencode(base64_encode($_POST[optionjson]));
}
else {
    if ($_GET[searchjson] && $_GET[searchjson] != "") {
        $_POST[optionjson] = base64_decode(rawurldecode($_GET[searchjson]));
    }
}

//상품 상세 페이지에서 POST로 넘어온 항목값. (기존 알래스카 프로세스 사용안함.)
//if ($_POST[option_json] && $_POST[option_json] != "") {
//    $_POST[option_json] = base64_encode($_POST[option_json]);
//}

if($_POST[option_json]){  //20200121  kkwon
	$_POST[option_json] = json_decode($_POST[option_json]);
}

//print_r($_POST[option_json]);
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

//편집완료 후 페이지 이동에 사용함. 
//알래스카 사용안함 20190111 kdk
$come_back = "web_list";
if ($_GET[come_back]) {
    //상품상세보기에서 직접디자인하기를 통해 넘어옴.(web_view)
    $come_back = $_GET[come_back];
}

if($goods->listData[pods_editor_type])
    $goods->listData[pods_editor_type] = json_decode($goods->listData[pods_editor_type],1);

//알래스카 템플릿 리스트 샘플이미지 사이즈는 3개 중 하나를 입력해야합니다. (type1:694x125 / type2:142x454 / type3:326x231)
//이미 설정한 값이 있으면 그대로 사용한다.
if ($cfg[listimg_w] == "694" && $cfg[listimg_h] == "125") {
    $temp_atc = "type_1";
}
else if ($cfg[listimg_w] == "142" && $cfg[listimg_h] == "454") {
    $temp_atc = "type_2";
}
else if ($cfg[listimg_w] == "326" && $cfg[listimg_h] == "231") {
    $temp_atc = "type_3";
}

if($temp_atc == "") {
    if ($cfg[listimg_w] == "" && $cfg[listimg_h] == "") {
        //debug($goods->listTemplateData[$editor[0][podsno]]);
        $templateSizeData = $goods->listTemplateData[$editor[0][podsno]][0][templateSize];
        $templateSize = explode("X",$templateSizeData);
        //debug($templateSize);
        
        if($templateSize[0] < 100) {
            $cfg[listimg_w] = "326";
            $cfg[listimg_h] = "231";
            //$temp_atc = "type_3";
        }
        
        $cfg[listimg_w] = floor($templateSize[0]);
        $cfg[listimg_h] = floor($templateSize[1]);
        $css_img_w = floor($templateSize[0]+20);
        $css_img_h = floor($templateSize[1]+60);
    }
    else {
        if ($cfg[listimg_h] == "") $cfg[listimg_h] = $cfg[listimg_w];
        $css_img_w = floor($cfg[listimg_w]+20);
        $css_img_h = floor($cfg[listimg_h]+60);
    }
//debug($temp_atc);
//debug($cfg[listimg_w]);
//debug($cfg[listimg_h]);
//debug($css_img_w);
//debug($css_img_h);
}


//템플릿 태그. / 20190129 / kdk
$templateData = $goods->listTemplateData[$editor[0][podsno]];
if ($templateData) {
    $templateTags = array();

    foreach ($templateData as $key => $val) {
        if($val[templateTags]) {
            //"," 구분함.
            if (strpos($val[templateTags], ",") !== false) {
                $tagArr = split(',', $val[templateTags]);
                foreach ($tagArr as $tag) {
                    //debug($tag);
                    if($tag) $templateTags[$tag] = $tag;
                }
            }
            else            
                $templateTags[$val[templateTags]] = $val[templateTags];
        }
    }
    //debug($templateTags);
}

//템플릿 분류
$m_goods = new M_goods();
$catLinkNo = $m_goods->getTemplateCategoryLinkNo($cid, $goods->listData[goodsno]);

//debug($goods->listPage);
//debug($goods->listTemplateData);
//$tpl->define('tpl', 'goods/list_template.htm');

//debug(date("Y-m-d H:i:s",time()));  
### 편집기 견적 정보 임시 저장을 20160325 by kdk
$pod_signed = signatureData($cid, $_SERVER[REQUEST_URI]."&date=".date("Y-m-d H:i:s",time()));
//debug($pod_signed);

//카테고리 리스트 버튼효과
$uri = $_SERVER[REQUEST_URI];

$tpl->assign("mode",$mode);
$tpl->assign("pg",$goods->listPage);
$tpl->assign("data",$goods->listData);
$tpl->assign("loop",$goods->listTemplateData);
$tpl->assign("pageurl",$goods->listPage->page[url]);
$tpl->assign("tcatno",$catLinkNo[catno]);
$tpl->assign("tcatnm",$catLinkNo[catnm]);
$tpl->assign("temp_atc_class",$temp_atc);
$tpl->assign("come_back",$come_back);
$tpl->assign("searchtext_B1",$uri);

$tpl->print_('tpl');
?>