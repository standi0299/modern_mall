<?
/*
* @date : 20190320
* @author : kdk
* @brief : 견적 상품 추가 현수막(Placard),실사출력(RealPrint)
* @desc : 현수막 (PR01),실사출력(PR02)
*/

/*
* @date : 20190304
* @author : kdk
* @brief : 데칼코마니 추천상품 섹션 추가
* @request : I1
* @desc :
*/
/*
* @date : 20190131
* @author : kdk
* @brief : 태웅(범아) 테마 추가.
* @request : B1
* @desc :
*/

/*
* @date : 20181118
* @author : kdk
* @brief : 사이별 설정 변경.
* @request : PODS20_DOMAIN cid별로 사용 추가.
* @desc : lib.cust.php 에서 선언($cid._local_const.php)
*/

/*
* @date : 20181105
* @author : kdk
* @brief : pod 알래스카 관련 설정추가.
* @request : 알래스카.
* @desc : 입금방법($r_deposit_method), 주문상태($r_pay_status)
*/

/*
* @date : 20180808
* @author : kdk
* @brief : wpod ver2 편집기를 지원하는 편집기 타입추가.
* @request : 알래스카프린트 적용.
* @desc : $r_podskind_wpod_ver2 = array(3040, 3041, 3050, 3051, 3052, 3055);
*/

/*
* @date : 20180226
* @author : chunter
* @brief : skin을 theme로 분리하면서 템플릿_ class 모듈에서 htm을 자동 변환한다. 이에 따라 자동 변환할 layout 설정 필요. 설정하지 layout 은 기본 공통으로 사용
* @request : 미오디오, 유니큐브 디자인을 변경
* @desc : Template_.class 파일 내부 구조를 변경함
*/
?>
<?
//2014.02 월 이후 추가되는 const 배열 변수들.
define("TODAY_Y_M_D", date("Y-m-d"));
define("TODAY_YMD", date("Ymd"));


define("THIS_MONTH_Y_M_D", date("Y-m-01"));			//이번달 1일
define("LAST_MONTH_Y_M_D", date("Y-m-01", strtotime("-1 month", mktime(0,0,0, date("m"), 1, date("Y")))));			//지난달 1일


define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);
define("CURRENT_FILE_NAME", basename($_SERVER[PHP_SELF]));     //현재 파일명.
define("CURRENT_FILE_PATH", dirname($_SERVER[PHP_SELF]));      //현재 경로명.
define("CURRENT_FILE_PATH_NAME", CURRENT_FILE_PATH ."/". CURRENT_FILE_NAME);      //현재 경로/파일명.

define("USER_USER_AGENT", $_SERVER['HTTP_USER_AGENT']);      //agent.
define("USER_REFERER", $_SERVER['HTTP_REFERER']);
define("USER_IP", $_SERVER['REMOTE_ADDR']);      //접속 IP.
define("USER_HOST", $_SERVER['HTTP_HOST']);      //접속 host.
define("NOW_FILE", $_SERVER["SCRIPT_FILENAME"]);
define("USER_FULL_URL", $_SERVER['REQUEST_URI']);
define("SERVER_DOMAIN", "http://".USER_HOST);
//define("PHP_SELF", $_SERVER['PHP_SELF']);     //해당 define 처리시 $_SERVER['PHP_SELF'] 값이 사라지는 버그 현상    20150206

define("DEFAULT_STORAGE_PRICE", "110000");      //스토리지 기본 1달 금액.      20150409    chunter
define("DEFAULT_TAX_ILARK_EMAIL", "tax@ilark.co.kr");   //tax@ilark.co.kr      //세금계산 요청 아이락 메일 주소     20150422    chunter
define("ADMIN_LOGIN_FAIL_COUNT", 5);   //관리자 로그인 실패 횟수 (인증코드 생성)     20150916    chunter

define("AES_ENCRYPT_PASSWD", "dkdlFKR146)(*eoqkr!");   //aes 암호화 비밀번호     20160531    chunter

//현수막,실사출력 가격 설정 방식
$r_extra_print_pr_price_type = array(
"SIZE" => '규격별 단가',
"MM" => '1mm당 단가'
);

### pod 알래스카 관련 설정 ###
//pod_deposit_money 알래스카 입금방법 / 20181105 / kdk
$r_deposit_method = array(
    '1' => "국민",
    '2' => "국민1",
    '3' => "국민2",
    '4' => "우리",
    '5' => "제일",
    '6' => "현금",
    '7' => "카드단말기",
    '8' => "온라인카드(KCP)",
    '9' => "온라인계좌이체(KCP)",
    '10' => "어음",
    '11' => "대체",
    '12' => "이월",
);
//pod_pay 주문상태 / 20181105 / kdk
$r_pay_status = array(
    '1' => "접수대기",
    '2' => "접수완료",
    '3' => "출고완료",
    '-9' => "주문취소",
);

//skin theme 별 파일 분류할  템플릿_ layout			20180226		chunter
$r_skin_theme_name = array("M1", "M2", "M3" , "I1", "P1", "B1", "H1");
$r_skin_theme_layout = array("tpl", "layout", "top", "left", "right", "bottom", "header", "footer", "header_popup", "footer_popup");

$r_skin_theme_exclude_tpl = array("service/company.htm", "service/policy.htm", "main/page.htm");            //테마 적용에서 빠지는 파일

//cache 파일을 사용할 파일 목록
$r_temple_cache_file = array(
	//array("goods/view.htm", 3600), 		//1hour
	//array("service/company.htm", 86400),			//1day
	//array("layout/layout.htm", 3600)
);

//debug 함수 출력되는 IP 설정.			20160726	chunter
//211.212.5.200 독산 사무실 ip 추가   20211001 jtkim
$r_debug_IP = array("localhost", "210.96.184.229", "211.212.5.200");

//PG 사 종류				20160713		chunter
$r_pg_kind = array(
	"kcp" => "KCP Payplus V6 AX HUB ",
	"inipaystdweb"=>_("이니시스 표준결제(통합)"),
	"smartxpay" =>"토스페이먼츠(모바일)",
	"inicis" =>"INICIS INIPay TX5.0",
	"lg" =>"토스페이먼츠",
	"easypay80"=>"EasyPay"
	//"lg" =>"LGU+ XpayLite"
	//"smartxpay" =>"LGU+ SmartXpay",
	//"epsilon_jp" =>"Epsilon(Japan)",
	//"tenpay" => "WeChat"
);

$r_emoney_status = array(
	'1' => _("적립"),
	'2' => _("사용"),
	'4' => _("환불"),
	'3' => _("만료"),
);

