<?
/*
* @date : 20190319
* @author : kdk
* @brief : 견적 상품 추가 현수막(Placard),실사출력(RealPrint)
* @desc : 현수막 (PR01),실사출력(PR02)
*/

/*
* @date : 20180628
* @author : kdk
* @brief : 견적 상품 타입별  상품 코드 추가.
* @desc : 상품 코드는 하드코딩으로 관리한다.
*/

/*
* @date : 20180530
* @author : chunter
* @brief : 기본 견적 설정 const 내용 삭제.
*/

/*
* @date : 20180309
* @author : chunter
* @brief : 인터프로 자동견적 관련 상품 셋팅
* @desc : 자동견적 관련 옵션, 항목들은 모두 하드코딩으로 관리한다.
*/
?>
<?

//견적 상품 타입별  상품 코드.
//옵셋상품 수량에 사용한다. /print/option_valid_check.php 
//옵션 제약사항 체크에 사용한다. /goods/_view_print.php
$r_inpro_print_goodsno = array(
    "JD" => array("761"), //전단지상품코드
    "PC" => array("762"), //초대장.엽서상품코드
    "PP" => array("765"), //포토프린트상품코드
    "LF" => array("763"), //리플렛상품코드
    "PK" => array("770"), //패키지상품코드
    "WB" => array("768"), //와블러상품코드
    "SM" => array("769"), //자석스티커상품코드
    "SO" => array("4717","756"), //스티커원형상품코드
    "SE" => array("757"), //스티커타원상품코드
    "SR" => array("758","759","760"), //스티커라운드상품코드
    "PO" => array("752","753"), //포스터상품코드
    
    "OPO" => array("4727","771","1834","1835"), //옵셋-포스터상품코드
    "OJD" => array("772","773"), //옵셋-전단지,리플렛상품코드
    "OBO" => array("4728","774","775","1836"), //옵셋-책자,단행본,카탈로그,브로슈어 상품코드
);

//견적 상품 종류. 가격설정과 화면결정에 사용
//디지털 윤전 추가 / 20181210 / kdk
$_r_inpro_print_goods_kind = array(
	"DG01" => "디지털 일반-명함",
	"DG02" => "디지털 일반-스티커",
	"DG03" => "디지털 낱장-일반",
	"DG04" => "디지털 낱장-스티커(자유형)",	
	"DG05" => "디지털 책자",
	"DG06" => "디지털 낱장-스티커(사각형)",
	"DG07" => "디지털 윤전 낱장",
	"DG08" => "디지털 윤전 책자",
	"OS01" => "옵셋 낱장",
	"OS02" => "옵셋 책자",
	
	"PR01" => "현수막",
	"PR02" => "실사출력",
);


$_r_inpro_print_goods_group = array(
	"CARD" => "명함",
	"POSTER" => "포스터",
	"ETCARD" => "기타낱장",
	"BOOK" => "책자",
);


$_r_inpro_print_direction  = array(
	"STAND" => "세움",
	"LAY" => "눕힘",
);


//견적 상품 타입,스킨.매핑정보 (책자,전단,명함,스티커,포스터등등)
//디지털 윤전 추가 / 20181210 / kdk
$_r_inpro_print_goods_type = array(
    //디지털.
    'DG01' => array('type' => "CARD", 'html' => "print/inter_card.htm"), 
    'DG02' => array('type' => "CARD", 'html' => "print/inter_card.htm"),
    'DG03' => array('type' => "CARD", 'html' => "print/inter_card.htm"),
    'DG04' => array('type' => "CARD", 'html' => "print/inter_card.htm"),
    'DG05' => array('type' => "BOOK", 'html' => "print/inter_book.htm"),
    'DG06' => array('type' => "CARD", 'html' => "print/inter_card.htm"),
    //윤전(디지털).
    'DG07' => array('type' => "BOOK", 'html' => "print/inter_card.htm"),
    'DG08' => array('type' => "CARD", 'html' => "print/inter_book.htm"),
        
    //옵셋.
    'OS01' => array('type' => "CARD", 'html' => "print/inter_card.htm"), 
    'OS02' => array('type' => "BOOK", 'html' => "print/inter_book.htm"),
    
    //현수막,실사출력.
    'PR01' => array('type' => "CARD", 'html' => "print/alaskaprint_card_pr.htm"), 
    'PR02' => array('type' => "BOOK", 'html' => "print/alaskaprint_card_pr.htm")    
);

