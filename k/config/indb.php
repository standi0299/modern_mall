<?

include "../lib.php";

$m_config = new M_config();
$m_mall = new M_mall();
$m_etc = new M_etc();

switch ($_GET[mode]) {

   case "faq_del" :
      $db->query("delete from exm_faq where no = '$_GET[no]'");
   break;

   case "del_bankinfo" :
	  $m_config->delBankInfo($cid, $_GET[bankno]);

   break;

   case "del_shipcomp" :
	  $m_config->delShipCompInfo($cid, $_GET[shipno]);

   break;

   case "del_popupinfo" : 
	  $m_config->delPopupInfo($cid, $_GET[popupno]);

   break;

   case "delete" :
	  $m_config->delAdminInfo($cid, $_GET[mid]);

   break;

   case "policy_mobile":
	   $policy = getCfg('policy');
	   $m_config->setConfigInfo($cid, $_GET[mode], str_replace("'", "\'", $policy));

   break;
}

switch ($_POST[mode]){
	
	case "design_config" :
      $data  = array("top" => $_POST['dg_top'], "bottom" => $_POST['dg_footer'], "goods_quick" => $_POST['dg_goods_quick']);
      $layout_data = serialize($data);

      $top_logo = "";
      if($_POST['top_logo'] == "txt") $top_logo = $_POST['top_logo_txt'];

		$flds = array(
         'layout'		                    => $layout_data,
    	   'dg_right_slide_menu'			  => $_POST[dg_right_slide_menu],
         'dg_goods_detail_option_layout' => $_POST[dg_goods_detail_option_layout],
         'dg_top_menu_fix'		           => $_POST[dg_top_menu_fix],
         'top_logo_type'                 => $_POST['top_logo'],
         'top_logo'                      => $top_logo,
         'main_display_board_left'       => $_POST[main_display_board_left],
         'main_display_board_right'       => $_POST[main_display_board_right]
   	);

      foreach ($flds as $k => $v) {
         $m_config->setConfigInfo($cid, $k, $v);
      }
      
      if (is_uploaded_file($_FILES[top_logo_img][tmp_name])) {
         $size = getImageSize($_FILES[top_logo_img][tmp_name]);

         if($size[2] != 3) msg(_("png 파일만 가능합니다."), -1);
         
         if ($size[0] > 152) {
            msg("너비는 최대 152px 이하의 사이즈여야합니다.", -1);
            exit;
         }

         if ($size[1] > 57) {
            msg("높이는 최대 57px 이하의 사이즈여야합니다.", -1);
            exit;
         }
         
         copy($_FILES[top_logo_img][tmp_name], "../../data/top_logo.png");
      }
   break;
	
	case "menu_priv" :
		include_once dirname(__FILE__) ."/../_inc_service_config.php";
		$r_priv = $_POST;
		$admin_id = $_POST[admin_id];

      unset($r_priv[mode]);
      unset($r_priv[admin_id]);
      //debug($admin_config[allow_left_menu]);
		//debug($r_priv);
      //exit;
		
      ### 사용안함 제거
      foreach ($r_priv as $folder => $v)
      {
         if (is_array($v)) 
         {
            foreach ($v as $section => $v2) {
               foreach ($v2 as $page => $priv) {
                  if (!$priv || $priv == "N")
	          	      unset($r_priv[$folder][$section][$page]);
               }
            }
         }
      }

      ### 빈배열제거 
      foreach ($r_priv as $folder => $v) 
      {
         if (is_array($v))
         {
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
      foreach ($r_priv as $folder => $v) 
      {    	
         $result = array();
         $result[] = $folder;
         if (is_array($v))
         {
            foreach ($v as $section => $v2) 
            {
        	      $m_priv = array();
					$subIndex = 0;
        	      $m_priv[] = $section;
        	      if (is_array($v2))
					{
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
		$priv = (count($menu_priv)) ? serialize($menu_priv) : "";
		$query = "update exm_admin set priv = '$priv' where cid = '$cid' and mid = '$admin_id'";
      $db -> query($query);

	break;
	case "category_hidden":
		
      if (is_array($_POST[catno]))
			$catArray = $_POST[catno];
		else 
			$catArray[] = $_POST[catno];
		
		//전체를 비활성화 hidden 처리후 선택된것만 활성화 처리. 1차 카테고리만 처리
		$query = "update exm_category set hidden = '1' where cid = '$cid' and LENGTH(catno) < 4";
		$db->query($query);

		foreach ($catArray as $key => $value) {
			$query = "update exm_category set hidden = '0' where cid = '$cid' and catno = '$value'";
			$db->query($query);
		}		
   break;

   case "faq" :
      
      if (!$_POST[no]){
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
      $db->query($query);

      msgNpopClose(_("수정 완료되었습니다."));
   break;
    
   case "service_menu_setting" :
      
      $data[menu_admin]          = $_POST[menu_admin];
      $data[menu_faq]            = $_POST[menu_faq];
      $data[menu_myinfo]         = $_POST[menu_myinfo];
      $data[menu_psd]            = $_POST[menu_psd];
      $data[menu_mycs]           = $_POST[menu_mycs];
      $data[menu_remote_service] = $_POST[menu_remote_service];
      $data[menu_help]           = $_POST[menu_help];

      $cfg_data = serialize($data);

      $query = "insert into exm_config set
                  cid = '$cid',
                  config = 'service_menu',
                  value = '$cfg_data'
                on duplicate key update
                  value = '$cfg_data'
               ";
      $db->query($query);
      
   break;

	### 메뉴항목 순서 설정
	case "category_sort":

		if (is_array($_POST)) foreach($_POST['sort'] as $k=>$v){
			$query = "update exm_category set sort = '$k' where cid = '$cid' and catno = '$v'";
			$db->query($query);
		}

		//msg("정렬순서가 저장되었습니다.");
		//exit; 
		break;
   
   case "basis" :	   
		if ($_POST[copyright]) $_POST[copyright] = addslashes(base64_decode($_POST[copyright]));
	   	if (!$_POST[deny_robots]) $_POST[deny_robots] = "0";
		if (!$_POST[AX_editor_use]) $_POST[AX_editor_use] = "N";
		if (!$_POST[local_storage_use]) $_POST[local_storage_use] = "N";
		if (!$_POST[editlist_use]) $_POST[editlist_use] = "N";
		
    	$flds = array(
    		'nameSite'			=> $_POST[nameSite],
    		'urlSite'			=> $_POST[urlSite],
    		'urlService'		=> $_POST[urlService],
    		'emailAdmin'		=> $_POST[emailAdmin],
    		'smsAdmin'			=> $_POST[smsAdmin],
    		'copyright'  		=> $_POST[copyright],
    		'nameComp'			=> $_POST[nameComp],
    		'typeBiz'			=> $_POST[typeBiz],
    		'itemBiz'			=> $_POST[itemBiz],
    		'regnumBiz'			=> $_POST[regnumBiz],
    		'regnumOnline'		=> $_POST[regnumOnline],
    		'nameCeo'			=> $_POST[nameCeo],
    		'managerInfo'		=> $_POST[managerInfo],
    		'zipcode'			=> $_POST[zipcode],
    		'address'			=> $_POST[address],
    		'addressComp2'		=> $_POST[addressComp2],
    		'phoneComp'			=> $_POST[phoneComp],
    		'faxComp'			=> $_POST[faxComp],
    		//'titleDoc'			=> $_POST[titleDoc],
    		//'keywordsDoc'		=> $_POST[keywordsDoc],
    		'deny_robots'		=> $_POST[deny_robots],
    		'AX_editor_use' 	=> $_POST[AX_editor_use],
    		'local_storage_use' => $_POST[local_storage_use],
    		'editlist_use'  	=> $_POST[editlist_use],
    	);
    	
    	foreach ($flds as $k => $v) {
    		$m_config->setConfigInfo($cid, $k, $v);
		}
		
		$dir = "../../data/favicon/";
		if (!is_dir($dir)) {
			mkdir($dir,0707);
			chmod($dir,0707);
		}
		
		$dir = "../../data/favicon/$cid/";
		if (!is_dir($dir)) {
			mkdir($dir,0707);
			chmod($dir,0707);
		}
		
		$dir = "../../data/favicon/$cid/$cfg[skin]/";
		if (!is_dir($dir)) {
			mkdir($dir,0707);
			chmod($dir,0707);
		}
		
		if (is_uploaded_file($_FILES[favicon][tmp_name])) {
			move_uploaded_file($_FILES[favicon][tmp_name], "../../data/favicon/$cid/$cfg[skin]/favicon.ico");
		}
		
		if ($_POST[d][favicon]) {
			@unlink("../../data/favicon/$cid/$cfg[skin]/favicon.ico");
		}
		
		if ($_POST[deny_robots]) {
			$fp = fopen("../../robots.txt", "w");
			fwrite($fp, "User-agent: *\nDisallow: /");
		} else {
			$fp = fopen("../../robots.txt", "w");
			fwrite($fp, "User-agent: *\nAllow: /");
		}
		
	break;
	
	case "company" :
		if ($_POST[company]) $_POST[company] = addslashes(base64_decode($_POST[company]));
		$m_config->setConfigInfo($cid, $_POST[mode], $_POST[company]);
	
	break;
	
	case "agreement" :
		$m_config->setConfigInfo($cid, $_POST[mode], $_POST[agreement]);

	break;

	case "search_word" :
		$m_config->setConfigInfo($cid, $_POST[mode], $_POST[search_word]);

	break;

	case "policy" :
		$flds = array(
			'policy'				=> $_POST[policy], 
			'agreement2'			=> $_POST[agreement2], 
			'agreement_thirdparty'	=> $_POST[agreement_thirdparty], 
			'nonmember_agreement'	=> $_POST[nonmember_agreement], 
			'privacy_agreement'		=> $_POST[privacy_agreement]
		);
		
		foreach ($flds as $k => $v) {
    		$m_config->setConfigInfo($cid, $k, $v);
		}
		
		### p3p.xml 업로드
		if (is_uploaded_file($_FILES[file1][tmp_name])) {
			$ext1 = explode(".", $_FILES[file1][name]);
			
			if ($ext1[0] != 'p3p' || $ext1[1] != 'xml') {
				msg("p3p.xml 파일이 아닙니다.", -1);
				exit ;
			} else {
				move_uploaded_file($_FILES[file1][tmp_name], "../../w3c/".$_FILES[file1][name]);
			}
		}
		
		### p3policy.xml 업로드
		if (is_uploaded_file($_FILES[file2][tmp_name])) {
			$ext2 = explode(".", $_FILES[file2][name]);
			
			if ($ext2[0] != 'p3policy' || $ext2[1] != 'xml') {
				msg("p3policy.xml 파일이 아닙니다.", -1);
				exit ;
			} else {
				move_uploaded_file($_FILES[file2][tmp_name], "../../w3c/".$_FILES[file2][name]);
			}
		}

   break;
   
   case "mall_info" : 
	   if ($_POST[phone]) $_POST[phone] = @implode("-", $_POST[phone]);
	   $m_mall->mallInfoUpdate($cid, $_POST[self_podsiteid], $_POST[self_podsid], $_POST[manager], $_POST[phone]);
	   
   break;
   
   case "main_page" :
	   if (!$_POST[main_page_popup]) $_POST[main_page_popup] = "0";
	   $m_config->setConfigInfo($cid, 'main_page', $_POST[main_page]);
	   $m_config->setConfigInfo($cid, 'main_page_popup', $_POST[main_page_popup]);
	   
   break;
   
   case "bankinfo" : 
   	   if ($_POST[bankinfo]) $_POST[bankinfo] = implode(" ", $_POST[bankinfo]);
	   $m_config->setBankInfo($cid, $_POST[bankinfo], $_POST[bankno]);
	   echo "<script>alert('"._("저장 완료되었습니다.")."');this.close();opener.location='payment.php';</script>";
	   exit;
   
   break;
   
   case "pg" : 
	   if (!$_POST[cash_receipt]) $_POST[cash_receipt] = "0";
	   if ($_POST[paymethod_bank]) $_POST[paymethod][-1] = "b";
	   if ($_POST[paymethod_credit]) $_POST[paymethod][-2] = "t";
	   if (is_array($_POST[paymethod])) ksort($_POST[paymethod]);
	   
	   $data[pg] = array(
	   	  "cash_receipt"    => $_POST[cash_receipt],
	   	  "module"          => $_POST[module],
           "paymethod"       => $_POST[paymethod],
           "code"            => $_POST[code],
           "key"             => $_POST['key'],
           "quotaopt"        => $_POST[quotaopt],
           "escrow"          => $_POST[escrow],
           "e_paymethod"     => $_POST[e_paymethod],
           "mid"             => $_POST[mid],
           "mid_e"           => $_POST[mid_e],
           "admin"           => $_POST[admin],
           "lgd_mid"         => $_POST[lgd_mid],
           "lgd_mertkey"     => $_POST[lgd_mertkey],
           "lgd_custom_skin" => $_POST[lgd_custom_skin],
           "inipay_sign_key" => $_POST[inipay_sign_key],
           "kcp_noint"       => $_POST[kcp_noint],
           "kcp_noint_str"   => $_POST[kcp_noint_str],
           "kcp_skin_indx"   => $_POST[kcp_skin_indx],
           "kcp_site_logo"   => $_POST[kcp_site_logo],
	   );

	   $data[pg] = serialize($data[pg]);

	   $m_config->setConfigInfo($cid, 'pg', $data[pg]);

   break;

   case "payment_info" :
	   if ($_POST[paymethod_info]) $_POST[paymethod_info] = stripslashes(base64_decode($_POST[paymethod_info]));

	   $cfg[paymethod_info][$_POST[paymethod]] = $_POST[paymethod_info];
	   $cfg[paymethod_info] = array_map("addslashes", $cfg[paymethod_info]);
	   $cfg[paymethod_info] = array_map("base64_encode", $cfg[paymethod_info]);
	   $paymethod_info = serialize($cfg[paymethod_info]);

	   $m_config->setConfigInfo($cid, 'paymethod_info', $paymethod_info);

   break;

   case "shipcomp" : 
	   if (!$_POST[isuse]) $_POST[isuse] = "0";
	   $m_config->setShipCompInfo($cid, $_POST[compnm], $_POST[url], $_POST[isuse], $_POST[shipno]);
	   echo "<script>alert('"._("저장 완료되었습니다.")."');this.close();opener.location='shipcomp.php';</script>";
	   exit;
	   
   break;

   case "self_deliv" :
	   $m_config->setConfigInfo($cid, 'self_deliv', $_POST[self_deliv]);
	   $m_config->setConfigInfo($cid, 'self_shiptype', $_POST[self_shiptype]);
	   $m_config->setConfigInfo($cid, 'self_shipconditional', $_POST[self_shipconditional]);
	   $m_config->setConfigInfo($cid, 'self_shipprice', $_POST[self_shipprice]);
	   $m_config->setConfigInfo($cid, 'order_shiptype', $_POST[order_shiptype]);

   break;
   
   case "popup" : 
	   if ($_POST[contents]) $_POST[contents] = addslashes(base64_decode($_POST[contents]));
	   if (!$_POST[popupno]) {
	   	   $_POST[popupno] = $m_config->getPopupNo($cid);
		   $_POST[popupno] = $_POST[popupno] + 1;
	   }

	   $m_config->setPopupInfo($cid, $_POST[popupno], $_POST[title], $_POST[state], $_POST[sdt], $_POST[edt], $_POST[width], $_POST[height], $_POST[top], $_POST[left], $_POST[contents]);

	   msgNlocationReplace(_("저장 완료되었습니다."), "popup.php");

   break;
   
   case "member_system" : 
   	   if ($_POST['system'] == 'close') $_POST[order_system] = $_POST['system']; //접근권한이 회원이면 주문도 회원제한으로 변경       20160324
       if ($_POST['system'] == 'open') $_POST[redirect_url] = ""; //접근권한이 비회원이면 리다이렉팅 주소도 초기화 / 16.08.08 / kjm

       $data = $_POST;

       unset($data[mode]);
       unset($data[x]);
       unset($data[y]);

       //추가 디자인만 json_encode 한번 해준다.
       $data = serialize($data);
	   
	   $m_config->setConfigInfo($cid, $_POST[mode], $data);

   break;
   
   case "pods_module" : 
	   if ($_POST[pod_intro]) $_POST[pod_intro] = addslashes(base64_decode($_POST[pod_intro]));
	   
	   $m_config->setConfigInfo($cid, 'pods_size', $_POST[pods_size]);
	   $m_config->setConfigInfo($cid, 'pod_intro', $_POST[pod_intro]);
	   
	   if (is_uploaded_file($_FILES[pod_logo][tmp_name])) {
	   		$dir = "../../data/pod/";
			if (!is_dir($dir)) {
				mkdir($dir,0707);
				chmod($dir,0707);
			}
			
			$dir = "../../data/pod/$cid/";
			if (!is_dir($dir)) {
				mkdir($dir,0707);
				chmod($dir,0707);
			}
		
            $size = getImageSize($_FILES[pod_logo][tmp_name]);

            if ($size[2] != 3) {
                msg("로고이미지는 png 파일 이여야만 합니다.", -1);
                exit ;
            }

            if ($size[0] > 157) {
                msg("로고이미지의 너비는 최대 157px 이하의 사이즈여야합니다.", -1);
                exit ;
            }

            if ($size[1] > 21) {
                msg("로고이미지의 높이는 최대 21px 이하의 사이즈여야합니다.", -1);
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
				mkdir($dir,0707);
				chmod($dir,0707);
			}
			
			$dir = "../../data/pod/$cid/";
			if (!is_dir($dir)) {
				mkdir($dir,0707);
				chmod($dir,0707);
			}
		
            $size = getImageSize($_FILES[pod_title][tmp_name]);

            if ($size[2] != 3) {
                msg("타이틀이미지는 png 파일 이여야만 합니다.", -1);
                exit ;
            }

            if ($size[0] > 60) {
                msg("타이틀이미지의 너비는 최대 60px 이하의 사이즈여야합니다.", -1);
                exit ;
            }

            if ($size[1] > 11) {
                msg("타이틀이미지의 높이는 최대 11px 이하의 사이즈여야합니다.", -1);
                exit ;
            }
			 
            copy($_FILES[pod_title][tmp_name], "../../data/pod/$cid/pod_title.png");
        }
		   
		if ($_POST[d][pod_title]) {
		    @unlink("../../data/pod/$cid/pod_title.png");
		}	   
	   
   break;
   
   case "self_release" : 
	    $m_config->setConfigInfo($cid, $_POST[mode], $_POST[self_release]);

        if ($_POST[self_release]) {
            $_POST[phone] = implode("-", $_POST[phone]);
            $rid = "_self_$cid";
			
			$m_etc->setReleaseInfo($cid, $rid, $_POST[compnm], $_POST[nicknm], $_POST[name], $_POST[manager], $_POST[phone], $_POST[zipcode], $_POST[address], $_POST[address_sub], $_POST[shiptype], $_POST[shipprice], $_POST[shipconditional], $_POST[oshipprice], $_POST[podsid], $_POST[siteid]);
		}
		
    break;
	
	case "bill_info" :	   
	   	if (!$_POST[bill_yn]) $_POST[bill_yn] = "0";
		if (!$_POST[bill_vat_yn]) $_POST[bill_vat_yn] = "0";
		
    	$flds = array(
    		'bill_yn'			=> $_POST[bill_yn],
    		'bill_vat_yn'		=> $_POST[bill_vat_yn],
    		'bill_payOpt'		=> $_POST[bill_payOpt],
    		'bill_expDt'		=> $_POST[bill_expDt],
    		'bill_nameComp'		=> $_POST[bill_nameComp],
    		'bill_typeBiz'  	=> $_POST[bill_typeBiz],
    		'bill_itemBiz'		=> $_POST[bill_itemBiz],
    		'bill_regnumBiz'	=> $_POST[bill_regnumBiz],
    		'bill_regnumOnline'	=> $_POST[bill_regnumOnline],
    		'bill_nameCeo'		=> $_POST[bill_nameCeo],
    		'bill_managerInfo'	=> $_POST[bill_managerInfo],
    		'bill_zipcode'		=> $_POST[bill_zipcode],
    		'bill_address'		=> $_POST[bill_address],
    		'bill_phoneComp'	=> $_POST[bill_phoneComp],
    		'bill_faxComp'		=> $_POST[bill_faxComp],
    		'bill_Etc'			=> $_POST[bill_Etc],
    	);
    	
    	foreach ($flds as $k => $v) {
    		$m_config->setConfigInfo($cid, $k, $v);
		}
		
		if (is_uploaded_file($_FILES[bill_seal][tmp_name])) {
			$dir = "../../data/bill/";
			if (!is_dir($dir)) {
				mkdir($dir,0707);
				chmod($dir,0707);
			}
			
			$dir = "../../data/bill/$cid/";
			if (!is_dir($dir)) {
				mkdir($dir,0707);
				chmod($dir,0707);
			}
			
            $size = getImageSize($_FILES[bill_seal][tmp_name]);

            if ($size[2] != 3) {
                msg("직인이미지는 png 파일 이여야만 합니다.", -1);
                exit ;
            }

            if ($size[0] > 52) {
                msg("직인이미지의 너비는 최대 52px 이하의 사이즈여야합니다.", -1);
                exit ;
            }

            if ($size[1] > 52) {
                msg("직인이미지의 높이는 최대 52px 이하의 사이즈여야합니다.", -1);
                exit ;
            }
			 
            copy($_FILES[bill_seal][tmp_name], "../../data/bill/$cid/bill_seal.png");
        }
		   
		if ($_POST[d][bill_seal]) {
		    @unlink("../../data/bill/$cid/bill_seal.png");
		}
		
	break;
	
	case "write" : 
		if (!trim($_POST[mid])) {
            msg("아이디가 입력되지 않았습니다.", -1);
            exit ;
        }
		
		$addWhere = "where cid='$cid' and mid='$_POST[mid]'";
		$data = $m_config->getAdminInfo($cid, $_POST[mid], $addWhere);
		
		if ($data[mid]) {
			msg("이미 등록된 아이디 입니다.", -1);
            exit ;
		}
		
		$addColumn = "set cid='$cid',mid='$_POST[mid]',password=md5('$_POST[password]'),name='$_POST[name]',redirect_url='$_POST[redirect_url]',regdt=now()";
		$m_config->setAdminInfo($_POST[mode], $addColumn);
		
		msgNlocationReplace(_("저장 완료되었습니다."), "admin.php");
		
	break;
		
	case "modify" : 
		if ($_POST[password]) $addColumn = "set password=md5('$_POST[password]'),name='$_POST[name]',redirect_url='$_POST[redirect_url]'";
		else $addColumn = "set name='$_POST[name]',redirect_url='$_POST[redirect_url]'";
		
		$addWhere = "where cid='$cid' and mid='$_POST[mid]'";
		$m_config->setAdminInfo($_POST[mode], $addColumn, $addWhere);
		
		msgNlocationReplace(_("저장 완료되었습니다."), "admin.php");

    break;	
	
	case "limited_ip" :
        $_POST[limited_ip] = trim($_POST[limited_ip]);
		
		$m_config->setConfigInfo($cid, $_POST[mode], $_POST[limited_ip]);

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
			"facebook_app_id" => $_POST[facebook_app_id]
		);
		
      	$data[sns_login] = serialize($data[sns_login]);
      	
		$m_config->setConfigInfo($cid, $_POST[mode], $data[sns_login]);
		
	break;
	
	case "emoney_config" :	   
		if (!$_POST[emoney_send_day]) $_POST[emoney_send_day] = "7";
		if (!$_POST[emoney_round]) $_POST[emoney_round] = "0";
		if (!$_POST[emoney_send_type]) $_POST[emoney_send_type] = "G";
		
		if (!$_POST[emoney_send_ratio]) $_POST[emoney_send_ratio] = "0";
		if (!$_POST[emoney_new_member]) $_POST[emoney_new_member] = "0";
		if (!$_POST[emoney_expire_day]) $_POST[emoney_expire_day] = "0";
		if (!$_POST[emoney_min_orderprice]) $_POST[emoney_min_orderprice] = "0";
		if (!$_POST[emoney_use_min]) $_POST[emoney_use_min] = "0";
		if (!$_POST[emoney_use_max]) $_POST[emoney_use_max] = "0";

				
  	$flds = array(
  		'emoney_send_day'			=> $_POST[emoney_send_day],
  		'emoney_round'			=> $_POST[emoney_round],
  		'emoney_send_type'		=> $_POST[emoney_send_type],
  		'emoney_send_ratio'		=> $_POST[emoney_send_ratio],
  		'emoney_new_member'			=> $_POST[emoney_new_member],
  		'emoney_expire_day'  		=> $_POST[emoney_expire_day],
  		'emoney_min_orderprice'			=> $_POST[emoney_min_orderprice],
  		'emoney_use_min'			=> $_POST[emoney_use_min],
  		'emoney_use_max'			=> $_POST[emoney_use_max],    		
  	);
  	
  	foreach ($flds as $k => $v) {
  		$m_config->setConfigInfo($cid, $k, $v, "emoney");
	}		
	break;
	
	//20160810 / minks / 모바일 회원연동 사용설정
	case "mobile_member_use":
		$m_config->setConfigInfo($cid, $_POST[mode], $_POST[mobile_member_use]);
		
	break;
}

msgNlocationReplace(_("저장 완료되었습니다."), $_SERVER['HTTP_REFERER']);

?>