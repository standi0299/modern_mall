<?

$login_offset = true;
include "../_header.php";

$m_member = new M_member();

if ($cfg[skin_theme] == "P1") {
	if ($_GET[reminderpw_type] != "email") $_GET[reminderpw_type] = "mobile";
}

if ($_POST[_sslData]) {
	$ssl = 1;
	$_POST = dec($_POST[_sslData]);
}

if ($_POST[mode] == "reminderpw") {
	if ($_POST[mid_en]) $_POST[mid] = base64_decode($_POST[mid_en]);
	if ($_POST[email_en]) $_POST[email] = base64_decode($_POST[email_en]);
	
	if ($_POST[mobile_type] != "Y" && $cfg[skin_theme] == "P1") {
		if ($_POST[reminderpw_type] == "mobile") {
			$_POST[mobile] = @implode("-", array_notnull($_POST[mobile]));
			$addWhere = "where mid='$_POST[mid]' and cid='$cid' and mobile='$_POST[mobile]' limit 1";
		} else if ($_POST[reminderpw_type] == "email") {
			$_POST[email] = @implode("@", array_notnull($_POST[email]));
			$addWhere = "where mid='$_POST[mid]' and cid='$cid' and email='$_POST[email]' limit 1";
		}
	} else {
		$addWhere = "where mid='$_POST[mid]' and cid='$cid' and email='$_POST[email]' limit 1";
	}
	
	$data = $m_member->getChkMemberInfo($cid, "exm_member", $addWhere);
	
    if (!$data[mid]) {
      	if ($_POST[mobile_type] == "Y") echo _("일치하는 회원정보가 없습니다.");
	    else if ($cfg[skin_theme] == "P1") msgNlocationReplace(_("일치하는 회원정보가 없습니다."), "reminderpw.php?reminderpw_type=".$_POST[reminderpw_type]);
		else msgNlocationReplace(_("일치하는 회원정보가 없습니다."), "reminderpw.php");
        exit;
    }

    $data[password] = substr(md5(crypt(null)), 0, 6);
    $password = passwordCommonEncode($data[password]);
	$addColumn = "set password = '$password'";
	$addWhere2 = "where cid = '$cid' and mid = '$data[mid]'";
	$m_member->setMemberInfo("myinfo", $addColumn, $addWhere2);
	
    /* 메일로직 */
    $data[nameSite] = $cfg[nameSite]; //사이트명 추가. / 20181128 / kdk
    autoMail("reminderpw", $data[email], $data);
    autoSms(_("비밀번호찾기"), $data[mobile], $data);
	
	if ($_POST[mobile_type] == "Y") {
		echo _("가입하신 이메일 주소로 임시비밀번호를")."<br />"._("전송하였습니다.")."<br />"._("로그인 후 비밀번호를 변경해 주세요.");
		exit;
	}
	
	//echo "<script>alert('임시 비밀번호를 메일로 전송하였습니다.\\r\\n메일 : $_POST[email]')</script>";
   //exit;
}

if ($sess[mid]) {
	go("../main/index.php");
	exit;
}

$tpl->print_('tpl');

?>