<?
    include_once '../_header.php';
    include "../_left_menu.php";
    include_once '../../print/lib_print.php';
		include_once 'lib_util_print_admin.php';

    //옵션 항목.
    $optionSizeData = adminGetOptionAllItems("SIZE");
    $optionPaperData = adminGetOptionAllItems("PAPER");
    
    //이미지 정보.
    $optionImgData = array();
    $m_print = new M_print();
    $data = $m_print -> getOptionInfoList($cid);
    
    foreach ($data as $key => $value) {
        $optionImgData[$value[opt_key]] = $value;
    }
?>

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("항목 이미지 설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("이미지 설정")?></h1>

    <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb_items.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
    <input type="hidden" name="mode" value="option_items_img"/>

    <div class="panel panel-inverse"> 
        <div class="panel-heading">
            <h4 class="panel-title"><?=_("규격 항목 이미지 설정")?></h4>
        </div>
    
        <div class="panel-body panel-form">
    
            <!-- begin #content -->

            <div class="panel-body panel-form">                
                <div class="panel-body">
                    <div class="table-responsive">
            
                        <!-- begin #content -->
                        
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th style="width: 300px;"><?=_("옵션명")?></th>
                                <th><?=_("옵션이미지 (옵션 이미지(jpg,png,gif) 파일은 100개 이하만 업로드 됩니다.)")?></th>
                            </tr>
                            <? foreach ($optionSizeData as $itemKey => $itemValue){ ?>
                            <tr align="center">
                                <td>
                                    <?=$itemValue?>
                                    <input type="hidden" class="form-control" name="opt_key[]" value="<?=$itemKey?>"/>
                                    <input type="hidden" class="form-control" name="opt_name[]" value="<?=$itemValue?>"/>
                                </td>
                                <td align="left">
                                    <input type="file" class="form-control" name="img[]"/>
                                    <? if ($optionImgData[$itemKey][opt_img]){ ?>
                                        <img src="../img/bt_preview.png" align="absmiddle" onclick="vLayer(this.nextSibling)" class="hand absmiddle"/><div style="display:none;"><img src="/data/print/goods_items_img/<?=$cid?>/<?=$optionImgData[$itemKey][opt_img]?>"></div>
                                        <input type="checkbox" name="delimg[]" value="<?=$optionImgData[$itemKey][ID]?>"><span><?=_("삭제")?></span>
                                    <? } ?>
                                </td>
                            </tr>
                            <? } ?>
                        </table>
                          
                        <!-- end #content -->
                        
                    </div>
                </div>
            </div>                

            <div class="form-group">
                <label class="col-md-9 control-label"></label>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-md btn-primary m-r-15" onclick="return confirm('<?=_("선택한 이미지 그대로 저장되며, 삭제된 이미지는 추후 복원이 되지 않습니다. 저장하시겠습니까?")?>');"><?=_("저장")?></button>
                    <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                </div>
            </div>

            <!-- end #content -->
        </div>
    </div>

    <div class="panel panel-inverse"> 
        <div class="panel-heading">
            <h4 class="panel-title"><?=_("용지 항목 이미지 설정")?></h4>
        </div>
    
        <div class="panel-body panel-form">
    
            <!-- begin #content -->

            <div class="panel-body panel-form">                
                <div class="panel-body">
                    <div class="table-responsive">
            
                        <!-- begin #content -->
                        
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th style="width: 300px;"><?=_("옵션명")?></th>
                                <th><?=_("옵션이미지 (옵션 이미지(jpg,png,gif) 파일은 100개 이하만 업로드 됩니다.)")?></th>
                            </tr>
                            <? foreach ($optionPaperData as $itemKey => $itemValue){ ?>
                            <tr align="center">
                                <td>
                                    <?=$itemValue?>
                                    <input type="hidden" class="form-control" name="opt_key[]" value="<?=$itemKey?>"/>
                                    <input type="hidden" class="form-control" name="opt_name[]" value="<?=$itemValue?>"/>
                                </td>
                                <td align="left">
                                    <input type="file" class="form-control" name="img[]"/>
                                    <? if ($optionImgData[$itemKey][opt_img]){ ?>
                                        <img src="../img/bt_preview.png" align="absmiddle" onclick="vLayer(this.nextSibling)" class="hand absmiddle"/><div style="display:none;"><img src="/data/print/goods_items_img/<?=$cid?>/<?=$optionImgData[$itemKey][opt_img]?>"></div>
                                        <input type="checkbox" name="delimg[]" value="<?=$optionImgData[$itemKey][ID]?>"><span><?=_("삭제")?></span>
                                    <? } ?>
                                </td>
                            </tr>
                            <? } ?>
                        </table>
                          
                        <!-- end #content -->
                        
                    </div>
                </div>
            </div>                
            
            <!-- end #content -->
        </div>
    </div>
   
   <div class="row">
      <div class="col-md-12">
         <p class="pull-right">
            <button type="submit" class="btn btn-md btn-primary m-r-15" onclick="return confirm('<?=_("선택한 이미지 그대로 저장되며, 삭제된 이미지는 추후 복원이 되지 않습니다. 저장하시겠습니까?")?>');"><?=_("저장")?></button>
            <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
         </p>
      </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>