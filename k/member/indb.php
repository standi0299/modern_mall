<?

include "../lib.php";

switch ($_POST[mode]) {
	### 자동메일
	case "automail" :
		//debug($_POST); exit;

		### form 전송 취약점 개선 20160128 by kdk
		//$_POST[content] = $_POST[content];
		$_POST[content] = addslashes(base64_decode($_POST[content]));

		$query = "
	    insert into exm_automsg set
	        cid         = '$cid',
	        catnm       = 'mail',
	        type        = '$_POST[type]',
	        subject     = '$_POST[subject]',
	        msg1        = '$_POST[content]',
	        send        = '$_POST[send]'
	    on duplicate key
	        update subject   = '$_POST[subject]', msg1 = '$_POST[content]', send = '$_POST[send]'
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

	### 회원가입설정
	case "fieldset" :
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

		$flds = array('basestate' => $_POST[basestate], 'unableid' => $_POST[unableid], 'baseemoney' => $_POST[emoney], 'basegrp' => $_POST[basegrp], 'register_sms_auth' => $_POST[register_sms_auth], //sms 인증 추가 		20160302		chunter
		'register_sms_auth_msg' => $_POST[register_sms_auth_msg], 'fieldset' => serialize($field));

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
		if ($_POST[grpdc] >= 100) {
			msg("할인율은 0~100 사이의 숫자를 넣어주세요", -1);
			exit ;
		}

		list($chk) = $db -> fetch("select grpnm from exm_member_grp where cid = '$cid' and grpnm = '$_POST[grpnm]'", 1);
		if ($chk) {
			msg("이미 동일한 이름의 그룹이 존재합니다", -1);
			exit ;
		}

		list($grpno) = $db -> fetch("select grpno from exm_member_grp order by grpno desc", 1);

	### 회원그룹 수정
	case "modGrp" :
		if ($_POST[grpdc] < 0 || $_POST[grpdc] > 100) {
			msg("할인율은 0~100 사이의 숫자를 넣어주세요", -1);
			exit ;
		}
		if ($_POST[grplv] < 1 || $_POST[grplv] > 16) {
			msg("레벨은 1~10사이의 숫자를 넣어주세요", -1);
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
	###그룹관리###
}

/* member_list.php에서 호출  */
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

		$m_pretty = new M_pretty();
		$year = getKidsServiceYear();

		$folder_data = $m_pretty -> getFolderList($cid, $_GET[mid], $year[0]);

		include_once "../../pretty/_module_util.php";
		//해당 서비스 년도의 폴더가 하나도 없으면 기본 폴더 생성 / 16.03.21 / kjm
		if (!$folder_data) {
			makeBasicFolder($year[0], $cid, $_GET[mid]);
			//휴지통 폴더 추가
			makeTrashFolder($year[0], $cid, $_GET[mid]);
			//대분류 폴더 정렬값
			makeFolderKindOrder($year[0], $cid, $_GET[mid]);
		} else {
			$trashfolder_chk = $m_pretty -> getFolderList($cid, $_GET[mid], '', 'U');
			if (!$trashfolder_chk) {
				//휴지통 폴더 추가
				makeTrashFolder($year[0], $cid, $_GET[mid]);
			}
		}

		_member_login($data);

		if (in_array($cfg[skin], $r_kids_skin))
			go("/main/main.php");
		else
			go("/");

		exit ;
		break;

	case "delMember" :
		$query = "delete from exm_member where cid = '$cid' and mid = '$_GET[mid]'";
		$db -> query($query);
		break;
}

/* member_form.php / member_modify.php 에서 호출  */
switch ($_POST[mode]) {
	case "member_join_A" :
		$_POST[birth] = implode("", array_notnull($_POST[birth]));
		$_POST[email] = implode("@", array_notnull($_POST[email]));
		$_POST[phone] = implode("-", array_notnull($_POST[phone]));
		$_POST[mobile] = implode("-", array_notnull($_POST[mobile]));

		$query = "insert into exm_member set
                  cid         = '$cid',
                  mid         = '$_POST[mid]',
                  password    = md5('$_POST[password]'),
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
                  memo        = '$_POST[memo]'
      ";

		$db -> query($query);
		break;

	case "member_modify_A" :
		$_POST[birth] = implode("", array_notnull($_POST[birth]));
		$_POST[email] = implode("@", array_notnull($_POST[email]));
		$_POST[phone] = implode("-", array_notnull($_POST[phone]));
		$_POST[mobile] = implode("-", array_notnull($_POST[mobile]));

		if ($_POST[password])
			$fld = "password = md5('$_POST[password]'),";

		$query = "update exm_member set
                  $fld
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
                  memo        = '$_POST[memo]'
                where cid = '$cid' and mid = '$_POST[mid]'
               ";

		$db -> query($query);
		break;
}

if (!$_POST[rurl])
	$_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
?>