<?

include "../lib/library.php";

$m_board = new M_board();
$m_etc = new M_etc();
$m_member = new M_member();

switch ($_GET[mode]) {
	case "coupon":
		if ($sess[mid]) {
			$data = $m_etc->getCouponInfo($cid, $_GET[coupon_code]);

			if (!$data[coupon_code]) {
				if ($_GET[mobile_type] == "Y") echo _("존재하지 않는 쿠폰입니다.");
				else msg(_("존재하지 않는 쿠폰입니다."), "coupon.php");
				exit;
			}

			$addWhere = "where cid = '$cid' and coupon_code = '$_GET[coupon_code]' and mid = '$sess[mid]'";
			$chk = $m_etc->getCouponSetInfo($cid, $addWhere);

			if ($chk[no]) {
				if ($_GET[mobile_type] == "Y") echo _("이미 지급 받으셨던 쿠폰입니다.");
				else msg(_("이미 지급 받으셨던 쿠폰입니다."), "coupon.php");
				exit;
			}

			if ($data[coupon_issue_system] != "download") {
				if ($_GET[mobile_type] == "Y") echo _("다운로드가 불가능한 쿠폰입니다.");
				else msg(_("다운로드가 불가능한 쿠폰입니다."), "coupon.php");
				exit;
			}

			if ($data[coupon_issue_unlimit] == 0 && ($data[coupon_issue_sdate] > date("Y-m-d") || $data[coupon_issue_edate] < date("Y-m-d"))) {
				if ($_GET[mobile_type] == "Y") echo _("다운로드를 받을 수 있는 일자가 아닙니다.");
				else msg(_("다운로드를 받을 수 있는 일자가 아닙니다."), "coupon.php");
				exit;
			}

			if ($data[coupon_issue_ea_limit] == 1) {
				$addWhere2 = "where cid = '$cid' and coupon_code = '$data[coupon_code]'";
				$chk2 = $m_etc->getSoldCouponCnt($cid, $addWhere2);

				if ($chk2 >= $data[coupon_issue_ea]) {
					if ($_GET[mobile_type] == "Y") echo _("발행된 쿠폰이 모두 소진되었습니다.");
					else msg(_("발행된 쿠폰이 모두 소진되었습니다."), "coupon.php");
					exit;
				}
			}

			if ($data[coupon_type] == "coupon_money") {
				$coupon_able_money = $data[coupon_price]+0;

				$addWhere3 = "where cid = '$cid' and coupon_code = '$_GET[couponno]' and mid = '$sess[mid]' order by no desc limit 1";
				$data2 = $m_etc->getCouponSetInfo($cid, $addWhere3);

				$chk_coupon = $data2[no];
				$coupon_setdt = $data2[coupon_setdt];

				switch ($data[coupon_period_system]) {
					case "deadline":
						if (date("Ymd", strtotime($coupon_setdt." + ".$data[coupon_period_deadline]." days")) < date("Ymd")) {
							unset($chk_coupon);
						}
						break;
				}
			}

			if ($data[coupon_type] == "coupon_money" && $chk_coupon) {
				$addColumn = "set 
					coupon_use			= 0,
					coupon_able_money 	= coupon_able_money + $coupon_able_money,
					coupon_setdt 		= now()";
				$addWhere4 = "where no = '$chk_coupon'";
				$m_etc->setCouponSetInfo($chk_coupon, $addColumn, $addWhere4);
			} else {
				$addColumn2 = "set
					cid					= '$cid',
					coupon_code			= '$_GET[coupon_code]',
					mid					= '$sess[mid]',
					coupon_able_money	= '$coupon_able_money',
					coupon_setdt		= now()";
				$m_etc->setCouponSetInfo("", $addColumn2);
			}

			if ($_GET[mobile_type] == "Y") echo _("쿠폰이 등록되었습니다.");
			else msg(_("쿠폰발급이 완료되었습니다."), "coupon.php");
		} else {
			if ($_GET[mobile_type] == "Y") echo _("회원만 쿠폰 등록이 가능합니다.");
			else msg(_("회원만 쿠폰 발급이 가능합니다."), "../member/login.php");
		}

	exit; break;

	//적립쿠폰 메세지 처리후 처리			20160419			chunter
	case "coupon_get_offline":
		$_POST[mode] = "coupon_get_offline";
		$_POST[coupon_issue_code] = $_GET[coupon_issue_code];

	break;

	case "del_each_wishlist":
		if (!$_GET[no]) {
			msg(_("삭제할 찜 상품이 없습니다."), "wishlist.php");
			exit;
		}

		$m_member->delWishInfo($cid, $sess[mid], $_GET[no]);

		msg(_("찜 상품이 삭제되었습니다."), "wishlist.php");

	exit; break;

	case "cancelPayInfo":
	   $m_order = new M_order();
	   $data = $m_order->getPayInfo($_GET[payno]);

	   if ($data) {
		   $nowtime = strtotime(date("Y-m-d H:i:s"), time());
		   if ($data[paydt]) $gabtime = floor(($nowtime - strtotime($data[paydt])) / 3600);
		   else if ($data[confirmdt]) $gabtime = floor(($nowtime - strtotime($data[confirmdt])) / 3600);
		   else $gabtime = 0;

		   if ($gabtime == 0) {
		   	  orderCancel($_GET[payno]);

			  //카드외 결제수단일 경우 환불받을 계좌정보를 알아야 환불해줄 수 있음
			  if ($_GET[refund_bank] && $_GET[refund_account_number]) {
			  	 $memo = _("환불정보")." : ".$r_inicis_bank_name[$_GET[refund_bank]]." / ".$_GET[refund_account_number];
			  	 $m_order->setPayRefundInfo($cid, $_GET[payno], $memo);

			  	 msg(_("주문을 취소요청하였습니다."), "orderlist.php");
			  } else {
			  	 msg(_("주문을 취소하였습니다."), "orderlist.php");
			  }
		   } else {
		   	  //주문접수완료 후 1시간 이전에만 주문취소 가능
		   	  msg(_("이미 상품이 제작중이거나 제작완료됐으므로 주문을 취소할 수 없습니다."), "orderlist.php");
		   }
	   } else {
	   	  msg(_("주문정보가 없습니다."), "orderlist.php");
	   }
	exit; break;
}

