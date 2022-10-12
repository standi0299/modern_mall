<?

/*
* @date : 20180309
* @author : kdk
* @brief : 회원리스트 하단 일괄처리(쿠폰)기능 추가.
* @request :
* @desc :
* @todo :
*/

/*
* @date : 20180306
* @author : kdk
* @brief : 회원가입항목설정 사용.
* @request :
* @desc : fieldset 보임 (exm_config)
* @todo :
*/

include "../lib.php";

switch ($_POST[mode]) {

    ### 회원 그룹 SMS보내기
    case "sendsms2":
		$_POST[sms_msg] = array_notnull($_POST[sms_msg]);
		if (!$_POST[sms_msg]) msg("메시지를 입력해주세요.","./sms_f.php");

		switch ($_POST[vtype]){
	        case "to":
				$r_to = explode(";",$_POST[to]);
				break;

			case "member":
				if ($_POST[member]) $add_query = "and grpno = '$_POST[member]'";

				$query = "select mobile from exm_member where cid = '$cid' and mobile != '' and apply_sms='1' $add_query";
				$res = $db->query($query);
				while ($data=$db->fetch($res)){
					$r_to[] = str_replace("-","",$data[mobile]);
				}

				break;

			case "excel":
				if (!is_uploaded_file($_FILES[file][tmp_name])) msg("엑셀파일을 업로드해주세요","./sms_f.php");
				set_time_limit(0);

				include "../../lib/excel.reader.php";
				$xls = new Spreadsheet_Excel_Reader();
				$xls->read($_FILES[file][tmp_name]);

				foreach ($xls->sheets[0][cells] as $idx=>$data){
					$r_to[] = iconv("euc-kr","utf-8",$data[2]);
				}

				break;
		}

		### 유효성체크

		if (!is_array($r_to)) msg("받는번호를 확인해주세요.","./sms_f.php");

		$r_to = array_unique($r_to);
		$r_to = array_notnull($r_to);
		if (!$r_to) msg("받는번호를 확인해주세요.","./sms_f.php");

		foreach ($r_to as $to){
			foreach ($_POST[sms_msg] as $msg){
				sendSms($to,$msg,$_POST[from]); //SMS발송
			}
		}

		//msg("SMS 전송이 완료되었습니다.", '');
		msgNlocationReplace("SMS 전송이 완료되었습니다.","./sms_f.php","Y");
		break;

	case "smslistup":
		set_time_limit(0);

		include "../../lib/excel.reader.php";
		$xls = new Spreadsheet_Excel_Reader();
		$xls->read($_FILES[file][tmp_name]);

		echo "<style>
		body{margin:0;font:8pt tahoma};div {width:105%;height:21px;border-bottom:1px solid #efefef;padding-top:3px};.c1{width:100px;text-align:center};.c2{width:113px;text-align:center};.c3{width:100px;text-align:center};</style>";

		if (!is_uploaded_file($_FILES[file][tmp_name])){
			msg("엑셀파일을 업로드해주세요"); exit;
		}

		foreach ($xls->sheets[0][cells] as $idx=>$data){
			$data[1] = iconv("euc-kr","utf-8",$data[1]);
			echo "<div><span class=c1>$idx</span><span class=c2>$data[1]</span><span class=c3>$data[2]</span></div>";
			flush();
		}
		echo "<div style='text-align:center'>- ".$idx."명의 데이터 추출 완료 -</div>";
		echo "<script>scroll(0,999999);</script>";
		exit;
		break;

	### SMS보내기
    case "sendsms":

        $_POST[sms_msg] = array_notnull($_POST[sms_msg]);
        if (!$_POST[sms_msg]) msg(_("메시지를 입력해주세요."),"./sms_popup.php");

        switch ($_POST[vtype]){

            case "to":
                $r_to = explode(";",$_POST[to]);
                break;

            case "member":

                if ($_POST[member]) $add_query = "and grpno = '$_POST[member]'";

                $query = "select mobile from exm_member where cid = '$cid' and mobile != '' and apply_sms='1' $add_query";
                $res = $db->query($query);
                while ($data=$db->fetch($res)){
                    $r_to[] = str_replace("-","",$data[mobile]);
                }

                break;

            case "excel":

                if (!is_uploaded_file($_FILES[file][tmp_name])) msg(_("엑셀파일을 업로드해주세요"),"./sms_popup.php");
                set_time_limit(0);

                include "../../lib/excel.reader.php";
                $xls = new Spreadsheet_Excel_Reader();
                $xls->read($_FILES[file][tmp_name]);

                foreach ($xls->sheets[0][cells] as $idx=>$data){
                    $r_to[] = iconv("euc-kr","utf-8",$data[2]);
                }

                break;
        }

        ### 유효성체크
        if (!is_array($r_to)) msg(_("받는번호를 확인해주세요."),"./sms_popup.php");

        $r_to = array_unique($r_to);
        $r_to = array_notnull($r_to);
        if (!$r_to) msg(_("받는번호를 확인해주세요."),"./sms_popup.php");

        foreach ($r_to as $to){
            foreach ($_POST[sms_msg] as $msg){
                sendSms($to,$msg,$_POST[from]); //SMS발송
            }
        }

        msg(_("SMS 전송이 완료되었습니다."), $_SERVER[HTTP_REFERER]);

        break;

	### 적립금 지급
	case "emoney":
		//set_emoney($_POST[mid],$_POST[memo],$_POST[emoney],$_POST[status]);
      setAddEmoney($cid, $_POST[mid], $_POST[emoney], $_POST[memo], '', '', '', '', $_POST[expire_day]);
    	msg(_("적립금이 지급되었습니다."),$_SERVER[HTTP_REFERER]);

    	exit;break;


	### 자동메일
	case "automail" :
		//debug($_POST); exit;

		### form 전송 취약점 개선 20160128 by kdk
		//$_POST[content] = $_POST[content];
		$_POST[content] = addslashes(base64_decode($_POST[content]));
		if(!$_POST[catnm]) $_POST[catnm] = 'mail';

		$query = "
	    insert into exm_automsg set
	        cid         = '$cid',
	        catnm       = '$_POST[catnm]',
	        type        = '$_POST[type]',
	        subject     = '$_POST[subject]',
	        msg1        = '$_POST[content]',
	        send        = '$_POST[send]',
	    	send_add_admin = '$_POST[send_add_admin]'
	    on duplicate key
	        update subject   = '$_POST[subject]', msg1 = '$_POST[content]', send = '$_POST[send]' , send_add_admin = '$_POST[send_add_admin]'
	    ";

		$db -> query($query);

		msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);

		exit ;
		break;

	### 자동SMS
	case "autosms" :
		//debug($_POST[mobile1]);
		//if ($_POST[phone]) $_POST[phone] = implode("-",$_POST[phone]);
		if ($_POST[mobile1])
			$_POST[mobile1] = implode("-", $_POST[mobile1]);

		for ($i = 0; $i < count($_POST[mobile2][1]); $i++) {
			if (!$_POST[mobile2][1][$i])
				continue;
			$mobile2[$i] = $_POST[mobile2][1][$i] . "-" . $_POST[mobile2][2][$i] . "-" . $_POST[mobile2][3][$i];
		}
		if ($mobile2)
			$mobile2 = implode("|", $mobile2);

		$db -> query("insert into exm_config set
                  cid     = '$cid',
                  config  = 'phone',
                  value = '$_POST[phone]' 
               on duplicate key
                  update value = '$_POST[phone]'");

		$db -> query("insert into exm_config set
                  cid     = '$cid',
                  config  = 'mobile1',
                  value = '$_POST[mobile1]'
               on duplicate key
                  update value = '$_POST[mobile1]'");

		$db -> query("insert into exm_config set
                  cid     = '$cid',
                  config  = 'mobile2',
                  value = '$mobile2' 
               on duplicate key 
                  update value = '$mobile2'");

		foreach ($r_title as $k => $v) {
			$send = 0;
			for ($i = 0; $i < count($_POST[send][$k]); $i++) {
				$send += $_POST[send][$k][$i];
			}

			$query = "
	        insert into exm_automsg set
	            cid         = '$cid',
	            catnm       = 'sms',
	            type        = '$v',
	            subject     = '',
	            msg1        = '{$_POST[sms_msg][$k][0]}',
	            msg2        = '{$_POST[sms_msg][$k][1]}',
	            send        = '$send'
	        on duplicate key 
	            update msg1 = '{$_POST[sms_msg][$k][0]}', msg2  = '{$_POST[sms_msg][$k][1]}', send = '$send'
	        ";
			$db -> query($query);
		}
		msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);

		exit ;
		break;

    ### 자동 카카오 알림톡
    case "autokakao" :

        foreach($_POST['alimtalk_mapping'] as $k => $v){
            $mapping_data = $db->fetch("select code from exm_alimtalk_mapping  where cid='$cid' and k_code='$k'");

            if($mapping_data){
                $query = "update exm_alimtalk_mapping 
                set code = '$v' 
                where cid = '$cid' and k_code='$k'";

            }else{
                $query ="
                    insert into exm_alimtalk_mapping set
                        cid = '$cid',
                        group_code = 'aligo',
                        code = '$v',
                        k_code = '$k'";
            }
            $db -> query($query);
        }

        $db -> query("insert into exm_config set
                cid     = '$cid',
                config  = 'alimtalk_api_key',
                value = '$_POST[alimtalk_api_key]' 
            on duplicate key 
                update value = '$_POST[alimtalk_api_key]'");

        $db -> query("insert into exm_config set
                cid     = '$cid',
                config  = 'alimtalk_sender_key',
                value = '$_POST[alimtalk_sender_key]' 
            on duplicate key 
                update value = '$_POST[alimtalk_sender_key]'");

        $db -> query("insert into exm_config set
                cid     = '$cid',
                config  = 'alimtalk_api_id',
                value = '$_POST[alimtalk_api_id]' 
            on duplicate key 
                update value = '$_POST[alimtalk_api_id]'");

        $db -> query("insert into exm_config set
                cid     = '$cid',
                config  = 'alimtalk_sender_number',
                value = '$_POST[alimtalk_sender_number]' 
            on duplicate key 
                update value = '$_POST[alimtalk_sender_number]'");

        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);

        exit ;
        break;

  ### 카카오 알림톡 코드 관리
  case "kakao_code" :

        foreach($_POST['code'] as $k => $v){
          $query = "
              update exm_alimtalk_mapping set
                  k_code = '$v'
              where cid='$cid'
              and code= ".stripslashes($k)."
              and group_code='aligo'
          ";
          $db -> query($query);
        }

        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);
        exit;
        break;

	### 회원가입설정
	case "fieldset" :

        //회원가입항목설정 사용 / 20180306 / kdk
        //회원가입항목설정 사용안함 / 20180227 / kdk
		if ($_POST[delField]) {
			$db -> query("delete from exm_config where cid = '$cid' and config = 'fieldset'");
			$field = $r_fieldset;
		} else {
			list($fieldset) = $db -> fetch("select value from exm_config where cid = '$cid' and config = 'fieldset'", 1);
			$fieldset = unserialize($fieldset);
			foreach ($_POST[field] as $k => $v) {
				$field[$k] = $v;
				if ($v[index] < 4) {
					$field[$k]['req'] = 1;
					$field[$k]['use'] = 1;
				} else {
					if (!$v['use']) {
						$field[$k]['use'] = 0;
					} else {
						$field[$k]['use'] = 1;
					}
					if (!$v['req']) {
						$field[$k]['req'] = 0;
					} else {
						$field[$k]['req'] = 1;
					}
				}
			}
		}
		$db -> query("update exm_member_grp set base = '0' where cid = '$cid' and base = '1'");
		$db -> query("update exm_member_grp set base = '1' where cid = '$cid' and grpno = '$_POST[basegrp]'");

		$flds = array(
		  'basestate' => $_POST[basestate],
		  'unableid' => $_POST[unableid],
		  'baseemoney' => $_POST[emoney],
		  'basegrp' => $_POST[basegrp],
		  'register_sms_auth' => $_POST[register_sms_auth], //sms 인증 추가 		20160302		chunter
		  'register_sms_auth_msg' => $_POST[register_sms_auth_msg],
		  'fieldset' => serialize($field)
		  );

		foreach ($flds as $k => $v) {
			$query = "
        insert into exm_config set
            cid     = '$cid',
            config  = '$k',
            value   = '$v'
        on duplicate key 
            update value = '$v'";
			$db -> query($query);
		}

		break;

	###그룹관리###
	### 회원그룹 등록
	case "addGrp" :
		if ($_POST[grpdc] < 0 || $_POST[grpdc] > 100) {
			msg(_("할인율은 0~100 사이의 숫자를 넣어주세요"), -1);
			exit ;
		}

		list($chk) = $db -> fetch("select grpnm from exm_member_grp where cid = '$cid' and grpnm = '$_POST[grpnm]'", 1);
		if ($chk) {
			msg(_("이미 동일한 이름의 그룹이 존재합니다"), -1);
			exit ;
		}

		list($grpno) = $db -> fetch("select grpno from exm_member_grp order by grpno desc", 1);

	### 회원그룹 수정
	case "modGrp" :
		if ($_POST[grpdc] < 0 || $_POST[grpdc] > 100) {
			msg(_("할인율은 0~100 사이의 숫자를 넣어주세요"), -1);
			exit ;
		}
		if ($_POST[grplv] < 1 || $_POST[grplv] > 16) {
			msg(_("레벨은 1~10사이의 숫자를 넣어주세요"), -1);
			exit ;
		}
		if ($_POST[base]) {
			$db -> query("update exm_member_grp set base = '0' where cid = '$cid'");
			$db -> query("update exm_member_grp set base = '1' where cid = '$cid' and grpno = '$_POST[grpno]'");
		}

		if ($_POST[base])
			$_POST[base] = 1;

		$flds = "
	    grpnm       = '$_POST[grpnm]',
	    grpdc       = '$_POST[grpdc]',
	    grpsc       = '$_POST[grpsc]',
	    grplv       = '$_POST[grplv]',
	    base        = '$_POST[base]',
	    adminmemo   = '$_POST[adminmemo]'
	    ";

		$query = ($_POST[mode] == "addGrp") ? "insert into exm_member_grp set $flds, cid = '$cid', grpno = $grpno+1" : "update exm_member_grp set $flds where cid = '$cid' and grpno = '$_POST[grpno]'";

		$db -> query($query);

		echo "<script>this.close();opener.location.reload();</script>";
		exit;
		break;

   case "xls_upload" :

      if ($_FILES['file']['name']){
         $uploaddir = '../../data/excel_temp/';

         if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 0707);
         } else
            @chmod($uploaddir, 0707);

         $ext = explode(".", $_FILES['file']['name']);

         $name = time() . "." . $ext[1];

         move_uploaded_file($_FILES[file][tmp_name], $uploaddir . $name);

         $excelImportFileName = $uploaddir . $name;

         $ext = substr(strrchr($excelImportFileName, "."), 1);
         $ext = strtolower($ext);

         if ($ext == "xlsx") {
            // Reader Excel 2007 file
            include "../../lib/PHPExcel.php";
            $objReader = PHPExcel_IOFactory::createReader("Excel2007");
            $objReader -> setReadDataOnly(true);
            //$objReader->setReadFilter( new MyReadFilter() );

            $objPHPExcel = $objReader -> load($excelImportFileName);
            $objPHPExcel -> setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel -> getActiveSheet();
            $xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
            foreach ($xlsData as $key => $value) {
               if ($key >= 2 && ($value[A] && $value[B])) {

                  //비밀번호 암호화
                  if($value[F])
                     $value[F] = md5($value[F]);

                  $value[N] = str_replace("..", "", $value[N]);

                  $QUERY = "
                     insert into exm_member set
                     cid                     = '$value[A]',
                     mid                     = '$value[B]',
                     bid                     = '$value[C]',
                     sort                    = -UNIX_TIMESTAMP(),
                     name                    = '$value[E]',
                     password                = '$value[F]',
                     resno                   = '$value[G]',
                     sex                     = '$value[H]',
                     calendar                = '$value[I]',
                     birth_year              = '$value[J]',
                     birth                   = '$value[K]',
                     email                   = '$value[L]',
                     apply_email             = '$value[M]',
                     zipcode                 = '$value[N]',
                     address                 = '$value[O]',
                     address_sub             = '$value[P]',
                     phone                   = '$value[Q]',
                     mobile                  = '$value[R]',
                     apply_sms               = '$value[S]',
                     regdt                   = now(),
                     lastlogin               = '$value[U]',
                     cntlogin                = '$value[V]',
                     state                   = '$value[W]',
                     grpno                   = '$value[X]',
                     emoney                  = '$value[Y]',
                     buying                  = '$value[Z]',
                     referer                 = '$value[AA]',
                     married                 = '$value[AB]',
                     cust_name               = '$value[AC]',
                     cust_type               = '$value[AD]',
                     cust_class              = '$value[AE]',
                     cust_tax_type           = '$value[AF]',
                     cust_no                 = '$value[AG]',
                     cust_ceo                = '$value[AH]',
                     cust_ceo_phone          = '$value[AI]',
                     cust_zipcode            = '$value[AJ]',
                     cust_address            = '$value[AK]',
                     cust_address_sub        = '$value[AL]',
                     cust_address_en         = '$value[AM]',
                     cust_phone              = '$value[AN]',
                     cust_fax                = '$value[AO]',
                     cust_bank_name          = '$value[AP]',
                     cust_bank_no            = '$value[AQ]',
                     cust_bank_owner         = '$value[AR]',
                     manager_no              = '$value[AS]',
                     memo                    = '$value[AT]'
                     
                     on duplicate key update
                     bid                     = '$value[C]',
                     name                    = '$value[E]',
                     password                = '$value[F]',
                     resno                   = '$value[G]',
                     sex                     = '$value[H]',
                     calendar                = '$value[I]',
                     birth_year              = '$value[J]',
                     birth                   = '$value[K]',
                     email                   = '$value[L]',
                     apply_email             = '$value[M]',
                     zipcode                 = '$value[N]',
                     address                 = '$value[O]',
                     address_sub             = '$value[P]',
                     phone                   = '$value[Q]',
                     mobile                  = '$value[R]',
                     apply_sms               = '$value[S]',
                     regdt                   = now(),
                     state                   = '$value[W]',
                     grpno                   = '$value[X]',
                     emoney                  = '$value[Y]',
                     referer                 = '$value[AA]',
                     married                 = '$value[AB]',
                     cust_name               = '$value[AC]',
                     cust_type               = '$value[AD]',
                     cust_class              = '$value[AE]',
                     cust_tax_type           = '$value[AF]',
                     cust_no                 = '$value[AG]',
                     cust_ceo                = '$value[AH]',
                     cust_ceo_phone          = '$value[AI]',
                     cust_zipcode            = '$value[AJ]',
                     cust_address            = '$value[AK]',
                     cust_address_sub        = '$value[AL]',
                     cust_address_en         = '$value[AM]',
                     cust_phone              = '$value[AN]',
                     cust_fax                = '$value[AO]',
                     cust_bank_name          = '$value[AP]',
                     cust_bank_no            = '$value[AQ]',
                     cust_bank_owner         = '$value[AR]',
                     manager_no              = '$value[AS]',
                     memo                    = '$value[AT]'
                  ;";

                  $db->query($QUERY);
               }
            }
         }
      }

      msg(_("완료되었습니다"), -1);
   break;

	###모바일 푸쉬알림 추가
	case "addPush":
		list($pushno) = $db->fetch("select pushno from exm_mobile_push order by pushno desc", 1);

	###모바일 푸쉬알림 수정
	case "modPush":
		$flds = "push_title = '$_POST[push_title]', push_message = '$_POST[push_message]'";

		$query = ($_POST[mode] == "addPush") ?
			"insert into exm_mobile_push set $flds, cid = '$cid', pushno = $pushno+1, regdt = now()" :
			"update exm_mobile_push set $flds where cid = '$cid' and pushno = '$_POST[pushno]'";
		$db->query($query);

		echo "<script>this.close();opener.location.reload();</script>";

		exit; break;

    ###회원리스트
    ### 선택/검색회원 메일전송
    case "bottom_email":

        include "../../lib/class.mail.php";

        $mail = new Mail($params);
        $headers['From']    = $cfg[emailAdmin];
        $headers['Name']    = $cfg[nameSite];
        $headers['Subject'] = $_POST[subject];

        echo "<style>body {margin:0;font:8pt tahoma}</style>";

        if($_POST[range] == 'selmember') $query = stripslashes(base64_decode($_POST[mquery]));
        else if($_POST[range] == 'allmember') $query = base64_decode($_POST[mquery]);
         $res = $db->query($query);
        $to = array();
        while ($data=$db->fetch($res)){
            $headers['To']  = $data[email];
            $mail->send($headers, $_POST[contents]);
            echo "<b>[".(++$idx)."]</b> $data[email] - $data[name] <br><script>scroll(0,99999999)</script>";
            flush();
            $to[] = $data[email];
        }
        emailLog(array("to"=>$to,"subject"=>$headers['Subject'],"contents"=>$_POST[contents]),count($to));
        echo "<p>"._("전체")." <b>".number_format($idx)."</b>"._("건의 메일이 성공적으로 발송되었습니다")."<br><script>scroll(0,99999999)</script>";

        //emailLog($_POST,$idx);
        exit; break;

    ### 선택/검색회원 SMS전송
    case "bottom_sms":

        $query = stripslashes(base64_decode($_POST[mquery]));
        $res = $db->query($query);

        while ($data=$db->fetch($res)){
            $dataTo[] = str_replace("-","",$data[mobile]);
        }

        $msg = array_notnull($_POST[msg]);

        ### 유효성체크
        $cnt = count($dataTo);
        if ($cnt<=0) break;

        foreach ($_POST[msg] as $k=>$msg){
            if (!trim($msg)) continue;
            sendSms($dataTo,$msg,$_POST[from]); //SMS발송
    //      debug($_POST[msg]);
        }

        msg(_("SMS전송이 완료되었습니다."));
        echo "<script>parent.location.reload();</script>";
        exit; break;

    ### 선택/검색회원 적립금 지급
    case "bottom_emoney":

        $db->start_transaction();

        $query = stripslashes(base64_decode($_POST[mquery]));
        $res = $db->query($query);
        while ($data=$db->fetch($res)){
       		//set_emoney($data[mid],$_POST[emoney_memo],$_POST[emoney],$_POST[status]);
					setAddEmoney($cid, $data[mid], $_POST[emoney], $_POST[emoney_memo], $sess_admin[mid]);
        }

        $db->end_transaction();

        echo "<script>alert('"._("적립금이 지급되었습니다.")."');parent.location.reload();</script>";

        exit;

    //  msg("적립금이 지급되었습니다.",$_SERVER[HTTP_REFERER]);exit;

        break;

    ### 선택/검색회원 그룹변경
    case "bottom_grp":

        $query = stripslashes(base64_decode($_POST[mquery]));
        $res = $db->query($query);
        while ($data=$db->fetch($res)){
            $db->query("update exm_member set grpno ='$_POST[grpno]' where cid='$cid' and mid='$data[mid]'");
        }
        msg(_("그룹이 정상적으로 변경되었습니다."),$_SERVER[HTTP_REFERER]);

        exit; break;

        break;

    ### 선택/검색회원 기업그룹변경
    case "bottom_business":

        $query = stripslashes(base64_decode($_POST[mquery]));
        $res = $db->query($query);

        while ($data=$db->fetch($res)){
            $db->query("update exm_member set bid ='$_POST[bid]' where cid='$cid' and mid='$data[mid]'");
        }
        msg(_("기업그룹이 정상적으로 변경되었습니다."),$_SERVER[HTTP_REFERER]);

        exit; break;

        break;

    ### 선택/검색회원 정산담당자변경
    case "bottom_manager":

        $query = stripslashes(base64_decode($_POST[mquery]));
        $res = $db->query($query);

        while ($data=$db->fetch($res)){
            $db->query("update exm_member set manager_no ='$_POST[manager_no]' where cid='$cid' and mid='$data[mid]'");
        }
        msg(_("정산담당자가 정상적으로 변경되었습니다."),$_SERVER[HTTP_REFERER]);

        exit; break;

        break;

    ### 선택/검색회원 승인여부
    case "bottom_state":

        $query = stripslashes(base64_decode($_POST[mquery]));
        $res = $db->query($query);
        while ($data=$db->fetch($res)){
            $db->query("update exm_member set state='$_POST[state]' where cid='$cid' and mid='$data[mid]'");
        }
        msg(_("승인여부가 정상적으로 변경되었습니다."),$_SERVER[HTTP_REFERER]);

        exit; break;

    ### 쿠폰 발급
    case "bottom_coupon":

       $query = stripslashes(base64_decode($_POST[mquery]));
       $res = $db->query($query);
       while ($data=$db->fetch($res)){
          list($chk) = $db->fetch("select no from exm_coupon_set where cid = '$cid' and coupon_code = '$_POST[couponno]' and mid = '$data[mid]'",1);
             $query = "
                insert into exm_coupon_set set
                   cid             = '$cid',
                   coupon_code     = '$_POST[couponno]',
                   mid             = '$data[mid]',
                   coupon_setdt    = now()
             ";
             $db->query($query);
       }
       msg(_("쿠폰발급이 완료되었습니다."),$_SERVER[HTTP_REFERER]);

       exit; break;

	case "sendmail" :
		$_POST[contents] = addslashes(base64_decode($_POST[contents]));
		$_POST[to] = explode(";",$_POST[to]);

		include "../../lib/class.mail.php";

		$mail = new Mail($params);

		if($cfg[emailAdmin]){
			$headers['From']    = $cfg[emailAdmin];
		}else{
			$headers['From'] = "noreply@bluepod.co.kr";
		}

        if($cfg[nameSite]){
            $headers['Name']    = $cfg[nameSite];
        }else{
            $headers['Name'] = $_SERVER['HTTP_HOST'];
        }

		$headers['Subject'] = $_POST[subject];

		echo "<style>body {margin:0;font:8pt tahoma}</style>";
		$to = array();
		if($_POST[vtype]){

			switch ($_POST[vtype]){

				case "to":
					foreach ($_POST[to] as $k=>$v){
						$headers['To']      = $v;
						$to[] = $v;
						$mail->send($headers, $_POST[contents]);
						if ($_POST[mode2]!="popup") echo "<b>[".(++$idx)."]</b> $v <span style='color:red'>[발송완료]</span></br>";
					}
					break;

				case "member":

					if ($_POST[member]) $add_query = "and grpno = '$_POST[member]'";

					$query = "select email, name from exm_member where cid = '$cid' and email != '' and apply_email='1' $add_query";
					$res = $db->query($query);
					while ($data=$db->fetch($res)){
						$to[] = $data[email];
						$headers['To']  = $data[email];
						$mail->send($headers, $_POST[contents]);
						if ($_POST[mode2]!="popup") echo "<b>[".(++$idx)."]</b> $data[email] - $data[name] <br><script>scroll(0,99999999)</script>";
						flush();
					}
				break;
			}

		} else {
			//vtype값이 없으면 메일 주소가 하나이기 대문에 $to에 POST로 받은 메일값을 넣어주고 메일을 보낸다 / 14.09.01 / kjm
			$to = $_POST[to][0];
			$headers['To']  = $to;
			$mail->send($headers, $_POST[contents]);
		}

		emailLog(array("to"=>$to,"subject"=>$headers['Subject'],"contents"=>$_POST[contents]),count($to));
		if ($_POST[mode2]=="popup"){
			$idx = 1; echo "<script>this.close();opener.location.reload();</script>";
		}
		exit; break;

}

/* member_list.php / cid_address_list.php 에서 호출  */
switch ($_GET[mode]) {
	case "login" :
		//그룹이 선택되지 않은 회원이 로그인시 해당 그룹 데이터가 없기때문에
		//cid값이 null로 처리되어서 select 값을 지정해줌 / 14.12.01 / kjm
		$query = "
      select a.*, b.grpnm from 
         exm_member a
         left join exm_member_grp b on a.grpno = b.grpno and a.cid = b.cid
      where
         mid = '$_GET[mid]'
         and a.cid = '$cid'
      ";
		$data = $db -> fetch($query);

		_member_login($data);
      go("/");

		exit ;
		break;

	case "delMember" :

        $sql = "select * from exm_member where cid='$cid' and mid='$_GET[mid]'";
        $data = $db -> fetch($sql);
        if($data) {
            $sql1 = "insert into exm_member_out set 
                        cid='$cid',
                        mid='$data[mid]',
                        name='',
                        email='',
                        regdt='$data[regdt]',
                        outdt=now()";
            $db -> query($sql1);

            $query = "delete from exm_member where cid = '$cid' and mid = '$_GET[mid]'";
            $db -> query($query);
        }

		break;

	### 모바일 푸쉬알림 삭제
	case "delPush":
		$query = "delete from exm_mobile_push where cid = '$cid' and pushno = '$_GET[pushno]'";
    	$db->query($query);

		$query2 = "delete from exm_log_mobile_push_resend where cid = '$cid' and pushno = '$_GET[pushno]'";
		$db->query($query2);

		break;

	### 모바일 푸쉬알림 보내기
	case "sendPush":
	case "resendPush":
		list($pushno, $push_title, $push_message) = $db->fetch("select pushno, push_title, push_message from exm_mobile_push where cid = '$cid' and pushno = '$_GET[pushno]'", 1);

		if ($pushno) {
			$registrationID = array();
			$resendID = array();
			$deleteID = array();
			$updateID = array();

			if ($_GET[mode] == "sendPush") {
				$query = "select * from tb_mobile_registration_id where cid = '$cid'";
				$res = $db->query($query);
			} else if ($_GET[mode] == "resendPush") {
				$query = "select * from exm_log_mobile_push_resend where cid = '$cid' and pushno = '$pushno'";
				$res = $db->query($query);
			}

			while ($data = $db->fetch($res)) {
				if ($data[registration_id]) $registrationID[] = $data[registration_id];
			}

			$registrationID_array = array_chunk($registrationID, 1000);

			for ($i=0; $i<count($registrationID_array); $i++) {
				$result = sendMobilePush($registrationID_array[$i], $push_title, $push_message);

				$logdir = dirname(__FILE__) . "/../../dblog/push_log/";

		        if (!is_dir($logdir)) {
		            mkdir($logdir, 0707);
		            chmod($logdir, 0707);
		        }

		        $filename = date("Ymd")."_push";
		        $fp = fopen($logdir . $filename, "a");
		        $logstr = "
		        ------------------------------------------------------------------------
		        " . date("Y-m-d h:i:s") . "
		        registrationID : ".implode(",", $registrationID_array[$i])."
		        title : $push_title
	        	message : $push_message
		        result : $result
		        remote_addr : $_SERVER[REMOTE_ADDR]
		        ";

		        fwrite($fp, $logstr);

				$result = json_decode($result);
				$failure = $result->{"failure"};
				$canonical_ids = $result->{"canonical_ids"};
	   			$results = $result->{"results"};

				if ($failure > 0 || $canonical_ids > 0) {
					foreach ($results as $k=>$v) {
						if ($v->{"error"}) {
							switch ($v->{"error"}) {
								case "Unavailable":
									$resendID[] = $registrationID_array[$i][$k];
									break;
								case "InvalidRegistration":
								case "NotRegistered":
									$deleteID[] = $registrationID_array[$i][$k];
									break;
							}
						} else if ($v->{"registration_id"}) {
							$updateID[$registrationID_array[$i][$k]] = $v->{"registration_id"};
						}
					}
				}
			}

			$query4 = "delete from exm_log_mobile_push_resend where cid = '$cid' and pushno = '$pushno'";
			$db->query($query4);

			//재전송
			if (count($resendID) > 0) {
				$query3 = "insert into exm_log_mobile_push_resend (cid, pushno, registration_id, regdt) values ";

				foreach ($resendID as $k2=>$v2) {
					$query3 .= "('$cid', '$pushno', '$v2', now()),";
				}

				$query3 = substr($query3, 0, -1);
				$db->query($query3);
			}

			//사용자아이디 삭제
			if (count($deleteID) > 0) {
				$query3 = "delete from tb_mobile_registration_id where cid = '$cid' and registration_id in ('".implode("','", $deleteID)."')";
				$db->query($query3);
			}

			//사용자아이디 수정
			if (count($updateID) > 0) {
				foreach ($updateID as $k2=>$v2) {
					$query3 = "update tb_mobile_registration_id set registration_id = '$v2' where cid = '$cid' and registration_id = '$k2'";
					$db->query($query3);
				}
			}

			if ($_GET[mode] == "sendPush") {
				$query2 = "update exm_mobile_push set senddt = now() where cid = '$cid' and pushno = '$pushno'";
				$db->query($query2);
			}

			msg(_("발송이 완료되었습니다."), $_SERVER[HTTP_REFERER]);
		} else {
			$query = "delete from exm_log_mobile_push_resend where cid = '$cid' and pushno = '$_GET[pushno]'";
			$db->query($query);

			msg(_("유효하지 않는 푸쉬알림입니다."), -1);
		}

		break;

	case "address_remove" :
		$query = "delete from exm_address where cid = '$cid' and addressno = '$_GET[addressno]'";
		$db -> query($query);
		break;
}

/* member_form.php / member_modify.php / cid_address_write.php 에서 호출  */
switch ($_POST[mode]) {
	case "member_join_A" :

      if($_POST[birth]) $_POST[birth] = implode("", array_notnull($_POST[birth]));
      if($_POST[email]) $_POST[email] = implode("@", array_notnull($_POST[email]));
      if($_POST[phone]) $_POST[phone] = implode("-", array_notnull($_POST[phone]));
      if($_POST[mobile]) $_POST[mobile] = implode("-", array_notnull($_POST[mobile]));

      if($_POST[cust_no]) $_POST[cust_no] = @implode("-", array_notnull($_POST[cust_no]));
      if($_POST[cust_ceo_phone]) $_POST[cust_ceo_phone] = @implode("-", array_notnull($_POST[cust_ceo_phone]));
      if($_POST[cust_phone]) $_POST[cust_phone] = @implode("-", array_notnull($_POST[cust_phone]));
      if($_POST[cust_fax]) $_POST[cust_fax] = @implode("-", array_notnull($_POST[cust_fax]));

      $password = passwordCommonEncode($_POST[password]);

		$query = "insert into exm_member set
                  cid         = '$cid',
                  mid         = '$_POST[mid]',
                  password    = '$password',
                  regdt       = now(),
                  sort        = -UNIX_TIMESTAMP(),
                  name        = '$_POST[name]',
                  sex         = '$_POST[sex]',
                  calendar    = '$_POST[calendar]',
                  birth_year  = '$_POST[birth_year]',
                  birth       = '$_POST[birth]',
                  email       = '$_POST[email]',
                  apply_email = '$_POST[apply_email]',
                  zipcode     = '$_POST[zipcode]',
                  address     = '$_POST[address]',
                  address_sub = '$_POST[address_sub]',
                  phone       = '$_POST[phone]',
                  mobile      = '$_POST[mobile]',
                  state       = '$_POST[state]',
                  #grpno      = '$_POST[grp]',
                  bid         = '$_POST[bid]',
                  
                  married            = '$_POST[married]',
                  cust_name          = '$_POST[cust_name]',
                  cust_type          = '$_POST[cust_type]',
                  cust_class         = '$_POST[cust_class]',
                  cust_tax_type      = '$_POST[cust_tax_type]',
                  cust_no            = '$_POST[cust_no]',
                  cust_ceo           = '$_POST[cust_ceo]',
                  cust_ceo_phone     = '$_POST[cust_ceo_phone]',
                  cust_zipcode       = '$_POST[cust_zipcode]',
                  cust_address       = '$_POST[cust_address]',
                  cust_address_sub   = '$_POST[cust_address_sub]',
                  cust_address_en    = '$_POST[cust_address_en]',
                  cust_phone         = '$_POST[cust_phone]',
                  cust_fax           = '$_POST[cust_fax]',                  
                  manager_no         = '$_POST[manager_no]',                  
                  
                  memo        = '$_POST[memo]'
      ";

		$db -> query($query);
		break;

	case "member_modify_A" :

      if($_POST[birth]) $_POST[birth] = implode("", array_notnull($_POST[birth]));
		if($_POST[email]) $_POST[email] = implode("@", array_notnull($_POST[email]));
		if($_POST[phone]) $_POST[phone] = implode("-", array_notnull($_POST[phone]));
		if($_POST[mobile]) $_POST[mobile] = implode("-", array_notnull($_POST[mobile]));

      if($_POST[cust_no]) $_POST[cust_no] = @implode("-", array_notnull($_POST[cust_no]));
      if($_POST[cust_ceo_phone]) $_POST[cust_ceo_phone] = @implode("-", array_notnull($_POST[cust_ceo_phone]));
      if($_POST[cust_phone]) $_POST[cust_phone] = @implode("-", array_notnull($_POST[cust_phone]));
      if($_POST[cust_fax]) $_POST[cust_fax] = @implode("-", array_notnull($_POST[cust_fax]));

		if ($_POST[password]){
		   $password = passwordCommonEncode($_POST[password]);
			$fld = "password = '$password',";
		}

		$query = "update exm_member set
                  $fld
                  name        = '$_POST[name]',
                  sex         = '$_POST[sex]',
                  calendar    = '$_POST[calendar]',
                  birth_year  = '$_POST[birth_year]',
                  birth       = '$_POST[birth]',
                  email       = '$_POST[email]',
                  apply_email = '$_POST[apply_email]',
                  apply_sms   = '$_POST[apply_sms]',
                  zipcode     = '$_POST[zipcode]',
                  address     = '$_POST[address]',
                  address_sub = '$_POST[address_sub]',
                  phone       = '$_POST[phone]',
                  mobile      = '$_POST[mobile]',
                  state       = '$_POST[state]',
                  grpno       = '$_POST[grp]',
                  bid         = '$_POST[bid]',

                  married            = '$_POST[married]',
                  cust_name          = '$_POST[cust_name]',
                  cust_type          = '$_POST[cust_type]',
                  cust_class         = '$_POST[cust_class]',
                  cust_tax_type      = '$_POST[cust_tax_type]',
                  cust_no            = '$_POST[cust_no]',
                  cust_ceo           = '$_POST[cust_ceo]',
                  cust_ceo_phone     = '$_POST[cust_ceo_phone]',
                  cust_zipcode       = '$_POST[cust_zipcode]',
                  cust_address       = '$_POST[cust_address]',
                  cust_address_sub   = '$_POST[cust_address_sub]',
                  cust_address_en    = '$_POST[cust_address_en]',
                  cust_phone         = '$_POST[cust_phone]',
                  cust_fax           = '$_POST[cust_fax]',                  
                  manager_no         = '$_POST[manager_no]',
                  
                  memo        = '$_POST[memo]'
               where cid = '$cid' and mid = '$_POST[mid]'
               ";

		$db -> query($query);
		break;

	case "address_write" :
		$query = "insert into exm_address set
			cid		  	   = '$_POST[cid]',
			mid		  	   = '',		
			addressnm            = '$_POST[addressnm]',
			receiver_name          = '$_POST[receiver_name]',
			receiver_phone          = '$_POST[receiver_phone]',
			receiver_mobile         = '$_POST[receiver_mobile]',
			receiver_zipcode      = '$_POST[receiver_zipcode]',
			receiver_addr            = '$_POST[address]',
			receiver_addr_sub           = '$_POST[receiver_addr_sub]'";
		$db -> query($query);
		break;

	case "address_modify" :
		$query = "update exm_address set
                  addressnm            = '$_POST[addressnm]',
                  receiver_name          = '$_POST[receiver_name]',
                  receiver_phone          = '$_POST[receiver_phone]',
                  receiver_mobile         = '$_POST[receiver_mobile]',
                  receiver_zipcode      = '$_POST[receiver_zipcode]',
                  receiver_addr            = '$_POST[address]',
                  receiver_addr_sub           = '$_POST[receiver_addr_sub]'
               where cid = '$cid' and addressno = '$_POST[addressno]'";
		$db -> query($query);
		break;
}

if (!$_POST[rurl])
	$_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
?>
