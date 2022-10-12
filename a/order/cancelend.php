<?
include "../_header.php";
include "../_left_menu.php";

$postData = base64_encode(json_encode($_POST));

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
         <?=_("취소리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("취소리스트")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("취소리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" onsubmit="return submitContents(this);">

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("주문일")?></label>
                     <div class="col-md-4">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST[end]?>" />
                        </div>
                     </div>
                     <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-warning"><?=_("검색")?></button>
                     </div>
                  </div>
                  
                  <div class="panel-body">
                     <div class="table-responsive">
                        <table id="data-table" class="table table-hover table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("결제번호")?></th>
                                 <th><?=_("주문일")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
                  
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         "aaSorting" : [[0, "asc"]],
         "bFilter" : true,
         "aLengthMenu": [10, 25, 50, 100],
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
         "ajax": $.fn.dataTable.pipeline({
            url: './cancelend_page.php?postData=<?=$postData?>',
            pages: 5 // number of pages to cache
         })
      });
   });
   
   var handleDatepicker = function() {
      $('.input-daterange').datepicker({
         language : 'kor',
         todayHighlight : true,
         autoclose : true,
         todayBtn : true,
         format : 'yyyy-mm-dd',
      });
   };

   handleDatepicker();
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>