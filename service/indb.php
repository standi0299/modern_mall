<?
include "../lib/library.php";

switch($_REQUEST[mode]) {/***/
   
   case "get_gallery_data":
      
      $m_etc = new M_etc();
      
      //갤러리 조회수 가져오기
      $m_etc->setGalleryViewCnt($_POST[storageid]);
      
      //갤러리 데이터 가져오기
      $data = $m_etc->getGalleryData($_POST[storageid]);

      //갤러리 코멘트 데이터 가져오기
      $comment = $m_etc->getGalleryComment($cid, $_POST[storageid]);

      if($comment){
         $data[comment] = "";

         foreach($comment as $key => $val){
            $data[comment] .= "<ul>";
            $data[comment] .= "<li><span class=\"redas_sp1\" style=\"width:125px; float:left;\">".substr($val[mid], 0, -3)."***</span></li>";
            $data[comment] .= "<li><span class=\"redas_sp2\" style=\"width:100px; float:right; font-size:12px; color:#777\">$val[regdt]</span></li>";
            $data[comment] .= "<li><span class=\"redas_sp3\" style=\"clear:both;\">".nl2br($val[comment])."</span></li>";
            $data[comment] .= "</ul>";
         }
      }

      //이미 찜을 했는지 여부 확인
      $data[goods_wish_chk] = "N";
      $chk_jjim = $m_etc->getJjimStorageid($cid, $sess[mid], $_POST[storageid]);
      if($chk_jjim) $data[goods_wish_chk] = "Y";
      
      $soapurl = 'http://'.PODS20_DOMAIN;
      $soapurl .= "/CommonRef/StationWebService/GetPreViewImgToJson.aspx?";
      $param = "storageid=$_POST[storageid]";
	  
      $ret = readUrlWithcurl($soapurl.$param, false);
      $result[success] = "true";
   
      if ($ret) {
         //fail.
         if (strpos($ret, "fail|") === false) {
            //$result[previewList] = $ret;
            $obj = json_decode($ret, TRUE);
            //debug($obj);
            $result[previewList] = $obj;
            //debug($obj);
            $frame_cnt = 0;
			
			if ($obj) {
	            foreach ($obj as $key => $val) {
	               $frame_cnt += $val[frame_cnt];
	            }
			}
      
            $result[frameCnt] = $frame_cnt;
         } else {
            $result[success] = "false";
            $result[resultMsg] = $ret;
         }
      }
   
      $page = $result[previewList];
      
      if ($page) {
	      foreach($page as $key => $val){
	         if($val[type] == "epilog"){
	            $loop_epilog[] = $val;
	         } else {
	            $loop[] = $val;
	         }
	      }
      }

      if ($loop && $loop_epilog) {
         $previewList = array_merge($loop, $loop_epilog);
	  } else if ($loop) {
	  	 $previewList = $loop;
	  } else if ($loop_epilog) {
	  	 $previewList = $loop_epilog;
	  }

      $data[gallery_layer_preview] = "";
	  
	  if ($previewList) {
	      foreach($previewList as $k => $v){
	         /*if($v[type] == "cover")
	            $data[gallery_layer_preview] .= "<div class=\"hard\" style=\"background-image:url($v[url]})\"></div>";
	         else
	            $data[gallery_layer_preview] .= "<div class=\"double\" style=\"background-image:url($v[url]);background-size:100% 100%;\"></div>";*/
	         
			 $data[width] = $v[width]."px";
			 $data[height] = $v[height]."px";
			 $data[gallery_layer_preview] .= "<img class=\"gallery_img\" src=\"$v[url]\" style=\"position:absolute;\">";
	      }
	  }

      $data[preview_cnt] = count($previewList);
	  
	  $data[mid] = substr($data[mid], 0, -3)."***";

      echo json_encode($data);
      
   exit; break;
   
   case "set_gallery_comment":
      $m_etc = new M_etc();
      $m_etc->setGalleryComment($cid, $sess[mid], $_POST[storageid], $_POST[comment]);
      
      echo "ok";
      exit;
   break;
   
   case "get_gallery_edit_data":
      $goods = new Goods();
      $goods->getView();
      $editor = $goods->editor;
      
      echo json_encode($editor[0]);
      
      exit;
   break;
   
   case "set_jjim_list":
      $m_etc = new M_etc();
      $chk_jjim = $m_etc->getJjimStorageid($cid, $sess[mid], $_POST[storageid]);
      
      if($chk_jjim){
         
         $query = "delete from md_jjim where storageid = '$_POST[storageid]'";
         $db->query($query);
         
         $sql = "update md_gallery set `like` = `like` -1 where storageid = '$_POST[storageid]'";
         $db->query($sql);
         
         $return = "delete";
      } else {
         $query = "insert into md_jjim set
                     cid = '$cid',
                     mid = '$sess[mid]',
                     storageid = '$_POST[storageid]',
                     regdt = now()
                  ";
         $db->query($query);
         
         $sql = "update md_gallery set `like` = `like` +1 where storageid = '$_POST[storageid]'";
         $db->query($sql);
         
         $return = "ok";
      }
      
      echo $return;
      exit;
      
   break;

	# 출석체크 이벤트 처리.
	case "attend_event":
		$today = date("Y-m-d");
		$m_attend = new M_attend_event();

		# 출석체크 이벤트 정보.
		$attend = $m_attend->getInfo($cid);
		//debug($attend);
		if ($attend) {
			/*$emoney = $attend[emoney];
			$sdate = $attend[sdate];
			$edate = $attend[edate];
			$count_tot = $attend[count_tot];
    		$count_seq = $attend[count_seq];
    		$add_emoney = $attend[add_emoney];
    		$emoney_expire_date = $attend[emoney_expire_date];
			$msg1 = $attend[msg1];
    		$msg2 = $attend[msg2];*/
		}
		//exit; break;
		//debug($today);
		# 출석체크 로그 등록 확인.
		$data = $m_attend->getUserTakeToday($cid, $sess[mid], $today);
		//debug($data);
		//exit;
		if(!$data) {
			# 출석체크 로그 등록.
			$m_attend->getIsertUserAttendLog($cid, $sess[mid], date('j'));
			$return_data = $db->id;
			if ($return_data > -1) {
				# 출석체크 포인트 지급.
				setAddEmoney($cid, $sess[mid], $attend[emoney], _('출석체크'), '', '', '', '', $attend[emoney_expire_date]);

				$msg = _("출석체크가 완료되었습니다.");

				if($attend[msg1])
					$msg = $attend[msg1];
				
				# 추가 혜택 확인.
				# 누적 횟수.
				$userTakeCnt = $m_attend->getUserTakeCntMonth($cid, $sess[mid], $attend[sdate], $attend[edate]);
				//debug($userTakeCnt);

				# 연속 횟수.
				$userTakeList = $m_attend->getUserTakeListMonth($cid, $sess[mid], $attend[sdate], $attend[edate]);
				//debug($userTakeList);

				$cntSeqAttendDay = 0;
				$startAttendDay = $userTakeList[0][attend_day];
				//debug($startAttendDay);
				
				foreach ($userTakeList as $key => $val) {
					if($startAttendDay == $val[attend_day]) {
						$cntSeqAttendDay++;
						$startAttendDay++;
					}
					else break;
				}
				
				//debug("cntSeqAttendDay : ".$cntSeqAttendDay);
				//debug("startAttendDay : ".$startAttendDay);

				# 출석체크 포인트 지급 (추가 혜택).
				if($attend[count_tot] <= $userTakeCnt && $attend[count_seq] <= $cntSeqAttendDay) {
					# 출석체크 포인트 지급 (추가 혜택).
					setAddEmoney($cid, $sess[mid], $attend[add_emoney], _('출석체크 추가 혜택'), '', '', '', '', $attend[emoney_expire_date]);
					
					if($attend[msg2])
						$msg += "\n\n".$attend[msg2];
				}
				
				msg($msg, '/service/attend_event.php');
			}
			else 
				msg(_("출석체크에 실패했습니다. 관리자에게 문의하세요."), '/service/attend_event.php');
		}
		else {
			msg(_("이미 출석체크를 하셨습니다."), '/service/attend_event.php');
		}
		
		break;

   case "api_login" :

      $b2b[mid]     = $_POST[mid];
      $b2b[name]    = $_POST[name];
      if($cfg[skin_theme] == "I1")
         $b2b[address] = "";
      else
         $b2b[address] = $_POST[address];

      $query = "
      insert into exm_member set
         cid     = '$cid',
         mid     = '$b2b[mid]',
         name    = '$b2b[name]',     
         regdt   = now(),
         sort    = -UNIX_TIMESTAMP()
      ";

      $db->query($query);

      _member_login($b2b);
      if($cfg[skin_theme] == "I1")
         msg(_("등록 되었습니다."), $_POST[move_url]);
      else
         msg(_("등록 되었습니다."), '/member/myinfo.php');
   break;

   //sns 연동 로그인 회원정보 저장하기  / 17.03.03 / kdk
	case "sns_login" :
    	$sns[mid] = $_POST[mid];
		$sns[id] = $_POST[id];
   		$sns[name] = $_POST[name];
   		$sns[type] = $_POST[type];
		//$sns[email] = $_POST[email];
		$sns[nickname] = $_POST[nickname];

		if ($sns[name] == "") $sns[name] = $sns[nickname];
		if ($_POST[email]) $sns[email] = @implode("@", array_notnull($_POST[email]));
		if ($_POST[mobile]) $sns[mobile] = @implode("-", array_notnull($_POST[mobile]));
		
		if (!$_POST[ismail]) $_POST[ismail] = 0;
		if (!$_POST[issms]) $_POST[issms] = 0;

		//$memo = "sns:$sns[type],id:$sns[id],name:$sns[name],email:$sns[email],nickname:$sns[nickname]";

   	$query = "
   	insert into exm_member set
     	cid = '$cid',
      	mid = '$sns[mid]',
      	name = '$sns[name]',
      	email = '$sns[email]',
      	apply_email = '$_POST[ismail]',
      	mobile = '$sns[mobile]',
      	apply_sms = '$_POST[issms]',
      	regdt = now(),
      	sort = -UNIX_TIMESTAMP(),
      	sns_id = '$sns[id]',
      	cntlogin = 1,
      	lastlogin = now() 
   	";

   	$db->query($query);

   	$_COOKIE[sess] = _member_login($sns);
   	$sess = getAuthMember();

		$m_member = new M_member();
		$data = $m_member->getInfoWithGrp($cid, $_POST[mid]);
		
		//적립금 지급처리.
		setAddNewMember($cid, $sns[mid]);
		memberRegistCouponSend($sns[mid]);		//회원가입 쿠폰 처리.		20180323		chunter

		//메일보내기.
		autoMail("register", $data[email], $data);

		//관리자에게 보내기.
		autoMailAdmin("admin_register", $cfg[email1], $data);

		//문자보내기.
		autoSms(_("회원가입"), $data[mobile], $data);

      msg(_("등록 되었습니다."), '/main/index.php'); ///member/myinfo.php
   	break;
   
   /***/
}/***/

if (!$_POST[rurl])
   $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
?>