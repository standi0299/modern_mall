<?
include_once "../lib.php";
include_once dirname(__FILE__) . "/../../pretty/_module_util.php";

	if (!$_POST[board_id]) $_POST[board_id] = $_POST[board];

	//printhome 업로드 주문 20140724   chunter
	$m_pretty = new M_pretty();
	$board = $m_pretty->getBoardSetting($cid, $_POST[board_id]);


	if ($_POST[subject_deco]) foreach ($_POST[subject_deco] as $k=>$v){
		if ($v) $subject_deco .= $k.":".$v.";";
	}
	
	$m_config = new M_config();
	$m_board = new M_board();

	/***/
	switch ($_REQUEST[mode]) {/***/

    case "faq_del":
    
        $db->query("delete from exm_faq where no = '$_REQUEST[no]'");
    
        break;
    
    case "cs_del": case "qna_del":
    
        $db->query("delete from exm_mycs where no = '$_REQUEST[no]'");
    
        break;	
	
    case "del":
        
        $m_board->getBoardFileData($cid, $_REQUEST[no], $_REQUEST[board_id], $admin='N');
            
        $dir = "../../data/board/$cid/$_REQUEST[board_id]/";
        
        $tableName2 = "exm_board_file";
        $addWhere2 = "where pno = '$_REQUEST[no]'";
        $data2 = $m_board->getCustomerService($cid, $tableName2, $addWhere2);
        
        foreach ($data2 as $k=>$v) {
            @unlink($dir.$v[filesrc]);
            $m_board->delCustomerServiceFile($v[fileno]);
        }

        $db->query("delete from exm_board where no = $_REQUEST[no]");
        msg(_("해당 게시물이 삭제되었습니다."),"board_list.php");exit;

    break;
		
		case "reply":
			$data = $db->fetch("select * from exm_board where cid = '$cid' and board_id = '$_POST[board_id]' and no = '$_POST[no]'");
			$len_thread = strlen($data[thread])+3;
			list($thread) = $db->fetch("select thread from exm_board where cid = '$cid' and board_id = '$_POST[board_id]' and main = '$data[main]' and thread like '$data[thread]%' and length(thread) = '$len_thread' order by thread desc limit 1",1);
			$thread = $data[thread].sprintf("%03d",$thread+1);
	
		case "write" :
			if (! $_POST[board_id]) msg(_("게시판을 선택해 주세요."), -1);
      $query = "
      insert into exm_board set
         cid          = '$cid',
         board_id     = '$_POST[board_id]',
         notice		 = '$_POST[notice]',
         secret		 = '$_POST[secret]',
         subject_deco = '$subject_deco',
         category		 = '$_POST[category]',
         thread		 = '$thread',
         subject      = '$_POST[subject]',
         name         = '$_POST[name]',
         email        = '$_POST[email]',
         content      = '$_POST[content]',
         regdt        = now()
      ";
      $db->query($query);
      $no = $db->id;
      if ($_POST[mode]=="write") $db->query("update exm_board set main = -$db->id where no = '$db->id'");

      if ($_FILES[file]){
      	 $dir = "../../data/board/";
         if (!is_dir($dir)){
            mkdir($dir,0707);
            chmod($dir,0707);
         }
		
         $dir = "../../data/board/$cid/";
         if (!is_dir($dir)){
            mkdir($dir,0707);
            chmod($dir,0707);
         }

         $dir = "../../data/board/$cid/$_POST[board_id]/";
         if (!is_dir($dir)){
            mkdir($dir,0707);
            chmod($dir,0707);
         }
         $file_path = substr($dir, 2);     //경로중 "".." 문자열 제거     20140624  chunter

         foreach ($_FILES[file][tmp_name] as $k=>$v){
            if (is_uploaded_file($v)){
               $filesrc = $no."_".time().rand(0,9999);
               $filename = $_FILES[file][name][$k];
               $filesize = $_FILES[file][size][$k];

               if ($filesize > $board[limit_filesize]*1024 && $board[limit_filesize]){
                  continue;
               }

               move_uploaded_file($v,$dir.$filesrc);
               $query = "
               insert into exm_board_file set
                  pno       = '$no',
                  filename  = '$filename',
                  filesrc   = '$filesrc',
                  filesize  = '$filesize',
                  file_path = '$file_path'
               ";
               $db->query($query);
            }
         }
      }

      msg(_("등록 완료되었습니다."), "board_list.php?board=$_POST[board_id]");
			break;

		case "modify" :

      $data = $db->fetch("select * from exm_board where cid = '$cid' and board_id = '$_POST[board_id]' and no = '$_POST[no]'");

      $query = "
      update exm_board set
         cid          = '$cid',
         board_id     = '$_POST[board_id]',
         notice		 = '$_POST[notice]',
         secret		 = '$_POST[secret]',
         subject_deco = '$subject_deco',
         category		 = '$_POST[category]',
         subject      = '$_POST[subject]',
         name         = '$_POST[name]',
         email        = '$_POST[email]',
         content      = '$_POST[content]'
      where
         cid = '$cid'
         and board_id = '$_POST[board_id]'
         and no = '$_POST[no]'
      ";
      $db->query($query);

      $r_file = array();
      $query = "select * from exm_board_file where pno = '$data[no]'";
      $res = $db->query($query);
      while ($tmp = $db->fetch($res)){
         $r_file[] = $tmp;
      }

      $dir = "../../data/board/$cid/$data[board_id]/";

      if ($data[file_path])
         $dir = ".." .$data[file_path];

      if ($_POST[delfile]){
         foreach ($_POST[delfile] as $k=>$v){
            @unlink($dir.$r_file[$k][filesrc]);
            $db->query("delete from exm_board_file where fileno = '{$r_file[$k][fileno]}'");
         }
      }

      if ($_FILES[file]){
         $board = $db->fetch("select * from exm_board_set where cid = '$cid' and board_id = '$_POST[board_id]'");
   		 
   		 $dir = "../../data/board/";
         if (!is_dir($dir)){
            mkdir($dir,0707);
            chmod($dir,0707);
         }
   		 
         $dir = "../../data/board/$cid/";
         if (!is_dir($dir)){
            mkdir($dir,0707);
            chmod($dir,0707);
         }
         
         $dir = "../../data/board/$cid/$_POST[board_id]/";
         if (!is_dir($dir)){
            mkdir($dir,0707);
            chmod($dir,0707);
         }
            
         $file_path = substr($dir, 2);     //경로중 "".." 문자열 제거     20140624  chunter
   
         foreach ($_FILES[file][tmp_name] as $k=>$v){
            
            if (is_uploaded_file($v)) {
               $filesrc = $data[no]."_".time().rand(0,9999);
               $filename = $_FILES[file][name][$k];
               $filesize = $_FILES[file][size][$k];
   
               if ($filesize > $board[limit_filesize]*1024 && $board[limit_filesize]){
                  continue;
               }
   
               @unlink($dir.$r_file[$k][filesrc]);
               $db->query("delete from exm_board_file where fileno = '{$r_file[$k][fileno]}'");
   
               move_uploaded_file($v,$dir.$filesrc);
               $query = "
               insert into exm_board_file set
                  pno         = '$data[no]',
                  filename = '$filename',
                  filesrc     = '$filesrc',
                  filesize = '$filesize',
                  file_path = '$file_path'
               ";
               $db->query($query);
            }
         }
      }
     
			msg(_("수정 완료되었습니다."), "board_list.php?board=$_POST[board_id]");
			break;

		case "faq" :   
            
            $_POST[q] = addslashes($_POST[q]);
            $_POST[a] = addslashes($_POST[a]);
               
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

      //msgNpopClose(_("수정 완료되었습니다."));
	  echo "<script>alert('"._("저장 완료되었습니다.")."');this.close();opener.location='faq.php';</script>";
			break;

		case "faq_del" :      
      $db->query("delete from exm_faq where no = '$_GET[no]'");
			break;

	case "review":
		$tableName = "exm_review";
		
		$addColumn = "set ";
		//적립금 수정
		if ($_POST[emoney] != $_POST[original_emoney]) {
			$emoney = $_POST[emoney] - $_POST[original_emoney];
			$addColumn .= "emoney = '$_POST[emoney]',emoneydt=now(),";
			
	        //set_emoney($_POST[mid],"후기적립금",$emoney,$_POST[payno],$_POST[ordno],$_POST[ordseq]);
	        setAddEmoney($cid, $_POST[mid], $emoney, _("후기적립금"), $sess_admin[mid], $_POST[payno], $_POST[ordno], $_POST[ordseq]);
		}	
		//이미지 수정
		if (is_uploaded_file($_FILES[img][tmp_name])) {
			$dir = "../../data/review/";
	      	if (!is_dir($dir)){
	         	mkdir($dir,0707);
	         	chmod($dir,0707);
	      	}
			
			$dir = "../../data/review/$cid/";
	      	if (!is_dir($dir)){
	         	mkdir($dir,0707);
	         	chmod($dir,0707);
	      	}
			
			$info = getimagesize($_FILES[img][tmp_name]);
			
			if (!in_array($info[2], array(1, 2, 3))) {
				msg(_("이미지는 gif,jpg,png 이미지만을 업로드 할 수 있습니다."), -1);
				exit;
			}
			
			switch ($info[2]) {
				case "1": $ext = ".gif"; break;
				case "2": $ext = ".jpg"; break;
				case "3": $ext = ".png"; break;
			}
			
			if ($_POST[original_img] && file_exists($dir.$_POST[original_img])) @unlink($dir.$_POST[original_img]);
			$filename = $_POST[mid]."_".uniqid().$ext;
			
			move_uploaded_file($_FILES[img][tmp_name], $dir.$filename);
			$addColumn .= "img = '$filename',";
		}
		$addColumn .= "subject	= '$_POST[subject]',
					   content = '$_POST[content]',
					   review_deny_admin = '$_POST[review_deny_admin]',
					   degree = '$_POST[degree]'";
					   
		$addWhere = "where cid = '$cid' and no = '$_POST[no]'";
		
		$m_board->setCustomerService("update", $tableName, $addColumn, $addWhere);
	
		break;

        case "review_admin":

            $fname = "";
            
            $dir = "../../data/review/";
            if (!is_dir($dir)) {
                mkdir($dir, 0707);
                chmod($dir, 0707);
            }
                
            $dir = "../../data/review/$cid/";
            if (!is_dir($dir)) {
                mkdir($dir, 0707);
                chmod($dir, 0707);
            }
            
            if ($fname && ($_FILES['img'][tmp_name] || $_POST[kind] == "normal")) {
                unlink($dir.$fname);
                $fname = "";
            }
                
            if (is_uploaded_file($_FILES['img'][tmp_name])) {
                $ext = substr(strrchr($_FILES['img'][name], "."), 1);
                $fname = time().rand(0, 9999).".".$ext;
                
                move_uploaded_file($_FILES['img'][tmp_name], $dir.$fname);
            }
            
			if (!$_POST[payno]) {
				$landom_num = str_replace("-", "", makeStorageCode());
				$_POST[payno] = substr($landom_num, -13);
			}
			
			if (!$_POST[review_mid]) $_POST[review_mid] = $sess_admin[mid];
			if (!$_POST[review_name]) $_POST[review_name] = $sess_admin[name];
			
            $m_member = new M_member();
            //$m_member->setReviewInfo($cid, $_POST[payno], $_POST[ordno], $_POST[ordseq], $_POST[catno], $_POST[goodsno], $sess_admin[mid], $sess_admin[name], $_POST[subject], $_POST[content], $_POST[degree], $_POST[review_deny_user], $_POST[kind], $fname);
            
            $tableName = "exm_review";
            $addColumn = "set 
                cid              = '$cid',
                payno            = '$_POST[payno]',
                ordno            = '$_POST[ordno]',
                ordseq           = '$_POST[ordseq]',
                catno            = '$_POST[catno]',
                goodsno          = '$_POST[goodsno]',
                mid              = '$_POST[review_mid]',
                name             = '$_POST[review_name]',
                subject          = '$_POST[subject]',
                content          = '$_POST[content]',
                degree           = '$_POST[degree]',
                review_deny_user = '$_POST[review_deny_user]',
                kind             = '$_POST[kind]',
                img              = '$fname',
                regdt            = now()";
                
            $m_board->setCustomerService("insert", $tableName, $addColumn);

        break;

	case "qna": case "cs":     
      $status = ($_POST[reply]) ? 2 : 1;
	  $_POST[reply] = addslashes($_POST[reply]);
      $query = "
      update exm_mycs set
         reply    = '$_POST[reply]',
         status      = '$status',
         reply_mid   = '$sess_admin[mid]',
         replydt     = NOW()
      where
         no       = '$_POST[no]'
      ";
      $db->query($query);
	  
	  ### SMS전송 ######################################################################################################
	  
	  $addWhere = "where cid = '$cid' and no = '$_POST[no]'";
	  $data = $m_board->getMycsInfo($cid, $addWhere);
	  
	  $m_member = new M_member();
	  $member = $m_member->getInfo($cid, $data[mid]);

	  autoSms(_("1:1문의-답글등록"), $member[mobile], $data);
	  kakao_alimtalk_send($member[mobile],$data[mid],_("1:1문의-답글등록"), $data);
	  
	  ### SMS전송 ######################################################################################################
	//20190305 답변 작성 후 페이지 이동안되는 현상 수정
	echo "<script>alert('"._("답변이 정상등록 되었습니다.")."');location.href='cs.php';</script>";
	exit; break;

		case "cs_del" :
    	$db->query("delete from exm_mycs where no = '$_GET[no]'"); 
			break;

		case "board_remove":
			$db->query("delete from exm_board_set where cid = '$cid' and board_id = '$_GET[board_id]'");
			break;
	
		case "chk_board_id":
			list($chk) = $db->fetch("select board_id from exm_board_set where cid = '$cid' and board_id='$_POST[board_id]'",1);	
			if ($chk) echo "duplicate";
			else echo "ok";
			exit; break;
	
		case "board_create":
			list($chk) = $db->fetch("select board_id from exm_board_set where cid = '$cid' and board_id='$_POST[board_id]'",1);
			if ($chk){
				msg(_("이미 존재하는 게시판 ID 입니다."),1);
				exit;
			}
	
			$_POST[board_width] = implode("",$_POST[board_width]);
			if ($_POST[on_subject_deco]) $_POST[on_subject_deco] = array_sum($_POST[on_subject_deco]);
			if (!$_POST[num_per_page]) $_POST[num_per_page] = 20;
	
			$query = "
			insert into exm_board_set set
				cid					= '$cid',
				board_id			   = '$_POST[board_id]',
				board_name			= '$_POST[board_name]',
				board_skin			= '$_POST[board_skin]',
				board_width			= '$_POST[board_width]',
				board_align			= '$_POST[board_align]',
				subject_length		= '$_POST[subject_length]',
				writer_type			= '$_POST[writer_type]',
				num_per_page		= '$_POST[num_per_page]',
				limit_new			= '$_POST[limit_new]',
				limit_hot			= '$_POST[limit_hot]',
				on_reply			   = '$_POST[on_reply]',
				on_comment			= '$_POST[on_comment]',
				on_category			= '$_POST[on_category]',
				category			   = '$_POST[category]',
				on_secret			= '$_POST[on_secret]',
				on_email			   = '$_POST[on_email]',
				on_file				= '$_POST[on_file]',
				limit_filecount	= '$_POST[limit_filecount]',
				limit_filesize		= '$_POST[limit_filesize]',
				on_subject_deco	= '$_POST[on_subject_deco]',
				permission_list	= '$_POST[permission_list]',
				permission_read	= '$_POST[permission_read]',
				permission_write	= '$_POST[permission_write]',
				permission_reply	= '$_POST[permission_reply]',
				permission_comment = '$_POST[permission_comment]',
				header_html			= '$_POST[header_html]',
				footer_html			= '$_POST[footer_html]',
				layout_top			= '$_POST[layout_top]',
				layout_left			= '$_POST[layout_left]',
				layout_bottom		= '$_POST[layout_bottom]',
				gallery_w			= '$_POST[gallery_w]',
				gallery_h			= '$_POST[gallery_h]',
				gallery_num			= '$_POST[gallery_num]',
				use_sms_write		= '$_POST[use_sms_write]',
				use_sms_reply		= '$_POST[use_sms_reply]',

		      use_filter     = '$_POST[use_filter]',    
		      filter_text    = '$_POST[filter_text]',
		      name_close     = '$_POST[name_close]',
		      ip_select      = '$_POST[ip_select]',
		      ip_close       = '$_POST[ip_close]',
		      ip_connect     = '$_POST[ip_connect]',
		      remote_connect = '$_POST[remote_connect]',
		      content_html   = '$_POST[content_html]'
			";
			$db->query($query);
			$_POST[rurl] = "board_set_list.php";
			break;

		case "board_modify":
			$_POST[board_width] = implode("",$_POST[board_width]);
			if ($_POST[on_subject_deco]) $_POST[on_subject_deco] = array_sum($_POST[on_subject_deco]);
			if (!$_POST[num_per_page]) $_POST[num_per_page] = 20;

			$query = "
			update exm_board_set set
				board_name			= '$_POST[board_name]',
				board_skin			= '$_POST[board_skin]',
				board_width			= '$_POST[board_width]',
				board_align			= '$_POST[board_align]',
				subject_length		= '$_POST[subject_length]',
				writer_type			= '$_POST[writer_type]',
				num_per_page		= '$_POST[num_per_page]',
				limit_new			= '$_POST[limit_new]',
				limit_hot			= '$_POST[limit_hot]',
				on_reply			   = '$_POST[on_reply]',
				on_comment			= '$_POST[on_comment]',
				on_category			= '$_POST[on_category]',
				category			   = '$_POST[category]',
				on_secret			= '$_POST[on_secret]',
				on_email			   = '$_POST[on_email]',
				on_file				= '$_POST[on_file]',
				limit_filecount	= '$_POST[limit_filecount]',
				limit_filesize		= '$_POST[limit_filesize]',
				on_subject_deco	= '$_POST[on_subject_deco]',
				permission_list	= '$_POST[permission_list]',
				permission_read	= '$_POST[permission_read]',
				permission_write	= '$_POST[permission_write]',
				permission_reply	= '$_POST[permission_reply]',
				permission_comment = '$_POST[permission_comment]',
				header_html			= '$_POST[header_html]',
				footer_html			= '$_POST[footer_html]',
				layout_top			= '$_POST[layout_top]',
				layout_left			= '$_POST[layout_left]',
				layout_bottom		= '$_POST[layout_bottom]',
				gallery_w			= '$_POST[gallery_w]',
				gallery_h			= '$_POST[gallery_h]',
				gallery_num			= '$_POST[gallery_num]',
				use_sms_write		= '$_POST[use_sms_write]',
				use_sms_reply		= '$_POST[use_sms_reply]',
				    
            use_filter     = '$_POST[use_filter]',
		      filter_text    = '$_POST[filter_text]',
		      name_close     = '$_POST[name_close]',
		      ip_select      = '$_POST[ip_select]',
		      ip_close       = '$_POST[ip_close]',
		      ip_connect     = '$_POST[ip_connect]',
		      remote_connect = '$_POST[remote_connect]',
		      content_html   = '$_POST[content_html]'
         where
				cid = '$cid'
				and board_id = '$_POST[board_id]'
			";
			$db->query($query);
			break;

	case "edking" :
		if (!$_POST[edking_flag]) $_POST[edking_flag] = "0";
		$m_config->setConfigInfo($cid, 'edking_flag', $_POST[edking_flag]);
		//$m_config->setConfigInfo($cid, 'edking_url', $_POST[edking_url]);
		
		msg(_("수정 완료되었습니다."), $_SERVER[HTTP_REFERER]);
		exit;

	break;

	case "bottom_edking_best":
		$tableName = "exm_edking";
	    $addWhere = "where cid='$cid' and no in ($_POST[bno])";
	    $data = $m_board->getCustomerService($cid, $tableName, $addWhere);
    
	    foreach ($data as $k=>$v) {
	    	$chk_yn = ($v[chk_yn] == "Y") ? "N" : "Y";
	    	$addColumn = "set chk_yn='$chk_yn'";
	    	$addWhere2 = "where cid='$cid' and no='$v[no]'";
	    	$m_board->setCustomerService("update", $tableName, $addColumn, $addWhere2);
	    }
	
		msg(_("베스트 플래그가 변경되었습니다."), $_SERVER[HTTP_REFERER]);
		exit;
	
	break;
	
	case "bottom_edking_emoney":
	   $db->start_transaction();

      $tableName = "exm_edking";
		$addWhere = "where cid='$cid' and no in ($_POST[bno])";
	   $data = $m_board->getCustomerService($cid, $tableName, $addWhere);
	    
		foreach ($data as $k=>$v) {
			$emoney = $v[emoney] + $_POST[emoney];
			$addColumn = "set emoney='$emoney',emoneydt=now()";
	    	$addWhere2 = "where cid='$cid' and no='$v[no]'";
	    	$m_board->setCustomerService("update", $tableName, $addColumn, $addWhere2);
			
			//set_emoney($v[mid], "편집왕적립금", $_POST[emoney], $v[payno], $v[ordno], $v[ordseq]);
			setAddEmoney($cid, $v[mid], $_POST[emoney], _("편집왕적립금"), $sess_admin[mid], $v[payno], $v[ordno], $v[ordseq]);
		}
	
	    $db->end_transaction();
	
	    msg(_("적립금이 지급되었습니다."), $_SERVER[HTTP_REFERER]);
	    exit;

    break;
	
	case "bottom_edking_delete":
		$tableName = "exm_edking";
		$addWhere = "where cid='$cid' and no in ($_POST[bno])";
	    $data = $m_board->getCustomerService($cid, $tableName, $addWhere);
		
		$cnt = count($data); //총개수
		$ok = 0; //성공개수
		
		foreach ($data as $k=>$v) {
			$client = "http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/SetDeleteEditKing.aspx?storageid=$v[storageid]";
			$ret[SetDeleteEditKing] = readurl($client);
			$ret[SetDeleteEditKing] = explode("|", $ret[SetDeleteEditKing]);
			
			if ($ret[SetDeleteEditKing][0] == "success") {
				$m_board->delCustomerService($cid, $tableName, $v[no], $v[storageid]);
				$ok++;
			}
		}
		
		if ($cnt == $ok) msg(_("해당 항목이 정상적으로 삭제되었습니다."), $_SERVER[HTTP_REFERER]);
		else if ($ok > 0) msg(_("일부 항목을 삭제하지 못했습니다."), $_SERVER[HTTP_REFERER]);
		else msg(_("정상적으로 삭제된 항목이 없습니다."), $_SERVER[HTTP_REFERER]);
		
		exit;

	break;
	
	case "delReview":
		$imgpath = "../../data/review/$cid/$_GET[img]";
		if ($_GET[img] && file_exists($imgpath)) @unlink($imgpath);
		
		$tableName = "exm_review";
		$m_board->delCustomerService($cid, $tableName, $_GET[no]);

	break;
	
	case "bottom_review_emoney":
	    $db->start_transaction();
		
		$tableName = "exm_review";
		$addWhere = "where cid='$cid' and no in ($_POST[bno])";
	    $data = $m_board->getCustomerService($cid, $tableName, $addWhere);
	    
		foreach ($data as $k=>$v) {
			$emoney = $v[emoney] + $_POST[emoney];
			$addColumn = "set emoney='$emoney',emoneydt=now()";
	    	$addWhere2 = "where cid='$cid' and no='$v[no]'";
	    	$m_board->setCustomerService("update", $tableName, $addColumn, $addWhere2);
			
			//set_emoney($v[mid], "후기적립금", $_POST[emoney], $v[payno], $v[ordno], $v[ordseq]);
			setAddEmoney($cid, $v[mid], $_POST[emoney], _("후기적립금"), $sess_admin[mid], $v[payno], $v[ordno], $v[ordseq]);
		}
	
	    $db->end_transaction();
	
	    msg(_("적립금이 지급되었습니다."), $_SERVER[HTTP_REFERER]);
	    exit;

    break;
	
	case "bottom_review_delete":
		$tableName = "exm_review";
		$addWhere = "where cid='$cid' and no in ($_POST[bno])";
	    $data = $m_board->getCustomerService($cid, $tableName, $addWhere);
		
		foreach ($data as $k=>$v) {
			$imgpath = "../../data/review/$cid/$v[img]";
			if ($v[img] && file_exists($imgpath)) @unlink($imgpath);
			
			$m_board->delCustomerService($cid, $tableName, $v[no]);
		}
	
		msg(_("해당 항목이 삭제되었습니다."), $_SERVER[HTTP_REFERER]);
		exit;

	break;
	
	case "cstime" :
		$m_config->setConfigInfo($cid, $_POST[mode], $_POST[cstime]);
		echo "<script>alert('"._("저장 완료되었습니다.")."');this.close();opener.location='cs.php';</script>";
		exit;

    break;
    
   case "change_gallery_flag":
      
      
      foreach($_POST[chk] as $key => $val){
         $data = explode("|", $val);
         
         if($data[1] == "open") $flag = "best";
         else $flag = "open";
         
         $db->query("update md_gallery set flag = '$flag' where storageid = '$data[0]'");
      }
      
   break;
   
   case "change_gallery_main_flag":
	   
	   $data = explode("|", $_POST[storageid]);
	   
	   $db->query("update md_gallery set main_flag = 'N' where cid = '$cid'");
	   
	   $db->query("update md_gallery set main_flag = 'Y' where cid = '$cid' and storageid = '$data[0]'");
	   
   break;
   
   case "change_review_main_flag":
	   
	   $db->query("update exm_review set main_flag = 'N' where cid = '$cid'");
	   
	   $db->query("update exm_review set main_flag = 'Y' where cid = '$cid' and no = '$_POST[review_no]'");
	   
   break;
   
   case "delete_gallery":
   	   
   	   foreach ($_POST[chk] as $k=>$v) {
   	   	  $data = explode("|", $v);
   	   	  
		  $db->query("update exm_edit set open_gallery = 'N' where cid = '$cid' and storageid = '$data[0]'");
		  
		  $db->query("delete from md_gallery where cid = '$cid' and storageid = '$data[0]'");
   	   }
	   
   break;
			
	}/***/
	
	if (!$_POST[rurl])
  	$_POST[rurl] = $_SERVER[HTTP_REFERER];
	go($_POST[rurl]);
?>