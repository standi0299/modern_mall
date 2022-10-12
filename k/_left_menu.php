<? //include_once dirname(__FILE__) ."/_inc_service_config.php";

if ($super_admin == 1){
   $service_menu = array(
      _("환경설정") => array("display" => _("환경설정"), "link" => "../config/basis.php", "icon" => "class='fa fa-cogs'", "active" => array("basis.php")),
      
      _("기본설정") => array("display" => _("기본설정"), "link" => "../config/basis.php", "icon" => "class='fa fa-cogs'", "active" => array("basis.php")),
      _("기본정보") => array("display" => _("기본정보"), "link" => "../config/basis.php", "icon" => "class='fa fa-cogs'", "active" => array("basis.php")),
      _("회사소개") => array("display" => _("회사소개"), "link" => "../config/company.php", "icon" => "class='fa fa-cogs'", "active" => array("company.php")),
      _("이용약관") => array("display" => _("이용약관"), "link" => "../config/agreement.php", "icon" => "class='fa fa-cogs'", "active" => array("agreement.php")),
      _("개인정보 취급방침") => array("display" => _("개인정보 취급방침"), "link" => "../config/w3c.php", "icon" => "class='fa fa-cogs'", "active" => array("w3c.php")),
      _("몰정보") => array("display" => _("몰정보"), "link" => "../config/mall.php", "icon" => "class='fa fa-cogs'", "active" => array("mall.php")),
      _("메인페이지 접근제어") => array("display" => _("메인페이지 접근제어"), "link" => "../config/mainpage.php", "icon" => "class='fa fa-cogs'", "active" => array("mainpage.php")),
      _("운영설정") => array("display" => _("운영설정"), "link" => "../config/payment.php", "icon" => "class='fa fa-cogs'", "active" => array("payment.php")),
      _("결제서비스관리") => array("display" => _("결제서비스관리"), "link" => "../config/payment.php", "icon" => "class='fa fa-cogs'", "active" => array("payment.php")),
      //_("결제수단별 안내관리") => array("display" => _("결제수단별 안내관리"), "link" => "../config/payment_info.php", "icon" => "class='fa fa-cogs'", "active" => array("payment_info.php")),
      _("택배업체관리") => array("display" => _("택배업체관리"), "link" => "../config/shipcomp.php", "icon" => "class='fa fa-cogs'", "active" => array("shipcomp.php")),
      _("자체배송비설정") => array("display" => _("자체배송비설정"), "link" => "../config/shipprice.php", "icon" => "class='fa fa-cogs'", "active" => array("shipprice.php")),
      _("팝업관리") => array("display" => _("팝업관리"), "link" => "../config/popup.php", "icon" => "class='fa fa-cogs'", "active" => array("popup.php","popup_write.php")),
      _("회원운영관리") => array("display" => _("회원운영관리"), "link" => "../config/member.php", "icon" => "class='fa fa-cogs'", "active" => array("member.php")),
      _("SNS로그인 연동설정") => array("display" => _("SNS로그인 연동설정"), "link" => "../config/sns_login.php", "icon" => "class='fa fa-cogs'", "active" => array("sns_login.php")),
      _("모바일 회원연동설정") => array("display" => _("모바일 회원연동설정"), "link" => "../config/mobile_member.php", "icon" => "class='fa fa-cogs'", "active" => array("mobile_member.php")),
      _("POD모듈설정") => array("display" => _("POD모듈설정"), "link" => "../config/pods_module.php", "icon" => "class='fa fa-cogs'", "active" => array("pods_module.php")),
      _("자체상품관리") => array("display" => _("자체상품관리"), "link" => "../config/release.php", "icon" => "class='fa fa-cogs'", "active" => array("release.php")),
      _("견적서정보관리") => array("display" => _("견적서정보관리"), "link" => "../config/bill.php", "icon" => "class='fa fa-cogs'", "active" => array("bill.php")),
      _("적립금설정") => array("display" => _("적립금설정"), "link" => "../config/emoney_config.php", "icon" => "class='fa fa-cogs'", "active" => array("emoney_config.php")),
      _("관리자설정") => array("display" => _("관리자설정"), "link" => "../config/admin.php", "icon" => "class='fa fa-cog'", "active" => array("admin.php")),
      _("관리자리스트") => array("display" => _("관리자리스트"), "link" => "../config/admin.php", "icon" => "class='fa fa-cog'", "active" => array("admin.php","admin_write.php", "admin_menu_config.php")),
      _("접속로그") => array("display" => _("접속로그"), "link" => "../config/log.php", "icon" => "class='fa fa-cog'", "active" => array("log.php")),
      _("접속아이피설정") => array("display" => _("접속아이피설정"), "link" => "../config/ip.php", "icon" => "class='fa fa-cog'", "active" => array("ip.php")),            

      _("상품관리") => array( "display" => _("상품관리"), "link" => "../goods/goods_list.php", "icon" => "class='fa fa-cubes'", "active" => array("goods_list.php")),

      _("상품관리_A") => array( "display" => _("상품관리"), "link" => "../goods/goods_list.php", "icon" => "class='fa fa-cubes'", "active" => array("goods_list.php")),
      _("상품리스트") => array( "display" => _("상품리스트"), "link" => "../goods/goods_list.php", "icon" => "class='fa fa-cogs'", "active" => array("goods_list.php", "goods_modify.php")),
      _("Center 상품연결") => array( "display" => _("Center 상품연결"), "link" => "../goods/center_goods_connect.php", "icon" => "class='fa fa-cogs'", "active" => array("center_goods_connect.php")),
      _("상품분류") => array( "display" => _("상품분류"), "link" => "../goods/goods_category.php", "icon" => "class='fa fa-cogs'", "active" => array("goods_category.php")),
      
      _("상품 우선순위 설정") => array( "display" => _("상품 우선순위 설정"), "link" => "../goods/goods_orderby.php", "icon" => "class='fa fa-cogs'", "active" => array("goods_orderby.php")),
      
      _("자체상품리스트") => array( "display" => _("자체상품리스트"), "link" => "../goods/self_goods_list.php", "icon" => "class='fa fa-cogs'", "active" => array("self_goods_list.php", "self_goods_modify.php")),
      _("자체상품등록") => array( "display" => _("자체상품등록"), "link" => "../goods/self_goods_regist.php", "icon" => "class='fa fa-cogs'", "active" => array("self_goods_regist.php")),
      _("제작사관리") => array( "display" => _("제작사관리"), "link" => "../goods/release_manage.php", "icon" => "class='fa fa-cogs'", "active" => array("release_manage.php", "release_modify.php", "release_add.php")),

      _("기타설정") => array( "display" => _("기타설정"), "link" => "../goods/etc.php", "icon" => "class='fa fa-cogs'", "active" => array("/goods/etc.php")),

      _("기타기능설정") => array( "display" => _("기타기능설정"), "link" => "../goods/etc.php", "icon" => "class='fa fa-cogs'", "active" => array("etc.php")),

	   _("SNS상품퍼가기 설정") => array( "display" => _("SNS상품퍼가기 설정"), "link" => "../goods/sns.php", "icon" => "class='fa fa-cogs'", "active" => array("sns.php")),

      _("메인화면블록설정") => array( "display" => _("메인화면 블록 설정"), "link" => "../goods/goods_main_block.php", "icon" => "class='fa fa-cogs'", "active" => array("goods_main_block.php")),
      
	   _("아이콘관리") => array( "display" => _("아이콘관리"), "link" => "../goods/icon.php", "icon" => "class='fa fa-cogs'", "active" => array("icon.php")),

      _("회원관리") => array( "display" => _("회원관리"), "link" => "../member/member_list.php", "icon" => "class='fa fa-users'", "active" => array("member_list.php", "member_modify.php")),
      _("회원리스트") => array( "display" => _("회원리스트"), "link" => "../member/member_list.php", "icon" => "class='fa fa-cogs'", "active" => array("member_list.php", "member_modify.php")),
      _("그룹관리") => array( "display" => _("그룹관리"), "link" => "../member/group_list.php", "icon" => "class='fa fa-cogs'", "active" => array("group_list.php")),      
   	_("회원가입설정") => array( "display" => _("회원가입설정"), "link" => "../member/fieldset.php", "icon" => "class='fa fa-cogs'", "active" => array("fieldset.php")),	
   	_("탈퇴회원관리") => array( "display" => _("탈퇴회원관리"), "link" => "../member/leave_list.php", "icon" => "class='fa fa-cogs'", "active" => array("leave_list.php")),
   	_("휴면계정복원") => array( "display" => _("휴면계정복원"), "link" => "../member/quiescence_account_list.php", "icon" => "class='fa fa-cogs'", "active" => array("quiescence_account_list.php")),
   	_("자동메일관리") => array( "display" => _("자동메일관리"), "link" => "../member/auto_email.php", "icon" => "class='fa fa-cogs'", "active" => array("auto_email.php")),
   	_("자동SMS관리") => array( "display" => _("자동SMS관리"), "link" => "../member/auto_sms.php", "icon" => "class='fa fa-cogs'", "active" => array("auto_sms.php")),
   	_("보낸이메일") => array( "display" => _("보낸이메일"), "link" => "../member/log_email.php", "icon" => "class='fa fa-cogs'", "active" => array("log_email.php")),
   	_("SMS로그") => array( "display" => _("SMS로그"), "link" => "../member/log_sms.php", "icon" => "class='fa fa-cogs'", "active" => array("log_sms.php")),

      _("주문관리") => array( "display" => _("주문관리"), "link" => "../order/order_list.php", "icon" => "class='fa fa-shopping-cart'", "active" => array("order_list.php")),

      _("주문리스트") => array( "display" => _("주문리스트"), "link" => "../order/order_list.php", "icon" => "class='fa fa-shopping-cart'", "active" => array("order_list.php")),

      _("일괄처리") => array( "display" => _("일괄처리")),
      _("운송장번호업로드") => array( "display" => _("운송장번호업로드"), "link" => "../order/shipping.php", "icon" => "class='fa fa-shopping-cart'", "active" => array("shipping.php")),
      _("비회원주문연결") => array( "display" => _("비회원주문연결"), "link" => "../order/nonmember_order_connect.php", "icon" => "class='fa fa-shopping-cart'", "active" => array("nonmember_order_connect.php")),

   	_("프로모션") => array( "display" => _("프로모션"), "link" => "../promotion/coupon_list.php?kind=on", "icon" => "class='fa fa-gift'", "active" => array("coupon_list.php"), "QUERY" => array("kind=on")),
   	
   	_("쿠폰리스트A") => array( "display" => _("쿠폰 리스트"), "link" => "../promotion/coupon_list.php?kind=on", "icon" => "class='fa fa-cogs'", "active" => array("coupon_list.php"), "QUERY" => array("kind=on")),
   	_("쿠폰등록A") => array( "display" => _("쿠폰 등록"), "link" => "../promotion/coupon_form.php?kind=on", "icon" => "class='fa fa-cogs'", "active" => array("coupon_form.php"), "QUERY" => array("kind=on")),
   	_("회원별발행리스트A") => array( "display" => _("회원별 발행 리스트"), "link" => "../promotion/coupon_member.php?kind=on", "icon" => "class='fa fa-cogs'", "active" => array("coupon_member.php"), "QUERY" => array("kind=on")),
   
   	_("쿠폰리스트B") => array( "display" => _("쿠폰 리스트"), "link" => "../promotion/coupon_list.php?kind=off", "icon" => "class='fa fa-cogs'", "active" => array("coupon_list.php"), "QUERY" => array("kind=off")),
   	_("쿠폰등록B") => array( "display" => _("쿠폰 등록"), "link" => "../promotion/coupon_form.php?kind=off", "icon" => "class='fa fa-cogs'", "active" => array("coupon_form.php"), "QUERY" => array("kind=off")),
   	_("쿠폰등록리스트") => array( "display" => _("쿠폰 등록 리스트"), "link" => "../promotion/coupon_regist.php", "icon" => "class='fa fa-cogs'", "active" => array("coupon_regist.php")),
   	_("회원별발행리스트B") => array( "display" => _("회원별 발행 리스트"), "link" => "../promotion/coupon_member.php?kind=off", "icon" => "class='fa fa-cogs'", "active" => array("coupon_member.php"), "QUERY" => array("kind=off")),
    _("출석체크이벤트") => array( "display" => _("출석 체크 이벤트"), "link" => "../promotion/attend_event.php", "icon" => "class='fa fa-cogs'", "active" => array("attend_event.php")),
   
   	_("이벤트관리") => array( "display" => _("이벤트관리"), "link" => "../promotion/event_list.php", "icon" => "class='fa fa-cogs'", "active" => array("event_list.php")),
    _("이벤트리스트") => array( "display" => _("이벤트리스트"), "link" => "../promotion/event_list.php", "icon" => "class='fa fa-cogs'", "active" => array("event_list.php", "event_write.php")),
    _("코멘트관리") => array( "display" => _("코멘트관리"), "link" => "../promotion/event_comment.php", "icon" => "class='fa fa-cogs'", "active" => array("event_comment.php")),
   
      _("주문관리_A") => array( "display" => _("주문관리"), "link" => "../order/order_list.php", "icon" => "class='fa fa-shopping-cart'", "active" => array("order_list.php", "order_list.php", "order_list.php")),

      _("취소/환불리스트") => array( "display" => _("취소/환불리스트")),
      _("입금전 취소") => array( "display" => _("입금전 취소"), "link" => "../order/cancelend.php", "icon" => "class='fa fa-shopping-cart'", "active" => array("cancelend.php")),
      _("환불접수리스트") => array( "display" => _("환불접수리스트"), "link" => "../order/refund.php?state=0", "icon" => "class='fa fa-shopping-cart'", "active" => array("refund.php", "refund_modify.php"), "QUERY" => array("state=0", "refundno=")),
      _("환불완료리스트") => array( "display" => _("환불완료리스트"), "link" => "../order/refund.php?state=1", "icon" => "class='fa fa-shopping-cart'", "active" => array("refund.php", "refund_modify.php"), "QUERY" => array("state=1", "refundno=")),
      
      _("편집관리") => array( "display" => _("편집관리")),
      _("편집리스트") => array( "display" => _("편집리스트"), "link" => "../order/editlist.php", "icon" => "class='fa fa-shopping-cart'", "active" => array("editlist.php")),

      _("로그/통계") => array("display" => _("로그/통계"), "link" => "../stat/day.php", "icon" => "class='fa fa-th'", "active" => array("day.php")),
      
      _("로그관리") => array("display" => _("로그관리"), "link" => "../stat/day.php", "icon" => "class='fa fa-th'", "active" => array("day.php")),
      _("일자별접속") => array("display" => _("일자별접속"), "link" => "../stat/day.php", "icon" => "class='fa fa-th'", "active" => array("day.php")),
      _("신규/재방문자") => array("display" => _("신규/재방문자"), "link" => "../stat/visit.php", "icon" => "class='fa fa-th'", "active" => array("visit.php")),
      _("시간별접속") => array("display" => _("시간별접속"), "link" => "../stat/time.php", "icon" => "class='fa fa-th'", "active" => array("time.php")),
      _("호스트별분석") => array("display" => _("호스트별분석"), "link" => "../stat/host.php", "icon" => "class='fa fa-th'", "active" => array("host.php")),
      _("검색어/검색엔진") => array("display" => _("검색어/검색엔진"), "link" => "../stat/search.php", "icon" => "class='fa fa-th'", "active" => array("search.php")),
      _("포털별분석") => array("display" => _("포털별분석"), "link" => "../stat/portal.php", "icon" => "class='fa fa-th'", "active" => array("portal.php")),
      _("통계관리") => array("display" => _("통계관리"), "link" => "../stat/sold_day.php", "icon" => "class='fa fa-th'", "active" => array("sold_day.php")),
      _("일별판매통계") => array("display" => _("일별판매통계"), "link" => "../stat/sold_day.php", "icon" => "class='fa fa-th'", "active" => array("sold_day.php")),
      _("유입경로별 매출통계") => array("display" => _("유입경로별 매출통계"), "link" => "../stat/sold_refer.php", "icon" => "class='fa fa-th'", "active" => array("sold_refer.php")),
      _("결제수단별 매출통계") => array("display" => _("결제수단별 매출통계"), "link" => "../stat/sold_paymethod.php", "icon" => "class='fa fa-th'", "active" => array("sold_paymethod.php")),
      _("카테고리별 매출통계") => array("display" => _("카테고리별 매출통계"), "link" => "../stat/sold_category.php", "icon" => "class='fa fa-th'", "active" => array("sold_category.php")),
      _("주문자별 주문통계") => array("display" => _("주문자별 주문통계"), "link" => "../stat/sold_order.php", "icon" => "class='fa fa-th'", "active" => array("sold_order.php")),
      _("상품별 주문통계") => array("display" => _("상품별 주문통계"), "link" => "../stat/sold_goods.php", "icon" => "class='fa fa-th'", "active" => array("sold_goods.php")),
      _("인화상품별 주문통계") => array("display" => _("인화상품별 주문통계"), "link" => "../stat/sold_print.php", "icon" => "class='fa fa-th'", "active" => array("sold_print.php")),
      _("사용일기준적립금통계") => array("display" => _("사용일기준적립금통계"), "link" => "../stat/use_emoney.php", "icon" => "class='fa fa-th'", "active" => array("use_emoney.php")),
      _("결제일기준적립금통계") => array("display" => _("결제일기준적립금통계"), "link" => "../stat/pay_emoney.php", "icon" => "class='fa fa-th'", "active" => array("pay_emoney.php")),
      _("쿠폰통계") => array("display" => _("쿠폰통계"), "link" => "../stat/coupon.php", "icon" => "class='fa fa-th'", "active" => array("coupon.php")),
	  
      _("게시판관리") => array( "display" => _("게시판관리"), "link" => "../board/board_list.php", "icon" => "class='fa fa-coffee'", "active" => array("board_list.php")),
      _("게시판관리_A") => array( "display" => _("게시판관리"), "link" => "../board/board_list.php", "icon" => "class='fa fa-cogs'", "active" => array("board_list.php")),      
      _("게시글관리") => array( "display" => _("게시글관리"), "link" => "../board/board_list.php", "icon" => "class='fa fa-file-cogs'", "active" => array("board_list.php","board_write.php")),
      _("게시판리스트") => array( "display" => _("게시판리스트"), "link" => "../board/board_set_list.php", "icon" => "class='fa fa-cogs'", "active" => array("board_set_list.php")),
      _("게시판생성") => array( "display" => _("게시판생성"), "link" => "../board/board_set_config.php", "icon" => "class='fa fa-cogs'", "active" => array("board_set_config.php")),
      _("상품문의") => array( "display" => _("상품문의"), "link" => "../board/qna.php", "icon" => "class='fa fa-cogs'", "active" => array("qna.php","qna.w.php")),
	   _("상품후기") => array( "display" => _("상품후기"), "link" => "../board/review.php", "icon" => "class='fa fa-cogs'", "active" => array("review.php","review.w.php")),
	   _("편집왕") => array( "display" => _("편집왕"), "link" => "../board/edking.php", "icon" => "class='fa fa-cogs'", "active" => array("edking.php")),
	   _("편집왕관리") => array( "display" => _("편집왕관리"), "link" => "../board/edking.php", "icon" => "class='fa fa-cogs'", "active" => array("edking.php")),
      _("편집왕리스트") => array( "display" => _("편집왕리스트"), "link" => "../board/edking_list.php", "icon" => "class='fa fa-cogs'", "active" => array("edking_list.php")),

      _("포인트관리") => array( "display" => _("포인트관리"), "link" => "../order/point_list.php", "icon" => "class='fa fa-file-powerpoint-o'", "active" => array("point_list.php")),

      _("공지사항") => array( "display" => _("공지사항"), "link" => "../board/notice.php", "icon" => "class='fa fa-info'", "active" => array("notice.php", "notice.w.php")),
      _("FAQ") => array( "display" => _("FAQ"), "link" => "../board/faq.php", "icon" => "class='fa fa-comments-o'", "active" => array("faq.php")),
      _("11문의") => array( "display" => _("1:1문의"), "link" => "../board/cs.php", "icon" => "class='fa fa-question'", "active" => array("cs.php", "cs.w.php")),

      _("SEO설정") => array( "display" => _("검색엔진 노출 설정"), "link" => "../extra/seo_config.php", "icon" => "class='fa fa-question'", "active" => array("seo_config.php")),
      _("디자인설정") => array( "display" => _("디자인 설정"), "link" => "../config/design_config.php", "icon" => "class='fa fa-question'", "active" => array("design_config.php")),

	_("추천검색어") => array( "display" => _("추천 검색어"), "link" => "../config/search_word.php", "icon" => "class='fa fa-cogs'", "active" => array("search_word.php")),
      
     );
}
$active_page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/", -1) + 1);

