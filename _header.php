<?
ob_start();

/* 장바구니 키 부여 */
if (!$_COOKIE[cartkey]) {
   //각 사이트마다 편집보관함 유지기간이 다르기 때문에 해당 사이트의 보관함 일수로 계산한다 / 16.03.14 / kjm
   if (!$cfg[source_save_days])
      $cfg[source_save_days] = 15;

   setCookie('cartkey', $_SERVER[REMOTE_ADDR] . "_" . time(), time() + 3600 * 24 * $cfg[source_save_days], '/');
   $_COOKIE[cartkey] = $_SERVER[REMOTE_ADDR] . "_" . time();
}

if ($_GET[tophidden]) {
   setCookie("tophidden", "1", 0, '/');
   $_COOKIE[tophidden] = $_SERVER[REMOTE_ADDR] . "_" . time();
}

include dirname(__FILE__)."/lib/library.php";
include dirname(__FILE__)."/lib/inc.counter.php";
include dirname(__FILE__)."/lib/inc.cnt.php";
include dirname(__FILE__)."/lib/template_/Template_.class.php";

//OutLoginCheck 2019.11.28 kkwon
if($cfg[member_system]['login_system']=="out_login_cookie"){
	if($_COOKIE['member_id'] && !$_COOKIE['member_check']){
		$m_member = new M_member();
		$m_member->setOutLoginBMemberInsert($cid, $_COOKIE['member_id'], "1");
	}
}

if($cfg[skin_theme] == "M2" || $cfg[skin_theme] == "M3"){
   $m_member = new M_member();
   $m_cart = new M_cart();
   $m_goods = new M_goods();

   $use_coupon_cnt = count($m_member->getMyCouponList($cid, $sess[mid]));
   setCookie("use_coupon_cnt", $use_coupon_cnt, 0, '/');

   if($sess[mid]){
      $cart_cnt = $m_cart->getCartCnt($cid, $sess[mid]);
      setCookie("cart_cnt", $cart_cnt, 0, '/');

      $poke_goods_cnt = count($m_goods->get_goods_poke_list($cid, $sess[mid]));
      setCookie("poke_goods_cnt", $poke_goods_cnt, 0, '/');
   } else {
      $cart_cnt = $m_cart->getCartCnt($cid, '', $_COOKIE[cartkey]);
      setCookie("cart_cnt", $cart_cnt, 0, '/');

      $use_coupon_cnt = 0;  //20200129 kkwon
      setCookie("use_coupon_cnt", $use_coupon_cnt, 0, '/');  //20200129 kkwon
      $poke_goods_cnt = 0;  //20200129 kkwon

      setCookie("poke_goods_cnt", 0, 0, '/');
   }
}

if($cfg[skin_theme] == "P1"){
   $m_cart = new M_cart();
   if($sess[mid]){
      $cart_cnt = $m_cart->getCartCnt($cid, $sess[mid]);
      setCookie("cart_cnt", $cart_cnt, 0, '/');
   } else {
      $cart_cnt = $m_cart->getCartCnt($cid, '', $_COOKIE[cartkey]);
      setCookie("cart_cnt", $cart_cnt, 0, '/');
   }

   $isMobileFlag = isMobile(); //20181127 / minks / css 분기를 위해 추가
}

if (strpos($_SERVER[SERVER_ADDR], "192.168.0.") > -1 || $_SERVER[SERVER_ADDR] == "127.0.0.1"){
	$db->query("set names utf8");
}

//$tpl_file = substr(str_replace(dirname(__FILE__),'',$_SERVER[SCRIPT_FILENAME]),1,-3)."htm";
$tpl_file = substr(str_replace(dirname(__FILE__), '', $_SERVER[SCRIPT_NAME]), 1, -3) . "htm";

if ($cfg[apply_parking] && $tpl_file != "main/parking.htm")
   header("location:/main/parking.php");

if ($cfg[member_system]['system']=="close" && !$login_offset && !$sess)
{
   //강제 이동 페이지가 설정되어 있을 때
   if($cfg[member_system][redirect_url]){
      go($cfg[member_system][redirect_url]);
   } else {
      if($_REQUEST['callmode']=="editor" && $_REQUEST['userid']){
		//kkwon	2020.10.29 bs 편집기 호출 크롬 세션 공유용 처리
		 //lib/class.pods.service.php>introurl 수정
	  }else {
		go("/member/login.php");
	  }

   }
}

