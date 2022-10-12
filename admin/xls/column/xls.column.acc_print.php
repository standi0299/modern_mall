<?

$r_column = array(
	"goodsno"				=> _("상품코드"),
	"goodsnm"				=> _("상품명"),
	"rid_compnm"			=> _("제작사"),
	"printoptnm"			=> _("인화규격"),
	"supplyprice_printopt"	=> _("공급가"),
	//"price_printopt"		=> "판매가",
	"print_aprice"			=> _("판매가"),
	"ea"					=> _("주문수량"),
	"sum_price_printopt"	=> _("주문금액"),
	/*"ea_minus"					=> "취소수량",
	"sum_price_printopt_minus"	=> "취소금액",
	"ea_minus_ext"					=> "기간외취소수량",
	"sum_price_printopt_minus_ext"	=> "기간외취소금액",*/
	"ea_total"						=> _("합계수량"),
	"sum_price_printopt_total"		=> _("합계금액"),
);

$r_rowspan[goodsno] = array(
	"goodsno",
	"goodsnm",
	"rid_compnm",
);

$r_num_flds = array(
	"supplyprice_printopt",
	"print_aprice",
	"ea",
	"sum_price_printopt",
);

?>