<?
/*
* @date : 20180704
* @author : chunter
* @brief : 오아시스 연동. 주문 내역 전송.
* @desc : 2018년 자동견적 상품 전용
*/
?>
<?
include "../lib/library.php";
include "../print/lib_print.php";
//include_once dirname(__FILE__)."/../print/lib_const_print.php";     //옵션 항목 설정 및 가격설정
//header("Content-type: text/xml; charset=utf-8");

if (!trim($_REQUEST[start_order_date]))
{
    $_REQUEST[start_order_date] = date("Y-m-d");
}
else 
{
	//date 형태로 만들기
	$start_order_date = $_REQUEST[start_order_date];
	if (strlen($start_order_date) == 8) 
	{
	    $start_order_date = substr($start_order_date, 0, 4) . "-" . substr($start_order_date,4, 2) . "-" . substr($start_order_date,6, 2);
		$_REQUEST[start_order_date] = $start_order_date;
	}
}

if (!trim($_REQUEST[end_order_date]))
{
    $_REQUEST[end_order_date] = date("Y-m-d");
}
else 
{
	//date 형태로 만들기
	$end_order_date = $_REQUEST[end_order_date];
	if (strlen($end_order_date) == 8)
	{
		$end_order_date = substr($end_order_date, 0, 4) . "-" . substr($end_order_date,4, 2) . "-" . substr($end_order_date,6, 2);
		$_REQUEST[end_order_date] = $end_order_date;
	}
}

//자동견적 상품만 주문리스트 만든다.
$query = "select 
sql_calc_found_rows a.*, a.payprice as order_payprice, b.*, c.goodsnm, c.extra_option,c.print_goods_group, d.order_shiptype from exm_pay a, exm_ord_item b, exm_goods c, exm_ord d
where a.payno = b.payno
and a.payno = d.payno
and b.goodsno = c.goodsno 
and a.paystep = '2'
and b.itemstep = '2' 
and b.est_order_type = 'UPLOAD'

and c.pods_use = '0'
and c.podskind = '0'
and c.extra_option != ''
and (b.oasis_action_d_state = 'r' or b.oasis_action_d_state = '')

