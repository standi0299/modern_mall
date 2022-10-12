<?
include_once "../lib/class.page.php";

$db_table = "exm_mycs";
$where_qna[] = "id = 'qna'";
$where_qna[] = "cid = '$cid'";
$where_qna[] = "goodsno = '$_GET[goodsno]'";

$pg_qna = new Page($_GET[page], 5);
$pg_qna->setQuery($db_table,$where_qna,"","order by no desc");
$pg_qna->exec();

$res = &$pg_qna->resource;
while($tmp=$db->fetch($res)){
   $tmp[content] = $tmp[content];
   $tmp[regdt] = substr($tmp[regdt],0,10);
   $qna[] = $tmp;
}

$tpl->assign('pg_qna',$pg_qna);
$tpl->assign('qna',$qna);

$db_table = "exm_review";
$where_review[] = "cid = '$cid'";
$where_review[] = "goodsno = '$_GET[goodsno]'";
$where_review[] = "review_deny_admin = '0'";
$where_review[] = "review_deny_user = '0'";

$pg_review = new Page($_GET[page], 5);
$pg_review->setQuery($db_table,$where_review,"","order by no desc");
$pg_review->exec();

$res = &$pg_review->resource;
while($tmp=$db->fetch($res)){
   $tmp[content] = $tmp[content];
   $tmp[regdt] = substr($tmp[regdt],0,10);
   $review[] = $tmp;
}

$tpl->assign('pg_review',$pg_review);
$tpl->assign('review',$review);

##### 인터프로만 자동견적 옵션 사용 #####
$extra_option = explode('|',$data[extra_option]); //항목 분리
if(count($extra_option) > 0) {
    $data[goodskind] = $extra_option[0];
    $data[preset] = $extra_option[1];
    $extra_price_type = $extra_option[2];
}

if ($data[goods_group_code] == "30" && $category[goods_view] == "view_interpro") { //자동 견적(인쇄) 3.0 && 인터프로만 사용.
   // preset 100114 낱장 기본 프리셋을 사용하여 옵션 구성.
   //debug($category);
   //debug($data);
   
   $extraOption = new ExtraOption();
   $extraOption->SetGoodsKind($data[goodskind]);
   $extraOption->SetPreset($data[preset]);
   
   $optionKindCodeArr = $extraOption->GetOptionKind($_GET[goodsno]); 
   $afterOptionSelectArr = $extraOption->GetAfterOptionData();
   $extraOption->getOrderMemoUse($_GET[goodsno]);
   $extraOption->getExtraTblKindCode();
   $extraOption->getOptionGroupType();
   /*
   debug($optionKindCodeArr);
   debug($afterOptionSelectArr);
   debug($extraOption->DocumentSizeScriptTag);
   
   debug($extraOption->Preset);
   debug($extraOption->GoodsKind);
   debug($extraOption->OrderMemoUse);
   debug($extraOption->OptionUseData);
   debug($extraOption->ExtraTblKindCodeArr);
   debug($extraOption->OptionGroupTypeArr);
   debug($extraOption->PageSizeScriptTag);
   */
   
   $displayNameArrTag = "";
   if($extraOption->OptionUseData) {
       foreach ($extraOption->OptionUseData as $key => $value) {
           if($value == "Y")   
               $displayNameArrTag .= "\"" . $key . '":"' . $extraOption->getDisplayName($key) . '",';
       }
   }
   //debug($displayNameArrTag);
   
   $cntDisplayName = $extraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT");
   
   $javascriptArrayTag = "";
   if($extraOption->ExtraTblKindCodeArr) {
       foreach ($extraOption->ExtraTblKindCodeArr as $key => $value)
         $javascriptArrayTag .= "\"" .$key. "\",";
   }
   //debug($javascriptArrayTag);
   
   $tpl->assign("javascriptArrayTag", $javascriptArrayTag); //옵션 코드 정보 (자바스크립트에서 사용)
   $tpl->assign("DocumentSizeArrTag", $extraOption->DocumentSizeScriptTag); //규격정보  (자바스크립트에서 사용)
   $tpl->assign("OrderMemoArr", $extraOption->OrderMemoUse); //주문제목, 주문메모 사용여부  (자바스크립트에서 사용)
   $tpl->assign("PageSizeArrTag", $extraOption->PageSizeScriptTag); //제본방식에 따른 최소,최대 페이지 정보   (자바스크립트에서 사용)
   //$tpl->assign('adminExtraOption', $extraOption);
   $tpl->assign('extraOption', $extraOption);
   $tpl->assign('afterOptionSelectArr', $afterOptionSelectArr);

}
##### 인터프로만 자동견적 옵션 사용 #####
$m_goods = new M_goods();
$like_cnt = $m_goods->get_goods_like_cnt($cid, $_GET[goodsno]);
$catnm = $m_goods->getCatnm2($cid, $_GET[catno]);
$data[catnm] = $catnm;

//og:image 변수 할당 (layout.htm에서 사용)			chunter
if ($data[img][0])
	$tpl->assign('ogGoodsImage',$data[img][0]);

//일반 상품
$tpl -> define('tpl', "goods/$category[goods_view].htm");

$data[pods_editor_type] = json_decode($data[pods_editor_type],1);