//견적 상품 타입,스킨.매핑정보 (책자,전단,명함,스티커,포스터등등) - 알래스카용.
//디지털 윤전 추가 / 20181210 / kdk
$_r_alaska_print_goods_type = array(
    //디지털.
    'DG01' => array('type' => "CARD", 'html' => "print/alaskaprint_card.htm"), 
    'DG02' => array('type' => "CARD", 'html' => "print/alaskaprint_card.htm"),
    'DG03' => array('type' => "CARD", 'html' => "print/alaskaprint_card.htm"),
    'DG04' => array('type' => "CARD", 'html' => "print/alaskaprint_card.htm"),
    'DG05' => array('type' => "BOOK", 'html' => "print/alaskaprint_book.htm"),
    'DG06' => array('type' => "CARD", 'html' => "print/alaskaprint_card.htm"),
    //윤전(디지털).
    'DG07' => array('type' => "BOOK", 'html' => "print/alaskaprint_card.htm"),
    'DG08' => array('type' => "CARD", 'html' => "print/alaskaprint_book.htm"),
    
    //옵셋.
    'OS01' => array('type' => "CARD", 'html' => "print/alaskaprint_card.htm"), 
    'OS02' => array('type' => "BOOK", 'html' => "print/alaskaprint_book.htm"),
    
    //현수막,실사출력.
    'PR01' => array('type' => "CARD", 'html' => "print/alaskaprint_card_pr.htm"), 
    'PR02' => array('type' => "BOOK", 'html' => "print/alaskaprint_card_pr.htm")            
);

