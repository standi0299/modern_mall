<?
/***
 * MAIL Amazon SES Class
 * 
 * $headers['Name']      = "{보내는 사람 이름}";
 * $headers['To']      = "{받는 사람 email 주소}";
 * $headers['Subject'] = "{제목}";
 * $body = "{보내는 내용}";
  *
 * $mail = new MailAmazon(); 
 * $mail->send($headers, $body);
*/

require_once(dirname(__FILE__)."/PHPMailer_5_2_4/class.phpmailer.php");

class MailAmazon
{
	var $mail;
	var $errMsg;

	function MailAmazon()
	{		
		$this->mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
		$this->mail->ContentType = "text/html";
		//$mail->CharSet = 'euc-kr'; 
		$this->mail->CharSet = "utf-8";  //한글깨짐 방지를 위한 문자 인코딩설정
		$this->mail->Encoding = "base64";
		
		$this->mail->IsSMTP(); 					// telling the class to use SMTP
				
		$this->mail->Host = "email-smtp.us-east-1.amazonaws.com"; // email 보낼때 사용할 서버를 지정
  	$this->mail->SMTPAuth = true; 	// SMTP 인증을 사용함
  	$this->mail->Port = 25; 				// email 보낼때 사용할 포트를 지정
  
  	//$mail->SMTPSecure = "ssl"; 		// SSL을 사용함
  	$this->mail->SMTPSecure = 'tls';
  	$this->mail->Username   = "AKIAJX5B77KQ2TWFFM3A"; 				// 계정
  	$this->mail->Password   = "AvpWNjZej28WQjW0Jfrk4Y+/YqrkAlc4nlFkfKlhRsET"; // 패스워드

  	//$mail->SetFrom('noreply@bluepod.co.kr', '아이락'); // 보내는 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)
  	$this->mail->From = 'noreply@bluepod.co.kr';		// 보내는 사람 email. 아마존 가입 email 이여야 한다.
	}

	function error($msg)
	{
		$this->errMsg = $msg;
		//echo "<span style='font:8pt tahoma'><b>[ERROR]</b> $msg</span>";
		return false;
	}

	function send($headers, $body)
	{
		//global $cfg;
		try {
			$body = stripslashes($body);
			   		
			//$this->mail->FromName = $cfg[nameSite];
			$this->mail->FromName = $headers['Name'];			//보내는 사람 이름
    	$this->mail->AddAddress($headers['To']); // 받을 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)
    	$this->mail->Subject = $headers['Subject']; // 메일 제목
    	
    	//$mail->MsgHTML(file_get_contents('contents.html')); // 메일 내용 (HTML 형식도 되고 그냥 일반 텍스트도 사용 가능함)
    	$this->mail->MsgHTML($body); // 메일 내용 (HTML 형식도 되고 그냥 일반 텍스트도 사용 가능함)

    	$this->mail->Send();
    	return true;

		}catch (phpmailerException $e) {
			return $this->error('sendmail returned error code ' . $e->errorMessage());
    	//echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			return $this->error('sendmail returned error code ' . $e->errorMessage());
    	//echo $e->getMessage(); //Boring error messages from anything else!
		}
	}

	function setHTMLBody($data, $isfile = false)
	{
  	if (!$isfile) return $data;
		else return $this->_file2str($isfile);
	}

	function _file2str($file_name)
	{
		if (!is_readable($file_name)){
			return $this->error('File is not readable ' . $file_name);
		}
		if (!$fd = fopen($file_name, 'rb')) {
			return $this->error('Could not open ' . $file_name);
		}
		$cont = fread($fd, filesize($file_name));
		fclose($fd);
		return $cont;
	}
}
?>