//모던 특정 페이지 별 스크립트 삽입    20191112 jtkim
$r_script_page_list = array(
   'payment_script' => _("payend.php"),
   'register_ok_script' => _("register.php"),      // register_ok 페이지로 호출되지 않는 경우 indb에서 reffer위치로 확인
);

//모던 상품 리스트 형식 php - 테마별 관리한다.
$r_goods_design_list['M1'] = array(
   'list' => _("기본 리스트"),
   'list_print' => _("인화 상품 리스트"),
   'list_template' => _("템플릿 상품 리스트 (단면)"),
   'list_templateset' => _("템플릿셋 상품 리스트 (양면)"),
   //'intro' => _("미오디오 인트로 페이지"),
   'intro_banner' => _("기본 인트로 페이지"),
   'list_interpro' => _("인터프로 리스트[minterpro.homeprint, minterpro.interproindigo, mcdev.mmdev, mcdev.mmdevtest2, mprione.service]"),
   'list_view_interpro' => _("인터프로 뷰 + 템플릿 리스트[minterpro.homeprint, minterpro.interproindigo, mcdev.mmdev, mcdev.mmdevtest2, mprione.service]"),
   'view_template.alaskaprint' => _("알래스카 템플릿 리스트[malaska.alaskaprint, malaska.alaskaindigo, mcdev.mmdev, mcdev.mmdevtest2]"),
);
$r_goods_design_list['M2'] = array(
   'list_sub_category' => _("서브 카테고리 있는 형태"),
   'list_sub_category_no' => _("서브 카테고리 없는 형태"),
);
$r_goods_design_list['M3'] = array(
   'list_sub_category' => _("서브 카테고리 있는 형태"),
   'list_sub_category_no' => _("서브 카테고리 없는 형태"),
);
$r_goods_design_list['I1'] = array(
   'list' => _("기본 리스트"),
);
$r_goods_design_list['P1'] = array(
   'list' => _("기본 리스트"),
   'intro' => _("인트로 페이지"),
);
$r_goods_design_list['B1'] = array(
   'list' => _("기본 리스트"),
   'list_template' => _("템플릿 상품 리스트 (단면)"),
   //'list_templateset' => _("템플릿셋 상품 리스트 (양면)"),
);

//모던 상품 뷰 형식 php	- 테마별 관리한다.
$r_goods_design_view['M1'] = array(
   'view' => _("기본 뷰"),
   'view_horizon' => _("기본 뷰 (가로)"),
   //'view_print' => "인화 상품 뷰", / 20170907 / 기본 뷰 사용하도록 수정 / kdk
);
$r_goods_design_view['M2'] = array(
   'view' => _("기본 뷰"),
);
$r_goods_design_view['M3'] = array(
   'view' => _("기본 뷰"),
);
$r_goods_design_view['I1'] = array(
   'view' => _("기본 뷰"),
);
$r_goods_design_view['P1'] = array(
   'view' => _("기본 뷰"),
);
$r_goods_design_view['B1'] = array(
   'view' => _("기본 뷰"),
);

//모던 메인 블럭 코드	- 테마별 관리한다.
$r_main_block['M1'] = array(
  'main_block_01' => _("메인상품1"),
  'main_block_02' => _("메인상품2"),
  'main_block_03' => _("메인상품3"),
  'main_block_04' => _("메인상품4"),
  'main_block_05' => _("메인상품5"),
  'main_block_06' => _("메인상품6"),
  'main_block_07' => _("메인상품7"),
  'main_block_08' => _("메인게시판"),
  'main_block_09' => _("메인배너+후기"),
  'main_block_10' => _("메인게시판+배너"),
  'main_block_11' => _("메인상품11-CANVAS"),
  'main_block_12' => _("메인배너-3개"),
);
$r_main_block['M2'] = array(
  'main_block_01_M2' => _("미오디오-M2"),
);
$r_main_block['M3'] = array(
   'main_block_01_M3' => _("미오디오-M3"),
 );
$r_main_block['I1'] = array(
  'main_block_01_I1' => _("아이스크림몰-I1"),
  'main_block_02_I1' => _("아이스크림몰(추천상품)-I1"),
);
$r_main_block['P1'] = array(
  'main_block_01_P1' => _("Pixstory 상품리스트"),
  'main_block_02_P1' => _("Pixstory 편집&후기"),
  'main_block_03_P1' => _("Pixstory 이벤트페이지"),
);
$r_main_block['B1'] = array(
  'main_block_01' => _("메인상품1-B1"),
  'main_block_02' => _("메인상품2-B1"),
  'main_block_03' => _("메인상품3-B1"),
  'main_block_04' => _("메인상품4-B1"),
  'main_block_05' => _("메인상품5-B1"),
  'main_block_06' => _("메인상품6-B1"),
  'main_block_07' => _("메인상품7-B1"),
  'main_block_08' => _("메인게시판-B1"),
  'main_block_09' => _("메인배너+후기-B1"),
  'main_block_10' => _("메인게시판+배너-B1"),
  'main_block_11' => _("메인상품11-CANVAS-B1"),
  'main_block_12' => _("메인배너-3개-B1"),
);

//모던 패키지 상품 관련 PODs20 일반상품  사이트상품코드 매핑 정의 (exm_cart.est_pods_version에 저장) / 20180119 / kdk
$r_site_product_code_pack = array(
    '1' => 'mm015571', //편집기 사용안하는 일반상품
    '2' => 'mm771201', //파일업로드하는 일반상품
    '3' => 'mm422159', //파일업로드하는 자동견적상품
);

//k관리자 - 포인트관리 - 충전금액, 사용금액 구분값 / 15.07.02 / kjm
$r_pretty_point_list_chk_flag = array("11", "12", "13", "14");

//모던 메인 블럭 상품 매핑이 가능한 코드
$r_main_block_mapping['M1'] = array(
	'main_block_01', 'main_block_02', 'main_block_03', 'main_block_04', 'main_block_05', 'main_block_06', 'main_block_07', 'main_block_11',
);
$r_main_block_mapping['M2'] = array(
	'main_block_01_M2',
);
$r_main_block_mapping['M3'] = array(
	'main_block_01_M3',
);
$r_main_block_mapping['I1'] = array(
	'main_block_01_I1','main_block_02_I1'
);
$r_main_block_mapping['P1'] = array(
	'main_block_01_P1',
);
$r_main_block_mapping['B1'] = array(
    'main_block_01', 'main_block_02', 'main_block_03', 'main_block_04', 'main_block_05', 'main_block_06', 'main_block_07', 'main_block_11',
);

