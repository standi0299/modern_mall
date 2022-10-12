<?
//견적의뢰상세보기
//2015.07.01 by kdk

include_once "../_header.php";

//견적의뢰정보
$query = "
select * from tb_extra_cart where cartno = '$_GET[cartno]'";
$db -> query("set names utf8");
$data = $db -> fetch($query);
//debug($data);

//인쇄견적 안내 2015.08.05 by kdk
list($data[extra_info]) = $db->fetch("select extra_info from exm_goods where goodsno='$data[goodsno]'",1);

//의뢰내용
$estOrderOptionDesc = formatEstOrderOptionDesc($data[est_order_option_desc]);
//debug($estOrderOptionDesc);

if($estOrderOptionDesc[_("수량")]) $data[est_cnt] = $estOrderOptionDesc[_("수량")];
if($estOrderOptionDesc[_("규격")]) $data[est_document] = $estOrderOptionDesc[_("규격")];
if($estOrderOptionDesc[_("표지")]) $data[est_cover] = $estOrderOptionDesc[_("표지")];
if($estOrderOptionDesc[_("내지")]) $data[est_page] = $estOrderOptionDesc[_("내지")];
if($estOrderOptionDesc[_("면지")]) $data[est_m_page] = $estOrderOptionDesc[_("면지")];
if($estOrderOptionDesc[_("간지")]) $data[est_g_page] = $estOrderOptionDesc[_("간지")];
if($estOrderOptionDesc[_("추가내지")]) $data[est_add_page] = $estOrderOptionDesc[_("추가내지")];
if($estOrderOptionDesc[_("옵션")]) $data[est_fix_option] = $estOrderOptionDesc[_("옵션")];
if($estOrderOptionDesc[_("후가공")]) $data[est_after_option] = $estOrderOptionDesc[_("후가공")];
//debug($data[est_cnt]);  
//debug($data[est_document]);  
//debug($data[est_cover]);  
//debug($data[est_page]);  
//debug($data[est_m_page]);  
//debug($data[est_g_page]);  
//debug($data[est_add_page]);  
//debug($data[est_fix_option]);  
//debug($data[est_after_option]);  
//debug($data);

if($data[est_order_option_desc]) {
	$data[est_order_option_desc] = str_replace("::", ":", $data[est_order_option_desc]);
	$data[est_order_option_desc] = str_replace("|", "/", $data[est_order_option_desc]);	
}

if($data[est_order_option_desc_fix]) {
	$data[est_order_option_desc_fix] = str_replace("::", ":", $data[est_order_option_desc_fix]);
	$data[est_order_option_desc_fix] = str_replace("|", "/", $data[est_order_option_desc_fix]);	
}

//첨부파일 목록
if (strpos($data[storageid], "_temp_") !== FALSE)
{
	
}
else {
	//첨부파일목록불러오기
	$url = $cfg[est_fileinfo_url];
	if($url == "")
		$url = "http://files.ilark.co.kr/portal_upload/estm/file/get_file_list.aspx";

    $returl = $url."?center_id=$cfg_center[center_cid]&storage_code=" .$data[storageid]. "&mall_id=$cid";
	
	//$ret = readurl($returl);
	$ret = readUrlWithcurl($returl, FALSE);

	if (strpos($ret,"html")){
		$ret = "";
	}

	$ret = array_notnull(explode("|",$ret));
	if ($ret[0]!=="fail"){

		foreach ($ret as $key => $val) {
			$pos = strrpos($val, "/"); 
		    if($pos===false) {
		        
		    } else {
		       	$filesData[] = "<div><a href='$val' target='_blank'>". substr($val, $pos+1) ."</a></div>";
		    }
		}
	}			
}

//debug($filesData);

$tpl->assign($data);
$tpl->assign('optCnt', $opt_cnt);
$tpl->assign('optDocument', $opt_document);
$tpl->assign('coverArr', $coverArr);
$tpl->assign('pageArr', $pageArr);
$tpl->assign('aPageArr', $aPageArr);
$tpl->assign('mPageArr', $mPageArr);
$tpl->assign('gPageArr', $gPageArr);
$tpl->assign('optArr', $optArr);
$tpl->assign('afterArr', $afterArr);
$tpl->assign('files', $filesData);

$tpl->print_('tpl');
?>