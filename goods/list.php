<?
include "../_header.php";

$mg = new M_goods();
$ct = $mg->getCategoryInfo($cid, $_GET[catno]);
//debug($ct);

GetSEOTag($cid, $ct);

//$_GET[goodsno] 가 있을 경우는 템플릿셋,템플릿 리스트이고 $_GET[catno] 가 있을 경우는 일반상품,인화상품 리스트.
if (!$_GET[goodsno] && $ct[params]) {
	$_GET[goodsno] = $ct[params];
}

//인터프로 뷰페이지 이동. 'list_view_interpro' => "인터프로 뷰 + 템플릿 리스트".
if ($ct[goods_list] == "list_view_interpro") {
   $url = "view.php?catno=$_GET[catno]&goodsno=$_GET[goodsno]";
   //debug($url);
   header("location:".$url);
}

//알래스카 템플릿 페이지 이동. 'view_template.alaskaprint' => 알래스카 템플릿 리스트.
if ($ct[goods_list] == "view_template.alaskaprint") {
    $url = "view_template.alaskaprint.php?catno=$_GET[catno]&goodsno=$_GET[goodsno]";
    if($_GET[editor_type]) $url .= "&editor_type=".$_GET[editor_type];
    //debug($url);
    header("location:".$url);
}

if ($ct[catmain]){
	go("main.php?catno=$_GET[catno]");
}
if (!$ct[catno]){
	msg(_("잘못된접근입니다."),-1);
}

if ($ct[goods_list] == "") $ct[goods_list] = "list";
//debug($ct[goods_list]);



if ($cfg[skin_theme] == "P1")
	$all_sub_catetory = get_all_sub_category_P1($_GET[catno]);
else 
	$all_sub_catetory = get_all_sub_category($_GET[catno]);
//debug($all_sub_catetory);

//if ($ct[goods_view] == "") $ct[goods_view] = "view";
//debug($ct[goods_view]);

//서브카테고리 탭 여백 조정해야 하는 cid
$bCateTab = false;
if ($cid == "iscream" || $cid == "pixstory") $bCateTab = true;

if($cfg[skin_theme] == "P1"){
   if($_GET[intro_flag] == "Y")
      $inc = "_". $ct[goods_list] .".php";
   else
      $inc = "_list.php";
   
} else 
   $inc = "_". $ct[goods_list] .".php";

//debug($inc);
//exit;
include $inc;
?>