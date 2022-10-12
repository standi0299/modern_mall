<?
/*
* @date : 20180803
* @author : chunter
* @brief : 관리자 메뉴 재구성을 위한 함수 생성.		필요없는 메뉴는 /a/_inc_service.php 선언.
* @desc : findMenuArrayKey(), setAdminMenuExclude()


* @date : 20180131
* @author : chunter
* @brief : 호스팅 결제 기능을 위해 결제창 이동 함수 제작. podmanage 사이트 호출
* @desc : getHostingAccountLocation()
*/
?>
<?php

//다국어 언어 변환을 위한 html 태그별 함수 			20151119			chunter
function __text($str, $tagname = "") {
   if ($tagname)
      return $tagname . "=\"" . _($str) . "\"";
   else
      return _($str);
}

function __alt($str) {
   return __text($str, "alt");
}

function __title($str) {
   return __text($str, "title");
}

function __msg($str) {
   return __text($str, "msg");
}

function __value($str) {
   return __text($str, "value");
}

function __label($str) {
   return __text($str, "label");
}

function __placeholder($str) {
   return __text($str, "placeholder");
}

function __java($str) {
   return "'" . _($str) . "'";
}

function __onclick_alert($str) {
   return "onclick=\"alert(" . __java($str) . ");\"";
}

function __onclick_confirm($str) {
   return "onclick=\"return confirm(" . __java($str) . ");\"";
}

function __onsubmit_confirm($str, $fun = "") {
   if ($fun)
   	  return "onsubmit=\"return " . $fun . "&&confirm(" . __java($str) . ");\"";
   else
   	  return "onsubmit=\"return confirm(" . __java($str) . ");\"";
}


//각종 로그 삭제 처리. 90일 이전 자료 삭제			20160729		chunter
function DeleteAllLog($logKeepDays = 90)
{
	$dir = dirname(__FILE__) ."/../dblog";	 
	$delete_files[] = getDeleteFileList($dir, $logKeepDays);
	
	$dir = dirname(__FILE__) ."/../pg/INIpay50/log";	 
	$delete_files[] = getDeleteFileList($dir, $logKeepDays);
	
	$dir = dirname(__FILE__) ."/../pg/lg/log";	 
	$delete_files[] = getDeleteFileList($dir, $logKeepDays);
	
	$dir = dirname(__FILE__) ."/../pg/kcp/log";	 
	$delete_files[] = getDeleteFileList($dir, $logKeepDays);
	
	$dir = dirname(__FILE__) ."/../pg/kcp_ilark/log";	 
	$delete_files[] = getDeleteFileList($dir, $logKeepDays);
	
	$dir = dirname(__FILE__) ."/../pg/smartXPay/log";	 
	$delete_files[] = getDeleteFileList($dir, $logKeepDays);
	
	$dir = dirname(__FILE__) ."/../pg/INIPayMobile/log";	 
	$delete_files[] = getDeleteFileList($dir, $logKeepDays);

	//파일 삭제 처리.
	foreach ($delete_files as $files) 
	{
		foreach ($files as $f) 
		{			
			echo $f."<BR>";
			//unlink($f);
		}
	}
}


//삭제 대상 파일 수집하기			20160729		chunter
function getDeleteFileList($directory, $logKeepDays = 90)
{
	$diff_date = date('Y-m-d');
	$diff_date = strtotime("-$logKeepDays day", strtotime($diff_date));
	
	$files = array();
	if (is_dir($directory))
	{
		if ($handle = opendir($directory)) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				$time=filemtime($directory.'/'.$file);
				
				if (is_file($directory.'/'.$file)) 
				{
					if ($time < $diff_date) 
					{
		      	$files[] = $directory.'/'.$file;
		     	}
		   	}
				else if ($file != "." && $file != ".." && is_dir($directory.'/'.$file)){
					$dir_files = getDeleteFileList($directory.'/'.$file, $logKeepDays);
					$files = array_merge($files, $dir_files);
				}
		 	}
		  closedir($handle);
		}
	}	
	return $files;	
}


// AES-256과 HMAC을 사용하여 문자열을 암호화하고 위변조를 방지하는 법.
// 비밀번호는 서버만 알고 있어야 한다. 절대 클라이언트에게 전송해서는 안된다.
// PHP 5.2 이상, mcrypt 모듈이 필요하다.
// 문자열을 암호화한다.

function aes_encrypt($plaintext)
{
    // 보안을 최대화하기 위해 비밀번호를 해싱한다.    
    $password = hash('sha256', AES_ENCRYPT_PASSWD, true);    
    // 용량 절감과 보안 향상을 위해 평문을 압축한다.    
    $plaintext = gzcompress($plaintext);    
    // 초기화 벡터를 생성한다.    
    $iv_source = defined(MCRYPT_DEV_URANDOM) ? MCRYPT_DEV_URANDOM : MCRYPT_RAND;
    $iv = mcrypt_create_iv(32, $iv_source);
    
    // 암호화한다.    
    $ciphertext = mcrypt_encrypt('rijndael-256', $password, $plaintext, 'cbc', $iv);
    
    // 위변조 방지를 위한 HMAC 코드를 생성한다. (encrypt-then-MAC)    
    $hmac = hash_hmac('sha256', $ciphertext, $password, true);
    
    // 암호문, 초기화 벡터, HMAC 코드를 합하여 반환한다.    
    return base64_encode($ciphertext . $iv . $hmac);
}

// 위의 함수로 암호화한 문자열을 복호화한다.
// 복호화 과정에서 오류가 발생하거나 위변조가 의심되는 경우 false를 반환한다.
function aes_decrypt($ciphertext)
{
    // 초기화 벡터와 HMAC 코드를 암호문에서 분리하고 각각의 길이를 체크한다.    
    $ciphertext = @base64_decode($ciphertext, true);
    if ($ciphertext === false) return false;
    $len = strlen($ciphertext);
    if ($len < 64) return false;
    $iv = substr($ciphertext, $len - 64, 32);
    $hmac = substr($ciphertext, $len - 32, 32);
    $ciphertext = substr($ciphertext, 0, $len - 64);
    
    // 암호화 함수와 같이 비밀번호를 해싱한다.    
    $password = hash('sha256', AES_ENCRYPT_PASSWD, true);
    
    // HMAC 코드를 사용하여 위변조 여부를 체크한다.    
    $hmac_check = hash_hmac('sha256', $ciphertext, $password, true);
    if ($hmac !== $hmac_check) return false;
    
    // 복호화한다.    
    $plaintext = @mcrypt_decrypt('rijndael-256', $password, $ciphertext, 'cbc', $iv);
    if ($plaintext === false) return false;
    
    // 압축을 해제하여 평문을 얻는다.    
    $plaintext = @gzuncompress($plaintext);
    if ($plaintext === false) return false;
    
    // 이상이 없는 경우 평문을 반환한다.    
    return $plaintext;
}


