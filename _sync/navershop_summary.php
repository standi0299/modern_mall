<?
/*
* @date : 20190215
* @author : kdk
* @brief : 네이버 쇼핑 상품정보연동 요약 EP.
* @request :
* @desc : 
* @todo : 
*/

include "../_header.php";
// clean the output buffer
ob_end_clean();

/*
EP 버전 3.0

네이버지식쇼핑상품EP (Engine Page) 제작및연동가이드 (제휴사제공용)
http://join.shopping.naver.com/misc/download/ep_guide.nhn

Field                   Status  Notes
id                      필수    판매하는 상품의 유니크한 상품ID
title                   필수    실제 서비스에 반영될 상품명(Title)
price_pc                필수    상품가격
link                    필수    상품URL
image_link              필수    해당 상품의 이미지URL
category_name1          필수    카테고리명(대분류)
category_name2          권장    카테고리명(중분류)
category_name3          권장    카테고리명(소분류)
category_name4          권장    카테고리명(세분류)
model_number            권장    모델명
brand                   권장    브랜드
maker                   권장    제조사
origin                  권장    원산지
event_words             권장    이벤트
coupon                  권장    쿠폰
interest_free_event     권장    무이자
point                   권장    포인트
shipping                필수    배송료
seller_id               권장    셀러 ID (오픈마켓에 한함)
class                   필수(요약)  I (신규상품) / U (업데이트 상품) / D (품절상품)
update_time             필수(요약)  상품정보 생성 시각
*/

/*
id	
title	
price_pc	
normal_price	
link	
image_link	
category_name1	
category_name2	
category_name3	
category_name4	
maker                   권장    제조사
brand                   권장    브랜드
order_made	
event_words	
point	
search_tag	
minimum_purchase_quantity	
review_count	
shipping	
delivery_grade	
delivery_detail
class                   필수(요약)  I (신규상품) / U (업데이트 상품) / D (품절상품)
update_time             필수(요약)  상품정보 생성 시각
*/

$goods = new Goods();
$m_goods = new M_goods();
$m_member = new M_member();

$r_brand = get_brand();
$r_rid = get_release();

$addWhere = "";
$orderby = "";
$limit = "";
//$limit = "limit 0, 2";

$today = date("Y-m-d");

$addWhere .= " and (regdt >= '$today 00:00:00' or update_date >= '$today 00:00:00')";

$list = $m_goods->getAdminSelfGoodsList($cid, $addWhere, $orderby, $limit);
$list_cnt = $m_goods->getAdminSelfGoodsList($cid, $addWhere);
$totalCnt = count($list_cnt);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

$data = array();
foreach ($list as $key => $value) {
    //debug($value);
    //$goods->getView($value[goodsno]);
    //$data = $goods->viewData;
    //debug($data);
    $categoryList = $m_goods->getLinkCategoryDataList($cid, $value[goodsno]);
	$r_catnm = array();	
    foreach ($categoryList as $k => $v) {
        $r_catnm[] = $v[catnm];
    }
    //debug($r_catnm);

    $goods_image = "";
    $goods_thumb_image = "";
    $delivery_detail = "";
    $class = "";

    $goods_desc = unserialize($value[goods_desc]);
    //debug($goods_desc);
    foreach ($goods_desc as $k => $v) {
        # code...
        if (strpos($v[name], "배송안내") > -1){
            $delivery_detail = $v[value];
            continue;
        }
    }

    $data[id] = $value[goodsno];
    $data[title] = $value[goodsnm];
    $data[price_pc] = $value[mall_price];
    $data[normal_price] = $value[mall_cprice];
    $data[link] = "http://".USER_HOST.'/goods/view.php?goodsno='.$value[goodsno]."&catno=".$categoryList[0][catno];

    $img_arr = explode("|", $value[cimg]);
    $goods_image = "http://". $cfg_center[host]."/data/goods/".$cid."/l/".$value[goodsno]."/" .$img_arr[0];

    if ($value[clistimg])
        $goods_thumb_image = "http://". $cfg_center[host]."/data/goods/".$cid."/s/".$value[goodsno]."/" .$value[clistimg];
    
    if (!$img_arr[0]) $goods_image = $goods_thumb_image;

    $data[image_link] = $goods_image;
    $data[category_name1] = ($categoryList[0][catno]) ? $categoryList[0][catnm] : "";
    $data[category_name2] = ($categoryList[1][catno]) ? $categoryList[1][catnm] : "";
    $data[category_name3] = ($categoryList[2][catno]) ? $categoryList[2][catnm] : "";
    $data[category_name4] = ($categoryList[3][catno]) ? $categoryList[3][catnm] : "";
    $data[maker] = $r_rid[$value[rid]];
    $data[brand] = $r_brand[$value[brandno]];
    $data[order_made] = ($value[podsno]) ? "Y" : "";
    $data[event_words] = "";
    $data[point] = "";
    $data[search_tag] = $value[search_word];
    $data[minimum_purchase_quantity] = "1";
    $data[review_count] = $m_member->getReviewCnt($cid," and goodsno='$value[goodsno]'");
    $data[shipping] = "0";
    $data[delivery_grade] = "Y";
    $data[delivery_detail] = $delivery_detail;

    //option_detail //레이스원피스^23000|멜빵원피스^25000|…

    //class                   필수(요약)  I (신규상품) / U (업데이트 상품) / D (품절상품)
    //update_time             필수(요약)  상품정보 생성 시각
    $class = "";

    if ($data[regdt] > $data[update_time]) {
        $class = "I";
    }
    else {
        if ($value[state] == "1") $class = "U";
        else if ($value[state] == "2") $class = "D";
    }

    $data['class'] = $class;
    $data[update_time] = $value[update_date];

    //debug($data);
    $plist[data][] = $data;
}

//debug($plist);
//exit;

$tab = "\t";

ob_start();

echo "id{$tab}title{$tab}price_pc{$tab}normal_price{$tab}link{$tab}image_link{$tab}category_name1{$tab}category_name2{$tab}category_name3{$tab}category_name4{$tab}maker{$tab}brand{$tab}order_made{$tab}event_words{$tab}point{$tab}search_tag{$tab}minimum_purchase_quantity{$tab}review_count{$tab}shipping{$tab}delivery_grade{$tab}delivery_detail{$tab}class{$tab}update_time";

if ($plist[data]) {
    foreach ($plist[data] as $key => $val) {
        echo "\n{$val['id']}{$tab}{$val['title']}{$tab}{$val['price_pc']}{$tab}{$val['normal_price']}{$tab}{$val['link']}{$tab}{$val['image_link']}{$tab}{$val['category_name1']}{$tab}{$val['category_name2']}{$tab}{$val['category_name3']}{$tab}{$val['category_name4']}{$tab}{$val['maker']}{$tab}{$val['brand']}{$tab}{$val['order_made']}{$tab}{$val['event_words']}{$tab}{$val['point']}{$tab}{$val['search_tag']}{$tab}{$val['minimum_purchase_quantity']}{$tab}{$val['review_count']}{$tab}{$val['shipping']}{$tab}{$val['delivery_grade']}{$tab}{$val['delivery_detail']}{$tab}{$val['class']}{$tab}{$val['delivery_detail']}";
    }
}

$content = ob_get_contents();
ob_end_clean();

echo $content;
?> 
