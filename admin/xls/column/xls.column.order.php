<?

$r_column = array(
	"cid"					=> _("분양몰아이디"),
	"compnm"				=> _("업체명"),
	"sitenm"				=> _("사이트명"),
	"domain"				=> _("도메인"),
	"payno"					=> _("결제번호"),
	"ordno"					=> _("주문번호"),
	"ordseq"				=> _("주문아이템번호"),
	"itemstepstr"			=> _("주문아이템상태"),
	"payprice"				=> _("결제금액"),
	"reserve"				=> _("적립예정액"),
	"paid"					=> _("입금여부"),
#	"paymethod"				=> "결제수단코드",
	"paymethodstr"			=> _("결제수단"),
	"bankinfo"				=> _("입금은행 및 계좌"),
#	"pgcode"				=> "PG Code",
#	"PG Key",
	"pglog"					=> _("PG로그"),
	"escrow"				=> _("에스크로"),
	"goodsno"				=> _("상품코드"),
	"goodsnm"				=> _("상품명칭"),
	"cmatch_goodsnm"		=> _("매칭상품명"),
	"brandnm"				=> _("브랜드"),
	"podskind"				=> _("편집기코드"),
#	"catno"					=> "상품분류코드",
#	"catno_str"				=> "상품분류",
	"opt1"					=> _("상품1차옵션"),
	"opt2"					=> _("상품2차옵션"),
	"addopt_str"			=> _("추가옵션"),
	"pirntopt_str"			=> _("인화옵션"),
	"addpage"				=> _("추가페이지수"),
#	"rid"					=> "공급사아이디",
#	"rid_compnm"			=> "공급사회사명",
	"nicknm"				=> _("공급사별칭"),
	"ea"					=> _("주문수량"),
#	"cost_goods"			=> "상품원가",
	"supply_goods"			=> _("상품공급가"),
	"price_goods"			=> _("상품판매가"),
#	"cost_opt"				=> "상품옵션원가",
	"supply_opt"			=> _("상품옵션공급가"),
	"price_opt"				=> _("상품옵션판매가"),
#	"cost_addopt"			=> "추가옵션원가",
	"supply_addopt"			=> _("추가옵션공급가"),
	"price_addopt"			=> _("추가옵션판매가"),
#	"cost_printopt"			=> "인화원가",
	"supply_printopt"		=> _("인화공급가"),
	"price_printopt"		=> _("인화판매가"),
#	"cost_addpage"			=> "추가페이지원가",
	"supply_addpage"		=> _("추가페이지공급가"),
	"price_addpage"			=> _("추가페이지판매가"),
#	"cost"					=> "원가총액",
	"supply"				=> _("공급가총액"),
	"price"					=> _("판매가총액"),
	"dc_member"				=> _("그룹할인"),
	"dc_coupon"				=> _("쿠폰할인"),
	"dc_coupon_code"		=> _("쿠폰코드"),
	"dc_coupon_name"		=> _("쿠폰명칭"),
	"dc_coupon_issue_code"	=> _("쿠폰번호"),
	"dc_emoney"				=> _("적립금사용"),
	"storageid"				=> _("보관함코드"),
	"pods_trans"			=> _("통신결과"),
	"shipprice"				=> _("일반배송비"),
#	"포장(지관)비",
#	"도서산간 추가배송비",
#	"배송수단코드",
#	"배송수단명칭",
 	"shipcomp"				=> _("택배사코드"),
	"shipcomp_oasis"		=> _("OASIS택배사코드"),
	"shipcompnm"			=> _("택배사명칭"),
	"shipcode"				=> _("운송장번호"),
#	"배송비할인쿠폰코드",
#	"배송비할인쿠폰명칭",
#	"배송비할인쿠폰번호",
#	"배송비할인금액",
	"orderer_name"			=> _("주문자명"),
	"payer_name"			=> _("입금자명"),
	"orderer_phone"			=> _("주문자연락처1"),
	"orderer_mobile"		=> _("주문자연락처2"),
	"orderer_email"			=> _("이메일"),
	"receiver_name"			=> _("수신자명"),
	"receiver_phone"		=> _("수신자연락처1"),
	"receiver_mobile"		=> _("수신자연락처2"),
	"receiver_zipcode"		=> _("우편번호"),
	"receiver_addr"			=> _("검색주소지"),
	"receiver_addr_sub"		=> _("상세주소지"),
	"request2"				=> _("추가메모"),
	"request"				=> _("남기는 말"),
	"memo"					=> _("관리자메모"),
	"orddt"					=> _("주문일자"),
	"paydt"					=> _("결제/승인일자"),
	"confirmadmin"			=> _("승인자ID(신용거래)"),
	"shipdt"				=> _("발송일자"),
	"canceldt"				=> _("취소일자"),
	"completedt"            => _("환불일자"),
	"mid"					=> _("주문자아이디"),
	"manager_name"			=> _("정산담당자명"),
	"manager_email"			=> _("정산담당자이메일"),
	"manager_phone"			=> _("정산담당자연락처"),
	"manager_mobile"		=> _("정산담당자휴대폰"),
	"manager_dep"			=> _("정산담당부서명"),
	"cust_name"				=> _("사업자명"),
	"cust_type"				=> _("거래처업태"),
	"cust_class"			=> _("거래처업종"),
	"cust_tax_type"			=> _("사업자등록유형"),
	"cust_no"				=> _("사업자등록번호"),
	"cust_ceo"				=> _("대표자명"),
	"cust_ceo_phone"		=> _("대표자연락처"),
	"cust_zipcode"			=> _("사업장우편번호"),
	"cust_address"			=> _("사업장주소"),
	"cust_address_en"		=> _("사업장영문주소"),
	"cust_phone"			=> _("사업장전화번호"),
	"cust_fax"				=> _("사업장팩스번호"),
	"cust_bank_name"		=> _("주거래은행"),
	"cust_bank_no"			=> _("주거래계좌번호"),
	"cust_bank_owner"		=> _("주거래계좌예금주"),
	"etc1"					=> _("추가정보1"),
	"etc2"					=> _("추가정보2"),
	"etc3"					=> _("추가정보3"),
	"etc4"					=> _("추가정보4"),
	"etc5"					=> _("추가정보5"),
	"b2b_goodsno 상품코드"  => _("제휴사 상품코드"),
    "csummary"              => _("간략설명"),
    "csearch_word"          => _("상품검색어"),
    "cresolution"           => _("권장해상도"),
    "cgoods_size"           => _("상품사이즈"),
    "cetc1"                 => _("상품특이사항1"),
    "cetc2"                 => _("상품특이사항2"),
    "cetc3"                 => _("상품특이사항3"),
    "vdp_name"				=> _("이름(신청내용)"),
    "vdp_department"		=> _("부서(신청내용)")
    
/*	"사업자등록번호",
	"~ 사업자정보 필드 일체*/
);

