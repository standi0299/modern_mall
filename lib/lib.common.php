<?
/**
 * PHP Library
 *
 EXM.co.kr 참조 mirrh
 */
header("p3p: CP=\"CAO DSP AND SO ON\" policyref=\"/w3c/p3p.xml\"");
header("Content-Type: text/html; charset=utf-8");



function md5_encode($arr,$mode=0){
  $txt = base64_encode(serialize($arr));
  $key = substr(md5(crypt('')),0,8);
  $ip = explode(".",($mode)?$_SERVER[SERVER_ADDR]:$_SERVER[REMOTE_ADDR]);
  for ($i=0;$i<4;$i++) $_ip[] = sprintf("%03d",$ip[$i]);
  for ($i=0;$i<8;$i++) $num .= sprintf("%03d",ord($key[$i])+$_ip[$i%4]);
  for ($i=0;$i<strlen($txt);$i++) $num .= sprintf("%03d",ord($txt[$i])+ord($key[$i%8]));
  for ($i=0;$i<strlen($num)/3;$i++) $ret .= chr(substr($num,$i*3,3)%256);
  $ret = base64_encode($ret);
  return $ret;
}

function md5_decode($txt,$mode=0){
  $ret = base64_decode($txt);
  $ip = explode(".",($mode)?$_SERVER[SERVER_ADDR]:$_SERVER[REMOTE_ADDR]);
  for ($i=0;$i<4;$i++) $_ip[] = sprintf("%03d",$ip[$i]);
  for ($i=0;$i<strlen($ret);$i++) $num .= sprintf("%03d",ord($ret[$i]));
  $_key = substr($num,0,24);
  for ($i=0;$i<8;$i++) $key .= chr(substr($_key,$i*3,3)-$_ip[$i%4]);
  $_txt = substr($num,24);
  for ($i=0;$i<strlen($_txt)/3;$i++) $xxx .= chr(substr($_txt,$i*3,3)-ord($key[$i%8]));
  $ret = unserialize(base64_decode($xxx));
  return $ret;
}

//회원인증
//ssl 인증서 추가에 따른 통신방식 별 쿠키 세팅 추가 / 18.01.23 / kjm
function setAuthMember($sess,$member=array(),$admin=0)
{
   $sess = md5_encode($sess);

   $referer_exp = explode("://", $_SERVER['HTTP_REFERER']);

   if ($admin) setCookie('sess_admin',$sess,0,'/');
   else {
      if(!$referer_exp[0] || $referer_exp[0] == "http")
         $cResutl = setCookie('sess', $sess, 0, '/', '');
      else
        $cResutl = setCookie('sess', $sess, 0, '/', '');
   }

   if ($member){
      $member = md5_encode($member);
      if(!$referer_exp[0] || $referer_exp[0] == "http")
         setCookie('member', $member, 0, '/', '');
      else
         setCookie('member', $member, 0, '/', '');
   }
   return $sess;
}

### 배열 null 제거 함수
function array_notnull($arr) {
    if (!is_array($arr))
        return;
    foreach ($arr as $k => $v)
        if (!$v)
            unset($arr[$k]);
    return $arr;
}

### 배열/클래스 출력 함수
function debug($data) {
   global $r_debug_IP;
   $bDebugDisp = false;

   //등록된 IP 에서만 debug 함수룰 출력하도록 변경			20160726		chunter
	foreach ($r_debug_IP as $key => $value)
	{
      if (($value == "all") || ($value == "localhost" && strpos(USER_HOST, "192.168.1") !== false) || ($_SERVER[REMOTE_ADDR] == $value))
		{
         $bDebugDisp = true;
			break;
		}
	}

   if ($bDebugDisp)
   {
      print "<div style='background:#000000;color:#00ff00;padding:10px;text-align:left'><xmp style=\"font:8pt 'Courier New'\">";
      print_r($data);
      print "</xmp></div>";
   }
}

### 쿼리 출력문 확인
function drawQuery($query, $mode = 0) {
   global $db;
   if ($mode)
      $query = "explain " . $query;
   $res = $db -> query($query);
   while ($data = $db -> fetch($res))
      $loop[] = $data;
   drawTable($loop);
}