//유효시간을 정해 그 시간동안 유효한 파라미터 암호화를 만든다.			//20160531		chunter	
//$expire_date =>0 1년 시잔			
function makeEncrypData($param, $expire_hour = 0)
{
	$result = "";
	
	//만료일 시간을 더해서 expire date 생성한다.
	if (!$expire_hour)
		$expire_hour = "8760";			//1년셋팅
	$expire_date = strtotime("+$expire_hour hour", time());
	
	//암호화 테스트시 사용할것. 실제에서는 꼭 주석처리해야함.
	//$expire_date = "8760";
			
	if (is_array($param))
	{
		$param[expire_date] = $expire_date;
		foreach ($param as $key => $value) {
			$param_plan .= $key . "=" .$value. "&"; 	
		}
		$param_plan = substr($param_plan, 0, -1);		
	} else {
		$param_plan = $param . "&expire_date=".$expire_date;
	}
	
	
	$result = aes_encrypt($param_plan);
	return $result;	
}

//복화화 처리후 유효시간을 체크한다.			//20160531		chunter
function makeDecryptData($param, $return_array = false)
{
	$result = aes_decrypt($param);
	if ($result)
	{
		$result_temp = explode("&", $result);
			
		if (is_array($result_temp))
		{
			foreach ($result_temp as $key => $value) {
				$temp = explode("=", $value);
				$result_array[$temp[0]] = $temp[1]; 
			}
		}
		//expire_date 비교
		if ($result_array[expire_date])
		{
			//$expire_date = date("Y-m-d H:i:s", $result_array[expire_date]);
			$datediff = $result_array[expire_date] - time();
			if ($datediff < 0)
				$result = "";

			if ($return_array)
				$result = $result_array;
		}
		else 
		{
			$result = "";
		}
	}
	
	return $result;
}



//IE 체크 한다			20151116		여기 저기 흩어져 있던 소스를 한곳에 모았다.
function chkIEBrowser() {
	global $cfg;
  if ($_COOKIE[CLICK_ONE_NON_EXECUTE] == "Y") {
  	$browser = "notIE";
	
	//ActiveX 사용하지 않을 경우 무조건 EXE 로 실행하기.				20160509		chunter
	} else if($cfg[AX_editor_use] == "N") {
		$browser = "notIE";
	} else {
  	$chkBrowser = getBrowser();

    if ($chkBrowser[name] == "Internet Explorer")
    	$browser = "IE";
   	else
    	$browser = "notIE";
	}

  return $browser;
}

// option 리스트에 selected 추가			20160324			chunter
function conv_selected_option($options, $value)
{
  if(!$options)
 		return '';
  $options = str_replace('value="'.$value.'"', 'value="'.$value.'" selected', $options);
  return $options;
}

//관리자 카테고리 태그 select 옵션 string 만들기
function makeCategorySelectOptionTag($categoryArr) {
   $result = '';
	if (is_array($categoryArr)) {
		foreach ($categoryArr as $key => $value) {
         $len = strlen($value['catno']) / 2 - 1;
	  	   $nbsp = '';

         for ($i=0; $i<$len; $i++) {
            $nbsp .= '&nbsp;&nbsp;&nbsp;';
         }
         $result .= '<option value="'.$value['catno'].'">'.$nbsp.$value['catnm'].'</option>'.PHP_EOL;
      }
   }
	return $result;
}

//pdf 파일의 page 수 구하기      20150703    chunter
// return -1 -> error;
function getPageCountInPDF($PDFPath) {
   $stream = @fopen($PDFPath, "r");
   $PDFContent = @fread($stream, filesize($PDFPath));
   if (!$stream || !$PDFContent)
      return -1;
   //return false;
   $firstValue = 0;
   $secondValue = 0;
   if (preg_match("/\/N\s+([0-9]+)/", $PDFContent, $matches)) {
      $firstValue = $matches[1];
   }
   if (preg_match_all("/\/Count\s+([0-9]+)/s", $PDFContent, $matches)) {
      $secondValue = max($matches[1]);
   }
   return (($secondValue != 0) ? $secondValue : max($firstValue, $secondValue));
}

//블루포토(유치원 시즌2) 상품 가격 표시 / 15.07.17 / kjm
function pretty_price_display($originPrice) {
   global $sess;

   if ($sess[pretty_pricedisplay] == "Y")
      return number_format($originPrice);
   else {
      $priceLen = strlen($originPrice);

      $m_char_num = mb_substr($originPrice, 0, $priceLen, 'utf-8');
      $m_char = str_repeat('*', mb_strlen($m_char_num, 'utf-8'));

      return $m_char;
   }
}

//아이락에 세금계산서 발행 요청      20150422    chunter
function requestTaxPaperToIlark($request_money, $request_money_date, $request_money_kind_text = "") {
   global $cfg;

   include_once dirname(__FILE__) . "/class.mail.php";
   
   if (!$request_money_kind_text) $request_money_kind_text = _("유치원 포인트 충전");

   $mail = new Mail($param);
   $headers['From'] = $cfg[emailAdmin];
   $headers['Name'] = $cfg[nameSite];
   $headers['Subject'] = _("세금 계산서 발행 요청.");

   $contents =
      _("사업자 번호")." : $cfg[regnumBiz] <Br>".
      _("사업자명")." : $cfg[nameComp] <Br>".
      _("대표자")." : $cfg[nameCeo] <Br>".
      _("업태")." : $cfg[typeBiz] <Br>".
      _("종목")." : $cfg[itemBiz] <Br><Br>".
     
      _("품목")." : $request_money_kind_text <Br>".
      _("거래금액")." : $request_money "._("원")."<Br>".
      _("거래일자")." : $request_money_date <Br><Br>".

      _("연락처")." : $cfg[phoneComp] <Br>".
      _("접속 사이트")." : " .USER_HOST;

   //$headers['To']  = "tax@ilark.co.kr";
   $headers['To'] = DEFAULT_TAX_ILARK_EMAIL;
   //$mail -> send($headers, $contents);		//mailSendAsyncWithLogID() 로 대체			20180928	chunter

   $to[] = $data[email];

   $log_mail_no = emailLog(array("to" => $to, "subject" => $headers['Subject'], "contents" => $contents), count($to));
	 mailSendAsyncWithLogID($log_mail_no);
}

