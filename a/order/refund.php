<?
include "../_header.php";
include "../_left_menu.php";

### 배송업체정보추출
$r_shipcomp = get_shipcomp();

if($_GET[state] == 0) $title = _("환불접수리스트");
else $title = _("환불완료리스트");

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_($title)?>
      </li>
   </ol>

   <h1 class="page-header"><?=_($title)?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_($title)?></h4>
            </div>

            <div class="panel-body panel-form">
               <form name="fm" class="form-horizontal form-bordered" method="post" action="indb.php">
                  <input type="hidden" name="mode" value="refund_complete"/>

                  <div class="form-group">
                     <div class="col-md-12 form-inline">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("선택")?></th>
                                 <th><?=_("번호")?></th>
                                 <th><?=_("결제번호")?></th>
                                 <th><?=_("주문자")?></th>
                                 <th><?=_("환불액")?></th>
                                 <th><?=_("현금")?></th>
                                 <th><?=_("PG취소")?></th>
                                 <th><?=_("적립금")?></th>
                                 <th><?=_("고객부담")?></th>
                                 <th><?=_("접수/완료일")?></th>
                                 <th><?=_("수정")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
                  
                  <? if($_GET[state] == "0") { ?>
                     <div class="col-md-12 col-sm-6">
                        <button type="button" class="btn btn-sm btn-danger" onclick="chk_data();"><?=_("환불 완료")?></button>
                     </div>
                  <? } ?>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         "aaSorting" : [[9, "desc"]],
         "bFilter" : true,
         "aLengthMenu": [10, 25, 50, 100],
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         ],
         "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
         "ajax": $.fn.dataTable.pipeline({
            url: './refund_page.php?state=<?=$_GET[state]?>',
            pages: 5 // number of pages to cache
         })
      });
   });
   
   function chk_data(){
      var c = document.getElementsByName('chk[]');
      var myArray = new Array();
      var k = 0;
      for (var i = 0; i < c.length; i++) {
         if (c[i].checked){
            myArray[k] = c[i].value;
            k++;
         }
      }
      
      var chk = myArray.join(",");
      if(chk){
         if(confirm('<?=_("환불완료처리하시겠습니까?")?>' + "\r\n" + '<?=_("환불완료처리된 주문은 복원이 불가능합니다.")?>' + "\r\n" + '<?=_("적립금은 완료와 동시에 자동지급 됩니다.")?>') == true){
            document.fm.submit();
         }
      }
      else {
         alert('<?=_("환불 완료 처리 할 주문을 선택해 주세요.")?>');
      }
   }
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>