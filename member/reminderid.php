<?

$login_offset = true;
include "../_header.php";

$m_member = new M_member();

if ($cfg[skin_theme] == "P1") {
	if ($_GET[reminderid_type] != "email") $_GET[reminderid_type] = "mobile";
}

if ($_POST[_sslData]) {
	$ssl = 1;
	$_POST = dec($_POST[_sslData]);
}

if ($_POST[mode] == "reminderid") {
	if ($_POST[email_en]) $_POST[email] = base64_decode($_POST[email_en]);
	
	if ($_POST[mobile_type] != "Y" && $cfg[skin_theme] == "P1") {
		if ($_POST[reminderid_type] == "mobile") {
			$_POST[mobile] = @implode("-", array_notnull($_POST[mobile]));
			$addWhere = "where cid = '$cid' and name='$_POST[name]' and mobile='$_POST[mobile]' limit 1";
		} else if ($_POST[reminderid_type] == "email") {
			$_POST[email] = @implode("@", array_notnull($_POST[email]));
			$addWhere = "where cid = '$cid' and name='$_POST[name]' and email='$_POST[email]' limit 1";
		}
	} else {
		$addWhere = "where cid = '$cid' and name='$_POST[name]' and email='$_POST[email]' limit 1";
	}
	
	$data = $m_member->getChkMemberInfo($cid, "exm_member", $addWhere);

	if (!$data[mid]) {
		if ($_POST[mobile_type] == "Y") echo _("일치하는 회원정보가 없습니다.");
		else if ($cfg[skin_theme] == "P1") msgNlocationReplace(_("일치하는 회원정보가 없습니다."), "reminderid.php?reminderid_type=".$_POST[reminderid_type]);
		else msgNlocationReplace(_("일치하는 회원정보가 없습니다."), "reminderid.php");
		exit;
	}
	
	if ($_POST[mobile_type] == "Y") {
		echo _("가입하신 아이디는")." ".substr($data[mid], 0, -3)."*** "._("입니다.");
		exit;
	}
	
	/* 메일로직 */
	//echo "<script>alert('가입하신 아이디를 메일로 전송하였습니다.\\r\\n메일 : $_POST[email]')</script>";
	//echo "<script>window.close();</script>";
}

if ($sess[mid]) {
	go("../main/index.php");
	exit;
}

$tpl->print_('tpl');

?>