and orddt > '$_REQUEST[start_order_date]' 
and orddt < adddate('$_REQUEST[end_order_date]',interval 1 day) 
order by orddt desc";
$listData = $db->listArray($query);
//debug($query);
/*
foreach ($listData as $key => $value) {
	debug($value[upload_info2]);
	$option_json = json_decode($value[upload_info2], TRUE);
	debug($option_json);
}
exit;
*/
//debug($listData);
$adminListData = getAdminData();
for ($i=0; $i < count($listData); $i++) {
//foreach ($listData as $key => $data) {
	$data = $listData[$i];
	$data['admin_list'] = $adminListData;				//관리자 이름을 알기위한 구조체
	
	$PRODUCT_ID = $data['goodsno'];
	$ORDER_CODE = $data['payno'];
	
	$ORDER_PRODUCT_CODE = $data['storageid'];
	$ORDER_COST = $data['order_payprice'];
	$ORDER_CNT = $data['ea'];
		
	$ORDER_DATE = date("Ymd", strtotime($data['orddt']));
	$ORDER_TIME = date("His", strtotime($data['orddt']));
	$ORDER_NAME = $data['orderer_name'];
	$ORDER_EMAIL = $data['orderer_email']; 
	$RECEIVER_NAME = $data['receiver_name'];
	$RECEIVER_POST = $data['receiver_zipcode'];
	$RECEIVER_ADDR1 = $data['receiver_addr'];
	$RECEIVER_ADDR2 = $data['receiver_addr_sub'];
	$RECEIVER_TEL = $data['receiver_phone'];
	$RECEIVER_HP = $data['receiver_mobile'];
	$RECEIVER_MSG = $data['request'];
	$MAKE_MEMO = $data['request2'];
	$PRODUCT_NAME = $data['goodsnm'];
	$PRODUCT_CODE = $data['goodsno'];	
	
	$DELIVERY_KIND = $r_shiptype_pods_deliveryKind[$data['order_shiptype']];
	
    if (is_null($data['est_order_data'])) $data['est_order_data'] = "";
    
	$order_option_json = $data['est_order_data'];				//자동견적 주문 옵션.
	//debug($order_option_json);
	if ($order_option_json)
	{
  	$option_json = json_decode($order_option_json, TRUE);
		//debug($option_json);
	}
	
//debug("ORDER_CODE : ".$ORDER_CODE.", m_site_order_code : ".$m_site_order_code.", RECEIVER_ADDR1 : ".$RECEIVER_ADDR1.", m_sLastDeliveryAddress : ".$m_sLastDeliveryAddress);
    //주문번호가 바뀌거나 주소가 바뀔때 새로운 주문을 생성한다.
	if ($ORDER_CODE != $m_site_order_code || $RECEIVER_ADDR1 != $m_sLastDeliveryAddress)
  {
		//debug("open Order");
		$ret .= "\t<Order order_code=\"". $ORDER_CODE ."\" order_date=\"". $ORDER_DATE ."\" order_time=\"". $ORDER_TIME ."\" order_price=\"". $ORDER_COST ."\" orderer_name=\"". $ORDER_NAME ."\" orderer_phone=\"". $ORDER_HP ."\" orderer_email=\"". $ORDER_EMAIL ."\" settlement_price=\"". $ORDER_COST ."\" delivery_kind_type=\"". $DELIVERY_KIND ."\">\n";
	}

	$ret .= "\t\t<Product>\n";
	$ret .= "\t\t\t<product_code><![CDATA[" . $PRODUCT_CODE . "]]></product_code>\n";
	$ret .= "\t\t\t<product_name><![CDATA[" . $PRODUCT_NAME . "]]></product_name>\n";

	//옵션 데이타 만들기
	$PRODUCT_OPTION = $ORDER_NAME . ",";
	foreach ($option_json as $key => $val) {
	//debug($key);
	//debug($val);
		if ($key != "size_x" && $key != "size_y" && $key != "cover_size_x" && $key != "cover_size_y" && $key != "product_type" && $key != "page_key" && $key != "product_side")
		{
    	if ($val != "")
			{
				if ($r_ipro_print_code[$val])
      		$PRODUCT_OPTION .= $r_ipro_print_code[$val] . ", ";
				else if ($r_ipro_paper[$val]['name'])
					$PRODUCT_OPTION .= $r_ipro_paper[$val]['name'] . ", ";
				else if ($r_ipro_print_sub_item[$val])
					$PRODUCT_OPTION .= $r_ipro_print_sub_item[$val] . ", ";
				else
					$PRODUCT_OPTION .= $val . ", ";
			}
		}		
	}

  $ret .= "\t\t\t<product_option_desc><![CDATA[" . $PRODUCT_OPTION . "]]></product_option_desc>\n";
  $ret .= "\t\t\t<order_product_code><![CDATA[" . $ORDER_PRODUCT_CODE . "]]></order_product_code>\n";
  $ret .= "\t\t\t<order_product_count><![CDATA[" . $ORDER_CNT . "]]></order_product_count>\n";

  $ret .= "\t\t\t<order_work_date><![CDATA[" . $ORDER_DATE . "]]></order_work_date>\n";
  $ret .= "\t\t\t<print_type><![CDATA[]]></print_type>\n";
  $ret .= "\t\t\t<order_type><![CDATA[]]></order_type>\n";
  $ret .= "\t\t\t<recipient_name><![CDATA[" . $RECEIVER_NAME . "]]></recipient_name>\n";
  $ret .= "\t\t\t<recipient_post><![CDATA[" . $RECEIVER_POST . "]]></recipient_post>\n";
  $ret .= "\t\t\t<recipient_address1><![CDATA[" . $RECEIVER_ADDR1 . "]]></recipient_address1>\n";
  $ret .= "\t\t\t<recipient_address2><![CDATA[" . $RECEIVER_ADDR2 . "]]></recipient_address2>\n";
  $ret .= "\t\t\t<recipient_telephone><![CDATA[" . $RECEIVER_TEL . "]]></recipient_telephone>\n";
  $ret .= "\t\t\t<recipient_cellphone><![CDATA[" . $RECEIVER_HP . "]]></recipient_cellphone>\n";
  $ret .= "\t\t\t<delivery_note><![CDATA[" . $RECEIVER_MSG . "]]></delivery_note>\n";
  $ret .= "\t\t\t<make_as><![CDATA[]]></make_as>\n";
  $ret .= "\t\t\t<make_as_msg><![CDATA[]]></make_as_msg>\n";
  $ret .= "\t\t\t<make_sample><![CDATA[]]></make_sample>\n";
  $ret .= "\t\t\t<make_note><![CDATA[" . $MAKE_MEMO . "]]></make_note>\n";
  $ret .= "\t\t\t<admin_note><![CDATA[]]></admin_note>\n";
  $ret .= "\t\t\t<check_type><![CDATA[03]]></check_type>\n";				//자동견적 상품인 경우 03 고정. 다운로드 상품.
  $ret .= "\t\t\t<delivery_kind><![CDATA[" . $DELIVERY_KIND . "]]></delivery_kind>\n";

 	//주문 옵션 정보 연동 (모든 옵션을 오아시스 마스터 옵션으로 처리)				20180710
	$ret .= MasterProductOption($data);  

  $ret .= "\t\t</Product>\n";

  $m_site_order_code = $ORDER_CODE;

  if ($i < count($listData))
  {
  	if ($i + 1 == count($listData))
    {

    }
    else
    {
    	$dr = $listData[$i + 1];      
      //다음 주문과 배송지가 다른 경우도 신규 주문 처리 해야함. 
			if ($m_site_order_code != $dr["payno"] || $RECEIVER_ADDR1 != $dr["receiver_addr"]) 
			{
				$ret .= "\t</Order>\n";
			}   
		}
  }

  //배송지별 주문을 나누기 위해 
  $m_sLastDeliveryAddress = $RECEIVER_ADDR1;
}

