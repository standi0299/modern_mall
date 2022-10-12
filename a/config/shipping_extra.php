<?

include "../_header.php";
include "../_left_menu.php";

//$_GET[rid] = "test1";
if (!$_GET[rid]) msg(_("제작사 ID가 없습니다. 정상적인 경로로 접근해 주세요"), "close");

$postData = base64_encode(json_encode($_GET));
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("추가배송비관리")?></h4>
      </div>
      
      <div class="panel-body">
         <div class="table-responsive">
            <table id="data-table" class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th><?=_("번호")?></th>
                     <th><?=_("우편번호")?></th>
                     <th><?=_("주소")?></th>
                     <th><?=_("추가금액")?></th>
                     <th><?=_("수정")?></th>
                     <th><?=_("삭제")?></th>
                  </tr>
               </thead>
            </table>
               
            <div class="form-group">
               <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-success" onclick="popup('shipping_extra_popup.php?rid=<?=$_GET[rid]?>', 630, 405)"><?=_("추가")?></button>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <div class="panel-body panel-form">
      <div class="form-group">
      	 <div class="col-md-12">
      	 	<div><span class="notice">[<?=_("설명")?>]</span> <?=_("우편번호를 기준으로 처리합니다. 중복 우편번호 등록시 가격과 주소가 변경됩니다.")?></div>      	 	      	 	
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
		],
		"processing": false,
		"serverSide": true,
		"bAutoWidth": false,
		"ajax": $.fn.dataTable.pipeline({
			url: 'shipping_extra_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
		})
	});
});
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>