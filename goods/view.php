<?

/*
* @date : 20180124
* @author : kjm
* @brief : 통합형 편집기의 옵션 데이터 구성 추가
* @request : story7
* @desc : view, _view, indb.php 수정
*/

/*
* @date : 20170117
* @author : chunter
* @brief : 템플릿셋ID를 상품등록시 추가함에 따라 편집기 호출시 templatsetID 를 편집기 호출할때 넘겨야 한다. 템플릿전시 기능과 동시 사용가능하다.
* @request : 기술지원팀, 김기웅 이사 (story7)
* @desc : _view**** 파일들 모두 수정함
*/
?>
<?
include_once "../_header.php";

$chkBrowser = getBrowser();
if ($chkBrowser[name] == "Internet Explorer")
	$browser = "IE";
else
	$browser = "notIE";

$goods = new Goods();
$goods->getView();
$editor = $goods->editor;
$data = $goods->viewData;

$inc = $goods->viewInc;

$goods->getMain();
$goods_list = $goods->getList();

//메인 블럭 컨텐츠 정보 조회.
$mcdata = $goods->mainBlockContentData;
//debug($mcdata);

//메인 블럭 정보 조회.
$data_block = $goods->mainBlockData;
//debug($data_block);

if ($data_block) {
	foreach ($data_block as $key => $val) {
		if ($val[block_code] == "main_block_11") {
			$blockDataArr[$val[block_code]] = $val;
		}
	}
}

//상세페이지 첫 옵션 구성
$first_cover_id = $data[cover_range][0][cover_id];
$first_cover_id = explode("_", $first_cover_id);

if (is_array($data[cover_range])) {
	foreach ($data[cover_range] as $key => $val) {
		$cover_range_data[$val[cover_range]][$val[cover_type]][$val[cover_paper_code]][$val[cover_coating_code]][] = $val[cover_id];
	}
}

//&& $val[cover_type] == $first_cover_id[1] && $val[cover_paper_code] == $first_cover_id[2] && $val[cover_coating_code] == $first_cover_id[3]

if ($data[goods_group_code] == "50") { //패키지 상품 관련 / 20171212 / kdk
	$inc = "./_view_package.php";
} else if ($data[goods_group_code] == "60") { //자동견적4.0 / 20180424 / kdk
	$inc = "./_view_print.php";
}

GetSEOTag($cid, $data);
//debug($data);
//debug($goods->viewData);
//debug($goods->viewInc);
$category = $goods->category;
//debug($category);
//debug($data[r_opt]);
//debug($data[r_addopt]);
/*
if (!$data[goodsno]){
	msg(_("준비중인 상품입니다."),-1);
	exit;
}
*/
//debug($data[addtionitem][recomand]);
//debug($data[addtionitem][relation]);

//NFUpload Config 파일 플래시 업로더 관련 작업 / 16.08.01 / kdk
//require_once('../js/NFUpload/nfupload_conf.inc.php');		// NFUpload Config
//echo $__NFUpload['UploadUrl'];

//setSkinTemplateDefine();			//스킨별 템플릿 변경 설정 적용			20160628		chunter

//WPOD 편집기 는 무조건 false,  퀙메뉴 사용여부 설정에 따라 없앤다.

//if($cid == 4483)
//$cfg[layout][goods_quick] = "miodio";

if ($cfg['dg_goods_detail_option_layout'] != "1" || $data['pods_use'] == "3")
	$tpl->define("goods_detail_quick_option", "common/blank.htm");
else
	$tpl->define("goods_detail_quick_option", "goods/view.quick.option." . $cfg[layout][goods_quick] . ".htm");

//Wpod 편집기 사용시 스크롤이동시 상단 메뉴를 없앤다.. 그래야  정상적인 편집기가 뜬다.
if ($cfg['layout']['top'] == "default" || $cfg['layout']['top'] == "miodio") {
	if ($data['pods_use'] == "3") {
		$tpl->define("top_menu_hidden_script", "common/top_menu_hidden_script.htm");
		$tpl->define("top_slide_banner", "common/blank.htm");
		$tpl->define("right_slide_area", "common/blank.htm");
	}
}

//템픞릿 전시에서 넘어온 templatesetID가 없을경우 상품에 설정된 템플릿셋 ID를 사용한다.			20180118		chunter
if (!$_GET[templateSetIdx]) $_GET[templateSetIdx] = $data[templateset_id];


//상품 종류에 따라 php 파일 분리. 상품의 기본 정보는 view.php 파일에서 가져오고 상푸별 구분 정보는 각 php 파일에서 불러오도록 처리한다.
//추후 상품별 스킨도 별도 사용할 수 있도록 확장할수 있도록 고려됨.      20140328

