<?
Header("p3p: CP=\"CAO DSP AND SO ON\" policyref=\"/w3c/p3p.xml\""); 
header("Pragma: no-cache");
header("Cache: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Expires:Mon, 26 Jul 1997 05:00:00 GMT");

include_once dirname(__FILE__)."/../conf/language_locale.php";				//다국어 파일 처리. 절대..절대 주석처리 하지 말것...		20180703	chunter
include_once dirname(__FILE__)."/lib_denyip.php";	//차단 ip 추가 20200624 jtkim

include_once dirname(__FILE__)."/class.db.php";
$db = new DB(dirname(__FILE__)."/../conf/conf.db.php");

include_once dirname(__FILE__)."/lib_const.php";     //전역 변수 모음 파일     20140210  chunter

//모델 관련 파일 추가
include_once dirname(__FILE__)."/../models/m_extra_option.php";      //자동 견적 옵션 20140321   chunter
include_once dirname(__FILE__)."/../models/m_goods.php";      //상품관련 20140603   chunter
include_once dirname(__FILE__)."/../models/m_cart.php";      //장바구니  20140603   chunter

//include dirname(__FILE__)."/../models/m_bluepod_service.php";      //서비스 도메인 20140522   chunter   //m_mall.php 로 통합 처리    20140905  chunter
include_once dirname(__FILE__)."/../models/m_mall.php";    //몰관련 정보    20140905  chunter
include_once dirname(__FILE__)."/../models/m_manager.php";    //manager 관련 정보    20150211  chunter
include_once dirname(__FILE__)."/../models/m_order.php";      //order 관련 정보    20150211  chunter
include_once dirname(__FILE__)."/../models/m_config.php";     //config 관련 정보    20150211  chunter
include_once dirname(__FILE__)."/../models/m_member.php";     //config 관련 정보    20150211  chunter

include_once dirname(__FILE__)."/../models/m_board.php";     //게시판 관련 정보    20150310  chunter
include_once dirname(__FILE__)."/../models/m_etc.php";     //기타 정보  관련 정보    20150421  chunter
include_once dirname(__FILE__)."/../models/m_pretty.php";
include_once dirname(__FILE__)."/../models/m_emoney.php";

include_once dirname(__FILE__)."/../models/m_print.php";    //자동견적(인터프로 ipro) 관련 정보  / 201804051 / kdk

include_once dirname(__FILE__)."/../models/m_common.php";     //공통 DB 관련 처리  20170411  chunter
include_once dirname(__FILE__)."/../lib2/db_common.php";     //공통 DB 관련 처리  20170411  chunter

include_once dirname(__FILE__)."/../models/m_board.php";     //board 관련 정보 / 15.02.16 / kjm
include_once dirname(__FILE__)."/../models/m_etc.php";     //etc 관련 정보 / 15.02.16 / kjm
include_once dirname(__FILE__)."/../models/m_modern.php";
include_once dirname(__FILE__)."/../models/m_cash_receipt.php";

include_once dirname(__FILE__)."/../models/m_attend_event.php";

include_once dirname(__FILE__)."/../models/m_pod.php"; //pod관련 정보 (알래스카) / 20181106 / kdk

include_once dirname(__FILE__)."/../config/lib_modern.php";


include_once dirname(__FILE__)."/lib_util.php";      //추가 유틸 함수    20131212  chunter
include_once dirname(__FILE__)."/lib.common.php";
include_once dirname(__FILE__)."/lib.cust.order.func.php";      //lib.cust 파일 분리 - 주문처리함수    20150211  chunter
include_once dirname(__FILE__)."/lib.cust.func.php";            //lib.cust 파일 분리 - 일반 함수    20150211  chunter
include_once dirname(__FILE__)."/lib_util_db_use.php";      //db 처리 공통 클래스    20150108  chunter

include_once dirname(__FILE__)."/lib_goods.php";     //상품 관련 함수 모음    20140603  chunter


include_once dirname(__FILE__)."/lib.cust.php";
include_once dirname(__FILE__)."/lib_skin_config.php";        //스킨별 레이아웃 설정 파일

include_once dirname(__FILE__)."/class.pods.php";        //pods 인터페이스     20150908

include_once dirname(__FILE__)."/class.goods.php";        //goods 인터페이스     20170517 kdk

include_once dirname(__FILE__)."/lib_util_signature.php"; //form 전송 취약점 개선(메세지 인증(시그니처)) 20160128 kdk


include_once dirname(__FILE__)."/../lib2/lib_modern_util.php";

include_once dirname(__FILE__)."/lib_extra_option.php";      //자동 견적 옵션 추가 함수    20170901  kdk
include_once dirname(__FILE__)."/class.extra.option.php";      //자동 견적 옵션 class   20170901  kdk

include_once dirname(__FILE__)."/../conf/P1.category.const.php";      //P1 테마에서 사용할 상단 카테고리 노출 정보			20181010		chunter
//include_once dirname(__FILE__)."";      // 네이버 wcslog    20190807 jtkim
//include_once dirname(__FILE__)."/../config/lib_const.php";
?>