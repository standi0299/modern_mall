<?
include "../lib.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

## 쿠폰등록리스트
$getData = json_decode(base64_decode($_GET[postData]),1);

$getData[coupon_regdt][0] = str_replace("-","",$getData[coupon_regdt][0]);
$getData[coupon_regdt][1] = str_replace("-","",$getData[coupon_regdt][1]);
$getData[coupon_issuedt][0] = str_replace("-","",$getData[coupon_issuedt][0]);
$getData[coupon_issuedt][1] = str_replace("-","",$getData[coupon_issuedt][1]);

if ($getData[coupon_code]) $code = $getData[coupon_code];
if ($getData[coupon_name]) $name = $getData[coupon_name];
if ($getData[coupon_issue_code]) $issue_code = $getData[coupon_issue_code];
if (is_numeric($getData[coupon_issue_yn])) $use = $getData[coupon_issue_yn];

if ($getData[coupon_regdt][0]) $regdt1 = $getData[coupon_regdt][0];
if ($getData[coupon_regdt][1]) $regdt2 = $getData[coupon_regdt][1];
if ($getData[coupon_issuedt][0]) $usedt1 = $getData[coupon_issuedt][0];
if ($getData[coupon_issuedt][1]) $usedt2 = $getData[coupon_issuedt][1];

$limit = " limit $_POST[start], $_POST[length]";

$m_etc = new M_etc();
$list = $m_etc -> getCouponRegistList($cid, $code, $name, $issue_code, $issue_yn, $regdt1, $regdt2, $usedt1, $usedt2, $limit);

$totalCnt = count($m_etc -> getCouponRegistList($cid, $code, $name, $issue_code, $issue_yn, $regdt1, $regdt2, $usedt1, $usedt2, ""));

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
   	$pdata = array();

   	$pdata[] = $value[coupon_code];
   	$pdata[] = $value[coupon_name];
	$pdata[] = $value[coupon_issue_code];
	$pdata[] = substr($value[coupon_regdt],2,14);
	$pdata[] = ($value[coupon_issuedt])? substr($value[coupon_issuedt],2,14) : "";
   	$pdata[] = ($value[coupon_issue_yn])?_("등록"):_("미등록");
	$pdata[] = $value[mid];
	$pdata[] = "<a href=\"indb.php?mode=del_coupon_issue&coupon_issue_code=$value[coupon_issue_code]\" onclick=\"return confirm('"._("정말로 삭제하시겠습니까?")."')\"><span class=\"btn btn-xs btn-danger\">"._("삭제")."</span></a>";

   	$psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>