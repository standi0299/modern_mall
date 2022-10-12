<?

/*
* @date : 20180307
* @author : kdk
* @brief : 회원가입항목설정 사용.
* @request : 
* @desc : fieldset 사용 (exm_config)
* @todo :
*/

include "../_header.php";

chkMember();
$m_member = new M_member();
$r_manager = get_manager("y");

## 회원가입항목
$cfg[fieldset] = getCfg("fieldset");
$cfg[fieldset] = unserialize($cfg[fieldset]);

if (is_array($cfg[fieldset])) {
	foreach ($cfg[fieldset] as $k=>$v) {
		if ($v[req] == 1) $required[$k] = "required";
		if ($v['use'] == 1) $used[$k] = "used";
	}
}

$data = $m_member->getInfoWithGrp($cid, $sess[mid]);

$data[mode] = "myinfo";

if ($_GET[mobile_type] != "Y") $data[email] = explode("@", $data[email]);
$data[mobile] = explode("-", $data[mobile]);
$data[phone] = explode("-", $data[phone]);

list($tmp[0], $tmp[1]) = array(substr($data[birth], 0, 2), sprintf("%02d", substr($data[birth], 2)));
$data[birth] = $tmp;

$data[cust_no] = explode("-", $data[cust_no]);
$data[cust_ceo_phone] = explode("-", $data[cust_ceo_phone]);
$data[cust_phone] = explode("-", $data[cust_phone]);
$data[cust_fax] = explode("-", $data[cust_fax]);

$selected[email][$data[email][1]] = "selected";
$selected[mobile][$data[mobile][0]] = "selected";
$selected[phone][$data[phone][0]] = "selected";
$selected[sex][$data[sex]] = "selected";
$selected[calendar][$data[calendar]] = "selected";
$selected[married][$data[married]] = "selected";
$selected[cust_tax_type][$data[cust_tax_type]] = "selected";
$selected[cust_ceo_phone][$data[cust_ceo_phone][0]] = "selected";
$selected[cust_phone][$data[cust_phone][0]] = "selected";
$selected[cust_fax][$data[cust_fax][0]] = "selected";
$selected[manager_no][$data[manager_no]] = "selected";
$selected[birth_year][$data[birth_year]] = "selected";
$selected[birth][0][$data[birth][0]] = "selected";
$selected[birth][1][$data[birth][1]] = "selected";

$checked[ismail][$data[apply_email]] = "checked";
$checked[issms][$data[apply_sms]] = "checked";

$checked[mprivacy][$data[privacy_flag]] = "checked";

$required[password] = "";

$r_years = array();
$toyear = date("Y"); 
$start_year = $toyear - 10; 
$end_year = $toyear - 90; 

for ($i=$start_year; $i >= $end_year; $i--) { 
    $r_years[$i] = $i;
}
//debug($selected);

//$cfg[ssl_use] = "Y";

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
$tpl->assign('mode','myinfo');
$tpl->assign($data);
$tpl->print_('tpl');

?>
