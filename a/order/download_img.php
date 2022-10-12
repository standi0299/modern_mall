<?

include "../_pheader.php";
include_once "../../lib/nusoap/lib/nusoap.php";
include "../../lib/func.xml.php";

$url = "http://".PODS10_DOMAIN."/StationWebService/GetSourceFileList.aspx";

if($_GET[pods_use] != "1") $url = "http://".PODS20_DOMAIN."/CommonRef/StationWebService/GetSourceFileList.aspx";

$returl = $url."?storageid=$_GET[storageid]";

$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,$returl);			// 접속할 URL 주소 
curl_setopt ($ch, CURLOPT_HEADER, 0);				// 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$ret = curl_exec ($ch);
$ret = iconv("EUC-KR", "UTF-8", $ret);
$ret = explode("|",$ret);

if ($ret[0]=="fail"){
	msg($ret[1]);
	echo "<script>parent.closeLayer();</script>";
	exit;
}

?>

<? if ($_GET[mode]=="filelist"){ ?>

<div class="stit"><?=_("원본이미지 보기")?></div>

<div style="padding:0 0 5px 0">
<div style="padding:5px 20px 0;"><a href="?storageid=<?=$_GET[storageid]?>" target="_blank"><?=_("이미지 전체보기")?></a></div>
<ol>
<? foreach ($ret as $k=>$v){ if(!$v) return; ?>
	<li><?=basename($v)?> <a href="<?=$v?>" target="_blank"> - <?=_("보기")?></a> - <a href="download.php?src=<?=urlencode($v)?>">download</a></li>
<? } ?>
</ol>
</div>

<? } else { ?>

<? foreach ($ret as $k=>$v){ if(!$v) return; ?>
<div style="margin:5px">
<div style="margin:5px 0"><b><?=basename($v)?></b></div>
<img src="<?=$v?>"/>
</div>
<? } ?>

<? } ?>

<? include "../_pfooter.php"; ?>