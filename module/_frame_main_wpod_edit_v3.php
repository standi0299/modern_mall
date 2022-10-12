<?
/*
* @date : 20180515
* @author : kjm
* @brief : wpod ver2, IE에서 wpod(web) 편집기 호출 시 사용
* @request :
* @desc : package_mode,cartno,rurl
* @todo :
*/

include "../lib/library.php";
include "../lib/class.pods.service.php";

if($cfg[member_system][order_system] == "edit_close" && !$sess[mid]){
   msgNpopClose(_("비회원은 편집을 할 수 없습니다.로그인을 해주세요."));
   exit;
}

$pods_use = $_GET[pods_use];
$podskind = $_GET[podskind];
$podsno = $_GET[podsno];
$mode = $_GET[mode];
$goodsno = $_GET[goodsno];
$optno = $_GET[optno];
$addopt = $_GET[addopt];
$impoptno = $_GET[impoptno];
$productid = $_GET[productid];
$optionid = $_GET[optionid];
$ea = $_GET[ea];
$vdp = $_GET[vdp];
$templatesetid = $_GET[templatesetid];
$templateid = $_GET[templateid];
$rurl = $_GET[rurl];
$editdate = $_GET[editdate];
$pod_signed = $_GET[pod_signed];
$editor_type = $_GET[editor_type];
$cover_id = $_GET[cover_id];
$insert_cartno = $_GET[insert_cartno];
$coverrangedata = explode("_", $cover_id);
$coverrangedata = "size$".$coverrangedata[0]."^cover_type$".$coverrangedata[1]."^desc_paper$".$coverrangedata[2];
$coverrangeurl = "http://".$cfg_center[host]."/_ilarksync/get_cover_range_data.php?goodsno=".$goodsno;
//$coverrangeurl = "http://192.168.1.195:9095/_ilarksync/get_cover_range_data.php?goodsno=".$goodsno;
$coverrangeurl = urlencode($coverrangeurl);

$m_goods = new M_goods();

$cover_range_data = $m_goods->getCoverRangeDataWithCoverID($cover_id);
$cover_range_name = $cover_range_data[cover_range]."/".$r_cover_type[$cover_range_data[cover_type]]."/".$cover_range_data[cover_paper_name]."/".$cover_range_data[cover_coating_name];

//debug($_GET);

# pods editor class
$pods = new PodsEditor($goodsno);

//상품에 설정된 pods 정보.
$editor_arr = $pods->GetPodsEditor();
//debug($editor_arr);

if($editor_arr) {
	foreach ($editor_arr as $key => $val) {
		if($val[pods_use] == $pods_use && $val[podskind] == $podskind && $val[podsno] == $podsno) {
			$editor = $val;
		}
	}
}
//debug($editor);

//pods편집기별 호출에 필요한 data 생성.
$info = $pods->GetPodsEditorData($editor);

# goods data check
if (!$info){
    msg(_("상품정보가 없습니다."));
}

//제작옵션(임포지션옵션) 2014.11.27 by kdk
if($_GET[impoptno]) {
	$query = "select * from tb_use_imposition_option where optno = '$_GET[impoptno]'";
	$ret = $db->fetch($query,1);

	if($ret) {
		//imps_option:split$2x1^cover_width$520^cover_height$520^        inside_width$500^inside_height$500^image_fit$imagefull^resize_max$8000^resize_value$3000

		$impoptstr = "imps_u_option:";
		$impoptstr .= "p_w$".$ret[paper_width]."^";
		$impoptstr .= "p_h$".$ret[paper_height]."^";
		$impoptstr .= "m_t$".$ret[margin_top]."^";
		$impoptstr .= "m_b$".$ret[margin_bottom]."^";
		$impoptstr .= "m_l$".$ret[margin_left]."^";
		$impoptstr .= "m_r$".$ret[margin_right]."^";
		$impoptstr .= "i_w$".$ret[item_width]."^";
		$impoptstr .= "i_h$".$ret[item_height]."^";
		$impoptstr .= "i_v_g$".$ret[item_vertical_gap]."^";
		$impoptstr .= "i_h_g$".$ret[item_horizen_gap]."^";
		$impoptstr .= "l_l_g$".$ret[line_left_gap]."^";
		$impoptstr .= "l_t_g$".$ret[line_top_gap]."^";
		$impoptstr .= "l_l$".$ret[line_length]."^";
		$impoptstr .= "l_w$".$ret[line_width];
	}
}