//유치원 서비스 연도를 구한다.      20150309    chunter
//return - array (서비스 연도)
function getKidsServiceYear() {
   global $cid, $cfg;

   if ($cfg[kids_service_year]) {
      $data_arr = $cfg[kids_service_year];
   } else {
      //값이 없으면 기본일인 4월1일로 계산한다. / 16.03.08 / kjm
      $data_arr = "04-01";
   }

   if ($data_arr) {
      $diff_month = date("m-d");

      if ($diff_month >= $data_arr) {
         //시즌 시작
         $result[] = date("Y");
      } else if ($diff_month < $data_arr) {
         //시즌 끝
         $result[] = date("Y") - 1;
      }

      //파일 삭제월까지 전년도 데이타 유지
      /*
      if ($diff_month >= $data_arr && $diff_month <= $data_arr) {
         if ($diff_month >= $data_arr[0])
            $append_year = date("Y") - 1;
         //종료월 이전까지 년도 -2 (1,2,3,4월)
         else if ($diff_month <= $data_arr[1]) {
            $append_year = date("Y") - 2;
         } else {
            $append_year = date("Y") - 1;
         }

         if (!in_array($append_year, $result))
            $result[] = $append_year;
      }
      */
   }
   
   //시즌 날짜를 2017로 통일 후 현재 사용하는 데이터는 업데이트 or 삭제시킨다 / 16.07.13 / kjm
   $result[0] = "2017";
   return $result;
}

//예치금 충전시 가산 적립금 % 구하기      20141223  chunter
function getDepositSetPercent($DepositSet, $accountPrice) {
   $result = 0;
   $data = unserialize($DepositSet);
   foreach ($data as $key => $value) {
      //설정금액보다 결제금액이 큰경우를 찾아라.
      if ($value[0] <= $accountPrice) {
         //가장 높은 %를 구한다.
         if (floatval($value[1]) > $result)
            $result = $value[1];
      }
   }

   return $result;
}

function chkBrooksMemberGrade($gradeTable, $totalBuyPrice, $diffKind) {
   $maxBid = 0;
   foreach ($gradeTable as $key => $value) {
      if ($diffKind == "12") {
         if ($gradeTable[buytotal_12] < $totalBuyPrice) {
            if ($gradeTable[bid] > $maxBid)
               $maxBid = $gradeTable[bid];
         }
      } else if ($diffKind == "3") {
         if ($gradeTable[buytotal_3] < $totalBuyPrice) {
            if ($gradeTable[bid] > $maxBid)
               $maxBid = $gradeTable[bid];
         }
      }
   }

   return $maxBid;
}

//년 선택 상자 만들기 - 현재 년도를 기준으로 - ~년 / 15.05.06 / kjm
function makeYearSelectTag($currentYear, $yearRange, $tagName, $selectValue, $bAllFlag = false) {
   $tags = "";

   $startYear = ($currentYear - $yearRange);
   $endYear = $currentYear;

   $tags = "<select name=\"" . $tagName . "\">";
   if ($bAllFlag)
      $tags .= "<option value=''></option>";
   for ($i = $startYear; $i <= $endYear; $i++) {
      $tags .= "<option value=\"" . $i . "\"";
      if ($selectValue == $i)
         $tags .= " selected";

      $tags .= ">" . $i . "</option>";
   }
   $tags .= "</select>";

   return $tags;
}

//월 선택 상자 만들기     20141216    chunter
function makeMonthSelectTag($tagName, $selectValue, $bAllFlag = false) {
   $tags = "";

   $tags = "<select name=\"" . $tagName . "\">";
   if ($bAllFlag)
      $tags .= "<option value=''></option>";
   for ($i = 1; $i < 13; $i++) {
      $tags .= "<option value=\"" . $i . "\"";
      if ($selectValue == $i)
         $tags .= " selected";

      $tags .= ">" . $i . "</option>";
   }
   $tags .= "</select>";

   return $tags;
}

function makeDaySelectTag($tagName, $selectValue, $bAllFlag = false) {
   $tags = "";

   $tags = "<select name=\"" . $tagName . "\">";
   if ($bAllFlag)
      $tags .= "<option value=''></option>";
   for ($i = 1; $i < 32; $i++) {
      $tags .= "<option value=\"" . $i . "\"";
      if ($selectValue == $i)
         $tags .= " selected";

      $tags .= ">" . $i . "</option>";
   }
   $tags .= "</select>";

   return $tags;
}

//child_enroll.htm의 필드 추가용 함수 시작 // 15.05.28 / kjm

//년 선택 상자 만들기 - 현재 년도를 기준으로 - ~년 / 15.05.06 / kjm
function makeYearSelectTag_script($currentYear, $yearRange, $tagName, $bAllFlag) {
   $tags = "";

   $startYear = ($currentYear - $yearRange);
   $endYear = $currentYear;

   $tags = "<select name=\\\"year[]" . $tagName . "\\\">";
   if ($bAllFlag)
      $tags .= "<option value=''></option>";
   for ($i = $startYear; $i <= $endYear; $i++) {
      $tags .= "<option value=\\\"" . $i . "\\\"";
      if ($selectValue == $i)
         $tags .= " selected";

      $tags .= ">" . $i . "</option>";
   }
   $tags .= "</select>";

   return $tags;
}

//월 선택 상자 만들기     20141216    chunter
function makeMonthSelectTag_script($tagName, $selectValue, $bAllFlag) {
   $tags = "";

   $tags = "<select name=\\\"month[]" . $tagName . "\\\">";
   if ($bAllFlag)
      $tags .= "<option value=''></option>";
   for ($i = 1; $i < 13; $i++) {
      $tags .= "<option value=\\\"" . $i . "\\\"";
      if ($selectValue == $i)
         $tags .= " selected";

      $tags .= ">" . $i . "</option>";
   }
   $tags .= "</select>";

   return $tags;
}

function makeDaySelectTag_script($tagName, $selectValue, $bAllFlag) {
   $tags = "";

   $tags = "<select name=\\\"day[]" . $tagName . "\\\">";
   if ($bAllFlag)
      $tags .= "<option value=''></option>";
   for ($i = 1; $i < 32; $i++) {
      $tags .= "<option value=\\\"" . $i . "\\\"";
      if ($selectValue == $i)
         $tags .= " selected";

      $tags .= ">" . $i . "</option>";
   }
   $tags .= "</select>";

   return $tags;
}

//child_enroll.htm의 필드 추가용 함수 끝 // 15.05.28 / kjm

//배송 방법 선택 태그 만들기     20140922    chunter
function makeShiptypeRadioTag($tagName, $selectValue, $excludeShipType, $addTag = "") {
   global $r_shiptype;

   $tags = "";

   foreach ($r_shiptype as $key => $value) {
      if ($key == $excludeShipType)
         continue;
      if ($key == "4" || $key == "8" || $key == "9")
         continue;
      //방문수령 제외.
      //착불, 퀵서비스 제외 / 16.04.08 / kjm
      $tags .= "<input type=\"radio\" class=\"radio-inline\" name=\"" . $tagName . "\" value=\"" . $key . "\"" . $selectValue[$key] . " $addTag >" . $value;
   }

   return $tags;
}

