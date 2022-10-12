<?
//2014.02 월 이후 추가되는 const 배열 변수들.
define("TODAY_Y_M_D", date("Y-m-d"));
define("TODAY_YMD", date("Ymd"));

define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);

define("CURRENT_FILE_NAME", basename($_SERVER[PHP_SELF]));     //현재 파일명.
define("CURRENT_FILE_PATH", dirname($_SERVER[PHP_SELF]));      //현재 경로명.

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

### pod 알래스카 관련 설정 ###
//pod_deposit_money 알래스카 입금방법 / 20181105 / kdk
$r_deposit_method = array(
    '1' => "현금", 
    '2' => "국민",
    '3' => "우리(1번구좌)",
    '4' => "우리(2번구좌)",
    '5' => "온라인(KCP)",
    '6' => "카드단말기",
    '7' => "온라인계좌이체(KCP)",
    '8' => "어음",
    '9' => "대체",
);
//pod_pay 주문상태 / 20181105 / kdk
$r_pay_status = array(
    '1' => "접수대기", 
    '2' => "접수완료",
    '3' => "출력완료",
    '4' => "후가공작업중",
    '5' => "제작완료",
    '6' => "출고완료",
    '7' => "주문취소",
);

//debug 함수 출력되는 IP 설정.			20160726	chunter
$r_debug_IP = array("localhost", "210.96.184.229");

//PG 사 종류				20160713		chunter
$r_pg_kind = array(
	"kcp" => "KCP Payplus V6 AX HUB",
	"inipaystdweb"=>_("이니시스 표준결제(통합)"),
	"smartxpay" =>"LGU+ SmartXpay",
	//"inicis" =>"INICIS INIPay TX5.0",
	//"lg" =>"LGU+ XpayLite",
	//"tenpay" => "WeChat"
);

//모바일 결제가 가능한 스킨들				//20160930			chunter
$r_mobile_PG_allow_skin = array(
  'basic', 'classic', 'monami', 'spring'
);



//몰 생성시 html_public 폴더를 만들지 않고 기본 data 폴더내에 업체별 스킨을 생성하여 처리하는 스킨들			//20160429
$r_not_make_public_skin = array(
  'printhome', 'printhome_black', 'classic'
);


//defense 모듈 사용 스킨들    20151021  chunter
$r_defense_use_skin = array(
  'printhome', 'printhome_black', 'bizcard'
);

//일반,단체 상품        20150428    chunter
$r_goods_member_mapping_kind = array(
  '1' => _("일반상품"),
  '2' => _("단체상품"),
);


### 선택값
$r_select = array(
    0   => _('사용안함'),
    9   => _('사용함'),
);

### 선택값배경색
$r_select_color = array(
    0   => '#FFC0C0',
    9   => '#C0F9CC',
);
		

//pretty 스토리지 크기별(T 테라) 결제 금액 (크기 TB => 금액)        20150408    chunter
$r_pretty_storage_price = array( 
  '1' => '110000',  
  '2' => '220000',
  '3' => '330000',
  '4' => '440000',
  '5' => '550000',
  '6' => '660000',
  '7' => '770000',
  '8' => '880000',
  '9' => '990000',
  '10' => '1100000',
  '11' => '1210000',
  '12' => '1320000',
  '13' => '1430000',
  '14' => '1540000',
  '15' => '1650000',
);

//웹하드 사용기간 (기간 => 금액) 금액은 사용되지 않음.
$r_pretty_storage_month = array(
  '1' => '110000', 
  '2' => '220000', 
  '3' => '330000', 
  '4' => '440000', 
  '5' => '550000',
  '6' => '660000',
  '7' => '770000',
  '8' => '880000',
  '9' => '990000',
  '10' => '1100000',
  '11' => '1210000',
  '12' => '1320000',  
);


$r_pretty_storage_pay_kind  = array(
  'c' => _("신용카드"),
  "v" => _("가상계좌"),  
  'k' => _("관리자"),  
);


//pretty cart_mapping 테이블 state 값      20150706    chunter
$r_pretty_mapping_state = array(
  'E' => _("편집중"), 
  'R' => _("주문요청완료"),   
  'O' => _("주문 완료"),

  'C' => _("취소"),
  'J' => _("주문 반려"),
  'M' => _("편집수정요청"),
  'CR' => _("주문요청완료"), //주문 취소건 재주문 완료
  'MR' => _("주문요청완료"), //재편집 주문건 재주문 완료
);