//모바일 결제가 가능한 스킨들				//20160930			chunter
$r_mobile_PG_allow_skin = array(
  'modern','m_default',
);

//몰 생성시 html_public 폴더를 만들지 않고 기본 data 폴더내에 업체별 스킨을 생성하여 처리하는 스킨들			//20160429
$r_not_make_public_skin = array(
  'modern',
);


//defense 모듈 사용 스킨들    20151021  chunter
$r_defense_use_skin = array(
  'modern',
);

//일반,단체 상품        20150428    chunter
$r_goods_member_mapping_kind = array(
  '1' => _("일반상품"),
  '2' => _("단체상품"),
);

//print group 포인트 결제 금액 (포인트 => 금액) / 16.08.30 / kdk
$r_printgroup_point_price = array(
  '10000' => '10000',
  '20000' => '20000',
  '30000' => '30000',
  '50000'  => '50000',
  '100000' => '100000',
  '200000' => '200000',
  '500000' => '500000',
);

//print group 서비스 사용기간 (기간 => 금액) / 16.09.22 / kdk
$r_printgroup_service_month = array(
  '1' => '99000',
  '2' => '198000',
  '3' => '297000',
  '4' => '396000',
  '5' => '495000',
  '6' => '594000',
  '7' => '693000',
  '8' => '792000',
  '9' => '891000',
  '10' => '990000',
  '11' => '1089000',
  '12' => '1188000',
);

$r_allow_cart_update_page = array(
  'index.php',
  'list.php',
  'view.php',
  'cart.php',
  'order.php',
  'main.php',
  'child_edit_list.php'
);

//유치원 편집보관함 상태값 정리      20150324    chunter
//E: edit, C:cart, R:request order, O : ordered
$r_cart_state = array(
  'E' => _("편집중"),
  'C' => _("장바구니"),
  'R' => _("원장에게 주문요청"),
  'O' => _("주문완료"),
  'M' => _("승인완료"),
  'F' => _("승인반려"),
);

//배송 수단   "4" 착불 배송 추가 (기능 개발 해야함)     20140825   chunter
//->작불배송은 주문시 사용자 선택 배송 수단으로 기능 개발하기로 함 20141101  chunter
$r_shiptype = array(
  '0' => _("일반배송"),
  '1' => _("무료배송"),
  '2' => _("개별배송"),
  '3' => _("조건부배송"),
  '4' => _("착불배송"),
  '8' => _("퀵서비스"),
  '9' => _("방문수령"),
);

//주문시 사용자 선택 배송 수단      //20141016  chunter
$r_order_shiptype = array(
  '0' => _("일반배송(택배,우편)"),
  '4' => _("택배 착불배송"),
  '5' => _("퀵서비스 착불배송"),
  '9' => _("방문수령"),
);

//bluepod => pods 배송 방법 코드 맴핑     20140922    chunter
$r_shiptype_pods_deliveryKind  = array(
  '0' => '01',    //택배
  '1' => '01',
  '2' => '01',
  '3' => '01',
  '4' => '06',    //착불
  '5' => '04',    //퀵서비스
  '8' => '04',
  '9' => '99',    //방문수령
);

//결제 분류. 여러가지 중복 결제 수단이 있다.       20141211    chunter
//미수금 dc => de 로 수정 / 14.12.30 / kjm
$r_pay_kind  = array(
  'c' => _("신용카드"),
  "v" => _("가상계좌"),
  "o" => _("계좌이체"),
  "h" => _("휴대폰결제"),
  "ve"=> _("에스크로(가상계좌)"),
  "oe"=> _("에스크로(계좌이체)"),
  'b' => _("무통장"),
  "t" => _("신용거래1"),
  'e' => _("적립금"),
  'de' => _("미수금"),			//신용거래 2로 칭함
  'da' => _("예치금"),  		//신용거래 2로 칭함
);

//입금 상태     20141215    chunter
$r_paystep = array(
  '1' => _("미입금"),
  '2' => _("입금완료"),
  '91' => _("승인요청"),
  '92' => _("승인완료"),
  '-9' => _("주문취소"),
  '-90' => _("승인반려"),
);

$r_coupon_type = array(
  "discount"  => _("가격할인"),
  "saving"  => _("추가적립"),
  "fix_date" => _("기간연장"),
  "point_save" => _("적립금전환"),
  "coupon_money" => _("정액할인"),
  "sale_code" => _("프로모션코드"),
);
$r_coupon_way = array(
  "price"   => _("정액"),
  "rate"    => _("정률"),
  "fdate" => _("기간연장"),
);

$r_mall_state = array(
  _("승인"),
  _("미승인"),
  _("차단"),
  _("운영중")
);

$r_accstep = array(
    0   => _("대기"),
    9   => _("완료"),
);

$r_acckind = array(
    2   => _("결제"),
    -2  => _("결제취소"),
    5   => _("출고"),
    -5  => _("출고취소"),
    9   => _("배송비"),
);

/* 편집기번호 */
$r_podskind = array(
    0       => _("일반상품"),
    1       => _("일반인화3.0"),
    2       => _("고급인화3.0"),
    3       => _("증명인화2.7"),
    4       => _("포토팬시2.7"),
    5       => _("포토앨범/북2.7"),
    6       => _("포토달력2.7"),
    7       => _("업로드3.0"),
    12      => _("편집인화2.7"),
    25      => _("포토앨범/북3.0(내지고정)"),
    27      => _("포토청첩장"),
    28      => _("포토명함3.0"),
    24      => _("포토팬시3.0"),
    26      => _("포토달력3.0"),
    31      => _("포토앨범/북3.0(내지/책등가변)"),
    33      => _("포토앨범/북3.0(스크랩)"),
    32      => _("포토앨범/북3.0(내지가변)"),
    35      => _("포토앨범/북3.0(원클릭)"),
    34      => _("포토앨범/북3.0(스크랩책등고정)"),
    3130    => _("브로셔편집기3.5"),
    3140    => _("현수막편집기"),
    3110    => _("간편편집기"),
    3180    => _("명함편집기3.5"),

    //3050  => "앨범/북3.5",     20140526
    /*
    3051    => "앨범/북3.5(내지가변)",
    3052    => "앨범/북3.5(커버,내지가변)",
    3110    => "간편편집기3.5",
    3111    => "POD복수주문편집기",
    3130    => "브로셔3.5",
    3140    => "현수막편집기3.5",
    3180    => "명함3.5",
    3230    => "표지편집기3.5",
    */
    );