$r_ipro_print_code = array(

	"A1" => "A1(594mm x 840mm)","A2" => "A2(465mm x 636mm)","A3" => "A3(297mm x 420mm)","A4" => "A4(210mm x 297mm)","A5" => "A5(148mm x 210mm)","A6" => "A6(105mm x 148mm)","B2" => "B2(510mm x 740mm)","B3" => "B3(364mm x 505mm)","B4" => "B4(257mm x 364mm)","B5" => "B5(182mm x 257mm)","B6" => "B6(128mm x 182mm)",			//일반 규격

	"C8" => "크라운판 (176mm x 248mm)","C9" => "인디고판 (240mm x 248mm)",		
	
	"SZ1" => "90*50","SZ2" => "85*55",			//명함 규격
	"SO20" => "10*10","SO21" => "20*20","SO22" => "30*30","SO23" => "40*40","SO24" => "50*50","SO25" => "60*60","SO26" => "70*70","SO27" => "80*80","SO28" => "90*90","SO29" => "100*100",			//스티커 원형
	"SE50" => "25*15","SE51" => "35*25","SE52" => "45*35","SE53" => "55*45","SE54" => "65*55","SE55" => "75*65","SE56" => "85*75","SE57" => "95*85","SE58" => "105*95","SE59" => "115*105",			//스티커 타원
	"SR70" => "35*112","SR71" => "40*20","SR72" => "40*30","SR73" => "40*40","SR74" => "50*20","SR75" => "50*30","SR76" => "50*40","SR77" => "50*50","SR78" => "60*30","SR79" => "60*40","SR80" => "60*50","SR81" => "60*60","SR82" => "70*10","SR83" => "70*20","SR84" => "70*30","SR85" => "70*40","SR86" => "70*50","SR87" => "70*60","SR88" => "85*54","SR89" => "60*20",			//스티커 라운드
	"MB1" => "190*420","MB2" => "150*300",			//미니배너
	"OC1" => "단면4도(앞컬러)",			//단면 인쇄 -칼라 
	"OB2" => "단면1도(앞1도) / 인디고","OB3" => "누베라 단면 1도(흑백)",			//단면인쇄 - 흑백
	"DC6" => "양면8도(앞뒤컬러)",			//양면인쇄 -칼러
	"DB7" => "양면2도(앞1도, 뒤1도) / 인디고","DB8" => "양면2도(앞1도, 뒤1도) / 누베라",			//양면인쇄 -흑백
	"ET1" => "그린","ET2" => "바이올렛","ET3" => "오렌지","ET4" => "화이트","ET5" => "팬톤","ET6" => "팬톤 베다","ET7" => "금","ET8" => "금 베다","ET9" => "은","ET10" => "은 베다",			//별색인쇄 
	"CT1" => "무광단면","CT2" => "유광단면","CT3" => "무광양면","CT4" => "유광양면",			//코팅 
	"PC1" => "1구","PC2" => "2구","PC3" => "3구","PC4" => "4구",			//타공 
	"OS1" => "1줄","OS2" => "2줄","OS3" => "3줄","OS4" => "4줄","OS5" => "5줄","OS6" => "6줄","OS7" => "7줄","OS8" => "8줄",			//오시 
	"MS1" => "1줄","MS2" => "2줄","MS3" => "3줄","MS4" => "4줄","MS5" => "5줄","MS6" => "6줄","MS7" => "7줄","MS8" => "8줄",			//미싱 
	"RD1" => "2귀도리(우상좌하)","RD2" => "2귀도리(좌상우하)","RD3" => "4귀도리",			//귀도리 
	"DM1" => "없음","DM2" => "있음",			//도무송 
	"BC1" => "없음","BC2" => "있음",			//바코드 
	"NR1" => "없음","NR2" => "있음",			//넘버링 
	"SD1" => "없음","SD2" => "있음",			//스탠드 
	"DG1" => "없음","DG2" => "있음",			//댕글 
	"DT1" => "없음","DT2" => "있음",			//양면테잎 
	"AP1" => "없음","AP2" => "있음",			//주소인쇄 
	"SC1" => "스코딕스(부분UV)안함","SC2" => "스코딕스(부분UV)",			//스코딕스 
	"SB1" => "금박","SB2" => "은박","SB3" => "청박","SB4" => "적박","SB5" => "녹박","SB6" => "먹박","SB7" => "홀로그램박",			//스코딕스박 
	"WI1" => "날개없음","WI2" => "날개있음",			//날개 
	"BD1" => "무선","BD2" => "무선날개","BD3" => "중철","BD4" => "링제본","BD5" => "스프링제본(PVC커버투명)","BD6" => "스프링제본(반투명)","BD7" => "제본안함",			//제본 
	"BT1" => "가로","BT2" => "세로",			//제본 타입 
	"CU2" => "재단","CU1" => "재단없음",			//재단 
	"IN2" => "즉석명함","IN1" => "없음",			//즉석명함 

    "HD1" => "십자접지","HD2" => "2단접지","HD3" => "3단접지","HD4" => "N접지","HD5" => "4단접지","HD6" => "4단두루마리접지","HD7" => "대문접지","HD8" => "6단병풍접지","HD9" => "5단병품접지","HD10" => "4단병풍접지",          //접지(옵셋) 
    "PR1" => "없음","PR2" => "있음",            //형압 
    "FL1" => "없음","FL2" => "금박(유광)","FL3" => "금박(무광)","FL4" => "은박(유광)","FL5" => "은박(무광)","FL6" => "청박","FL7" => "녹박","FL8" => "먹박","FL9" => "홀로그램박","FL10" => "적박","FL11" => "펄박",           //박(옵셋) 
    "UV1" => "없음","UV2" => "유광",            //부분UV(옵셋) 	
	
	"SCS01" => "10","SCS02" => "20","SCS03" => "30","SCS04" => "40","SCS05" => "50","SCS06" => "60","SCS07" => "70","SCS08" => "80","SCS09" => "90","SCS10" => "100","SCS11" => "120","SCS12" => "140","SCS13" => "200","SCS14" => "280","SCS15" => "400",			//스티커 비규격 

#
    "SPR1" => "1000*1000","SPR2" => "841*594","SPR3" => "420*594","SPR4" => "297*420","SPR5" => "210*297","SPR6" => "515*728","SPR7" => "364*515","SPR8" => "257*364","SPR9" => "176*250",          //현수막 실사출력 사이즈
    
    
    /*"ECT1" => "단면무광콜드코팅","ECT2" => "양면무광콜드코팅","ECT3" => "단면유광콜드코팅","ECT4" => "양면유광콜드코팅",            //코팅(현수막 실사출력) 
    "ECU1" => "사각재단","ECU2" => "모양재단-기본모양","ECU3" => "모양재단-다각형모양","ECU4" => "모양재단-복잡한모양",           //재단(현수막 실사출력) 
    "EDS1" => "없음","EDS2" => "디자인 작업 포함",           //디자인(현수막 실사출력) 
    "EPC1" => "열재단","EPC2" => "막대 가공(~120cm까지)","EPC3" => "유리 접착용 고무","EPC4" => "금속링","EPC5" => "끈고리 가공","EPC6" => "봉미싱",           //가공&마감(현수막 실사출력)*/
    
    "ECT1" => "없음","ECT2" => "있음","ECT3" => "단면 무광 콜드코팅","ECT4" => "단면 유광 콜드코팅",           //코팅(현수막 실사출력) 
    "ECU1" => "사각재단","ECU2" => "모양재단-기본모양","ECU3" => "모양재단-다각형모양","ECU4" => "모양재단-복잡한모양",           //재단(현수막 실사출력) 
    "EDS1" => "없음 - 0원","EDS2" => "원본파일 첨부(출력의뢰) - 0원","EDS3" => "원본파일_글자 또는 이미지 교체 - 5,000원","EDS4" => "이미지 파일 첨부 후 작업 - 15,000원","EDS5" => "신규 디자인 작업 - 20,000원",         //디자인(현수막 실사출력) 
    "EPC1" => "열재단","EPC2" => "사방 타공","EPC3" => "사방 줄미싱","EPC4" => "타공+큐방","EPC5" => "타공+로프(3m)","EPC6" => "각목+로프(3m)","EPC7" => "끈고리+로프(3m)","EPC8" => "블라인드","EPC9" => "재단","EPC10" => "타공","EPC11" => "타공+로프",           //가공&마감(현수막 실사출력) 
);

