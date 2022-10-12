<?
/*
* @date : 20180822
* @author : chunter
* @brief : 아이스크림몰 회원연동에 따라 외부 회원 연동 기능 추가 개발
* @desc : 관리자 환경->운영->회원운영 설정도 추가됨
*/
?>
<?
$login_offset = true;
include_once "../_header.php";

if ($cfg[skin_theme] == "P1") {
	$save_id_connect_data = md5_decode($_COOKIE[save_id_connect_member_data]);  //아이디 저장 20200117 kkwon
	//debug($save_id_connect_data);
	if (!$_GET[page_type]) $_GET[page_type] = "login";
}

//외부 회원 연동 처리 설정정보			20180822		chunter
if ($cfg[member_system][login_system] == "out_login_param")
{
	if ($cfg[member_system][out_login_param_login_url])
		$goUrl = base64_decode($cfg[member_system][out_login_param_login_url]);
  else
  	$goUrl = "/main/index.php";

	if ($cfg[member_system][out_login_param_login_msg])
		msg(base64_decode($cfg[member_system][out_login_param_login_msg]), $goUrl);
	else
		go($goUrl);
	exit;
}

if($sess[mid]){
//   $goUrl = "http://".$_SERVER['HTTP_HOST'];
//   go($goUrl); exit;
   go("/"); exit;
}

//20140418 / minks / 로그인 후 이동할 경로
if ($cfg[login_page]) $url = $cfg[login_page];
else $url =  "../main/index.php";

//if ($sess) go($url); //로그인 되어 있을경우 이동한다    //해당 소스 변경하지 말것   20140919  chunter

if (!$_GET[rurl]){
   if($cfg[ssl_url] && $cfg[ssl_use] == "Y") $_GET[rurl] = $cfg[ssl_url];
   else $_GET[rurl] = $_SERVER[HTTP_REFERER];
}
else $_GET[rurl] = str_replace("|", "@", $_GET[rurl]); //|로 합친것을 @로 바꿔줌 / 14.05.09 / kjm

//2020.11.17 kkwon 외부몰 링크 타고 오는 경우 로그인 후 referer이동 버그 때문에 추가
if($cfg[rurl]) $_GET[rurl] = $cfg[rurl];

if ($cfg[sns_login]) $cfg[sns_login] = unserialize($cfg[sns_login]);

if($cfg[ssl_use] == "Y" /* && $cfg[skin_theme] == "M2" */) {
	// 서비스도메인과 현재 접속한 도메인이 같을경우 ssl은 접속한 도메인으로 변경
	if(trim($cfg[urlService]) && $_SERVER[HTTP_HOST] == $cfg[urlService])
		$ssl_action = "https://".$_SERVER[HTTP_HOST]."/member/indb.php";
	// 서비스도메인과 현재 접속한 도메인이 다른경우 ssl은 ssl설정url로 처리
	else
		$ssl_action = "https://".$_SERVER[HTTP_HOST]."/member/indb.php";

   $tpl->assign('ssl_action', $ssl_action);
}

if($cfg[sns_login][kidsnote_login_use] == 'Y'){

	$kidsnote_client_url = $cfg[sns_login][kidsnote_client_url]."o/authorize/";
	$kidsnote_client_id = $cfg[sns_login][kidsnote_client_id];
	$kidsnote_client_secret = $cfg[sns_login][kidsnote_client_secret];
	$kidsnote_redirect_uri = "http://".$_SERVER['SERVER_NAME']."/_oauth/callback_kidsnote.php";

	$kidsnote_oauth_url = $kidsnote_client_url."?client_id=".$kidsnote_client_id."&redirect_uri=".$kidsnote_redirect_uri."&response_type=code";
	$tpl->assign('kidsnote_oauth_url',$kidsnote_oauth_url);

}

$tpl->assign('rurl',$_GET[rurl]);
$tpl->print_('tpl');

?>