function drawTable($data) {
    if (!$data) {
        echo "-- No Data --";
        return;
    }
    $keys = array_keys($data[0]);
    $ret = "<table border=1 bordercolor=#cccccc style='border-collapse:collapse' style='font:8pt tahoma'>";
    $ret .= "<tr bgcolor=#f7f7f7><th>" . implode("</th><th>", $keys) . "</th></tr>";
    foreach ($data as $v)
        $ret .= "<tr><td>" . implode("</td><td>", $v) . "</td></tr>";
    $ret .= "</table>";
    echo $ret;
}

### 문자열 자르기 함수
/*function strcut($str, $len) {
    if (strlen($str) <= $len)
        return $str;
    else {
        return mb_strcut($str, 0, $len, "utf-8") . "..";
    }
}*/
function strcut($str,$len,$mark="..")
{
    if (strlen($str) <= $len) return $str;
    else {
        return mb_strcut($str,0,$len,"utf-8").$mark;
    }
}

function msgNpopClose($msg)
{
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo "<script>alert('$msg');";
    echo "window.opener.location.reload();";
    echo "window.close()</script>";
    exit;
}

//메세지 출력 후 해당 페이지로 이동 / 14.06.05 / kjm
function msgNlocationReplace($msg,$location,$parent = "")
{
   echo "<script type='text/javascript'>";
   echo "alert('$msg');";

   //부모창을 이동시키기 위해 추가 / 17.02.02 / kjm
   if($parent == "Y")
      echo "parent.location.replace('$location');";
   else
      echo "document.location.replace('$location');";

   echo "</script>";
   exit;
}

### 예외 처리
function msg($msg, $code = null, $target = '') {

   echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
   echo "<script>alert('$msg');</script>";
   switch (getType($code)) {
      case "null" :
         return;
      case "integer" :
         if ($code)
            echo "<script>history.go($code)</script>";
         exit ;
      case "string" :

         if ($code == "close")
            echo "<script>window.close()</script>";
         else {
             //alert창 띄우고 창 종료 메세지 제거 / 14.05.14 / kjm
             //echo "<script>window.close()</script>";
             echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";

             //location.href('$code');는 크롬에서 안되서 ='$code'로 수정 / 14.08.25 / kjm
             //echo "<script>{$target}location.href='$code'</script>";
             echo "<script>{$target}location.href='$code'</script>";
         }
      exit;
   }
}

### 기존 msg 함수는 url로 이동을 하지 않고 윈도우창이 닫힘
### 팝업창에서 헤더 중복되서 헤더 제거
### 메세지출력, 해당 url로 이동 / 14.04.25 / kjm
### 사용하지 않음 / 14.10.10 / kjm
/*
function msg_url($msg, $code = null, $target = '') {

    //echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo "<script>alert('$msg')</script>";
    switch (getType($code)) {
        case "null" :
            return;
        case "integer" :
            if ($code)
                echo "<script>history.go($code)</script>";
            exit ;
        case "string" :

            if ($code == "close")
                echo "<script>window.close()</script>";
            else {
                echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
                echo "<script>{$target}location.href('$code')</script>";
        }
        exit ;
    }
}
*/

function msg_confirm($msg, $ok_script='', $fail_script='')
{
  echo '<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">';
  echo "<script> if (confirm('$msg')) { ";
  if ($ok_script) echo $ok_script .';';
  echo "} else { ";
  if ($fail_script) echo $fail_script.';';
  echo "}";
  echo "</script>";
}

function popupMsgNLocation($msg, $location = '')
{
  echo '<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">';
  echo "<script>alert('$msg');window.close();";
  if ($location)
    echo "opener.parent.location.href='$location';</script>";
  else
    echo "opener.parent.location.href=window.opener.document.URL;</script>";

  exit;
}

### 페이지 이동
function go($url) {
    header("location:$url");
    exit ;
}