//pretty 포인트 결제 금액  (포인트 => 금액)         20150408    chunter
//100,000원 포인트 추가(문팀장님 요청) / 16.01.06 / kjm
$r_pretty_point_price = array(
  '100000' => '100000',
  //'200000' => '200000',
  '500000'  => '500000',
  '1000000' => '1000000',
  '2000000' => '2000000',
  '5000000' => '5000000',
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


//유치원에서 포인트 사용내역        20150326    chunter
$r_pretty_point_account_flag = array(
  '11' => _("주문결제"), 
  '12' => _("승인반려후 재결제"),      //모든 주문은 승인요청페이지에서 상품 건건 포인트가 차감되는 구조이기에 사용되지 않음      20150710
  '13' => _("관리자 차감"),  
  '14' => _("SMS 발송"),
  '21' => _("사용자 충전"), 
  '22' => _("관리자 충전"), 
  '23' => _("관리자 승인반려"),       //모든 주문은 승인요청페이지에서 포인트가 차감되는 구조이기에 사용되지 않음      20150710
  '24' => _("관리자 주문취소"),       //주문 취소 기능이 없음      20150710
);

//유치원 기타 내역 flag / 16.12.27 / kjm
$r_pretty_etc_history_flag = array(
  'D'    => _("편집내역 삭제"), 
  'MCD'  => _("원장님 권한 삭제"),
  'CDLM' => _("편집내역 원아 목록 수정"), 
  'CSLM' => _("편집내역 반 목록 수정"), 
);

//k관리자 - 포인트관리 - 충전금액, 사용금액 구분값 / 15.07.02 / kjm
$r_pretty_point_list_chk_flag = array("11", "12", "13", "14", "15");

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
  'de' => _("미수금"), 
  'da' => _("예치금"),  
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
    9999, //일반상품,
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
    3040, //4.0 팬시편집기
    3042, //4.0 액자편집기
    3043, //4.0 팬시그룹편집기
    3060, //4.0 달력편집기
    3053, //4.0 자동완성북(커버가변)
    3054, //4.0 자동완성북(커버고정)
    3020  //4.0 프롤인화편집기
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



$r_phone = array('02','031','032','033','041','042','043','044','051','052','053','054','055','061','062','063','064','070','010');
$r_mobile = array('010','011','016','017','018','019');
$r_email = array(
    _("네이버")   => 'naver.com',
    _("네이트")   => 'nate.com',
    _("한메일")   => 'daum.net',
    _("지메일")   => 'gmail.com',
    _("핫메일")   => 'hotmail.com',
    _("야후")     => 'yahoo.co.kr',
    _("파란")     => 'paran.com',
    _("엠파스")   => 'empal.com',
    _("드림위즈") => 'dreamwiz.com',
    _("라이코스") => 'lycos.co.kr',
    _("한미르")   => 'hanmir.com',
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
    'd' => _("신용거래2"),
    'f' => _("정액제"),
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
$r_title = array(_("회원가입"), _("비밀번호찾기"), _("게시판-글등록"), _("게시판-답글등록"), _("견적신청"), _("샘플신청"), _("주문접수"), _("접수내역"), _("입금확인"), _("발송완료"), _("주문취소"), _("관리자충전"), _("1:1문의-답글등록"));

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
//https://www.juso.go.kr/addrlink/main.htm
$r_zipcode_api_key = array(
	"112.175.54.235" => "U01TX0FVVEgyMDE2MDgzMTE0NDY0MDE0OTc1",
	"112.175.54.239" => "U01TX0FVVEgyMDE2MDgzMTE0NDY0MDE0OTc3",
	"112.175.54.245" => "U01TX0FVVEgyMDE2MDgzMTE0NDY0MDE0OTc4",
	"210.96.184.229" => "U01TX0FVVEgyMDE2MDgzMTE0NDY0MDE0OTc5",	
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


//사이트별 설정이 필요한걸 별도로 분리한다.				20160308		chunter
$g_pods10_domain = "podstation.ilark.co.kr";
$g_pods20_domain = "podstation20.ilark.co.kr";
$g_nation_code = "82";			//korea
if (file_exists(dirname(__FILE__) . "/../conf/local_const.php")) {
	include_once dirname(__FILE__)."/../conf/local_const.php";		
}
define("PODS10_DOMAIN", $g_pods10_domain);
define("PODS20_DOMAIN", $g_pods20_domain);
?>