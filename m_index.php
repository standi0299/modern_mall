<?
//include dirname(__FILE__)."/lib/library.php";

//index 페이지의 최소화를 위해 필요한 것들만 처리한다.    20141111  chunter
Header("p3p: CP=\"CAO DSP AND SO ON\" policyref=\"/w3c/p3p.xml\""); 

try
{
  include dirname(__FILE__)."/lib/class.db.php";
  include dirname(__FILE__)."/lib/lib_const.php";
  $db = new DB(dirname(__FILE__)."/conf/conf.db.php");
  
  if (file_exists(dirname(__FILE__) . "/conf/bpmvalue")) {
  	$fp = fopen(dirname(__FILE__) . "/conf/bpmvalue", "r");
  	$cid = trim(fread($fp, 9999));
  } else {
	//도메인 세팅중 다른 cid 쿠키값이 저장 되어 일시적(19.7.24부터 일주일)으로 중지
  	// if ($_COOKIE[domain_cid]) {
  	//   $cid = $_COOKIE[domain_cid];
	// } else {
	  //printhome 과 같은 형태는 cid 를 도메인에서 가져온다.      20140522    chunter
	  foreach ($r_new_cid_domain as $value) {
	  	if (strpos($_SERVER[SERVER_NAME], $value) !== false) {
	  	  $value = str_replace("." . $value, "", $_SERVER[SERVER_NAME]);
	  	  $value = str_replace("http://", "", $value);
	  	  //debug($value);
	  	  if ($value != $_SERVER[SERVER_NAME])
	  	  	if ($value != "www") $cid = $value;
	  	  break;
		}
	  }
	//}
	
	//몰이 활성화 상태인지 체크를 계속해야함.
	if (!$cid) {
	  //자체 도메인 쓰는 업체인지 DB 검사.   20140522 
	  if (substr($_SERVER[SERVER_NAME], 0, 4) == 'www.')
	    $server_domain = str_replace('www.', '', $_SERVER[SERVER_NAME]);      //www 접속을 제거. 도메인 셋팅시 www 을 제거했을 경우을 대비.
	  else
	    $server_domain = $_SERVER[SERVER_NAME];
	  
	  $domainInfo = $db->fetch("select * from exm_mall where service_domain like '%{$server_domain}%'");
	} else {
	  $domainInfo = $db->fetch("select * from exm_mall where cid='$cid'");
	}
	
	if ($domainInfo) {
	  if ($domainInfo[state] == '3') { //운영중	
	    $cid = $domainInfo[cid];
	  } else {
	  	//msg("사용이 중지된 사이트 입니다.업체에 문의하세요.", "www.printhome.co.kr");
	  	//go("/service/mobile_service_error.php?code=101");
		header("location:service/mobile_service_error.php?code=101&skin=m_default");
	  	exit ;
	  }
	} else {
	  //msg("등록되지 않은 도메인입니다. 다시 확인해 주세요.", "www.printhome.co.kr");
	  //go("/service/mobile_service_error.php?code=201");
	  header("location:service/mobile_service_error.php?code=201&skin=m_default");
	  exit ;
	}
	
	if ($cid)
	  setCookie('domain_cid', $cid, time() + 3600 * 24, '/', $_SERVER[SERVER_NAME]);
  }

  //20170824 / minks / 모바일 url스키마에서 사용
  list($center_cid) = $db->fetch("select value from exm_config where config='center_cid'", 1);
  list($center_host) = $db->fetch("select value from exm_config where cid='$center_cid' and config='host'", 1);
  $center_host_array = explode(".", $center_host);
  if (count($center_host_array) > 1) setCookie('domain_code', $center_host_array[1], 0, '/', $_SERVER[SERVER_NAME]);
  
  $query = "select * from exm_config where cid = '$cid' and (config = 'main_page' or config = 'apply_intro' or config = 'skin')";
  $res = $db->query($query);
  while ($data = $db->fetch($res)){
    $cfg[$data[config]] = $data[value];  
  }
  
    
  if($cfg[main_page] != ""){
    $url = $cfg[main_page]; 
  }else{
  	$cfg[skin] = "m_default";
    $url = ($cfg[apply_intro])? "main/intro.php?skin=".$cfg[skin] : "main/index.php?skin=".$cfg[skin];
  }
  if ($_SERVER["QUERY_STRING"]){
    $url = $url."?".$_SERVER["QUERY_STRING"];
  }
  
  header("location:".$url);  
} catch (Exception $e) {
   echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>