if (count($listData) > 0)
	$ret .= "\t</Order>\n";

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<Photo version=\"1.0\">\n";
echo $ret;
echo "</Photo>\n";
exit;

function return_fail_xml($data){
	$result = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	$result .= "<result>\n";
	if ($data[success] == FALSE)
	{
		$result .= "\t<response_id response_id=\"fail\">$data[error]</response_id>";
	}
	$result .= "</result>\n";
	
	return $result;	
}



//{"opt_size":"B5","outside_wing":"WI1","outside_wing_width":"","outside_page":"4","outside_paper_group":"고급지","outside_paper":"PLU24","outside_paper_gram":"190","jump":"#","est_title":"단행본 책자 제목입니다.",
//"cut_width":"","cut_height":"","cnt":"1",
//"outside_print1":"C","outside_print2":"D","opt_bind":"BD3","opt_bind_type":"BT2","outside_gloss":"CT4","opt_gloss":"CT3","opt_domoo":"DM2","opt_sc":"SC2","outside_print3_check":"Y","opt_outside_gloss_check":"Y","opt_gloss_check":"Y","opt_domoo_check":"Y","opt_sc_check":"Y","opt_scb_check":"Y",
//"outside_print3":"ET1,ET3","opt_scb":"SB1,SB2,SB3,SB4,SB5,SB6,SB7",
//"inside_page_0":"200","inside_paper_group_0":"일반지","inside_paper_0":"PNM11","inside_paper_gram_0":"250","inside_print1_0":"C","inside_print2_0":"D",
//"inside_print3_check_0":"Y","inside_print3_0":"ET2","inpage_page_0":"50",
//"inpage_paper_group_0":"일반지","inpage_paper_0":"PNM01","inpage_paper_gram_0":"100","inpage_print1_0":"B","inpage_print2_0":"O","est_order_memo":"단행본 책자 작업메모 입니다.","order_option_desc":"[제목:단행본 책자 제목입니다.;메모:단행본 책자 작업메모 입니다.;][규격:B5;1건;][표지::표지날개:날개없음;페이지수:4;표지용지:고급지/아르떼 울트라/190g;인쇄컬러:컬러;양면/단면:양면;별색:그린,오렌지;][내지::페이지수:200;내지용지:일반지/아트지/250g;인쇄컬러:컬러;양면/단면:양면;별색:바이올렛;][간지/면지::페이지수:50;간지/면지용지:일반지;/뉴플러스 미색/100g;인쇄컬러:흑백;양면/단면:단면;][후가공::제본(책자):중철;제본방향(책자):세로;코팅(표지):유광양면;코팅(내지):무광양면;도무송:있음;스코딕스:스코딕스(부분UV);스코딕스 박:금박,은박,청박,적박,녹박,먹박,홀로그램박;]","est_inside_cnt":2,"est_inpage_cnt":1,"est_supply_price":"106101.5","est_price":"116700.5"}