switch ($_POST[mode]) {
   case "addCs":
      if ($sess[mid]) {
         $_POST[subject] = addslashes($_POST[subject]);
         $_POST[content] = addslashes($_POST[content]);

         $tableName = "exm_mycs";
         $addColumn = "set 
         id       = 'cs',
         cid      = '$cid',
         mid      = '$sess[mid]',
         name     = '$sess[name]',
         category = '$_POST[category]',
         payno    = '$_POST[payno]',
         subject  = '$_POST[subject]',
         content  = '$_POST[content]',
         regdt    = now()";

         $m_board->setCustomerService("insert", $tableName, $addColumn);
         $no = mysql_insert_id();

         $dir = "../data/board/";
         if (!is_dir($dir)) {
            mkdir($dir, 0707);
            chmod($dir, 0707);
         }

         $dir = "../data/board/$cid/";
         if (!is_dir($dir)) {
            mkdir($dir, 0707);
            chmod($dir, 0707);
         }

         $dir = "../data/board/$cid/cs/";
         if (!is_dir($dir)) {
            mkdir($dir, 0707);
            chmod($dir, 0707);
         }


         if (is_uploaded_file($_FILES[file][tmp_name])) {
            $ext = substr(strrchr($_FILES[file][name], "."), 1);
            $fname = time().rand(0, 9999).".".$ext;

			// 확장자가 jpg,png,pdf가 아닐시 업로드 불가
			// 허용 확장자
			$ext_allow = array("jpeg","jpg","png","pdf");
			// 확장자 소문자 처리
			$ext_lower = strtolower($ext);

			if (in_array($ext_lower,$ext_allow)){
				move_uploaded_file($_FILES[file][tmp_name], $dir.$fname);

				$addColumn2 = "set img = '$fname'";
				$addWhere = "where no = '$no'";
				$m_board->setCustomerService("update", $tableName, $addColumn2, $addWhere);
			}else{
				msg(_("이미지는 jpg,png,pdf 형태만 업로드 가능합니다."), "mycs_write.php");
				exit;
			}
         }

         if ($_FILES[file]) {
            $dir = "../data/board/$cid/mycs/";
            if (!is_dir($dir)) {
               mkdir($dir, 0707);
               chmod($dir, 0707);
            }

            $file_path = substr($dir, 2);     //경로중 "".." 문자열 제거     20140624  chunter

            if (is_uploaded_file($_FILES[file][tmp_name])) {
               $filesrc = $no."_".time().rand(0, 9999);
               $filename = $_FILES[file][name][$k];
			   $filesize = $_FILES[file][size][$k];


               if ($filesize > $board[limit_filesize]*1024 && $board[limit_filesize]) {
                  continue;
			   }

               move_uploaded_file($_FILES[file][tmp_name], $dir.$filesrc);

               $tableName2 = "exm_mycs_file";
               $addColumn3 = "set 
               pno       = '$no',
               filename  = '$filename',
               filesrc   = '$filesrc',
               filesize  = '$filesize',
               file_path = '$file_path'";

               $m_board->setCustomerService("insert", $tableName2, $addColumn3);
            }
         }

         if ($_POST[mobile_type] == "Y") {
            echo "ok";
            exit;
         }
      } else {
         if ($_POST[mobile_type] == "Y") echo _("로그인 세션이 만료되었습니다.");
         else msg(_("로그인 세션이 만료되었습니다."), "mycs.php");
         exit;
      }

   break;

	case "addCs_M2":
		if ($sess[mid]) {
			$_POST[subject] = addslashes($_POST[subject]);
			$_POST[content] = addslashes($_POST[content]);

			$tableName = "exm_mycs";
			$addColumn = "set 
			id			= 'cs',
			cid		= '$cid',
			mid		= '$sess[mid]',
			name		= '$sess[name]',
			category	= '$_POST[category]',
			payno		= '$_POST[payno]',
			subject	= '$_POST[subject]',
			content	= '$_POST[content]',
			regdt		= now()";

			$m_board->setCustomerService("insert", $tableName, $addColumn);
			$no = mysql_insert_id();

			$dir = "../data/board/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$dir = "../data/board/$cid/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$dir = "../data/board/$cid/cs/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}
         $file_path = substr($dir, 2);     //경로중 "".." 문자열 제거     20140624  chunter
         //debug($_FILES); exit;
         if($_FILES){
            foreach ($_FILES[img][tmp_name] as $k => $v) {

               if (is_uploaded_file($v)) {

                  $ext = substr(strrchr($_FILES[img][name][$k], "."), 1);
                  $fname = time().rand(0, 9999);
                  $filename = $_FILES[img][name][$k];
                  $filesize = $_FILES[img][size][$k];
                  $filesrc = $fname.".".$ext;

                  move_uploaded_file($v, $dir.$filesrc);

                  $addColumn2 = "set img = '$fname'";
                  $addWhere = "where no = '$no'";
                  $m_board->setCustomerService("update", $tableName, $addColumn2, $addWhere);

                  $tableName2 = "exm_mycs_file";
                  $addColumn3 = "set 
                  pno       = '$no',
                  filename  = '$filename',
                  filesrc   = '$filesrc',
                  filesize  = '$filesize',
                  file_path = '$file_path'";

                  $m_board->setCustomerService("insert", $tableName2, $addColumn3);
               }
            }
         }
      }


	break;

	case "modCs":
		if ($sess[mid]) {
			$_POST[subject] = addslashes($_POST[subject]);
			$_POST[content] = addslashes($_POST[content]);

			$tableName = "exm_mycs";
			$addColumn = "set 
			category	= '$_POST[category]',
			payno		= '$_POST[payno]',
			subject 	= '$_POST[subject]',
			content 	= '$_POST[content]'";
			$addWhere = "where no = '$_POST[no]'";

			$m_board->setCustomerService("update", $tableName, $addColumn, $addWhere);

			$dir = "../data/board/$cid/cs/";
			$data = $m_board->getCustomerServiceInfo($cid, $tableName, $addWhere);
			$_file = $data[img];

			if ($_FILES[file][tmp_name] || $_POST[delfile]) {
				$ext = substr(strrchr($_FILES[file][name], "."), 1);
				$fname = time().rand(0, 9999).".".$ext;
				if($_file) unlink($dir.$_file);

				$addColumn2 = "set img = ''";
				$m_board->setCustomerService("update", $tableName, $addColumn2, $addWhere);
			}

			if (is_uploaded_file($_FILES[file][tmp_name])) {
				move_uploaded_file($_FILES[file][tmp_name], $dir.$fname);

				$addColumn3 = "set img = '$fname'";
				$m_board->setCustomerService("update", $tableName, $addColumn3, $addWhere);
			}

			if ($_POST[mobile_type] == "Y") {
				echo "ok";
				exit;
			}
		} else {
			if ($_POST[mobile_type] == "Y") echo _("로그인 세션이 만료되었습니다.");
			else msg(_("로그인 세션이 만료되었습니다."), "mycs.php");
			exit;
		}

	break;

	case "excel_down":
		### form 전송 취약점 개선 20160128 by kdk
		if(isset($_POST[pod_signed]) && isset($_POST[pod_expired])) {
			if(exp_compare($_POST[pod_expired])) {
				$url_query = $_SERVER[REQUEST_URI]."?query=".$_POST[query];
				$signedData = signatureData($cid, $url_query);

				if (sig_compare($signedData, $_POST[pod_signed])) {
					$_POST[query] = urldecode(base64_decode($_POST[query]));
				} else {
					msg(_("서버키 검증에 실패했습니다."), $_SERVER[HTTP_REFERER]);
					exit;
				}
			} else {
				msg(_("서버키가 만료되었습니다."), $_SERVER[HTTP_REFERER]);
				exit;
			}
		} else {
			$_POST[query] = urldecode(base64_decode($_POST[query]));
		}
		### form 전송 취약점 개선 20160128 by kdk

		header("Content-Type: application/vnd.ms-excel;charset=utf-8");
		header("Content-Disposition: attachment; filename=orderlist_".date("YmdHi").".xls");
		header("Content-Description: PHP5 Generated Date");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");

		##한글 깨짐현상 방지
		print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

		$r_rid_x = get_release();

		//$query = base64_decode($_POST[query]);### form 전송 취약점 개선 20160128 by kdk
		//$res = $db->query($query);
		$res = $db->query($_POST[query]);

		while ($data = $db->fetch($res)) {
			if ($data[addopt]) $data[addopt] = unserialize($data[addopt]);
			if ($data[printopt]) $data[printopt] = unserialize($data[printopt]);
			if (!$loop[$data[payno]]) $loop[$data[payno]] = $data;
			if (!$loop[$data[payno]]['ord'][$data[ordno]]) $loop[$data[payno]]['ord'][$data[ordno]] = $data;

			if ($data[shipcode] && $data[itemstep] >= 5 && $data[itemstep] < 10) {
				$data[strshipcode] = "<div class='col'>".$r_shipcomp[$data[shipcomp]][compnm]."<br><a href='".$r_shipcomp[$data[shipcomp]][url].$data[shipcode]."' target='_blank'>".$data[shipcode]."</a></div>";
			}

			$loop[$data[payno]]['ord'][$data[ordno]]['item'][$data[ordseq]] = $data;
			$loop[$data[payno]][rowspan]++;
			$loop[$data[payno]]['ord'][$data[ordno]][rowspan]++;
		}

		include "inc.excel.php";

	exit; break;

	case "coupon_get_offline":
		$addWhere = "and coupon_issue_system = 'download_write'";
		$data = $m_etc->getCouponInfo($cid, $_POST[coupon_issue_code], $addWhere);
		if ($data[coupon_code]) {
			$download_write = true;

			if ($data[coupon_issue_ea_limit] == 1) {
				$addWhere2 = "where cid = '$cid' and coupon_code = '$data[coupon_code]'";
				$chk2 = $m_etc->getSoldCouponCnt($cid, $addWhere2);

				if ($chk2 >= $data[coupon_issue_ea]) {
					if ($_POST[mobile_type] == "Y")	echo _("발행된 쿠폰이 모두 소진되었습니다.");
					else msg(_("발행된 쿠폰이 모두 소진되었습니다."), "coupon.php");
					exit;
				}
			}

			$addWhere3 = "where cid = '$cid' and coupon_code = '$data[coupon_code]' and mid = '$sess[mid]'";
			$chk3 = $m_etc->getCouponSetInfo($cid, $addWhere3);

			if ($chk3[no]) {
				if ($_POST[mobile_type] == "Y")	echo _("이미 발급완료된 쿠폰입니다.");
				else msg(_("이미 발급완료된 쿠폰입니다."), "coupon.php");
				exit;
			}
		} else {
			/*if (is_array($_POST[coupon_issue_code])) $coupon_issue_code = @implode("-", $_POST[coupon_issue_code]);
			else $coupon_issue_code = $_POST[coupon_issue_code];*/

      $coupon_issue_code = $_POST[coupon_issue_code];

      if(strlen($coupon_issue_code)=="16"){  //kkwon 2020.07.03 쿠폰입력시 "-" 없이 입력하는 경우
        $coupon_issue_code = $_POST[coupon_issue_code] = substr($_POST[coupon_issue_code],0,5)."-".substr($_POST[coupon_issue_code],5,6)."-".substr($_POST[coupon_issue_code],11);
      }

			if (strlen($coupon_issue_code) != "18") {
				if ($_POST[mobile_type] == "Y")	echo _("쿠폰의 코드형식이 맞지 않습니다.");
				else msg(_("쿠폰의 코드형식이 맞지 않습니다."), "coupon.php");
				exit;
			}

			$issue = $m_etc->getCouponIssueInfo($cid, $coupon_issue_code);

			if (!$issue[coupon_issue_code]) {
				if ($_POST[mobile_type] == "Y") echo _("유효하지 않은 쿠폰입니다.");
				else msg(_("유효하지 않은 쿠폰입니다."), "coupon.php");
				exit;
			}

			if ($issue[coupon_issue_yn] == 1) {
				if ($_POST[mobile_type] == "Y") echo _("이미 발급완료된 쿠폰입니다.");
				else msg(_("이미 발급완료된 쿠폰입니다."), "coupon.php");
				exit;
			}

			$data = $m_etc->getCouponInfo($cid, $issue[coupon_code]);
		}

		if (!$data[coupon_code]) {
			if ($_POST[mobile_type] == "Y") echo _("쿠폰이 삭제되었거나 정보가 존재하지 않습니다.");
			else msg(_("쿠폰이 삭제되었거나 정보가 존재하지 않습니다."), "coupon.php");
			exit;
		}

		if ($data[coupon_issue_system] != "download" && $data[coupon_issue_system] != "download_write") {
			if ($_POST[mobile_type] == "Y") echo _("다운로드가 불가능한 쿠폰입니다.");
			else msg(_("다운로드가 불가능한 쿠폰입니다."), "coupon.php");
			exit;
		}

		if ($data[coupon_issue_unlimit] == 0 && ($data[coupon_issue_sdate] > date("Y-m-d") || $data[coupon_issue_edate] < date("Y-m-d"))) {
			if ($_POST[mobile_type] == "Y") echo _("다운로드를 받을 수 있는 일자가 아닙니다.");
			else msg(_("다운로드를 받을 수 있는 일자가 아닙니다."), "coupon.php");
			exit;
		}

		if ($data[coupon_type] == "coupon_money") {
			$coupon_able_money = $data[coupon_price]+0;

			$addWhere4 = "where cid = '$cid' and coupon_code = '$data[coupon_code]' and mid = '$sess[mid]' order by no desc limit 1";
			$data2 = $m_etc->getCouponSetInfo($cid, $addWhere4);

			$chk_coupon = $data2[no];
			$coupon_setdt = $data2[coupon_setdt];

			switch ($data[coupon_period_system]) {
				case "deadline":
					if (date("Ymd", strtotime($coupon_setdt." + ".$data[coupon_period_deadline]." days")) < date("Ymd")) {
						unset($chk_coupon);
					}
					break;
			}
		}

		$addColumn = "set
				cid							= '$cid',
				coupon_code					= '$data[coupon_code]',
				mid							= '$sess[mid]',
				coupon_able_money			= '$coupon_able_money',
				coupon_setdt				= now(),
				coupon_issue_code			= '$issue[coupon_issue_code]',
				coupon_issue_code_history	= '$issue[coupon_issue_code]'";

		if (!$download_write) {
			//적립금 전환 쿠폰의 경우 교환 불가 메세지를 띄우고 처리한다.			//20160419		chunter
			if ($data[coupon_type] == "point_save") {
				if ($_GET[mode_sub] == "p_save") {
					//적립금 전환처리.
					//set_emoney($sess[mid], "쿠폰 적립금 전환"."[".$coupon_issue_code."]", $data[coupon_price]);
					setAddEmoney($cid, $sess[mid], $data[coupon_price], _("쿠폰적립금전환")." [$coupon_issue_code]");
					$addColumn .= ",coupon_use = '1', coupon_usedt=now(), coupon_memo = '$data[coupon_price] point'";
				} else {
					msg_confirm(_("적립금으로 적립됩니다.")."\\n"._("등록후 취소가 불가능합니다.")."\\n"._("등록하시겠습니까?")."[".$data[coupon_price]."Point]", "location.href='indb.php?mode=" .$_POST[mode]. "&coupon_issue_code=" .$coupon_issue_code. "&mode_sub=p_save'", "location.href='coupon.php'");
					exit;
				}
			}
		}

		if ($data[coupon_type] == "coupon_money" && $chk_coupon) {
			$addColumn2 = "set 
				coupon_use					= 0,
				coupon_able_money 		= coupon_able_money + $coupon_able_money,
				coupon_setdt 				= now(),
				coupon_issue_code			= '$issue[coupon_issue_code]',
				coupon_issue_code_history	= concat_ws('|',coupon_issue_code_history,'$issue[coupon_issue_code]')";
			$addWhere5 = "where no = '$chk_coupon'";
			$m_etc->setCouponSetInfo($chk_coupon, $addColumn2, $addWhere5);
		} else {
			$m_etc->setCouponSetInfo("", $addColumn);
		}

		if (!$download_write) {
			$addColumn3 = "set mid = '$sess[mid]', coupon_issuedt = now(), coupon_issue_yn = '1'";
			$addWhere6 = "where coupon_issue_code = '$issue[coupon_issue_code]'";
			$m_etc->setCouponIssueInfo($cid, $addColumn3, $addWhere6);
		}

		if ($_POST[mobile_type] == "Y") {
			echo _("쿠폰이 등록되었습니다.");
		} else {
			if ($data[coupon_type] == "point_save") msg(_("쿠폰이 등록되었습니다."), "coupon.php");
			else msg(_("쿠폰발급이 완료되었습니다."), "coupon.php");
		}

	exit; break;

	case "del_wishlist":
		if (!$_POST[no]) {
			msg(_("삭제할 찜 상품을 선택해주세요."), "wishlist.php");
			exit;
		}

		$no = implode(",", $_POST[no]);

		$m_member->delWishInfo($cid, $sess[mid], $no);

		msg(_("찜 상품이 삭제되었습니다."), "wishlist.php");

	exit; break;

   case "get_coupon_apply_list" :

      $m_etc = new M_etc();
      $m_goods = new M_goods();

      $data = $m_etc->getCouponInfo($cid, $_POST[coupon_code]);

      if ($data[coupon_range] == "category") {
         $data[coupon_catno] = explode(",", $data[coupon_catno]);

         $table = "";
         $table .= "<a href=\"#\" class=\"close\">닫기</a>";
         $table .= "<div class=\"pop-top\">";
         $table .= "<h2>쿠폰 적용가능 상품</h2>";
         $table .= "</div>";

         $table .= "<div class=\"inner\">
                    <ul class=\"view-product-list\">";

         foreach($data[coupon_catno] as $key => $val){
            $table .= "<li><a href=\"#\" disabled=\"disabled\">".getCatnm($val)."</a></li>";
         }

         $table .= "
         </ul>
            
         </div>";

      } else if ($data[coupon_range] == "all"){
         $table = "all";
      }

      echo $table;
   exit;
   break;

   case "modDelivery":
	   $m_order = new M_order();
	   $data = $m_order->getPayInfo($_POST[payno]);

	   if ($data) {
		   $nowtime = strtotime(date("Y-m-d H:i:s"), time());
		   if ($data[paydt]) $gabtime = floor(($nowtime - strtotime($data[paydt])) / 3600);
		   else if ($data[confirmdt]) $gabtime = floor(($nowtime - strtotime($data[confirmdt])) / 3600);
		   else $gabtime = 0;

		   if ($gabtime == 0) {
		   	  $_POST[receiver_mobile] = implode("-", $_POST[receiver_mobile]);
		   	  $m_order->setPayDeliveryInfo($_POST[payno], $_POST[receiver_name], $_POST[receiver_mobile], $_POST[receiver_zipcode], $_POST[receiver_addr], $_POST[receiver_addr_sub], $_POST[request]);

		   	  $item = $m_order->getOrdItemList($_POST[payno]);
			  foreach ($item as $k=>$v) {
			  	 if ($v[storageid] && $v[pods_trans] == '1' && in_array($v[itemstep], array(2,3,4,5,92))) {
			  	 	$podsApi = new PODStation('20');

					$pod_data[storageId] 	  = $v[storageid];
					$pod_data[orderUserName]  = $data[orderer_name];
					$pod_data[orderUserPost]  = $data[orderer_zipcode];
					$pod_data[orderUserAddr1] = trim($data[orderer_addr]);
					$pod_data[orderUserAddr2] = trim($data[orderer_addr_sub]);
					$pod_data[orderUserTel]   = $data[orderer_phone];
					$pod_data[orderUserHp] 	  = $data[orderer_mobile];
					$pod_data[orderUserEmail] = $data[orderer_email];
					$pod_data[receiverName]   = $_POST[receiver_name];
					$pod_data[receiverPost]   = $_POST[receiver_zipcode];
					$pod_data[receiverAddr1]  = trim($_POST[receiver_addr]);
					$pod_data[receiverAddr2]  = trim($_POST[receiver_addr_sub]);
					$pod_data[receiverTel]    = $data[receiver_phone];
					$pod_data[receiverHp] 	  = $_POST[receiver_mobile];
					$pod_data[receiverMemo]   = $_POST[request];
					$pod_data[adminMemo] 	  = "";
					$pod_data[receiverMemo2]  = $data[request2];

					$podsApi->SetOrderInfoDelivery($pod_data);
			  	 }
			  }

			  msg(_("배송정보가 변경되었습니다."), "orderview.php?payno=".$_POST[payno]);
		   } else {
		   	  //주문접수완료 후 1시간 이전에만 배송정보 변경 가능
		   	  msg(_("이미 상품이 제작중이거나 제작완료됐으므로 배송정보를 변경할 수 없습니다."), "orderview.php?payno=".$_POST[payno]);
		   }
	   } else {
	   	  msg(_("주문정보가 없으므로 배송정보를 변경할 수 없습니다."), "orderview.php?payno=".$_POST[payno]);
	   }

   exit; break;

   case "document":
	   if ($_POST[document_type] == "CRD" || $_POST[document_type] == "CRE" || $_POST[document_type] == "TI") {
	   	  if ($_POST[document_type] == "CRD") {
					if ($_POST[mobile]) $_POST[mobile] = implode("-", $_POST[mobile]);
					if ($_POST[card_num]) $_POST[card_num] = implode("-", $_POST[card_num]);
	   	  } else if ($_POST[document_type] == "CRE") {
	   	   	  if ($_POST[licensee_num]) $_POST[licensee_num] = implode("-", $_POST[licensee_num]);


				if($_FILES[document_file][tmp_name]) {
					$fname = "";

					$dir = "../data/document/";
					if (!is_dir($dir)) {
						mkdir($dir, 0707);
						chmod($dir, 0707);
					}

					$dir = "../data/document/$cid/";
					if (!is_dir($dir)) {
						mkdir($dir, 0707);
						chmod($dir, 0707);
					}

						//첨부파일 유효성 체크
						//if ($_FILES[document_file][tmp_name]) { //여기 오류???
							$info = getimagesize($_FILES[document_file][tmp_name]);
							$fileExt = substr(strrchr($_FILES[document_file][name], "."), 1);
							if ( !(in_array($info[2], array(1, 2, 3)) || $fileExt == 'pdf')) {
								msgNlocationReplace(_(" $fileExt 파일 확장자가 gif,jpg,png,pdf 인 파일만 업로드할 수 있습니다."), "document.php?document_type=".$_POST[document_type], "Y");
								exit;
							}

							if (is_uploaded_file($_FILES[file][tmp_name])) {
								$filesrc = $no."_".time().rand(0, 9999);
								$filename = $_FILES[file][name][$k];
								$filesize = $_FILES[file][size][$k];


								if ($filesize > $board[limit_filesize]*1024 && $board[limit_filesize]) {
									continue;
								}

								move_uploaded_file($_FILES[file][tmp_name], $dir.$filesrc);

							$size = filesize($_FILES[document_file][tmp_name]) / 1024 / 1024;
							if ($size > 10) {
								msgNlocationReplace(_("파일 용량이 10MB미만의 파일만 업로드할 수 있습니다."), "document.php?document_type=".$_POST[document_type], "Y");
								exit;
							}
						}

						if ($_FILES[document_file][tmp_name] || $_POST[file_delete]) {
							//기존 첨부파일 삭제
							if ($_POST[old_document_file]) {
								unlink($dir.$_POST[old_document_file]);
							}

							//첨부파일 업로드
							if ($_FILES[document_file][tmp_name] && !$_POST[file_delete]) {
								$ext = substr(strrchr($_FILES[document_file][name], "."), 1);
								$fname = time().rand(0, 9999).".".$ext;

								move_uploaded_file($_FILES[document_file][tmp_name], $dir.$fname);
							}
						}
					}

	   	   	} else if ($_POST[document_type] == "TI") {
	   	   	  if ($_POST[licensee_num]) $_POST[licensee_num] = implode("-", $_POST[licensee_num]);
			  		$fname = "";

						$dir = "../data/document/";
						if (!is_dir($dir)) {
								mkdir($dir, 0707);
							chmod($dir, 0707);
						}

						$dir = "../data/document/$cid/";
						if (!is_dir($dir)) {
								mkdir($dir, 0707);
							chmod($dir, 0707);
						}

						//첨부파일 유효성 체크
						if ($_FILES[document_file][tmp_name]) {
							$info = getimagesize($_FILES[document_file][tmp_name]);
							$fileExt = substr(strrchr($_FILES[document_file][name], "."), 1);

							if ( !(in_array($info[2], array(1, 2, 3)) || $fileExt == 'pdf')) {
									msgNlocationReplace(_("파일 확장자가 gif,jpg,png 인 파일만 업로드할 수 있습니다."), "document.php?document_type=".$_POST[document_type], "Y");
								exit;
							}

							$size = filesize($_FILES[document_file][tmp_name]) / 1024 / 1024;
							if ($size > 10) {
									msgNlocationReplace(_("파일 용량이 10MB미만의 파일만 업로드할 수 있습니다."), "document.php?document_type=".$_POST[document_type], "Y");
								exit;
							}
						}

						if ($_FILES[document_file][tmp_name] || $_POST[file_delete]) {
								//기존 첨부파일 삭제
							if ($_POST[old_document_file]) {
								unlink($dir.$_POST[old_document_file]);
							}

							//첨부파일 업로드
							if ($_FILES[document_file][tmp_name] && !$_POST[file_delete]) {
									$ext = substr(strrchr($_FILES[document_file][name], "."), 1);
									$fname = time().rand(0, 9999).".".$ext;
									move_uploaded_file($_FILES[document_file][tmp_name], $dir.$fname);
							}
						}
	   	   	}

		   if ($_POST[email]) $_POST[email] = implode("@", $_POST[email]);

		   $m_member->setDocumentInfo($cid, $sess[mid], $_POST[document_type], $_POST[payno], $_POST[mobile], $_POST[email], $_POST[card_num], $_POST[licensee_num], $fname, "");

		   msgNlocationReplace(_("서류발급이 신청되었습니다."), "document.php?document_type=".$_POST[document_type], "Y");
		} else {
				msgNlocationReplace(_("서류발급신청 종류가 유효하지 않습니다."), "document.php", "Y");
		}

   exit; break;

   case "bigorder_request":
	   if ($_POST[category] == "quotation" || $_POST[category] == "sample" || $_POST[category] == "marketing") {
	   	   $goodsnm_arr = explode("|^|", $_POST[goodsnm]);

		   if ($_POST[category] == "quotation") $_POST[goodsnm] = $goodsnm_arr[1]." ".$_POST[sub_goodsnm_1];
		   else if ($_POST[category] == "sample") $_POST[goodsnm] = $goodsnm_arr[1]." ".$_POST[sub_goodsnm_2];

		   if (!$_POST[ea]) $_POST[ea] = 0;
		   if ($_POST[request_mobile]) $_POST[request_mobile] = implode("-", $_POST[request_mobile]);
		   if ($_POST[request_email]) $_POST[request_email] = implode("@", $_POST[request_email]);

		   $_POST[content] = addslashes($_POST[content]);

			 //문의 유형이 추가된 경우 내용에 붙여준다.			20181217		chunter
			 if ($_POST['request_content_kind']) $_POST[content] = "문의 유형 - ". $_POST['request_content_kind'] ."<br>". $_POST[content];

		   $m_member->setBigorderRequestInfo($cid, $_POST[category], $_POST[goodsnm], $_POST[ea], $_POST[request_company], $_POST[request_name], $_POST[request_mobile], $_POST[request_email], $_POST[request_zipcode], $_POST[request_address], $_POST[request_address_sub], $_POST[content]);

		   if ($_POST[category] == "quotation") $rtn_msg = _("견적요청이 완료되었습니다.");
		   else if ($_POST[category] == "sample") $rtn_msg = _("샘플신청이 완료되었습니다.");
			 else $rtn_msg = _("완료되었습니다.");

		   msgNlocationReplace($rtn_msg, "bigorder.php?category=".$_POST[category], "Y");
	   } else {
	   	   msgNlocationReplace(_("대량구매문의 종류가 유효하지 않습니다."), "bigorder.php", "Y");
	   }

   exit; break;

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
}

if (!$_POST[rurl]) $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);

?>
