<?
include "../lib/library.php";

if ($_GET[mode] == "tax")
{
	$_dir = "../data/bill/$cid/tax_seal.jpg";
	$dnfile="사업자등록증.jpg";
}
else { 
	$_dir = "../data/bill/$cid/bank_seal.jpg";
	$dnfile="통장사본.jpg";
}
	
$file=dirname(__FILE__). "/".$_dir; # 서버에 저장된 파일 정보
$dnfile = iconv("utf-8","euc-kr",$dnfile);

if (strstr($HTTP_USER_AGENT, "MSIE 5.5")) {
	Header("Content-Type: doesn/matter");
	Header("Content-Length: " .filesize("$file"));
	Header("Content-Disposition: filename=$dnfile");
	Header("Content-Transfer-Encoding: binary");
	Header("Pragma: no-cache");
	Header("Expires: 0");
} else {
	Header("Content-type: file/unknown");
	Header("Content-Length: " .filesize("$file"));
	Header("Content-Disposition: attachment; filename=$dnfile");
	Header("Content-Description: PHP3 Generated Data");
	Header("Pragma: no-cache");
	Header("Expires: 0");
}

if (is_file("$file")) { 
  $fp = fopen("$file", "rb"); 

  if (!fpassthru($fp))
    fclose($fp); 

} else { 
  echo "해당 파일이나 경로가 존재하지 않습니다."; 
} 

exit();

?>
