<?

$login_offset = true;
include "../_header.php";

$m_member = new M_member();

if ($_POST[_sslData]) {
	$ssl = 1;
	$_POST = dec($_POST[_sslData]);
}

if ($_POST[mode] == "reminderid") {
	if ($_POST[email_en]) $_POST[email] = base64_decode($_POST[email_en]);
	
	$addWhere = "where cid = '$cid' and name='$_POST[name]' and email='$_POST[email]' limit 1";
	$data = $m_member->getChkMemberInfo($cid, "exm_member", $addWhere);

	if (!$data[mid]) {
		if ($_POST[mobile_type] == "Y") echo _("일치하는 회원정보가 없습니다.");
		else msgNlocationReplace(_("일치하는 회원정보가 없습니다."), "reminderidpw.php");
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

if ($_POST[mode] == "reminderpw") {
   if ($_POST[mid_en]) $_POST[mid] = base64_decode($_POST[mid_en]);
   if ($_POST[email_en]) $_POST[email] = base64_decode($_POST[email_en]);
   
   $addWhere = "where mid='$_POST[mid]' and cid='$cid' and email='$_POST[email]' limit 1";
   $data = $m_member->getChkMemberInfo($cid, "exm_member", $addWhere);
   
   if (!$data[mid]) {
      if ($_POST[mobile_type] == "Y") echo _("일치하는 회원정보가 없습니다.");
      else msgNlocationReplace(_("일치하는 회원정보가 없습니다."), "reminderidpw.php");
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
   kakao_alimtalk_send($data[mobile],$data[mid],_("비밀번호찾기"), $data);
   
   if ($_POST[mobile_type] == "Y") {
      echo _("가입하신 이메일 주소로 임시비밀번호를")."<br />"._("전송하였습니다.")."<br />"._("로그인 후 비밀번호를 변경해 주세요.");
      exit;
   }
   
   //echo "<script>alert('임시 비밀번호를 메일로 전송하였습니다.\\r\\n메일 : $_POST[email]')</script>";
   //exit;
}

$tpl->print_('tpl');

?>