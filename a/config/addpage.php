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
         <h4 class="panel-title"><?=_("추가페이지관리")?></h4>
      </div>
      
      <div class="panel-body">
         <div class="table-responsive">
            <table id="data-table" class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th><?=_("타입")?></th>
                     <th><?=_("설명")?></th>
                     <th><?=_("URL주소")?></th>
                     <th><?=_("URL복사")?></th>
                     <th><?=_("수정")?></th>
                     <th><?=_("삭제")?></th>
                  </tr>
               </thead>
            </table>
               
            <div class="form-group">
               <div class="col-md-12">
                  <a href="addpage_write.php"><button type="button" class="btn btn-sm btn-success"><?=_("페이지추가")?></button></a>
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
		],
		"processing": false,
		"serverSide": true,
		"bAutoWidth": false,
		"ajax": $.fn.dataTable.pipeline({
			url: 'addpage_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
		})
	});
});
</script>

<script>
function source(code){
    var IE = (document.all) ? true : false;
    //var txt = "/main/index.php?type="+ code;
    var txt = "/main/page.php?type="+ code;

    if (IE) {
        window.clipboardData.setData('Text', txt);
        alert('<?=_("URL이 복사되었습니다.")?>');
    } else {
        temp = prompt('<?=_("URL 링크입니다.. Ctrl+C를 눌러 복사하세요")?>', txt );
    }
}
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>