//opt_prefix 에 해당하는 후가공 명침	. 전체 옵션 말고 헤갈리는 옵션만 명칭을 나오게 하자.
$r_opt_prefix_group = array(
	//"SZ" => "규격","SZ" => "규격","SO" => "규격","SE" => "규격","SR" => "규격"	,"A" => "규격","B" => "규격","C" => "규격", "SCS" => "스티커규격",
	//"OC" => "인쇄","OB" => "인쇄","DC" => "인쇄","DB" => "인쇄","ET" => "별색",
	//"CT" => "코팅","RD" => "귀도리"
	
	"PC" => "타공","OS" => "오시","MS" => "미싱","DM" => "도무송","BC" => "바코드",
	"NR" => "넘버링","SD" => "스탠드","DG" => "댕글","DT" => "테이프","AP" => "주소인쇄",
	"WI" => "날개","BD" => "제본","BT" => "제본방향",
	"CU" => "재단","IN" => "즉석명함","HD" => "접지","PR" => "형압","UV" => "부분UV","MB" => "미니배너",
		
	//"SC" => "스코딕스박","SB" => "스코딕스","FL" => "박",
	//"PPE" => "지류","PLC" => "지류","PLU" => "지류","PML" => "지류","PNC" => "지류","PNM" => "지류","PSP" => "지류","PPK" => "지류","PPR" => "지류","PPT" => "지류","PSB" => "지류","PST" => "지류"	
); 
	