### 변수 선택적 자동 병합
function getQueryString($except = '', $data = '') {
    $ret = array();

    if (!$data)
        $data = $_GET;

    if ($except) {

        $except = explode(",", $except);
        foreach ($data as $k => $v) {
            if (!in_array($k, $except))
                $ret[$k] = $v;
        }
    } else
        $ret = &$data;

    return http_build_query($ret);
}
### 외부 파일 읽기
function readurl($str,$port=80,$method="GET",$tmp='', $bConvertUtf8 = false) {
   $url = parse_url($str);

   //https 접속을 이용하기 위해 포트와 스키마 설정을 변경한다.     20150717    chunter
   if ($url[scheme] == "https") {
      if ($port == 80)
         $port = 443;
      $connectUrl = "ssl://$url[host]";
   } else
      $connectUrl = $url[host];

   //debug($connectUrl);
   $fp = @fsockopen($connectUrl, $port);

   if ($method=="GET") {
      @fwrite($fp, "GET $url[path]?$url[query] HTTP/1.0\r\nHost: $url[host]\r\n\r\n");
   } else {
      @fwrite($fp, "POST $url[path] HTTP/1.0\r\nHost: $url[host]\r\n{$tmp}Content-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($url[query]) . "\r\nConnection: close\r\n\r\n$url[query]\r\n\r\n");
   }
   if (!$fp) return;

   while (!feof($fp)) $out .= @fread($fp, 1024);
   fclose($fp);

   $out = explode("\r\n\r\n", $out);
   array_shift($out);
   $out = implode("", $out);

   if ($bConvertUtf8)
      $out = iconv("EUC-KR", "UTF-8", $out);

   return $out;
}


function readUrlWithcurl($returl, $bConvertUtf8 = true)
{
   return readurl($returl,80,"GET",'', $bConvertUtf8);

   /*
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL,$returl);     // 접속할 URL 주소
    curl_setopt ($ch, CURLOPT_HEADER, 0);       // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
    curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec ($ch);

    if ($bConvertUtf8)
        $ret = iconv("EUC-KR", "UTF-8", $ret);

    return $ret;
  */
}


### 날짜 출력형식
function toDate($date, $div = "-") {
    if (!$date)
        return "";
    $s = ($div == "kr") ? array(_("년").' ', _("월").' ', _("일")) : array($div, $div);
    $ret = sprintf("%04d{$s[0]}%02d{$s[1]}%02d{$s[2]}", substr($date, 0, 4), substr($date, 4, 2), substr($date, 6, 2));
    if (strlen($date) == 6)
        $ret = substr($ret, 0, 7);
    return $ret;
}

### 디렉토리 파일명 추출
function ls($dir, $mode = '', $except = array()) {
   $ret[0] = array();
   $ret[1] = array();

   if (!is_dir($dir))
      return array();
   if ($handle = opendir($dir)) {
      while (false !== ($file = readdir($handle))) {
         if ($file != "." && $file != ".." && !in_array($file, $except)) {
            if (is_dir($dir . $file))
               $ret[0][] = $file;
            else
               $ret[1][] = $file;
         }
      }
      closedir($handle);
   }

   if ($mode == "dir")
      $ret[1] = array();
   if ($mode == "file")
      $ret[0] = array();

   if ($ret[0])
      sort($ret[0]);
   if ($ret[1])
      sort($ret[1]);
   $ret = array_merge($ret[0], $ret[1]);
   return $ret;
}

### 키워드 분리
function getSiteKeyword($referer) {
    $enginList = array("naver" => "query", "daum" => "q", "nate" => "q", "google" => "q", "yahoo" => "p", "paran" => "Query", "bing" => "q", "gekota" => "q", "lycos" => "query", "chol" => "q", "altavista" => "p", "freechal" => "keyword", "goo" => "MT", "dreamwiz" => "q", );
    $browsers = implode("|", array_keys($enginList));
    $site = parse_url($referer);
    preg_match("/($browsers)/i", $site['host'], $results);

    if ($results[0]) {
        $querystring = explode("&", $site['query']);
        foreach ($querystring as $qs) {
            list($k, $v) = explode("=", $qs, 2);
            if ($k == $enginList[$results[0]]) { $r = $v;
                break;
            }
        }
        if ($r) {
            $q = trim(urldecode($r));
            //			if (@preg_match('/.+/u', $q)) $q = iconv("UTF-8", "EUC-KR", $q);
            $q = addslashes(strtolower(urlutfchr($q)));
        }
    }
    $dname = domainName($site['host']);
    $adtype = getAdType($site['host'], $dname);

    if (is_numeric($dname))
        $dname = "IP";
    else if (!$referer || !$site['host'] || strpos($site['host'], $_SERVER[HTTP_HOST]) !== false) { $dname = "bookmark";
        $adtype = "";
    }

    return array("keyword" => $q, "host" => $site['host'], "site" => $dname, "adtype" => $adtype);
}

### 네이버 키워드 (UTF)
function tostring($text) {
    return iconv('UTF-16LE', 'UHC', chr(hexdec(substr($text[1], 2, 2))) . chr(hexdec(substr($text[1], 0, 2))));
}

function urlutfchr($text) {
    return urldecode(preg_replace_callback('/%u([[:alnum:]]{4})/', 'tostring', $text));
}

function domainName($host) {
    $div = explode(".", $host);
    list($ret) = array_slice($div, -2, 1);
    if (strlen($ret) < 3)
        list($ret) = array_slice($div, -3, 1);
    return $ret;
}

function getAdType($host, $site) {
    $div = explode(".", $host);
    switch ($site) {
        case "naver" :
            if (in_array($div[0], array('kin', 'cafe', 'shopping', 'blog')))
                $ret = $div[0];
            else if (strpos($div[0], "mail") !== false)
                $ret = "mail";
            else if (in_array($_GET[adtype], array('click', 'time')))
                $ret = $_GET[adtype];
            break;
        case "daum" :
            /*** 스폰서박스 sponbox ***/
            if (in_array($div[0], array('k', 'shopping'))) { $ret = $div[0];
                break;
            } else if (strpos($div[0], "cafe") !== false)
                $ret = "cafe";
            else if (strpos($div[0], "mail") !== false)
                $ret = "mail";
            break;
        case "adtive" :
            $ret = "adtive";
            break;
    }
    if ($host == "googleads.g.doubleclick.net")
        $ret = "adsense";
    else if ($_GET[gclid])
        $ret = "adwars";
    if ($_GET[OVRAW])
        $ret = "over";
    return $ret;
}

### 썸네일 함수
function thumbnail($src, $folder, $sizeX = 100, $fix = 0) {
   $size = @getimagesize($src);
   switch ($size[2]) {
      case 1 :
         $image = @ImageCreatefromGif($src);
         break;
     case 2 :
         $image = ImageCreatefromJpeg($src);
         break;
     case 3 :
         $image = ImageCreatefromPng($src);
         break;
     default :
         return;
   }
   if ($fix) {
      $sizeX = explode(",", $sizeX);
      $sizeX[1] = $sizeX[0];

      $bigger = ($size[0] >= $size[1]) ? $size[0] : $size[1];
      $per = ($bigger > $sizeX[0]) ? $sizeX[0] / $bigger : 1;

      $width = $size[0] * $per;
      $height = $size[1] * $per;
      $posX = ($sizeX[0] - $width) / 2;
      $posY = ($sizeX[1] - $height) / 2;

      $dst = ImageCreateTruecolor($sizeX[0], $sizeX[1]);

         //PNG 파일인 경우와 아닌경우를 처리(투명처리를 위해)			20160701			chunter
			if ($size[2] == 3)
			{
     	      imagealphablending($dst, false);
				imagesavealpha($dst, true);
				imagealphablending($image, true);
			} else {
     	      $bgc = ImageColorAllocate($dst, 255, 255, 255);
     	      ImageFilledRectangle($dst, 0, 0, $sizeX[0], $sizeX[1], $bgc);
			}

      Imagecopyresampled($dst, $image, $posX, $posY, 0, 0, $width, $height, $size[0], $size[1]);
   } else {
      $width = $sizeX;
      $height = $size[1] / $size[0] * $sizeX;
      if ($width == $size[0] || $size[2] == 1) {
         copy($src, $folder);
         return;
      }
      $dst = ImageCreateTruecolor($width, $height);

      //PNG 투명 유지.		20160701			chunter
      if ($size[2] == 3)
      {
     	   imagealphablending($dst, false);
			imagesavealpha($dst, true);
			imagealphablending($image, true);
		}

     //이미지 사이즈만 조절 전체 배경사이즈는 안바뀜
     Imagecopyresampled($dst, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
   }

   if ($size[2] == 3)
		imagepng($dst,$folder);
	else
      ImageJpeg($dst, $folder, 100);

      ImageDestroy($dst);
      ImageDestroy($image);
}

function popupReload() {
   echo "<script>parent.location.reload();</script>";
   exit ;
}

### 관리자 로그인
function _admin_login($data) {
   ### 인증처리 (수정불가)
   $sess_admin = array('admin' => $data[admin], 'cid' => $data[cid], 'mid' => $data[mid], 'super' => $data[super], 'name' => $data[name], 'menu_priv' => "", "service_expire_date" => $data[service_expire_date]); //$data[priv] 오류!

   setAuthMember($sess_admin, array(), 1);
}

### 회원 로그인
function _member_login($data) {
    global $db;

    ### 인증처리 (수정불가)
    $sess = array(
            'admin' => $data[admin],
            'cid'   => $data[cid],
            'mid'   => $data[mid],
            'super' => $data[super],
            'name'  => $data[name],
            'grpnm' => $data[grpnm],
            'mngno' => $data[manager_no],
            'submid' => $data[sub_mid],
            'submidname' => $data[sub_mid_name],
            'submngflag' => $data[sub_mng_flag],
            'pretty_pricedisplay' => $data[pretty_pricedisplay],
            );
    $ret = setAuthMember($sess);
    //debug($ret);
    return $ret;
}



function utf2euckr($str) {
   //return (mb_detect_encoding($str)=="UTF-8") ? iconv("UTF-8", "EUC-KR", $str) : $str;
   return (mb_detect_encoding($str) == "UTF-8") ? mb_convert_encoding($str, "EUC-KR", "UTF-8") : $str;
}

function euckr2utf($str) {
   //return (mb_detect_encoding($str,"ASCII,EUC-KR,CP949",true)) ? iconv("EUC-KR","UTF-8",$str) : $str;
   return (mb_detect_encoding($str) == "UTF-8") ? mb_convert_encoding($str, "UTF-8", "EUC-KR") : $str;
}

### 탭,엔터 제거 2014.06.26 by kdk
function strDelEntTab($str) {
	if($str) {
		$str = str_replace("\t", "", $str);
		$str = str_replace("\n", "", $str);
		$str = str_replace("\r", "", $str);
	}

	return $str;
}

### div tag를 span tag 로 변경 (프린트 홈 스킨 일부만 사용) 2015.04.23 by kdk
function div2span($str) {
	if($str) {
		$str = str_replace("<div", "<span", $str);
		$str = str_replace("/div>", "/span>", $str);
	}

	return $str;
}



function openPopup($src, $width, $height, $alertMsg = '', $locationHref='', $scrollbars = '', $resizable = '', $popupName = '') {
  $scrollbars = "1";
  $resizable = "no";

  //resizable = "yes";
  $script = "";
  if ($alertMsg)
    $script .= "alert('$alertMsg');";
  if ($locationHref)
    $script .= "location.href = '$locationHref';";

  $script .= "window.open('$src', '$popupName','width=$width,height=$height,scrollbars=$scrollbars,toolbar=no,status=no,resizable=$resizable,menubar=no');";
  echo "<script>$script</script>";
  exit;
}

//파일이 서버에 존재하는지 확인 / 15.12.02 / kjm
function URL_exists($url) {
    $url = str_replace("http://", "", $url);

    list($domain, $file) = explode("/", $url, 2); // 도메인부분과 주소부분으로 나눕니다.
    $fid = fsockopen($domain, 80); // 도메인을 오픈합니다.
    fputs($fid, "GET /$file HTTP/1.0\r\nHost: $domain\r\n\r\n"); // 파일 정보를 얻습니다.
    $gets = fgets($fid, 128);
    fclose($fid);

    if(preg_match("/200 OK/", $gets)) // 서버가 200을 리턴할 경우
        return TRUE;
    else // 아닐경우
        return FALSE;
}

function instgram_linkage_data(){
   global $cfg;

   //$cfg[insta_config] = unserialize($cfg[insta_config]);

   $client_id = $cfg[insta_config][client_id];
   $client_secret = $cfg[insta_config][client_secret];
   $access_token = $cfg[insta_config][access_token];

   $url = "https://api.instagram.com/v1/users/self/media/recent?client_id=".$client_id."&access_token=".$access_token."&count=6";

   if($client_id && $client_secret && $access_token)
      $_source=RestCurl($url, $json, $http_status);//readUrlWithcurl($url); //미오디오 소켓통신에서 curl로 변경 / 20190402 / kdk.

   $_data = json_decode($_source,1);

   $json = $_data[data];

   return $json;
}

function curl_get_contents($url)
{
   $ch = curl_init($url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
   $data = curl_exec($ch);
   curl_close($ch);
   return $data;
}

function RestCurl($url, $post_data, &$http_status, $header = null) {
   $ch=curl_init();
   // user credencial
   //curl_setopt($ch, CURLOPT_USERPWD, "username:passwd");
   curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_URL, $url);
   // post_data

   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
   if (!is_null($header)) {
   		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
		 	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_HEADER, true);
   }
	 if (substr($url, 0, 5) == "https")
	 {
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	 }
   //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

   curl_setopt($ch, CURLOPT_VERBOSE, true);
   $response = curl_exec($ch);

   $body = null;
   // error
   if (!$response) {
      $body = curl_error($ch);
      // HostNotFound, No route to Host, etc  Network related error
      $http_status = -1;
   } else {
      //parsing http status code
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (!is_null($header)) {
         $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
         $header = substr($response, 0, $header_size);
         $body = substr($response, $header_size);
      } else {
         $body = $response;
      }
   }
   curl_close($ch);
   return $body;
}

function passwordCommonEncode($data, $mode=''){
   global $cfg,$cid;

   // 미오디오에서는 base64_encode가 추가되어 포토큐브 비밀번호 이관 후 같은 인코딩 타입이 아니어서 기존 인코딩으로 변경 jtkim 19.08.02
   if($cid == "fotocube2019"){
      return md5($data);
   }else if($cfg[skin_theme] == "M2" || $cfg[skin_theme] == "M3"){
      return base64_encode(md5(mb_convert_encoding($data, "UTF-16LE", "UTF-8"),true));
   } else {
      return md5($data);
   }
}

//게시판별 auto_msg 체크 및 등록 210517 jtkim
function setAutoMailByBoard(){
    global $cid,$db;
    $board_sql = "select * from exm_board_set where cid = '$cid'";
    $board_list = $db->listArray($board_sql);

    $auto_msg_sql = "select * from exm_automsg where cid ='$cid' and catnm = 'mail_board'";
    $auto_msg_list = $db->listArray($auto_msg_sql);

    // 게시판이 삭제된 경우 exm_automsg 내용삭제 시켜야함
    if(count($auto_msg_list) > 0){
        forEach($auto_msg_list as $k){
            $delete_flag = true;
            if(count($board_list) > 0){
                forEach($board_list as $kk){
                    if($k['type'] == $kk['board_id']) $delete_flag = false;
                }
            }
            if($delete_flag) $db->query("delete from exm_automsg where cid = '$cid' and catnm = 'mail_board' and type='$k[type]' ");
        }
    }

    // 게시판이 존재하고, exm_automsg 등록이 안되어 있을시 insert함
    if(count($board_list) > 0){
        forEach($board_list as $k) {
            $insert_flag = true;
            if(count($auto_msg_list) > 0){
                forEach($auto_msg_list as $kk){
                    if($k['board_id'] == $kk['type']) $insert_flag = false;
                }
            }
            if($insert_flag){
                $query = "
                    insert into exm_automsg set
                        cid         = '$cid',
                        catnm       = 'mail_board',
                        type        = '$k[board_id]',
                        subject     = '',
                        msg1        = '',
                        msg2        = '',
                        send        = '0'
                    on duplicate key 
                        update msg1 = '', msg2  = '', send = '0'
                    ";
                $db -> query($query);
            }
        }
    }
}
?>