//라디오 선택 태그 만들기     20141203    chunter
function makeRadioTag($radioArray, $tagName, $selectValue, $excludeShipType, $addTag = "") {
   $tags = "";

   foreach ($radioArray as $key => $value) {
      if ($key == $excludeShipType)
         continue;
      if ($key == "9")
         continue;
      //방문수령 제외.
      $tags .= "<input type=\"radio\" name=\"" . $tagName . "\" value=\"" . $key . "\"" . $selectValue[$key] . " $addTag >" . $value;
   }

   return $tags;
}

function get_request($param_name, $opt = "") {
   //debug($_POST[start]);
   global $_GET, $_POST;

   $return_value = "";
   $opt = strtoupper($opt);

   if ($opt == "G") {
      // ONLY GET
      $return_value = $_GET[$param_name];

   } else if ($opt == "P") {
      // ONLY POST
      $return_value = $_POST[$param_name];

   } else {
      // GET, POST
      if ($_GET[$param_name]) {
         $return_value = $_GET[$param_name];
      } else {
         $return_value = $_POST[$param_name];
      }
   }

   return $return_value;
}

function sendPostData($url, $postargs, $userAgent = "") {
   try {
      $ch = curl_init($url);
      //curl_setopt($ch, CURLOPT_URL, $url);
      //curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");

      if ($userAgent)
         curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

      curl_setopt($ch, CURLOPT_ENCODING, 'utf-8');
      //curl_setopt($ch,CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postargs);
      $rst_exc = curl_exec($ch);
      curl_close($ch);

      //=================로그 남기는 로직 추가 필요

      return strip_tags($rst_exc);
   } catch(Exception $ex) {
      //=================로그 남기는 로직 추가 필요

      return "fail|" . $ex -> getMessage();
   }
}

function sendQueryData($url, $enc = 'utf-8', $port='') {
   try {
      $ch = curl_init($url);
      //curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
      
      if ($port)
				curl_setopt($ch, CURLOPT_PORT, $port);
      curl_setopt($ch, CURLOPT_ENCODING, $enc);
      //curl_setopt($ch,CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $rst_exc = curl_exec($ch);
      curl_close($ch);

      //=================로그 남기는 로직 추가 필요

      return strip_tags($rst_exc);
   } catch(Exception $ex) {
      //=================로그 남기는 로직 추가 필요

      return "fail|" . $ex -> getMessage();
   }
}


// CURL 비동기식 호출.			20180927		chunter
function curl_post_async($url, $params, $type='POST')  
{
	foreach ($params as $key => &$val)  
	{  
  	if (is_array($val))  
    	$val = implode(',', $val);  
 		$post_params[] = $key.'='.urlencode($val);  
	}  
  
  $post_string = implode('&', $post_params);
	$parts=parse_url($url);  
  
  if ($parts['scheme'] == 'http')  
  {  
  	$fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);  
 	}  
  else if ($parts['scheme'] == 'https')  
  {  
  	$fp = fsockopen("ssl://" . $parts['host'], isset($parts['port'])?$parts['port']:443, $errno, $errstr, 30);  
	}  
  
  // Data goes in the path for a GET request  
  if('GET' == $type)  
  	$parts['path'] .= '?'.$post_string;  
  
	$out = "$type ".$parts['path']." HTTP/1.1\r\n";  
  $out.= "Host: ".$parts['host']."\r\n";  
  $out.= "Content-Type: application/x-www-form-urlencoded\r\n";  
  $out.= "Content-Length: ".strlen($post_string)."\r\n";  
  $out.= "Connection: Close\r\n\r\n";  
  // Data goes in the request body for a POST request  
  if ('POST' == $type && isset($post_string))  
  	$out.= $post_string;  
  
	fwrite($fp, $out);  
	fclose($fp);
}  


//########### 부가세 포함 가격 계산 ##########

//부가세포함 판매가 리턴
//$price_calcu_type : 반올림(R),버림(F)
//$price_calcu_type_num : 자리수 (문자열을 수를 자리수로 처리-> 1:1단자리, 10: 10단위, 99:10단위, 123:100단위)
//$price_calcu_vat_flag : 부가세포함 전 금액절삭(N), 부가세포함 후 금액절삭(Y)
function getTotalPriceNTax($price_obj, $price_calcu_type, $price_calcu_type_num, $price_calcu_vat_flag) {
   //echo $price_obj.','.$price_calcu_type.','.$price_calcu_type_num.','.$price_calcu_vat_flag;
   $price = intval($price_obj);

   //부가세 포함 전 반올림/올림 처리
   if ($price_calcu_vat_flag == 'N')
      $price = getCuttingPrice($price, $price_calcu_type, $price_calcu_type_num);

   $addprice = intval($price / 10);
   $price = $price + $addprice;

   //부가세 포함 후 반올림/올림처리
   if ($price_calcu_vat_flag != 'N')
      $price = getCuttingPrice($price, $price_calcu_type, $price_calcu_type_num);

   return $price;
}

function getCuttingPrice($price, $price_calcu_type, $price_calcu_type_num) {
   //20130904반올림,버림,절삭위치 적용
   
	$deep = strlen($price_calcu_type_num);
  $n = 1;
  $m = 1;
  for ($i = 0; $i < $deep; $i++) {
  	$n *= 0.1;
    $m *= 10;
  }
			
   if ($price_calcu_type == "R")//반올림
   {
      $price = round($price * $n) * $m;
   } elseif ($price_calcu_type == "F")//버림
   {      
      $price = floor($price * $n) * $m;
   }
	 elseif ($price_calcu_type == "C")//올림
   {      
      $price = ceil($price * $n) * $m;
   }
   return $price;
}

//########### 부가세 포함 가격 계산 끝 ##########

function getVatTax($total_price, $price_calcu_type)
{
	
	$result = $total_price / 11;	
	if ($price_calcu_type == "R")//반올림
		$result = round($result);
 	elseif ($price_calcu_type == "F")//버림
		$result = floor($result);
	elseif ($price_calcu_type == "C")//올림
  	$result = ceil($result); 
	
	return $result;
}

function startsWith($haystack, $needle) {
   return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle) {
   $length = strlen($needle);
   if ($length == 0) {
      return true;
   }

   return (substr($haystack, -$length) === $needle);
}

function dateDiff($date1, $date2, $diff_unit = "d", $minus_flag = false) {
   $_date1 = explode("-", $date1);
   $_date2 = explode("-", $date2);

   $tmp1 = mktime(0, 0, 0, $_date1[1], $_date1[2], $_date1[0]);
   $tmp2 = mktime(0, 0, 0, $_date2[1], $_date2[2], $_date2[0]);

   if ($tmp1 >= $tmp2)
      $interval = $tmp1 - $tmp2;
	 else {
		 if ($minus_flag)
		 	$interval = $tmp1 - $tmp2;
	 }

   if ($diff_unit == "d")
      return ($interval) / 86400;
   else if ($diff_unit == "m")
      return date("m", $interval);
	 else if ($diff_unit == "s")
      return date("s", $interval);
}


