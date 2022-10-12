<?

include "../lib/library.php";

$m_board = new M_board();
$m_member = new M_member();
$m_order = new M_order();

$board = $m_board->getBoardSetInfo($cid, $_POST[board_id]);

/*--------------------- 각종차단필터 ---------------------*/    //20150107    chunter
if ($_POST[mode] == "write" || $_POST[mode] == "modify" || $_POST[mode] == "reply") {
	// 불량단어 필터링
	if($board[use_filter] == "1") {
		if(bad_check($board[filter_text], $_POST[subject])) msg(_("제목에 불량단어가 포함되어 있습니다."), -1);
		if(bad_check($board[filter_text], $_POST[content])) msg(_("내용에 불량단어가 포함되어 있습니다."), -1);
	}

	// 등록 이름 차단
	if($board[name_close]) {
		if(bad_check($board[name_close], $_POST[name])) msg(_("이름에 차단단어가 포함되어 있습니다."), -1);
	}

	// 게시판 원격 글쓰기 차단
	if($board[remote_connect] == "1") {
		if(!eregi(getenv("HTTP_HOST"), getenv("HTTP_REFERER"))) msg(_("원격 글쓰기가 허용되지 않습니다."), -1);
	}

	// IP 접근 관련
	if($board[ip_select] == "1") {
		if(bad_check($board[ip_close], USER_IP)) msg(_("접근이 차단된 IP 입니다."), -1);
	} else if($board[ip_select] == "2") {
		if(!bad_check($board[ip_connect], USER_IP)) msg(_("접근이 허용된 IP가 아닙니다."), -1);
	}
}

