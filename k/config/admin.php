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
         <h4 class="panel-title"><?=_("관리자리스트")?></h4>
      </div>
      
      <div class="panel-body">
         <div class="table-responsive">
            <table id="data-table" class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th><?=_("번호")?></th>
                     <th><?=_("아이디")?></th>
                     <th><?=_("이름")?></th>
                     <th><?=_("등록날짜")?></th>
                     <th><?=_("수정")?></th>
                     <th><?=_("메뉴설정")?></th>
                     <th><?=_("삭제")?></th>
                  </tr>
               </thead>
            </table>
               
            <div class="form-group">
               <div class="col-md-12">
                  <a href="admin_write.php?mode=write"><button type="button" class="btn btn-sm btn-success"><?=_("관리자추가")?></button></a>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <div class="panel-body panel-form">
      <div class="form-group">
      	 <div class="col-md-12">
      	 	<div><span class="warning">[주의]</span> 삭제된 관리자는 복구되지 않으니, 주의하여 주시기 바랍니다. 재등록으로만 가능합니다.</div>
      	 	<div><span class="notice">[설명]</span> 관리자의 메뉴별 권한 설정시 권한설정 기능에서 적용하실 수 있습니다.</div>
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
		"bFilter" : true,
		"oLanguage" : {
			"sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
		},
		"aoColumns": [
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
			url: 'admin_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
		})
	});
});
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>