function dateTimeDiff($date1, $date2, $diff_unit = "d", $minus_flag = false) {
   $_date1 = explode(" ", $date1);
   $_date2 = explode(" ", $date2);	 
	 $_date1Arr = explode("-", $_date1[0]);
   $_date2Arr = explode("-", $_date2[0]);	 
	 $_time1Arr = explode(":", $_date1[1]);
   $_time2Arr = explode(":", $_date2[1]);
 
   $tmp1 = mktime($_time1Arr[0],$_time1Arr[1], $_time1Arr[2], $_date1Arr[1], $_date1Arr[2], $_date1Arr[0]);
   $tmp2 = mktime($_time2Arr[0],$_time2Arr[1], $_time2Arr[2], $_date2Arr[1], $_date2Arr[2], $_date2Arr[0]);
	 
   if ($tmp1 >= $tmp2)
      $interval = $tmp1 - $tmp2;
	 else {
		 if ($minus_flag)
		 	$interval = $tmp1 - $tmp2;
	 }

   if ($diff_unit == "d")
      return ($interval) / 86400;
   else if ($diff_unit == "m")
      return date("m", $interval);     
	 else if ($diff_unit == "i")
      return date("i", $interval);
	 else
	 		return $interval;
}

### bpm main 각 화면에서 출고완료로 넘어갈 때 날짜 출력형식
function toDate_main($date, $div = "-") {
   if (!$date)
      return "";
   $s = ($div == "kr") ? array(_("년") . ' ', _("월") . ' ', _("일")) : array($div, $div);
   $ret = sprintf("%04d{$s[0]}%02d{$s[1]}%02d{$s[2]}", substr($date, 0, 4), substr($date, 5, 2), substr($date, 8, 2));
   if (strlen($date) == 6)
      $ret = substr($ret, 0, 7);
   return $ret;
}

function get_time() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function debug_time($this_time, $postFix = '', $bLast = false) {
   global $check_index;
   if (!$check_index)
      $check_index = 1;
   else
      $check_index++;

   $check_data .= "$check_index [$postFix] - " . number_format(get_time() - $this_time, 4) . _("초") . "\n";

   if ($bLast)
      $check_index = 0;
   return $check_data;
}

function get_domain($domain, $debug = false) {
   $domain = str_replace('www.', '', $domain);
   //www 제거
   $original = $domain = strtolower($domain);

   if (filter_var($domain, FILTER_VALIDATE_IP)) {
      return $domain;
   }

   $tlds = array('com', 'co.kr', 'biz', 'edu', 'gov', 'info', 'jobs', 'mil', 'mobi', 'museum', 'name', 'net', 'org', 'kr');
   foreach ($tlds as $key => $value) {
      $domain = str_replace('.' . $value, '', $domain);
   }

   $debug ? print('<strong style="color:green">&raquo;</strong> Parsing: ' . $original) : false;
   $arr = explode('.', $domain);

   if (count($arr) > 1)
      return str_replace($arr[0] . '.', '', $original);
   else
      return $original;
}

function makeStorageCode() {
   $micro = explode(" ", microtime());
   $microsecond = substr($micro[0], 2, 4);
   $result = date("Ymd-His", $micro[1]) . "-" . $microsecond;
   $result .= '-' . sprintf('%03d', mt_rand(0, 999));
   // sprintf()로 3자리 만들기
   return $result;
}

//정산코드 생성       20141216    chunter
function makeAccountCode() {
   $micro = explode(" ", microtime());
   $microsecond = substr($micro[0], 2, 4);
   $result = date("Ymd-His", $micro[1]) . "-" . $microsecond;
   //$result .= '-'.sprintf('%03d',mt_rand(0,999)); // sprintf()로 3자리 만들기
   return $result;
}

function getFileNames($directory) {
   $results = array();
   $handler = opendir($directory);
   while ($file = readdir($handler)) {
      if ($file != '.' && $file != '..' && is_dir($file) != '1') {
         $results[] = $file;
      }
   }
   closedir($handler);
   return $results;
}

function getDaumLngLat($api_key, $address) {

   $url = "http://apis.daum.net/local/geo/addr2coord?apikey=$api_key&q=" . urlencode($address) . "&output=json";

   $resultJsn = sendQueryData($url);
   $daumMap = json_decode($resultJsn, 1);
   //debug($daumMap);

   if ($daumMap[channel][totalCount] > 0) {
      foreach ($daumMap[channel][item] as $key => $value) {
         $result[lng] = $value[lng];
         $result[lat] = $value[lat];
         break;
      }
   }

   return $result;
}

function MoveAllFiles($sourceDir, $targetDir) {
   if ($handle = opendir($sourceDir)) {
      while (false !== ($fileName = readdir($handle))) {
         if ($fileName != '.' && $fileName != '..') {
            if (!is_dir($sourceDir . $fileName)) {
               $newName = $targetDir . $fileName;
               //debug($sourceDir.$fileName);
               //debug($newName);
               rename($sourceDir . $fileName, $newName);
            }
         }
      }
   }
   closedir($handle);
}

//웹하드 계정 암호화 소스 코드
function GenerationKey($length) {
   $data = "abcdefghijklmnopqrstuvwxyz0123456789";

   mt_srand(doubleval(microtime()) * 100000000);

   for ($i = 0; $i < $length; $i++) {
      $n = mt_rand(1, 1000) % 36;
      $key .= $data[$n];
   }

   return $key;
}

function Encrypt($SourceData, $Key) {
   $EncryptData = "";

   $count = 0;
   $length = strlen($SourceData);

   for ($i = 0; $i < $length; $i++) {
      if ($count == $length) {
         $count = 0;
      }

      $EncryptData .= substr($SourceData, $i, 1) ^ substr($Key, $count, 1);
      $count++;
   }
   return base64_encode($EncryptData);
}

function EncryptKey($SourceData) {
   $Key = "20ject02worl200pro30401dcupnetiddacomwebhard";
   $EncryptData = "";

   $count = 0;
   $length = strlen($SourceData);

   for ($i = 0; $i < $length; $i++) {
      if ($count == $length) {
         $count = 0;
      }

      $EncryptData .= substr($SourceData, $i, 1) ^ substr($Key, $count, 1);
      $count++;
   }
   return base64_encode($EncryptData);
}

//--------------------------------------------------------------------------------
// 욕 기타 광고글 필터링 .. 개X끼 육X랄      20150107  chunter
//--------------------------------------------------------------------------------
function bad_check($badword, $str, $divide = ",") {
   $badword = preg_replace('/' . $divide . '/', '|', $badword);
   if (preg_match('/' . $badword . '/', $str, $match)) {
      return $match[0];
   }
   return 0;
}


