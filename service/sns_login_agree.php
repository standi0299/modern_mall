<?

/*
* @date : 20180321
* @author : kdk
* @brief : SNS회원가입항목설정 수정.
* @request : 
* @desc : 이름 사용자 입력.
* @todo :
*/

$login_offset = true;
include "../_header.php";

$data = json_decode(base64_decode($_GET[data]),1);

if($data) {
    $data[id] = $data[mid];
    $data[mid] = substr($data[type], 0, 1) ."_". $data[mid];
    
    if ($data[email]) $data[email] = explode("@", $data[email]);
}

if (in_array($cfg[skin], $r_printhome_skin)) {
  	//이용약관 : agreement
  	//개인정보수집동의 : policy1
  	//개인정보활용동의 : policy2
  	//개인정보위탁동의 : policy3
  	//개인정보마케팅활용동의 : policy4
  	//개인정보 수집/활용/위탁 동의 : policy5
  	//개인정보마케팅활용동의 사용여부: policy4_flag
  		
    $cfg[agreement] = getCfg('agreement');
    //$cfg[policy1] = getCfg('policy1');
    //$cfg[policy2] = getCfg('policy2');
    //$cfg[policy3] = getCfg('policy3');
    $cfg[policy4] = getCfg('policy4');	 
	$cfg[policy5] = getCfg('policy5');	
	$cfg[policy4_flag] = getCfg('policy4_flag');
}
else {
	$cfg[agreement] = getCfg('agreement');
	$cfg[policy] = getCfg('policy');
	//debug($cfg[agreement]);
	//debug($cfg[policy]);	
	$cfg[privacy_agreement] = getCfg('privacy_agreement');
}

/*$tpl->define(array(
   'header' => 'layout/layout.htm?header',
   'footer' => 'layout/layout.htm?footer',
));*/

$tpl -> assign('data', $data);
$tpl -> print_('tpl');
?>