<?

include "../lib.php";

function _ilark_vars($vars,$flag=";"){
	$r = array();
	$div = explode($flag,$vars);
	foreach ($div as $tmp){
		$pos = strpos($tmp,"=");
		list ($k,$v) = array(substr($tmp,0,$pos),substr($tmp,$pos+1));
		if (!$k) continue;
		$r[$k] = $v;
	}
	return $r;
}

/***/switch ($_POST[mode]){/***/
case "xls":

	### form 전송 취약점 개선 20160128 by kdk
	if(isset($_POST[pod_signed]) && isset($_POST[pod_expired])) {
		//debug($cid);
		//debug($_POST[pod_signed]);
		//debug($_POST[pod_expired]);
		//debug($_POST[query]);
		//debug($_SERVER);
		if(exp_compare($_POST[pod_expired])) {
			//debug("not expired!");
			$url_query = $_SERVER[REQUEST_URI]."?query=".$_POST[query];
			//debug($url_query);
			$signedData = signatureData($cid, $url_query);
			//debug($signedData);
			if (sig_compare($signedData, $_POST[pod_signed])) {
				$query = urldecode(base64_decode($_POST[query]));
			} 
			else {
				//debug("sig not match!");
				msg(_("서버키 검증에 실패했습니다."),$_SERVER[HTTP_REFERER]);exit;
			}
		}
		else {
			//debug("expired!");
			msg(_("서버키가 만료되었습니다."),$_SERVER[HTTP_REFERER]);exit;
		}
	}
	else {
		$query = urldecode(base64_decode($_POST[query]));
	}
	//debug($_POST[query]);
	//exit;		
	### form 전송 취약점 개선 20160128 by kdk

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
		
		//column값이 있을때와 없을때 include하는 column파일을 다르게 지정 / 14.05.15 / kjm
		if($_POST[column]){
			//셀 병합 여부 체크  200106  jtkim
			if($_REQUEST[rowspan_chk] == 1) $_POST[column] .= "_no_rowspan";

    		$column_file = dirname(__FILE__)."/column/xls.column.".$_POST[column].".php";
    		if (!is_file($column_file)){
    			echo _("컬럼파일")." : '<b class='red'>$column_file</b>' "._("없음");
    			exit;
    		}
    		include $column_file;
        } else {
			//셀 병합 여부 체크  200106  jtkim
			if($_REQUEST[rowspan_chk] == 1) $_POST[column] .= "_no_rowspan";

            $column_file = dirname(__FILE__)."/column/xls.column.".$_POST['case'].".php";
            if (!is_file($column_file)){
                echo _("컬럼파일")." : '<b class='red'>$column_file</b>' "._("없음");
                exit;
            }
        include $column_file;
        }

		$columns = explode("|@|",$_POST[columns]);
		$columns2 = array_keys(array_keys($r_column));

		//$query = urldecode(base64_decode($_POST[query]));
		//$query = $_POST[query];

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$_POST['case'].date("YmdHi").".xls");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");

		include "form/form.".$_POST['case'].".php";
	}

	exit; break;
    
case "studioXls":
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=".$_POST['case'].date("YmdHi").".xls");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    header("Pragma: public");

    include "form/form.".$_POST['case'].".php";

exit; break;
/***/}/***/

?>