<?
include "../lib.php";

$m_etc = new M_etc();

$search_data = $_POST[search][value];
if ($search_data) {
   $addwhere .= " and (a.mid like '%$search_data%' or b.title like '%$search_data%')";
}

$order_column_arr = array("", "a.regdt", "a.mid", "b.title", "a.view", "a.like", "", "");
$order_data = $_POST[order][0];
$orderby = " order by ".$order_column_arr[$order_data[column]]." ".$order_data[dir];
$limit = "limit $_POST[start], $_POST[length]";

$list = $m_etc->getGalleyData($cid, '', $addwhere, $orderby, $limit);

$totalCnt = $m_etc->getGalleyDataCnt($cid, '', $addwhere);
$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   $pdata = array();
	
   $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[storageid]|$value[flag]\">";
   $pdata[] = $value[regdt];
   $pdata[] = $value[mid];
   $pdata[] = str_replace("\"", "&quot;", stripslashes($value[title]));
   $pdata[] = $value[view];
   $pdata[] = $value[like];
   
   if ($value[flag] == "best") $flag = _("베스트");
   else $flag = _("공개");
   
   if ($value[main_flag] == "Y") $main_flag = _("노출");
   else $main_flag = _("노출안함");
   
   $pdata[] = $flag;
   $pdata[] = $main_flag;

   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>