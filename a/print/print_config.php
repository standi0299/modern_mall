<?
/*
* @date : 20190320
* @author : kdk
* @brief : 견적 상품 추가 현수막(Placard),실사출력(RealPrint)
* @desc : 현수막 (PR01),실사출력(PR02)
*/

include "../_header.php";
include "../_left_menu.php";

//일반 인쇄 설정 가격으로 처리함.(DG01 디지털 일반-명함,DG02 디지털 일반-스티커)
//상품조회.
$m_print = new M_print();
$data = $m_print->getNormalGoodsList($cid);

if($cfg[extra_use])
    $extra_use = json_decode($cfg[extra_use],1);
?>

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("견적가격설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("견적가격설정")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("견적가격설정")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered">
                  <div class="form-group"> 
                    <!--<label class="col-md-2 control-label"><?=_("표준 규격 설정")?></label>
                    <div class="col-md-10">
                        <button type="button" class="btn btn-xs btn-success disabled" onclick=";" ><?=_("설정")?></button>
                    </div>-->
                    
                    <label class="col-md-2 control-label"><?=_("지류 설정")?></label>
                    <div class="col-md-10">
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_paper_price.php');" ><?=_("가격")?></button>
<button type="button" class="btn btn-xs btn-inverse" onclick="windowopenPopup('option_paper_dc.php');" ><?=_("할인율 ")?></button>
                    </div>
                    
                    <label class="col-md-2 control-label"><?=_("디지털 인쇄비 설정")?></label>
                    <div class="col-md-10">
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=print_digital&opt_group=PRINT');" ><?=_("디지털 낱장")?></button>
<button type="button" class="btn btn-xs btn-inverse" onclick="windowopenPopup('option_items_dc.php?opt_mode=print_dc_digital&opt_group=PRINT');" ><?=_("디지털 낱장 할인율")?></button>

<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=print_book_inside_digital&opt_group=PRINT');" ><?=_("디지털 책자(내지)")?></button>
<button type="button" class="btn btn-xs btn-inverse" onclick="windowopenPopup('option_items_dc.php?opt_mode=print_book_inside_dc_digital&opt_group=PRINT');" ><?=_("디지털 책자(내지) 할인율")?></button>

<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=print_rotary_digital&opt_group=PRINT');" ><?=_("디지털 윤전 낱장")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=print_book_inside_rotary_digital&opt_group=PRINT');" ><?=_("디지털 윤전 책자(내지)")?></button>
                    </div>
                    <label class="col-md-2 control-label"><?=_("디지털 후가공 설정")?></label>
                    <div class="col-md-10">
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=gloss_digital&opt_group=GLOSS');" ><?=_("코팅")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=punch_digital&opt_group=PUNCH');" ><?=_("타공")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=oshi_digital&opt_group=OSHI');" ><?=_("오시")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=missing_digital&opt_group=MISSING');" ><?=_("미싱")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=round_digital&opt_group=ROUND');" ><?=_("귀도리")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=domoo_digital&opt_group=DOMOO');" ><?=_("도무송")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=domoo_sticker_square_digital&opt_group=DOMOO');" ><?=_("도무송")?>(사각형 스티커)</button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=domoo_sticker_digital&opt_group=DOMOO');" ><?=_("도무송")?>(자유형 스티커 1장)</button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=domoo_sticker_other_digital&opt_group=DOMOO');" ><?=_("도무송")?>(자유형 스티커 2장이상)</button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=barcode_digital&opt_group=BARCODE');" ><?=_("바코드")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=number_digital&opt_group=NUMBER');" ><?=_("넘버링")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=stand_digital&opt_group=STAND');" ><?=_("스탠드(미니배너)")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=dangle_digital&opt_group=DANGLE');" ><?=_("댕글(와블러)")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=tape_digital&opt_group=TAPE');" ><?=_("양면테잎(봉투)")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=address_digital&opt_group=ADDRESS');" ><?=_("주소인쇄(봉투)")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=sc_digital&opt_group=SC');" ><?=_("스코딕스")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=scb_digital&opt_group=SCB');" ><?=_("스코딕스 (박)")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=bind_digital&opt_group=BIND');" ><?=_("제본(책자)")?></button>
<!--<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=bindtype_digital&opt_group=BINDTYPE');" ><?=_("제본 방향(책자)")?></button>-->
<button type="button" class="btn btn-xs btn-success" onclick="popup('option_items_price.php?opt_mode=instant_digital&opt_group=INSTANT');" ><?=_("즉석명함")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=cutting_digital&opt_group=CUTTING');" ><?=_("재단")?></button>
                    </div>
                    
                    <label class="col-md-2 control-label"><?=_("옵셋 인쇄비 설정")?></label>
                    <div class="col-md-10">
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=print_opset&opt_group=PRINT');" ><?=_("옵셋 인쇄비")?></button>
                    </div>
                    <label class="col-md-2 control-label"><?=_("옵셋 후가공 설정")?></label>
                    <div class="col-md-10">
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=gloss_opset&opt_group=GLOSS&opt_goods_kind=opset');" ><?=_("코팅")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=punch_opset&opt_group=PUNCH&opt_goods_kind=opset');" ><?=_("타공")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=oshi_opset&opt_group=OSHI&opt_goods_kind=opset');" ><?=_("오시")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_opset.php?opt_mode=missing_opset&opt_group=MISSING&opt_goods_kind=opset');" ><?=_("미싱")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_opset.php?opt_mode=domoo_opset&opt_group=DOMOO&opt_goods_kind=opset');" ><?=_("도무송 작업비")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_opset.php?opt_mode=press_opset&opt_group=PRESS&opt_goods_kind=opset');" ><?=_("형압 작업비")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_opset.php?opt_mode=foil_opset&opt_group=FOIL&opt_goods_kind=opset');" ><?=_("박 작업비")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_opset.php?opt_mode=uvc_opset&opt_group=UVC&opt_goods_kind=opset');" ><?=_("부분UV 작업비")?></button>

