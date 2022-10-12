<?

$r_column = array(
    //필수항목
    "no"                                => "no",
	"order_name"                        => _("주문자명"),
    "orddt"                             => _("주문일자"),
    "goodsnm"                           => _("상품명"),
    "goods_price"                       => _("단가"),
    "order_file_cnt"                    => _("페이지"),
    "goods_pay"                         => _("상품금액"),
    "addopt_aprice"                     => _("추가옵션단가"),
    "ea"                                => _("수량"),
    "total_price"                       => _("총상품금액"),
    "delivery_normal_price"             => _("일반배송비"),
    "total_price_delevery_basic_price"  => _("총액+배송비"),
    
    "dc_emoney"                         => _("적립금사용"),
    "dc_member"                         => _("그룹할인"),
    "dc_coupon"                         => _("쿠폰할인"),
    "dc_coupon_name"                    => _("쿠폰명칭"),
    "dc_coupon_issue_code"              => _("쿠폰번호"),
    "payprice"                          => _("결제금액"),
    "itemstep"                          => _("주문아이템상태"),
    "product_match_name"                => _("매칭상품명"),
    "shipdt"                            => _("발송일자"),
    "shipcomp"                          => _("택배사명칭"),
    "shipcode"                          => _("운송장번호"),
    "paymethod"                         => _("결제수단"),
        
    //부분적으로 필요한 항목
    "opt1"                              => _("상품1차옵션"),
    "opt2"                              => _("상품2차옵션"),
    "addopt_str"                        => _("추가옵션"),
    "printopt_str"                      => _("인화옵션"),
    "supply_goods"                      => _("상품공급가"),
    "supply_opt"                        => _("상품옵션공급가"),
    "price_opt"                         => _("상품옵션판매가"),
    "supply_addopt"                     => _("추가옵션공급가"),
    "price_addopt"                      => _("추가옵션판매가"),
    "supply_printopt"                   => _("인화공급가"),
    "price_printopt"                    => _("인화판매가"),
    "supply_addpage"                    => _("추가페이지공급가"),
    "price_addpage"                     => _("추가페이지판매가"),
    "supply"                            => _("공급가총액"),
    "input_flag"                        => _("자동/수동")
);

#병합셀 정의
$r_rowspan[orddt] = array(
    "order_name",
    "orddt",
    "total_price_delevery_basic_price",
    "dc_emoney",
    "payprice",
    "paymethod"
);

$r_rowspan[ordno] = array(
    "order_name",
    "orddt",
    "payprice",
    "paystep",
    "goods_price",
    "order_file_cnt"
);

$r_num_flds = array(
	"addpage",
	"ea",
	"supply_goods",
	"goods_price",
	"supply_opt",
	"aprice",
	"supply_addopt",
	"addopt_aprice",
	"supply_printopt",
	"print_aprice",
	"supply_addpage",
	"addpage_aprice",
	"shipprice",
	"shipprice",
	"supply",
	"price",
);

?>