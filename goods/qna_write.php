<?

include "../_header.php";

$m_board = new M_board();
$m_goods = new M_goods();

$rurl = "/goods/view.php?catno=".$_GET[catno]."&goodsno=".$_GET[goodsno];
if(!$sess[mid]) {
	echo "<script>alert('"._("로그인 후 이용이 가능하십니다.")."');</script>";
	echo "<script>this.close();opener.location='../member/login.php?rurl=$rurl';</script>";
}

$data2 = $m_goods->getInfo($_GET[goodsno]);
$goodsnm = $data2[goodsnm];

if (!$_GET[no]) {
	$mode = "addQna";
} else {
	$mode = "modQna";
	
	$addWhere = "where id = 'qna' and no = '$_GET[no]'";
	$data = $m_board->getMycsInfo($cid, $addWhere);
	
	$checked[secret][$data[secret]] = "checked";
	
	$tpl->assign($data);
}

$tpl->print_('tpl');

?>