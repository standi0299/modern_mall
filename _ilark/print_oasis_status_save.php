<?
/*
* @date : 20180710
* @author : chunter
* @brief : 오아시스 연동. 주문 상태 변경.
* @desc : 
*/
?>
<?
include "../lib/library.php";
header("Content-type: text/xml; charset=utf-8");

$order_product_code = $_REQUEST[order_product_code];
if ($order_product_code == "") $order_product_code = $_REQUEST[product_order_id];

# 1. 주문상품코드 전달 여부 체크
if (!trim($order_product_code)){
	$ret[success] = false;
	$ret[error] = "주문 상품 코드 누락";
	echo return_fail_xml($ret);
	exit;
}

if (!trim($_REQUEST[order_code])){
	$ret[success] = false;
	$ret[error] = "주문 코드 누락";
	echo return_fail_xml($ret);
	exit;
}

$order_code = $_REQUEST[order_code];			//주문번호
$production_state = $_REQUEST[production_state];		//주문상태 코드
$process_state = $_REQUEST[process_state];		//제작공정상태
$update_datetime = $_REQUEST[update_datetime];		//처리일시
$process_update_datetime = $_REQUEST[process_update_datetime];		//공정 처리일시
$express_id = $_REQUEST[express_id];			//택배사 코드
$invoice_number = $_REQUEST[invoice_number];			//운송장번호
$delivery_cnt = $_REQUEST[delivery_cnt];		//배송수량
$delivery_date = $_REQUEST[delivery_date];		//발송일자
$delivery_kind = $_REQUEST[delivery_kind];		//배송방법
$overwrite_flag = $_REQUEST[overwrite_flag];	//배송정보 덮어쓰기 여부

$mesg = $_REQUEST[mesg]; //처리상세정보

$success = "9";
	if ($order_product_code)
	{			
		$itemstep = $r_oasis_to_bluepod_step[$production_state];	
		$data = $db->fetch("select * from exm_ord_item where payno = '$order_code' and storageid = '$order_product_code'");
	
		//주문 상태가 환불완료, 주문취소 상태일때는 오아시스의 공정업데이트에 영향을 받지 않음 / 14.03.07 / kjm
		if ($data[itemstep]!=$itemstep || $overwrite_flag == "Y")
		{
			if ($itemstep == 5)
			{
				//발송 완료 처리.	
				if ($express_id && $invoice_number)
				{
					list($shipcomp) = $db->fetch("select shipno from exm_shipcomp where oasis_num = '$express_id'",1);
					$sql = "update exm_ord_item set shipcomp='$shipcomp', shipcode = '$invoice_number', shipdt = '$delivery_date' 
						where payno = '$order_code' and storageid = '$order_product_code';";
					$db->query($sql);

					$automail_url = "http://".USER_HOST."/_sync/send_automail.php?type=shipping&payno=".$data[payno];
	      	readurl($automail_url);	
					
					$success = "1";
				}	
	      
			} else {
	      //chg_itemstep($data[payno],$data[ordno],$data[ordseq],$data[itemstep],$itemstep,"오아시스 자동처리");
				
				//상태 변경 		//item2는 오아시스 공정상태가 맞는가? 주문상태가 맞는가?
				//$query = "update exm_ord_item set itemstep = '{$r_oasis_to_bluepod_step[$production_state]}', itemstep2 = '{$r_oasis_order_status_step[$production_state]}'
				$query = "update exm_ord_item set itemstep = '$itemstep', itemstep2 = '$process_state'
					where payno = '$order_code' and storageid = '$order_product_code'";
				$db->query($query);	
				
				$success = "1";		
			}
		}	
	}


	if($success == 1)
		$ret = "\t<response_id response_id=\"success\">정상적으로 처리되었습니다.</response_id>";
	else
		$ret = "\t<response_id response_id=\"fail\">DB오류로 처리 실패 하였습니다.</response_id>";	

		
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo "<result>\n";
	echo $ret;
	echo "</result>\n";
	exit;

	function return_fail_xml($data)
	{
    $result = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $result .= "<result>\n";
    if ($data[success] == FALSE)
    {
        $result .= "\t<response_id response_id=\"fail\">$data[error]</response_id>";
    }
    $result .= "</result>\n";
	
		return $result;	
	}
	
?>