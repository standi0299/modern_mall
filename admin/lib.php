<?

include_once dirname(__FILE__)."/../lib/library.php";
include_once dirname(__FILE__)."/lib.inc.php";
include_once dirname(__FILE__)."/../models/m_member.php";

### form 전송 취약점 개선 20160128 by kdk
include_once dirname(__FILE__)."/../lib/lib_util_signature.php";
### form 전송 취약점 개선 20160128 by kdk

if ($login_check_flag != 'N')
{
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
  
  if (!$ici_admin) go("../login.php");
  if (!$super_admin) msg(_("전체 관리자만 접근할 수 있습니다."), -1);
}




?>