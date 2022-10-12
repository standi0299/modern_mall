<?

include_once dirname(__FILE__)."/../lib/library.php";
//debug(dirname(__FILE__));

if ($login_check_flag != 'N') {
   ### 접속아이피제한
   if (trim($cfg[limited_ip] && $sess_admin[admin])){
      $r_limited_ip = explode("\r\n",trim($cfg[limited_ip]));
  	   if (!in_array($_SERVER[REMOTE_ADDR],$r_limited_ip)){
  
         if (!$sess_admin[super]){
            echo "<div style='padding:10px;font:bold 20pt 돋움'>"._("관리자에 의해 설정된 IP주소에서만 접속이 가능합니다.")." <br/>"._("현재 귀하의 접속 IP주소")." $_SERVER[REMOTE_ADDR] "._("는 관리자 페이지의 접속 권한이 없습니다.")."</div>";
            exit;
         }
  	   }
   }
   //debug($sess_admin);
   //debug($ici_admin);
   //exit;
   //debug("c");
   if (!$ici_admin) go("../login.php");
   //debug($ici_admin);
   //debug($login_check_flag);
}

### 현재폴더추출
if (!$folder){
   $path = dirname($_SERVER[PHP_SELF]);
   $folder = substr($path,strrpos($path,"/")+1);
}


### 테스트용
if ($_SERVER[REMOTE_ADDR]=="192.168.0.195"){
   $whenji = 1;
}

$include_header = true;
if($_GET[not_header] == "Y" || $_POST[not_header] == "Y")
	$include_header = false;

$bIlarkNoticeFlag = false;				//아이락 공지사항 가져오기.	main 에서만 가져오도록 하기 위해 설정함			20161012		chunter
?>