//모바일 결제가 가능한지 체크. 모바일 환경 + 적용 가능 스킨만			20160930		chunter
function allowMobilePGCheck()
{
	global $cfg, $r_mobile_PG_allow_skin;
	
	if (isMobile() && in_array($cfg[skin], $r_mobile_PG_allow_skin))
		return true;
	else 
		return false;	
}

//모바일 브루아져인치 체크   20150116  chunter
function isMobile() {
   $arr_browser = array("iphone", "android", "ipod", "iemobile", "mobile", "lgtelecom", "ppc", "symbianos", "blackberry", "ipad");
   $arr_browser = array("Android", "iphone", "ipod", "iemobile", "mobile", "lgtelecom", "ppc", "symbianos", "blackberry", "ipad", 'Windows CE', 'LG', 'MOT', 'SAMSUNG', 'SonyEricsson');
   $httpUserAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

   // 기본값으로 모바일 브라우저가 아닌것으로 간주함
   $mobile_browser = false;
   // 모바일브라우저에 해당하는 문자열이 있는 경우 $mobile_browser 를 true로 설정
   for ($indexi = 0; $indexi < count($arr_browser); $indexi++) {
      if (strpos($httpUserAgent, $arr_browser[$indexi]) == true) {
         $mobile_browser = true;
         break;
      }
   }

   return $mobile_browser;
}

//paging NAV 생성     20150206    chunter
//$url : 페이지링크 URL, $param : 추가파라미터, $page : 현재페이지, $blockPage : 표시 페이지수, $totalPage:총페이지수, $prev_img_tag:이전 페이지 이동 태그, $next_img_tag : 다음 페이지 이동 태그
function blockpaging($url, $param, $page, $blockPage, $totalPage, $prev_img_tag, $next_img_tag) {

   $intTemp = intval(($page - 1) / $blockPage) * $blockPage + 1;

   if ($intTemp == 1)
      $buffer = $prev_img_tag . "&nbsp;&nbsp;";
   else
      $buffer = "[<a href='" . $url . "?page=" . intval($intTemp - $blockPage) . "&" . $param . "'>" . $prev_img_tag . "</a>] ";

   $intLoop = 0;

   for ($i = 0; $i < $blockPage; $i++) {
      if ($intTemp == intval($page))
         $buffer = $buffer . "<strong>" . $intTemp . "</strong> ";
      else
         $buffer = $buffer . "[<a href='" . $url . "?page=" . $intTemp . "&" . $param . "'>" . $intTemp . "</a>] ";

      $intTemp = $intTemp + 1;
      $intLoop = $intLoop + 1;

      if (($intLoop > $blockPage) OR ($intTemp > $totalPage)) {
         break;
      }
   }

   If ($intTemp > $totalPage)
      $buffer = $buffer . $next_img_tag;
   else
      $buffer = $buffer . "[<a href='" . $url . "?page=" . $intTemp . "&" . $param . "'>" . $next_img_tag . "</a>]";

   echo($buffer);
}

function byteFormat($bytes, $unit = "", $decimals = 2, $bUnitAppend = true) {
   $units = array('B' => 0, 'K' => 1, 'M' => 2, 'G' => 3, 'T' => 4, 'P' => 5, 'E' => 6, 'Z' => 7, 'Y' => 8);

   $value = 0;
   if ($bytes > 0) {
      // Generate automatic prefix by bytes
      // If wrong prefix given
      if (!array_key_exists($unit, $units)) {
         $pow = floor(log($bytes) / log(1024));
         $unit = array_search($pow, $units);
      }

      // Calculate byte value by prefix
      $value = ($bytes / pow(1024, floor($units[$unit])));
   }
   // If decimals is not numeric or decimals is less than 0
   // then set default value
   if (!is_numeric($decimals) || $decimals < 0) {
      $decimals = 2;
   }
   // Format output
   if ($bUnitAppend)
      return sprintf('%.' . $decimals . 'f ' . $unit, $value);
   else
      return sprintf('%.' . $decimals . 'f ', $value);
}

//1000에서 970으로 바꿈 / 17.01.18 / kjm
function byteFormat_1000($bytes, $unit = "", $decimals = 2, $bUnitAppend = true) {
   $units = array('B' => 0, 'K' => 1, 'M' => 2, 'G' => 3, 'T' => 4, 'P' => 5, 'E' => 6, 'Z' => 7, 'Y' => 8);

   $value = 0;
   if ($bytes > 0) {
      // Generate automatic prefix by bytes
      // If wrong prefix given
      if (!array_key_exists($unit, $units)) {
         $pow = floor(log($bytes) / log(970));
         $unit = array_search($pow, $units);
      }

      // Calculate byte value by prefix
      $value = $bytes;
      for($i = 0; $i < floor($units[$unit]) ; $i++){
         if($i < 4) $per = 970;
         else $per = 1000;

         $value = ($value / $per);
      }
      //$value = ($bytes / pow(970, floor($units[$unit])));
   }
   // If decimals is not numeric or decimals is less than 0
   // then set default value
   if (!is_numeric($decimals) || $decimals < 0) {
      $decimals = 2;
   }
   // Format output
   if ($bUnitAppend)
      return sprintf('%.' . $decimals . 'f ' . $unit, $value);
   else
      return sprintf('%.' . $decimals . 'f ', $value);
}

function TerabyteToByte($tera_size) {
   return $tera_size * 1099511627776;
}

//견적 내용 처리. 2015.07.02 by kdk
function formatEstOrderOptionDesc($str) {

   $desc = explode('<br>', $str);
   //항목 분리
   //debug($desc);

   foreach ($desc as $key => $val) {
      if ($val) {
         $val = str_replace("[", "", $val);
         $val = str_replace("]", "", $val);
         $val = str_replace("|", "/", $val);

         $optArr = explode('::', $val);

         if ($optArr[1]) {
            $optSubArr = explode(',', $optArr[1]);
         }

         if ($optArr[0] == _("추가내지")) {
            $data[_("추가내지")][] = $optSubArr;
         } else {
            $data[$optArr[0]] = $optSubArr;
         }
      }
   }

   return $data;
}

