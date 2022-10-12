<?
include "../lib.php";

$m_board = new M_board();


$where = "";
$board_id = "";
if ($_GET[regdt][0]) $where .= "regdt >= '{$_GET[regdt][0]}'";
if ($_GET[regdt][1]) $where .= "regdt < ADDDATE('{$_GET[regdt][1]}',interval 1 day)";
if ($_GET[sword]) $where .= "concat(mid,name,category,subject) like '%$_GET[sword]%'";
if ($_GET[board]) $board_id = $_GET[board];

$order_column_arr = array("no","subject", "name", "regdt", "hit", "");
$order_data = $_POST[order][0];
$orderby = $order_column_arr[$order_data[column]] . " " .$order_data[dir];
$orderby .= " limit $_POST[start], $_POST[length]";

$list = $m_board->getBoardList($cid, $board_id, $where, $orderby);
$totalCnt = $m_board->getBoardCount($cid, $board_id, $where);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$start_index = $_POST[start] + 1;
foreach ($list as $key => $value) {
   $change_goods = "<button type=\"button\" class=\"btn btn-sm btn-info\" onclick=\"location.href='board_write.php?mode=modify&no=" .$value[no]. "';\">"._("수정")."</button>";
		if ($value[notice] == "-1") $notice_tag = "[" ._("알림"). "] ";
		else $notice_tag = "";
		if ($value[secret] == "1") $secret_tag = "[" ._("비밀"). "] ";
		else $secret_tag = "";

   $pdata = array();
   $pdata[] = $start_index;
   $pdata[] = $notice_tag. $secret_tag."<a href='board_write.php?board_id=$value[board_id]&mode=view&no=$value[no]'>".$value[subject]."</a>";
   $pdata[] = $value[name];
   $pdata[] = $value[regdt];
   $pdata[] = $value[hit];

   $pdata[] = $change_goods;
   $psublist[] = $pdata;

   $start_index++;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>