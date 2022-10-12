<?

include "../_header.php";
include "../_left_menu.php";

/*$m_goods = new M_goods();

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

if (is_array($_POST[catno])) {
	list($_POST[catno]) = array_slice(array_notnull($_POST[catno]), -1);
}

$postData = base64_encode(json_encode($_POST));*/

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
         <?=_("이벤트리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("이벤트리스트")?> <small><?=_("이벤트 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
		   <div class="panel panel-inverse"> 
		      <div class="panel-heading">
		         <h4 class="panel-title"><?=_("이벤트리스트")?></h4>
		      </div>
		      
		      <div class="panel-body panel-form">
		         <form class="form-horizontal form-bordered" method="post" action="event_list.php">
			         <!--<div class="form-group">
			         	<label class="col-md-2 control-label"><?=_("카테고리")?></label>
			         	<div class="col-md-8 form-inline">
			         		<select name="catno[]" class="form-control">
			               	   <option value="">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $_POST[catno])?>
			                </select>
			         	</div>
			         	<div class="col-md-2">
			         		<button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
			         	</div>
			         </div>-->
			         
			         <div class="panel-body">
			         	<div class="table-responsive">
			         		<table id="data-table" class="table table-striped table-bordered">
			         			<thead>
			         				<tr>
			         					<th><?=_("번호")?></th>
			         					<th><?=_("이벤트명")?></th>
			         					<th><?=_("이벤트 시작일자")?></th>
			         					<th><?=_("이벤트 만료일자")?></th>
			         					<th><?=_("미리보기")?></th>
			         					<th><?=_("수정")?></th>
			         					<th><?=_("삭제")?></th>
			         				</tr>
			         			</thead>
			         		</table>
			         		
			         		<div class="form-group">
				               <div class="col-md-12">
				                  <a href="event_write.php"><span class="btn btn-sm btn-success"><?=_("이벤트추가")?></span></a>
				               </div>
				            </div>
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

<script type="text/javascript">
/* Table initialisation */
$(document).ready(function() {
	$('#data-table').dataTable({
		"sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
		"sPaginationType" : "bootstrap",
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
		{ "bSortable": false },
		{ "bSortable": false },
		],
		"processing": false,
		"serverSide": true,
		"bAutoWidth": false,
		"ajax": $.fn.dataTable.pipeline({
			url: 'event_list_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
		})
	});
});
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>