if ($_COOKIE[skin]) {
    $cfg[skin] = $_COOKIE[skin];
}

if ($_GET[skin]) {
   setCookie('skin', $_GET[skin], 0, '/');
   $cfg[skin] = $_GET[skin];
}

$tpl = new Template_;
$tpl -> template_dir = dirname(__FILE__) . "/skin";
$tpl -> skin = $cfg[skin];
$tpl -> compile_dir = dirname(__FILE__) . "/_compile";
$tpl -> cache_dir = dirname(__FILE__) . "/_cache";
$tpl -> prefilter = "adjustPath";

### 레이아웃정보
$f = str_replace("/", "_", $tpl_file);
$f = str_replace(".htm", ".php", $f);
if (is_file("../skin/{$cfg[skin]}/_conf/$f")) {
   include "../skin/{$cfg[skin]}/_conf/$f";
}

$r_layout = array("top", "left", "right", "bottom");
//debug($cfg[layout]);

foreach ($r_layout as $v) {
    if ($cfg[bd][$v])
        $cfg[layout][$v] = $cfg[bd][$v];
    if (!$cfg[layout][$v] || $cfg[skin] == "m_default")
        $cfg[layout][$v] = "default";

    if ($cfg[layout][$v] != "hidden")
        $tpl -> define($v, 'layout/' . $v . '.' . $cfg[layout][$v] . '.htm');
}

### 카테고리 정보
if (!$_GET[catno]) $_GET[catno] = 0;

$res = $db -> query("select * from exm_category where cid = '$cid' and hidden = 0 order by sort");
while ($tmp = $db -> fetch($res)) {
if (strlen($tmp[catno]) < 7) {
 	$r_cate[substr($tmp[catno], 0, 3)][substr($tmp[catno], 0, 6)][sub] = $tmp[catno];
   if (substr($tmp[catno], 0, 3) == substr($tmp[catno], 0, 6))
   	$r_cate[substr($tmp[catno], 0, 3)] = "";
	} else {
 	$r_cate[substr($tmp[catno], 0, 3)][substr($tmp[catno], 0, 6)][sub] = $tmp[catno];
	}
}

//공통 상단, 하단 배너 정보 가져오기(DB 조회수를 줄이기 위해 )   20140403    chunter
$r_user_banner = array();
//DB에서 공통적으로 사용하는 배너 정보를 가져와서 담아 놓는다.  추후 f_bannber 함수 내부에서 사용된다.
$common_banner_code = "'top_logo', 'top_left_banner', 'top_right_banner','_sys_btn_top_search','left_category_top_001','left_category_top_006','left_category_banner', 'layer_banner_right','bottom_banner'";
$query = "select * from exm_banner where cid = '$cid' and skin = '$cfg[skin]' and code in (" . $common_banner_code . ")";
$res = $db -> query($query);
while ($tmp = $db -> fetch($res)) {
   $r_user_banner[$tmp[code]] = $tmp;
}
//db 에 없는 배너정보를 구분하여 저장해 준다.
$common_banner_code_arr = explode(',', $common_banner_code);
foreach ($common_banner_code_arr as $value) {
    $value = str_replace("'", "", trim($value));
    if (!$r_user_banner[$value])
        $r_user_banner[$value] = array();
}

### 현재 위치
$file_path = $_SERVER['PHP_SELF'];
//debug($file_path);
$file_path2 = explode("/", $file_path);
$file_location = substr($file_path2[2], 0, strrpos($file_path2[2], "."));

$dir_path = dirname($_SERVER[PHP_SELF]);
$dir_location = substr($dir_path, strrpos($dir_path, "/") + 1);

### 메인페이지확인
if ($file_path=="/main/index.php")  $mainpage = 1;
if ($cfg[main_page] && $cfg[main_page_popup]){
   $tmp = parse_url($cfg[main_page]);
   if (strpos($tmp[path],$file_path) !== false) $mainpage = 1;
}

//AX 편집기 안내 문구 나타나지 않게 하기. (무조건 mainpage)      20131204    chunter
if ($cfg[AX_editor_use] == "N") $mainpage = "1";

//seo 검색 엔진 노쿨 설정 자겨오기
if (CURRENT_FILE_PATH_NAME != "/goods/list.php" && CURRENT_FILE_PATH_NAME != "/goods/view.php")
	GetSEOTag($cid);

### b2b 로그인
//debug($cfg[member_system]);

