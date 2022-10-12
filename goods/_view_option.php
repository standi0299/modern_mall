<?
//자동 견적(웹 견적)

set_time_limit(360);
ini_set('memory_limit','-1'); //테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk

//자동견적 모드 : detail 고급
if(!$_GET[optmode]) $opt_url = $_SERVER[REQUEST_URI]."&optmode=detail";

$editor_mode = "N";

//상품정보에서 자동견적옵션 조회
//debug($data);
$extra_option = explode('|',$data[extra_option]); //항목 분리
if(count($extra_option) > 0) {
	$data[goodskind] = $extra_option[0];
	$data[preset] = $extra_option[1];
	$extra_price_type = $extra_option[2];
	//$extra_editor = $extra_option[3];
}

$extraOption = new ExtraOption();
$extraOption->SetGoodsKind($data[goodskind]);
$extraOption->SetPreset($data[preset]);

$optionKindCodeArr = $extraOption->GetOptionKind($_GET[goodsno]); 
$afterOptionSelectArr = $extraOption->GetAfterOptionData();
$extraOption->getOrderMemoUse($_GET[goodsno]);
$extraOption->getExtraTblKindCode();
$extraOption->getOptionGroupType();

//debug($optionKindCodeArr);
//debug($afterOptionSelectArr);
//debug($extraOption->DocumentSizeScriptTag);

//debug($extraOption->Preset);
//debug($extraOption->GoodsKind);
//debug($extraOption->OrderMemoUse);
//debug($extraOption->OptionUseData);
//debug($extraOption->ExtraTblKindCodeArr);
//debug($extraOption->OptionGroupTypeArr);

$javascriptArrayTag = "";
if($extraOption->ExtraTblKindCodeArr) {
	foreach ($extraOption->ExtraTblKindCodeArr as $key => $value)
      $javascriptArrayTag .= "\"" .$key. "\",";
}
//debug($javascriptArrayTag);
//debug($extraOption->PageSizeScriptTag);

$tpl->assign("javascriptArrayTag", $javascriptArrayTag); //옵션 코드 정보 (자바스크립트에서 사용)
$tpl->assign("DocumentSizeArrTag", $extraOption->DocumentSizeScriptTag); //규격정보  (자바스크립트에서 사용)
$tpl->assign("OrderMemoArr", $extraOption->OrderMemoUse); //주문제목, 주문메모 사용여부  (자바스크립트에서 사용)
$tpl->assign("PageSizeArrTag", $extraOption->PageSizeScriptTag); //제본방식에 따른 최소,최대 페이지 정보   (자바스크립트에서 사용)

$tpl->define('tpl', 'goods/view_option.htm');

/*if ($extraOption->Preset == "100102") //낱장
	$tpl->define('tpl', 'goods/view_option_100102.htm');
else if ($extraOption->Preset == "100104") //책자
	$tpl->define('tpl', 'goods/view_option_100104.htm');
else if ($extraOption->Preset == "100106") //책자
	$tpl->define('tpl', 'goods/view_option_100106.htm');
else if ($extraOption->Preset == "100108") //책자
	$tpl->define('tpl', 'goods/view_option_100108.htm');
else if ($extraOption->Preset == "100110") //책자 스튜디오 견적 프리셋1
	$tpl->define('tpl', 'goods/view_option_100110.htm');
else if ($extraOption->Preset == "100112") //책자 견적 프리셋3
	$tpl->define('tpl', 'goods/view_option_100112.htm');*/
	
if($data["podskind"]) {
	$data[order_type]="EDITOR";
}
else { 
	$data[order_type]="UPLOAD";
}

//debug(date("Y-m-d H:i:s",time()));  
### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk
$pod_signed = signatureData($cid, $_SERVER[REQUEST_URI]."&date=".date("Y-m-d H:i:s",time()));
//debug($pod_signed);

