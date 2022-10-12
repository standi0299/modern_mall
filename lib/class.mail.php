<?

/***
 * MAIL Class
 *
 * $headers['From']    = "{보내는 사람}";
 * $headers['To']      = "{받는 사람}";
 * $headers['Subject'] = "{제목}";
 * $body = "{보내는 내용}";
 * //$params['sendmail_path'] = "/usr/sbin/sendmail";
 *
 * $mail = new Mail($params);
 * //$mail->bodyFile = "{보내는 내용 템플릿 파일}";
 * $mail->send($headers, $body);
*/

class Mail
{
	var $sep = "\n";
	var $sendmail_args = '';
	var $sendmail_path = '/usr/sbin/sendmail';

	function Mail($params)
	{
		if (isset($params['sendmail_path'])) $this->sendmail_path = $params['sendmail_path'];
      if (isset($params['sendmail_args'])) $this->sendmail_args = $params['sendmail_args'];
	}

	function error($msg)
	{
		//echo "<span style='font:8pt tahoma'><b>[ERROR]</b> $msg</span>";
		return false;
	}

	function send($headers, $body) {
      global $cfg;
      
      $body = stripslashes($body);

		$headers[Subject] = iconv("utf-8","euc-kr",$headers[Subject]);

		$headerElements = $this->prepareHeaders($headers);
		list($from, $text_headers) = $headerElements;

		if (!isset($from)) return $this->error('No from address given');
		if (!$this->recipients) return $this->error('No to address given');

		$body = $this->setHTMLBody($body,$this->bodyFile);
		$body = chunk_split(base64_encode($body));

		$result = 0;
		if (@is_file($this->sendmail_path)) {
			$from = escapeShellCmd($from);
			$return = $cfg[emailAdmin];	// 리턴메일 주소 설정

			$mail = popen($this->sendmail_path . (!empty($this->sendmail_args) ? ' ' . $this->sendmail_args : '') . " -f$return -- $this->recipients", 'w');
			fputs($mail, $text_headers);
			fputs($mail, $this->sep);
			fputs($mail, $body);
			$result = pclose($mail) >> 8 & 0xFF;
		} else {
			$ret = mail($headers['To'],$headers['Subject'],$body,$text_headers);
         //debug($ret);
			if (!$ret) return $this->error('sendmail [' . $this->sendmail_path . '] is not a valid file');
			//return $this->error('sendmail [' . $this->sendmail_path . '] is not a valid file');
		}

		if ($result != 0) {
			return $this->error('sendmail returned error code ' . $result);
		}

		return true;
	}

	function encode_2047($subject)
	{
      return '=?euc-kr?b?'.base64_encode($subject).'?=';
		//return '=?utf-8?B?'.base64_encode($subject).'?=';
	}

   function prepareHeaders($headers)
   {
		$lines	= array();
		$from	= null;

		$headers['MIME-Version'] = '1.0';
		//$headers['Content-Type'] = 'text/html;charset=euc-kr';
		$headers['Content-Type'] = 'text/html;charset=utf-8';
		$headers['Content-Transfer-Encoding'] = 'base64';

		foreach ($headers as $key => $value) {
			if (strcasecmp($key, 'From') === 0){
				$from = $value;
				if ($headers['Name']) $value = "<$value>";
                //if ($headers['Name']) $value = '"'.iconv("utf-8","euc-kr",$headers['Name']).'" <'.$value.'>';
                //if ($headers['Name']) $value = '"'.$headers['Name'].'" <'.$value.'>';
			} else if (strcasecmp($key, 'Subject') === 0) $value = $this->encode_2047($value);
			else if (strcasecmp($key, 'To') === 0) $this->recipients = $value;
			if (strcasecmp($key, 'Name') !== 0) $lines[] = $key . ': ' . $value;
		}
		return array($from, join($this->sep, $lines) . $this->sep);
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