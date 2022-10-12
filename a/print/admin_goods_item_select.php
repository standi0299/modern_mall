<?
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
         <?=_("낱장 항목 설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("낱장 설정")?></h1>

   <form class="form-horizontal form-bordered" name="fm" method="POST" action="admin_indb.php">
   <input type="hidden" name="mode" value="goods_opt_sel" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("낱장 항목 설정")?></h4>
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
            <div class="col-md-3">
               <input type="checkbox" name="opt_directupload_use" value="N" <?if ($printGoodsItem[directupload_use] == "N"){?>checked<?}?>> <?=_("사용 안함")?>
            </div>
            <label class="col-md-2 control-label"><?=_("건수 사용여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" name="opt_cnt_use" value="N" <?if ($printGoodsItem[cnt_use] == "N"){?>checked<?}?>> <?=_("사용 안함")?>
            </div>            
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("규격(사이즈)")?></label>
            <div class="col-md-10 form-inline">
<?
    $optionData = adminGetOptionAllItems("SIZE");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[size], "opt_size");  
?>
            <input type="checkbox" name="opt_size[]" value="USER" <?=$bSizeUser?> >사이즈 직접입력 

            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("용지")?></label>
            <div class="col-md-3">
일반지<BR>
<?
    $optionData = adminGetOptionPaperItems("PNM");
    //debug($printGoodsItem[outside_paper]);
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");
?>
<br><BR>일반색지<BR>
<?
    $optionData = adminGetOptionPaperItems("PNC");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>
            </div>
            <div class="col-md-3 form-inline">
고급지<BR>
<?
    $optionData = adminGetOptionPaperItems("PLU");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>

            </div>
            <div class="col-md-3 form-inline">
펄색지<BR>
<?
    $optionData = adminGetOptionPaperItems("PPR");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>

<br><BR>고급색지<BR>
<?
    $optionData = adminGetOptionPaperItems("PLC");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>


<br><BR>패키지 용지<BR>
    <?
    $optionData = adminGetOptionPaperItems("PPK");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>

<br><BR>스티커<BR>
    <?
    $optionData = adminGetOptionPaperItems("PST");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>

<br><BR>PET지<BR>
    <?
    $optionData = adminGetOptionPaperItems("PPE");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>

<br><BR>메탈릭용지<BR>
    <?
    $optionData = adminGetOptionPaperItems("PML");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>

<br><BR>포토용지<BR>
    <?
    $optionData = adminGetOptionPaperItems("PPT");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>

<br><BR>선방(종이)<BR>
    <?
    $optionData = adminGetOptionPaperItems("PSB");
    //debug($optionData);
    echo adminMakePaperOptionCheckTag($optionData, $printGoodsItem[paper], "opt_paper");    
?>

            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("인쇄")?></label>
            <div class="col-md-10 form-inline">
<?
    $optionData = adminGetOptionAllItems("PRINT");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem['print'], "opt_print"); 
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
            <label class="col-md-2 control-label"><?=_("스코딕스")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("SC");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[sc], "opt_sc");    
?>
            </div>
            <label class="col-md-2 control-label"><?=_("스코딕스 박")?></label>
            <div class="col-md-4">
<?
    $optionData = adminGetOptionAllItems("SCB");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[scb], "opt_scb");   
?>
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("코팅")?></label>
            <div class="col-md-3 form-inline">
<?
    $optionData = adminGetOptionAllItems("GLOSS");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[gloss], "opt_gloss"); 
?>
            </div>
            <label class="col-md-2 control-label"><?=_("타공")?></label>
            <div class="col-md-4 form-inline">
<?
    $optionData = adminGetOptionAllItems("PUNCH");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[punch], "opt_punch"); 
?>
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("오시")?></label>
            <div class="col-md-3 form-inline">
<?
    $optionData = adminGetOptionAllItems("OSHI");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[oshi], "opt_oshi");  
?>
            </div>
            <label class="col-md-2 control-label"><?=_("")?></label>
            <div class="col-md-4 form-inline">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("제본")?></label>
            <div class="col-md-3 form-inline">
<?
    $optionData = adminGetOptionAllItems("BIND");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[bind], "opt_bind");  
?>
            </div>
            <label class="col-md-2 control-label"><?=_("제본방향")?></label>
            <div class="col-md-4 form-inline">
<?
    $optionData = adminGetOptionAllItems("BINDTYPE");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[bind_type], "opt_bind_type");  
?>
            </div>            
         </div>
                  
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("미싱")?></label>
            <div class="col-md-3 form-inline">
<?
    $optionData = adminGetOptionAllItems("MISSING");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[missing], "opt_missing");  
?>
            </div>
            <label class="col-md-2 control-label"><?=_("귀도리")?></label>
            <div class="col-md-4">
<?
    $optionData = adminGetOptionAllItems("ROUND");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[round], "opt_round"); 
?>
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("도무송")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("DOMOO");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[domoo], "opt_domoo"); 
?>
            </div>            
            <label class="col-md-2 control-label"><?=_("바코드")?></label>
            <div class="col-md-4">
<?
    $optionData = adminGetOptionAllItems("BARCODE");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[barcode], "opt_barcode"); 
?>
            </div>            
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("넘버링")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("NUMBER");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[number], "opt_number"); 
?>
            </div>            
            <label class="col-md-2 control-label"><?=_("스탠드")?></label>
            <div class="col-md-4">
<?
    $optionData = adminGetOptionAllItems("STAND");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[stand], "opt_stand"); 
?>
            </div>            
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("댕글")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("DANGLE");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[dangle], "opt_dangle"); 
?>
            </div>            
            <label class="col-md-2 control-label"><?=_("양면테잎")?></label>
            <div class="col-md-4">
<?
    $optionData = adminGetOptionAllItems("TAPE");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[tape], "opt_tape"); 
?>
            </div>            
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("주소인쇄")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("ADDRESS");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[address], "opt_address"); 
?>
            </div>            
            <label class="col-md-2 control-label"><?=_("날개")?></label>
            <div class="col-md-4">
<?
    $optionData = adminGetOptionAllItems("WING");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[wing], "opt_wing"); 
?>
            </div>            
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("즉석명함")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("INSTANT");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[instant], "opt_instant"); 
?>
            </div>            
            <label class="col-md-2 control-label"><?=_("재단")?></label>
            <div class="col-md-4">
<?
    $optionData = adminGetOptionAllItems("CUTTING");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[cutting], "opt_cutting"); 
?>
            </div>            
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("박")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("FOIL");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[foil], "opt_foil"); 
?>
            </div>            
            <label class="col-md-2 control-label"><?=_("접지")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("HOLDING");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[holding], "opt_holding"); 
?>
            </div>            
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("형압")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("PRESS");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[press], "opt_press"); 
?>
            </div>            
            <label class="col-md-2 control-label"><?=_("부분UV")?></label>
            <div class="col-md-3">
<?
    $optionData = adminGetOptionAllItems("UVC");
    echo adminMakeOptionCheckTag($optionData, $printGoodsItem[uvc], "opt_uvc"); 
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