/* 2.0편집기 */
$r_podskind20 = array(
    9999, //피규어,
    99999, //피규어,
    1001, //WPodCardEditor
    1002, //WPodCoverEditor
    1003, //WPodPhoneEditor
    1005, //WPodYelloKitEditor
    1006, //WPodEnvelopeEditor
    1007, //WPodLeafletEditor
    1008, //WPodLeaflet2Editor
    3010, //4.0인화 편집기
    3011,
    //3050,
    3051,
    3052,
    3230,
    3231,
    3030,
    3040, //4.0팬시 편집기
    3041, //4.0멀티 팬시 편집기
    3050,   //pods2 전용으로 설정   20140526    chunter
    3112, //싱글 주문 편집기 PODIlark30
    3113,   //멀티 주문 편집기 PODIlark35
    /* 3110편집기가 pods1.0 2.0 모두 연동되어있어서 2.0에서 제거함 / 14.03.31 / kjm
    3110,
    3051,
    3052,
    3110,
    3111,
    3130,
    3140,
    3180,
    3230,
    */
    3240, //초간단 포토북
    3042, //4.0 액자편집기
    3043, //4.0 팬시그룹편집기
    3060, //4.0 달력편집기
    3053, //4.0 자동완성북(커버가변)
    3054, //4.0 자동완성북(커버고정)
    3020,  //4.0 프롤인화편집기
    3055
    );

/* 2.0 WPOD편집기 */
$r_podskind30 = array(
    1001, //WPodCardEditor
    1002, //WPodCoverEditor
    1003, //WPodPhoneEditor
    1005, //WPodYelloKitEditor
    1006, //WPodEnvelopeEditor
    1007, //WPodLeafletEditor
    1008, //WPodLeaflet2Editor
    );

/* WPOD Ver2 편집기 */
$r_podskind_wpod_ver2 = array(3040, 3041, 3050, 3051, 3052, 3055, 3010, 3011, 3230, 3231, 3030, 3112, 3113, 3240, 3042, 3060, 3053, 3054, 3020);

// 유치원 날짜 임시 데이터 / 15.06.04 / kjm
$r_year = array('2000','2001','2002','2003','2004','2005','2006','2007','2008','2009','2010','2011','2012','2013','2014','2015');
$r_month = array('1','2','3','4','5','6','7','8','9','10','11','12');
$r_day = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');

$r_deposit_pay_flag = array(
   '1' => _("사용자 결제"),
   '2' => _("관리자 승인반려"),
   '3' => _("관리자 충전"),
   '4' => _("사용자 충전"),
   '5' => _("관리자 주문취소"),
   '6' => _("관리자 주문취소 재결제"),
);

$r_order_msg = array(
   '1' => _('배송전 연락주세요.'),
   '2' => _('경비실에 맡겨주세요.'),
   '3' => _('문 앞에 놔주세요.'),
);

$r_phone = array('02','031','032','033','041','042','043','044','051','052','053','054','055','061','062','063','064','070','010');
$r_mobile = array('010','011','016','017','018','019');
$r_email = array(
    _("naver.com")   => 'naver.com',
    _("nate.com")   => 'nate.com',
    _("daum.net")   => 'daum.net',
    _("gmail.com")   => 'gmail.com',
    _("hotmail.com")   => 'hotmail.com',
);

$r_goods_state = array(_("판매"),_("품절"),_("보류"));
$r_member_state = array(_("승인"),_("미승인(보류)"),_("차단"));
$r_receive = array(_("거부"),_("수신"));

//기존 스튜디오 개발진행 시 미수금, 예치금 결제 방식을 신용거래2로 통합 / 14.09.17 / kjm
$r_paymethod = array(
    "c" => _("카드결제"),
    "v" => _("가상계좌"),
    "o" => _("계좌이체"),
    "h" => _("휴대폰결제"),
    "ve"=> _("에스크로(가상계좌)"),
    "oe"=> _("에스크로(계좌이체)"),
    "b" => _("무통장입금"),
    "t" => _("신용거래"),
    "e" => _("적립금결제"),
    'd' => _("미수거래"),
    'f' => _("정액제"),
    'k' => _("Kakao Pay"),
    //'ST' => _("T멤버할인"),
    'kp' => _("Kakao Pay"),
);
$r_step = array(
    0   => _("결제시도"),
    1   => _("미입금"),
    2   => _("입금완료"),
    91  => _("승인요청"),
    92  => _("승인완료"),
    3   => _("상품제작중"),
    4   => _("발송대기"),
    5   => _("출고완료"),
    -1  => _("결제실패"),
    -9  => _("주문취소"),
    -90 => _("승인반려"),
    11  => _("환불접수"),
    19  => _("환불완료"),
    81  => _("시안 제작요청"),
    82  => _("시안 검수요청"),
    83  => _("시안 검수완료"),
);

//신용거래 충전 관리 / 14.04.21 / kjm
$r_deposit_flag = array(
    1   => _("결제"),
    2   => _("사용자충전"),
    3   => _("관리자충전")
);


$r_escrow = array(_("일반주문"), _("에스크로전체"), _("에스크로미정산"));

//$r_title = array("회원가입","주문접수","입금요청","입금확인","발송완료","송장번호","주문취소","환불완료","상품품절","아이디찾기","비밀번호찾기","생일축하","자동주문취소","1:1 문의 답변");
//$r_title = array("회원가입","주문접수","입금확인","발송완료","비밀번호찾기","게시판-글등록","게시판-답글등록");
$r_title = array(_("회원가입"), _("비밀번호찾기"), _("게시판-글등록"), _("게시판-답글등록"), _("견적신청"), _("샘플신청"), _("주문접수"), _("접수내역"), _("입금확인"), _("발송완료"), _("주문취소"), _("관리자충전"), _("1:1문의-답글등록"), _("편집기간만료"), _("적립금만료"));

$r_sex = array(f => _("여자"), m => _("남자"));
$r_catnm = array(
    '0' => _("전화"),
    '1' => _("주문")
    );
$r_importance = array(
    '0' => _("낮음"),
    '1' => _("보통"),
    '2' => _("높음")
    );
$r_state = array(
    '0' => _("미처리"),
    '1' => _("처리")
    );


