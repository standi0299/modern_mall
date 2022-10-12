<?
include "../lib.php";

$m_pretty = new M_pretty();

$getData = json_decode(base64_decode($_GET[postData]),1);

$addQuery = '';
if ($getData[stype] && $getData[sword]) $addQuery .= " and $getData[stype] like '%$getData[sword]%'";
if ($getData[status]) $addQuery .= " and status = '$getData[status]'";
if ($getData[start]) $addQuery .= " and regdt >= '$getData[start] 00:00:00'";
if ($getData[end]) $addQuery .= " and regdt <= '$getData[end] 23:59:59'";
if ($getData[category]) $addQuery .= " and category = '$getData[category]'";

$order_column_arr = array("no", "category", "subject", "name", "regdt", "status", "", "");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$addQuery .= $orderby;
$totalCnt = $m_pretty->getMycsCount($cid, "cs", $addQuery);

$addQuery .= " limit $_POST[start], $_POST[length]";

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$list = $m_pretty->getMycs($cid, "cs", $addQuery);

$start_index = $_POST[start] + 1;
foreach ($list as $key => $value) {
   $cs_answer = "<button type=\"button\" class=\"btn btn-xs btn-primary\" onclick=location.href=\"cs.w.php?no=".$value[no]."\">"._("답변달기")."</button>";
   $cs_delete = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"cs_delete('".$value[no]."');\">"._("삭제")."</button>";
   $cs_subject = "<a href='cs.w.php?no=".$value[no]."'>".$value[subject]."</a>";

   $pdata = array();

   $pdata[] = $start_index;
   $pdata[] = $r_cs_category[$value[category]];
   $pdata[] = stripslashes($cs_subject);

   if($value[mid]!="admin") {
      $pdata[] = $value[name];
   } else if($value[mid]) {
      $pdata[] = $value[name]."<br>(".$value[mid].")";
   }

   $pdata[] = $value[regdt];

   if($value[status]==2) {
      $pdata[] = "<b style=\"color:#28a5f9\">".$r_cs[$value[status]]."</style></b>";
   } else
      $pdata[] = "<b>".$r_cs[$value[status]]."</b>";

   $pdata[] = $cs_answer;
   $pdata[] = $cs_delete;
   $psublist[] = $pdata;
   $start_index++;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>