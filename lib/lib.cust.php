<?
/*
* @date : 20181118
* @author : kdk
* @brief : 사이별 설정 변경.
* @request : PODS20_DOMAIN cid별로 사용 추가.($cid._local_const.php)
* @desc :
*/

// $cid 선언.    bpmvalue 파일이 있는경우 기존 몰 구조로 판단함.
if (file_exists(dirname(__FILE__) . "/../conf/bpmvalue")) {
   $fp = fopen(dirname(__FILE__) . "/../conf/bpmvalue", "r");
   $cid = trim(fread($fp, 9999));
} else {

// 쿠키오류 임시주석처리 190724
// 	if ($_COOKIE[domain_cid]) {
//       $cid = $_COOKIE[domain_cid];
//    } else {

      //printhome 과 같은 형태는 cid 를 도메인에서 가져온다.      20140522    chunter
      foreach ($r_new_cid_domain as $value) {
         if (strpos($_SERVER[SERVER_NAME], $value) !== false) {
            $value = str_replace("." . $value, "", $_SERVER[SERVER_NAME]);
            $value = str_replace("http://", "", $value);
            //debug($value);
            if ($value != $_SERVER[SERVER_NAME])
               $cid = $value;
            break;
         }
      }
//   }

   if ($printgroup_service_not_check_flag != "Y");			//printgroup 사용기간(여부) 체크 안하기. 결제창 연동시 체크하지 않는다.			20170104
   {
      //몰이 활성화 상태인지 체크를 계속해야함.
      $m_mall = new M_mall();
      if (!$cid) {
         //자체 도메인 쓰는 업체인지 DB 검사.   20140522
         if (substr($_SERVER[SERVER_NAME], 0, 4) == 'www.')
            $server_domain = str_replace('www.', '', $_SERVER[SERVER_NAME]);      //www 접속을 제거. 도메인 셋팅시 www 을 제거했을 경우을 대비.
         else
            $server_domain = $_SERVER[SERVER_NAME];

         $domainInfo = $m_mall -> checkServiceDomain($server_domain);
      } else {
         $domainInfo = $m_mall -> getInfo($cid);
			}

			//mallinfo가 없을 경우 도메인으로 한번더 체크한다.			20170914		chunter
			if (! $domainInfo){
				if (substr($_SERVER[SERVER_NAME], 0, 4) == 'www.')
            $server_domain = str_replace('www.', '', $_SERVER[SERVER_NAME]);      //www 접속을 제거. 도메인 셋팅시 www 을 제거했을 경우을 대비.
         else
            $server_domain = $_SERVER[SERVER_NAME];

         $domainInfo = $m_mall -> checkServiceDomain($server_domain);
			}

	    if ($domainInfo) {
	        if ($domainInfo[state] == '3')//운영중
	        {
	      		$cid = $domainInfo[cid];
	        } else {
            //msg("사용이 중지된 사이트 입니다.업체에 문의하세요.", "www.printhome.co.kr");
            go("/service/ph_service_error.php?code=101");
            exit ;
         }
      } else {
         //msg("등록되지 않은 도메인입니다. 다시 확인해 주세요.", "www.printhome.co.kr");
         go("/service/ph_service_error.php?code=201");
         exit ;
      }
   }

   if ($cid)
      setCookie('domain_cid', $cid, time() + 3600 * 24, '/', $_SERVER[SERVER_NAME]);
}

//debug($_COOKIE, true);
//exit;

$cfg = getCfg();
$cfg_center = getCfgCenter();
if (!$cfg[top_header_color]) $cfg[top_header_color] = "#7ECAFD";		//헤더 기본 색상값 지정			20170804		chunter
if (!$cfg[img_w]) $cfg[img_w] = 450;
if (!$cfg[img_horizen_w]) $cfg[img_horizen_w] = 1000;

// 사무실 내부 ip 변경 210929 jtkim
if (strpos($_SERVER[SERVER_ADDR], "192.168.0.") > -1 || strpos($_SERVER[SERVER_ADDR], "192.168.1.") > -1 || $_SERVER[SERVER_ADDR] == "127.0.0.1"){
  $db->query("set names utf8");
}
else
{
  ### 서비스도메인 리다이렉션처리
  if (trim($cfg[urlService]) && $_SERVER[HTTP_HOST]!=trim($cfg[urlService])){
    header("location:http://".$cfg[urlService].$_SERVER[REQUEST_URI]);
  }
}



//GET 으로 스킨을 변경한경우 스킨정보가 쿠키에 담기기 때문에 처리해준다.			20160719		chunter
if ($_COOKIE[skin]){
	$cfg[skin] = $_COOKIE[skin];
}


//$cfg['skin_theme'] = "B1";

//자동견적 업로드 주소 설정    20140410    chunter
if ($cfg[est_upload_url] == "")
  $cfg[est_upload_url] = "http://files.ilark.co.kr/portal_upload/estm/file/upload.aspx";
if ($cfg[est_fileinfo_url] == "")
  $cfg[est_fileinfo_url] = "http://files.ilark.co.kr/portal_upload/estm/file/get_file_list.aspx";

if (!$cfg[source_save_days]) $cfg[source_save_days] = 15;