switch ($_GET[mode]) {
	case "del":
		$data = $m_board->getBoardInfo($cid, $_GET[board_id], $_GET[no]);

		if ($data[password] && $data[password] != $_GET[password] && !$ici_admin) {
			msg(_("비밀번호가 일치하지 않습니다."), -1);
			exit;
		}

		$tableName = "exm_board";
		$addWhere = "where cid = '$cid' and board_id = '$_GET[board_id]' and no != '$data[no]' and main = '$data[main]' and thread like '$data[thread]%'";
		$chk = $m_board->getCustomerServiceInfo($cid, $tableName, $addWhere);

		if ($chk[no]) {
			msg(_("답글이 존재하는 게시물은 삭제하실 수 없습니다."), -1);
			exit;
		}

		$dir = "../data/board/$cid/$_GET[board_id]/";

		$tableName2 = "exm_board_file";
		$addWhere2 = "where pno = '$_GET[no]'";
		$data2 = $m_board->getCustomerService($cid, $tableName2, $addWhere2);

		foreach ($data2 as $k=>$v) {
			@unlink($dir.$v[filesrc]);
			$m_board->delCustomerServiceFile($v[fileno]);
		}

		$tableName3 = "exm_board";
		$m_board->delCustomerService($cid, $tableName3, $_GET[no]);

		go("list.php?board_id=$_GET[board_id]");

	break;

	case "edking_comment":
		$m_board->setEdkingComment($_GET[no], $cid, $sess[mid]);

		$addColumn = "set comment = comment+1";
		$addWhere = "where no = '$_GET[no]'";
		$m_board->setEdkingInfo($_GET[no], $addColumn, $addWhere);

		msg(_("추천하셨습니다."), $_SERVER[HTTP_REFERER]);

	break;

	case "edking_insert":
		$storageid = $_GET[storageid];

		$addWhere = "where cid = '$cid' and storageid = '$storageid'";
		$data = $m_board->getEdkingInfo($cid, $addWhere);

		if ($data[storageid]) {
			msg(_("이미 편집왕에 등록된 상품입니다."), -1);
			exit;
		}

		##########################################################################################################################################
		/// <summary>
		/// 편집정보 미리보기 이미지 보관함 복사
		/// 블루팟에서 직접 호출
		/// 포토큐브 편집왕 전용
		/// 2014.02.19 by kdk
		/// </summary>
		/// <returns>성공: success|보관함코드, 실패: fail|메세지</returns>
		include_once "../lib/nusoap/lib/nusoap.php";

		/*$edking_url = getCfg('edking_url');
		$client = (strpos($edking_url, "?") !== false) ? $edking_url."&storageid=$storageid" : $edking_url."?storageid=$storageid";*/
		$client = "http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/SetReOrderEditKing.aspx?storageid=$storageid";
		$ret[SetReOrderEditKing] = readurl($client);
		$loop = explode("|", $ret[SetReOrderEditKing]);

		//편집정보 미리보기 이미지 복사 내역 DB 처리
		$data4 = $m_board->getEdkingCopyfolderInfo($cid, $storageid);

		$copy_no = $data4[no];

		if ($copy_no) {
			//update
			$addColumn = "set trans_result = '$loop[0]',regdt = now()";
			$addWhere2 = "where no = '$copy_no'";
			$m_board->setEdkingCopyfolderInfo($copy_no, $addColumn, $addWhere2);
		} else {
			//insert
			$addColumn2 = "set cid = '$cid',storageid = '$storageid',trans_result = '$loop[0]',regdt = now()";
			$m_board->setEdkingCopyfolderInfo("", $addColumn2);
		}

		if ($loop[0] == "success") {
			$img_url = str_replace("success|", "", $ret[SetReOrderEditKing]);
		} else {
			msg(_("편집왕 등록에 실패했습니다.[편집정보 미리보기 이미지폴더 복사 실패]"), -1);
			exit;
		}
		##########################################################################################################################################

		$data2 = $m_order->getOrdItemForStorageid($storageid);

		$payno = $data2[payno];
		$ordno = $data2[ordno];
		$ordseq = $data2[ordseq];
		$goodsno = $data2[goodsno];
		$goodsnm = $data2[goodsnm];
		$catno = $data2[catno];

		$data3 = $m_order->getPayInfo($payno);

		$mid = $data3[mid];
		$orderer_name = $data3[orderer_name];

		$addColumn3 = "set
			cid		 = '$cid',
			storageid = '$storageid',
			payno		 = '$payno',
			ordno		 = '$ordno',
			ordseq	 = '$ordseq',
			goodsno 	 = '$goodsno',
			goodsnm 	 = '$goodsnm',
			catno		 = '$catno',
			mid		 = '$mid',
			name		 = '$orderer_name',
			img_url 	 = '$img_url',
			regdt		 = now()";
		$m_board->setEdkingInfo("", $addColumn3);

		msg(_("편집왕에 등록되었습니다."), "edking_list.php");

	break;
}

