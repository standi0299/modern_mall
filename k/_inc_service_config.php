<?

$admin_config = array(
	"main_title" => _("BluePod Admin"),
	"main_icon" => "fa-camera-retro",
	"index_file" => "index.php",
	"mall_index_file" => "/",
	"navbar_header_class" => "navbar-inverse",
	"css_theme" => "<link href='../assets/css/theme/red.css' rel='stylesheet' id='theme' />",
	"visible_pannel" => array(
      "order_order_chart_bottom_button_bluephoto",
   ),

   //파일 설정이 필요한 경우만 지정한다.
   "include_file" => array(
      "order_list_header_status" => "_div_order_list_header_status.php",
      "order_list_search_A" => "_div_order_list_search.php",
   ),

   
	"allow_left_menu" => array(
		array(_("환경설정"),
   		 array(_("기본설정"),_("기본정보"),_("회사소개"),_("이용약관"),_("개인정보 취급방침"),_("몰정보"),_("메인페이지 접근제어"), _("SEO설정")),
   		 array(_("운영설정"),_("결제서비스관리"),_("택배업체관리"),_("자체배송비설정"),_("팝업관리"),_("회원운영관리"),_("적립금설정"),_("SNS로그인 연동설정"),_("모바일 회원연동설정"),_("디자인설정"),_("POD모듈설정"),_("자체상품설정")),
   		 array(_("관리자설정"),_("관리자리스트"),_("접속로그"),_("접속아이피설정"))),
	  array(_("상품관리"),	   	
	   	array(_("상품관리"), _("상품리스트"),_("Center 상품연결"),_("상품분류")),

	   	array(_("자체상품관리"), _("자체상품리스트"),_("자체상품등록"),_("상품 우선순위 설정")),
	   	array(_("기타설정"), _("제작사관리"),_("기타기능설정"),_("SNS상품퍼가기 설정"),_("메인화면블록설정"),_("아이콘관리"),_("추천검색어"))),

		array(_("회원관리"),
	  	array(_("회원관리"),	_("회원리스트"),_("그룹관리"),_("회원가입설정"),_("탈퇴회원관리"),_("휴면계정복원"),),
	  	array(_("기타설정"), _("자동메일관리"),_("자동SMS관리"),_("보낸이메일"),_("SMS로그"))),

	 	array(_("주문관리"),
	 		array(_("주문관리"),_("주문관리_A"),),
	 		array(_("일괄처리"),_("운송장번호업로드"),_("비회원주문연결")),
	 		array(_("취소/환불관리"),_("입금전 취소"),_("환불접수리스트"),_("환불완료리스트")),
	 		array(_("편집관리"),_("편집리스트"))),

		array(_("프로모션"), 
			array(_("온라인 쿠폰 관리"), _("쿠폰리스트A"), _("쿠폰등록A"), _("회원별발행리스트A")), 
			array(_("오프라인 쿠폰 관리"), _("쿠폰리스트B"), _("쿠폰등록B"), _("쿠폰등록리스트"), _("회원별발행리스트B")),
			array(_("이벤트관리"),_("이벤트리스트"),_("코멘트관리")),
			array(_("기타설정"),_("출석체크이벤트"))),

		array(_("로그/통계"),
         array(_("로그관리"),_("일자별접속"),_("신규/재방문자"),_("시간별접속"),_("호스트별분석"),_("검색어/검색엔진"),_("포털별분석")),
         array(_("통계관리"),_("일별판매통계"),_("유입경로별 매출통계"),_("결제수단별 매출통계"),_("카테고리별 매출통계"),_("주문자별 주문통계"),_("상품별 주문통계"),_("인화상품별 주문통계"),_("사용일기준적립금통계"),_("결제일기준적립금통계"),_("쿠폰통계"))),	   
      array(_("게시판관리"),

	  	array(_("게시판관리"), _("게시글관리"), _("게시판리스트"), _("게시판생성")),
	  	array(_("고객센터"), _("공지사항"), 	_("11문의"), "FAQ", 	_("상품문의"), _("상품후기")),
	  	array(_("편집왕"), _("편집왕관리"),_("편집왕리스트"))),
      array(_("포인트관리"),
	  	array(_("포인트관리"),_("포인트관리"),)),
	),
);

//admin_config 에 설정된 파일 있는지 체크하여 출력한다.
function setAdminIncudeFile($code, $folder_name = '') {
   global $cfg_center, $admin_config, $count, $r_manager, $super_admin, $branch, $selected, $season_list;

	$config_include_file = "";
	$config_include_folder = $folder_name;
	if ($admin_config[include_file][$code])
		$config_include_file = $admin_config[include_file][$code];
	else
		$config_include_file = "_div_" .$code. "_" .$cfg_center[service_kind]. ".php";			//기본적인 파일명 규칙.

	if (! $config_include_folder)
		$config_include_folder = substr($code, 0, strpos($code, '_'));

   //버튼 색이 변경되지 않아 추가 / 16.07.06 / kjm
   if (!$_POST[date_buton_type]) $_POST[date_buton_type] = "week";
   $button_color = array("yesterday" => "inverse","today" => "inverse","tdays" => "inverse","week" => "inverse","month" => "inverse","all" => "inverse");
   if ($_POST[date_buton_type]) {
      $button_color[$_POST[date_buton_type]] = "warning";
   }

	if (file_exists(dirname(__FILE__) ."/include/$config_include_folder/".$config_include_file))
		include_once dirname(__FILE__) ."/include/$config_include_folder/".$config_include_file;
}
?>