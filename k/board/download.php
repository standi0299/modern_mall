<?
include "../../lib/library.php";

$_dir = "../../data/board/$cid/$_GET[board_id]/";

$lang_kind = $_GET[langs];
if ($lang_kind == "kor") $lang_kind="kor";

$lang_kor_data = array(
            //한글모드
            'FileBlock'                     => _("접근이 차단된 파일입니다.")                   
            );
            
$lang_eng_data = array(
            //영어모드
            'FileBlock'                     => 'File access is blocked'
            );

if ($lang_kind == "eng")
    $lang_data = $lang_eng_data;
else
    $lang_data = $lang_kor_data;

$data = $db->fetch("select * from exm_board_file where fileno=$_GET[fileno]");
if (!$data) msg("$lang_data[FileBlock]",-1);

//$data[filename] = iconv("utf-8","euc-kr",$data[filename]);

if (strstr($HTTP_USER_AGENT,"MSIE 5.5")){
	Header("Content-Type: doesn/matter");
	Header("Content-Length: $data[filesize]");
	Header("Content-Disposition: filename=$data[filename]");
	Header("Content-Transfer-Encoding: binary");
	Header("Pragma: no-cache");
	Header("Expires: 0");
} else {
	Header("Content-type: file/unknown");
	Header("Content-Length: $data[filesize]");
	Header("Content-Disposition: attachment; filename=$data[filename]");
	Header("Content-Description: PHP3 Generated Data");
	Header("Pragma: no-cache");
	Header("Expires: 0");
}

if ($fp = fopen($_dir.$data[filesrc], "r")){
	print fread($fp,$data['filesize']);
}
fclose($fp);
exit();

?>
