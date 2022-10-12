<?

include "../_header_only_template.php";


if ($_GET[code] == "" || $_GET[code] == "101")
{ 
  $_GET[code] = "101";

  $message[msg_title] = _("운영중지");
  $message[msg1] = _("운영이 중지된 사이트입니다.");
  $message[msg2] = _("업체 고객센터에 문의해 주세요.");
  //$message[msg3] = "<button class=\"btn-u btn-u-blue\" type=\"button\" onclick=\"location.href='http://www.webhard.co.kr';\">"._("웹하드™ 프린트홈 소개")."</button> <button class=\"btn-u btn-u-orange\" type=\"button\" onclick=\"location.href='http://www.webhard.co.kr';\">"._("웹하드 이동")."</button>";
} 
else if ($_GET[code] == "201")
{
  $message[msg_title] = "Service Error";
  $message[msg1] = _("등록되지 않은 도메인 입니다.");
  $message[msg2] = _("정확한 도메인 주소를 확인해 주세요.")."<BR>"._("업체 고객센터에 문의해 주세요.");
  //$message[msg3] = "<button class=\"btn-u btn-u-blue\" type=\"button\" onclick=\"location.href='http://www.webhard.co.kr';\">"._("웹하드™ 프린트홈 소개")."</button> <button class=\"btn-u btn-u-orange\" type=\"button\" onclick=\"location.href='http://www.webhard.co.kr';\">"._("웹하드 이동")."</button>";
}
else
{
  $message[msg_title] = _("서비스 오류");
  $message[msg1] = _("서비스중 오류가 발견되었습니다.");
  $message[msg2] = _("업체 고객센터에 문의해 주세요.");
}


$message[code] = $_GET[code];

$tpl -> assign('message', $message);
$tpl -> print_('tpl');
?>