//행자부 우편번호 검색 API 키 관리 			20160831			chunter
//https://www.juso.go.kr/addrlink/main.do
$r_zipcode_api_key = array(
	"112.175.54.235" => "U01TX0FVVEgyMDE2MDgzMTE0NDY0MDE0OTc1",
	"112.175.54.239" => "U01TX0FVVEgyMDE2MDgzMTE0NDY0MDE0OTc3",
	"112.175.54.245" => "U01TX0FVVEgyMDE2MDgzMTE0NDY0MDE0OTc4",
	"210.96.184.229" => "U01TX0FVVEgyMDE2MDgzMTE0NDY0MDE0OTc5",

	"211.210.124.46" => "U01TX0FVVEgyMDE3MDcyNTE2MzI0MDIzMTU1",			//miodio 구 IDC
	"112.175.54.246" => "U01TX0FVVEgyMDE3MTExMzEyMzEzNDEwNzQ3NjU=",			//miodio 신 IDC
	"121.78.115.215" => "U01TX0FVVEgyMDE3MDcyNTE2MzQzOTIzMTU2",			//fotocube 구 IDC
	"112.175.54.251" => "U01TX0FVVEgyMDE3MTEwMTE1MjkzMjEwNzQ1MDM=",			//fotocube 신 IDC


	"115.68.51.132" => "U01TX0FVVEgyMDE4MDMyMjE2MzI0NDEwNzc1NzA=",
	"115.68.51.161" => "U01TX0FVVEgyMDE4MDMyMjE2MzI0NDEwNzc1NzM=",
	"115.68.51.134" => "U01TX0FVVEgyMDE4MDMyMjE2MzI0NDEwNzc1NzE=",
	"115.68.51.151" => "U01TX0FVVEgyMDE4MDMyMjE2MzI0NDEwNzc1NzQ=",
	"115.68.51.154" => "U01TX0FVVEgyMDE4MDMyMjE2MzI0NDEwNzc1NzU=",
	);


$r_inicis_bank_name = array(
"02" => _("한국산업은행"),
"03" => _("기업은행"),
"04" => _("국민은행  (주택은행)"),
"05" => _("외환은행"),
"07" => _("수협중앙회"),
"11" => _("농협중앙회"),
"12" => _("단위농협"),
"16" => _("축협중앙회"),
"20" => _("우리은행"),
"21" => _("신한은행  (조흥은행)"),
"23" => _("제일은행"),
"25" => _("하나은행  (서울은행)"),
"26" => _("신한은행"),
"27" => _("한국씨티은행  (한미은행)"),
"31" => _("대구은행"),
"32" => _("부산은행"),
"34" => _("광주은행"),
"35" => _("제주은행"),
"37" => _("전북은행"),
"38" => _("강원은행"),
"39" => _("경남은행"),
"41" => _("비씨카드"),
"53" => _("씨티은행"),
"54" => _("홍콩상하이은행"),
"71" => _("우체국"),
"81" => _("하나은행"),
"83" => _("평화은행"),
"87" => _("신세계"),
"88" => _("신한은행(조흥 통합)")
);


//블루포토 지사 1차 승인 / 15.11.18 / kjm
$r_manager_inspection = array(
   'Y' => _("확인완료"),
   'N' => _("미확인")
);