//브라우저 체크 / 15.09.21 / kjm
function getBrowser() {
   $u_agent = $_SERVER['HTTP_USER_AGENT'];
   $bname = 'Unknown';
   $platform = 'Unknown';
   $version = "";

   //First get the platform?
   if (preg_match('/linux/i', $u_agent)) {
      $platform = 'linux';
   } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
      $platform = 'mac';
   } elseif (preg_match('/windows|win32/i', $u_agent)) {
      $platform = 'windows';
   }
   //debug(preg_match('/MSIE/i', $u_agent));

   // Next get the name of the useragent yes seperately and for good reason
   //IE8 미만 버전 체크때문에 trident || msie로 변경 / 15.10.26 / kjm
   if (preg_match('/Trident/i', $u_agent) || preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
      $bname = 'Internet Explorer';
      $ub = "Trident";
   } elseif (preg_match('/Firefox/i', $u_agent)) {
      $bname = 'Mozilla Firefox';
      $ub = "Firefox";
   } elseif (preg_match('/Chrome/i', $u_agent)) {
      $bname = 'Google Chrome';
      $ub = "Chrome";
   } elseif (preg_match('/Safari/i', $u_agent)) {
      $bname = 'Apple Safari';
      $ub = "Safari";
   } elseif (preg_match('/Opera/i', $u_agent)) {
      $bname = 'Opera';
      $ub = "Opera";
   } elseif (preg_match('/Netscape/i', $u_agent)) {
      $bname = 'Netscape';
      $ub = "Netscape";
   }
   //debug($bname);
   // finally get the correct version number
   $known = array('Version', $ub, 'other');
   $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
   if (!preg_match_all($pattern, $u_agent, $matches)) {
      // we have no matching number just continue
   }

   // see how many we have
   $i = count($matches['browser']);
   if ($i != 1) {
      //we will have two since we are not using 'other' argument yet
      //see if version is before or after the name
      if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
         $version = $matches['version'][0];
      } else {
         $version = $matches['version'][1];
      }
   } else {
      $version = $matches['version'][0];
   }

   // check if we have a number
   if ($version == null || $version == "") {
      $version = "?";
   }
   return array('userAgent' => $u_agent, 'name' => $bname, 'version' => $version, 'platform' => $platform, 'pattern' => $pattern);
}

function readUrlWithcurlPost($returl, $param, $bConvertUtf8 = false) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $returl);
   // 접속할 URL 주소
   curl_setopt($ch, CURLOPT_HEADER, 0);
   // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
   curl_setopt($ch, CURLOPT_TIMEOUT, 30);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   $ret = curl_exec($ch);

   if ($bConvertUtf8)
      $ret = iconv("EUC-KR", "UTF-8", $ret);

   return $ret;
}

/**
 * Fixes the odd indexing of multiple file uploads from the format:
 * $_FILES['field']['key']['index']
 * To the more standard and appropriate:
 * 2015.10.29 kdk
 */
function fixFilesArray(&$files) {
   $names = array('name' => 1, 'type' => 1, 'tmp_name' => 1, 'error' => 1, 'size' => 1);

   foreach ($files as $key => $part) {
      // only deal with valid keys and multiple files
      $key = (string)$key;
      if (isset($names[$key]) && is_array($part)) {
         foreach ($part as $position => $value) {
            $files[$position][$key] = $value;
         }
         // remove old key reference
         unset($files[$key]);
      }
   }
}



//K 관리자 왼쪽 서브메뉴 구성 함수			20160614		chunter
function makeKadminLeftSubMenu($menuArray, $hasChild)
{
	global $service_menu, $active_page;
	$result = "<ul class='sub-menu'>";	
	foreach ($menuArray as $key => $value) 
	{
		$active_tag = "";
		if (is_array($value))
		{
			if (count($value) < 2) continue;			//2차 메뉴의 서브가 없을경우 다음 메뉴로 넘어간다.			20180803		chunter
			$hasChildClass = "has-sub";
			
			if ($service_menu[$value[0]])
				$leftMenu = $service_menu[$value[0]];
			else {
				//$service_menu 설정에 없을 경우 메뉴명설정. icon 은 기본 아이콘으로 처리.
				$leftMenu[display] = $value[0];
				$leftMenu[icon] = "class='fa fa-cogs'";
			}
									
		} else {
			if ($key == 0) continue;
			$hasChildClass = "";
			$leftMenu = $service_menu[$value];
		}	
		
		//debug($leftMenu[active]);
		//exit;
		if (is_array($leftMenu[active]))
		{									
			if (in_array($active_page, $leftMenu[active]))
			{
      	if ($leftMenu[QUERY]) 
      	{
      		//debug($_SERVER["QUERY_STRING"]);
					//debug($leftMenu[QUERY]);
					$QUERY_STRING_ARR = explode("&", $_SERVER["QUERY_STRING"]);					
					if (count($QUERY_STRING_ARR) > count($leftMenu[QUERY]))
					{
						foreach ($QUERY_STRING_ARR as $Qvalue) {
							if (in_array($Qvalue, $leftMenu[QUERY]))
							{ 
	          		$active_tag = "active";
								break;
							}
						}
						
					} else {
	          if (in_array($_SERVER["QUERY_STRING"], $leftMenu[QUERY])) 
	          	$active_tag = "active";
					}
      	} else 
      		$active_tag = "active";
      	if ($leftMenu[target]) $link_target = " target='$leftMenu[target]'";
			}
		}
		

		if ($hasChildClass == "has-sub") 
		{
			$result .= "<li class='$hasChildClass  parent_active_tag'>\r\n";
			$result .= "<a href=\"javascript:;\">";
			$result .= "<b class='caret pull-right'></b>";
		} else {
			$result .= "<li class='$hasChildClass $active_tag'>\r\n";
			$result .= "<a href=\"$leftMenu[link]\" $link_target>";
		}
		$result .= "<span name=\"$leftMenu[display]\">$leftMenu[display]</span></a>\r\n";
		if ($hasChildClass == "has-sub")
		{						
			$subNodeTag = makeKadminLeftSubMenu($value, true);
			if (strpos($subNodeTag, "active") !== FALSE)
				//exit;
				$result = str_replace("parent_active_tag", "active", $result);
												
			$result .= $subNodeTag;
		}
		$result = str_replace("parent_active_tag", "", $result);
		
		$result .= "</li>\r\n";
	}
	
	$result .= "</ul>\r\n";
	return $result;
}


//센터별 메뉴 구성.				20180802		chunter
function setAdminMenuExclude($menuData)
{
	global $admin_config;
	
	if (is_array($menuData))
	{
		foreach ($menuData as $key => $value) 
		{
			foreach ($value as $mkey => $mvalue) {
				//echo $mvalue;
				if (findMenuArrayKey($mvalue, 1) > -1)
					$Mkey[0] = findMenuArrayKey($mvalue, 1);			
				
				if (is_array($mvalue))
				{
					foreach ($mvalue as $subkey => $subvalue) {
						if ($subkey == 0)
						{
							if (findMenuArrayKey($subvalue, 2) > -1)
								$Mkey[1] = findMenuArrayKey($subvalue, 2);
			
						} else {
							if (findMenuArrayKey($subvalue, 3) > -1)
							{
								$Mkey[2] = findMenuArrayKey($subvalue, 3);
								
								unset($admin_config[allow_left_menu][$Mkey[0]][$Mkey[1]][$Mkey[2]]);
							}
						}
					}		
				}
			}
		}
	}
}


