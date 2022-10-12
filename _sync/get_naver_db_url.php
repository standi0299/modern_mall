<?
/*
EP 버전 3.0

네이버지식쇼핑상품EP (Engine Page) 제작및연동가이드 (제휴사제공용)
http://join.shopping.naver.com/misc/download/ep_guide.nhn

EP 생성 규칙 (공통)
1. EP 가이드에맞게생성하신EP는반드시네이버쇼핑과약속된 EP URL에 txt 파일로생성해주셔야하며, http로접근이가능해야합니다
2. EUC-KR,UTF-8 지원하나 확장 한글 밑 특수문자표기를 위해 UTF-8(without BOM : Byte Order Mark포함되지않은 UTF-8) 권장
3. 모든 컬럼값에서는 탭/엔터문자 등의 제어문자 공백문자 프로그래밍언어등은 제외 되어야 합니다.

EP 필드
Field                   	Status  		Notes
id                      	필수    		판매하는 상품의 유니크한 상품 ID
title                   	필수    		실제 서비스에 반영될 상품명(Title)
price_pc                	필수    		상품가격
normal_price				권장			정가
link                    	필수    		상품URL
mobile_link					권장			상품 모바일URL
image_link              	필수    		해당 상품의 이미지URL
add_image_link				권장			추가 이미지URL
category_name1          	필수    		카테고리명(대분류)
category_name2          	권장    		카테고리명(중분류)
category_name3          	권장    		카테고리명(소분류)
category_name4          	권장    		카테고리명(세분류)
naver_category				권장			네이버 카테고리
naver_product_id			권장			가격비교 페이지 ID
condition					해당상품필수	상품상태
import_flag					해당상품필수	해외구매대행 여부
parallel_import				해당상품필수	병행수입 여부
order_made					해당상품필수	주문제작상품 여부
product_flag				해당상품필수	판매방식 구분
adult						해당상품필수	미성년자 구매불가 상품 여부
search_tag					권장			검색태그
group_id					권장			Group ID
vendor_id					권장			제휴사 상품 ID
coordi_id					권장			코디상품 ID
minimum_purchase_quantity	해당상품필수	최소구매수량
shipping                	필수    		배송료
attribute					권장			상품속성
option_detail				권장			구매옵션
age_group					권장			주 이용 고객층
gender						권장			성별
class                   	필수(요약)  	I (신규상품) / U (업데이트 상품) / D (품절상품)
update_time             	필수(요약)  	상품정보 생성 시각
*/

//EP규칙에 따라 txt파일형태 charset 변경
header('Content-type:text/tab-separated-value; charset=utf-8');

include_once "../lib/library.php";
include_once "../lib/template_/tpl_plugin/function.f_get_delivery.php";
$tab = "\t";
$enter = "\n";
$cnt = 0;

ob_start();

echo "id{$tab}title{$tab}price_pc{$tab}normal_price{$tab}link{$tab}mobile_link{$tab}image_link{$tab}add_image_link{$tab}category_name1{$tab}category_name2{$tab}category_name3{$tab}category_name4{$tab}naver_category{$tab}naver_product_id{$tab}condition{$tab}import_flag{$tab}parallel_import{$tab}order_made{$tab}product_flag{$tab}adult{$tab}search_tag{$tab}group_id{$tab}vendor_id{$tab}coordi_id{$tab}minimum_purchase_quantity{$tab}shipping{$tab}attribute{$tab}age_group{$tab}gender{$tab}class{$tab}update_time";

$query = "select
		a.*,b.*,if(b.price is null,a.price,b.price) price,
		if(b.`desc` ='',a.`desc`,b.`desc`) `desc`,
		if(b.mall_pageprice is null,a.pageprice,b.mall_pageprice) pageprice
	from
		exm_goods a
		inner join exm_goods_cid b on b.cid = '$cid' and a.goodsno = b.goodsno";
		
