<?
/*
* @date : 20190403
* @author : kdk
* @brief : 인스카그램 연동 사용여부 추가.
* @request :
* @desc :
* @todo :
*/

/*
* @date : 20180116 (20180116)
* @author : kdk
* @brief : 사용자 스크립트 추가.
* @request :
* @desc :
* @todo :
*/

include "../lib.php";

$m_config = new M_config();
$m_mall = new M_mall();
$m_etc = new M_etc();

switch ($_GET[mode]) {

	case "faq_del" :
		$db -> query("delete from exm_faq where no = '$_GET[no]'");
		break;

	case "del_bankinfo" :
		$m_config -> delBankInfo($cid, $_GET[bankno]);

		break;

	case "del_shipcomp" :
		$m_config -> delShipCompInfo($cid, $_GET[shipno]);

		break;

	case "del_popupinfo" :
		$m_config -> delPopupInfo($cid, $_GET[popupno]);

		break;

	case "del_addpage" :
		$m_config -> delAddpageInfo($cid, $_GET[type]);
		break;

	case "delete" :
		$m_config -> delAdminInfo($cid, $_GET[mid]);

		break;

	case "policy_mobile" :
		$policy = getCfg('policy');
		$m_config -> setConfigInfo($cid, $_GET[mode], str_replace("'", "\'", $policy));

		break;

	//배송 추가금액 삭제				20171228	chunter
	case "del_shipping_extra" :
		$m_config->delShippingExtraInfo($_GET[rid], $_GET[zipcode]);
		break;
}