//P1 테마의 경우 상품 상세 화면 상단에 카테고리 링크가 필요. 구조가 규칙성이 없어 무조건 하드코딩이다.
if ($cfg[skin_theme] == "P1" && $_GET[catno]) {

	$cateStr3 = substr($_GET[catno], 0, 3);
	$cateStr6 = substr($_GET[catno], 0, 6);

	foreach ($P1_P_category as $Ckey => $Cvalue) {
		if ($cateStr3 == $Ckey || $cateStr6 == $Ckey) {
			$cateData[catno] = $Ckey;
			$cateData[catnm] = "전체";
			$all_sub_catetory[] = $cateData;

			foreach ($Cvalue as $Csubkey => $Csubvalue) {
				$cateData[catno] = $Csubkey;
				$cateData[catnm] = $Csubvalue;
				$all_sub_catetory[] = $cateData;
			}
		}
	}

	//$rootct = $mg->getCategoryInfo($cid, substr($_GET[catno], 0, 3));
	//$tpl->assign("rootCateName",$rootct[catnm]);
	if (!$all_sub_catetory)
		$all_sub_catetory = get_all_sub_category_P1($_GET[catno]);
	if ($all_sub_catetory)
		$tpl->assign("sub_cate", $all_sub_catetory);
}

//debug($data);
$cfg_emoney_data = getCfg("", "emoney");
//debug($cfg_emoney_data['emoney_send_ratio']);
if ($cfg_emoney_data['emoney_send_ratio'])
	$data['emoney_ratio'] = $cfg_emoney_data['emoney_send_ratio'] / 100;
//debug($data[r_opt]);
//debug($inc);exit;

// kidsnote의 경우 상품페이지 에서 센터 리스트 선택하는 selectbox 필요 211113 jtkim
if ($cid == "kidsnote") {
	$kn_centerList = array();
	if ($_COOKIE['kidsnote_access_token']) {
		$cfg[sns_login] = unserialize($cfg[sns_login]);

		$kidsnote_client_url = $cfg[sns_login][kidsnote_client_url] . "o/token/";
		$kidsnote_client_id = $cfg[sns_login][kidsnote_client_id];
		$kidsnote_client_secret = $cfg[sns_login][kidsnote_client_secret];
		$kidsnote_redirect_uri = "http://" . $_SERVER['SERVER_NAME'] . "/_oauth/callback_kidsnote.php";
		$kidsnote_code = $_GET['code'];

		$kidsnote_me_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/";
		$kidsnote_me_center_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/center";
		$kidsnote_me_employments_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/employments";
		$header_data = array(
			'Content-Type' => 'application/x-www-form-urlencoded'
		);

		$header_data[] = 'Content-Type: application/json';
		$header_data[] = 'Authorization: ' . $_COOKIE['kidsnote_token_type'] . " " . $_COOKIE['kidsnote_access_token'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $kidsnote_me_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$me_type = curl_exec($ch);
		curl_close($ch);
		$me_type_arr = json_decode($me_type, true);
		//print_r($me_type_arr);
		if ($me_type_arr['error']) {
			//echo "회원정보 가져오기 실패";
		}

		//admin 원장 / teacher 교사 / parent 부모
		if ($me_type_arr['type'] == "teacher") {
			$ch_ = curl_init();
			curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_employments_url);
			curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data);
			curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
			$me_teacher = curl_exec($ch_);
			// $info = curl_getinfo($ch_);
			//print_r($info);
			curl_close($ch_);
			$me_teacher_arr = json_decode($me_teacher, true);
			// print_r($me_teacher_arr);
			// 센터리스트 파싱 이후 $kn_centerList array push
			if(isset($me_teacher_arr)){
				if(count($me_teacher_arr) > 0){
					// center id 중복 제거 
					forEach($me_teacher_arr as $k){
						$center_id_duplicate = false;
						forEach($kn_centerList as $k_){
							if($k['center_id'] == $k_['center_id']){
								$center_id_duplicate = true; 
							}
						}
						if($center_id_duplicate === false){
							array_push($kn_centerList, $k);
						}
					}
				}
			}

		} else if ($me_type_arr['type'] == "admin") {
			$ch_ = curl_init();
			curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_center_url);
			curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data);
			curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch_, CURLOPT_VERBOSE, true);
			$me_admin = curl_exec($ch_);
			//$info = curl_getinfo($ch_);
			//print_r($info);
			curl_close($ch_);
			$me_admin_arr = json_decode($me_admin, true);
			// print_r($me_admin_arr);
                        if(isset($me_admin_arr)){
				array_push($kn_centerList, array('center_id' => $me_admin_arr['id'] , 'center_name' => $me_admin_arr['name']));
                        }

		}

	}
	if($_COOKIE['kidsnote_access_token'] && $_COOKIE['kidsnote_token_type']) $tpl->assign("kn_token",$_COOKIE['kidsnote_token_type']." ".$_COOKIE['kidsnote_access_token']);
	$tpl->assign("kn_centerList",$kn_centerList);
}


include $inc;
?>