### 회원/관리자 인증처리
unset($sess);
if ($_COOKIE[sess]) $sess = getAuthMember();
else {

	//로그인 쿠키가 없을 경우 로그인 유지 데이타가 있을경우 처리			20160825		chunter
	if ($_COOKIE[login_connect_member_data] != "")
	{
		$login_connect_data = md5_decode($_COOKIE[login_connect_member_data]);
		if ($login_connect_data[member_id])
		{
			$login_data = memberLoginProc($login_connect_data[member_id], "", $login_connect_data[is_mobile], "/member/login.php", "N");
			if ($login_data[mid])
			{
				$sess = _member_login($login_data);

				//로그인 유지 데이타 다시 30일 기준으로 만들기.
				setCookie("login_connect_member_data",$_COOKIE[login_connect_member_data], time()+3600*24*30,"/");
			}
		}
	}
}
//debug($_COOKIE);
# B2B 회원로그인처리------------------------------------------------------------------------------------------------

	### 회원/관리자 인증처리
	unset($sess_admin);
	if ($_COOKIE[sess_admin]) $sess_admin = getAuthMember(1);
	$ici_admin = ($sess_admin['admin']) ? 1 : 0;

	#$intra_admin = ($ici_admin || $sess_admin['intra']) ? 1 : 0;
	//$super_admin = ($ici_admin && $sess_admin['level']==99) ? 1 : 0;
	$super_admin = ($ici_admin && $sess_admin['super']==99) ? 0 : 1;      //super_admin 조건을 level을 사용하지 않아 super 로 변경. super 99는 정산담당자. 20141120  chunter

	//defense 모듈 적용. 20160128 kdk
	if ($super_admin !== 1)		//슈퍼 관리자는 defense 사용하지 않는다.
	{
		define("__DEFENSE_PHP_VERSION_BASE_DIR__", dirname(__FILE__)."/defense/");
		include_once(__DEFENSE_PHP_VERSION_BASE_DIR__."/defense_referee.php");
	}



	//선결제 여부에따라 podmanage access code 설정하기				20161107		chunter
	//몰이 선결제이거나 센터가 선결제(몰 설정이 없을경우) 이거나.
	if (($cfg[before_account_flag] == "Y") || ($cfg_center[before_account_flag] == "Y" && $cfg[before_account_flag] != "N"))
	{
		if(!$cfg[podmanage_access_code])
		{
			$m_mall = new M_mall();
         $mallInfo = $m_mall -> getInfo($cid);

			$podmanage_url = "http://podmanage.bluepod.kr/_account/get_account_access_code.php";
			$podmanage_param = array(
				"mode" => "make_access_code",
				"center_cid" => $cfg_center[center_cid],
				"mall_cid" => $cid,
				"mall_domain" => $mallInfo[domain],
				"mall_name" => $mallInfo[sitenm]
			);
			$result_json = readUrlWithcurlPost($podmanage_url, $podmanage_param);
			$result_arr = json_decode($result_json, 1);
			//config DB 저장.
			if ($result_arr[center_cid] == $cfg_center[center_cid] && $result_arr[mall_cid] == $cid)
			{
				$cfg[podmanage_access_code] = $result_arr[podmanage_access_code];
				$m_config = new M_config();
				$m_config->setConfigInfo($cid, 'podmanage_access_code', $cfg[podmanage_access_code]);
			}
		}

		$cfg[before_account_flag] = "Y";

		//몰설정이 없으면 센터설정을 따른다.	//선정산 처리 주체가 설정되지 않을경우 기본값 oasis		20161123		chunter
		if (!$cfg[whos_account_call]) $cfg[whos_account_call] = $cfg_center[whos_account_call];
		if (!$cfg[whos_account_call])	$cfg[whos_account_call] = "OASIS";
	}

	if ($sess[mid] && $_COOKIE[cartkey]) {
	   //모든 페이지에서 처리 하지 말고 상품,장바구니 페이지 접근시만 처리한다.      20140403    chunter
	   if (in_array(CURRENT_FILE_NAME, $r_allow_cart_update_page)) {
	      $m_cart = new M_cart();
	      $m_cart->updateGuestCartToUserCart($cid, $sess[mid], $_COOKIE[cartkey]);
	      $m_cart->updateGuestEditToUserEdit($cid, $sess[mid], $_COOKIE[cartkey]);
	   }
	}


	//특정 페이지 스크립트 호출		20191112	jtkim
	//구매 완료 페이지 접속시 관련 스크립트 호출
	if($cfg[payment_script] && strpos(basename($_SERVER['PHP_SELF']),$r_script_page_list[payment_script]) !== false){
		$cfg[payment_script_use] = true;
	}else{
		$cfg[payment_script_use] = false;
	}
	//회원가입 완료 페이지 접속시 관련 스크립트 호출 member/indb 에서 처리
	// if($cfg[register_ok_script] && strpos(basename($_SERVER['PHP_SELF']),$r_script_page_list[register_ok_script]) !== false){
	// 	$cfg[register_ok_script_use] = true;
	// }else{
	// 	$cfg[register_ok_script_use] = false;
	// }

//cid별로 사용 하도록 변경 lib.cust.php에서 선언 ($cid._local_const.php) / 20181120 / kdk
$g_pods10_domain = "podstation.ilark.co.kr";
$g_pods20_domain = "podstation20.ilark.co.kr";
if (file_exists(dirname(__FILE__) . "/../conf/".$cid."_local_const.php")) {
    include_once dirname(__FILE__)."/../conf/".$cid."_local_const.php";
}
define("PODS10_DOMAIN", $g_pods10_domain);
define("PODS20_DOMAIN", $g_pods20_domain);
?>