//인쇄 화면 출력을 위한 고정값 (실제 코드와 연관없음.)
$r_ipro_print_sub_item = array("C" => "컬러", "B" => "흑백", "N" => "흑백(누베라)", "D" => "양면", "O" => "단면");


$r_ipro_standard_size = array(
	"A1" => array("name" => "A1", "size_x" => "594", "size_y" => "840"),
	"A2" => array("name" => "A2", "size_x" => "420", "size_y" => "594"), 
	"A3" => array("name" => "A3", "size_x" => "297", "size_y" => "420"),
	"A4" => array("name" => "A4", "size_x" => "210", "size_y" => "297"),
	"A5" => array("name" => "A5", "size_x" => "148", "size_y" => "210"),
	"A6" => array("name" => "A6", "size_x" => "105", "size_y" => "148"),
	
	"B2" => array("name" => "B2", "size_x" => "514", "size_y" => "728"),
	"B3" => array("name" => "B3", "size_x" => "364", "size_y" => "514"),
	"B4" => array("name" => "B4", "size_x" => "257", "size_y" => "364"),
	"B5" => array("name" => "B5", "size_x" => "182", "size_y" => "257"),
	"B6" => array("name" => "B6", "size_x" => "128", "size_y" => "182"),
	
	"C8" => array("name" => "크라운판", "size_x" => "176", "size_y" => "248"),
	"C9" => array("name" => "인디고판", "size_x" => "240", "size_y" => "248"),
);


$r_ipro_standard_digital = array(
	"A2" => array("B2" => "1", "A3" => "0"), 
	"A3"=> array("B2" => "2", "A3" => "1"),
	"A4" => array("B2" => "4", "A3" => "2"),
	"A5" => array("B2" => "8", "A3" => "4"),
	"A6" => array("B2" => "16", "A3" => "8"),
	
	"B2" => array("B2" => "1", "A3" => "0"),
	"B3" => array("B2" => "2", "A3" => "0"),
	"B4" => array("B2" => "2", "A3" => "1"),
	"B5" => array("B2" => "4", "A3" => "2"),
	"B6" => array("B2" => "8", "A3" => "4"),
	
	"C8" => array("B2" => "0", "A3" => "2"),
	"C9" => array("B2" => "0", "A3" => "2"),
);


//디지털 인쇄견적 인쇄비 / 후가공비 계산을 위한 기준 규격과 조판 수량
$r_ipro_standard_print_digital = array(
	"A2" => array("B2" => "1", "A3" => "0"), 
	"A3"=> array("B2" => "0", "A3" => "1"),
	"A4" => array("B2" => "0", "A3" => "2"),
	"A5" => array("B2" => "0", "A3" => "4"),
	"A6" => array("B2" => "0", "A3" => "8"),
	
	"B2" => array("B2" => "1", "A3" => "0"),
	"B3" => array("B2" => "2", "A3" => "0"),
	"B4" => array("B2" => "0", "A3" => "1"),
	"B5" => array("B2" => "0", "A3" => "2"),
	"B6" => array("B2" => "0", "A3" => "4"),
	
	"C8" => array("B2" => "0", "A3" => "2"),
	"C9" => array("B2" => "0", "A3" => "2"),
);

