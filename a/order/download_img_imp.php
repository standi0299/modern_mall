<?

include "../_pheader.php";
include_once "../../lib/nusoap/lib/nusoap.php";
include "../../lib/func.xml.php";

$release = $db->fetch("select * from exm_release where rid = '$_GET[item_rid]'");

$url = trim($release[oasis_url])."/FileDown.aspx";
$returl = $url."?order_code=$_GET[payno]&order_product_code=$_GET[storageid]";
//debug($returl);
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,$returl);			// 접속할 URL 주소 
curl_setopt ($ch, CURLOPT_HEADER, 0);				// 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$ret = curl_exec ($ch);
$ret = iconv("EUC-KR", "UTF-8", $ret);
$ret = trim(preg_replace("/\<\?.*?\?\>/si","",$ret));
$ret = xml2Array($ret);
//debug($ret);
switch ($ret[Photo][result][response_attr][response_id]){
	case "ERROR":
		msg($ret[Photo][result][response]);
		echo "<script>parent.closeLayer();</script>";
		exit; break;
	case "SUCCESS":
		break;
	default:
		msg(_("오아시스와의 통신에러입니다."));
		echo "<script>parent.closeLayer();</script>";
		exit; break;
}

$loop = $ret[Photo][Download][File];

unset($ret);
foreach ($loop as $k=>$v){
	if (is_numeric($k)) $ret[$k][url] = $v;
	else {
		$k = str_replace("_attr","",$k);
		$ret[$k][attr] = $v;
	}
}
foreach ($ret as $k=>$v){
	$ret2[$v[attr][type]][] = $v[url];
}
$r_type = array(
	"page"			=> _("내지/페이지"),
	"thumbnail"		=> _("내지썸네일"),
	"infothumbnail"	=> _("전체썸네일"),
);

?>

<div class="stit"><?=_("합성결과 보기")?></div>

<table class="tb22">
<tr>
	<th><?=_("유형")?></th>
	<th><?=_("보기/다운로드")?></th>
</tr>
<? foreach ($ret2 as $k=>$v){ foreach ($v as $k2=>$v2){

if (is_numeric(strpos($v2,"http://"))){
	$url = str_replace("http://","",$_GET[src]);
	$http = "http://";
}
if (is_numeric(strpos($v2,"https://"))){
	$url = str_replace("https://","",$_GET[src]);
	$http = "https://";
}

$url = explode("/",$url);
foreach ($url as $k3=>$v3){
	$url[$k3] = urlencode($v3);
}
$url = $http.implode("/",$url);

?>
<tr>
	<? if ($b_type!=$k){ ?>
	<td rowspan="<?=count($v)?>" nowrap><?=$r_type[$k]?></td>
	<? } ?>
	<td style="line-height:150%;">
	<a href="<?=$v2?>" target="_blank"><?=basename($v2)?></a><br/><a href="download.php?src=<?=$v2?>"><?=_("다운로드")?></a>
	</td>
</tr>
<? $b_type = $k; }} ?>
</table>

<? include "../_pfooter.php"; ?>