//프로토콜 없을경우 초기화
if (!$cfg[protocol_type]) $cfg[protocol_type] = "http://";

$frameParams = "";
if($editor_type == "web"){
   $frameLink = "$cfg[protocol_type]$g_pods20_domain/CommonRef/Wpod2/WPodEditorFrame.aspx"; //WPodEditorFrame
   $hostUrl = "$cfg[protocol_type]$_SERVER[HTTP_HOST]/module/p20mweb.get.main.frame.php?pods_use=$pods_use&podskind=$podskind&podsno=$podsno&mode=$mode&goodsno=$goodsno&optionid=$info[optionid]&productid=$info[productid]&mid=$sess[mid]&optno=$info[intro_optno]&addopt=$info[intro_addopt]&editor_type=$editor_type&ea=$ea";
}
else {
   $frameLink = "$cfg[protocol_type]pods2.wpod.kr/CommonRef/Wpod/WPodEditorFrame.aspx"; //WPodEditorFrame
   $hostUrl = "$cfg[protocol_type]$_SERVER[HTTP_HOST]/module/p20mweb.get.main.frame.php?pods_use=$pods_use&podskind=$podskind&podsno=$podsno&mode=$mode&goodsno=$goodsno&optionid=$info[optionid]&productid=$info[productid]&mid=$sess[mid]&optno=$info[intro_optno]&addopt=$info[intro_addopt]";
}
//$frameLink = "http://192.168.1.185:80/CommonRef/wpod2/WPodEditorTest.aspx";

//관리자에서 호출 2014.06.16 by kdk
if($_GET[admin_mode] == "Y") {
	$hostUrl = "$cfg[protocol_type]$_SERVER[HTTP_HOST]/module/p20mweb.get.admin.php?pods_use=$pods_use&podskind=$podskind&podsno=$podsno&mode=$mode&goodsno=$goodsno&optionid=$info[optionid]&productid=$info[productid]&mid=$_GET[mid]&optno=$info[intro_optno]&addopt=$info[intro_addopt]&editor_type=$editor_type";
}

### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk
if($pod_signed && $pod_signed <> "") {
	$hostUrl .= "&pod_signed=".$pod_signed;
}

//패키지상품 관련 파라미터 추가 / 20180104 / kdk
if($_GET[package_mode]) {
   $hostUrl .= "&package_mode=".$_GET[package_mode]."&cartno=".$_GET[cartno]."&rurl=".base64_encode(urlencode($_GET[rurl]));
}

$addoptstr = implode($info[addopt], "^");

if($opt_name){
   if($addoptstr) $and = "^";
   $addoptstr .= $and."cover_range_name^".urlencode($cover_range_name);
}

//20140812 / minks / 사이트url이 한글일 경우 고려
$rurl = explode("/", $_GET[rurl]);
$hostCancelUrl = str_replace($rurl[2], $_SERVER[HTTP_HOST], $_GET[rurl]);

$addoptstr = urlencode($addoptstr);

//20140814 / minks / sessionparam 파라미터 추가
$sessionparaminfo = "sub_option:".$addoptstr.",param:".$info[param].",pname:".urlencode($info[pname]);
if($impoptstr) {
	$sessionparaminfo .= ",".$impoptstr;
}
if($insert_cartno){
  $sessionparaminfo .= ",insert_cartno:".$insert_cartno;
}

//test.
//$frameLink = "http://192.168.1.199:8096/CommonRef/Wpod/WPodEditorFrame.aspx"; //WPodEditorFrame
//debug($frameLink);
//debug($info);
//debug($sessionparaminfo);