//옵션별 엑셀의 가격테이블의 수량 문구 설정. 
$r_ipro_opt_mode_paper_unit = array(
	"default" => "수량(장)",
	"print_book_inside_digital" => "수량(페이지)",
	"bind_digital" => "수량(부/권)",

	"domoo_sticker_digital" => "하리꼬미 수량 (개)",
	"domoo_sticker_other_digital" => "하리꼬미 장수(장)",
	"domoo_sticker_square_digital" => "주문수량(개)",
	
	"print_opset" => "수량(연)",	
	"gloss_opset" => "수량(연)",
	"punch_opset" => "수량(연)",
	"oshi_opset" => "수량(연)",
	"missing_opset" => "수량(연)",
	"round_opset" => "수량(연)",
	"domoo_opset" => "수량(연)",
	"barcode_opset" => "수량(연)",
	"cutting_opset" => "수량(연)",		
	"bind_opset" => "수량(부/권)",	
	"foil_opset" => "수량(연)",
	"press_opset" => "수량(연)",
	"holding_opset" => "수량(연)",
	
    "bind_BD1_opset" => "수량(장)",
    "bind_BD3_opset" => "부수",
);

//용지 mm.
$r_ipro_standard_paper_width = array(
    "PLU04" => array(
            "105" => "0.13",
            "130" => "0.17",
            "160" => "0.2",
            "190" => "0.255",
            "210" => "0.275",
            "240" => "0.326"
        ),
    "PLU06" => array(
            "105" => "0.13",
            "130" => "0.165",
            "160" => "0.203",
            "190" => "0.239",
            "210" => "0.278",
            "240" => "0.313"
        ),
    "PLU12" => array(
            "90" => "0.117",
            "100" => "0.136",
            "130" => "0.187",
            "160" => "0.224",
            "190" => "0.265",
            "210" => "0.289",
            "240" => "0.334"
        ),
    "PLU14" => array(
            "105" => "0.11"
        ),
    "PLU23" => array(
            "105" => "0.15",
            "130" => "0.18",
            "160" => "0.22",
            "190" => "0.26",
            "210" => "0.28",
            "230" => "0.32"
        ),
    "PLU24" => array(
            "105" => "0.15",
            "130" => "0.18",
            "160" => "0.22",
            "190" => "0.26",
            "210" => "0.28",
            "230" => "0.31"
        ),
    "PNM01" => array(
            "80" => "0.077",
            "100" => "0.099"
        ),
    "PNM03" => array(
            "80" => "0.077",
            "100" => "0.099"
        ),
    "PNM05" => array(
            "80" => "0.093",
            "100" => "0.114",
            "120" => "0.137"
        ),
    "PNM07" => array(
            "80" => "0.09",
            "100" => "0.115",
            "120" => "0.137",
            "150" => "0.163",
            "180" => "0.196",
            "220" => "0.238"
        ),
    "PNM09" => array(
            "100" => "0.088",
            "120" => "0.107",
            "150" => "0.142",
            "180" => "0.179",
            "200" => "0.212",
            "250" => "0.26",
            "300" => "0.318"
        ),
    "PNM11" => array
        (
            "100" => "0.079",
            "120" => "0.096",
            "150" => "0.121",
            "180" => "0.154",
            "200" => "0.192",
            "250" => "0.241",
            "300" => "0.296"
        )
);

//현수막,실사출력 규격 정보
$r_ipro_pr_standard_size = array(
    "SPR1" => array("name" => "헤배", "size_x" => "1000", "size_y" => "1000"),
    "SPR2" => array("name" => "A1", "size_x" => "841", "size_y" => "594"),
    "SPR3" => array("name" => "A2", "size_x" => "420", "size_y" => "594"), 
    "SPR4" => array("name" => "A3", "size_x" => "297", "size_y" => "420"),
    "SPR5" => array("name" => "A4", "size_x" => "210", "size_y" => "297"),
    "SPR6" => array("name" => "B2", "size_x" => "514", "size_y" => "728"),
    "SPR7" => array("name" => "B3", "size_x" => "364", "size_y" => "515"),
    "SPR8" => array("name" => "B4", "size_x" => "257", "size_y" => "364"),
    "SPR9" => array("name" => "B5", "size_x" => "176", "size_y" => "250"),
);
?>