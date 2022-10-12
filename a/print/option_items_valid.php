<?
    include_once '../_header.php';
    include "../_left_menu.php";
    include_once '../../print/lib_print.php';

    //제약사항 정보.
    $m_print = new M_print();
    $optionData = $m_print -> getOptionInfo($cid, "opt_limit", "gram_page");
?>

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("항목 제약사항 설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("제약사항 설정")?></h1>

   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb_items.php">
    <input type="hidden" name="mode" value="option_items_valid"/>
    <input type="hidden" class="form-control" name="opt_key" value="opt_limit"/>
    <input type="hidden" class="form-control" name="opt_name" value="gram_page"/>
    <div class="panel panel-inverse"> 
        <div class="panel-heading">
            <h4 class="panel-title"><?=_("중철제본 평량별 제약사항 설정")?></h4>
        </div>
    
        <div class="panel-body panel-form">
    
            <!-- begin #content -->

            <div class="panel-body panel-form">                
                <div class="panel-body">
                    <div class="table-responsive">
            
                        <!-- begin #content -->
                        
                        <table class="table table-striped table-bordered" id="opt_table">
                            <tr>
                                <th style="width: 30px;"><?=_("평량 gram")?></th>
                                <th style="width: 30px;"><?=_("페이지 page")?></th>
                                <th style="width: 100px;"></th>
                            </tr>
                            <? 
                            if ($optionData) {
                                //debug($optionData);
                                $opt_data = json_decode($optionData[opt_desc], 1);
                                //debug($opt_data);
                                foreach ($opt_data as $itemKey => $itemValue) { 
                            ?>
                            <tr>
                                <td>
                                    <input type="input" class="form-control" style="width: 50px;" name="gram[]" value="<?=$itemValue[gram]?>"/>
                                </td>
                                <td align="left">
                                    <input type="input" class="form-control" style="width: 50px;" name="page[]" value="<?=$itemValue[page]?>"/>                                   
                                </td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-danger" onclick="del_opt(this)"><?=_("삭제")?></button>
                                </td>
                            </tr>
                            <? 
                                }
                            } 
                            ?>
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
   td.innerHTML = "<input type=\"text\" class=\"form-control\" style=\"width: 50px;\" name=\"gram[]\"/>";
   $j(td).appendTo($j(tr));
   
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"text\" class=\"form-control\" style=\"width: 50px;\" name=\"page[]\"/>";
   $j(td).appendTo($j(tr));

   var td = document.createElement("td");
   td.innerHTML = "<button type=\"button\" class=\"btn btn-xs btn-danger\" onclick=\"del_opt(this);\">삭제</button>";
   $j(td).appendTo($j(tr));
   
   _pt_set();
}

function del_opt(obj) {
   $j(obj).parent().parent().remove();
}
/*
function DelOpt(id) {
    if (confirm('선택한 항목을 삭제하시겠습니까? 삭제된 항목은 추후 복원이 되지 않습니다.')) {
        location.href='indb_items.php?mode=option_items_valid_del&id=' + id;
    }
}*/
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>