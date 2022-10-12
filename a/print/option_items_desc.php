<?
    include_once '../_header.php';
    include "../_left_menu.php";
    include_once '../../print/lib_print.php';
		include_once 'lib_util_print_admin.php';

    //옵션 항목.
    $optionData = adminGetOptionItems("and opt_group not in ('SIZE','PRINT','PAPER') group by opt_group order by id");
    
    //도움말 정보.
    $optionDescData = array();
    $m_print = new M_print();
    $data = $m_print -> getOptionInfoList($cid, "and opt_desc != ''");
    
    foreach ($data as $key => $value) {
        $optionDescData[$value[opt_key]] = $value;
    }
?>

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("항목 도움말 설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("도움말 설정")?></h1>

   <form class="form-horizontal form-bordered">
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("항목 도움말 설정")?></h4>
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
                                <th><?=_("도움말")?></th>
                            </tr>
                            <? foreach ($optionData as $itemKey => $itemValue){ ?>
                            <tr>
                                <td><?=$itemValue[opt_desc]?></td>
                                <td>
                                    <?if($optionDescData[$itemValue[opt_group]]) {?>
                                    <button type="button" class="btn btn-xs btn-primary" onclick="popup('option_items_desc_view_popup.php?id=<?=$optionDescData[$itemValue[opt_group]][ID]?>')"><?=_("도움말 보기")?></button>
                                    <button type="button" class="btn btn-xs btn-danger" onclick="location.href='option_items_desc_write.php?mode=update&id=<?=$optionDescData[$itemValue[opt_group]][ID]?>'"><?=_("수정")?></button>
                                    <?} else { ?>
                                    <button type="button" class="btn btn-xs btn-danger" onclick="location.href='option_items_desc_write.php?mode=insert&type=<?=$itemValue[opt_group]?>&type2=<?=urlencode($itemValue[opt_desc])?>'"><?=_("등록")?></button>
                                    <?}?>                                    
                                    <input type="hidden" name="opt_key[]" value="<?=$itemValue[opt_group]?>"/>
                                    <input type="hidden" name="opt_name[]" value="<?=$itemValue[opt_desc]?>"/>
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

   </form>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>