#병합셀 정의
$r_rowspan[payno] = array(
	"cid",
	"compnm",
	"sitenm",
	"domain",
	"payno",
	//아이템스텝별 조회에서 엑셀 저장시 상품금액이 가장 위의 상품 금액만 나옴
	//payprice를 제거해서 각 상품별 금액이 출력되도록 조정 / 14.04.25 / kjm
//	"payprice",
	"paid",
	"paymethod",
	"paymethodstr",
	"bankinfo",
	"pgcode",
	"pglog",
	"escrow",
	"dc_emoney",
	"orderer_name",
	"payer_name",
	"orderer_phone",
	"orderer_mobile",
	"orderer_email",
	"receiver_name",
	"receiver_phone",
	"receiver_mobile",
	"receiver_zipcode",
	"receiver_addr",
	"receiver_addr_sub",
	"request2",
	"request",
	"memo",
	"orddt",
	"paydt",
	"confirmadmin",
	"canceldt",
	"mid",
	"manager_name",
	"manager_email",
	"manager_phone",
	"manager_mobile",
	"manager_dep",
	"cust_name",
	"cust_type",
	"cust_class",
	"cust_tax_type",
	"cust_no",
	"cust_ceo",
	"cust_ceo_phone",
	"cust_zipcode",
	"cust_address",
	"cust_address_en",
	"cust_phone",
	"cust_fax",
	"cust_bank_name",
	"cust_bank_no",
	"cust_bank_owner",
	"etc1",
	"etc2",
	"etc3",
	"etc4",
	"etc5",
);

$r_rowspan[ordno] = array(
	"ordno",
	"rid",
	"rid_compnm",
	"nicknm",
	"shipprice",
);

$r_num_flds = array(
	"payprice",
	"reserve",
	"ea",
	"cost_goods",
	"supply_goods",
	"price_goods",
	"cost_opt",
	"supply_opt",
	"price_opt",
	"cost_addopt",
	"supply_addopt",
	"price_addopt",
	"cost_printopt",
	"supply_printopt",
	"price_printopt",
	"cost_addpage",
	"supply_addpage",
	"price_addpage",
	"cost",
	"supply",
	"price",
	"dc_member",
	"dc_coupon",
	"dc_emoney",
	"shipprice",
);

?>