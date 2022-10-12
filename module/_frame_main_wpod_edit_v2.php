<?

/*
 * @date : 20180528
 * @author : kjm
 * @brief : v3버젼 사용으로 사용하지 않음
 */


/**
 * PODStation Call Editor v2
 * 2016.03.16 by kdk
 * PODStation 편집기 호출 class 와 분리함.
 * 빠른 시일안에 include file 파일에서 처리하는 script부분을 통합할 예정.
 */

/*
 * @date : 20180104 (20180104)
 * @author : kdk
 * @brief : 패키지 상품 관련 파라미터 추가.
 * @request : 
 * @desc : package_mode,cartno,rurl
 * @todo :
 */

include "../lib/library.php";
include "../lib/class.pods.service.php";

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
if($cfg_center[center_cid] == "wpod") {
	$frameLink = "$cfg[protocol_type]pods2.wpod.kr/CommonRef/Wpod/WPodEditor.aspx"; //WPodEditor
  	$hostUrl = "$cfg[protocol_type]$_SERVER[HTTP_HOST]/module/p20mweb.get.main.php?pods_use=$pods_use&podskind=$podskind&podsno=$podsno&mode=$mode&goodsno=$goodsno&optionid=$info[optionid]&productid=$info[productid]&mid=$sess[mid]&optno=$info[intro_optno]&addopt=$info[intro_addopt]&editor_type=$editor_type";
}
else {

    if (in_array($podskind,$r_podskind_wpod_ver2)) //wpod ver2 편집기를 지원하는 편집기.
        $frameLink = "$cfg[protocol_type]$g_pods20_domain/CommonRef/Wpod2/WPodEditorFrame.aspx"; //WPodEditorFrame
    else
        $frameLink = "$cfg[protocol_type]pods2.wpod.kr/CommonRef/Wpod/WPodEditorFrame.aspx"; //WPodEditorFrame

	$hostUrl = "$cfg[protocol_type]$_SERVER[HTTP_HOST]/module/p20mweb.get.main.frame.php?pods_use=$pods_use&podskind=$podskind&podsno=$podsno&mode=$mode&goodsno=$goodsno&optionid=$info[optionid]&productid=$info[productid]&mid=$sess[mid]&optno=$info[intro_optno]&addopt=$info[intro_addopt]&editor_type=$editor_type";
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

//20140812 / minks / 사이트url이 한글일 경우 고려
$rurl = explode("/", $_GET[rurl]);
$hostCancelUrl = str_replace($rurl[2], $_SERVER[HTTP_HOST], $_GET[rurl]);

//20140814 / minks / sessionparam 파라미터 추가
$sessionparaminfo = "sub_option:".$addoptstr.",param:".$info[param].",pname:".$info[pname];
if($impoptstr) {
	$sessionparaminfo .= ",".$impoptstr;
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

<form name="wpod_editor_form" action="<?=$frameLink?>"  method="POST">
    <input type="hidden" name="document_domain" value="<?=$cfg[service_domain]?>">
    <input type="hidden" name="storageid" value="<?=$info[storageid]?>">
  	<input type="hidden" name="productid" value="<?=$info[productid]?>">
    <input type="hidden" name="siteid" value="<?=$info[siteid]?>">
    <input type="hidden" name="product_siteid" value="<?=$info[center_podsid]?>">
    <input type="hidden" name="userid" value="<?=$info[userid]?>">
    <input type="hidden" name="defaultpage" value="<?=$info[defaultpage]?>">
    <input type="hidden" name="minpage" value="10<?=$info[minpage]?>">
    <input type="hidden" name="maxpage" value="10<?=$info[maxpage]?>">
    <input type="hidden" name="options" value="<?=$info[optionid]?>">
    <input type="hidden" name="addoptstr" value="<?=$addoptstr?>">
    <input type="hidden" name="param" value="<?=$info[param]?>">
    <input type="hidden" name="pname" value="<?=$info[pname]?>">
    <input type="hidden" name="introurl" value="<?=$info[introurl]?>">
    <input type="hidden" name="logourl" value="<?=$info[logourl]?>">
    <input type="hidden" name="displayproductname" value="<?=$info[pname]?>">
    <input type="hidden" name="displaycount" value="<?=$info[displaycount]?>">
    <input type="hidden" name="requestuser" value="<?=$info[requestuser]?>">
    <input type="hidden" name="adminmode" value="<?=$info[adminmode]?>">
    <input type="hidden" name="hosturl" value="<?=$hostUrl?>">
    <input type="hidden" name="hostcancelurl" value="<?=$hostCancelUrl?>">
    <input type="hidden" name="templatesetid" value="<?=$info[templatesetid]?>">
    <input type="hidden" name="templateid" value="<?=$info[templateid]?>">
    <input type="hidden" name="extradataurl" value="<?=$info[extradataurl]?>">

    <input type="hidden" name="pid" value="<?=$info[podsno]?>">
    <input type="hidden" name="opt" value="<?=$info[optionid]?>">

    <input type="hidden" name="eventurl" value="<?=$info[eventurl]?>">
   	
    <input type="hidden" name="vdp" value="<?=$info[vdp]?>">
   	
    <input type="hidden" name="protocol_type" value="<?=$cfg[protocol_type]?>">
   	
    <input type="hidden" name="sessionparam" value="<?=$sessionparaminfo?>">
</form> 