$r_fieldset = array(
    "mid"       => array(
            "name"  => _("아이디"),
            "use"   => 1,
            "req"   => 1,
        ),
    "password"  => array(
            "name"  => _("비밀번호"),
            "use"   => 1,
            "req"   => 1,
        ),
    "name"      => array(
            "name"  => _("이름"),
            "use"   => 1,
            "req"   => 1,
        ),
    "email"     => array(
            "name"  => _("이메일"),
            "use"   => 1,
            "req"   => 1,
        ),
    "mobile"        => array(
            "name"  => _("핸드폰번호"),
            "use"   => 1,
            "req"   => 1,
        ),
    "sex"       => array(
            "name"  => _("성별"),
            "use"   => 1,
            "req"   => 1,
        ),
    "birth"     => array(
            "name"  => _("생년월일"),
            "use"   => 1,
            "req"   => 1,
        ),
    "address"       => array(
            "name"  => _("주소"),
            "use"   => 1,
            "req"   => 1,
        ),
    "phone"     => array(
            "name"  => _("전화번호"),
            "use"   => 1,
            "req"   => 0,
        ),
    "res"       => array(
            "name"  => _("주민등록번호"),
            "use"   => 0,
            "req"   => 0,
        ),
    "married"       => array(
            "name"  => _("결혼여부"),
            "use"   => 1,
            "req"   => 0,
        ),
    "cust_name"     => array(
            "name"  => _("사업자명"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_type"     => array(
            "name"  => _("업태"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_class"    => array(
            "name"  => _("업종"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_tax_type" => array(
            "name"  => _("사업자등록유형"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_no"   => array(
            "name"  => _("사업자등록번호"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_ceo"  => array(
            "name"  => _("대표자명"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_ceo_phone"    => array(
            "name"  => _("대표자연락처"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_address"  => array(
            "name"  => _("사업장 주소"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_address_en"   => array(
            "name"  => _("사업장 영문주소"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_phone"    => array(
            "name"  => _("사업장 전화번호"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_fax"  => array(
            "name"  => _("사업장 팩스번호"),
            "use"   => 0,
            "req"   => 0,
        ),
    "cust_bank" => array(
            "name"  => _("주거래계좌정보"),
            "use"   => 0,
            "req"   => 0,
        ),
    "manager_no"   => array(
            "name"  => _("정산담당자"),
            "use"   => 0,
            "req"   => 0,
        ),
);
$r_state_color = array('#ffffff','#9d0038','#006594');
$r_cs = array("",_("답변대기"),_("답변완료"));

$r_notice_category = array('notice'=>_("공지사항"),'upgrade'=>_("업그레이드"));


//장바구니 편집상태 관련 상태값 cart class 내부에 있던걸 가져옴       20140409    chunter
$r_cart_error = array(
    1   => _("상품 삭제"),
    2   => _("샵의 판매중지"),
    3   => _("품절 및 판매중지"),
    4   => _("삭제된 상품 옵션"),
    5   => _("상품 옵션 판매중지"),
    6   => _("재고부족"),
    7   => _("편집미완료"),
    8   => _("편집조회실패"),
    9   => _("판매가 없음"),
    10  => _("구매가능한 그룹이 아님"),
    11  => _("주문완료"),
);

//커버규격
$r_cover_ragne = array(
   1,
   2,
   3,
   4,
   5,
   6,
   7,
   8,
   9,
   10,
   11,
   12,
   13,
   14,
   15,
   16,
   17,
   18,
   19,
   20
);


$r_cover_type = array(
   "S" => _("소프트"),
   "H" => _("하드")
);

$r_cover_paper = array(
   "ST" => _("사틴"),
   "LS" => _("러스터"),
   "DBGL" => _("글로시")
);

$r_cover_coating = array(
   "MATT" => _("무광 라미네이팅"),
   "SHINE" => _("유광 라미네이팅")
);



$r_quotation_reqeust_state = array(
  '1'   => _("접수"),
  '2'   => _("대기"),
  '3'   => _("완료"),
);


# 기능성배너 아이디 정의
$r_util_banner = array(
    "_top_search_btn",
);

//############ ibookcover (정핵회원) 관련 설정 ######################
//정액 회원 결제 금액   20140106    chunter
//금액 변경(김기웅이사 요청) 20140211 kdk
$r_fix_member_price = array(
  /*'1' => '55000',
  '2' => '110000',
  '3' => '156750',
  '4' => '209000',
  '5' => '261250',
  '6' => '297000',
  '7' => '346500',
  '8' => '396000',
  '9' => '445500',
  '10' => '495000',
  '11' => '544500',
  '12' => '561000',
  '24' => '1056000',
  '36' => '1320000',*/

  _('1개월') => '55000',
  _('2개월') => '110000',
  _('3개월') => '165000',
  _('4개월') => '220000',
  _('5개월') => '275000',
  _('6개월') => '330000',
  _('7개월') => '385000',
  _('8개월') => '440000',
  _('9개월') => '495000',
  _('10개월') => '550000',
  _('11개월') => '605000',
  _('12개월') => '660000',
  _('24개월') => '1320000',
  _('36개월') => '1980000',
);
//############ podgroup (정핵회원) 관련 설정 ######################
$r_fix_member_paymethod = array(
  'c' => _("카드"),
  'cp' => _("쿠폰"),
  'ad' => _("관리자"),
);

//############ podgroup (정핵회원) 관련 설정 ######################
//정액 회원 다운로드 횟수 / 16.09.20 / kdk
$r_fix_member_download = array(
  _('베이직(1개월)') => '20',
  _('베이직(2개월)') => '20',
  _('베이직(3개월)') => '20',
  _('베이직(6개월)') => '20',
  _('실버(12개월)') => '30',
  _('골드(12개월)') => '30',
  _('프리미엄(12개월)') => '-1', //무제한.
);

//############ 자동견적  관련 설정 ######################

//견적 시스템 옵션 구분    20140128
$r_est_option = array(
  'OCNT' => _("주문수량(부수)"),
  'PCNT' => _("내지출력수"),
  'B-DS' => _("규격(책자)"),
  'P-PP' => _("지류(내지)"),
  'C-PP' => _("지류(표지)"),
  'C-BB' => _("제본(표지)"),

  'DS' => _("규격"),
  'PP' => _("지류"),
  'IC' => _("인쇄도수"),
  'PT' => _("인쇄방식"),
  'OS' => _("오시"),
  'FL' => _("오시"),
  'MS' => _("미싱"),
  'CT' => _("코팅"),
  'DM' => _("도무송"),
  'BK' => _("박"),
  'HA' => _("형압"),
  'TG' => _("타공"),
  'JZ' => _("접지"),
  'JC' => _("접착"),
  'GD' => _("귀도리"),
  'ET' => _("편집방법"),
  'PC' => _("부분코팅"),
  'BD' => _("제본"),
  'PG' => _("추가페이지"),
  'BP' => _("제본 위치").' (Binding Position)',
  'BS' => _("책등사이즈").' (Book Spine)',
  'JD' => _("제단"),
  'BT' => _("봉투")
);

//견적 시스템 옵션 구분 2015.01.23 by kdk
$r_est_option2 = array(
  'P-PP' => _("지류"),

  'DOCUMENT' => _("규격"),
  'C-PP' => _("표지(지류)"),
  'C-OP' => _("표지(옵션)"),
  'PP' => _("내지(지류)"),
  'O-PP' => _("옵션"),
  'OP' => _("내지(옵션)"),
  'M-PP' => _("면지(지류)"),
  'M-OP' => _("면지(옵션)"),
  'G-PP' => _("간지(지류)"),
  'G-OP' => _("간지(옵션)"),
  'PP-' => _("추가내지(지류)"),
  'OP-' => _("추가내지(옵션)"),

);

$r_goods_group_code = array(
  '10' => _("일반상품"),
  '30' => _("인쇄견적"),
  '20' => _("스튜디오견적"),
  '40' => _("결제전용상품"),
  '50' => _("패키지상품"),
  '60' => _("견적상품"), //인터프로 ipro
);

$r_est_item_price_type = array(
  'CNT' => _("단가"),
  'TIME' => _("구간1회"),
  'SIZE' => _("면적"),
);

$r_est_item_price_type_info = array(
  'CNT' => _("페이지 1장 당 가격 설정 방식(1장 x 설정가격)"),
  'TIME' => _("주문 구역별 가격 설정 방식(각각의 구역별 => 설정가격)")
);

$r_est_item_price_type_help = array(
  'CNT' => '#',
  'TIME' => '#'
);

$r_est_item_price_type_title = array(
  'CNT' => "("._("장당가격설정").")",
  'TIME' => "("._("구역별가격설정").")"
);

$r_est_product = array(
	'CARD' => _("낱장상품"),
	'BOOK' => _("책자상품"),
	//'ETC' => '실사출력'
	//'STUDIO' => '스튜디오'
);

$r_est_product_help = array(
	'CARD' => '#',
	'BOOK' => '#',
	//'ETC' => '#'
);

/*$r_est_preset = array(
	'100101' => '프리셋1 (2013 소풍스타일)',
	//'100102' => '프리셋2 (2013 성진스타일)'
);*/

$r_est_preset = array(
	'CARD' => array('100102' => _("프리셋1").'(100102)', '100106' => _("프리셋2").'(100106)', '100114' => '기본 프리셋(100114)',),
	'BOOK' => array('100104' => _("프리셋1").'(100104)', '100108' => _("프리셋2").'(100108)', '100112' => _("프리셋3").'(100112)',), //'100103' => '프리셋1',
	//'ETC' => array('100102' => '프리셋2 (2013 성진스타일)',),
	//'100102' => '프리셋2 (2013 성진스타일)'
);

//자동견적 프리셋 서브 옵션 설정       20150320    chunter
$r_est_preset_sub_option_group = array(
  '100102' => array(
    "F-FIX" => array("1" => "1,6,11", "2"=>"2,7,12", "3"=>"3,8,13", "4"=>"4,9,14", "5"=>"5,10,15")
   ),
  '100106' => array(
    "F-FIX" => array("2" => "1,7,12", "3"=>"2,8,13", "4"=>"3,9,14", "5"=>"4,10,15", "6"=>"5,11,16"),
  ),
  '100104' => array(
    "C-FIX" => array("2" => "1,7,12", "3"=>"2,8,13", "4"=>"3,9,14", "5"=>"4,10,15", "6"=>"5,11,16"),
    "F-FIX" => array("19" => "1,24,29", "20"=>"2,25,30", "21"=>"3,26,31", "22"=>"4,27,32", "23"=>"5,28,33"),
    "M-FIX" => array("36" => "1,41,46", "37"=>"2,42,47", "38"=>"3,43,48", "39"=>"4,44,49", "40"=>"5,45,50"),
    "G-FIX" => array("53" => "1,58,63", "54"=>"2,59,64", "55"=>"3,60,65", "56"=>"4,61,66", "57"=>"5,62,67")
   ),

  '100108' => array(
    "C-FIX" => array("2" => "1,7,12", "3"=>"2,8,13", "4"=>"3,9,14", "5"=>"4,10,15", "6"=>"5,11,16"),
    "F-FIX" => array("21" => "1,26,31", "22"=>"2,27,32", "23"=>"3,28,33", "24"=>"4,29,34", "25"=>"5,30,35"),
    "M-FIX" => array("40" => "1,45,50", "41"=>"2,46,51", "42"=>"3,47,52", "43"=>"4,48,53", "44"=>"5,49,54"),
    "G-FIX" => array("59" => "1,64,69", "60"=>"2,65,70", "61"=>"3,66,71", "62"=>"4,67,72", "63"=>"5,68,73")
  ),
   '100112' => array(
    "C-FIX" => array("4" => "1,5,6"),
    "F-FIX" => array("9" => "1,10,11"),
    "M-FIX" => array("13" => "1,14,15"),
    "G-FIX" => array("17" => "1,18,19")
  )
);

$r_est_preset_help = array(
	'100101' => '#', //낱장(사용안함)
	'100102' => '#', //낱장
	'100103' => '#', //책자(사용안함)
	'100104' => '#', //책자
	'100106' => '#', //책자
	'100108' => '#', //책자
	'100110' => '#', //책자(스튜디오)
	'100112' => '#', //책자(신규)
	'100114' => '#', //낱장(기본)
);

$r_stu_order = array(
	'UPL' => _("업로더 프로그램 사용 (프로그램을 사용해 업로드 및 주문)"),
	'EXT' => _("견적 주문 방식 (웹에서 옵션 선택후 파일첨부)")
);

$r_stu_preset = array(
	'100110' => _("프리셋1").'(100110)'
);

//$r_est_editor = array(
//	'0' => '없음',
//	'1' => 'H5 Ver 1.0'
//);

$r_est_price_table_type = array(
	'1' => _("X 축(수량) , Y 축(옵션/가격)"),
	'2' => _("X 축(옵션/가격) , Y 축(수량)")
);

//카테고리별 템플릿 파일    20140210
$r_category_template = array(
  'list.basic' => array('list' => _("기본 리스트")),
  'list.template' => array('list' => _("템필릿별 리스트"))
);

//상품 상세 템플릿 파일      20140210
$r_goods_detail_template = array(
  'view' => array('view_name' => _("기본"), 'template_file' => array('view.htm' => _("기본"))),
  'view_option' => array('view_name' => _("자동견적"), 'template_file' => array('view_option.htm' => _("기본"))),
  'view_wpod' => array('view_name' => 'wpod', 'template_file' => array('view_wpod.htm' => _("기본"))),
  'view_studio' => array('view_name' => _("스튜디오"), 'template_file' => array('view_studio.htm' => _("기본"))),
);


$r_email_type = array(
   "admin_register"   => _("회원가입시"),
   "admin_order"      => _("주문접수시"),
   "admin_payment"    => _("입금확인시"),
   "admin_shipping"   => _("발송완료시"),
   "admin_order_canc" => _("주문취소시")
);

//############ bizcard 관련 설정 ######################

//20140925 / minks / 연락처형식 배열 추가
$r_contact_separator_front = array(
  'A. B. C'  =>  "010. 1234. 5678",
  '(A)B-C'  =>  "(010)1234-5678",
  'A-B-C'  =>  "010-1234-5678",
  'A.B.C'  =>  "010.1234.5678",
);

$r_contact_separator_back = array(
  '82. A. B. C'  =>  "82. 10. 1234. 5678",
  '82-A-B-C'  =>  "82-10-1234-5678",
  '+82-A-B-C'  =>  "+82-10-1234-5678",
  '82.A.B.C'  =>  "82.10.1234.5678",
);



//############ 블루포토 관련 설정 ######################
//유치원 스킨들    20150303  chunter
$r_kids_skin = array(
  'kids', 'pretty'
);

//유치원 시즌2 스킨들    20150803  chunter
$r_pritty_skin = array(
  'pretty', 'pretty_dnp'
);

//유치원 시즌2 선생님 관리자 권한
$r_mng = array(
  'N' => _("그룹관리자"),
  'Y' => _("대표관리자"),
);

//회원 상태
$r_member_state = array(
  '0' => _("승인"),
  '1' => _("미승인"),
  '2' => _("차단"),
);


//############ printhome 관련 설정 ######################
//프린트홈 스킨들    20141022  chunter
$r_printhome_skin = array(
  'printhome', 'printhome_black'
);

//$daum_map_key = "d8245e359e2e5d248a2baaa6abbfe5009462a9b7";     //192.168.1.197
$daum_map_key = "7c89e159d025db93764bdef61c5fb1574091632b";       //dev.webhardprint.co.kr
$daum_local_key = "2a383c4aaaa746e5a8b85c6e6c76ba8675fc88ef";

$r_main_color = array(
  'aqua' => '#27d7e7',
  'blue' => '#3498db',
  'brown' => '#9c8061',
  'dark-blue' => '#4765a0',
  'light' => '#95a5a6',
  'light-green' => '#79d5b3',
  'orange' => '#e67e22',
  'purple' => '#9b6bcc',
  'red' => '#e74c3c',
  'lime' => '#72c02c'
);


$r_main_color_printhome_balck_theme = array(
  'aqua' => '#27d7e7',
  'blue' => '#3498db',
  'green' => '#00bb5c',
  'lavender' => '#ad4dd3',
  'orange' => '#e67e22',
  'brown' => '#9c8061',
);

//printhome 과 같은 구조의 bluepod 도메인 설정
$r_new_cid_domain = array(
  "bluepod.co.kr",
  //"bluepod.kr",

	"unipod.co.kr",
	"miodio.co.kr",
	"interproindigo.co.kr",
	"alaskaprint.co.kr",
  //"168.1.197"
);


$r_ph_upload_state = array(
  '01'   => _("주문 접수"),
  '02'   => _("시안 검토 요청"),
  '03'   => _("시안 수정 요청"),
  '04'   => _("시안 확정"),
  '05'   => _("처리 완료"),
  '09'   => _("주문 취소"),
  '11'   => _("견적 의뢰"), //견적 의뢰 기능 추가. (프린트홈) 2015.07.24 by kdk
  '12'   => _("견적 발행"), //견적 의뢰 기능 추가. (프린트홈) 2015.07.24 by kdk
);

//후기구분
$r_kind = array(
	"normal"	=> _("일반후기"),
	"photo"		=> _("포토후기"),
);

//후기추천
$r_degree = array(
	"5" => _("적극추천"),
	"4"	=> _("추천"),
	"3"	=> _("보통"),
	"2"	=> _("비추천"),
	"1"	=> _("적극비추천"),
);

//상담유형
$r_cs_category = array(
	1 => _("주문/결제"),
	2 => _("배송관련"),
	3 => _("취소/환불"),
	4 => _("상품관련"),
	5 => _("회원관련"),
	6 => _("적립금/쿠폰"),
	7 => _("오류/불편사항"),
	8 => _("기타"),
	9 => _("페이백"),
);

//서류발급종류
$r_document_type = array(
	"CRD" => _("현금영수증(소득공제)"),
	"CRE" => _("현금영수증(지출증빙)"),
	"TI" => _("세금계산서"),
);

//대량구매문의종류
$r_bigorder_type = array(
	"quotation" => _("견적요청"),
	"sample" => _("샘플신청"),
);

$r_printhome_topmenu_active = array(
  '/main/index.php'  =>  "/",
  '/service/company.php'  =>  "/service/company.php",
  '/order/order_upload_webhard.php'  =>  "/order/order_upload_webhard.php",

  '/order/design_confirm_list_new.php'  =>  "/order/design_confirm_list_new.php",
  '/order/design_confirm_modify.php'  =>  "/order/design_confirm_list_new.php",

  '/service/estimate_request_list.php'  =>  "/service/estimate_request_list.php",
  '/service/estimate_request.php'  =>  "/service/estimate_request_list.php",
  '/service/request.php'  =>  "/service/estimate_request_list.php",

  '/board/list.php'  =>  "/board/list.php?board_id=notice",
  '/board/write.php'  =>  "/board/list.php?board_id=notice",
  '/board/view.php'  =>  "/board/list.php?board_id=notice",
  '/service/faq.php'  =>  "/board/list.php?board_id=notice",
  '/service/ph_help1.php'  =>  "/board/list.php?board_id=notice",
  '/service/ph_help2.php'  =>  "/board/list.php?board_id=notice",
  '/service/ph_help3.php'  =>  "/board/list.php?board_id=notice",
  '/service/ph_help4.php'  =>  "/board/list.php?board_id=notice",
  '/service/ph_help5.php'  =>  "/board/list.php?board_id=notice",
);

//게시판 단어 필터링 기본 단어들			20160617		chunter
$_board_default_filter_text = _("8억,새끼,개새끼,소새끼,병신,지랄,씨팔,십팔,니기미,찌랄,지랄,쌍년,쌍놈,빙신,좆까,니기미,좆같은게,잡놈,벼엉신,바보새끼,씹새끼,씨발,씨팔,시벌,씨벌,떠그랄,좆밥,추천인,추천id,추천아이디,추천id,추천아이디,추천인,쉐이,등신,싸가지,미친놈,미친넘,찌랄,죽습니다,님아,님들아,씨밸넘");
$_board_default_name_close = _("운영자,관리자,매니저").",admin,ADMIN,Admin";

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

$r_inpro_print_goods_group = array(
    "CARD" => "명함",
    "POSTER" => "포스터",
    "ETCARD" => "기타낱장",
    "BOOK" => "책자",
);

//견적상품구분 / 20180517 / kdk.
//디지털 윤전 추가 / 20181210 / kdk
//현수막,실사출력 추가 / 20190320 / kdk
$r_est_print_product = array(
    'DG01' => _("디지털 일반-명함"),
    'DG02' => _("디지털 일반-스티커"),
    'DG03' => _("디지털 낱장-일반"),
    'DG04' => _("디지털 낱장-스티커")."(자유형)",
    'DG06' => _("디지털 책자-스티커")."(사각형)",
    'DG05' => _("디지털 책자"),
    'DG07' => _("디지털 윤전-낱장"),
    'DG08' => _("디지털 윤전-책자"),
    'OS01' => _("옵셋 낱장"),
    'OS02' => _("옵셋 책자"),
    "PR01" => _("현수막"),
    "PR02" => _("실사출력"),
);

//견적상품 항목 수정 링크 / 20180710 / kdk.
//디지털 윤전 추가 / 20181210 / kdk
//현수막,실사출력 추가 / 20190320 / kdk
$r_est_print_product_admin_link = array(
    //디지털.
    'DG01' => "/a/print/admin_goods_item_select.php",
    'DG02' => "/a/print/admin_goods_item_select.php",
    'DG03' => "/a/print/admin_goods_item_select.php",
    'DG04' => "/a/print/admin_goods_item_select.php",
    'DG05' => "/a/print/admin_goods_book_item_select.php",
    'DG06' => "/a/print/admin_goods_item_select.php",
    //윤전(디지털).
    'DG07' => "/a/print/admin_goods_item_select.php",
    'DG08' => "/a/print/admin_goods_book_item_select.php",

    //옵셋.
    'OS01' => "/a/print/admin_goods_item_select.php",
    'OS02' => "/a/print/admin_goods_book_opset_item_select.php",

    //현수막.실사출력
    'PR01' => "/a/print/admin_goods_pr_item_select.php",
    'PR02' => "/a/print/admin_goods_pr_item_select.php"
);


//사이트별 설정이 필요한걸 별도로 분리한다.				20160308		chunter
//cid별로 사용 하도록 변경 lib.cust.php에서 선언 ($cid._local_const.php) / 20181120 / kdk
//$g_pods10_domain = "podstation.ilark.co.kr";
//$g_pods20_domain = "podstation20.ilark.co.kr";
$g_nation_code = "82";			//korea
//if (file_exists(dirname(__FILE__) . "/../conf/local_const.php")) {
//	include_once dirname(__FILE__)."/../conf/local_const.php";
//}
//define("PODS10_DOMAIN", $g_pods10_domain);
//define("PODS20_DOMAIN", $g_pods20_domain);
?>
