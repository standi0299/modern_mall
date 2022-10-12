<?
include "../_header.php";
include "../_left_menu.php";
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
         <?=_("카카오 알림톡")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("카카오 알림톡")?> <small><?=_("카카오 알림톡 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("카카오 알림톡 목록")?><h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="form1" method="post" action="indb.php">
                  <input type="hidden" name="mode">
                  <div class="panel-body">
                    <div class="table-responsive">
                       <table id="data-table" class="table table-striped table-bordered">
                          <thead>
                             <tr>
                                 <th><?=_("번호")?></th>
                                 <th><?=_("전송일시")?></th>
                                 <th><?=_("받는사람")?></th>
                                 <th><?=_("제목")?></th>
                                 <th><?=_("전송결과")?></th>
                                 <!-- <th><?=_("전송건수")?></th> -->
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
<!-- end #content -->

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
			"sPaginationType" : "bootstrap",
			"aaSorting" : [[0, "desc"]],
			"bFilter" : false,
			"oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
			},
			"aoColumns": [
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
		 "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
			"ajax": $.fn.dataTable.pipeline({
            url: './log_kakao_page.php',
            pages: 10 // number of pages to cache
         })
      });
   });
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>