<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=barcode_opset&opt_group=BARCODE&opt_goods_kind=opset');" ><?=_("바코드")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=number_opset&opt_group=NUMBER&opt_goods_kind=opset');" ><?=_("넘버링")?></button>
<!--<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=bind_opset&opt_group=BIND&opt_goods_kind=opset');" ><?=_("제본(책자)")?></button>-->

<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_opset.php?opt_mode=bind_BD1_opset&opt_group=BIND&opt_goods_kind=opset');" ><?=_("무선제본")?> </button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_opset.php?opt_mode=bind_BD3_opset&opt_group=BIND&opt_goods_kind=opset');" ><?=_("중철제본")?> </button>

<!--<button type="button" class="btn btn-xs btn-success disabled" onclick="windowopenPopup('option_items_price.php?opt_mode=bindtype_opset&opt_group=BINDTYPE');" ><?=_("제본 방향(책자)")?></button>-->
<!--button type="button" class="btn btn-xs btn-success disabled" onclick="popup('option_items_price.php?opt_mode=instant_opset&opt_group=INSTANT');" ><?=_("즉석명함")?></button-->
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=cutting_opset&opt_group=CUTTING&opt_goods_kind=opset');" ><?=_("재단")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=holding_opset&opt_group=HOLDING&opt_goods_kind=opset');" ><?=_("접지")?></button>

                    </div>
                    
                    <label class="col-md-2 control-label"><?=_("일반 인쇄 설정")?></label>
                    <div class="col-md-10">
<!--<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_normal_price.php?opt_mode=normal_namecard&opt_group=NM_NAMECARD');" ><?=_("명함")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_normal_price.php?opt_mode=normal_sticker&opt_group=NM_STICKER');" ><?=_("스티커")?></button>-->

<?
if ($data) {
    foreach ($data as $k=>$v){ 
?>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_normal_price.php?opt_mode=normal_<?=$v[goodsno]?>&opt_group=<?=$v[extra_option]?>&goodsno=<?=$v[goodsno]?>');" ><?=$v[goodsnm]?>(<?=$v[goodsno]?>)</button>
<?
    }
}
else {
?>
<p><span>[<?=_("확인")?>]</span> <?=_("디지털 일반 상품으로 등록 및 항목 설정 후 가격 설정이 가능합니다.")?></p>
<?
}
?>

                    </div>

                    <label class="col-md-2 control-label"><?=_("기타 설정")?></label>
                    <div class="col-md-10">
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_ctp_opset.php?opt_mode=ctp_opset&opt_group=CTP');"><?=_("CTP(판비)")?></button>
                    </div>



                    <label class="col-md-2 control-label"><?=_("현수막, 실사출력 지류 설정")?></label>
                    <div class="col-md-10">
                    <?if ($extra_use[extra_print_pr_price_type] == "MM") {?>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_paper_pr_price_1mm.php?opt_mode=paper_pr_price_1mm');" ><?=_("가격")?>(<?=$r_extra_print_pr_price_type['MM']?>)</button>
                    <?} else {?>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_paper_pr_price.php?opt_mode=paper_pr');" ><?=_("가격")?>(<?=$r_extra_print_pr_price_type['SIZE']?>)</button>
                    <?}?>
<button type="button" class="btn btn-xs btn-inverse" onclick="windowopenPopup('option_paper_pr_dc.php?opt_mode=paper_pr_dc');" ><?=_("할인율")?></button>                    
                    </div>
                    

                    <?if ($extra_use[extra_print_pr_price_type] != "MM") {?>
                    <label class="col-md-2 control-label"><?=_("현수막,실사출력 인쇄비 설정")?></label>
                    <div class="col-md-10">
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=print_pr_PR01&opt_group=PRINT');" ><?=_("현수막 인쇄비")?></button>
<button type="button" class="btn btn-xs btn-inverse" onclick="windowopenPopup('option_print_pr_addprice.php?opt_mode=print_pr_addprice_PR01&opt_group=PRINT');" ><?=_("현수막 추가 인쇄비 ")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=print_pr_PR02&opt_group=PRINT');" ><?=_("실사출력 인쇄비")?></button>
<button type="button" class="btn btn-xs btn-inverse" onclick="windowopenPopup('option_print_pr_addprice.php?opt_mode=print_pr_addprice_PR02&opt_group=PRINT');" ><?=_("실사출력 추가 인쇄비 ")?></button>
                    </div>
                    <?}?>

                    
                    <label class="col-md-2 control-label"><?=_("현수막,실사출력 후가공 설정")?></label>
                    <div class="col-md-10">
                    <?if ($extra_use[extra_print_pr_price_type] != "MM") {?>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=coating_pr&opt_group=COATING');" ><?=_("코팅")?></button>
                    <?}?>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=processing_pr&opt_group=PROCESSING');" ><?=_("가공&마감")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=design_pr&opt_group=DESIGN');" ><?=_("디자인")?></button>
<button type="button" class="btn btn-xs btn-success" onclick="windowopenPopup('option_items_price.php?opt_mode=cut_pr&opt_group=CUT');" ><?=_("재단")?></button>
                    </div>
               </div>
               </form>
            </div>
            
            <div style="border:2px solid #DEDEDE;padding:10px;">
               <p><span>[<?=_("확인")?>]</span> <?=_("안내말...")?></p>
               <p><span>[<?=_("확인")?>]</span> <?=_("안내말...")?></p>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
function windowopenPopup(url){
    window.open(url);
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>