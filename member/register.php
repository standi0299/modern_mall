<?
/*
* @date : 20180822
* @author : chunter
* @brief : 아이스크림몰 회원연동에 따라 외부 회원 연동 기능 추가 개발
* @desc : 관리자 환경->운영->회원운영 설정도 추가됨
  
* @date : 20180307
* @author : kdk
* @brief : 회원가입항목설정 사용.
* @request : 
* @desc : fieldset 사용 (exm_config)
* @todo :
*/
?>
<?
$login_offset = true;
include "../_header.php";

//외부 회원 연동 처리 설정정보			20180822		chunter
if ($cfg[member_system][login_system] == "out_login_param")
{
	if ($cfg[member_system][out_login_param_regist_url]) 
		$goUrl = base64_decode($cfg[member_system][out_login_param_regist_url]);
  else 
  	$goUrl = "/main/index.php";
	
	if ($cfg[member_system][out_login_param_regist_msg])	
		msg(base64_decode($cfg[member_system][out_login_param_regist_msg]), $goUrl);
	else 
		go($goUrl);
	exit;
}


$r_manager = get_manager("y");

if ($sess[mid]) {
	go("../main/index.php");
	exit;
}

//이용약관 : agreement
//개인정보수집동의 : policy1
//개인정보활용동의 : policy2
//개인정보위탁동의 : policy3
//개인정보마케팅활용동의 : policy4
//개인정보 수집/활용/위탁 동의 : policy5
//개인정보마케팅활용동의 사용여부: policy4_flag
$cfg[agreement] = getCfg('agreement');
$cfg[policy] = getCfg('policy');
$cfg[privacy_agreement] = getCfg('privacy_agreement');
$cfg[agreement2] = getCfg('agreement2');
$cfg[personal_data_collect_use_choice] = getCfg('personal_data_collect_use_choice');

## 회원가입항목
$cfg[fieldset] = unserialize(getCfg('fieldset'));
if (!$cfg[fieldset]) $cfg[fieldset] = array();

foreach ($cfg[fieldset] as $k=>$v) {
	if ($v[req] == 1) $required[$k] = "required";
	if ($v['use'] == 1) $used[$k] = "used";
}

$checked[ismail][1] = "checked";
$checked[issms][1] = "checked";

$r_years = array();
$toyear = date("Y"); 
$start_year = $toyear - 10; 
$end_year = $toyear - 90; 

for ($i=$start_year; $i >= $end_year; $i--) { 
	$r_years[$i] = $i;
}

//$cfg[ssl_use] = "Y";
if ($cfg[sns_login]) $cfg[sns_login] = unserialize($cfg[sns_login]); //관리자 SNS설정 가져오기 standi

if($cfg[ssl_use] == "Y" /* && $cfg[skin_theme] == "M2" */) {
	// 서비스도메인과 현재 접속한 도메인이 같을경우 ssl은 접속한 도메인으로 변경
	if(trim($cfg[urlService]) && $_SERVER[HTTP_HOST] == $cfg[urlService])
		$ssl_action = "https://".$_SERVER[HTTP_HOST]."/member/indb.php";
	// 서비스도메인과 현재 접속한 도메인이 다른경우 ssl은 ssl설정url로 처리
	else
		$ssl_action = $cfg[ssl_url]."/member/indb.php";
	
   $tpl->assign('ssl_action', $ssl_action);
}

if ($_GET[mobile_type] == "Y") $tpl->define('fm_member',"member/fm.member.htm");
$tpl->assign('mode','register');
$tpl->assign('rurl','../main/index.php');
$tpl->print_('tpl');

?>