switch ($_POST[mode]) {
	case "password_view":
		$r_password = array();
		$data = $m_board->getBoardInfo($cid, $_POST[board_id], $_POST[no]);
		$r_password[] = $data[password];

		if ($data[thread]) {
			$depth = strlen($data[thread])/3;

			for ($i=0; $i<$depth; $i++) {
				$thread = substr($data[thread], 0, $i*3);

				$tableName = "exm_board";
				$addWhere = "where cid = '$cid' and board_id = '$_POST[board_id]' and main = '$data[main]' and thread like '$thread%'";
				$tmp = $m_board->getCustomerServiceInfo($cid, $tableName, $addWhere);

				if ($tmp[password]) {
					$r_password[] = $tmp[password];
				}
			}
		}

		if (in_array(md5($_POST[password]), $r_password)) {
			echo "ok";
		} else {
			echo _("비밀번호가 일치하지 않습니다.");
		}

	exit; break;

	case "reply":
		$data = $m_board->getBoardInfo($cid, $_POST[board_id], $_POST[no]);

		$len_thread = strlen($data[thread])+3;

		$tableName3 = "exm_board";
		$addWhere2 = "where cid = '$cid' and board_id = '$_POST[board_id]' and main = '$data[main]' and thread like '$data[thread]%' and length(thread) = '$len_thread' order by thread desc limit 1";
		$data2 = $m_board->getCustomerServiceInfo($cid, $tableName3, $addWhere2);
		$thread = $data2[thread];

		$thread = $data[thread].sprintf("%03d", $thread+1);

	case "write":
		include '../lib/zmSpamFree/zmSpamFree.php';

		if (!$rslt && !$ici_admin) {
			msg(_("보안코드를 입력해주세요."), -1);
			exit;
		}

		if ($_POST[subject_deco]) {
			foreach ($_POST[subject_deco] as $k=>$v) {
				if ($v) $subject_deco .= $k.":".$v.";";
			}
		}

		if ($_POST[password]) {
		   $_POST[password] = passwordCommonEncode($_POST[password]);
			$addqr = "password = '$_POST[password]',";
		}

		if (!$ici_admin && $sess[mid]) {
			$_POST[mid] = $sess[mid];
			$_POST[name] = $sess[name];
		}

		if ($ici_admin && !$_POST[name]) $_POST[name] = _("관리자");

		$tableName = "exm_board";
		$addColumn = "
		set $addqr
		cid			 = '$cid',
		board_id		 = '$_POST[board_id]',
		notice		 = '$_POST[notice]',
		subject		 = '$_POST[subject]',
		subject_deco = '$subject_deco',
		mid			 = '$_POST[mid]',
		name			 = '$_POST[name]',
		thread		 = '$thread',
		summary		 = '$_POST[summary]',
		content		 = '$_POST[content]',
		secret		 = '$_POST[secret]',
		email			 = '$_POST[email]',
		category		 = '$_POST[category]',
		regdt			 = now()";

		$m_board->setCustomerService("insert", $tableName, $addColumn);
		$no = mysql_insert_id();

		if ($_POST[mode] == "write") {
			$addColumn2 = "set main = -$no";
			$addWhere = "where no = '$no'";
			$m_board->setCustomerService("update", $tableName, $addColumn2, $addWhere);
		} else if ($_POST[mode] == "reply") {
			$addColumn2 = "set main = '$data[main]'";
			$addWhere = "where no = '$no'";
			$m_board->setCustomerService("update", $tableName, $addColumn2, $addWhere);
		}

		if ($_FILES[file]) {
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

			$dir = "../data/board/$cid/$_POST[board_id]/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$file_path = substr($dir, 2);     //경로중 "".." 문자열 제거     20140624  chunter

			foreach ($_FILES[file][tmp_name] as $k=>$v) {
				if (is_uploaded_file($v)) {
					$filesrc = $no."_".time().rand(0, 9999);
					$filename = $_FILES[file][name][$k];
					$filesize = $_FILES[file][size][$k];

					if ($filesize > $board[limit_filesize]*1024 && $board[limit_filesize]) {
						continue;
					}

					move_uploaded_file($v, $dir.$filesrc);

					$tableName2 = "exm_board_file";
					$addColumn3 = "set
         	   					pno			= '$no',
         	   					filename	= '$filename',
         	   					filesrc		= '$filesrc',
         	   					filesize	= '$filesize',
         	   					file_path   = '$file_path'";

					$m_board->setCustomerService("insert", $tableName2, $addColumn3);
				}
			}
		}

		### SMS전송 ######################################################################################################

		list($_POST[board_name]) = $board['board_name'];  // 게시판명추출

		if ($_POST[mode] == "write") {
			$member = $m_member->getInfo($cid, $sess[mid]);
			$mobile = $member[mobile]; // 나의핸드폰번호추출
			autoSms(_("게시판-글등록"), $mobile, $_POST);
		} else if ($_POST[mode] == "reply") {
			$member = $m_member->getInfo($cid, $data[mid]);
			$mobile = $member[mobile]; // 나의핸드폰번호추출
			autoSms(_("게시판-답글등록"), $mobile, $_POST);
		}

		### SMS전송 #######################################################################################################

		// board email 전송추가 210518 jtkim
		if ($_POST[mode] == "write"){
			$member = $m_member->getInfo($cid, $sess[mid]);
			$member[nameSite] = $cfg[nameSite]; //사이트명 추가. / 20181128 / kdk
			autoMailBoard($_POST[board_id], $member[email],$member);
		}

		$_POST[rurl] = "list.php?board_id=".$_POST[board_id];
		echo "<script>location.href='$_POST[rurl]';</script>";

	exit; break;

	case "modify":
		$data = $m_board->getBoardInfo($cid, $_POST[board_id], $_POST[no]);
		$password = passwordCommonEncode($_POST[password]);

		if ($data[password] && $data[password] != $password && !$ici_admin) {
			msg(_("비밀번호가 일치하지 않습니다."), -1);
			exit;
		}

		if ($_POST[subject_deco]) {
			foreach ($_POST[subject_deco] as $k=>$v) {
				if ($v) $subject_deco .= $k.":".$v.";";
			}
		}

		if (!$ici_admin && !$data[mid]) {
			$addqr = "name = '$_POST[name]',";
		}

		if ($ici_admin && !$_POST[name]) {
			$addqr = "name = '"._("관리자")."',mid = '',";
		}

		$tableName = "exm_board";
		$addColumn = "
		set $addqr
		cid				= '$cid',
		board_id		= '$_POST[board_id]',
		notice			= '$_POST[notice]',
		subject			= '$_POST[subject]',
		subject_deco	= '$subject_deco',		
		summary			= '$_POST[summary]',
		content			= '$_POST[content]',
		secret			= '$_POST[secret]',
		email			= '$_POST[email]',
		category		= '$_POST[category]'";
		$addWhere = "
		where
		cid = '$cid'
		and board_id = '$_POST[board_id]'
		and no = '$_POST[no]'";

		$m_board->setCustomerService("update", $tableName, $addColumn, $addWhere);

		$r_file = array();
		$tableName2 = "exm_board_file";
		$addWhere2 = "where pno = '$data[no]'";
		$data2 = $m_board->getCustomerService($cid, $tableName2, $addWhere2);

		foreach ($data2 as $k=>$v) {
			$r_file[] = $v;
		}

		$dir = "../data/board/$cid/$data[board_id]/";
		if ($data[file_path]) $dir = ".." .$data[file_path];

		if ($_POST[delfile]) {
			foreach ($_POST[delfile] as $k=>$v) {
				@unlink($dir.$r_file[$k][filesrc]);
				$m_board->delCustomerServiceFile($r_file[$k][fileno]);
			}
		}

		if ($_FILES[file]) {
			$board = $m_board->getBoardSetInfo($cid, $_POST[board_id]);

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

			$dir = "../data/board/$cid/$_POST[board_id]/";
			if (!is_dir($dir)) {
				mkdir($dir, 0707);
				chmod($dir, 0707);
			}

			$file_path = substr($dir, 2);     //경로중 "".." 문자열 제거     20140624  chunter

			foreach ($_FILES[file][tmp_name] as $k=>$v) {
				if (is_uploaded_file($v)) {
					$filesrc = $data[no]."_".time().rand(0, 9999);
					$filename = $_FILES[file][name][$k];
					$filesize = $_FILES[file][size][$k];

					if ($filesize > $board[limit_filesize]*1024 && $board[limit_filesize]) {
						continue;
					}

					@unlink($dir.$r_file[$k][filesrc]);
					$m_board->delCustomerServiceFile($r_file[$k][fileno]);

					move_uploaded_file($v, $dir.$filesrc);

					$tableName3 = "exm_board_file";
					$addColumn2 = "set
	   					pno			= '$data[no]',
	   					filename	= '$filename',
	   					filesrc		= '$filesrc',
	   					filesize	= '$filesize',
	   					file_path   = '$file_path'";

					$m_board->setCustomerService("insert", $tableName3, $addColumn2);
				}
			}
		}

		$_POST[rurl] = "list.php?board_id=".$_POST[board_id];

	break;

	case "password_del":
		$data = $m_board->getBoardInfo($cid, $_POST[board_id], $_POST[no]);
		$password = passwordCommonEncode($_POST[password]);
		if ($data[password] == $password) {
			echo _("비밀번호가 일치합니다.")." <a href='indb.php?mode=del&board_id=$data[board_id]&no=$data[no]&password=$data[password]' onclick='return confirm(\""._("정말 삭제하시겠습니까?")."\")'><b>"._("삭제하기")."</b></a>";
		} else {
			echo _("비밀번호가 일치하지 않습니다.");
		}

	exit; break;

	case "comment":
		include dirname(__FILE__)."/../lib/template_/Template_.class.php";

		$tpl = new Template_;
		$tpl->template_dir = dirname(__FILE__)."/../skin";
		$tpl->skin = $cfg[skin];
		$tpl->compile_dir = dirname(__FILE__)."/../_compile";
		$tpl->prefilter	= "adjustPath";

		if ($sess[mid]) {
			$member = $m_member->getInfoWithGrp($cid, $sess[mid]);
			$sess[email] = $member[email];
			$sess[level] = $member[grplv];
		}

		$board = $m_board->getBoardSetInfo($cid, $_POST[board_id]);

		if ($board[permission_comment] > $sess[level]+0 && !$ici_admin) $deny[write] = 1;

		$tableName = "exm_board_comment";
		$addWhere = "where cid = '$cid' and board_id = '$_POST[board_id]' and pno = '$_POST[no]'";
		$data = $m_board->getCustomerService($cid, $tableName, $addWhere);

		foreach ($data as $k=>$v) {
			$v[writer] = $v[$board[writer_type]];
			if (!$v[writer]) $v[writer] = $v[name];
			$loop[] = $v;
		}

		$tpl->assign('loop',$loop);
		$tpl->assign('board',$board);
		$tpl->define('comment',"board/{$board[board_skin]}/comment.htm");
		$tpl->print_('comment');

	exit; break;

	case "comment_write":
		$_POST[content] = addslashes($_POST[content]);
		if ($_POST[password]){
		   $password = passwordCommonEncode($_POST[password]);
		   $pw = "password = '$password',";
      }

		$tableName = "exm_board_comment";
		$addColumn = "
		set $pw
		cid			= '$cid',
		board_id	= '$_POST[board_id]',
		pno			= '$_POST[no]',
		mid			= '$sess[mid]',
		name		= '$_POST[name]',
		comment		= '$_POST[comment]',
		regdt		= now()";

		$m_board->setCustomerService("insert", $tableName, $addColumn);
		$no = mysql_insert_id();
		$main = $no * -1;

		$addColumn2 = "set main	= '$main'";
		$addWhere = "where no = '$no'";
		$m_board->setCustomerService("update", $tableName, $addColumn2, $addWhere);

	exit; break;

	case "comment_del":
		$tableName = "exm_board_comment";
		$m_board->delCustomerService($cid, $tableName, $_POST[no]);
		echo "true";

	exit; break;

	case "comment_chkPassword":
		$tableName = "exm_board_comment";
		$addWhere = "where no = '$_POST[no]'";
		$data = $m_board->getCustomerServiceInfo($cid, $tableName, $addWhere);
		$password = passwordCommonEncode($_POST[password]);

		if ($data[password] != $password) {
			$ret = "false";
		} else {
			$ret = "true";
		}

		echo $ret;

	exit; break;

	case "edking_previewimg":
		$addWhere = "where cid = '$cid' and storageid = '$_POST[storageid]'";
		$data = $m_board->getEdkingInfo($cid, $addWhere);

		$img_url = $data[img_url];
		$loop = explode("|", $img_url);

		if ($loop[0] != "fail") {
			$dir = "../data/edking/";
     		if (!is_dir($dir)) {
     			mkdir($dir, 0707);
     			chmod($dir, 0707);
			}

			$dir = "../data/edking/$cid/";
     		if (!is_dir($dir)) {
     			mkdir($dir, 0707);
     			chmod($dir, 0707);
			}

			$xxx = readurl($loop[0]);
			$tmp = $dir.$_POST[storageid];

			$fp = fopen($tmp, "w");
			fwrite($fp, $xxx);
			fclose($fp);

			$info = getimagesize($tmp);
			unlink($tmp);

			echo $loop[0]."|".$info[0]."|".$info[1];
		}

	exit; break;
}

if (!$_POST[rurl]) $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);

?>