//회원정보조회
if($sess[mid]) {
	list($order_name,$order_cname,$order_phone,$order_mobile,$order_email,$privacy_flag) = $db->fetch("select name,cust_name,phone,mobile,email,privacy_flag from exm_member where cid='$cid' and mid='$sess[mid]' limit 1",1);	
}

if ((in_array($cfg[skin], $r_printhome_skin))) { //printhome은 header 없음.   

}
else {
	//$tpl->define('header', 'layout/layout_extra_option.htm?header');    //wpod layout 으로 변경할때.  	
}

if($data[img]) {
	foreach ($data[img] as $key => $value) {
	    if(strpos($value, "http://$cfg_center[host]/") === false)
            $imgs .= "http://$cfg_center[host]/data/goods/$cid/l/$goodsno/".$value."||";
	}

    if($imgs) {
	   $data[img] = array_notnull(explode("||",$imgs));
    }
}

if($_GET[templateSetIdx] && $_GET[templateIdx]) {
	//템플릿 전시 리스트에서 견적상품으로 넘어온 경우 2016.03.24 by kdk
	$data[templateSetIdx] = $_GET[templateSetIdx];
	$data[templateIdx] = $_GET[templateIdx];
	$data[img] = $extraOption->GetImgData($_GET[url], $_GET[templateSetIdx], $_GET[templateIdx]);
	//debug($data[img]);
	$template = $extraOption->GetTemplateData($_GET[url], $_GET[templateSetIdx], $_GET[templateIdx]);
	//debug($template);
}

if($_POST[templateSetIdx] && $_POST[templateIdx]) {
    //템플릿 전시 리스트에서 견적상품으로 넘어온 경우 / 20190114 / kdk
    $data[templateSetIdx] = $_POST[templateSetIdx];
    $data[templateIdx] = $_POST[templateIdx];
    $data[templateName] = $_POST[templateName];
    $data[templateURL] = $_POST[templateURL];
}

$editorBtn = "";
$btnLbl = _("내파일주문");
if ($extraOption->Preset == "100102") { //낱장
	$inc_file = "/goods/_view_option_100102.htm";
}
else if ($extraOption->Preset == "100104") { //책자
	$inc_file = "/goods/_view_option_100104.htm";
}
else if ($extraOption->Preset == "100106") { //낱장(책자-프리셋2)
	$inc_file = "/goods/_view_option_100106.htm";
}
else if ($extraOption->Preset == "100108") { //책자
	$inc_file = "/goods/_view_option_100108.htm";
}
else if ($extraOption->Preset == "100110") { //책자 스튜디오 견적 프리셋1
	$inc_file = "/goods/_view_option_100110.htm";
}
else if ($extraOption->Preset == "100112") { //책자 견적 프리셋3
	$inc_file = "/goods/_view_option_100112.htm";
	$editorBtn .= "<a href='javascript:;' onclick='openExtraBillPrint();' class='estimate_btn'><span class='btn_04'></span>"._("견적서보기")."</a>";
	
	if(!$_GET[optmode]) $editorBtn .= "<a href='$opt_url' class='estimate_btn'><span class='btn_05'></span>"._("고급견적")."</a>";
	$btnLbl = _("주문하기");
}
//debug($inc_file);

