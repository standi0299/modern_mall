<?

//############ system 배너  관련 설정 ######################
/*** User Func. ***/
### 기능성 배너 선언
$r_sys_banner = array(
    "_sys_btn_register_submit"  => array(
        "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_register_submit' --event-- style='margin-right:5px;'/>",
        "src"   => "_sys_btn_register_submit.gif",
    ),
    "_sys_btn_register_back"    => array(
        "shape" => "<a href='javascript:history.back()'><img src='--banner--' class='_banner' code='_sys_btn_register_back' --event-- style='margin-right:5px;'></a>",
        "src"   => "_sys_btn_register_back.gif",
    ),
    "_sys_btn_register_ok1" => array(
        "shape" => "<a href='/'><img src='--banner--' class='_banner' code='_sys_btn_register_ok1' --event--/></a>",
        "src"   => "_sys_btn_register_ok1.gif",
    ), 
    "_sys_btn_register_ok2" => array(
        "shape" => "<a href='../member/login.php'><img src='--banner--' class='_banner' code='_sys_btn_register_ok2' --event--/></a>",
        "src"   => "_sys_btn_register_ok2.gif",
    ), 
    "_sys_txt_register_ok"  => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_txt_register_ok' --event--/>",
        "src"   => "_sys_txt_register_ok.gif",
    ),
    "_sys_right_today_tit"  => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_right_today_tit' --event--/>",
        "src"   => "_sys_right_today_tit.jpg",
    ),
    "_sys_right_today_bot"  => array(
        "shape" => "<a href='#'><img src='--banner--' class='_banner' code='_sys_right_today_bot' --event--/></a>",
        "src"   => "_sys_right_today_bot.jpg",
    ),
    "_sys_cart_desc"        => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_cart_desc' --event-- style='vertical-align:middle'/>",
        "src"   => "_sys_cart_desc.gif",
    ),
    "_sys_btn_back"     => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_back' --event-- style='vertical-align:middle'/>",
        "src"   => "_sys_btn_back.gif",
    ),
    "_sys_btn_mycs"     => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mycs' --event-- style='vertical-align:middle'/>",
        "src"   => "_sys_btn_mycs.gif",
    ),
    "_sys_btn_search"   => array(
        "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_search' --event-- style='vertical-align:middle'/>",
        "src"   => "_sys_btn_search.png",
    ),
    "_sys_btn_cart_delete_lump2"    => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_cart_delete_lump2' --event--/>",
        "src"   => "_sys_btn_cart_delete_lump.gif",
    ),
    "_sys_btn_cart_order4"  => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_cart_order4' --event--/>",
        "src"   => "_sys_btn_cart_order.gif",
    ),
    "_sys_btn_pay_cancel"   => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_pay_cancel' --event--/>",
        "src"   => "_sys_btn_pay_cancel.gif",
    ),
    "_sys_btn_cart_order3"  => array(
        "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_cart_order3' id='btn_submit_disable' --event--/>",
        "src"   => "_sys_btn_cart_pay.gif",
    ),
    "_sys_btn_order_cancel" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_order_cancel' --event--/>",
        "src"   => "_sys_btn_order_cancel.gif",
    ),
    "_sys_btn_cart_order2"  => array(
        "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_cart_order2' --event--/>",

        "src"   => "_sys_btn_cart_order.gif",
    ),
    "_sys_btn_cart_shopping"    => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_cart_shopping' --event--/>",
        "src"   => "_sys_btn_cart_shopping.gif",
    ),
    "_sys_btn_cart_order"   => array(
        "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_cart_order' --event-- onclick='set_mode(\"order\")'/>",
        "src"   => "_sys_btn_cart_order.gif",
    ),
    "_sys_btn_cart_delete_lump" => array(
        "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_cart_delete_lump' --event-- onclick='set_mode(\"truncate\")'/>",
        "src"   => "_sys_btn_cart_delete_lump.gif",
    ),
    "_sys_btn_top_search"   => array(
        "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_top_search' --event--/>",
        "src"   => "_sys_btn_top_search.jpg",
    ),
    "_sys_btn_goods_make"   => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_make' --event--/>",
        "src"   => "_sys_btn_goods_make.gif",
    ),
    "_sys_btn_goods_sns_fb" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_sns_fb' --event--/>",
        "src"   => "_sys_btn_goods_sns_fb.gif",
    ),
    "_sys_btn_goods_sns_tw" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_sns_tw' --event--/>",
        "src"   => "_sys_btn_goods_sns_tw.gif",
    ),
    "_sys_btn_goods_sns_m2" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_sns_m2' --event--/>",
        "src"   => "_sys_btn_goods_sns_m2.gif",
    ),
    "_sys_btn_goods_sns_yz" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_sns_yz' --event--/>",
        "src"   => "_sys_btn_goods_sns_yz.gif",
    ),
    "_sys_btn_goods_buy"    => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_buy' --event--/>",
        "src"   => "_sys_btn_goods_buy.gif",
    ),
    "_sys_btn_goods_cart"   => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_cart' --event--/>",
        "src"   => "_sys_btn_goods_cart.gif",
    ),
    "_sys_btn_qna_write"    => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_qna_write' --event--/>",
        "src"   => "_sys_btn_qna_write.gif",
    ),
    "_sys_btn_review_write" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_review_write' --event--/>",
        "src"   => "_sys_btn_review_write.gif",
    ),
    //<img src='/skin/basic/img/blt_arrow2.gif' align='absmiddle' style='margin-right:5px;vertical-align:middle;'/> _sys_btn_mypage_txt_ 삭제 / 16.08.09 / kdk
    "_sys_btn_mypage_txt_name"  => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mypage_txt_name' style='vertical-align:middle;' --event--/>",
        "src"   => "_sys_btn_mypage_txt_name.gif",
    ),
    "_sys_btn_mypage_txt_id"    => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mypage_txt_id' style='vertical-align:middle;' --event--/>",
        "src"   => "_sys_btn_mypage_txt_id.gif",
    ),
    "_sys_btn_mypage_txt_email" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mypage_txt_email' style='vertical-align:middle;' --event--/>",
        "src"   => "_sys_btn_mypage_txt_email.gif",
    ),
    "_sys_btn_mypage_txt_grp"   => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mypage_txt_grp' style='vertical-align:middle;' --event--/>",
        "src"   => "_sys_btn_mypage_txt_grp.gif",
    ),
    "_sys_btn_mypage_txt_phone" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mypage_txt_phone' style='vertical-align:middle;' --event--/>",
        "src"   => "_sys_btn_mypage_txt_phone.gif",
    ),
    "_sys_btn_mypage_txt_emoney"    => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mypage_txt_emoney' style='vertical-align:middle;' --event--/>",
        "src"   => "_sys_btn_mypage_txt_emoney.gif",
    ),
    "_sys_btn_mypage_txt_addr"  => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mypage_txt_addr' style='vertical-align:middle;' --event--/>",
        "src"   => "_sys_btn_mypage_txt_addr.gif",
    ),
    "_sys_btn_mypage_txt_coupon"    => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mypage_txt_coupon' style='vertical-align:middle;' --event--/>",
        "src"   => "_sys_btn_mypage_txt_coupon.gif",
    ),
    "_sys_tit_goods_view01" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_view01' style='vertical-align:middle;' usemap='#_sys_tit_goods_view01'/>",
        "map"   => "<map name='_sys_tit_goods_view01'><area shape='rect' coords='5,10,95,35' href='#desc'/><area shape='rect' coords='108,10,168,35' href='#review'/><area shape='rect' coords='183,10,230,35' href='#trackback'/><area shape='rect' coords='245,10,280,35' href='#qna'/><area shape='rect' coords='295,10,390,35' href='#ship'/></map>",
        "src"   => "_sys_tit_goods_view01.gif",
    ),
    "_sys_tit_goods_view02" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_view02' style='vertical-align:middle;' usemap='#_sys_tit_goods_view02'/>",
        "map"   => "<map name='_sys_tit_goods_view02'><area shape='rect' coords='5,10,65,35' href='#desc'/><area shape='rect' coords='83,10,168,35' href='#review'/><area shape='rect' coords='183,10,230,35' href='#trackback'/><area shape='rect' coords='245,10,280,35' href='#qna'/><area shape='rect' coords='295,10,390,35' href='#ship'/></map>",
        "src"   => "_sys_tit_goods_view02.gif",
    ),
    "_sys_tit_goods_view03" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_view03' style='vertical-align:middle;' usemap='#_sys_tit_goods_view03'/>",
        "map"   => "<map name='_sys_tit_goods_view03'><area shape='rect' coords='5,8,65,32' href='#desc'/><area shape='rect' coords='80,8,137,32' href='#review'/><area shape='rect' coords='153,8,230,32' href='#trackback'/><area shape='rect' coords='243,8,278,32' href='#qna'/><area shape='rect' coords='293,8,388,32' href='#ship'/></map>",
        "src"   => "_sys_tit_goods_view03.gif",
    ),
    "_sys_tit_goods_view04" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_view04' style='vertical-align:middle;' usemap='#_sys_tit_goods_view04'/>",
        "map"   => "<map name='_sys_tit_goods_view04'><area shape='rect' coords='5,10,65,32' href='#desc'/><area shape='rect' coords='80,10,137,32' href='#review'/><area shape='rect' coords='153,10,200,32' href='#trackback'/><area shape='rect' coords='213,10,278,32' href='#qna'/><area shape='rect' coords='293,10,388,32' href='#ship'/></map>",
        "src"   => "_sys_tit_goods_view04.gif",
    ),
    "_sys_tit_goods_view05" => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_view05' style='vertical-align:middle;' usemap='#_sys_tit_goods_view05'/>",
        "map"   => "<map name='_sys_tit_goods_view05'><area shape='rect' coords='5,6,65,30' href='#desc'/><area shape='rect' coords='80,6,137,30' href='#review'/><area shape='rect' coords='153,6,200,30' href='#trackback'/><area shape='rect' coords='213,6,252,30' href='#qna'/><area shape='rect' coords='268,6,398,30' href='#ship'/></map>",
        "src"   => "_sys_tit_goods_view05.gif",
    ),
    "_sys_btn_request_quotation"    => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_request_quotation' --event--/>",
        "src"   => "_sys_btn_quotation.gif",
    ),
    "_sys_btn_request_sample"   => array(
        "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_request_sample' --event--/>",
        "src"   => "_sys_btn_sample.gif",
    ),
  
  //20140115  추가    chunter
    "_sys_btn_orderlist_delete_lump"  => array(
    "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_orderlist_delete_lump' --event-- onclick='set_confirm()'/>",
    "src" => "_sys_btn_cart_delete_lump.gif",
  ),
  "_sys_btn_goods_list_make" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_list_make' --event--/>",
    "src" => "_sys_btn_goods_make.gif",
  ),
  //20140528추가 / minks
  "_sys_btn_goods_list_make_much" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_list_make_much' --event--/>",
    "src" => "_sys_btn_goods_make.gif",
  ),
  //20140129 추가 kdk
  "_sys_btn_goods_list_preview" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_list_preview' --event--/>",
    "src" => "_sys_btn_goods_preview.gif",
  ),
  //20140702 추가(bizcard 기본 배너 이미지) / minks
  "_sys_btn_top_right_banner1" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_top_right_banner1' --event--/>",
    "src" => "_sys_btn_top_right_banner1.png",
  ),
  "_sys_btn_left_category_banner" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_left_category_banner' --event--/>",
    "src" => "_sys_btn_left_category_banner.png",
  ),
  "_sys_btn_left_main_banner" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_left_main_banner' --event--/>",
    "src" => "_sys_btn_left_category_banner.png",
  ),
  "_sys_btn_main_top_left_logotitle" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_main_top_left_logotitle' --event--/>",
    "src" => "_sys_btn_left_category_banner.png",
  ),
  "_sys_btn_left_category_banner3" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_left_category_banner3' --event--/>",
    "src" => "_sys_btn_left_category_banner3.png",
  ),
  "_sys_btn_left_main_banner3" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_left_main_banner3' --event--/>",
    "src" => "_sys_btn_left_category_banner3.png",
  ),
  "_sys_btn_main_top_left_logotitle3" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_main_top_left_logotitle3' --event--/>",
    "src" => "_sys_btn_left_category_banner3.png",
  ),
  "_sys_btn_orderlist" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_orderlist' --event--/>",
    "src" => "_sys_btn_orderlist.png",
  ),
  "_sys_btn_main_banner" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_main_banner' --event--/>",
    "src" => "_sys_btn_main_banner.png",
  ),
  "_sys_btn_left_category_banner2" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_left_category_banner2' --event--/>",
    "src" => "_sys_btn_left_category_banner2.png",
    "src2" => "_sys_btn_left_category_banner2_on.png",
  ),
  "_sys_btn_left_main_banner2" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_left_main_banner2' --event--/>",
    "src" => "_sys_btn_left_category_banner2.png",
    "src2" => "_sys_btn_left_category_banner2_on.png",
  ),
  "_sys_btn_main_top_left_logotitle2" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_main_top_left_logotitle2' --event--/>",
    "src" => "_sys_btn_left_category_banner2.png",
    "src2" => "_sys_btn_left_category_banner2_on.png",
  ),
  "_sys_btn_bizcard_cart_order2"    => array(
     "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_btn_bizcard_cart_order2' --event--/>",
     "src"   => "_sys_btn_bizcard_cart_order2.png",
     "src2"  => "_sys_btn_bizcard_cart_order2_on.png",
  ),
  "_sys_btn_bizcard_order_cancel"   => array(
     "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_bizcard_order_cancel' --event--/>",
     "src"   => "_sys_btn_bizcard_order_cancel.png",
     "src2"  => "_sys_btn_bizcard_order_cancel_on.png",
  ),
  "_sys_btn_bizcard_goods_list_make" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_bizcard_goods_list_make' --event--/>",
    "src" => "_sys_btn_bizcard_goods_list_make.png",
    "src2" => "_sys_btn_bizcard_goods_list_make_on.png",
  ),
  "_sys_btn_bizcard_goods_list_make_much" => array(
    "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_bizcard_goods_list_make_much' --event--/>",
    "src" => "_sys_btn_bizcard_goods_list_make_much.png",
    "src2" => "_sys_btn_bizcard_goods_list_make_much_on.png",
  ),
   //20150430 / minks / 견적서작성버튼 추가
   "_sys_btn_bizcard_estimate"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_bizcard_estimate' --event--/>",
      "src"   => "_sys_btn_bizcard_estimate.png",
      "src2"  => "_sys_btn_bizcard_estimate_on.png",
   ),
   //20141016 / minks / 주문이관버튼 추가
   "_sys_btn_transfer_edit"  => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_transfer_edit' --event--/>",
      "src"   => "_sys_btn_transfer_edit.png",
   ),
   //20141103 / minks / 배송지선택버튼 추가
   "_sys_btn_delivery_select"  => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_delivery_select' --event--/>",
      "src"   => "_sys_btn_delivery_select.png",
   ),
   //20150414 / minks / spring 스킨 상품상세 페이지
   "_sys_tit_goods_desc" => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_desc' --event--/>",
      "src"   => "_sys_tit_goods_desc.jpg",
      "src2" => "_sys_tit_goods_desc_on.jpg",
   ),
   "_sys_tit_goods_review" => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_review' --event--/>",
      "src"   => "_sys_tit_goods_review.jpg",
      "src2" => "_sys_tit_goods_review_on.jpg",
   ),
   "_sys_tit_goods_qna" => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_qna' --event--/>",
      "src"   => "_sys_tit_goods_qna.jpg",
      "src2" => "_sys_tit_goods_qna_on.jpg",
   ),
   "_sys_tit_goods_ship" => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_tit_goods_ship' --event--/>",
      "src"   => "_sys_tit_goods_ship.jpg",
      "src2" => "_sys_tit_goods_ship_on.jpg",
   ),

   //---------- basic 스킨 버튼 배너화(디지털명성텍 요청) / 15.12.29 / kjm
   
   //회원 탈퇴
   "_sys_member_leave_bar_withdraw" => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_member_leave_bar_withdraw' --event--/>",
      "src"   => "bar_withdraw.png",
   ),
   "_sys_member_leave_txt_withdrawl" => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_member_leave_txt_withdrawl' --event--/>",
      "src"   => "txt_withdrawl.gif",
   ),
   
   //견적서 확인 페이지
   "_sys_extra_cart_list_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_extra_cart_list_title' --event--/>",
      "src"   => "extra_cart_list_title.jpg",
   ),
   "_sys_extra_cart_list_view" => array(
      "shape" => "<a href=\"javascript:;\" onclick=\"set_bill();\"><img src='--banner--' class='_banner' code='_sys_extra_cart_list_view' --event--/></a>",
      "src"   => "but_07.png",
   ),
   "_sys_extra_cart_list_tocart" => array(
      "shape" => "<a href=\"javascript:;\" onclick=\"set_mode();\"><img src='--banner--' class='_banner' code='_sys_extra_cart_list_tocart' --event--/></a>",
      "src"   => "but_10.png",
   ),
   "_sys_extra_cart_list_cancel" => array(
      "shape" => "<a href=\"javascript:;\" onclick=\"set_mode('del_extra');\"><img src='--banner--' class='_banner' code='_sys_extra_cart_list_cancel' --event--/></a>",
      "src"   => "but_11.png",
   ),
   
   //회원정보수정
   "_sys_member_modify_submit" => array(
      "shape" => "<input type='image' src='--banner--' class='_banner' code='_sys_member_modify_submit' --event--/>",
      "src"   => "mbtn_submit.gif",
   ),
   "_sys_member_modify_cancel" => array(
      "shape" => "<a href=\"javascript:history.back()\"><img src='--banner--' class='_banner' code='_sys_member_modify_cancel' --event--/></a>",
      "src"   => "mbtn_cancel.gif",
   ),
   
   //---------- 끝

   //pretty 기본배너(이미지, 버튼) / 15.07.06 / kjm
   "child_image_manage_sub_title" => array(
      "shape" => "<img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='child_image_manage_sub_title' --event--/>",
      "src"   => "sub1_title.png",
   ),
   
   "child_image_manage_reg_button" => array(
      "shape" => "<a href='javascript:popup(\"class_child_manage.php\",\"728px\",\"405px\");'><img src='--banner--' class='_banner mt30' code='child_image_manage_reg_button' --event--/></a>",
      "src"   => "ban_reg_set01.png",
      "src2"  => "ban_reg_set02.png",
   ),
   
   "child_image_manage_reg_button_s2" => array(
      "shape" => "<a href='javascript:popup(\"class_child_manage_s2.php\",\"728px\",\"405px\");'><img src='--banner--' class='_banner mt30' code='child_image_manage_reg_button' --event--/></a>",
      "src"   => "ban_reg_set01.png",
      "src2"  => "ban_reg_set02.png",
   ),

   "child_edit_list_class_child_manage_button" => array(
      "shape" => "<a href='javascript:popup(\"/pretty_s2/class_child_manage_s2.php\",\"728px\",\"405px\");'><img src='--banner--' class='_banner' code='child_edit_list_class_child_manage_button' --event--/></a>",
      "src"   => "ban_set01.png",
      "src2"  => "ban_set02.png",
   ),

   "child_edit_list_class_list_manage_button" => array(
      "shape" => "<img src='--banner--' class='_banner' code='child_edit_list_class_list_manage_button' --event--/>",
      "src"   => "btn_list_admin01.png",
      "src2"  => "btn_list_admin02.png",
   ),
   
   "pop_goods_class_mapping_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='pop_goods_class_mapping_title' --event--/>",
      "src"   => "h1_title9.png",
   ),

   "top_menu_info_banner_homepage" => array(
      "shape" => "<a href='/pretty/home_open.php'><img src='--banner--' class='_banner' code='top_menu_info_banner_homepage' --event--/></a>",
      "src"   => "top_menu_homepage.gif",
      "src2"  => "top_menu_homepage_on.gif",
   ),

   "top_menu_info_banner_bluephotomall" => array(
      "shape" => "<a href='/service/goto_bluephotomall.php'><img src='--banner--' class='_banner' code='top_menu_info_banner_bluephotomall' --event--/></a>",
      "src"   => "top_menu_bluephotomall.png",
      "src2"  => "top_menu_bluephotomall_on.png",
   ),
   
   //반/원아 관리 팝업창의 타이틀
   "class_child_manage_pop_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='class_child_manage_pop_title' target='_blank' --event--/>",
      "src"   => "h1_title3.png",
   ),
   
   "class_child_manage_pop_class_reg" => array(
      "shape" => "<img src='--banner--' class='_banner' code='class_child_manage_pop_class_reg' target='_blank' --event--/>",
      "src"   => "btn_ban_reg01.png",
      "src2"   => "btn_ban_reg02.png",
   ),
   
   "class_enroll_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='class_enroll_title' target='_blank' --event--/>",
      "src"   => "h1_title4.png",
   ),
   
   "class_child_manage_pop_stu_join" => array(
      "shape" => "<img src='--banner--' class='_banner' code='class_child_manage_pop_stu_join' target='_blank' --event--/>",
      "src"   => "btn_child_reg01.png",
   ),
   
   "child_img_manage_photo_save" => array(
      "shape" => "<a href='javascript:upload_app_click();'><img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='child_img_manage_photo_save' --event--/></a>",
      "src"   => "photo_save01.png",
      "src2"  => "photo_save02.png",
   ),
   
   "child_img_manage_photo_save_s2" => array(
      "shape" => "<a href='javascript:upload_app_click();'><img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='child_img_manage_photo_save_s2' --event--/></a>",
      "src"   => "photo_save01.png",
      "src2"  => "photo_save02.png",
   ),

   "pop_goods_child_mapping_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='pop_goods_child_mapping_title' --event--/></a>",
      "src"   => "h1_title27.png",
   ),
      
   "pop_child_edit_list_mng_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='pop_child_edit_list_mng_title' --event--/></a>",
      "src"   => "h1_title9.png",
   ),
      
   "class_orderby_change_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='class_orderby_change_title' --event--/></a>",
      "src"   => "h1_title5.png",
   ),
      
   "child_orderby_change_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='child_orderby_change_title' --event--/></a>",
      "src"   => "h1_title6.png",
   ),
   
   "class_modify_title" => array(
      "shape" => "<img src='--banner--' class='_banner' code='class_modify_title' --event--/></a>",
      "src"   => "h1_title13.png",
   ),
   
   //자동견적 주문버튼 시스템배너화
   "estimate_request" => array(
      "shape" => "<img src='--banner--' class='_banner' banner_type='s2' onclick='orderInfoOpenLayer(this)' code='estimate_request' style='margin-right:5px;cursor: pointer;' --event--/>",
      "src"   => "but_06.png",
   ),
   
   "estimate_order" => array(
      //"shape" => "<img src='--banner--' class='_banner' banner_type='s2' onclick='initOrder(\"\",\"NEW\")' code='estimate_order' style='margin-right:5px;cursor: pointer;' --event--/>",
      "shape" => "<img src='--banner--' class='_banner' banner_type='s2' onclick='fileInfoOpenLayer(this)' code='estimate_order' style='margin-right:5px;cursor: pointer;' --event--/>",
      "src"   => "but_03.jpg",
   ),
    
   //---------------------------------------------------------------------------------------------------------//
    
   "img_upload_sub_title" => array(
      "shape" => "<img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='img_upload_sub_title' --event--/>",
      "src"   => "sub2_title.png",
   ),
   "img_upload_folder_reg" => array(
      "shape" => "<a href='javascript:popup(\"folder_manage.php\",\"735px\",\"415px\");'><img src='--banner--' class='_banner_text mt30' code='img_upload_folder_reg' --event--/></a>",
      "src"   => "folder_reg_set02.png",
      "src2"  => "folder_reg_set01.png",
   ),
   "img_upload_btn_uplode" => array(
      "shape" => "<a href='javascript:upload_app_click(\"upload\");'><img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='img_upload_btn_uplode' --event--/></a>",
      "src"   => "btn_uplode_01.png",
      "src2"  => "btn_uplode_02.png",
   ),
   
   "img_upload_btn_uplode_s2" => array(
      "shape" => "<a href='javascript:upload_app_click(\"folder\");'><img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='img_upload_btn_uplode_s2' --event--/></a>",
      "src"   => "btn_uplode_01.png",
      "src2"  => "btn_uplode_02.png",
   ),
   
   "img_upload_btn_photo_sort" => array(
      "shape" => "<a href='javascript:upload_app_click(\"grouping\");'><img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='img_upload_btn_photo_sort' --event--/></a>",
      "src"   => "btn_photo_sort01.png",
      "src2"  => "btn_photo_sort02.png",
   ),
   
   "img_upload_btn_photo_sort_s2" => array(
      "shape" => "<a href='javascript:upload_app_click(\"private\");'><img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='img_upload_btn_photo_sort_s2' --event--/></a>",
      "src"   => "btn_photo_sort01.png",
      "src2"  => "btn_photo_sort02.png",
   ),
   
    "admin_cart_reorder_sub_title" => array(
      "shape" => "<img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='admin_cart_reorder_sub_title' --event--/>",
      "src"   => "cart_reorder_title.png",
   ),
    
   //---------------------------------------------------------------------------------------------------------//
    
   "child_edit_list_sub_title" => array(
      "shape" => "<img src='--banner--' class='_banner_text' banner_type='s2' image_type='Y' text_cnt='1' add_type='Y' code='child_edit_list_sub_title' --event--/>",
      "src"   => "sub3_title.png",
   ),
   "child_edit_list_group_item_add" => array(
      "shape" => "<a href=\"javascript:popup('/goods/list.php?catno=001','650px', '528px', 'yes', 'yes','goods_detail');\"><img src='--banner--' class='_banner' code='child_edit_list_group_item_add' --event--/></a>",
      "src"   => "group_item_add02.png",
      "src2"  => "group_item_add01.png",
   ),
   "child_edit_list_edit_item_set" => array(
      "shape" => "<a href=\"javascript:popup('/pretty/pop_child_edit_list_mng.php?catno=001','410px', '398px');\"><img src='--banner--' class='_banner' code='child_edit_list_edit_item_set' --event--/></a>",
      "src"   => "edit_item_set02.png",
      "src2"  => "edit_item_set01.png",
   ),
   //20151007 / minks / 모바일 전용 버튼
   "_sys_bar_slide_menu_logo"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_bar_slide_menu_logo' --event-- style='width:auto;height:111px;' />",
      "src"   => "_sys_bar_slide_menu_logo.png",
   ),
   "_sys_btn_mobile_go_home"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_home' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_go_home.png",
   ),
   "_sys_btn_mobile_go_cart"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_cart' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_go_cart.png",
   ),
   "_sys_btn_mobile_go_orderlist"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_orderlist' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_go_orderlist.png",
   ),
   "_sys_btn_mobile_go_faq"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_faq' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_go_faq.png",
   ),
   "_sys_btn_mobile_go_notice"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_notice' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_go_notice.png",
   ),
   "_sys_btn_mobile_go_configapp"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_configapp' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_go_configapp.png",
   ),
   "_sys_btn_mobile_prd_make"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_prd_make' --event-- style='width:auto;height:57px;' />",
      "src"   => "_sys_btn_mobile_prd_make.png",
   ),
   "_sys_btn_mobile_prd_make_print"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_prd_make_print' --event-- style='width:80px;height:24px;' />",
      "src"   => "_sys_btn_mobile_prd_make_print.png",
   ),
   "_sys_btn_mobile_cart_del"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_cart_del' --event-- style='width:50px;height:20px;' />",
      "src"   => "_sys_btn_mobile_cart_del.png",
   ),
   "_sys_btn_mobile_cart_edit"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_cart_edit' --event-- style='width:50px;height:20px;' />",
      "src"   => "_sys_btn_mobile_cart_edit.png",
   ),
   "_sys_btn_mobile_cart_preview"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_cart_preview' --event-- style='width:50px;height:20px;' />",
      "src"   => "_sys_btn_mobile_cart_preview.png",
   ),
   "_sys_btn_mobile_cart_minus"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_cart_minus' --event-- style='width:20px;height:20px;' />",
      "src"   => "_sys_btn_mobile_cart_minus.png",
   ),
   "_sys_btn_mobile_cart_plus"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_cart_plus' --event-- style='width:20px;height:20px;' />",
      "src"   => "_sys_btn_mobile_cart_plus.png",
   ),
   "_sys_btn_mobile_cart_order"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_cart_order' --event-- style='width:auto;height:57px;' />",
      "src"   => "_sys_btn_mobile_cart_order.png",
   ),
   "_sys_btn_mobile_zipcode"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_zipcode' --event-- style='width:60px;height:27px;' />",
      "src"   => "_sys_btn_mobile_zipcode.png",
   ),
   "_sys_btn_mobile_order_pay"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_order_pay' --event-- style='width:auto;height:57px;' />",
      "src"   => "_sys_btn_mobile_order_pay.png",
   ),
   "_sys_btn_mobile_go_orderlist2"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_orderlist2' --event-- style='width:60px;height:26px;' />",
      "src"   => "_sys_btn_mobile_go_orderlist2.png",
   ),
   "_sys_btn_mobile_go_cart2"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_cart2' --event-- style='width:60px;height:26px;' />",
      "src"   => "_sys_btn_mobile_go_cart2.png",
   ),
   "_sys_btn_mobile_go_orderview"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_orderview' --event-- style='width:auto;height:34px;' />",
      "src"   => "_sys_btn_mobile_go_orderview.png",
   ),
   "_sys_btn_mobile_search_ship"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_search_ship' --event-- style='width:50px;height:21px;' />",
      "src"   => "_sys_btn_mobile_search_ship.png",
   ),
   "_sys_btn_mobile_cancel"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_cancel' --event-- style='width:auto;height:47px;' />",
      "src"   => "_sys_btn_mobile_cancel.png",
   ),
   "_sys_btn_mobile_confirm"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_confirm' --event-- style='width:auto;height:47px;' />",
      "src"   => "_sys_btn_mobile_confirm.png",
   ),
   "_sys_btn_mobile_cancel2"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_cancel2' --event-- style='width:auto;height:26px;' />",
      "src"   => "_sys_btn_mobile_cancel2.png",
   ),
   "_sys_btn_mobile_search"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_search' --event-- style='width:auto;height:26px;' />",
      "src"   => "_sys_btn_mobile_search.png",
   ),
   //20160126 / minks / 모바일 전용 버튼(회원 연동 관련)
   "_sys_btn_mobile_go_coupon"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_coupon' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_go_coupon.png",
   ),
   "_sys_btn_mobile_go_logout"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_logout' --event-- style='width:60px;height:27px;' />",
      "src"   => "_sys_btn_mobile_go_logout.png",
   ),
   "_sys_btn_mobile_go_login"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_login' --event-- style='width:60px;height:27px;' />",
      "src"   => "_sys_btn_mobile_go_login.png",
   ),
   "_sys_btn_mobile_login"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_login' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_login.png",
   ),
   "_sys_btn_mobile_member_register"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_member_register' --event-- style='width:auto;height:34px;' />",
      "src"   => "_sys_btn_mobile_member_register.png",
   ),
   "_sys_btn_mobile_search_myinfo"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_search_myinfo' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_search_myinfo.png",
   ),
   "_sys_btn_mobile_member_register2"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_member_register2' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_member_register2.png",
   ),
   "_sys_btn_mobile_member_modify"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_member_modify' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_member_modify.png",
   ),
   "_sys_btn_mobile_member_leave_cancel"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_member_leave_cancel' --event-- style='width:auto;height:47px;' />",
      "src"   => "_sys_btn_mobile_member_leave_cancel.png",
   ),
   "_sys_btn_mobile_member_leave"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_member_leave' --event-- style='width:auto;height:47px;' />",
      "src"   => "_sys_btn_mobile_member_leave.png",
   ),
   //20160205 / minks / 모바일 전용 버튼(회원 주문 관련)
   "_sys_btn_mobile_coupon_insert"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_coupon_insert' --event-- style='width:auto;height:57px;' />",
      "src"   => "_sys_btn_mobile_coupon_insert.png",
   ),
   "_sys_btn_mobile_coupon_detail"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_coupon_detail' --event-- style='width:60px;height:26px;' />",
      "src"   => "_sys_btn_mobile_coupon_detail.png",
   ),
   "_sys_btn_mobile_coupon_down"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_coupon_down' --event-- style='width:60px;height:26px;' />",
      "src"   => "_sys_btn_mobile_coupon_down.png",
   ),
   "_sys_btn_mobile_coupon_close"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_coupon_close' --event-- style='width:auto;height:34px;' />",
      "src"   => "_sys_btn_mobile_coupon_close.png",
   ),
   "_sys_btn_mobile_coupon_use"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_coupon_use' --event-- style='width:50px;height:21px;vertical-align:-20%;' />",
      "src"   => "_sys_btn_mobile_coupon_use.png",
   ),
   "_sys_btn_mobile_emoney_use"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_emoney_use' --event-- style='width:auto;height:40px;' />",
      "src"   => "_sys_btn_mobile_emoney_use.png",
   ),
   "_sys_btn_mobile_coupon_use2"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_coupon_use2' --event-- style='width:auto;height:57px;' />",
      "src"   => "_sys_btn_mobile_coupon_use2.png",
   ),
   "_sys_btn_mobile_mycs_write"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_mycs_write' --event-- style='width:80px;height:24px;' />",
      "src"   => "_sys_btn_mobile_mycs_write.png",
   ),
   "_sys_btn_mobile_mycs_write_cancel"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_mycs_write_cancel' --event-- style='width:auto;height:47px;' />",
      "src"   => "_sys_btn_mobile_member_leave_cancel.png",
   ),
   "_sys_btn_mobile_mycs_write_confirm"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_mycs_write_confirm' --event-- style='width:auto;height:47px;' />",
      "src"   => "_sys_btn_mobile_mycs_write_confirm.png",
   ),
   "_sys_btn_mobile_mycs_write_edit"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_mycs_write_edit' --event-- style='width:50px;height:20px;' />",
      "src"   => "_sys_btn_mobile_mycs_write_edit.png",
   ),
   "_sys_btn_mobile_guest_order"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_guest_order' --event-- style='width:auto;height:34px;' />",
      "src"   => "_sys_btn_mobile_guest_order.png",
   ),
   "_sys_btn_mobile_go_mycs"    => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_mobile_go_mycs' --event-- style='width:auto;height:56px;' />",
      "src"   => "_sys_btn_mobile_go_mycs.png",
   ),
   //20151029 / minks / spring 스킨 견적상품 관련
   "_sys_btn_goods_estimate_request"   => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_estimate_request' --event--/>",
      "src"   => "_sys_btn_goods_estimate_request.png",
   ),
   "_sys_btn_goods_estimate_make"   => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_goods_estimate_make' --event--/>",
      "src"   => "_sys_btn_goods_estimate_make.jpg",
   ),
   "_sys_btn_order_estimate_view"   => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_order_estimate_view' --event--/>",
      "src"   => "_sys_btn_order_estimate_view.png",
   ),
   "_sys_btn_order_estimate_cart"   => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_order_estimate_cart' --event--/>",
      "src"   => "_sys_btn_order_estimate_cart.png",
   ),
   "_sys_btn_order_estimate_cancel"   => array(
      "shape" => "<img src='--banner--' class='_banner' code='_sys_btn_order_estimate_cancel' --event--/>",
      "src"   => "_sys_btn_order_estimate_cancel.png",
   ),
   //20151104 / minks / spring 스킨 버튼
   "_sys_btn_myinfo_submit"  => array(
      "shape" => "<input type='image' src='--banner--' class='_banner null' code='_sys_btn_myinfo_submit' --event-- style='margin-right:5px;'/>",
      "src"   => "_sys_btn_myinfo_submit.gif",
  ),
  "_sys_btn_myinfo_cancel"    => array(
      "shape" => "<a href='javascript:history.back()'><img src='--banner--' class='_banner' code='_sys_btn_myinfo_cancel' --event-- style='margin-right:5px;'></a>",
      "src"   => "_sys_btn_myinfo_cancel.gif",
  ),
  
  "_sys_btn_register_sms_auth"    => array(
        "shape" => "<a href='javascript:sms_auth_send();'><img src='--banner--' class='_banner' code='_sys_btn_register_sms_auth' style='margin-left:5px;'></a>",
        "src"   => "",
    ),
);
?>