//test.
//$info[extradataurl] = "http://" .PODS20_DOMAIN. "/CommonRef/WPod/get_wpod_extra_data.asp?mid=guest";

//debug($info);
?>
<script src="/js/jquery.js"></script>
<script>
   $(document).ready(function()
   {
      var fm = document.wpod_editor_form;
      fm.submit();
   });
</script>

<form name="wpod_editor_form" action="<?=$frameLink?>"  method="POST">

   <input type="hidden" name="storageid" value="<?=$info[storageid]?>">
   <input type="hidden" name="pid" value="<?=$info[podsno]?>">
   <input type="hidden" name="siteid" value="<?=$info[siteid]?>">
   <!--   <input type="hidden" name="p_siteid" value="--><?//=$info[podsiteid]?><!--">-->
   <!-- 220315 WPOD p_siteid는 center의 podsiteid 사용(exe편집기와 동일한 형태) -->
   <input type="hidden" name="p_siteid" value="<?=$info[center_podsid]?>">
   <input type="hidden" name="userid" value="<?=$info[userid]?>">
   <input type="hidden" name="dp" value="<?=$info[defaultpage]?>">
   <input type="hidden" name="minp" value="<?=$info[minpage]?>">
   <input type="hidden" name="maxp" value="<?=$info[maxpage]?>">
   <input type="hidden" name="opt" value="<?=$info[optionid]?>">
   <input type="hidden" name="sessionparam" value="<?=$sessionparaminfo?>">
   <input type="hidden" name="dpcnt " value="<?=$info[displaycount]?>">

   <input type="hidden" name="dpopt" value="">

   <input type="hidden" name="dpprice" value="<?=$info[displayprice]?>">
   <input type="hidden" name="requestuser" value="<?=$info[requestuser]?>">

   <input type="hidden" name="editno" value="<?=$info[podskind]?>">

   <input type="hidden" name="adminmode" value="<?=$info[adminmode]?>">
   <input type="hidden" name="templatesetid" value="<?=$info[templatesetid]?>">
   <input type="hidden" name="templateid" value="<?=$info[templateid]?>">

   <input type="hidden" name="macroxmlurl" value="<?=$info[macroxmlurl]?>">
   <input type="hidden" name="printoptnm" value="">

   <input type="hidden" name="coverrangeid" value="<?=$cover_id?>">
   <input type="hidden" name="coverrangeurl" value="<?=$coverrangeurl?>">
   <input type="hidden" name="coverrangedata" value="<?=$coverrangedata?>">

   <input type="hidden" name="startdate" value="">

   <input type="hidden" name="hosturl" value="<?=$hostUrl?>">
   <input type="hidden" name="hostcancelurl" value="<?=$hostCancelUrl?>">

   <!--
   <input type="hidden" name="document_domain" value="<?=$cfg[service_domain]?>">
  	<input type="hidden" name="productid" value="<?=$info[productid]?>">
   <input type="hidden" name="product_siteid" value="<?=$info[center_podsid]?>">
   <input type="hidden" name="addoptstr" value="<?=$addoptstr?>">
   <input type="hidden" name="param" value="<?=$info[param]?>">
   <input type="hidden" name="pname" value="<?=$info[pname]?>">
   <input type="hidden" name="introurl" value="<?=$info[introurl]?>">
   <input type="hidden" name="logourl" value="<?=$info[logourl]?>">
   <input type="hidden" name="displayproductname" value="<?=$info[pname]?>">
   <input type="hidden" name="displaycount" value="<?=$info[displaycount]?>">
   <input type="hidden" name="extradataurl" value="<?=$info[extradataurl]?>">
   <input type="hidden" name="eventurl" value="<?=$info[eventurl]?>">
   <input type="hidden" name="vdp" value="<?=$info[vdp]?>">
   <input type="hidden" name="protocol_type" value="<?=$cfg[protocol_type]?>">
   -->
</form>