//센터별 메뉴 구성을 위해 기존 메뉴에 존재여부를 체크한다.				20180802		chunter
function findMenuArrayKey($find, $step)
{
	global $admin_config;
	$result = -1;
	if (is_array($find))	return $result;
	
	foreach ($admin_config[allow_left_menu] as $mkey => $mvalue) {
		foreach ($mvalue as $subkey => $subvalue) {
			
			if ($step == 1 && $find === $subvalue)
			{
				return $mkey;
			}
			
			if (is_array($subvalue))
			{
				foreach ($subvalue as $subsubkey => $subsubvalue) {
					if ($step == 2 && $subsubvalue === $find && $subsubkey == 0)
						return $subkey;
					
					if ($step == 3 && $subsubvalue === $find)
						return $subsubkey;
				}
			}			
		}		
	}
}

function getHostingAccountLocation($alertMsg = '')
{
	global $cfg_center, $cid;
	$postData = array("center_cid" => $cfg_center['center_cid'], "mall_cid" => $cid, "mode" => "make_access_code", "mall_name" => "", "mall_domain" => "");
	$podmanageUrl = "http://podmanage.bluepod.kr/_account/get_account_access_code.php";			
	$podmanageAccessCode = json_decode(sendPostData($podmanageUrl, $postData), 1);
						
  $podmanageUrl = "http://podmanage.bluepod.kr/kcp_ilark/order.php?podmanage_access_code=".$podmanageAccessCode['podmanage_access_code']."&account_type=H";
	//openPopup($podmanageUrl, 600, 600, $alertMsg);
	if ($alertMsg)		
		msg($alertMsg, $podmanageUrl);
	else 
		go($podmanageUrl);
}


//js, css 파일의 버젼을 포함한 url 을 만든다.			20180620		chunter
function makeUrlWithVersion( $url ) {
	// URL이 비어있으면 리턴
  if(empty($url)) return $url;
 
  // URL 베이스경로를 가져온다.
  $base_URL = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
  $base_URL .= ($_SERVER['SERVER_PORT'] != '80') ? $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'] : $_SERVER['HTTP_HOST'];
 
  // 해당 URL이 베이스경로를 포함하고 있거나, 처음 시작이 / 이면서 // 이 아닐때 실행한다.
  if( strpos($url, $base_URL) !== FALSE || ( substr($url,0,1) == '/' && substr($url,1,1) != '/' ))
  {
		
  	$absolute_url = $_SERVER['DOCUMENT_ROOT'] . str_replace($base_URL, "", $url);
		$url .= "?ver=".filemtime( $absolute_url );
		
		// 해당 파일이 실제로 존재할때만.  처리속도 문제가 있을것 같아 일단 주석처리			20180620
    //if( file_exist($_SERVER['DOCUMENT_ROOT'] . str_replace($base_URL, "", $url)) )
    //{
    //	$url .= "?ver=".filemtime( $absolute_url ); 
   	//}
 	}    
  return $url;
}

//인터프로 견적.
function optionDescStr($desc)
{
    $result = "";

    if ($desc)
    {
        $option_desc = explode(";]", $desc);
        $option_desc = str_replace("[", "", $option_desc);
        $option_desc = str_replace(";", ",", $option_desc);
        $option_desc = str_replace("]", "", $option_desc);
        //debug($option_desc);

        foreach ($option_desc as $key => $val) {

            if ($val) {
                //debug($key);
                //debug($val);

                if (strpos($val, "제목:") !== false) {
                    //$optionArr['title'] = str_replace("제목:", "", $val);
                }
                else if (strpos($val, "규격:") !== false) {
                    $optionArr['size'] = $val;
                }
                else if (strpos($val, "표지::") !== false) {
                    $val = str_replace("표지::", "", $val);
                    $optionArr['outside'] = $val;
                }
                else if (strpos($val, "내지::") !== false) {
                    $val = str_replace("내지::", "", $val);
                    $val = explode("||", $val);
                    $optionArr['inside'] = $val;
                }
                else if (strpos($val, "간지/면지::") !== false) {
                    $val = str_replace("간지/면지::", "", $val);
                    $val = explode("||", $val);
                    $optionArr['inpage'] = $val;
                }
                else if (strpos($val, "후가공::") !== false) {
                    $optionArr['after'] = str_replace("후가공::", "", $val);
                }
                else if (strpos($val, "메모:") !== false) {
                    //$optionArr['memo'] = str_replace("메모:", "", $val);
                }
                else {
                    $val = str_replace("::", ":", $val);
                    $val = explode("||", $val);
                    $optionArr['inside'] = $val;
                }                
            }
            
        }
    
        //debug($optionArr);

        if ($optionArr['outside']) {
            $result .= "표지:". $optionArr['outside'] ."<br>";
        }

        if ($optionArr['inside']) {
            foreach ($optionArr['inside'] as $key => $val) {
                $result .= "내지:". $val ."<br>";
            }
        }

        if ($optionArr['inpage']) {
            foreach ($optionArr['inpage'] as $key => $val) {
                $result .= "간지/면지:". $val ."<br>";
            }
        }        

        if ($optionArr['after']) {
            $result .= "후가공:". $optionArr['after'];
        }
    }

    //debug($result);
    return $result;
}
  //자릿수 처리 함수 20190604 jtkim
  //num : 계산될숫자,pow : 자릿수,cutting : 처리방식,flag : 처리여부,$mod : 나머지 계산여부
  //exm_config 테이블 컬럼 형태가 text형으로 선언되어서 integer로 형변환, 나누기연산시 float으로 변환되어 결과값 int로변환
function getDigitNumCutting($num,$pow,$cutting,$flag,$mod){
   //초기화
   $org_num=$num;
   $res_num=$num;

   if($flag == "1"){
   //자릿수
   $pow=pow(10,(int)$pow);

      switch($cutting){
         //내림처리
         case "F" :
            $res_num = (int)(floor( (int)$num  / $pow) * $pow); 
            break;
         //올림처리
         case "C" :
            $res_num = (int)(ceil( (int)$num  / $pow) * $pow); 
            break;
         //반올림처리
         case "R" :
            $res_num = (int)(round( (int)$num / $pow ) * $pow); 
            break;
         default : 
            $res_num = $num;
            break;
      }


         //나머지 계산 (리턴값 2개: 처리숫자,나머지숫자)
         if( !($res_num==$org_num) && $mod == 1){
            $mod_num=$res_num-$org_num;
            $res_arr = array(
               "res_num" => $res_num,
               "res_mod" => $mod_num,
            );
            return $res_arr;
         }
   } //flag end if
   
         return $res_num;
}
?>