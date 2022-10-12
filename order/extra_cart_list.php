<?
//견적의뢰리스트
//2015.06.25 by kdk

include_once "../_header.php";
include_once "../lib/class.page.php";

$_GET[orddt][0] = str_replace("-","",$_GET[orddt][0]);
$_GET[orddt][1] = str_replace("-","",$_GET[orddt][1]);

$db_table = "
    tb_extra_cart
";

$where[] = " cid = '$cid'";

if ($_GET[guest] && $_GET[cartno]) { //비회원 주문조회
	$where[] = " (cartno in ($_GET[cartno])) ";
}
else {
	$where[] = " (order_status is null or order_status = 'p') "; //d:삭제, c:취소, o:주문, p:발행
	$where[] = ($sess[mid]) ? " mid = '{$sess[mid]}'" : " cartkey = '$_COOKIE[cartkey]'";
}

$where[] = " goodsno in (select goodsno from exm_goods where extra_auto_pay_flag=0) "; //extra_auto_pay_flag=0 자동견적결제를 사용 안하는 상품만

if ($_GET[orddt][0]) $where[] = "updatedt > {$_GET[orddt][0]}";
if ($_GET[orddt][1]) $where[] = "updatedt < adddate({$_GET[orddt][1]},interval 1 day)+0";
$db->query("set names utf8");
$pg = new Page($_GET[page]);

$pg->setQuery($db_table,$where,"cartno desc");

//debug($pg->query);
//exit;

$pg->exec();

//첨부파일목록불러오기
$url = $cfg[est_fileinfo_url];
if($url == "")
	$url = "http://files.ilark.co.kr/portal_upload/estm/file/get_file_list.aspx";

$xls_query = substr($pg->query,0,strrpos($pg->query," limit"));
$xls_query = base64_encode($xls_query);

$res = &$pg->resource;

$i = 0;
while ($data = $db->fetch($res)){

    if ($data[est_order_option_desc])
    {
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
		
      	$data[est_order_option_desc] = str_replace("::", ":", $data[est_order_option_desc]);
    }

    if ($data[est_order_data])
    {
      $est_order_data_arr = json_decode($data[est_order_data], true);
    }
		
	//debug($est_order_data_arr);
	//exit;
	
	$data[link] = getViewLinkWithTemplate($data[goodsno], $est_order_data_arr[templateSetIdx], $est_order_data_arr[templateIdx]);
	
	//20141110 / minks / 상품명이 없을 경우 상품일련번호로 조회
	if(!$data[goodsnm]) {
		list($data[goodsnm]) = $db->fetch("select goodsnm from exm_goods where goodsno='$data[goodsno]'",1);
	}
	
	//category name 출력 2014.11.21 by kdk
	$q = "select catno, catnm from exm_category where cid='$cid' and catno in (select catno from exm_category_link where cid='$cid' and goodsno='$data[goodsno]')";
	$r = $db->query($q);
	while ($d = $db->fetch($r)){
		switch (strlen($d[catno])){
			case "3":
					$data[catnm1] = $d[catnm];
				break;
			case "6":
					$data[catnm2] = $d[catnm];
				break;
			case "9":
					$data[catnm3] = $d[catnm];
				break;
			case "12":
					$data[catnm4] = $d[catnm];
				break;
		}
	}

	//첨부파일 목록
	if (strpos($data[storageid], "_temp_") !== FALSE)
	{
		
	}
	else {
	    $returl = $url."?center_id=$cfg_center[center_cid]&storage_code=" .$data[storageid]. "&mall_id=$cid";
		//$ret = readurl($returl);
		$ret = readUrlWithcurl($returl, FALSE);
		
		if (strpos($ret,"html")){
			$ret = "";
		}
		
		$ret = array_notnull(explode("|",$ret));
		$num = count($ret);
		
		if ($ret[0]!=="fail"){
				
			$pos = strrpos($ret[0], "/"); 
		    if($pos===false) {
		        
		    } else {
		        $filename = substr($ret[0], $pos+1);
		    }		  		

		  	if($num > 1) {
		  		$num = $num - 1;
		  		$filename .=" ". _("외")." ". $num ._("건");
		  	}

			$data[files] = $filename; 
		}
		//debug($ret);
	}
	
    if (!$loop[$data[cartno]]) $loop[$data[cartno]] = $data;
}

//debug($loop);
//exit;


    	//자동 SMS 발송 $cfg[nameComp]
    	//autoSms(_("접수내역"), "01089273092");
		//autoSms(_("견적신청"), "01089273092");

//편집리스트 목록 개수 추출 / 14.01.08 / kjm
$total_num = $pg -> recode[total];

$tpl_extra_cart_list = "/order/_extra_cart_list.htm";

$tpl->define("extracartlist",$tpl_extra_cart_list);

$tpl->assign('orderlist_button',$orderlist);
$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->assign('total_num',$total_num);
$tpl->assign('member_type',$member_type);
//20140408 / minks / itemstep 파라미터 값 넘김
$tpl->assign('param_item_step',$_GET[itemstep]);
$tpl->assign('orderview_add_link',$orderview_add_link);
$tpl->print_('tpl');

?>