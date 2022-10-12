<?
$podspage = true;
include "../_header.php";

if ($_GET[est])
{
    $returl = $_GET[est];
}
else 
{
    $url = "http://files.ilark.co.kr/portal_upload/estm/file/get_file_list.aspx";
    $url = $cfg[est_fileinfo_url];
    $returl = $url."?center_id=$cfg_center[center_cid]&storage_code=" .$_GET[storageid]. "&mall_id=$cid";
}

//$ret = readurl($returl);
$ret = readUrlWithcurl($returl, FALSE);

if (strpos($ret,"html")){
	$ret = "";
}

$ret = array_notnull(explode("|",$ret));

if ($ret[0]=="fail"){
  msg($ret[1]);
  echo "<script>close();</script>";
  exit;
}
?>

<div style="padding:10px">
<h3><?=_("(일반상품,자동견적)주문파일 다운로드")?></h3>
<div style="line-height:20px;padding-left:10px;"><?=_("클릭하여 다운로드하세요.")?></div>

<ol>
<? if (!count($ret)){ ?>
<?=_("다운로드할 파일이 없습니다.")?>
<? } ?>
<? foreach ($ret as $k=>$v){ ?>
<li style="line-height:20px;"><a href="download.php?src=<?=$v?>"><?=_("다운로드")?> [<?=_("파일명")?> - <?=substr($v, strrpos($v, '/') + 1)?>]</a></li>
<? } ?>
</ol>
</div>