<?
/*
* @date : 20190320
* @author : kdk
* @brief : 견적 상품 추가 현수막(Placard),실사출력(RealPrint)
* @desc : 현수막 (PR01),실사출력(PR02)
*/

include_once '../_header.php';
include "../_left_menu.php";
include_once '../../print/lib_print.php';
include_once 'lib_util_print_admin.php';

if (file_exists("../../data/print/goods_items/$_GET[goodsno].php"))
{
    include_once "../../data/print/goods_items/$_GET[goodsno].php";
    //debug(json_decode($goods_item, 1));
    $printGoodsItem = json_decode($goods_item, 1);
    //debug($printGoodsItem);
    
    foreach ($printGoodsItem[size] as $key => $value) {
        if ($value == "USER") $bSizeUser = "checked";
    }
}
?>

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("현수막/실사출력 항목 설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("현수막/실사출력 설정")?></h1>

   <form class="form-horizontal form-bordered" name="fm" method="POST" action="admin_indb.php">
   <input type="hidden" name="mode" value="goods_opt_sel" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("현수막/실사출력 항목 설정")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("상품코드")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" readonly="readonly" name="goods_no" value="<?=$_GET[goodsno]?>">
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("다이렉트 파일업로드 사용여부")?></label>
            <div class="col-md-2">
               <input type="checkbox" name="opt_directupload_use" value="N" <?if ($printGoodsItem[directupload_use] == "N"){?>checked<?}?>> <?=_("사용 안함")?>
            </div>
            <label class="col-md-2 control-label"><?=_("건수 사용여부")?></label>
            <div class="col-md-2">
               <input type="checkbox" name="opt_cnt_use" value="N" <?if ($printGoodsItem[cnt_use] == "N"){?>checked<?}?>> <?=_("사용 안함")?>
            </div>
            <label class="col-md-2 control-label"><?=_("부가세포함 여부")?></label>
            <div class="col-md-2">
               <input type="checkbox" name="opt_vat_use" value="N" <?if ($printGoodsItem[vat_use] == "N"){?>checked<?}?>> <?=_("포함 안함")?>
            </div>            
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("규격(사이즈)")?></label>
            <div class="col-md-10 form-inline">
<?
    $optionData = adminGetOptionAllItems("SIZE", "SPR");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[size], "opt_size");  
?>
            <input type="checkbox" name="opt_size[]" value="USER" <?=$bSizeUser?> >사이즈 직접입력 

            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("용지")?></label>
            <div class="col-md-10 form-inline">
현수막 실사출력 인쇄지<BR>
<?
    $optionData = adminGetOptionPaperItems("EPR");
    //debug($printGoodsItem[outside_paper]);
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper", false);
?>
            </div>
            
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("인쇄")?></label>
            <div class="col-md-10 form-inline">
<?
    $optionData = adminGetOptionAllItems("PRINT","OC");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem['print'], "opt_print", false); 
?>
            </div>
         </div>
         
      </div>
   </div>

   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("후가공 항목 설정")?></h4>
      </div>

      <div class="panel-body panel-form">

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("코팅")?></label>
            <div class="col-md-3 form-inline">
<?
    $optionData = adminGetOptionAllItems("COATING");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[coating], "opt_coating"); 
?>
            </div>
            <label class="col-md-2 control-label"><?=_("가공&마감")?></label>
            <div class="col-md-4 form-inline">
<?
    $optionData = adminGetOptionAllItems("PROCESSING");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[processing], "opt_processing"); 
?>
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("디자인")?></label>
            <div class="col-md-3 form-inline">
<?
    $optionData = adminGetOptionAllItems("DESIGN");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[design], "opt_design");  
?>
            </div>
            <label class="col-md-2 control-label"><?=_("재단")?></label>
            <div class="col-md-4 form-inline">
<?
    $optionData = adminGetOptionAllItems("CUT");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[cut], "opt_cut");  
?>
            </div>
         </div>
         
      </div>
   
   </div>
   
   <div class="row">
      <div class="col-md-12">
         <p class="pull-right">
            <button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("저장")?></button>
            <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
         </p>
      </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>