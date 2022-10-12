<?

include_once dirname(__FILE__)."/../lib/library.php";

if (is_numeric(strpos($_GET[src],"http://"))){
	$_GET[src] = str_replace("http://","",$_GET[src]);
	$http = "http://";
}
if (is_numeric(strpos($_GET[src],"https://"))){
	$_GET[src] = str_replace("https://","",$_GET[src]);
	$http = "https://";
}

$_GET[src] = explode("/",$_GET[src]);
foreach ($_GET[src] as $k=>$v){
  if ($k > 0)     //index 0 은 호스트 정보를 담고 있다.. 192.168.1.197:8080 과 같이 : 도 인코딩 되어 포트정보를 읽을수 없다.    20141027    chunter
    $_GET[src][$k] = urlencode($v);
}
$_GET[src] = $http.implode("/",$_GET[src]);
$_GET[src] = str_replace(basename($_GET[src]),basename($_GET[src]),$_GET[src]);
$_GET[src] = str_replace("+", "%20",$_GET[src]);      //파일명에 스페이스가 있을경우 encoding시 +로 변환된다.    20131218    chunter

//echo $_GET[src];
//exit;
Header("Content-type: file/unknown");
Header("Content-Disposition: attachment; filename=".basename($_GET[src]));
Header("Content-Description: PHP3 Generated Data");
Header("Pragma: no-cache");
Header("Expires: 0");

echo readurl($_GET[src]);

?>