switch ($_POST[mode]) {

	case "design_config" :
		//$data = array("top" => $_POST['dg_top'], "bottom" => $_POST['dg_footer'], "goods_quick" => $_POST['dg_goods_quick']);
		//$layout_data = serialize($data);

		$top_logo = "";
		if ($_POST['top_logo'] == "txt")
			$top_logo = $_POST['top_logo_txt'];

		$flds = array(
			//'layout' => $layout_data,
			'dg_right_slide_menu' => $_POST[dg_right_slide_menu],
			'dg_goods_detail_option_layout' => $_POST[dg_goods_detail_option_layout],
			'dg_top_menu_fix' => $_POST[dg_top_menu_fix],
			'top_logo_type' => $_POST['top_logo'],
			'top_logo' => $top_logo,
			'main_display_board_left' => $_POST[main_display_board_left],
			'main_display_board_right' => $_POST[main_display_board_right],
			'list_catnav' => $_POST[list_catnav],
			'list_orderby' => $_POST[list_orderby],
			//'top_header_color' => $_POST[top_header_color],
			'dg_top_slide_banner' => $_POST[dg_top_slide_banner],
			'sns_goods' => $_POST[sns_goods],
			'zoom_goods' => $_POST[zoom_goods],
		);

		foreach ($flds as $k => $v) {
			$m_config -> setConfigInfo($cid, $k, $v);
		}

		if (is_uploaded_file($_FILES[top_logo_img][tmp_name])) {
			$size = getImageSize($_FILES[top_logo_img][tmp_name]);

         if(!is_dir("../../data/top_logo/$cid")) {
            mkdir("../../data/top_logo", 0707);
            mkdir("../../data/top_logo/$cid", 0707);
         }

         copy($_FILES[top_logo_img][tmp_name], "../../data/top_logo/$cid/top_logo.png");
		}
		break;

	case "menu_priv" :
		include_once dirname(__FILE__) . "/../_inc_service_config.php";
		$r_priv = $_POST;
		$admin_id = $_POST[admin_id];

		unset($r_priv[mode]);
		unset($r_priv[admin_id]);
		//debug($admin_config[allow_left_menu]);
		//debug($r_priv);
		//exit;

		/*
		### 사용안함 제거
		foreach ($r_priv as $folder => $v) {
			if (is_array($v)) {
				foreach ($v as $section => $v2) {
					foreach ($v2 as $page => $priv) {
						if (!$priv || $priv == "N")
							unset($r_priv[$folder][$section][$page]);
					}
				}
			}
		}

		### 빈배열제거
		foreach ($r_priv as $folder => $v) {
			if (is_array($v)) {
				foreach ($v as $section => $v2) {
					if (!count($v2))
						unset($r_priv[$folder][$section]);
				}
			}
		}

		### 빈배열제거
		foreach ($r_priv as $folder => $v) {
			if (!count($v))
				unset($r_priv[$folder]);
		}

		//left_menu 구조로 만들기
		foreach ($r_priv as $folder => $v) {
			$result = array();
			$result[] = $folder;
			if (is_array($v)) {
				foreach ($v as $section => $v2) {
					$m_priv = array();
					$subIndex = 0;
					$m_priv[] = $section;
					if (is_array($v2)) {
						foreach ($v2 as $kPage => $vPage) {
							$subIndex++;
							//$r_priv[$section] = array_merge($v2, $vPage);
							$m_priv[] = $kPage;
						}
					}
					$result[] = $m_priv;
				}
			}
			//debug($menu_priv . "<BR>");
			$menu_priv[] = $result;
		}

		//debug($menu_priv);
		//exit;
		$priv = (count($menu_priv)) ? serialize($menu_priv) : "";*/

		$priv = serialize($r_priv);

		$query = "update exm_admin set priv = '$priv' where cid = '$cid' and mid = '$admin_id'";
		$db -> query($query);

		break;
	case "category_hidden" :
		if (is_array($_POST[catno]))
			$catArray = $_POST[catno];
		else
			$catArray[] = $_POST[catno];

		//전체를 비활성화 hidden 처리후 선택된것만 활성화 처리. 1차 카테고리만 처리
		$query = "update exm_category set hidden = '1' where cid = '$cid' and LENGTH(catno) < 4";
		$db -> query($query);

		foreach ($catArray as $key => $value) {
			$query = "update exm_category set hidden = '0' where cid = '$cid' and catno = '$value'";
			$db -> query($query);
		}
		break;

	case "faq" :
		if (!$_POST[no]) {
			$query = "
         insert into exm_faq set
            cid      = '$cid',
            catnm = '$_POST[catnm]',
            q     = '$_POST[q]',
            a     = '$_POST[a]',
            sort  = -unix_timestamp()
         ";

		} else {

			$query = "
         update exm_faq set
            catnm = '$_POST[catnm]',
            q     = '$_POST[q]',
            a     = '$_POST[a]'
         where no = '$_POST[no]'
         ";

		}
		$db -> query($query);

		msgNpopClose(_("수정 완료되었습니다."));
		break;

	case "service_menu_setting" :
		$data[menu_admin] = $_POST[menu_admin];
		$data[menu_faq] = $_POST[menu_faq];
		$data[menu_myinfo] = $_POST[menu_myinfo];
		$data[menu_psd] = $_POST[menu_psd];
		$data[menu_mycs] = $_POST[menu_mycs];
		$data[menu_remote_service] = $_POST[menu_remote_service];
		$data[menu_help] = $_POST[menu_help];

		$cfg_data = serialize($data);

		$query = "insert into exm_config set
                  cid = '$cid',
                  config = 'service_menu',
                  value = '$cfg_data'
                on duplicate key update
                  value = '$cfg_data'
               ";
		$db -> query($query);

		break;

	### 메뉴항목 순서 설정
	case "category_sort" :
		if (is_array($_POST))
			foreach ($_POST['sort'] as $k => $v) {
				$query = "update exm_category set sort = '$k' where cid = '$cid' and catno = '$v'";
				$db -> query($query);
			}

		//msg("정렬순서가 저장되었습니다.");
		//exit;
		break;

	case "basis" :
		if ($_POST[copyright])
			$_POST[copyright] = addslashes(base64_decode($_POST[copyright]));
		if (!$_POST[deny_robots]) $_POST[deny_robots] = "0";
		if (!$_POST[AX_editor_use]) $_POST[AX_editor_use] = "N";
		if (!$_POST[local_storage_use]) $_POST[local_storage_use] = "N";
		if (!$_POST[editlist_use]) $_POST[editlist_use] = "N";
		if (!$_POST[review_display]) $_POST[review_display] = "N";
		if (!$_POST[mouse_event_use]) $_POST[mouse_event_use] = "N";
		if (!$_POST[ssl_use]) $_POST[ssl_use] = "N";
		if (!$_POST[admin_main_board_cnt]) $_POST[admin_main_board_cnt] = "5";

		$ilarkMailFlag = false;
		$smsAdminData = $m_config->getConfigInfo($cid, "smsAdmin");
		if ($_POST[smsAdmin] != $smsAdminData[value]) $ilarkMailFlag = true;

		$flds = array(
			'nameSite' => $_POST[nameSite],
			'urlSite' => $_POST[urlSite],
			'urlService' => $_POST[urlService],
			'emailAdmin' => $_POST[emailAdmin],
			'smsAdmin' => $_POST[smsAdmin],
			'copyright' => $_POST[copyright],
			'nameComp' => $_POST[nameComp],
			'typeBiz' => $_POST[typeBiz],
			'itemBiz' => $_POST[itemBiz],
			'regnumBiz' => $_POST[regnumBiz],
			'regnumOnline' => $_POST[regnumOnline],
			'nameCeo' => $_POST[nameCeo],
			'managerInfo' => $_POST[managerInfo],
			'zipcode' => $_POST[zipcode],
			'address' => $_POST[address],
			'addressComp2' => $_POST[addressComp2],
			'phoneComp' => $_POST[phoneComp],
			'faxComp' => $_POST[faxComp],
			//'titleDoc'			=> $_POST[titleDoc],
			//'keywordsDoc'		=> $_POST[keywordsDoc],
			'deny_robots' => $_POST[deny_robots],
			'AX_editor_use' => $_POST[AX_editor_use],
			'local_storage_use' => $_POST[local_storage_use],
			'editlist_use' => $_POST[editlist_use],
			'review_display' => $_POST[review_display],
			'mouse_event_use' => $_POST[mouse_event_use],

			'admin_main_board_cnt' => $_POST[admin_main_board_cnt],
			'admin_order_web_app' => $_POST[admin_order_web_app],

			'ssl_use' => $_POST[ssl_use],
			'ssl_action' => $_POST[ssl_action],
			'ssl_url' => $_POST[ssl_url],

		);

		foreach ($flds as $k => $v) {
			$m_config -> setConfigInfo($cid, $k, $v);
		}

		$dir = "../../data/favicon/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		$dir = "../../data/favicon/$cid/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		$dir = "../../data/favicon/$cid/$cfg[skin]/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		if (is_uploaded_file($_FILES[favicon][tmp_name])) {
			move_uploaded_file($_FILES[favicon][tmp_name], "../../data/favicon/$cid/$cfg[skin]/favicon.ico");
		}

		if ($_POST[d][favicon]) {
			@unlink("../../data/favicon/$cid/$cfg[skin]/favicon.ico");
    }

    //kkwon 20.08.19 로고 추가
    if (is_uploaded_file($_FILES[top_logo][tmp_name])) {
			move_uploaded_file($_FILES[top_logo][tmp_name], "../../data/favicon/$cid/$cfg[skin]/top_logo.png");
		}

		if ($_POST[d][top_logo]) {
			@unlink("../../data/favicon/$cid/$cfg[skin]/top_logo.png");
    }

    if (is_uploaded_file($_FILES[top_call][tmp_name])) {
			move_uploaded_file($_FILES[top_call][tmp_name], "../../data/favicon/$cid/$cfg[skin]/top_call.png");
		}

		if ($_POST[d][top_call]) {
			@unlink("../../data/favicon/$cid/$cfg[skin]/top_call.png");
    }

    //kkwon 20.08.24 로고스몰 추가
    if (is_uploaded_file($_FILES[top_logo_sm][tmp_name])) {
			move_uploaded_file($_FILES[top_logo_sm][tmp_name], "../../data/favicon/$cid/$cfg[skin]/top_logo_sm.png");
		}

		if ($_POST[d][top_logo_sm]) {
			@unlink("../../data/favicon/$cid/$cfg[skin]/top_logo_sm.png");
    }

		if ($_POST[deny_robots]) {
			$fp = fopen("../../robots.txt", "w");
			fwrite($fp, "User-agent: *\nDisallow: /");
		} else {
			$fp = fopen("../../robots.txt", "w");
			fwrite($fp, "User-agent: *\nAllow: /");
		}

		//20181210 / minks / 관리자 sms발송번호 변경시 자동메일 발송
		if ($ilarkMailFlag) {
			$data[subject] = "[".$_POST[nameSite]."] 문자메시지 발송번호 업데이트 이벤트 발생";
			$data[contents] = "판매처 : [".$_POST[nameSite]."] / ".$cfg_center[center_cid].", ".$cid."\r\n발송번호 : ".$_POST[smsAdmin];
			autoMailIlark("solution@ilark.co.kr", $data);
		}

		break;

	case "company" :
		if ($_POST[company])
			$_POST[company] = addslashes(base64_decode($_POST[company]));
		$m_config -> setConfigInfo($cid, $_POST[mode], $_POST[company]);

		break;

	case "agreement" :
		$m_config -> setConfigInfo($cid, $_POST[mode], $_POST[agreement]);

		break;

	case "search_word" :
		$m_config -> setConfigInfo($cid, $_POST[mode], $_POST[search_word]);

		break;

	case "policy" :
		$flds = array(
			'policy' => str_replace("'", "\'", $_POST[policy]),
			'agreement2' => str_replace("'", "\'", $_POST[agreement2]),
			'personal_data_collect_use_choice' => str_replace("'", "\'", $_POST[personal_data_collect_use_choice]),
			'personal_data_referral' => str_replace("'", "\'", $_POST[personal_data_referral]),
			'privacy_agreement' => str_replace("'", "\'", $_POST[privacy_agreement])
		);

		foreach ($flds as $k => $v) {
			$m_config -> setConfigInfo($cid, $k, $v);
		}

		### p3p.xml 업로드
		if (is_uploaded_file($_FILES[file1][tmp_name])) {
			$ext1 = explode(".", $_FILES[file1][name]);

			if ($ext1[0] != 'p3p' || $ext1[1] != 'xml') {
				msg(_("p3p.xml 파일이 아닙니다."), -1);
				exit ;
			} else {
				move_uploaded_file($_FILES[file1][tmp_name], "../../w3c/" . $_FILES[file1][name]);
			}
		}

		### p3policy.xml 업로드
		if (is_uploaded_file($_FILES[file2][tmp_name])) {
			$ext2 = explode(".", $_FILES[file2][name]);

			if ($ext2[0] != 'p3policy' || $ext2[1] != 'xml') {
				msg(_("p3policy.xml 파일이 아닙니다."), -1);
				exit ;
			} else {
				move_uploaded_file($_FILES[file2][tmp_name], "../../w3c/" . $_FILES[file2][name]);
			}
		}
   break;

   case "policy_nonmember" :
      $flds = array(
         'nonmember_agreement' => str_replace("'", "\'", $_POST[nonmember_agreement]),
         'personal_data_collect_use_choice_nonmember' => str_replace("'", "\'", $_POST[personal_data_collect_use_choice_nonmember]),
         'personal_data_referral_nonmember' => str_replace("'", "\'", $_POST[personal_data_referral_nonmember])
      );

      foreach ($flds as $k => $v) {
         $m_config -> setConfigInfo($cid, $k, $v);
      }
   break;

	case "mall_info" :
		if ($_POST[phone])
			$_POST[phone] = @implode("-", $_POST[phone]);
		$m_mall -> mallInfoUpdate($cid, $_POST[self_podsiteid], $_POST[self_podsid], $_POST[manager], $_POST[phone]);

		break;

	case "main_page" :
		if (!$_POST[main_page_popup])
			$_POST[main_page_popup] = "0";
		$m_config -> setConfigInfo($cid, 'main_page', $_POST[main_page]);
		$m_config -> setConfigInfo($cid, 'main_page_popup', $_POST[main_page_popup]);

		break;

	case "bankinfo" :
		if ($_POST[bankinfo])
			$_POST[bankinfo] = implode(" ", $_POST[bankinfo]);
		$m_config -> setBankInfo($cid, $_POST[bankinfo], $_POST[bankno]);
		echo "<script>alert('" . _("저장 완료되었습니다.") . "');this.close();opener.location='payment.php';</script>";
		exit ;

		break;

	case "pg" :
		if (!$_POST[cash_receipt])
			$_POST[cash_receipt] = "0";
		if ($_POST[paymethod_bank])
			$_POST[paymethod][-1] = "b";
		if ($_POST[paymethod_credit])
			$_POST[paymethod][-2] = "t";
		if ($_POST[paymethod_deposit])
			$_POST[paymethod][-3] = "d";

		if (is_array($_POST[paymethod]))
			ksort($_POST[paymethod]);

		$data[pg] = array(
			"cash_receipt" => $_POST[cash_receipt],
			"module" => $_POST[module],
			"paymethod" => $_POST[paymethod],
			"code" => $_POST[code],
			"key" => $_POST['key'],
			"quotaopt" => $_POST[quotaopt],
			"escrow" => $_POST[escrow],
			"e_paymethod" => $_POST[e_paymethod],
			"mid" => $_POST[mid],
			"mid_e" => $_POST[mid_e],
			"admin" => $_POST[admin],
			"lgd_mid" => $_POST[lgd_mid],
			"lgd_mertkey" => $_POST[lgd_mertkey],
			"lgd_custom_skin" => $_POST[lgd_custom_skin],
			"inipay_sign_key" => $_POST[inipay_sign_key],
			"kcp_noint" => $_POST[kcp_noint],
			"kcp_noint_str" => $_POST[kcp_noint_str],
			"kcp_skin_indx" => $_POST[kcp_skin_indx],
			"kcp_site_logo" => $_POST[kcp_site_logo],
			"epsilon_mid" => $_POST[epsilon_mid],
			"tmembership_use" => $_POST[tmembership_use],
			"easypay_mid" => $_POST[easypay_mid],




			"npay_use" => $_POST[npay_use],
			"npay_shopid" => $_POST[npay_shopid],
			"npay_authkey" => $_POST[npay_authkey],
			"npay_btnkey" => $_POST[npay_btnkey],
			"npay_commkey" => $_POST[npay_commkey],
			"npay_button_type" => $_POST[npay_button_type],
			"npay_button_color" => $_POST[npay_button_color],
			"npay_addshipping_text" => $_POST[npay_addshipping_text],
			"npay_test_flag" => $_POST[npay_test_flag],
			"npay_test_mid" => $_POST[npay_test_mid],

			"kakaopay_use" => $_POST[kakaopay_use],
			"kakaopay_adminkey" => $_POST[kakaopay_adminkey],
			"kakaopay_cid" => $_POST[kakaopay_cid],
		);

		$data[pg] = serialize($data[pg]);

		$m_config -> setConfigInfo($cid, 'pg', $data[pg]);

		break;

	case "payment_info" :
		if ($_POST[paymethod_info])
			$_POST[paymethod_info] = stripslashes(base64_decode($_POST[paymethod_info]));

		$cfg[paymethod_info][$_POST[paymethod]] = $_POST[paymethod_info];
		$cfg[paymethod_info] = array_map("addslashes", $cfg[paymethod_info]);
		$cfg[paymethod_info] = array_map("base64_encode", $cfg[paymethod_info]);
		$paymethod_info = serialize($cfg[paymethod_info]);

		$m_config -> setConfigInfo($cid, 'paymethod_info', $paymethod_info);

		break;

	case "shipcomp" :
		if (!$_POST[isuse])
			$_POST[isuse] = "0";
		$m_config -> setShipCompInfo($cid, $_POST[compnm], $_POST[url], $_POST[isuse], $_POST[shipno]);
		echo "<script>alert('" . _("저장 완료되었습니다.") . "');this.close();opener.location='shipcomp.php';</script>";
		exit ;

		break;

	case "self_deliv" :
		$m_config -> setConfigInfo($cid, 'self_deliv', $_POST[self_deliv]);
		$m_config -> setConfigInfo($cid, 'self_shiptype', $_POST[self_shiptype]);
		$m_config -> setConfigInfo($cid, 'self_shipconditional', $_POST[self_shipconditional]);
		$m_config -> setConfigInfo($cid, 'self_shipprice', $_POST[self_shipprice]);
		$m_config -> setConfigInfo($cid, 'order_shiptype', $_POST[order_shiptype]);

		break;

	case "popup" :
		if ($_POST[contents]) $_POST[contents] = addslashes(base64_decode($_POST[contents]));
		if ($_POST[skintype]) $_POST[skintype] = implode("|", $_POST[skintype]);

		if (!$_POST[popupno]) {
			$_POST[popupno] = $m_config -> getPopupNo($cid);
			$_POST[popupno] = $_POST[popupno] + 1;
		}

		$m_config -> setPopupInfo($cid, $_POST[popupno], $_POST[title], $_POST[state], $_POST[sdt], $_POST[edt], $_POST[width], $_POST[height], $_POST[top], $_POST[left], $_POST[contents], $_POST[skintype]);

		msgNlocationReplace(_("저장 완료되었습니다."), "popup.php");

		break;

	case "member_system" :
		if ($_POST['system'] == 'close')
			$_POST[order_system] = $_POST['system'];
		//접근권한이 회원이면 주문도 회원제한으로 변경       20160324
		if ($_POST['system'] == 'open')
			$_POST[redirect_url] = "";
		//접근권한이 비회원이면 리다이렉팅 주소도 초기화 / 16.08.08 / kjm

		$data = $_POST;

		//데이타 깨짐현상때문에 base64 인코딩 처리			20180822		chunter
		$data[out_login_param_login_msg] = base64_encode($data[out_login_param_login_msg]);
		$data[out_login_param_login_url] = base64_encode($data[out_login_param_login_url]);
		$data[out_login_param_logout_msg] = base64_encode($data[out_login_param_logout_msg]);
		$data[out_login_param_logout_url] = base64_encode($data[out_login_param_logout_url]);
		$data[out_login_param_regist_msg] = base64_encode($data[out_login_param_regist_msg]);
		$data[out_login_param_regist_url] = base64_encode($data[out_login_param_regist_url]);

		unset($data[mode]);
		unset($data[mobile_member_use]);
		unset($data[x]);
		unset($data[y]);

		//추가 디자인만 json_encode 한번 해준다.
		$data = serialize($data);

		$m_config -> setConfigInfo($cid, "member_system", $data);
		$m_config -> setConfigInfo($cid, "mobile_member_use", $_POST[mobile_member_use]);

		break;

	case "pods_module" :
		if ($_POST[pod_intro])
			$_POST[pod_intro] = addslashes(base64_decode($_POST[pod_intro]));

		$m_config -> setConfigInfo($cid, 'pods_size', $_POST[pods_size]);
		$m_config -> setConfigInfo($cid, 'pod_intro', $_POST[pod_intro]);
      $m_config -> setConfigInfo($cid, 'pod_module_intro_use', $_POST[pod_module_intro_use]);

		if (is_uploaded_file($_FILES[pod_logo][tmp_name])) {
			$dir = "../../data/pod/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$dir = "../../data/pod/$cid/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$size = getImageSize($_FILES[pod_logo][tmp_name]);

			if ($size[2] != 3) {
				msg(_("로고이미지는 png 파일 이여야만 합니다."), -1);
				exit ;
			}

			if ($size[0] > 157) {
				msg(_("로고이미지의 너비는 최대 157px 이하의 사이즈여야합니다."), -1);
				exit ;
			}

			if ($size[1] > 21) {
				msg(_("로고이미지의 높이는 최대 21px 이하의 사이즈여야합니다."), -1);
				exit ;
			}

			copy($_FILES[pod_logo][tmp_name], "../../data/pod/$cid/pod_logo.png");
		}

		if ($_POST[d][pod_logo]) {
			@unlink("../../data/pod/$cid/pod_logo.png");
		}

		if (is_uploaded_file($_FILES[pod_title][tmp_name])) {
			$dir = "../../data/pod/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$dir = "../../data/pod/$cid/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$size = getImageSize($_FILES[pod_title][tmp_name]);

			if ($size[2] != 3) {
				msg(_("타이틀이미지는 png 파일 이여야만 합니다."), -1);
				exit ;
			}

			if ($size[0] > 60) {
				msg(_("타이틀이미지의 너비는 최대 60px 이하의 사이즈여야합니다."), -1);
				exit ;
			}

			if ($size[1] > 11) {
				msg(_("타이틀이미지의 높이는 최대 11px 이하의 사이즈여야합니다."), -1);
				exit ;
			}

			copy($_FILES[pod_title][tmp_name], "../../data/pod/$cid/pod_title.png");
		}

		if ($_POST[d][pod_title]) {
			@unlink("../../data/pod/$cid/pod_title.png");
		}

		break;

	case "self_release" :
		$m_config -> setConfigInfo($cid, $_POST[mode], $_POST[self_release]);

		if ($_POST[self_release]) {
			$_POST[phone] = implode("-", $_POST[phone]);
			$rid = "_self_$cid";

			$m_etc -> setReleaseInfo($cid, $rid, $_POST[compnm], $_POST[nicknm], $_POST[name], $_POST[manager], $_POST[phone], $_POST[zipcode], $_POST[address], $_POST[address_sub], $_POST[shiptype], $_POST[shipprice], $_POST[shipconditional], $_POST[oshipprice], $_POST[podsid], $_POST[siteid]);
		}

		break;

	case "bill_info" :
		if (!$_POST[bill_yn])
			$_POST[bill_yn] = "0";
		if (!$_POST[bill_vat_yn])
			$_POST[bill_vat_yn] = "0";

		$flds = array(
			'bill_yn' => $_POST[bill_yn],
			'bill_vat_yn' => $_POST[bill_vat_yn],
			'bill_payOpt' => $_POST[bill_payOpt],
			'bill_expDt' => $_POST[bill_expDt],
			'bill_nameComp' => $_POST[bill_nameComp],
			'bill_typeBiz' => $_POST[bill_typeBiz],
			'bill_itemBiz' => $_POST[bill_itemBiz],
			'bill_regnumBiz' => $_POST[bill_regnumBiz],
			'bill_regnumOnline' => $_POST[bill_regnumOnline],
			'bill_nameCeo' => $_POST[bill_nameCeo],
			'bill_managerInfo' => $_POST[bill_managerInfo],
			'bill_zipcode' => $_POST[zipcode],
			'bill_address' => $_POST[address],
			'bill_phoneComp' => $_POST[bill_phoneComp],
			'bill_faxComp' => $_POST[bill_faxComp],
			'bill_Etc' => $_POST[bill_Etc],
			'bill_bank_name' => $_POST[bill_bank_name],
            'bill_bank_no' => $_POST[bill_bank_no],
            'bill_bank_owner' => $_POST[bill_bank_owner],
		);

		foreach ($flds as $k => $v) {
			$m_config -> setConfigInfo($cid, $k, $v);
		}


		$dir = "../../data/bill/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		$dir = "../../data/bill/$cid/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		if (is_uploaded_file($_FILES[bill_seal][tmp_name])) {
			$size = getImageSize($_FILES[bill_seal][tmp_name]);

			if ($size[2] != 3) {
				msg(_("직인이미지는 png 파일 이여야만 합니다."), -1);
				exit ;
			}

			if ($size[0] > 53) {
				msg(_("직인이미지의 너비는 최대 53px 이하의 사이즈여야합니다."), -1);
				exit ;
			}

			if ($size[1] > 53) {
				msg(_("직인이미지의 높이는 최대 53px 이하의 사이즈여야합니다."), -1);
				exit ;
			}

			copy($_FILES[bill_seal][tmp_name], "../../data/bill/$cid/bill_seal.png");
		}

		if ($_POST[d][bill_seal]) {
			@unlink("../../data/bill/$cid/bill_seal.png");
		}


		if (is_uploaded_file($_FILES[tax_seal][tmp_name])) {
			$size = getImageSize($_FILES[tax_seal][tmp_name]);
			copy($_FILES[tax_seal][tmp_name], "../../data/bill/$cid/tax_seal.jpg");
		}
		if ($_POST[d][tax_seal]) {
			@unlink("../../data/bill/$cid/tax_seal.jpg");
		}

		if (is_uploaded_file($_FILES[bank_seal][tmp_name])) {
			$size = getImageSize($_FILES[bank_seal][tmp_name]);
			copy($_FILES[bank_seal][tmp_name], "../../data/bill/$cid/bank_seal.jpg");
		}
		if ($_POST[d][bank_seal]) {
			@unlink("../../data/bill/$cid/bank_seal.jpg");
		}

		break;

	case "write" :
		if (!trim($_POST[mid])) {
			msg(_("아이디가 입력되지 않았습니다."), -1);
			exit ;
		}

		$addWhere = "where cid='$cid' and mid='$_POST[mid]'";
		$data = $m_config -> getAdminInfo($cid, $_POST[mid], $addWhere);

		if ($data[mid]) {
			msg(_("이미 등록된 아이디 입니다."), -1);
			exit ;
		}
      $password = passwordCommonEncode($_POST[password]);
		$addColumn = "set cid='$cid',mid='$_POST[mid]',password='$password',name='$_POST[name]',redirect_url='$_POST[redirect_url]',regdt=now()";
		$m_config -> setAdminInfo($_POST[mode], $addColumn);

		msgNlocationReplace(_("저장 완료되었습니다."), "admin.php");

		break;

	case "modify" :
		if ($_POST[password]){
		   $password = passwordCommonEncode($_POST[password]);
			$addColumn = "set password='$password',name='$_POST[name]',redirect_url='$_POST[redirect_url]'";
      }
		else
			$addColumn = "set name='$_POST[name]',redirect_url='$_POST[redirect_url]'";

		$addWhere = "where cid='$cid' and mid='$_POST[mid]'";
		$m_config -> setAdminInfo($_POST[mode], $addColumn, $addWhere);

		msgNlocationReplace(_("저장 완료되었습니다."), "admin.php");

		break;

	case "limited_ip" :
		$_POST[limited_ip] = trim($_POST[limited_ip]);

		$m_config -> setConfigInfo($cid, $_POST[mode], $_POST[limited_ip]);

		break;

	case "sns_login" :
		$data[sns_login] = array(
			"sns_login_use" => $_POST[sns_login_use],
			"naver_login_use" => $_POST[naver_login_use],
			"naver_client_id" => $_POST[naver_client_id],
			"naver_client_secret" => $_POST[naver_client_secret],
			"kakao_login_use" => $_POST[kakao_login_use],
			"kakao_rest_api_key" => $_POST[kakao_rest_api_key],
			"kakao_javascript_key" => $_POST[kakao_javascript_key],
			"facebook_login_use" => $_POST[facebook_login_use],
			"facebook_app_id" => $_POST[facebook_app_id],
			"kidsnote_login_use" => $_POST[kidsnote_login_use],
			"kidsnote_client_url" => $_POST[kidsnote_client_url],
			"kidsnote_client_id" => $_POST[kidsnote_client_id],
			"kidsnote_client_secret" => $_POST[kidsnote_client_secret]
		);

		$data[sns_login] = serialize($data[sns_login]);

		$m_config -> setConfigInfo($cid, $_POST[mode], $data[sns_login]);

		break;

   case "insta_config" :
      $data[insta_config] = array(
         "insta_use" => $_POST[insta_use],
         "client_id" => $_POST[client_id],
         "client_secret" => $_POST[client_secret],
         "access_token" => $_POST[access_token]
      );

      $data[insta_config] = serialize($data[insta_config]);

      $m_config -> setConfigInfo($cid, $_POST[mode], $data[insta_config]);
   break;

	case "naver_relation_config" :
		if (!$_POST[naver_relation_sameAs]) $_POST[naver_relation_sameAs] = array();
		$_POST[naver_relation_sameAs] = array_values(array_filter(array_map('trim', $_POST[naver_relation_sameAs])));

		foreach ($_POST[naver_relation_sameAs] as $k=>$v) {
			$_POST[naver_relation_sameAs][$k] = urlencode($v);
		}

		$data[naver_relation_config] = array(
			"naver_relation_use" => $_POST[naver_relation_use],
			"naver_relation_sameAs" => $_POST[naver_relation_sameAs]
		);

		$data[naver_relation_config] = serialize($data[naver_relation_config]);

		$m_config -> setConfigInfo($cid, $_POST[mode], $data[naver_relation_config]);

	break;

	case "emoney_config" :

		if (!$_POST[emoney_use_flag])
			$_POST[emoney_use_flag] = "N";

		if (!isset($_POST[emoney_send_day]))
			$_POST[emoney_send_day] = "7";
		if (!$_POST[emoney_round])
			$_POST[emoney_round] = "0";
		if (!$_POST[emoney_send_type])
			$_POST[emoney_send_type] = "G";

		if (!$_POST[emoney_send_ratio])
			$_POST[emoney_send_ratio] = "0";
		if (!$_POST[emoney_new_member])
			$_POST[emoney_new_member] = "0";
		if (!$_POST[emoney_expire_day])
			$_POST[emoney_expire_day] = "0";
		if (!$_POST[emoney_min_orderprice])
			$_POST[emoney_min_orderprice] = "0";
		if (!$_POST[emoney_use_min])
			$_POST[emoney_use_min] = "0";
		if (!$_POST[emoney_use_max])
			$_POST[emoney_use_max] = "0";
		if (!$_POST[emoney_review_write])
			$_POST[emoney_review_write] = "0";
		if (!$_POST[emoney_open_gallery])
			$_POST[emoney_open_gallery] = "0";

		$flds = array(
			'emoney_use_flag' => $_POST[emoney_use_flag],
			'emoney_send_day' => $_POST[emoney_send_day],
			'emoney_round' => $_POST[emoney_round],
			'emoney_send_type' => $_POST[emoney_send_type],
			'emoney_send_ratio' => $_POST[emoney_send_ratio],
			'emoney_new_member' => $_POST[emoney_new_member],
			'emoney_expire_day' => $_POST[emoney_expire_day],
			'emoney_min_orderprice' => $_POST[emoney_min_orderprice],
			'emoney_use_min' => $_POST[emoney_use_min],
			'emoney_use_max' => $_POST[emoney_use_max],
			'emoney_review_write' => $_POST[emoney_review_write],
			'emoney_open_gallery' => $_POST[emoney_open_gallery],
		);

		foreach ($flds as $k => $v) {
			$m_config -> setConfigInfo($cid, $k, $v, "emoney");
		}
		break;

	case "cash_supp" :
		if (!$_POST[cash_supp_use])
			$_POST[cash_supp_use] = "0";
		if (!$_POST[cash_supp_co_kind])
			$_POST[cash_supp_co_kind] = "0";
		if (!$_POST[cash_supp_price_policy])
			$_POST[cash_supp_price_policy] = "R";
		if (!$_POST[cash_supp_limit_day])
			$_POST[cash_supp_limit_day] = "0";

		$flds = array('cash_supp_use' => $_POST[cash_supp_use], 'cash_supp_co_kind' => $_POST[cash_supp_co_kind], 'cash_supp_price_policy' => $_POST[cash_supp_price_policy], 'cash_supp_limit_day' => $_POST[cash_supp_limit_day]);

		foreach ($flds as $k => $v) {
			$m_config -> setConfigInfo($cid, $k, $v, "cash_supp");
		}
		break;

	//20160810 / minks / 모바일 회원연동 사용설정
	case "mobile_member_use" :
		$m_config -> setConfigInfo($cid, $_POST[mode], $_POST[mobile_member_use]);

		break;

	case "intro" :
		if ($_POST[intro])
			$_POST[intro] = addslashes(base64_decode($_POST[intro]));
		if (!$_POST[apply_intro])
			$_POST[apply_intro] = "0";

		$m_config -> setConfigInfo($cid, 'intro', $_POST[intro]);
		$m_config -> setConfigInfo($cid, 'apply_intro', $_POST[apply_intro]);

		break;

	case "parking" :
		if ($_POST[parking])
			$_POST[parking] = addslashes(base64_decode($_POST[parking]));
		if (!$_POST[apply_parking])
			$_POST[apply_parking] = "0";

		$m_config -> setConfigInfo($cid, 'parking', $_POST[parking]);
		$m_config -> setConfigInfo($cid, 'apply_parking', $_POST[apply_parking]);

		break;

    #스크립트추가 / 20180116 / kdk
	case "bottom_script" :
        if ($_POST[bottom_script])
            $_POST[bottom_script] = addslashes(base64_decode($_POST[bottom_script]));

        $m_config -> setConfigInfo($cid, 'bottom_script', $_POST[bottom_script]);

        break;

    #구매 완료 스크립트추가 / 20191111 / jtkim
    case "payment_script" :
        if ($_POST[payment_script])
            $_POST[payment_script] = addslashes(base64_decode($_POST[payment_script]));

        $m_config -> setConfigInfo($cid, 'payment_script', $_POST[payment_script]);

		break;

    #회원가입 완료 스크립트추가 / 20191111 / jtkim
    case "register_ok_script" :
        if ($_POST[register_ok_script])
            $_POST[register_ok_script] = addslashes(base64_decode($_POST[register_ok_script]));

        $m_config -> setConfigInfo($cid, 'register_ok_script', $_POST[register_ok_script]);
		break;

    #장바구니 담기 완료 스크립트추가 / 20191111 / jtkim
    case "in_cart_script" :
        if ($_POST[in_cart_script])
            $_POST[in_cart_script] = addslashes(base64_decode($_POST[in_cart_script]));

        $m_config -> setConfigInfo($cid, 'in_cart_script', $_POST[in_cart_script]);
		break;

	# 메인 컨텐츠 영역 추가 	2017.07.13	kdk
	case "main_content" :
		$imgfile = $diffimg = array();

		$dir = "../../data/main_content/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		$dir = "../../data/main_content/$cid/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		$img = ($_POST[image]) ? $_POST[image] : array();
		$img_t = ($_POST[image_t]) ? $_POST[image_t] : array();
		$img_t_on = ($_POST[image_t_on]) ? $_POST[image_t_on] : array();
		$img_b = ($_POST[image_b]) ? $_POST[image_b] : array();

		if (is_array($_FILES[img][name])) {
			foreach ($_FILES[img][name] as $k => $v) {
				if (!is_uploaded_file($_FILES[img][tmp_name][$k])) {

					if ($img[$k] && file_exists($dir.$img[$k])) {
						$extension = pathinfo($dir.$img[$k], PATHINFO_EXTENSION);
						// 순서 변경시 파일을 덮어씌워서 복사할 파일을 tmp 파일로 저장
						$name = "tmp_slide_img_".($k+1).".".$extension;
						copy($dir.$img[$k], $dir.$name);
						$img[$k] = $name;
					}

					continue;
				}


				$name = getImageSize($_FILES[img][tmp_name][$k]);
				$ext = getExtension($name[2]);

				//$name = time() . $k . "." . $ext;
				$name = "tmp_slide_img_" . ($k+1). "." .$ext;
				move_uploaded_file($_FILES[img][tmp_name][$k], $dir . $name);
				$img[$k] = $name;
			}
		}

		// 순서 변경시 파일을 덮어씌워서 복사할 파일을 slide가 아닌 tmp 파일로 저장 tmp파일
		if(count($img) > 0){
			foreach($img as $k => $v){
				if($img[$k] && file_exists($dir.$img[$k])){
					//echo $dir.$img[$k];
					$base_name = pathinfo($dir.$img[$k], PATHINFO_BASENAME);
					//echo $name;
					if( substr($base_name,0,3) == "tmp"){
						copy($dir.$base_name,$dir.substr($base_name,4));
						//unlink($dir.$base_name);
						$img[$k] = substr($base_name,4);

					}
				}
			}
		}

		if (is_array($img)) {
			foreach ($img as $k => $v) {
				//썸네일 이미지
				if (is_uploaded_file($_FILES[img_t][tmp_name][$k])) {
					$name = getImageSize($_FILES[img_t][tmp_name][$k]);
					$ext = getExtension($name[2]);

					//$name = time() . $k . "_t." . $ext;
					$name = "slide_btn_" . ($k+1). "." .$ext; //slide_btn_1.png
					move_uploaded_file($_FILES[img_t][tmp_name][$k], $dir . $name);
					$img_t[$k] = $name;
				} else {
					if ($img_t[$k] && file_exists($dir.$img_t[$k])) {
						//debug($img_t[$k]);
						$extension = pathinfo($dir.$img_t[$k], PATHINFO_EXTENSION);
						$name = "slide_btn_".($k+1).".".$extension;
						copy($dir.$img_t[$k], $dir.$name);
						$img_t[$k] = $name;
					} else {
						$img_t[$k] = "";
					}
				}

				//썸네일 오버 이미지
				if (is_uploaded_file($_FILES[img_t_on][tmp_name][$k])) {
					$name = getImageSize($_FILES[img_t_on][tmp_name][$k]);
					$ext = getExtension($name[2]);

					//$name = time() . $k . "_t_on." . $ext;
					$name = "slide_btn_" . ($k+1) . "_on". "." .$ext; //slide_btn_1_on.png
					move_uploaded_file($_FILES[img_t_on][tmp_name][$k], $dir . $name);
					$img_t_on[$k] = $name;
				} else {
					if ($img_t_on[$k] && file_exists($dir.$img_t_on[$k])) {
						//debug($img_t_on[$k]);
						$extension = pathinfo($dir.$img_t_on[$k], PATHINFO_EXTENSION);
						$name = "slide_btn_".($k+1)."_on.".$extension;
						copy($dir.$img_t_on[$k], $dir.$name);
						$img_t_on[$k] = $name;
					} else {
						$img_t_on[$k] = "";
					}
				}

				//배경 이미지
				if (is_uploaded_file($_FILES[img_b][tmp_name][$k])) {
					$name = getImageSize($_FILES[img_b][tmp_name][$k]);
					$ext = getExtension($name[2]);

					//$name = time() . $k . "_b." . $ext;
					$name = "slide_BG_" . ($k+1). "." .$ext; //slide_BG_1.jpg
					move_uploaded_file($_FILES[img_b][tmp_name][$k], $dir . $name);
					$img_b[$k] = $name;
				} else {
					if ($img_b[$k] && file_exists($dir.$img_b[$k])) {
						//debug($img_b[$k]);
						$extension = pathinfo($dir.$img_b[$k], PATHINFO_EXTENSION);
						$name = "slide_BG_".($k+1).".".$extension;
						copy($dir.$img_b[$k], $dir.$name);
						$img_b[$k] = $name;
					} else {
						$img_b[$k] = "";
					}
				}
			}
		}

		$b_img = ls($dir);
		$del_img = array_diff($b_img, array_merge($img, $img_t, $img_t_on, $img_b));

		if (is_array($del_img)) {
			foreach ($del_img as $v) {
				@unlink($dir . $v);
			}
		}

        $img = @implode("||", $img);
        $img_t = @implode("||", $img_t);
        $img_t_on = @implode("||", $img_t_on);
        $img_b = @implode("||", $img_b);

        $_POST[url] = @implode("||", $_POST[url]);
        $_POST[target] = @implode("||", $_POST[target]);
        $_POST[title] = addslashes(@implode("||", $_POST[title]));

        if ($_POST[use_flag])
            $use_flag = @implode("||", $_POST[use_flag]);

        $query = "
           insert into md_main_content set
              cid         = '$cid',
              img         = '$img',
              img_t       = '$img_t',
              img_t_on    = '$img_t_on',
              img_b       = '$img_b',             
              url         = '$_POST[url]',
              target      = '$_POST[target]',             
              use_flag    = '$use_flag',
              title       = '$_POST[title]'
           on duplicate key update
              img         = '$img',
              img_t       = '$img_t',
              img_t_on    = '$img_t_on',
              img_b       = '$img_b',
              url         = '$_POST[url]',
              target      = '$_POST[target]',             
              use_flag    = '$use_flag',
              title       = '$_POST[title]'
           ";

        $db -> query($query);

		//debug($query);
		break;

    case "addpage" :
        if ($_POST[contents])
            $_POST[contents] = addslashes(base64_decode($_POST[contents]));

        $m_config -> setAddpageInfo($cid, $_POST[type], $_POST[memo], $_POST[contents]);

        msgNlocationReplace(_("저장 완료되었습니다."), "addpage.php");

        break;

	//추가금액 설정			20171228		chunter
	case "shipping_extra" :
		$m_config->setShippingExtraInfo($_POST[rid], $_POST[zipcode], $_POST[address], $_POST[add_price]);
		echo "<script>alert('" . _("저장 완료되었습니다.") . "');this.close();opener.location='shipping_extra.php?rid={$_POST[rid]}';</script>";
		exit ;
		break;

	//절사관련 설정			20190529		jtkim
	case "cutmoney" :
		if (!$_POST[cutmoney_use] || $_POST[cutmoney_use]=='0'){
			$_POST[cutmoney_use] = "0";
			$_POST[cutmoney_type] = "0";
			$_POST[cutmoney_op] = "0";
			$_POST[cutmoney_display] = "0";
			$_POST[cutmoney_display_money] = "0";
		}

		if (!$_POST[cutmoney_display_text]) $_POST[cutmoney_display_text]="";

		$flds = array(
			'cutmoney_use' => $_POST[cutmoney_use],
			'cutmoney_type' => $_POST[cutmoney_type],
			'cutmoney_op' => $_POST[cutmoney_op],
			'cutmoney_display' => $_POST[cutmoney_display],
			'cutmoney_display_text' => $_POST[cutmoney_display_text],
			'cutmoney_display_money' => $_POST[cutmoney_display_money],
		);

		foreach ($flds as $k => $v) {
			$m_config -> setConfigInfo($cid, $k, $v, "cutmoney");
		}
		break;

	//배송할인 설정			20190621		jtkim
	case "ship_cfg" :
		if(!$_POST[ship_cfg_type] || $_POST[ship_cfg_type]=='D'){
			$_POST[ship_cfg_type] = "D";
			// $ship_cfg_list = "";
			// $_POST[ship_cfg_next] = "";
			$_POST[ship_cfg_dc] = "";
		}

		//if($_POST[ship_cfg_list_arr1]==true && $_POST[ship_cfg_list_arr2]==true && $_POST[ship_cfg_list_arr3]==true)
		//	$ship_cfg_list = $_POST[ship_cfg_list_arr1].",".$_POST[ship_cfg_list_arr2].",".$_POST[ship_cfg_list_arr3];

		$flds = array(
			'ship_cfg_type' => $_POST[ship_cfg_type],
			// 'ship_cfg_list' => $ship_cfg_list,
			// 'ship_cfg_next' => $_POST[ship_cfg_next],
			'ship_cfg_dc' => $_POST[ship_cfg_dc]
		);

		foreach ($flds as $k => $v) {
			$m_config -> setConfigInfo($cid, $k, $v, "ship_cfg");
		}

		break;


	//20210625 / 기타 화면 설정 / kdk
	case "display_config" :
		//20210625 / 할인 숨김 설정 / kdk
		if ($_POST[discount_hidden_flag]) {
			$m_config->setConfigInfo($cid, "discount_hidden_flag", $_POST[discount_hidden_flag]);
		} else {
			$m_config->setConfigInfo($cid, "discount_hidden_flag", "");
		}

		//20210625 / 가격 숨김 설정 / kdk
		if ($_POST[account_hidden_flag]) {
			$m_config->setConfigInfo($cid, "account_hidden_flag", $_POST[account_hidden_flag]);
		} else {
			$m_config->setConfigInfo($cid, "account_hidden_flag", "");
		}

		//추가메모 숨김 설정 20210723 jtkim
		if ($_POST[order_request2_hide]) {
			$m_config->setConfigInfo($cid, "order_request2_hide", $_POST[order_request2_hide]);
		} else {
			$m_config->setConfigInfo($cid, "order_request2_hide", "");
		}

		break;


	/***/
}

function getExtension($fileName) {

	switch ($fileName) {
		//case "1" :
		//	$result = "gif";
		//	break;
		case "2" :
			$result = "jpg";
			break;
		case "3" :
			$result = "png";
			break;
		default :
			//msg("이미지는 gif,jpg,png 형식의 파일만 업로드가 가능합니다.", -1);
			msg(_("이미지는 jpg,png 형식의 파일만 업로드가 가능합니다."), -1);
			break;
	}

   return $result;
}

msgNlocationReplace(_("저장 완료되었습니다."), $_SERVER['HTTP_REFERER']);
?>
