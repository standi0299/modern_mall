<?
include_once "../lib.php";

header('Content-Type:text/csv;charset=UTF-8;');

$r_state = array(_("승인"),_("미승인"),_("차단"));
$r_email = array(_("거부"),_("수신"));
$r_sms = array(_("거부"),_("수신"));

### 회원그룹 추출
$r_grp = getMemberGrp();

### 기업그룹 추출
$r_bid = getBusiness();


$_query = base64_decode($_REQUEST[query]);
$sql = stripslashes(urldecode($_query));
//echo $sql;

$file_name = "member_email_".time().".csv";
header( "Content-type: application/vnd.ms-excel" );   
header( "Content-type: application/vnd.ms-excel; charset=utf-8");  
header( "Content-Disposition: attachment; filename = $file_name" );   
header( "Content-Description: PHP4 Generated Data" ); 
 
$result = mysql_query($sql);  
  
// 테이블 상단 만들기  
$csv_dump .= "아이디,이메일,이름,핸드폰,그룹,분류,기업그룹,이메일수신,문자수신,";
$csv_dump .= "\r\n";
  
while($data = mysql_fetch_array($result)) {
	$data[grpno]	= $r_grp[$data[grpno]];
	$data[bid]		= $r_bid[$data[bid]];
	$data[state]	= $r_state[$data[state]];
	$data[apply_email]	= $r_email[$data[apply_email]];
  $data[apply_sms]	= $r_sms[$data[apply_sms]];
  
  $csv_dump .= $data['mid'].",";
  $csv_dump .= $data['email'].",";
  $csv_dump .= $data['name'].",";
  $csv_dump .= $data['mobile'].",";
  $csv_dump .= $data['grpno'].",";
  $csv_dump .= $data['state'].",";
  $csv_dump .= $data['bid'].",";
  $csv_dump .= $data['apply_email'].",";
  $csv_dump .= $data['apply_sms'].",";
  $csv_dump .= "\r\n";
}  

$date = date("YmdHis");
$filename = "csvoutput_".$date.".csv";

header('Content-type: text/csv; charset=UTF-8');
header( "Content-type: application/vnd.ms-excel;charset=KSC5601" );
header("Content-Disposition: attachment; filename=$filename");
header( "Content-Description: PHP4 Generated Data" );
echo "\xEF\xBB\xBF"; 

echo $csv_dump;

?>
