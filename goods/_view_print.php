<?
/*
* @date : 20180424
* @author : kdk
* @brief : 자동견적 4.0.
* @desc :
*/

$editor_mode = "N";
$calcu_mode = "Y";

//일반상품 수량은 가격(엑셀)테이블에서 가져온다.
if ($data[extra_option] == "DG01" || $data[extra_option] == "DG02" || $data[extra_option] == "PR01" || $data[extra_option] == "PR02") $_POST[print_goods_type] = $data[extra_option];

include_once '../print/lib_print.php';
//debug($r_iro_normal_product);
//debug($print_goods_item[$data[goodsno]]);
//debug($data);
//debug($r_ipro_paper);
//debug($data[extra_option]);

//일반상품 수량은 가격(엑셀)테이블에서 가져온다.
if ($data[extra_option] == "DG01" || $data[extra_option] == "DG02") {
    foreach ($r_iro_normal_product as $k => $v) {
        //debug($k);
        //debug($v);
        foreach ($v as $k1 => $v1) {
            //debug($k1);
            $print_page[$k1] = $k1;
        }
    }
}
//옵셋상품 수량은 상품별로 각각 설정한다.
else if ($data[extra_option] == "OS01" || $data[extra_option] == "OS02") {
    if (in_array($data[goodsno], $r_inpro_print_goodsno['OPO'])) //옵셋-포스터상품코드
    {
        $print_page = getPrintPage("OPO");
    }
    else if (in_array($data[goodsno], $r_inpro_print_goodsno['OJD'])) //옵셋-전단지,리플렛상품코드 
    {
        $print_page = getPrintPage("OJD");
    }
    else if (in_array($data[goodsno], $r_inpro_print_goodsno['OBO'])) //옵셋-책자,단행본,카탈로그,브로슈어 상품코드
    {
        $print_page = getPrintPage("OBO");
    }    
}

#현수막 실사출력 상품 수량은 가격(엑셀)테이블에서 가져온다.
else if ($data[extra_option] == "PR01" || $data[extra_option] == "PR02") {
    $print_page = getPrintPage("PR");
}

//견적 옵션 이미지,도움말,팁 조회.
$m_print = new M_print();
$info = $m_print->getOptionInfoList($cid, "and opt_img <> ''");
foreach ($info as $key => $val) {
    //$img[$val[opt_key]] = "/data/print/goods_items_img/".$cid."/".$val[opt_img];
    $img[] = array('key' => $val[opt_key], "url" => "/data/print/goods_items_img/".$cid."/".$val[opt_img]);
}
if ($img) $print_goods_item[$data[goodsno]][img] = json_encode($img);

$info = $m_print->getOptionInfoList($cid, "and opt_desc <> ''");
foreach ($info as $key => $val) {
    $desc[$val[opt_key]] = $val[opt_desc]; 
}
if ($desc) $print_goods_item[$data[goodsno]][desc] = $desc;

$info = $m_print->getOptionInfoList($cid, "and opt_tip <> ''");
foreach ($info as $key => $val) {
    $tip[$val[opt_key]] = $val[opt_tip]; 
}
if ($tip) $print_goods_item[$data[goodsno]][tip] = $tip;

//내 파일 가져오기용 업로드 스토리지 아이디.
$micro = explode(" ",microtime());
$storageKey = date("Ymd")."-".substr($micro[1].sprintf("%03d",floor($micro[0]*1000)), -6);
//debug($storageKey);

//인터프로,알래스카 구분이 필요함.
if ($cfg[layout][top] == "alaskaprint") {
    //debug($print_goods_item[$data[goodsno]]);
    //debug($_r_alaska_print_goods_type);
    //debug($_r_inpro_print_goods_type);
    
    //템플릿리스트에 편집기가 실행되어 상품상세페이지로 바로 됨어옴.
    if($_GET[editor_type] == "web_list" || $_GET[storageid]) {
        $editor_mode = "Y";
    }

    $tpl -> define('tpl', $_r_alaska_print_goods_type[$data[extra_option]]['html']);
}
else {
	$tpl -> define('tpl', $_r_inpro_print_goods_type[$data[extra_option]]['html']);
}

#####review#####
//include "../lib/class.page.php";
$db_table = "exm_mycs";
$where_qna[] = "id = 'qna'";
$where_qna[] = "cid = '$cid'";
$where_qna[] = "goodsno = '$_GET[goodsno]'";

$pg_qna = new Page($_GET[page], 5);
$pg_qna->setQuery($db_table,$where_qna,"","order by no desc");
$pg_qna->exec();

$res = &$pg_qna->resource;
while($tmp=$db->fetch($res)){
   $tmp[content] = $tmp[content];
   $tmp[regdt] = substr($tmp[regdt],0,10);
   $qna[] = $tmp;
}

$tpl->assign('pg_qna',$pg_qna);
$tpl->assign('qna',$qna);

$db_table = "exm_review";
$where_review[] = "cid = '$cid'";
$where_review[] = "goodsno = '$_GET[goodsno]'";
$where_review[] = "review_deny_admin = '0'";
$where_review[] = "review_deny_user = '0'";

$pg_review = new Page($_GET[page], 5);
$pg_review->setQuery($db_table,$where_review,"","order by no desc");
$pg_review->exec();

$res = &$pg_review->resource;
while($tmp=$db->fetch($res)){
   $tmp[content] = $tmp[content];
   $tmp[regdt] = substr($tmp[regdt],0,10);
   $review[] = $tmp;
}

$tpl->assign('pg_review',$pg_review);
$tpl->assign('review',$review);

if ($data[img][0]) {
    $default_img = $data[img][0]; 
}
else {
    $default_img = "/data/noimg.png"; 
}
//debug($print_goods_item[$data[goodsno]]);

//템플릿 전시 리스트에서 견적상품으로 넘어온 경우 / 20190412 / kdk
if($_POST[templateSetIdx] && $_POST[templateIdx]) {    
    $data[templateSetIdx] = $_POST[templateSetIdx];
    $data[templateIdx] = $_POST[templateIdx];
    $data[templateName] = $_POST[templateName];
    $data[templateURL] = $_POST[templateURL];
}

$tpl -> assign($data);
$tpl -> assign("print_data", $print_goods_item[$data[goodsno]]);
$tpl -> assign("print_goods_type", $data[extra_option]);
$tpl -> assign("print_img", $print_goods_item[$data[goodsno]][img]); //옵션 이미지 정보 (자바스크립트에서 사용)
$tpl -> assign("default_img", $default_img); //상품 이미지 정보 
$tpl -> assign("print_page", $print_page); //일반상품 수량(DG01,DG02)
$tpl -> assign('editor_mode',$editor_mode);
$tpl -> print_('tpl');
?>