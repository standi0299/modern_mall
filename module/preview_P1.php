<?

header("Pragma: no-cache");
header("Cache: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Expires:Mon, 26 Jul 1997 05:00:00 GMT");

$login_offset = true;
$style_not_use = true;
include_once "../_header.php";
include_once "../lib/func.xml.php";

if ($previewLink) {
	$previewLink = str_replace("|", "?editdate=".$editdate."|", $previewLink);
	$loop = explode("|", $previewLink);
	$loop = array_notnull($loop);
	
	foreach ($loop as $k=>$v) {
		$result[previewList][$k][type] = "page";
		$result[previewList][$k][url] = $v;
	}
} else {
	if (in_array($podskind, array(1, 3010, 3011, 3020))) {
		$photo = true;
	}
	
	if ($photo) {
		$soapurl = "http://".PODS20_DOMAIN."/CommonRef/StationWebService/GetPrintCountResult.aspx?";
		$param = "storageid=".$_REQUEST[storageid];
		$ret = readUrlWithcurl($soapurl.$param, false);
		
		if ($ret) {
			if (strpos($ret, "fail|") === false) {
				$obj = explode("|", $ret);
				
				if ($obj[2]) {
					$ret2 = readurl($obj[2]);
					$info = xml2array($ret2);
					
					if ($info['print'][image][filename]) {
						$loop[] = $info['print'][image];
					} else {
						$loop = $info['print'][image];
					}
				}
				
				if ($loop) {
					$k_num = 0;
					
					foreach ($loop as $k=>$v) {
						if ($v[thumbnail]) {
							for ($i=0; $i<$v[copies][copy_attr][count]; $i++) {
								$img_url[$i] = strpos($v[thumbnail], "/images/thumbnail");
								$img_url[$i] = substr($v[thumbnail], 0, $img_url[$i]);
								
								$result[previewList][$k_num][type] = "page";
								$result[previewList][$k_num][url] = ($v[copies][copy][preview]) ? $img_url[$i]."/document/new/".$v[copies][copy][preview] : $v[thumbnail];
								$k_num++;
							}
						}
					}
				}
			} else {
				$obj = explode("|", $ret);
				$result[resultMsg] = $obj[1];
			}
		}
	} else {
		$soapurl = "http://".PODS20_DOMAIN."/CommonRef/StationWebService/GetPreViewImgToJson.aspx?";
		$param = "storageid=".$_REQUEST[storageid];
		$ret = readUrlWithcurl($soapurl.$param, false);
		
		if ($ret) {
			if (strpos($ret, "fail|") === false) {
				$obj = json_decode($ret, true);
				$result[previewList] = $obj;
			} else {
				$obj = explode("|", $ret);
				$result[resultMsg] = $obj[1];
			}
		}
	}
}

$previewList = $result[previewList];
//debug($previewList);

if (!$previewList) $result[resultMsg] = _("미리보기가 지원되지 않는 편집입니다.");
if ($result[resultMsg]) msg($result[resultMsg], "close");

$tpl->assign("previewList", $previewList);
$tpl->print_('tpl');

?>