?>
<? if ($include_header) { ?>
<!-- begin #sidebar -->
<div id="sidebar" class="sidebar">
   <!-- begin sidebar scrollbar -->
   <div data-scrollbar="true" data-height="100%">
      <!-- begin sidebar user -->
      <ul class="nav">
         <li class="nav-profile">
            <div class="info"><?=$sess_admin[name]?><?=_("님")?>
               <small><?=_("환영합니다.")?></small>
            </div>
         </li>
      </ul>
      <!-- end sidebar user -->

      <!-- begin sidebar nav -->
      <ul class="nav">
         <li class="nav-header"><?=_("관리메뉴")?></li>
<?
//관리자 설정 메뉴가 있다면..
	if ($sess_admin[menu_priv])
	{
		$admin_config[allow_left_menu] = unserialize($sess_admin[menu_priv]); 
	}
   
	foreach ($admin_config[allow_left_menu] as $key => $value) 
	{

		if (is_array($value)) {
			$hasChildClass = "has-sub";

			if ($service_menu[$value[0]])
				$leftMenu = $service_menu[$value[0]];
			else {
				//$service_menu 설정에 없을 경우 메뉴명설정. icon 은 기본 아이콘으로 처리.
				$leftMenu[display] = $value[0];
				$leftMenu[icon] = "class='fa fa-cogs'";
				$leftMenu[active] = "";
     	}  
		} else {
			$hasChildClass = "";
			$leftMenu = $service_menu[$value];
		}

		$active_tag = "";
		if ($leftMenu[active]) 
		{
    	if (in_array($active_page, $leftMenu[active])) 
    	{
      	if ($leftMenu[QUERY]) {                  	
        	if (in_array($_SERVER["QUERY_STRING"], $leftMenu[QUERY])) 
          	$active_tag = "active";
        } else 
        	$active_tag = "active";
      	if ($leftMenu[target]) $link_target = " target='$leftMenu[target]'";
     	}
  	}

   	//지사로 로그인 했을 때 나오지 않는 목록은 아예 보여주지 않기 위해 if문 추가 / 16.12.15 / kjm
    if($leftMenu[link])
    {   		
   		if ($hasChildClass == "has-sub") {
   			$menuTag = "<li class='$hasChildClass parent_active_tag'>\r\n";
				$menuTag .= "<a href='javascript:;'><i $leftMenu[icon]></i> ";
   			$menuTag .= "<b class='caret pull-right'></b>";
   		} else {
   			$menuTag = "<li class='$hasChildClass $active_tag'>\r\n";
   			$menuTag .= "<a href=\"$leftMenu[link]\" $link_target><i $leftMenu[icon]></i> ";
			}

			$menuTag .= "<span name=\"$leftMenu[display]\">$leftMenu[display]</span></a>\r\n";

   		if ($hasChildClass == "has-sub") {
				$subNodeTag = makeKadminLeftSubMenu($value, true);				//lib_util.php 함수 있음.

   			//하위 메뉴에 활성화가 있을경우 부모 메뉴도 활성화 처리를 해야 메뉴가 펼쳐진다.
   			if (strpos($subNodeTag, "active") !== FALSE)
					$menuTag = str_replace("parent_active_tag", "active", $menuTag);

   			$menuTag .= $subNodeTag;
			}
   		$menuTag = str_replace("parent_active_tag", "", $menuTag);

   		$menuTag .= "</li>\r\n";
   		echo $menuTag;
		}
 	}

         ?>
         <!-- begin sidebar minify button -->
         <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
         <!-- end sidebar minify button -->
      </ul>
      <!-- end sidebar nav -->
   </div>
   <!-- end sidebar scrollbar -->
</div>
<?	}	?>
<!-- end #sidebar -->