<?

//"custom"=>"커스텀 디자인(미지원)", 미지원 항목 미노출 요청으로 주석 처리 / 17.08.28 / kdk
$_r_dg_top_block = array("default"=>_("기본형 디자인"), "istorybook"=>_("아이락"),"interpro"=>"MITP V1","fotocube"=>"MFTC V1"); // "custom"=>"커스텀 디자인(미지원)",
$_r_dg_footer_block = array("default"=>_("기본형 디자인"), "istorybook"=>_("아이락"),"interpro"=>"MITP V1","fotocube"=>"MFTC V1"); //"custom"=>"커스텀 디자인(미지원)",
$_r_dg_main_slide_block = array("default"=>_("기본형 슬라이드"), "ilark"=>_("기본확장형 슬라이드"), "istorybook"=>_("배경, 이미지 별도형 슬라이드"),"interpro"=>"MITP V1","fotocube"=>"MFTC V1"); //"custom"=>"커스텀 디자인(미지원)",
$_r_dg_goods_quick_option_block = array("miodio"=>_("기본형 디자인"),);

$_r_mdn_goodslist_extra_kind = array(
	"hot" => array("addtion_goods_kind" => "hot", "display" => _("Hot 상품"), "use_page" => array("/goods/list.php", "/goods/view.php")),
	"best" => array("addtion_goods_kind" => "best", "display" => _("Best 상품"), "use_page" => array("/goods/list.php", "/goods/view.php")),
	"new" => array("addtion_goods_kind" => "new", "display" => _("New 상품"), "use_page" => array("/goods/list.php", "/goods/view.php")),
	"choice" => array("addtion_goods_kind" => "choice", "display" => _("MD 선택 상품"), "use_page" => array("/goods/list.php", "/goods/view.php")),
	"recomand" => array("addtion_goods_kind" => "recomand", "display" => _("추천 상품"), "use_page" => array("/goods/list.php", "/goods/view.php")),
	"relation" => array("addtion_goods_kind" => "relation", "display" => _("연관 상품"), "use_page" => array("/goods/list.php", "/goods/view.php")),
);


$_r_mdn_main_block = array(
	array("code"=>"main_block_01", "display" => _("Hot 상품"), "addtion_goods_kind" => "hot"),
	array("code"=>"main_block_02", "display" => _("Hot 상품"), "addtion_goods_kind" => "best"),
	array("code"=>"main_block_03", "display" => _("Hot 상품"), "addtion_goods_kind" => "new"),
	array("code"=>"main_block_04", "display" => _("Hot 상품"), "addtion_goods_kind" => "choice"),
	array("code"=>"main_block_05", "display" => _("Hot 상품"), "addtion_goods_kind" => "hit"),
	array("code"=>"main_block_06", "display" => _("Hot 상품"), "addtion_goods_kind" => "hot"),
	array("code"=>"main_block_07", "display" => _("Hot 상품"), "addtion_goods_kind" => "hot"),
	array("code"=>"main_block_08", "display" => _("Hot 상품"), "addtion_goods_kind" => "hot"),
	array("code"=>"main_block_09", "display" => _("Hot 상품"), "addtion_goods_kind" => "hot"),
	array("code"=>"main_block_10", "display" => _("Hot 상품"), "addtion_goods_kind" => "hot"),	
	array("code"=>"main_block_11", "display" => _("canvas 상품"), "addtion_goods_kind" => "canvas"),
);

//상품 정렬 우선순위 설정의 우선순위 목록 / 17.04.26 / kjm
$_r_mdn_goods_sort = array(
   "popular" => _("인기상품"),
   "new" => _("신상품") 
);

### 선택값
$_r_select = array(
    0   => _('사용안함'),
    9   => _('사용함'),
    );

### 선택값배경색
$_r_admin_menu_select_color = array(
    0   => '#FFC0C0',
    9   => '#C0F9CC',
);

$_r_right_slide_not_use_page = array("/member/login.php", "/member/register.php")
?>