<?
/*
* @date : 20180704
* @author : chunter
* @brief : 오아시스 연동. 주문 접수 완료.
* @desc : 
*/
?>
<?
include "../lib/library.php";
header("Content-type: text/xml; charset=utf-8");

if (!$_REQUEST[order_code]){
	unset($ret);
	$ret[success] = false;
	$ret[error] = "관련주문 없음";
	echo return_fail_xml($ret);
	exit;
}

# 1. 주문상품코드 전달 여부 체크
if (!trim($_REQUEST[order_product_code])){
	$ret[success] = false;
	$ret[error] = "주문 상품 코드 누락";
	echo return_fail_xml($ret);
	exit;
}

if (!trim($_REQUEST[result_id])){
	$ret[success] = false;
	$ret[error] = "필수 정보 누락";
	echo return_fail_xml($ret);
	exit;
}

switch ($_REQUEST[result_id]){
	case "success":
		$oasis_action_d_state = "s";			
		$itemstep2 = "itemstep2='{$r_oasis_order_status_step[004]}',";		//주문접수
		break;
	case "fail":
		$oasis_action_d_state = "f";
		$itemstep2 = "";			
		break;
}

if (!$oasis_action_d_state){
	$ret[success] = false;
	$ret[error] = "필수 정보 누락";
	echo return_fail_xml($ret);
	exit;
}


if ($_REQUEST[order_product_code]){
	$query = "update exm_ord_item set $itemstep2 oasis_action_d_state = '$oasis_action_d_state', oasis_action_d_msg = '$_REQUEST[result_reason]' 
		where payno = '{$_REQUEST[order_code]}' and storageid = '{$_REQUEST[order_product_code]}'";
	$db->query($query);
	$ret = "\t<response_id response_id=\"success\">정상적으로 처리되었습니다.</response_id>";
}


echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<result>\n";
echo $ret;
echo "</result>\n";
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
	
?>