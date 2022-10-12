<?
/*
* @date : 20180710
* @author : chunter
* @brief : 메일 발송.
* @desc : 메일 로그에 저장된 내용으로 발송한다.	필수 파라미터 log_mail_no
*/
?>
<?
include "../lib/library.php";
include "../lib/class.mail.amazon.php";
header("Content-type: text/xml; charset=utf-8");

$log_mail_no = $_REQUEST[log_mail_no];

# 1. 주문상품코드 전달 여부 체크
if (!trim($log_mail_no)){
	$ret[success] = false;
	$ret[error] = "필수 코드 누락";
	echo json_encode($ret);
	exit;
}

	if ($log_mail_no)
	{
		$log_mail_no_arr = explode(",", $log_mail_no);
		foreach ($log_mail_no_arr as $value) {
			$log_no = trim($value);
			if (!$log_no) break;
			
			$data = $db->fetch("select * from exm_log_email where no = '$log_no' and cid = '$cid'");			
			//print_r($data);
			
			$mail = new MailAmazon();
			$headers['Name']    = $cfg[nameSite];
			$headers['From']    = $cfg[emailAdmin];
			$headers['Subject'] = $data[subject];
			
			$mailToArr = explode(";",$data[to]);
			$mailContents = stripslashes($data[contents]);
			foreach ($mailToArr as $mvalue) {
				$mailTo = trim($mvalue);				
				if (!$mailTo) break;				
				
				$headers['To']      = $mailTo;				
				//print_r($headers);
				$mail->send($headers, $mailContents);
			}
		}
		$success = "1";
	}

?>