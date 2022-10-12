<?

include "../lib/library.php";

$m_board = new M_board();

$_dir = "../data/board/$cid/$_GET[board_id]/";

$tableName = "exm_board_file";
$addWhere = "where fileno='$_GET[fileno]'";
$data = $m_board->getCustomerServiceInfo($cid, $tableName, $addWhere);
if (!$data) msg(_("접근이 차단된 파일입니다."), -1);

//$data[filename] = iconv("utf-8","euc-kr",$data[filename]);

if (strstr($HTTP_USER_AGENT, "MSIE 5.5")) {
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

if ($fp = fopen($_dir.$data[filesrc], "r")) {
	print fread($fp, $data['filesize']);
}

fclose($fp);
exit();

?>
