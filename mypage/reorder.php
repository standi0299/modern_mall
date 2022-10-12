<?

set_time_limit(0);

include "../lib/library.php";
//include "../lib/lib_util.php";

$m_order = new M_order();

$payno = $_GET[payno];
$storageid = $_GET[storageid];

$data = $m_order->getReOrderList($payno, $storageid);
foreach ($data as $k=>$v) {
	if ($v[pods_use] == "2" || $v[pods_use] == "3") { /* 2.0 상품 */ /* WPOD 복사 추후 rens서버 연동안에 따라 pods2에서 처리함 by kdk */
		$r_storageid20 = trim($v[storageid]);
	} else {
		$r_storageid = trim($v[storageid]);
	}
	
	$goodsno = trim($v[goodsno]);
	$optno = trim($v[optno]);
	$addoptno = trim($v[addoptno]);
	$ea = trim($v[ea]);
}

if ($r_storageid) {
	$client = "http://".PODS10_DOMAIN."/StationWebService/SetReOrder.aspx?storageid=$storageid";
}

if ($r_storageid20) {
	$client = "http://".PODS20_DOMAIN."/CommonRef/StationWebService/SetReOrder.aspx?storageid=$storageid";
}

$ret[SetReOrderResult] = readurl($client);
$ret[SetReOrderResult] = explode("|", $ret[SetReOrderResult]);

if ($ret[SetReOrderResult][0] == "success") {
	$editor_return_json = $m_order->getEditorJsonData($storageid);
	$editor_return_json = str_replace($_GET[storageid], $ret[SetReOrderResult][1], $editor_return_json);
	
	/*$db->query(
	"insert into exm_edit set 
		storageid	= '{$ret[SetReOrderResult][1]}', 
		goodsno		= '$goodsno', 
		optno		= '$optno',
		addoptno	= '$addoptno',
		cid			= '$cid',
		mid			= '$sess[mid]',
		updatedt	= now(),
		state		= 2
	");*/
	//{"exit_code":"1","view":"editor","user_id":"odyssey","userNum":"","site_id":"ibookcover","uploaded_list":[{"rsid":"IB03420131220323100011", "session_param":"editor=3231&storageid=&pid=cover_layout_W-X176&siteid=ibookcover&userid=odyssey&dp=1&minp=1&maxp=1&opt=1&p_siteid=podgroup&dpcnt=1&sessionparam=sub_option%3a%2cparam%3aeyJnb29kc25vIjoiNDgiLCJvcHRubyI6IiIsImFkZG9wdCI6IiJ9%2cpname%3acover_layout_W-X176&adminmode=Y&", "title":"", "order_count":"", "order_option":""}]}
	//$url = "../order/cart_n_order.php?mode=cart&mode2=reorder&goodsno=$goodsno&optno=$optno&addopt=$addoptno&ea=$ea&storageid={$ret[SetReOrderResult][1]}";
	
	$post_data = array(
		"mode" => 'cart', 
		"mode2" => 'reorder',
		"goodsno" => $goodsno,
		"optno" => $optno,
		"addopt" => $addoptno,
		"ea" => $ea,
		"storageid" => $editor_return_json);
		
	//sendPostData('/order/cart_n_order.php', $post_data);
	//redirect_post('/order/cart_n_order.php', $post_data);
	
	//$url = "../order/cart_n_order.php?mode=cart&mode2=reorder&goodsno=$goodsno&optno=$optno&addopt=$addoptno&ea=$ea&storageid=$editor_return_json";
	//msg("주문 저장 후 리스트로 이동합니다",$url);
	
	echo "<form action='/order/cart_n_order.php' method='post' name='frm'>";
	foreach ($post_data as $a=>$b) {
		echo "<input type='hidden' name='".htmlentities($a)."' value='".htmlentities($b)."'>";
	}
	echo "</form>";
	echo "<script language=\"JavaScript\">";
	echo "document.frm.submit();";
	echo "</script>";
} else {
	msg(iconv($ret[SetReOrderResult][1], "euc-kr", "utf8"), $_SERVER[HTTP_REFERER]);
}

?>