$optOsortData = $m_goods->getOptDataWithOsort($_GET[goodsno], 1);

if ($optOsortData) {
	$data[first_opt_name] = $optOsortData[opt1];
	$data[first_podsno_name] = $optOsortData[podsno];
	$data[first_podoptno_name] = $optOsortData[podoptno];
} else {
	$data[first_podsno_name] = $data[podsno];
	$data[first_podoptno_name] = 1;
}

$data[center_goods_desc] = unserialize($data[center_goods_desc]);
$data[mall_goods_desc] = unserialize($data[mall_goods_desc]);


if($cfg[skin_theme] == "P1"){
   $soapurl = 'http://' .PODS20_DOMAIN;
   $soapurl .= "/CommonRef/StationWebService/GetSiteProductTemplateToJson.aspx?";

   $siteCode = $GLOBALS[cfg][podsiteid];
   $siteProductCode = $data[podsno];

   if($data[r_opt][0][item]) {
      foreach ($data[r_opt][0][item] as $key => $val) {
         if ($val[podoptno]){
            $optionCode = $val[podoptno];
         }
         
         if ($siteProductCode == "" && $val[podsno] != "") $siteProductCode = $val[podsno];

         break;
      }
   }
   else {
      $optionCode = "1";
   }
   
   if(!$optionCode) $optionCode = 1;
   //$siteCode = "ilarktest";

   $param = "siteCode=$siteCode&siteProductCode=$siteProductCode&optionCode=$optionCode";
   //$param = "siteCode=moderndemo&siteProductCode=modernPageBook&optionCode=1";
   
   //templateSetIdx, templateSetIdx 파라미터 추가           20180117    chunter
   //$param .= "&templateSetIdx=$templateset_id&templateIdx=$template_id";
   $ret = readUrlWithcurl($soapurl.$param, false);

   $result[success] = "true";

   if ($ret) {
      //fail.
      if (strpos($ret, "fail|") === false) {
         //$result[previewList] = $ret;
         $obj = json_decode($ret, TRUE);
         //debug($obj);
         $result[previewList] = $obj;
         //debug($obj);
         $frame_cnt = 0;
		 
		 if ($obj) {
	         foreach ($obj as $key => $val) {
	            $frame_cnt += $val[frame_cnt];
	         }
		 }
   
         $result[frameCnt] = $frame_cnt;
      }
      else {
         $result[success] = "false";
         $result[resultMsg] = $ret;
      }
   }

   $page = $result[previewList];
   
   if ($page) {
	   foreach($page as $key => $val){
	      if($val[type] == "epilog"){
	         $loop_epilog[] = $val;
	      } else {
	         $loop[] = $val;
	      }
	   }
   }
   
   if($loop && $loop_epilog)
      $previewList = array_merge($loop, $loop_epilog);
   
   $tpl->assign("previewList",$previewList);
   
   $goods = new Goods();
   $goods->customLimit = 100;
   $goods->getList();
   $tpl->assign("loop",$goods->listData);
   
   $startdate_list = array();
   
   //pixstory에서 전전월,전월,현재월,다음월 순으로 출력해달라고 요청
   if ($data[podskind] == "3060") {
   	   for ($i=-2; $i<10; $i++) {		   
   	   	  //$startdate_list[date("Y-m", strtotime($i." month", time()))] = date("Y년 m월", strtotime($i." month", time()));
		  $startdate_list[date("Y-m", mktime(0,0,0,date("m")+$i,15,date("Y")))] = date("Y년 m월", mktime(0,0,0,date("m")+$i,15,date("Y")));  //20200131 kkwon
   	   }
   }
   // debug($startdate_list);
   if (isset($_GET[slides_margin_left])) {
   	   $slides_margin_left = $_GET[slides_margin_left];
   } else if ($goods->listData) {
	   foreach ($goods->listData as $goods_k=>$goods_v) {
	   	  if ($goods_v[goodsno] == $_GET[goodsno]) $slides_margin_left = -212 * $goods_k;
	   }
	   
	   if (($slides_margin_left <= (-212 * (count($goods->listData) - 5))) && count($goods->listData) >= 5) $slides_margin_left = -212 * (count($goods->listData) - 5);
   } else {
   	   $slides_margin_left = 0;
   }
   
   $tpl->assign("slides_margin_left", $slides_margin_left);
   $tpl->assign("startdate_list", $startdate_list);
   
   $m_goods = new M_goods();
   
   $goods_wish_chk = $m_goods->getCheckWishListGoods($cid, $sess[mid], $_GET[goodsno]);
   //debug($goods_wish_chk);
   $tpl->assign("goods_wish_chk",$goods_wish_chk);
}


$tpl->assign($data);
$tpl->assign("like_cnt",$like_cnt);

$tpl->assign("templateSetIdx",$_GET[templateSetIdx]);
$tpl->assign("templateIdx",$_GET[templateIdx]);
$tpl->assign("url",$_GET[url]);

$tpl->assign("cover_range_data",$cover_range_data);
$tpl->assign("first_cover_id",$first_cover_id);

$tpl->assign("browser",$browser);

$tpl -> print_('tpl');
//debug($tpl);
?>