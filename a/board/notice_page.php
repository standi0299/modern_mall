<?
include "../lib.php";

$m_pretty = new M_pretty();

$order_column_arr = array("no","subject", "name", "regdt", "hit", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$totalCnt = $m_pretty->getNoticeBoardCount($cid, 'notice');
$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$addQuery = $orderby;
$addQuery .= " limit $_POST[start], $_POST[length]";
$list = $m_pretty->getNoticeBoardList($cid, 'notice', $addQuery);

$start_index = $_POST[start] + 1;
foreach ($list as $key => $value) {
   $change_goods = "<button type=\"button\" class=\"btn btn-xm btn-primary\" onclick=\"location.href='write.php?mode=modify&no=" .$value[no]. "';\">"._("수정")."</button>";

   $pdata = array();

   $pdata[] = $start_index;
   $pdata[] = "<a href='notice.w.php?board_id=notice&mode=view&no=$value[no]'>".$value[subject]."</a>";
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