$res = $db->query($query);
while ($data = $db->fetch($res)) {
	//초기화
	$add_image_link = "";
	$r_catno = array();
	$category_name = array();
	
	list($catno) = $db->fetch("select catno from exm_goods_link where cid = '$cid' and goodsno = '$data[goodsno]' order by cat_index limit 1", 1);
	
	//상품 리스트 진열방식 고려
	$link = "http://{$_SERVER[SERVER_NAME]}/goods/view.php?catno={$catno}&goodsno={$data[goodsno]}";
	
	if ($data[cimg]) $data[img] = $data[cimg];
	if ($data[img]) {
		$data[img] = array_notnull(explode("||", $data[img]));
		$image_link = "http://{$cfg_center[host]}/data/goods/{$cid}/l/{$data[goodsno]}/{$data[img][0]}";
		
		foreach ($data[img] as $k=>$v) {
			if ($k > 0) $add_image_link[] = "http://{$cfg_center[host]}/data/goods/{$cid}/l/{$data[goodsno]}/{$v}";
		}
		
		if ($add_image_link) $add_image_link = implode("|", $add_image_link);
	} else {
		$image_link = "http://{$_SERVER[SERVER_NAME]}/skin/modern/img/noimg.png";
	}
	
	if ($catno) {
		for ($i = 0; $i < strlen($catno)/3; $i++) {
			$r_catno[] = substr($catno, 0, ($i+1)*3);
		}
		
		$query2 = "select * from exm_category where cid = '$cid' and catno in (".implode(",", $r_catno).") order by length(catno)";
		
		$res2 = $db->query($query2);
		while ($data2 = $db->fetch($res2)) {
			$category_name[] = $data2[catnm];
		}
	}
	
	if ($data[csearch_word]) $data[search_word] = $data[csearch_word];
	$data[search_word] = str_replace(",", "|", $data[search_word]);
	
	/*** 
	배송비 처리
	반드시정수형금액표기. 
	무료배송: 0 (컬럼값이null일경우에러처리됨)•
	착불이라면착불표기(e.g.  -1)
	일반배송이라면상품1개구매시적용되는배송금액을표기(e.g.  2500)
	조건부무료배송인경우: 상품1개구매시결제금액이조건부무료배송기준을넘어설경우배송비무료( 0 ) 
	***/
	if(is_array($shipping = f_get_delivery($data[goodsno]))){
		if($shipping[0]["shiptype"]== 0){
			if($shipping[0]["r_shiptype"]== 0){
				$shipping_price=$shipping[0]["shipprice"];
			}else if($shipping[0]["r_shiptype"]== 1){
				//무료배송
				$shipping_price=0;	
			}else{
				//조건부 배송 (상품 한개의 가격이 조건금액보다 가격이 비쌀때 배송비0원처리, 싸면배송비입력)
				$shipping_price = ($data[price]>$shipping[0][shipconditional]) ? 0 : $shipping[0][shipprice];
			}
		}else if($shipping[0]["shiptype"]== 1){
			//무료배송
			$shipping_price=0; 
		}else if($shipping[0]["shiptype"]== 2){
			//개별배송
			$shipping_price=$shipping[0]["shipprice"]; 
		}else if($shipping[0]["shiptype"]== 3){
			//조건부 배송 (상품 한개의 가격이 조건금액보다 가격이 비쌀때 배송비0원처리, 싸면배송비입력)
			$shipping_price = ($data[price]>$shipping[0][shipconditional]) ? 0 : $shipping[0][shipprice];
		}
	}
	if ($data[state] == 1) $class = "D";
	else if ($data[regdt] > date("Y-m-d",strtotime("-10 day", time()))) $class = "I";
	else $class = "U";
	
	# 데이터 값 검증
	$validate_data = array(
		'goodsnm' => $data[goodsnm],		// 상품명
		'catnm1' => $category_name[0],		// 1차 카테고리명
		'catnm2' => $category_name[1],		// 2차 카테고리명
		'catnm3' => $category_name[2],		// 3차 카테고리명
		'catnm4' => $category_name[3],		// 4차 카테고리명
		'searchword' => $data[search_word],	// 검색어
	);

	$validated_data = array();
	
	foreach($validate_data as $k => $v){
		$v=htmlspecialchars($v);		// html 태그 문자화
		$v=stripslashes($v); 			// '\'역슬래쉬 제거
		$validated_data[$k] = $v;
	}
	
	
	//debug($data);
	//EP요청하는 필수값이 있는지 검사
	if(isset($validated_data[goodsnm],$data[price],$data[goodsno],$link,$image_link,$validated_data[catnm1],$shipping_price)) {
		echo "{$enter}{$cid}_{$data[goodsno]}{$tab}{$validated_data[goodsnm]}{$tab}{$data[price]}{$tab}{$data[cprice]}{$tab}{$link}{$tab}{$tab}{$image_link}{$tab}{$add_image_link}{$tab}{$validated_data[catnm1]}{$tab}{$validated_data[catnm2]}{$tab}{$validated_data[catnm3]}{$tab}{$validated_data[catnm4]}{$tab}{$tab}{$tab}{$tab}{$tab}{$tab}{$tab}{$tab}{$tab}{$validated_data[searchword]}{$tab}{$tab}{$tab}{$tab}1{$tab}{$shipping_price}{$tab}{$tab}{$tab}{$tab}{$class}{$tab}".date("Y-m-d H:i:s");
	}
	
}

$content = ob_get_contents();
ob_end_clean();

echo $content;

?>