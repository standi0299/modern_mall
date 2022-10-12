<?

/*
* @date : 20180829
* @author : kdk
* @brief : 미오디오용 필름 스캔이미지 다운로드.
* @desc : http://storage.wecard.co.kr/zipfile_down/downloadZipFile.ashx
*/

include "../_header.php";

chkMember();

if (!$_GET[dt] || $_GET[dt] == ""){
    msg(_("파라메타가 없습니다."));
    echo "<script>close();</script>";
    //exit;
}

$storage_code = str_replace("-", "", $_GET[dt])."_".$sess[mid];
$url = "http://storage.wecard.co.kr/zipfile_down/downloadZipFile.ashx";
$returl = $url."?center_id=$cfg_center[center_cid]&mall_id=$cid&mid=$sess[mid]&storage_code=" .$storage_code;

$ret = sendQueryData($returl);
//debug($ret);
//exit;

//$ret = readurl($returl);
//$ret = readUrlWithcurl($returl, FALSE);

if (strpos($ret,"html")){
	$ret = "";
}

$ret = array_notnull(explode("|",$ret));

if ($ret[0]=="FAIL"){
  msg($ret[1]);
  echo "<script>close();</script>";
  exit;
}

$src = $ret[1];
if (is_numeric(strpos($src,"http://"))){
    $src = str_replace("http://","",$src);
    $http = "http://";
}
if (is_numeric(strpos($src,"https://"))){
    $src = str_replace("https://","",$src);
    $http = "https://";
}

$src = explode("/",$src);
foreach ($src as $k=>$v){
  if ($k > 0)     //index 0 은 호스트 정보를 담고 있다.. 192.168.1.197:8080 과 같이 : 도 인코딩 되어 포트정보를 읽을수 없다.    20141027    chunter
    $src[$k] = urlencode($v);
}
$src = $http.implode("/",$src);
$src = str_replace(basename($src),basename($src),$src);
$src = str_replace("+", "%20",$src);      //파일명에 스페이스가 있을경우 encoding시 +로 변환된다.    20131218    chunter

//echo $src;
//exit;
Header("Content-type: file/unknown");
Header("Content-Disposition: attachment; filename=".basename($src));
Header("Content-Description: PHP3 Generated Data");
Header("Pragma: no-cache");
Header("Expires: 0");

echo readurl($src);



?>
<!--
<div style="padding:10px">
<h3><?=_("(필름 스캔 이미지) 전체 파일 다운로드")?></h3>
<div style="line-height:20px;padding-left:10px;"><?=_("클릭하여 다운로드하세요.")?></div>

<ol>
<? if (!count($ret)){ ?>
<?=_("다운로드할 파일이 없습니다.")?>
<? } ?>
<? if ($ret[1]){ ?>
<li style="line-height:20px;"><a href="/a/order/download.php?src=<?=$ret[1]?>"><?=_("다운로드")?> [<?=_("파일명")?> - <?=substr($ret[1], strrpos($ret[1], '/') + 1)?>]</a></li>
<? } ?>
</ol>
</div>
-->