<?

include "../lib/library.php";

//debug($_COOKIE);

$headers = array("Accept: application/json", "Content-Type: application/json");
$url = "https://www.i-scream.co.kr/api/iscreamMemberBriefInfo?m_id=".$_REQUEST[m_id];
$ret = RestCurl($url, $json, $http_status, $headers);
$mem_data = json_decode($ret,1);

//debug($mem_data);
//exit;
$mem_data[memberName] = urldecode($mem_data[memberName]);
$mem_data[memberEmail] = urldecode($mem_data[memberEmail]);
$mem_data[memberSchoolAddr] = urldecode($mem_data[memberSchoolAddr]);
$mem_data[memberSchoolName] = urldecode($mem_data[memberSchoolName]);

/*
$mem_data[memberName] = "김지만";
$mem_data[memberEmail] = "elgarsg@ilark.co.kr";
$mem_data[memberSchoolAddr] = "가나다라";
$mem_data[memberSchoolName] = "홍길동";
$mem_data[memberId] = "asdldvnuienf";
$mem_data[move_page] = "/goods/list.php?catno=005001";
*/

$move_url = "/main/index.php";

if($_REQUEST[move_page] && $_REQUEST[move_page] != "undefined")
   $move_url = $_REQUEST[move_page];

$b2b[cid]     = $cid;
$b2b[mid]     = $mem_data[memberId];
$b2b[name]    = $mem_data[memberName];
$b2b[address] = $mem_data[memberSchoolAddr];
$b2b[move_page] = $move_url;


$mem_data[resultCode] = 1;
$mem_data[result] = "success";

if($mem_data[resultCode] == 1 && $mem_data[result] == "success"){

   $m_member = new M_member();
   $chk_member = $m_member -> getInfo($cid, $mem_data[memberId]);
   //회원 데이터가 있으면 로그인
   if($chk_member) {
      //기존 데이터중 이름, 주소 업데이트
      
      $query = "
      update exm_member set
         name        = '$mem_data[memberName]',
         lastlogin  = now()
      where
         cid = '$cid' and mid = '$mem_data[memberId]'
      ";

      $db->query($query);
      
      //로그인 처리
      _member_login($b2b);
      go($move_url);

   //없으면 서비스/개인정보 약관 동의 페이지 이동
   //$b2b 데이터도 같이 보낸다.
   } else {
      //전송 데이터 인코딩
      
      $api_data = urlencode(base64_encode(json_encode($b2b)));
      
      //약관 동의 페이지로 이동
      go("../service/api_login_agree.php?data=$api_data");
      exit;
   }
} else {
   //msg("로그인 정보가 유요하지 않습니다.");
   //debug($mem_data);
   
   $err_msg = "오류가 발생하였습니다. 관리자에 문의하시기 바랍니다. 에러코드 : [".$mem_data[resultCode]."]";
   
   msg($err_msg);
   /*
   $error = $mem_data[resultCode];
   $error = $mem_data[requriedKey];
   $error = $mem_data[resultMessage];
   $error = $mem_data[result];
   */
   
   //goto_url("http://glm.qa.golfzonpark.com");
   exit;
}

/*
function goto_url($url)
{
    echo "<script language='JavaScript'> window.location.href = '$url'; </script>";
    exit;
}
*/
?>