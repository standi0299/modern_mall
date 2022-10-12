<?

include "../lib.php";

/***/switch ($_POST[mode]){/***/
case "xls":

	if ($_POST['delete']){
		$db->query("delete from exm_xls_case where no = '$_POST[no]'");
		msg(_("삭제되었습니다."),$_SERVER[HTTP_REFERER]);
		exit;
	}

	if (is_array($_POST[columns])){
		$_POST[columns] = implode("|@|",$_POST[columns]);
	}

	### 저장 선택시
	if ($_POST[save]){
		$flds = "
			`cid`		= '$cid',
			`mode`		= '$_POST[case]',
			`default`	= '$_POST[default]',
			`columns`	= '$_POST[columns]'
		";

		$query = (!$_POST[no]) ? 
		"
		insert into exm_xls_case set
			`name`		= '$_POST[name]',
			$flds
		":
		"
		update exm_xls_case set
			$flds
		where no = '$_POST[no]'
		";
		$db->query($query);
		$no = ($_POST[no]) ? $_POST[no]:$db->id;

		if ($_POST['default']){
			$query = "
			update exm_xls_case set
				`default` = 0
			where
				`cid` = '$cid'
				and `mode` = '$_POST[case]'
				and `no` != '$no'
			";
            $db->query($query);
        }
	}

	if (!$_POST[mode_opt]){
		msg(_("저장되었습니다."),$_SERVER[HTTP_REFERER]);
		exit;
	}

	if ($_POST[mode_opt]=="download"){
		# 엑셀 컬럼파일 참조
		$column_file = dirname(__FILE__)."/column/xls.column.".$_POST['case'].".php";
        if (!is_file($column_file)){
			echo _("컬럼파일")." : '<b class='red'>$column_file</b>' "._("없음");
            exit;
        }
		include $column_file;

        $columns = explode("|@|",$_POST[columns]);
        $columns2 = array_keys(array_keys($r_column));

        $query = urldecode(base64_decode($_POST[query]));

        include "form/form.".$_POST['case'].".php";
    }
	exit; break;

/***/}/***/

?>