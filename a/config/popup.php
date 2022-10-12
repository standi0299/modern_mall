<?

include "../_header.php";
include "../_left_menu.php";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("팝업관리")?></h4>
      </div>
      
      <div class="panel-body">
         <div class="table-responsive">
            <table id="data-table" class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th><?=_("번호")?></th>
                     <th><?=_("타이틀")?></th>
                     <th><?=_("사용여부")?></th>
                     <th><?=_("적용할 스킨")?></th>
                     <th><?=_("시작일")?></th>
                     <th><?=_("종료일")?></th>
                     <th><?=_("수정")?></th>
                     <th><?=_("삭제")?></th>
                  </tr>
               </thead>
            </table>
               
            <div class="form-group">
               <div class="col-md-12">
                  <a href="popup_write.php"><button type="button" class="btn btn-sm btn-success"><?=_("팝업추가")?></button></a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<script type="text/javascript">
/* Table initialisation */
$(document).ready(function() {
	$('#data-table').dataTable({
		"sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
		"sPaginationType" : "bootstrap",
		"aaSorting" : [[0, "desc"]],
		"bFilter" : true,
		"oLanguage" : {
			"sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
		},
		"aoColumns": [
		{ "bSortable": true },
		{ "bSortable": false },
		{ "bSortable": false },
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
			url: 'popup_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
		})
	});
});
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>