if (is_file("../skin/$cfg[skin]/img/_topmenu_background.jpg")) {
   $dummy = getImageSize("../skin/$cfg[skin]/img/_topmenu_background.jpg");
   $h_topmenu = $dummy[1];
}

//저작권표시방법(copyright) {YYYY} 년도 치환
if($cfg[copyright]) {
	$cfg[copyright] = str_replace("{YYYY}", date("Y"), $cfg[copyright]);
}

//네이버 연관채널 연동설정 가져오기
if ($cfg[naver_relation_config]) $cfg[naver_relation_config] = unserialize($cfg[naver_relation_config]);

if (count($cfg[naver_relation_config][naver_relation_sameAs]) > 0) {
	foreach ($cfg[naver_relation_config][naver_relation_sameAs] as $nrc_k=>$nrc_v) {
		$cfg[naver_relation_config][naver_relation_sameAs][$nrc_k] = urldecode($nrc_v);
	}
}

//debug($_COOKIE);
//4.0 exe 편집기 설치 안내

//공통 헤더에서 선언해야 할 부분이다.
$tpl->define("slide_exe_info", "common/slide_exe_info.htm");
if($cfg[dg_top_slide_banner] || CURRENT_FILE_PATH_NAME == "/goods/view.php")			//상품 상세에서는 나오지 않는다. (다른 몰 참고할때)
	$tpl->define("top_slide_banner", "common/blank.htm");
else
	$tpl->define("top_slide_banner", "common/top_slide_banner01.htm");

if ($cfg['dg_right_slide_menu'])
{
	if (in_array(CURRENT_FILE_PATH."/".CURRENT_FILE_NAME, $_r_right_slide_not_use_page))
		$tpl->define("right_slide_area", "common/blank.htm");
	else{
	   if($cfg[skin_theme] == "M2") $theme_type = ".M2";
      else if($cfg[skin_theme] == "I1") $theme_type = ".I1";
      else if($cfg[skin_theme] == "M3") $theme_type = ".M3";

		$tpl->define("right_slide_area", "common/right_slide_default".$theme_type.".htm");
   }
} else {
	$tpl->define("right_slide_area", "common/blank.htm");
}

//상단 메뉴 고정 사용시 감추는 스크립트 비 활성화		1:고정 사용함.
if ($cfg['layout']['top'] == "default" || $cfg['layout']['top'] == "miodio")
{
	if ($cfg['dg_top_menu_fix'] != "1")
		$tpl->define("top_menu_hidden_script", "common/top_menu_hidden_script.htm");
	else
		$tpl->define("top_menu_hidden_script", "common/blank.htm");
}

//pixstory 테마인경우	 top 파일을 변경해야 한다.			20180823		chunter
if ($cfg[skin] == "modern" && $cfg[skin_theme] == "P1")
{
	if ($_SERVER["PHP_SELF"] == "/goods/view.php")
		$tpl->define("top", "layout/top.product.htm");

	if ($_SERVER["PHP_SELF"] == "/board/gallery_list.php" || $_SERVER["PHP_SELF"] == "/board/gallery_view.php" || $_SERVER["PHP_SELF"] == "/board/gallery_write.php")
		$tpl->define("top", "layout/top.gallery.htm");

	// orderpayment 페이지에서 jquery.js 파일 분기 처리 필요 210727 jtkim
	// if($file_path) $tpl->assign("file_path",$file_path);
}

// 네이버 메타태그 추가 200607 jtkim
if ($cfg[skin] == "modern"){
   $m_config = new M_config();
   $naver_site_verification = $m_config->getConfigInfo($cid, "naver_site_verification");
   if($naver_site_verification[value]) $cfg['naver_site_verification'] = $naver_site_verification[value];
}




//$tpl->assign('tpl',$tpl);
//$tpl -> define("top", 'layout/top.interpro.htm');
//$tpl -> define("bottom", 'layout/bottom.interpro.htm');
//20140421 / minks / bizcard에서 상단메뉴 분기하는데 사용
//$tpl->assign('tpl_file', $tpl_file);

$tpl->define(array('tpl' => $tpl_file, 'header' => 'layout/layout.htm?header', 'footer' => 'layout/layout.htm?footer', 'header_popup' => 'layout/header.popup.htm', 'footer_popup' => 'layout/footer.popup.htm', 'category' => 'module/category.htm', 'banner_left' => 'layout/banner_left.htm', 'banner_right' => 'layout/banner_right.htm', ));
//debug($cfg);
?>
