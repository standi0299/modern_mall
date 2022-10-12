<?
include_once "../_header.php";

$m_etc = new M_etc();

if($_GET[orderby]){
	$orderby = "order by ".$_GET[orderby];
}else{
	$orderby = "order by a.regdt desc";
}

$list = $m_etc->getGalleyData($cid, "open", "", $orderby);

//속도가 느려 제거함
/*if ($cfg[skin_theme] == "P1") {
	$podsApi = new PODStation('20');
	
	foreach ($list as $k=>$v) {
		$preview_ret = $podsApi->GetPreViewImg($v[storageid]);
		
		if (count($preview_ret) > 0) {
			$list[$k][preview_img] = "<div class='img_up'><img src='$preview_ret[0]' style='display:none;' onerror='this.src=\"/data/noimg.png\"'/></div>";
		}
	}
}*/

//$query = "select * from md_gallery where cid = '$cid' and flag = 'open'";
//$list = $db->listArray($query);

$tpl->assign('list',$list);
$tpl->print_('tpl');
?>