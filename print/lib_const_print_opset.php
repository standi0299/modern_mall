<?
/*
* @date : 20180530
* @author : chunter
* @brief : 기본 견적 설정 const 내용 삭제.
*/

/*
* @date : 201804010
* @author : chunter
* @brief : 인터프로 자동견적 관련 옵셋 상품 셋팅
* @desc : 자동견적 관련 옵션, 항목들은 모두 하드코딩으로 관리한다.
*/
?>
<?


$r_ipro_standard_opset = array(
	"A1" => array("B2" => "0", "A1" => "1"),
	"A2" => array("B2" => "0", "A1" => "2"), 
	"A3" => array("B2" => "0", "A1" => "4"),
	"A4" => array("B2" => "0", "A1" => "8"),
	"A5" => array("B2" => "0", "A1" => "16"),
	"A6" => array("B2" => "0", "A1" => "32"),
	
	"B2" => array("B2" => "1", "A1" => "0"),
	"B3" => array("B2" => "2", "A1" => "0"),
	"B4" => array("B2" => "4", "A1" => "0"),
	"B5" => array("B2" => "8", "A1" => "0"),
	"B6" => array("B2" => "16", "A1" => "0"),
	
	"CN" => array("B2" => "0", "A1" => "0"),
	"IG" => array("B2" => "0", "A1" => "0"),
);


//대당여분수량 (인쇄로스)		//단면, 양면, 흑백, 칼러
$r_ipro_lose_opset = array(	
	"O" => array("B" => "100", "C" => "150"),			//단면
	"D" => array("B" => "150", "C" => "200"),			//양면
);


$r_ipro_ctp_opset = array(	
	"1" => "7000",			//1판당 가격	
);



//형압  동판 가격. mm2 단가
$r_ipro_press_mm2_opset = array(
	"PR2" => array("1" => "10"),
);	

//박 동판 가격, mm2 단가
$r_ipro_foil_mm2_opset = array(
	"FL2" => array("1" => "10"),
	"FL3" => array("1" => "10"),
	"FL4" => array("1" => "10"),
	"FL5" => array("1" => "10"),
	"FL6" => array("1" => "10"),
	"FL7" => array("1" => "10"),
	"FL8" => array("1" => "10"),
	"FL9" => array("1" => "10"),
);

//부분UV 동판 가격. mm2 단가
$r_ipro_uvc_mm2_opset = array(
	"UV2" => array("1" => "10"),
);

//도무송 동판가격 . mm2 단가
$r_ipro_domoo_mm2_opset = array(
	"DM2" => array("1" => "10"),
);

//무선제본 기본 페이지수
$r_ipro_bind_BD1_default = array("page" => "64");
$r_ipro_bind_BD1_page_gram = array("120" => "0.8", "140" => "1", "150" => "1.2");			//120이하, 140이하, 150이상

//중철제본 기본 페이지수
$r_ipro_bind_BD3_default = array("page" => "32", "cnt" => "1000", "price" => 60000);
$r_ipro_bind_BD3_page_gram = array("120" => "10", "140" => "13", "150" => "15");			//120이하, 140이하, 150이상



?>