<?

include "../lib/library.php";

$m_config = new M_config();
$m_member = new M_member();
$m_order = new M_order();
$m_emoney = new M_emoney();

if ($_POST[_sslData]) {
	$ssl = 1;
	$_POST = dec($_POST[_sslData]);
}

switch ($_POST[mode]) {
	case "chkMid":
		if ($_POST[mid_en]) $_POST[mid] = base64_decode($_POST[mid_en]);
		
		$_POST[mid] = trim($_POST[mid]);

		$chk = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and mid='$_POST[mid]'");
		$chk2 = $m_member->getChkMemberInfo($cid, "exm_member_out", "where cid='$cid' and mid='$_POST[mid]'");
		$unableid = $m_config->getConfigInfo($cid, "unableid");

		$unableid[value] = explode(",", $unableid[value]);
		$data = $m_member->getAdminList($cid, "");

		foreach ($data as $k=>$v) {
			$unableid[value][] = $v[mid];
		}
	
		if ($chk[mid]) {
			echo "duplicate";
			exit;
		}
		if ($chk2[mid]) {
			echo "out";
			exit;
		}
		if (in_array($_POST[mid], $unableid[value])) {
			 echo "unable";
			 exit;
		}

		echo "good";

	exit; break;

	case "chkResno":
		$_POST[resno] = trim($_POST[resno]);

		$chk = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and resno='$_POST[resno]'");

		if ($chk[mid]) {
			echo "Not"; 
			exit;
		}

	exit; break;
	
	case "chkPassword":

		if ($_POST[mid_en]) $_POST[mid] = base64_decode($_POST[mid_en]);
		if ($_POST[password_en]) $_POST[password] = base64_decode($_POST[password_en]);
		
		$_POST[mid] = trim($_POST[mid]);
		$_POST[password] = trim($_POST[password]);
      
	  //debug($_POST[mid]);
      $password = passwordCommonEncode($_POST[password]);
		
		$chk = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and mid='$_POST[mid]' and password='$password'");
		
		if ($chk[mid]) {
			echo "Ok";
			exit;
		}

	exit; break;
	
	case "chkEmail":
		if ($_POST[mid_en]) $_POST[mid] = base64_decode($_POST[mid_en]);
		if ($_POST[email_en]) $_POST[email] = base64_decode($_POST[email_en]);
		
		$_POST[mid] = trim($_POST[mid]);
		$_POST[email] = trim($_POST[email]);
		
		if ($_POST[mid]) $chk = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and mid!='$_POST[mid]' and email='$_POST[email]'");
		else $chk = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and email='$_POST[email]'");
		
		if ($chk[mid]) echo "duplicate";
		else echo "good";
		
	exit; break;
		
	case "chkMobile":
		if ($_POST[mid_en]) $_POST[mid] = base64_decode($_POST[mid_en]);
		
		$_POST[mid] = trim($_POST[mid]);
		$_POST[mobile] = trim($_POST[mobile]);
		
		if ($_POST[mid]) $chk = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and mid!='$_POST[mid]' and mobile='$_POST[mobile]'");
		else $chk = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and mobile='$_POST[mobile]'");
		
		if ($chk[mid]) echo "duplicate";
		else echo "good";
		
	exit; break;
	
	//sms 인증 발송 처리				20160602			chunter
	case "register_sms_send":
		if ($cfg[register_sms_auth]) {
			$authCode = sprintf('%05d', mt_rand(0, 99999));			//인증코드 5자리
			
			$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
			//setcookie('register_sms_auth', '', time()-3600, '/');			//쿠키 지우고 새로 셋팅
			//setcookie("register_sms_auth", $authCode, time() + 60 * 30, '/');  /* expire in 30 Min*/
			
			if (headers_sent()) {
			} else {
				setCookie("register_sms_auth", $authCode, 0, "/");
				//setCookie('domain_cidss', $cid, time() + 3600 * 24, '/', $_SERVER[SERVER_NAME]);
				
				if (!$cfg[register_sms_auth_msg]) {
					$smsMsg = _("인증번호")." [". $authCode. "] "._("입니다.");
				} else { 
					$smsMsg = str_replace("{"._("인증번호")."}", $authCode, $cfg[register_sms_auth_msg]);
				}
				
				sendSms($_POST[mobile_number], $smsMsg);
			}
		}
		
	exit; break;
	
	case "register":

      if($_POST[mid_en] && $_POST[password_en] && $_POST[password2_en]){
      	 $_POST[mid] = base64_decode($_POST[mid_en]);
      	 $_POST[password] = base64_decode($_POST[password_en]);
      	 $_POST[password2] = base64_decode($_POST[password2_en]);
	  }
	  
	  if ($_POST[email_en]) {
	  	 $_POST[email] = base64_decode($_POST[email_en]);
	  }

      $burl = ($ssl) ? -2 : -1;
	
		if (!$_POST[mid]) {
			msg(_("접근이 잘못되었습니다."), $burl);
			exit;
		}
		
		if ($_POST[mobile_type] != "Y") {
			include '../lib/zmSpamFree/zmSpamFree.php';
			if (!$rslt && $cfg[skin_theme] != "M2" && $cfg[skin_theme] != "M3") {
				msg(_("보안코드를 입력해주세요."), -1); 
				exit;
			}
		
			//sms 인증번호 비교하기.			20160602			chunter
			if ($cfg[register_sms_auth]) {
				if (!$_COOKIE["register_sms_auth"]) {
					msg(_("인증코드 시간이 만료되었습니다."), $burl);
				} else if ($_POST[sms_code] != $_COOKIE["register_sms_auth"]) {
					msg(_("인증코드가 일치하지 않습니다."), $burl);
				}
			}
		}

		if (is_array($_POST[resno])) $_POST[resno]	= implode("", array_notnull($_POST[resno]));

		$chk = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and mid='$_POST[mid]'");
		$chk2 = $m_member->getChkMemberInfo($cid, "exm_member_out", "where cid='$cid' and mid='$_POST[mid]'");
		$resno = $m_member->getChkMemberInfo($cid, "exm_member", "where cid='$cid' and resno='$_POST[resno]'");
		$base = $m_member->getChkMemberInfo($cid, "exm_member_grp", "where cid='$cid' and base=1");
		$state = $m_config->getConfigInfo($cid, "basestate");
		//$emoney = $m_config->getConfigInfo($cid, "baseemoney");
		$referer = $m_member->getChkMemberInfo($cid, "exm_counter_ip", "where ip='$_SERVER[REMOTE_ADDR]' and day=curdate()+0");

		if ($chk[mid] || $chk2[mid]) {
			msg(_("이미 등록된 아이디입니다."), $burl);
			exit;
		}

		if ($resno[resno]) {
			msg(_("이미 등록된 주민등록번호입니다."), $burl);
			exit;
		}

      if($_POST[grpno]) $base[grpno] = $_POST[grpno];

		$qr = "grpno = '$base[grpno]',";
		$qr .= "resno = '$_POST[resno]',";
		$qr .= "regdt = now(), sort = -UNIX_TIMESTAMP(), referer = '$referer[referer]',";


	case "myinfo":

	  if($_POST[mode] == "myinfo" && ($_POST[mid_en] || $_POST[password_en] || $_POST[password2_en] || $_POST[email_en])){
	  	 if($_POST[mid_en])
	  	 	$_POST[mid] = base64_decode($_POST[mid_en]);
	  	 	
	  	 if($_POST[password_en])
	  	 	$_POST[password] = base64_decode($_POST[password_en]);
	  	 	
	  	 if($_POST[password2_en])
	  	 	$_POST[password2] = base64_decode($_POST[password2_en]);
	  	 	
	  	 if($_POST[email_en])
	  	 	$_POST[email] = base64_decode($_POST[email_en]);
	  }

      $password = passwordCommonEncode($_POST[password]);

		$burl = ($ssl) ? -2 : -1;
		
		if ($ssl) {
			$_POST[rurl] = "../member/myinfo.php";
		}
	
		if (is_array($_POST[birth])) {
			foreach ($_POST[birth] as $k=>$v) {
				if (strlen($v) == 1) {
					$_POST[birth][$k] = "0".$v;
				}
			}
		}
		
		if ($_POST[mobile_type] != "Y" && $cfg[skin_theme] != "M2" && $cfg[skin_theme] != "M3") $_POST[email] = @implode("@", array_notnull($_POST[email]));
      	$_POST[mobile] = @implode("-", array_notnull($_POST[mobile]));
		$_POST[birth] = @implode("", array_notnull($_POST[birth]));
		$_POST[phone] = @implode("-", array_notnull($_POST[phone]));
		$_POST[cust_no]	= @implode("-", array_notnull($_POST[cust_no]));
		$_POST[cust_ceo_phone] = @implode("-", array_notnull($_POST[cust_ceo_phone]));
		$_POST[cust_phone] = @implode("-", array_notnull($_POST[cust_phone]));
		$_POST[cust_fax] = @implode("-", array_notnull($_POST[cust_fax]));

		if (!$_POST[ismail]) $_POST[ismail] = 0;
		if (!$_POST[issms]) $_POST[issms] = 0;
	
		if ($_POST[password]) $qr2 = "password = '$password',";
      
      $personal_data_collect_use_choice = 'N';
      if($_REQUEST[personal_data_collect_use_choice]) $personal_data_collect_use_choice = 'Y';

		//$qr2 .= "manager_no = '$_POST[manager_no]',";
		//개인정보 마케팅 활용에 동의 / 17.08.24 / kdk
		$qr2 .= ($_POST[mprivacy] == "on") ? "privacy_flag = 1," : "privacy_flag = 0,";

		$flds = "
		$qr
		$qr2
		name             = '$_POST[name]',
		email		     = '$_POST[email]',
		apply_email		 = '$_POST[ismail]',
		mobile			 = '$_POST[mobile]',
		apply_sms		 = '$_POST[issms]',
		sex			     = '$_POST[sex]',
		calendar		 = '$_POST[calendar]',
		birth_year		 = '$_POST[birth_year]',
		birth			 = '$_POST[birth]',
		zipcode			 = '$_POST[zipcode]',
		address			 = '$_POST[address]',
		address_sub		 = '$_POST[address_sub]',
		phone			 = '$_POST[phone]',
		married			 = '$_POST[married]',
		state			 = '$state[value]',
		cust_name		 = '$_POST[cust_name]',
		cust_type		 = '$_POST[cust_type]',
		cust_class		 = '$_POST[cust_class]',
		cust_tax_type	 = '$_POST[cust_tax_type]',
		cust_no			 = '$_POST[cust_no]',
		cust_ceo		 = '$_POST[cust_ceo]',
		cust_ceo_phone	 = '$_POST[cust_ceo_phone]',
		cust_zipcode	 = '$_POST[cust_zipcode]',
		cust_address	 = '$_POST[cust_address]',
		cust_address_sub = '$_POST[cust_address_sub]',
		cust_address_en  = '$_POST[cust_address_en]',
		cust_phone		 = '$_POST[cust_phone]',
		cust_fax         = '$_POST[cust_fax]',
		cust_bank_name	 = '$_POST[cust_bank_name]',
		cust_bank_no	 = '$_POST[cust_bank_no]',
		cust_bank_owner  = '$_POST[cust_bank_owner]',
		personal_data_collect_use_choice_flag = '$personal_data_collect_use_choice'";

		if ($_POST[mode] == "register") {
			$addColumn = "set $flds, cid = '$cid', mid = '$_POST[mid]'";
			$addWhere = "";
		} else {
			$addColumn = "set $flds";
			$addWhere = "where cid = '$cid' and mid = '$_POST[mid]'";
		}

		$m_member->setMemberInfo($_POST[mode], $addColumn, $addWhere);
	
		if ($_POST[mode] == "register") {
			$data = $m_member->getInfoWithGrp($cid, $_POST[mid]);
			
			//적립금 지급처리.
			setAddNewMember($cid, $_POST[mid]);
            memberRegistCouponSend($_POST[mid]);		//회원가입 쿠폰 처리.		20180323		chunter

			//_member_login($data);
			$data[nameSite] = $cfg[nameSite]; //사이트명 추가. / 20181128 / kdk
			
			autoMail("register", $data[email], $data);

			//관리자에게 보내기
			autoMailAdmin("admin_register", $cfg[email1], $data);
			autoSms(_("회원가입"), $data[mobile], $data);

			kakao_alimtalk_send($data[mobile],$_POST[mid],_("회원가입"));

			if ($_POST[mobile_type] == "Y") {
				exit;
			} else {

				if ($cfg[member_system]['login_system'] != "out_login_param") {
					//회원가입후 자동 로그인
					if($cfg[ssl_use] == "Y" /* && $cfg[skin_theme] == "M2" */) {
						if(trim($cfg[urlService]) && $_SERVER[HTTP_HOST] == $cfg[urlService])
							$action = "https://".$_SERVER[HTTP_HOST]."/member/indb.php";
						else
							$action = $cfg[ssl_url]."/member/indb.php";
					}

					echo "
					<form name='fm' method='post' action='{$action}'>
						<input type='hidden' name='mode' value='login'>
						<input type='hidden' name='rurl' value='/'>
						<input type='hidden' name='mid_en' value='{$_POST[mid_en]}'>
						<input type='hidden' name='password_en' value='{$_POST[password_en]}'>
					</form>";
					
					// 몰 설정에서 회원가입 완료 스크립트가 있을경우 수행
					if(trim($cfg[register_ok_script])) echo $cfg[register_ok_script];
					if(trim($cfg[bottom_script])) echo $cfg[bottom_script];		

					echo "<script>alert('회원가입이 완료되었습니다.');document.fm.submit();</script>";
					exit;
				} else {
					if($cfg[ssl_use] == "Y" /* && $cfg[skin_theme] == "M2" */) {
						// 서비스도메인과 현재 접속한 도메인이 같을경우 ssl은 접속한 도메인으로 변경
						if(trim($cfg[urlService]) && $_SERVER[HTTP_HOST] == $cfg[urlService])
							$_POST[rurl] = "http://".$_SERVER[SERVER_NAME]."/member/register_ok.php?mid=".$_POST[mid];
						// 서비스도메인과 현재 접속한 도메인이 다른경우 ssl은 ssl설정url로 처리
						else
							$_POST[rurl] = $cfg[ssl_url]."/member/register_ok.php?mid=".$_POST[mid];
					}else{
						$_POST[rurl] = "register_ok.php?mid=".$_POST[mid];
					}
				}
         			// 몰 설정에서 회원가입 완료 스크립트가 있을경우 수행
					if(trim($cfg[register_ok_script])) echo $cfg[register_ok_script];
					if(trim($cfg[bottom_script])) echo $cfg[bottom_script];		
				$r_msg = _("회원가입이 완료되었습니다.");
			}
		} else {
	        //회원 정보 변경후 회원 sess 정보 update   20140523    chunter
	        $sess = md5_decode($_COOKIE[sess]);
	        $sess[name] = $_POST[name];
	        $sess = md5_encode($sess);
	        setCookie('sess', $sess, 0, '/');

			if ($_POST[mobile_type] == "Y") {
				exit;
			} else {
				$_POST[rurl] = $_SERVER[HTTP_REFERER];
				if($_POST[rurl]) $_POST[rurl] = str_replace("https://", "http://", $_POST[rurl]);
				if($_POST[rurl]) $_POST[rurl] = str_replace("https://", "http://", $_POST[rurl]);
				$r_msg = _("회원정보가 수정되었습니다.");
			}
		}
		
		msg($r_msg, $_POST[rurl]);
	  
	exit; break;
	
	case "login":
   	Header("Cache-Control: no-store, no-cache, must-revalidate");
   	Header("Pragma: no-cache");
      
      if ($_POST[mid_en] && $_POST[password_en]) {
         $_POST[mid] = base64_decode($_POST[mid_en]);
         $_POST[password] = base64_decode($_POST[password_en]);
      }

	  $chk_rest = $m_member->getInfo($cid, $_POST[mid]);

      $password =  passwordCommonEncode($_POST[password]);
		
		//회원이관시 회원이 처음 로그인했을 경우 비밀번호찾기 페이지로 이동
		if ($chk_rest[mid] && !$chk_rest[password] && $chk_rest[cntlogin] == 0 && ($cid == "mmdev" || $cid == "pixstory")) {
			go("reminderpw.php");
			exit;
		}

		if(($_POST[mid] == $chk_rest[mid]) && ($password == $chk_rest[password]) && $chk_rest[rest_flag] == '1') {
			msg_confirm(_("휴면 계정으로 전환된 회원이십니다.")."\n"._("휴면 계정을 해지 하시겠습니까?"), "location.href='quiescence_account.php?mid=$_POST[mid]'", "history.go(-1)");
			exit;
		}
		
		$burl = ($ssl) ? -2:-1;
		switch ($cfg[member_system]['login_system']) {
			case "out_login_cookie":
	
				if (!$_POST[rurl]) $_POST[rurl] = "http://".$_SERVER[HTTP_HOST]."/main/index.php";
				
				if (!is_numeric(strrpos($_POST[rurl], "http://"))) {
					$_POST[rurl] = str_replace("../", "/", $_POST[rurl]);
					$_POST[rurl] = "http://".$_SERVER[HTTP_HOST].$_POST[rurl];
				}
				
				$url = $cfg[member_system][out_login_cookie][url][login];
				
				$data['cid'] = $cid;
				$data['mid'] = $_POST['mid'];
				
				//_member_login($data);
				
				//$url = parse_url($url);
				//$url[query] = explode("&",$url[query]);
				//$url[query][] = $cfg[member_system][out_login_cookie][val][mid]."=".urlencode($_POST[mid]);
				//$url[query][] = $cfg[member_system][out_login_cookie][val][password]."=".urlencode($_POST[password]);
				//$url[query][] = $cfg[member_system][out_login_cookie][val][return_url]."=".urlencode($_POST[rurl]);
				//$url[query] = implode("&",$url[query]);
				//$gourl = $url[scheme]."://".$url[host].$url[path]."?".$url[query];

				echo "
				<form name='fm' method='post' action='$url'>
				    <input type='hidden' name='{$cfg[member_system][out_login_cookie][val][mid]}' value='$_POST[mid]'/>
					<input type='hidden' name='{$cfg[member_system][out_login_cookie][val][password]}' value='$_POST[password]'/>
					<input type='hidden' name='{$cfg[member_system][out_login_cookie][val][return_url]}' value='$_POST[rurl]'/>
				</form>
				<script>document.fm.submit();</script>";
				
			exit; break;
			
			case "out_login":
				$url = $cfg[member_system][out_login][url][login];
				$url = parse_url($url);
				$url[query] = explode("&", $url[query]);
				$url[query][] = $cfg[member_system][out_login][val][mid]."=".urlencode($_POST[mid]);
				$url[query][] = $cfg[member_system][out_login][val][password]."=".urlencode($_POST[password]);
				$url[query] = implode("&", $url[query]);
				$gourl = $url[scheme]."://".$url[host].$url[path]."?".$url[query];
				
				$ret = readurl($cfg[member_system][out_login][url][login]);
				$ret = json_decode($ret, 1);
				
				if ($ret[success] == "F") {
					msg($ret[msg], $burl);
				} else if ($ret[success] == "T") {
					$m_member->setBtoBMemberInsert($cid, $ret[mid], $ret[bid], $ret[name], $ret[grpno]);
					$data = $m_member->getInfo($cid, $ret[mid]);
				} else {
					msg(_("통신상의 장애가 있습니다.")."\n"._("관리자에게 문의 해주세요."), $burl);
				}
				
			break;
			
			default:
            
            $data = memberLoginProc($_POST[mid], $_POST[password], $_POST[mobile_type], $burl, 'Y', "N");          //lib_util_lib_db_use.php
            		
         break;
		}

		//로그아웃을 위해 기존 cartkey 를 저장			20160829		chunter
		if ($_POST[logining_flag] == "Y") {
			setCookie("login_connect_cartkey", $_COOKIE[cartkey], 0, "/");
		}
		
		if($cfg[member_system]['login_system']!="out_login_cookie")	_member_login($data);  //2019.11.28 조건 추가
		
		//로그인 유지 기능 처리			20160824		chunter
		if ($_POST[logining_flag] == "Y") {
			$login_connect = array("member_id" => $_POST[mid], "is_mobile" => $_POST[mobile_type]);
			$login_connect_data = md5_encode($login_connect);
			setCookie("login_connect_member_data", $login_connect_data, time()+3600*24*30, "/");			//로그인 유지를 위해 아이디를 쿠키어 넣어 30일 보관			20160824		chunter
		} else {
			setCookie("login_connect_member_data", "", 0, "/");
		}

		//아이디 저장 처리  20200117 kkwon
		if($_POST[save_id] == "Y"){
			$save_id_connect = array("member_id" => $_POST[mid],"save_id" => "Y");
			$save_id_connect_data = md5_encode($save_id_connect);
			setCookie("save_id_connect_member_data", $save_id_connect_data, time()+3600*24*30, "/");
		}else{
			setCookie("save_id_connect_member_data", "", 0, "/");
		}	
		
		//적립금 만료 처리	20191209	kkwon
		$emoney_use_flag = getCfg("emoney_use_flag");
		if($emoney_use_flag=="Y"){  //적립금 사용 몰
			$m_emoney->setEmoneyUpdateExpire($cid, $_POST[mid]); //만료 처리
			$m_emoney->setEmoneyUpdate($cid, $_POST[mid]); //적립금 회원 처리
		}
		
		### 로그인정보 업데이트
		$m_member->setLoginDataUpdate($cid, $data[mid]);
        if($_POST[rurl]) $_POST[rurl] = str_replace("https://", "http://", $_POST[rurl]);
		if (!$_POST[rurl]) $_POST[rurl] = $_SERVER[HTTP_REFERER];
		if (!$_POST[rurl]) $_POST[rurl] = "/";
        if ($_POST[mobile_type] == "Y") { echo $_POST[rurl]; exit; }
        
        if ($_POST[location_flag] == "Y") { echo("OK"); exit; } //Pixstory cart.php에서 레이어 로그인창에서 호출 시 location 안함. / 20190220 / kdk.

	break;
		
	case "guest":
		$payno = $m_order->getNomemberOrder($cid, $_POST[payno], $_POST[email]);
		
		if ($payno) {
			go("../mypage/orderview.php?guest=1&payno=$payno");
		} else {
			msg(_("정보가 일치하지 않습니다."), -1);
			exit;
		}
		
	break;
	
	//비회원 견적의뢰 주문조회 리스트페이지를 조회하는 기능 추가     //20150702  kdk	
	case "guest_extra_cart":
		//storageid
		$data = $m_order->getNomemberExtraOrder($cid, $_POST[order_name], $_POST[order_mobile]);
		
		foreach ($data as $k=>$v) {
			$cartno[] = $v[cartno];
		}
		
		$cartno = implode(",", $cartno);
		
		if ($cartno) {
			go("../order/extra_cart_list.php?guest=1&cartno=$cartno");
		} else {
			msg(_("정보가 일치하지 않습니다."), -1);
			exit;
		}
		
	break;
	
	//휴면계정 복구 인증 메일 보내기
	case "restore_num_send":
		$restore_num = rand(000000, 999999);
		
		$m_member->setMemberRestoreCode($cid, $_POST[mid], $restore_num);
		
		$data[mid] = $_POST[mid];
		$data[nameSite] = $cfg[nameSite];
		$data[restore_name] = $restore_num;
		
		//$data에 아이디, 인증번호등을 담아서 넘긴다.
		autoMail("restore_number", $_POST[email], $data);
		exit;
		//$mail->send($headers, $_POST[contents]);
		
	break;
	
	//휴면계정 복구 인증 번호 확인
	case "chk_restore_num":
		$restore_num = $m_member->getMemberRestoreCode($cid, $_POST[mid]);
		
		if ($restore_num == $_POST[restore_num]) echo "ok";
		else echo "no";
		
	exit; break;
	
	//휴면 계정 복구 - 비밀번호 변경	
	case "pw_resetting":
		include_once "../lib/class.db.mysqli.php";
		
		//이관한 DB 연결
		$db_rest = new DBMysqli(dirname(__FILE__)."/../conf/conf.db.security.php");
		
		$data = $db_rest->listArray("select * from exm_member where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'");
		foreach ($data as $k=>$v) {
			$batchQuery[] = "update exm_member set
						name        = '$v[name]',
						birth_year  = '$v[birth_year]',
						birth       = '$v[birth]',
						email       = '$v[email]',
						zipcode     = '$v[zipcode]',
						address     = '$v[address]',
						address_sub = '$v[address_sub]',
						phone       = '$v[phone]',
						mobile      = '$v[mobile]',
						rest_flag   = '0'
					where cid = '$cid' and mid = '$_POST[mid]'";
		}
		
		$data = $db_rest->listArray("select * from exm_pay where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'");
		foreach ($data as $k=>$v) {
			$batchQuery[] = "update exm_pay set
						orderer_name      = '$v[orderer_name]',
						orderer_email     = '$v[orderer_email]',
						orderer_phone     = '$v[orderer_phone]',
						orderer_mobile    = '$v[orderer_mobile]',
						orderer_zipcode   = '$v[orderer_zipcode]',
						orderer_addr      = '$v[orderer_addr]',
						orderer_addr_sub  = '$v[orderer_addr_sub]',
						payer_name        = '$v[payer_name]',
						receiver_name     = '$v[receiver_name]',
						receiver_phone    = '$v[receiver_phone]',
						receiver_mobile   = '$v[receiver_mobile]',
						receiver_zipcode  = '$v[receiver_zipcode]',
						receiver_addr     = '$v[receiver_addr]',
						receiver_addr_sub = '$v[receiver_addr_sub]',
						pgcode            = '$v[pgcode]',
						bankinfo          = '$v[bankinfo]'
					where cid = '$cid' and mid = '$_POST[mid]' and payno = '$v[payno]'";
		}
		
		$data = $db_rest->listArray("select * from exm_del_pay where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'");
		foreach ($data as $k=>$v) {
			$batchQuery[] = "update exm_del_pay set
						orderer_name      = '$v[orderer_name]',
						orderer_email     = '$v[orderer_email]',
						orderer_phone     = '$v[orderer_phone]',
						orderer_mobile    = '$v[orderer_mobile]',
						orderer_zipcode   = '$v[orderer_zipcode]',
						orderer_addr      = '$v[orderer_addr]',
						orderer_addr_sub  = '$v[orderer_addr_sub]',
						payer_name        = '$v[payer_name]',
						receiver_name     = '$v[receiver_name]',
						receiver_phone    = '$v[receiver_phone]',
						receiver_mobile   = '$v[receiver_mobile]',
						receiver_zipcode  = '$v[receiver_zipcode]',
						receiver_addr     = '$v[receiver_addr]',
						receiver_addr_sub = '$v[receiver_addr_sub]',
						pgcode            = '$v[pgcode]',
						bankinfo          = '$v[bankinfo]'
					where cid = '$cid' and mid = '$_POST[mid]'";
		}
		
		$data = $db_rest->listArray("select * from exm_ord_upload where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'");
		foreach ($data as $k=>$v) {
			$batchQuery[] = "update exm_ord_upload set
						upload_order_code         = '$v[upload_order_code]',
						upload_order_product_code = '$v[upload_order_product_code]',
						upload_order_option_desc  = '$v[upload_order_option_desc]'
					where cid = '$cid' and mid = '$_POST[mid]'";
		}
		
		$data = $db_rest->listArray("select * from tb_extra_cart where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'");
		foreach ($data as $k=>$v) {
			$batchQuery[] = "update tb_extra_cart set
						upload_code      = '$v[upload_code]',
						take_user_name   = '$v[take_user_name]',
						take_user_phone  = '$v[take_user_phone]',
						take_user_mobile = '$v[take_user_mobile]',
						take_email       = '$v[take_email]',
						delivery_address = '$v[delivery_address]'
					where cid = '$cid' and mid = '$_POST[mid]'";
		}
		
		$data = $db_rest->listArray("select * from exm_log_email where cid = '$cid' and mid = '$v[mid]' and center_cid = '$cfg_center[center_cid]'");
		foreach ($data as $k=>$v) {
			$batchQuery[] = "update exm_log_email set
						`to` = '$v[to]'
					where cid = '$cid' and no = '$v[no]'";
		}

		$data = $db_rest->listArray("select * from exm_log_sms where cid = '$cid' and mid = '$v[mid]' and center_cid = '$cfg_center[center_cid]'");
		foreach ($data as $k=>$v) {
			$batchQuery[] = "update exm_log_sms set
						number = '$v[number]'
					where cid = '$cid' and no = '$v[no]'";
		}
		
		//복원할 데이터를 한번에 update 처리
		//conf파일은 따로 만들어서 배포 해야 함
		$Mysqli = new DBMysqli(dirname(__FILE__)."/../conf/conf.db.php");
		
		$queryAll = implode(";", $batchQuery).";";
		$Mysqli->multiQuery($queryAll);
		
		//이관 DB 데이터 삭제
		$batchQuery_del[] = "delete from exm_member where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'";
		$batchQuery_del[] = "delete from exm_pay where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'";
		$batchQuery_del[] = "delete from exm_del_pay where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'";
		$batchQuery_del[] = "delete from exm_ord_upload where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'";
		$batchQuery_del[] = "delete from tb_extra_cart where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'";
		$batchQuery_del[] = "delete from exm_log_email where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'";
		$batchQuery_del[] = "delete from exm_log_sms where cid = '$cid' and mid = '$_POST[mid]' and center_cid = '$cfg_center[center_cid]'";
		
		$queryAll_del = implode(";", $batchQuery_del).";";
		$db_rest->multiQuery($queryAll_del);
		
		$pw = $password;
		
		//휴면게정 -> 일반 계정 전환
		$addColumn = "set password='$pw',rest_flag='0',rest_email_flag='0'";
		$addWhere = "where cid='$cid' and mid='$_POST[mid]'";
		$m_member->setMemberInfo("myinfo", $addColumn, $addWhere);
		
		//휴면계정 전환 인증 번호 삭제
		$m_member->delMemberRestoreCode($cid, $_POST[mid]);
		msg(_("변경 완료되었습니다.")."\n"._("로그인 화면으로 이동합니다."), "../member/login.php");
		
	break;
	
	case "member_leave":
		if ($_POST[password_en]) $_POST[password] = base64_decode($_POST[password_en]);
		
		//회원 탈퇴시 cid값을 가져와서 조회하도록 where 조건 추가 / 14.06.13 / kjm
		$tableName = "exm_member";
		
		if ($_POST[mobile_type] != "Y" && $cfg[skin_theme] == "P1") $password = $_POST[password];
		else $password = passwordCommonEncode($_POST[password]);
		
		$addWhere = "where cid='$cid' and mid='$sess[mid]' and password='$password'";
		$data = $m_member->getChkMemberInfo($cid, $tableName, $addWhere);
		
		if (!$data) msg(_("비밀번호가 일치하지 않습니다."), -1);
		
		$m_member->delMemberInfo($cid, $sess[mid], $password);

		$m_member->delEmoneyInfo($cid, $sess[mid], $password); //적립금 삭제 추가  20200121  kkwon
		
		if ($_POST[mobile_type] == "Y") { 
			echo _("정상적으로 회원탈퇴가 되었습니다.")."<br />"._("이용해 주셔서 감사합니다."); exit; 
		}
		else { 			
			$_POST[rurl] = "/member/logout.php?rurl=../member/leave_ok.php"; 
		}
		
	break;
}

if (!$_POST[rurl]){
	$_POST[rurl] = $_SERVER[HTTP_REFERER];
	$_POST[rurl] = str_replace("https://", "http://", $_POST[rurl]);
}else{
	if( strpos($_POST[rurl],"http") > -1 ){
		$_POST[rurl] = str_replace("https://","http://",$_POST[rurl]);
	}else{
		$_POST[rurl] = "http://".$_SERVER['HTTP_HOST'].$_POST[rurl];
	}
}
go($_POST[rurl]);
exit;

?>
