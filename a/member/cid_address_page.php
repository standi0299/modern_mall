<?
include "../lib.php";

$m_board = new M_board();

//mid가 없는경우 배송지관리에 해당하는 데이터

$where = "cid = '$cid' and mid = ''";

if($_GET[sword]){
	$where .= " and concat(addressno,addressnm,receiver_name,receiver_addr,receiver_phone,receiver_mobile) like '%$_GET[sword]%'";
}
if($_POST[start] && $_POST[length]){
	$query = "select * from exm_address where $where limit $_POST[start], $_POST[length] ";
	$list = $db->listArray($query);
}else{
	$query = "select * from exm_address where $where ";
	$list = $db->listArray($query);
}

$cnt_query = "select count(*) cnt from exm_address where $where";
$cntRes = $db->fetch($cnt_query);
$totalCnt = $cntRes[cnt];


$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;
if(!$_POST[start]) $_POST[start] = 0;
$start_index = $_POST[start] + 1;

foreach ($list as $key => $value) {
	$pdata = array();
	$pdata[] = $start_index;
	$pdata[] = $value[addressno];
	$pdata[] = $value[addressnm];
	$pdata[] = $value[receiver_name];
	$pdata[] = $value[receiver_addr];
	$pdata[] = $value[receiver_phone];
	$pdata[] = $value[receiver_mobile];
	$pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=\"location.href='cid_address_write.php?addressno=".$value[addressno]."'\">"._("수정")."</button>";
	$pdata[] = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"address_delete('".$value[addressno]."')\">"._("삭제")."</button>";
	$psublist[] = $pdata;

   $start_index++;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>