function MasterProductOption($data) 
{
	global $db, $cid, $_r_inpro_print_goods_kind, $r_ipro_paper, $r_ipro_print_sub_item, $r_paymethod, $_r_inpro_print_goods_group, $_r_inpro_print_direction, $r_ipro_standard_size;
	
	 
	$result = "";
	$order_option_json = $data["est_order_data"];				//자동견적 주문 옵션.
	//debug($order_option_json);
	if ($order_option_json)
	{
  	$json = json_decode($order_option_json, TRUE);
		//debug($option_json);
	}
	
	$query = "select a.catnm from exm_category a, exm_category_link b where a.cid='$cid' and b.goodsno='{$data[goodsno]}' and a.catno = b.catno and LENGTH(a.catno) > 3";
	//debug($query);
	list($category_name) = $db->Fetch($query, 1);
	
	$query = "select cust_name from exm_member where cid='$cid' and mid='{$data[mid]}'";
	//debug($query);
	list($data[order_co_name]) = $db->Fetch($query, 1);  
	//$data[order_co_name] = "";
	  
	$moSb = "";
		
	//interpro_print_option			//작업지시서 데이타
	//router_cover_option
	//router_page_option1
	//router_page_option2 
	//router_page_option3 
	
			
	//, , , , , 작업사이즈, 작업메모
			
 	//debug($data);
	//exit;
	$interpro_print_option[goods_type] = $_r_inpro_print_goods_kind[$data[extra_option]];				//상품 종류 (낱장, 책자, 옵셋 구분 코드)		
	$interpro_print_option[pay_method] = $r_paymethod[$data[paymethod]];				//결제방식,  
	$interpro_print_option[est_title] = $json[est_title];				//주문명,
	
	
	if ($data['est_goodsnm'] == "DIRECTUPLOAD")
		$interpro_print_option[file_worker_name] = '사용자';				//작업자,
	else
		$interpro_print_option[file_worker_name] = $data['admin_list'][$data[proc_admin_id]];				//작업자,
	
	$interpro_print_option[category_name] = $category_name;			//상품카테고리,
	$interpro_print_option[cnt] = $json[cnt];										//건수,		
	$interpro_print_option[shipprice] = $data[shipprice];				//배송비,
			
	$interpro_print_option[cut_size] = $json[cut_width]."x".$json[cut_height];				//재단 사이즈,
	
	$interpro_print_option[order_co_name] = $data[order_co_name];				//업체명,
	$interpro_print_option[order_deadline] = $data['orddt'];				//납기기한,			//일단 테스트로 주문일시를 넘긴다.
		
	
	//낱장 상품의 경우.
	if ($data[extra_option] == "DG01" || $data[extra_option] == "DG02" || $data[extra_option] == "DG03" || $data[extra_option] == "DG04" || $data[extra_option] == "DG06" || $data[extra_option] == "OS01")
	{
		$interpro_print_option[opt_paper] = $r_ipro_paper[$json[opt_paper]]['name'];				//용지,
		$interpro_print_option[opt_print_color] = $r_ipro_print_sub_item[$json[opt_print1]];				//인쇄컬러,
		$interpro_print_option[opt_print_side] = $r_ipro_print_sub_item[$json[opt_print2]];				//양/단면,
		$interpro_print_option[opt_page] = $r_ipro_print_sub_item[$json[opt_page]];				//페이지수,
		 
		//별색 처리. 
		$opt_print3 = explode(",", $json[opt_print3]);
		if (is_array($opt_print3))
		{
			foreach ($opt_print3 as $key => $value) {
				$interpro_print_option[opt_print_color] .= ",".$r_ipro_print_sub_item[$value];			//별색.
			}
		} else {
			if ($opt_print3)
				$interpro_print_option[opt_print_color] .= ",".$r_ipro_print_sub_item[$opt_print3];			//별색.
		}
			
		$opt_print_etc_4 = "";
		if (strpos($json[opt_print3], "ET4") !== FALSE)
			$opt_print_etc_4 = "화이트";				//화이트 별색,
		
		//옵셋과 디지털의 출력종이 규격을 찾는 방법이 다르다.
		if ($data[extra_option] == "OS01")
			$print_paper_key = getOpsetPaperSize($json[opt_size]);
		else if ($data[extra_option] == "DG02" || $data[extra_option] == "DG04")
			$print_paper_key = "A3";			//스티커는 무존건 A3 종이 인쇄.		일반인쇄 스티커도 A3로 출려해야 하는가????
		else
			$print_paper_key = getPrintPaperKeyDigital($json[opt_paper], $json[opt_paper_gram]);
		$page_fit_tag = getFitCountInPaper($print_paper_key, $json[work_width], $json[work_height])."up";
		
		
		$fileListCnt = 1;	
		//파일 갯수를 읽어와서 갯수만큼 처리
		if ($data[est_goodsnm] == "FILEUPLOAD")
		{			
			$query = "select count(*) cnt from md_print_upload_file where storageid='{$data[storageid]}';";
			list($fileListCnt) = $db->fetch($query, 1);
		} else if ($data[est_goodsnm] == "DIRECTUPLOAD") {
			$query = "select count(*) cnt from exm_ord_upload_file where upload_order_product_code='{$data[storageid]}';";
			list($fileListCnt) = $db->fetch($query, 1);
		}
		
		for ($i=0; $i < $fileListCnt; $i++) { 
			$router_page_option[]	= $_r_inpro_print_goods_group[$data['print_goods_group']] ."_".$page_fit_tag."_".$r_ipro_print_sub_item[$json['opt_print1']]."_".$opt_print_etc_4."_".$r_ipro_print_sub_item[$json['opt_print2']];	
		}
			
	
	} else if ($data[extra_option] == "DG05" || $data[extra_option] == "OS02") {
		//책자의 경우.		
		
		//커버 속성 찾기
		$interpro_print_option[opt_cover_paper] = $r_ipro_paper[$json[outside_paper]]['name'];				//용지,
		
		if ($json[outside_print1] == "C" || $json[outside_print4] == "C")
			$interpro_print_option[opt_cover_print_color] = $r_ipro_print_sub_item["C"];				//인쇄컬러,
		else
			$interpro_print_option[opt_cover_print_color] = $r_ipro_print_sub_item["B"];				//인쇄컬러,
		
		if ($json[outside_print4])
			$interpro_print_option[opt_cover_print_side] = $r_ipro_print_sub_item["D"];				//양/단면,
		else
			$interpro_print_option[opt_cover_print_side] = $r_ipro_print_sub_item["O"];				//양/단면,
		$interpro_print_option[opt_cover_pape] = "1";
		
		
		//옵셋과 디지털의 출력종이 규격을 찾는 방법이 다르다.		
		if ($data[extra_option] == "OS02")
			$print_paper_key = getOpsetPaperSize($json[real_cover_size]);
		else 
			$print_paper_key = getPrintPaperKeyDigital($json[outside_paper], $json[outside_paper_gram]);
		//표지는 실제 출력시 펼침면 이니까 실제출력 표지 규격의 크기로 계산해야 한다.
		//debug($r_ipro_standard_size[$print_paper_key]);
		//debug($r_ipro_standard_size[$print_paper_key]['size_x'] ."<BR>");
		//debug($r_ipro_standard_size[$print_paper_key]['size_y']);
		$cover_fit_tag = getFitCountInPaper($print_paper_key, $r_ipro_standard_size[$print_paper_key][size_x], $r_ipro_standard_size[$print_paper_key][size_y])."up";
		$router_cover_option = $_r_inpro_print_direction[$data[oasis_router_print_direction]] ."_".$_r_inpro_print_goods_group[$data[print_goods_group]] ."_".$cover_fit_tag."_".$interpro_print_option[opt_cover_print_color]."_".$interpro_print_option[opt_cover_print_side];
		
		//debug($json);
		//내지와 간지 속성 찾기.		
		for ($i=0; $i < 100; $i++) {
			$index_key1 = 'inside_print1_' .$i;			 
			$index_key2 = 'inside_print2_' .$i;
			$index_key3 = 'inside_paper_' .$i;
			$index_key4 = 'inside_pape_' .$i;
			$index_key5 = 'inside_print3_' .$i;
			$index_key6 = 'inside_paper_gram_' .$i;
							
			if ($json[$index_key1])
			{						
				$interpro_print_option['opt_'.$index_key3] = $r_ipro_paper[$json[$index_key3]]['name'];				//용지,
				$interpro_print_option['opt_'.$index_key1] = $r_ipro_print_sub_item[$json[$index_key1]];				//인쇄컬러,
				$interpro_print_option['opt_'.$index_key2] = $r_ipro_print_sub_item[$json[$index_key2]];				//양/단면,
				$interpro_print_option['opt_'.$index_key4] = $json[$index_key4];				//페이지,
				
				
				//별색 처리. 
				$opt_print3 = explode(",", $json[$index_key5]);
				if (is_array($opt_print3))
				{
					foreach ($opt_print3 as $key => $value) {
						$interpro_print_option['opt_'.$index_key1] .= ",".$r_ipro_print_sub_item[$value];			//별색.
					}
				} else {
					if ($opt_print3)
						$interpro_print_option['opt_'.$index_key1] .= ",".$r_ipro_print_sub_item[$opt_print3];			//별색.
				}
		
			
				//옵셋과 디지털의 출력종이 규격을 찾는 방법이 다르다.		
				if ($data[extra_option] == "OS02")
					$print_paper_key = getOpsetPaperSize($json[opt_size]);
				else 
					$print_paper_key = getPrintPaperKeyDigital($json[$index_key3], $json[$index_key6]);
				//debug($print_paper_key);			
				$page_fit_tag = getFitCountInPaper($print_paper_key, $json[work_width], $json[work_height]) ."up";					
				$router_page_option[]	= $_r_inpro_print_direction[$data[oasis_router_print_direction]] ."_".$_r_inpro_print_goods_group[$data[print_goods_group]] ."_".$page_fit_tag."_".$r_ipro_print_sub_item[$json[$index_key1]]."_".$r_ipro_print_sub_item[$json[$index_key2]];	
			} else 
				break;
		}
		
		for ($i=0; $i < 100; $i++) {
			$index_key1 = 'inpage_print1_' .$i;			 
			$index_key2 = 'inpage_print2_' .$i;
			$index_key3 = 'inpage_paper_' .$i;
			$index_key4 = 'inpage_pape_' .$i;
			$index_key5 = 'inpage_print3_' .$i;
			$index_key6 = 'inpage_paper_gram_' .$i;
			
			if ($json[$index_key1])
			{
				$interpro_print_option['opt_'.$index_key3] = $r_ipro_paper[$json[$index_key3]]['name'];				//용지,
				$interpro_print_option['opt_'.$index_key1] = $r_ipro_print_sub_item[$json[$index_key1]];				//인쇄컬러,
				$interpro_print_option['opt_'.$index_key2] = $r_ipro_print_sub_item[$json[$index_key2]];				//양/단면,
				$interpro_print_option['opt_'.$index_key4] = $json[$index_key4];				//페이지					
				
				//별색 처리. 
				$opt_print3 = explode(",", $json[$index_key5]);
				if (is_array($opt_print3))
				{
					foreach ($opt_print3 as $key => $value) {
						$interpro_print_option['opt_'.$index_key1] .= ",".$r_ipro_print_sub_item[$value];			//별색.
					}
				} else {
					if ($opt_print3)
						$interpro_print_option['opt_'.$index_key1] .= ",".$r_ipro_print_sub_item[$opt_print3];			//별색.
				}
				
				//옵셋과 디지털의 출력종이 규격을 찾는 방법이 다르다.		
				if ($data[extra_option] == "OS02")
					$print_paper_key = getOpsetPaperSize($json[opt_size]);
				else 
					$print_paper_key = getPrintPaperKeyDigital($json[$index_key3], $json[$index_key6]);
				$page_fit_tag = getFitCountInPaper($print_paper_key, $json[work_width], $json[work_height]) ."up";					
				$router_page_option[]	= $_r_inpro_print_direction[$data[oasis_router_print_direction]] ."_".$_r_inpro_print_goods_group[$data[print_goods_group]] ."_".$page_fit_tag."_".$r_ipro_print_sub_item[$json[$index_key1]]."_".$r_ipro_print_sub_item[$json[$index_key2]];	
			} else 
				break;
		}
		
		
		$moSb .= "\t\t\t<master_product_option code=\"router_cover_option\"><![CDATA[$router_cover_option]]></master_product_option>\n";
	}
		
	if (is_array($router_page_option))
	{
		foreach ($router_page_option as $key => $value) 
		{
			$option_key = $key+1;
			$moSb .= "\t\t\t<master_product_option code=\"router_page_option$option_key\"><![CDATA[$value]]></master_product_option>\n";
		}
	}
		
	$interpro_print_option_json = json_encode($interpro_print_option);
	$moSb .= "\t\t\t<master_product_option code=\"interpro_print_option\"><![CDATA[$interpro_print_option_json]]></master_product_option>\n";
	
	$result = $moSb;
	return $result;
}


function getAdminData()
{
	global $cid, $db;
	$sql = "select * from exm_admin where cid='$cid'";
	$arrayList = $db->listArray($sql);
	foreach ($arrayList as $key => $value) {
		$admin_data[$value[mid]] = $value[name];
	}
	
	return $admin_data;
}

?>