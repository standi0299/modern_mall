<?
    include_once '../_header.php';
    include "../_left_menu.php";
    include_once '../../print/lib_print.php';

    //도움말 정보.
    $m_print = new M_print();
    $optionData = $m_print -> getOptionInfoList($cid, "and opt_tip != ''");
?>

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("항목 설명팁 설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("설명팁 설정")?></h1>

   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb_items.php">
    <input type="hidden" name="mode" value="option_items_tip"/>

    <div class="panel panel-inverse"> 
        <div class="panel-heading">
            <h4 class="panel-title"><?=_("항목 설명팁 설정")?></h4>
        </div>
    
        <div class="panel-body panel-form">
    
            <!-- begin #content -->

            <div class="panel-body panel-form">                
                <div class="panel-body">
                    <div class="table-responsive">
            
                        <!-- begin #content -->
                        
                        <table class="table table-striped table-bordered" id="opt_table">
                            <tr>
                                <th style="width: 300px;"><?=_("항목명")?></th>
                                <th><?=_("항목 설명팁")?></th>
                                <th style="width: 100px;"></th>
                            </tr>
                            <? foreach ($optionData as $itemKey => $itemValue){ ?>
                            <tr>
                                <td>
                                    <input type="input" class="form-control" name="opt_name[]" value="<?=$itemValue[opt_name]?>"/>
                                </td>
                                <td align="left">
                                    <input type="input" class="form-control" name="opt_tip[]" value="<?=$itemValue[opt_tip]?>"/>
                                    <input type="hidden" class="form-control" name="ID[]" value="<?=$itemValue[ID]?>"/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-danger" onclick="DelOpt('<?=$itemValue[ID]?>')"><?=_("삭제")?></button>
                                </td>
                            </tr>
                            <? } ?>
                        </table>
                          
                        <!-- end #content -->
                        
                    </div>
                </div>
            </div>                

            <div class="form-group">
                <div class="col-md-12">
                    <p class="pull-left">
                        <button type="button" class="btn btn-md btn-success" onclick="add_opt()"><?=_("추가")?></button>                        
                    </p>
                </div>
            </div>

            <!-- end #content -->
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

<script>
function add_opt(){
   var obj = $j("#opt_table");
   //obj.css("display","block");
   var tr = document.createElement("tr");
   $j(tr).appendTo(obj);
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"text\" class=\"form-control\" name=\"opt_name[]\"/>";
   $j(td).appendTo($j(tr));
   
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"text\" class=\"form-control\" name=\"opt_tip[]\"/><input type=\"hidden\" class=\"form-control\" name=\"ID[]\"/>";
   $j(td).appendTo($j(tr));

   var td = document.createElement("td");
   td.innerHTML = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"del_opt(this);\">삭제</button>";
   $j(td).appendTo($j(tr));
   
   _pt_set();
}

function del_opt(obj) {
   $j(obj).parent().parent().remove();
}

function DelOpt(id) {
    if (confirm('선택한 항목을 삭제하시겠습니까? 삭제된 항목은 추후 복원이 되지 않습니다.')) {
        location.href='indb_items.php?mode=option_items_tip_del&id=' + id;
    }
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>