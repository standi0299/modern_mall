<?
include "../lib.php";

$m_config = new M_config();

$search_data = $_POST[search][value];
$addWhere = "where cid='$cid'";
if ($search_data) $addWhere .= " and mid like '%$search_data%'";
if ($sess_admin[super] != 1) $addWhere .= " and mid='$sess_admin[mid]'";

$orderby = "order by mid desc";

$limit = "limit $_POST[start], $_POST[length]";

$list = $m_config->getAdminInfo($cid, '', $addWhere, $orderby, $limit);
$list_cnt = $m_config->getAdminInfo($cid, '', $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
    $pdata = array();
//debug($value);
    $pdata[] = $totalCnt-$key;
    $pdata[] = $value[mid];
    $pdata[] = $value[name];
    $pdata[] = $value[regdt];
    $pdata[] = "<a href=\"admin_write_pod.php?mode=modify&mid=$value[mid]\"><button type=\"button\" class=\"btn btn-xs btn-primary\">"._("수정")."</button></a>";
	 
    if ($value[super] == 1) {
        $pdata[] = "관리자";
	} else {   		
   	    $pdata[] = "영업담당자";
    }
    
    if ($sess_admin[super] == 1) {
   		$pdata[] = "<a href=\"indb.php?mode=delete&mid=$value[mid]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\"><span class=\"btn btn-xs btn-danger\">"._("삭제")."</span></a>";
    } else {
        $pdata[] = "";
    }
   
    $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>