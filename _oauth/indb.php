<?

include "../lib/library.php";
$m_member = new M_member();

if($_POST[mode]) {

	$sns[admin] = "";
	$sns[cid] = $cid;
	$sns[mid] = $_POST[id];
	$sns[super] = "";
	$sns[name] = $_POST[name];
	$sns[grpnm] = "";
	$sns[manager_no] = "";
	$sns[sub_mid] = "";
	$sns[sub_mid_name] = "";
	$sns[sub_mng_flag] = "";
	$sns[pretty_pricedisplay] = "";

	$sns[type] = $_POST[mode];
	$sns[email] = $_POST[email];
	$sns[nickname] = $_POST[nickname];
	if($_POST[picture]){
		$sns[picture] = $_POST[picture];
	}else{
		$sns[picture] = "";
	}

	if($_POST[user_type]){
		$sns[user_type] = $_POST[user_type];
	}else{
		$sns[user_type] = "";
	}

 	//sns 연동 로그인 로그 저장하기 .
	$m_member -> setLogSnsLoginInsert($cid, $_POST[mode], $_POST[id], $_POST[name], $_POST[email], $_POST[nickname], $sns[picture], $sns[user_type]);

   	//$chk_member = $m_member -> getInfo($cid, $_POST[id]);

    //회원 업체 id정보로 검색하여 회원 정보가 있다면 로그인함.
   	$chk_member = $m_member -> getSearchWithSnsId($cid, $_POST[id]);
	//debug($chk_member);

    //회원 이메일주로로 검색하여 회원 정보가 있다면 로그인함. 단, 카카오 , 키즈노트는 이메일정보가 없음.
    if (!$chk_member && $sns[type] != "kakao" && $sns[type] != "kidsnote" && $sns[email] != "") {
        $chk_member = $m_member -> getSearchWithEmail($cid, $_POST[email]);

        //회원 데이터가 있으면 id값 입력.
        if($chk_member) {
            $m_member -> setMemberSnsIdUpdate($cid, $chk_member[mid], $chk_member[email], $_POST[id]);
        }
    }

    //키즈노트의 경우 회원가입이 안된 경우 회원가입 처리
	if (!$chk_member && $sns[type] == "kidsnote"){

	    $addColumn = "set
	        cid = '$cid',
	        mid = 'kn_$sns[mid]',
	        name = '$sns[name]',
	        email = '$sns[email]',
	        regdt = now(),
	        sort = -UNIX_TIMESTAMP(),
	        sns_id = '$sns[mid]',
	        cntlogin = 1,
	        lastlogin = now() 
	    ";
		$m_member->setMemberInfo('register', $addColumn);

		$chk_member = $m_member->getSearchWithSnsId($cid, $_POST[id]);
	}

   	//회원 데이터가 있으면 로그인
   	if($chk_member) {

        $sns[mid] = $chk_member[mid];
        $sns[name] = $chk_member[name];

      	//로그인 처리
      	$_COOKIE[sess] = _member_login($sns);
      	$sess = getAuthMember();

		//로그인 후 횟수,날짜 업데이트.
		$m_member -> setLogSnsLoginUpdate($cid, $_POST[mode], $_POST[id]);

      	//로그인 성공 메인페이지로 이동.
      	echo "login_success";
   	} else {
   		//없으면 서비스/개인정보 약관 동의 페이지 이동
   		//$sns 데이터도 같이 보낸다.

		//로그인 후 횟수,날짜 업데이트.
		$m_member -> setLogSnsLoginUpdate($cid, $_POST[mode], $_POST[id]);

      	//회원 가입을 위한 약관 동의 페이지로 이동.
		echo "login_agree";
	}

	/*
	$query = "
	insert into exm_log_snslogin set
		cid	= '$cid',
		sns_type = '$_POST[mode]',
		sns_id = '$_POST[id]',
		sns_name = '$_POST[name]',
		sns_email = '$_POST[email]',
		sns_nickname = '$_POST[nickname]',
		sns_connect_date = now()
	on duplicate key update
        cid = '$cid',
        sns_id = '$_POST[id]',
	";

	//debug($query);
	$db->query($query);

	if ($db->id)
		echo "success";
	else
		echo "fail";
	*/
}
else {
	echo "fail";
}

exit;
?>
