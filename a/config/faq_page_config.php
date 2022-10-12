<?
include "../lib.php";

$m_pretty = new M_pretty();

$order_column_arr = array("no", "catnm", "q", "", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$totalCnt = $m_pretty->getFAQCount($cid);
$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$addQuery = $orderby;
$addQuery .= " limit $_POST[start], $_POST[length]";
$list = $m_pretty->getFAQ($cid, $addQuery);


$start_index = $_POST[start] + 1;
foreach ($list as $key => $value) {
   $faq_popup = "<button type=\"button\" class=\"btn btn-sm btn-info\" onclick=\"faq_popup('".$value[no]."');\">"._("수정")."</button>";
   $faq_delete = "<button type=\"button\" class=\"btn btn-sm btn-danger\" onclick=\"faq_delete('".$value[no]."');\">"._("삭제")."</button>";

   $pdata = array();

   $pdata[] = $start_index;
   $pdata[] = $value[catnm];
   $pdata[] = $value[q];

   $pdata[] = $faq_popup;
   $pdata[] = $faq_delete;
   $psublist[] = $pdata;
   
   $start_index++;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>