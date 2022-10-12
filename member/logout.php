<?
/*
* @date : 20180822
* @author : chunter
* @brief : 아이스크림몰 회원연동에 따라 외부 회원 연동 기능 추가 개발
* @desc : 관리자 환경->운영->회원운영 설정도 추가됨
*/
?>
<?
include_once "../lib/library.php";


switch ($cfg[member_system][login_system]){

	case "out_cookie":

		$url = $cfg[member_system][out_cookie][url][logout];
		$url = parse_url($url);
		$url[query] = explode("&",$url[query]);
		$url[query][] = $cfg[member_system][out_cookie][cookie_val][mid]."=".urlencode($sess[mid]);
		$url[query][] = $cfg[member_system][out_cookie][val][return_url]."=".urlencode($_SERVER[HTTP_REFERER]);
		$url[query] = implode("&",array_notnull($url[query]));
		$gourl = $url[scheme]."://".$url[host].$url[path]."?".$url[query];

		setCookie('sess','',0,'/');
		setCookie('member','',0,'/');
		setCookie('cartkey','',0,'/');

		go($gourl);

		exit; break;

	case "out_login_cookie":

		$url = $cfg[member_system][out_login_cookie][url][logout];
		$url = parse_url($url);
		$url[query] = explode("&",$url[query]);
		$url[query][] = $cfg[member_system][out_login_cookie][val][mid]."=".urlencode($sess[mid]);
		$url[query][] = $cfg[member_system][out_login_cookie][val][logout_return_url]."=".urlencode("http://".$_SERVER[HTTP_HOST]);

		$url[query] = implode("&",array_notnull($url[query]));
		$gourl = $url[scheme]."://".$url[host].$url[path]."?".$url[query];

		setCookie('sess','',0,'/');
		setCookie('member','',0,'/');
		setCookie('cartkey','',0,'/');
		setCookie('member_check','',0,'/');

		go($gourl);

		exit; break;

	case "out_login":

		$url = $cfg[member_system][out_login][url][logout];
		$url = parse_url($url);
		$url[query] = explode("&",$url[query]);
		$url[query][] = $cfg[member_system][out_login][val][mid]."=".urlencode($sess[mid]);
		$url[query] = implode("&",array_notnull($url[query]));
		$gourl = $url[scheme]."://".$url[host].$url[path]."?".$url[query];

		$ret = readurl($gourl);
		$ret = json_decode($ret,1);
		if ($ret[success]=="F"){
			msg($ret[msg],-1);
			exit;
		} else if ($ret[success]=="T"){
			setCookie('sess','',0,'/');
			setCookie('member','',0,'/');
			setCookie('cartkey','',0,'/');
		} else {
			msg(_("통신장애. 관리자에게 문의해주세요."),-1);
			exit;
		}

		break;

	default:
		if($cfg[sns_login]) $cfg[sns_login] = unserialize($cfg[sns_login]);
		if($cfg[sns_login][kidsnote_login_use] == 'Y'){
			//kidsnote logout request
			if($_COOKIE[kidsnote_access_token] && $_COOKIE[kidsnote_token_type]){
				$kidsnote_client_url = $cfg[sns_login][kidsnote_client_url]."o/token/";
				$kidsnote_client_id = $cfg[sns_login][kidsnote_client_id];
				$kidsnote_client_secret = $cfg[sns_login][kidsnote_client_secret];
				$kidsnote_logout_url = $cfg[sns_login][kidsnote_client_url]."o/revoke_token/";

				$kidsnote_encode = $kidsnote_client_id.":".$kidsnote_client_secret;
				$kidsnote_encode_res = base64_encode($kidsnote_encode);
				$logout_header[] = 'Content-Type: application/x-www-form-urlencoded';
				$logout_header[] = 'Authorization: Basic '.$kidsnote_encode_res;
				$kidsnote_logout_str = 'token='.$_COOKIE[kidsnote_access_token];

				$ch_logout = curl_init();
				curl_setopt($ch_logout, CURLOPT_URL, $kidsnote_logout_url);
				curl_setopt($ch_logout, CURLOPT_HTTPHEADER, $logout_header);
				curl_setopt($ch_logout, CURLOPT_POST, true);
				curl_setopt($ch_logout, CURLOPT_POSTFIELDS, $kidsnote_logout_str);
				curl_setopt($ch_logout, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch_logout, CURLOPT_RETURNTRANSFER, 1);
				$logout_res = curl_exec($ch_logout);
				curl_close($ch_logout);

				$kidsnote_me_url = $cfg[sns_login][kidsnote_client_url]."v1/me/";
				$header_data2[] = 'Content-Type: application/json';
				$header_data2[] = 'Authorization: '.$_COOKIE[kidsnote_token_type]." ".$_COOKIE[kidsnote_access_token];

				$ch_ = curl_init();
				curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_url);
				curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data2);
				curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
				curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
				$me_res = curl_exec($ch_);
				curl_close($ch_);
				$me_res_arr = json_decode($me_res, true);
			    // debug($me_res);

				setCookie('kidsnote_access_token','',0,'/');
				setCookie('kidsnote_token_type','',0,'/');
			}
		}

		setCookie('sess','',0,'/');
		setCookie('member','',0,'/');
		setCookie('cartkey','',0,'/');

		setCookie('login_connect_member_data','',0,'/');

		//로그아웃시 기존 cartkey 를 저장한게 있다면 복원 시킨다.			20160829		chunter
		if ($_COOKIE[login_connect_cartkey])
		{
			setCookie('cartkey',$_COOKIE[login_connect_cartkey],0,'/');
		}

		//외부 회원 연동 처리 설정정보			20180822		chunter
		if ($cfg[member_system][login_system] == "out_login_param")
		{
			if ($cfg[member_system][out_login_param_logout_url])
				$goUrl = base64_decode($cfg[member_system][out_login_param_logout_url]);
		  	else
		  		$goUrl = "/main/index.php";

			if ($cfg[member_system][out_login_param_logout_msg])
				msg(base64_decode($cfg[member_system][out_login_param_logout_msg]), $goUrl);
			else
				go($goUrl);
			exit;
		}

		break;

}

// if (!$_GET[rurl]) $_GET[rurl] = $_SERVER[HTTP_REFERER];
// go($_GET[rurl]);
if(strpos($_GET[rurl], "leave_ok.php") !== false) {  //2019.11.29 kkwon
	go($_GET[rurl]);
	exit;
}else{
	// 로그아웃시 인덱스페이지로 이동   jtkim 191119
	go("/");
	exit;
}
?>
