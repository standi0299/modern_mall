<?

include "../../lib/library.php";

### sk_server_ip_addr sk_server_port encrypted_msg
$fp = fsockopen($_GET[sk_server_ip_addr],$_GET[sk_server_port],$errno,$errstr,5);
if(!$fp){
	// 소켓 연결 에러 처리
	echo "$errstr ($errno)<br>\n";
} else {
	$send_msg = fputs($fp, $_GET[encrypted_msg]);

	$receive_msg = '';
	while(!feof($fp)){$receive_msg .= fgets($fp, 4096);}
	fclose($fp);

	echo $receive_msg;
}

?>