$estimateClass = "estimateR";
//$data[horizon_view_flag] = "1";
//템플릿리스트+견적상품 중 현수막일 경우 가로보기 처리.
if($data[horizon_view_flag] == "1") {
	$data[img_w] = "50%";
	$estimateClass = "estimateC";
}
else {
	$data[img_w] = $cfg[img_w];
}	
//debug($estimateClass);
//debug($data);
if ($data[podskind] > 0) { //pods 연동 상품이면...
	if($editor) {
		foreach ($editor as $key => $val) {
			$pods_use = $val[pods_use];
			$podskind = $val[podskind];
			$podsno = $val[podsno];
			$templateSetIdx = $template[$podsno][templateSetIdx];
			$templateIdx = $template[$podsno][templateIdx];
			
			if ($val[pods_use] == "3") {
				//pods_use = 3 간편편집 주문하기
				$btnSpan = "<span class=\"btn_01\"></span>"._("간편편집주문");
			}
			else if ($val[pods_use] == "2") {
				//pods_use = 2 고급편집 주문하기
				$btnSpan = "<span class=\"btn_02\"></span>"._("고급편집주문");
			}
			else {
				$btnSpan = "<span class=\"btn_04\"></span>"._("편집하기");
			}
			
			$editorBtn .= "<a href=\"javascript:call_exec('$pods_use', '$podskind', '$podsno', '$templateSetIdx', '$templateIdx');\" class=\"estimate_btn\">$btnSpan</a>";
		}
	}
	//수동업로드 내파일 주문하기
	$editorBtn .= "<a href=\"javascript:;\" onclick=\"fileInfoOpenLayer(this);\" class=\"estimate_btn\"><span class=\"btn_03\"></span>$btnLbl</a>";	
}
else {
	if ($data[order_type] == "UPLOAD") {
		if ($data[extra_auto_pay_flag] == "0") {
			//견적의뢰
			$editorBtn .= "<a href=\"javascript:;\" onclick=\"orderInfoOpenLayer(this);\" class=\"estimate_btn\"><span class=\"btn_05\"></span>"._("견적의뢰")."</a>";
		}
		else {
			//수동업로드 내파일 주문하기
			$editorBtn .= "<a href=\"javascript:;\" onclick=\"fileInfoOpenLayer(this);\" class=\"estimate_btn\"><span class=\"btn_03\"></span>$btnLbl</a>";
		}
	}
	else {
		//바로구매
		$editorBtn .= "<a href=\"javascript:;\" onclick=\"exec('buy');\" class=\"estimate_btn\"><span class=\"btn_05\"></span>"._("바로구매")."</a>";

		//장바구니
		$editorBtn .= "<a href=\"javascript:;\" onclick=\"exec('cart');\" class=\"estimate_btn\"><span class=\"btn_03\"></span>"._("장바구니")."</a>";
	}
}

//debug($editorBtn);
//exit;

//내 파일 가져오기용 업로드 스토리지 아이디.
$micro = explode(" ",microtime());
$storageKey = date("Ymd")."-".substr($micro[1].sprintf("%03d",floor($micro[0]*1000)), -6);
//debug($storageKey);

//견적 두번째 수량 표시 여부 추가 2016.04.14 by kdk
//$data[extra_unitcnt_view_flag] = "1";
if ($extraOption->Preset == "100114") {
    $tpl -> define('tpl', "print/alaskaprint_100114.htm");

    $displayNameArrTag = "";
    if($extraOption->OptionUseData) {
        foreach ($extraOption->OptionUseData as $key => $value) {
            if($value == "Y")   
                $displayNameArrTag .= "\"" . $key . '":"' . $extraOption->getDisplayName($key) . '",';
        }
    }
    //debug($displayNameArrTag);
    
    $cntDisplayName = $extraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT");
    //debug($cntDisplayName);
    //exit;
    
    //템플릿리스트에 편집기가 실행되어 상품상세페이지로 바로 됨어옴.
    if($_GET[editor_type] == "web_list" || $_GET[storageid]) {
        $editor_mode = "Y";
    }
}
else {
    $tpl -> define('tpl', "goods/view_option.htm");
}

//debug($extraOption->MakeSelectOptionTag("1","","forwardAction",""));
//debug($data);
$tpl->assign('adminExtraOption', $extraOption);
$tpl->assign('extraOption', $extraOption);
$tpl->assign('afterOptionSelectArr', $afterOptionSelectArr);
$tpl->assign('order_name', $order_name);
$tpl->assign('order_cname', $order_cname);
$tpl->assign('order_phone', $order_phone);
$tpl->assign('order_mobile', $order_mobile);
$tpl->assign('order_email', $order_email);
$tpl->assign('privacy_flag', $privacy_flag);
$tpl->assign('editor_mode',$editor_mode);
$tpl->assign($data);
$tpl->print_('tpl');
?>