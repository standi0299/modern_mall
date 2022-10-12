<?

include "../_header.php";
include "../_left_menu.php";

$postData = base64_encode(json_encode($_POST));

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("접속로그")?></h4>
      </div>
      
      <div class="panel-body panel-form">
         <form class="form-horizontal form-bordered" name="fm" method="POST">
          	 <div class="form-group">
          	 	<label class="col-md-2 control-label"><?=_("접속날짜")?></label>
          	 	<div class="col-md-3">
          	 		<div class="input-group input-daterange">
          	 			<input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>">
          	 			<span class="input-group-addon"> ~ </span>
          	 			<input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST['end']?>">
          	 		</div>
          	 	</div>
          	 	
          	 	<div class="col-md-5">
          	 		<button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');"><?=_("오늘")?></button>
          	 		<button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');"><?=_("3일")?></button>
          	 		<button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');"><?=_("1주일")?></button>
          	 		<button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');"><?=_("1달")?></button>
          	 		<button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start', 'all'); regdtOnlyOne('today','end');"><?=_("전체")?></button>
          	 	</div>
          	 	
          	 	<div class="col-md-2">
          	 		<button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
          	 	</div>
          	 </div>
          	 
          	 <div class="panel-body">
          	 	<div class="table-responsive">
          	 		<table id="data-table" class="table table-hover table-bordered">
          	 			<thead>
          	 				<tr>
          	 					<th><?=_("번호")?></th>
          	 					<th><?=_("접속시간")?></th>
          	 					<th><?=_("접속아이디")?></th>
          	 					<th><?=_("이름")?></th>
          	 					<th><?=_("접속패스워드")?></th>
          	 					<th><?=_("접속IP")?></th>
          	 				</tr>
          	 			</thead>
          	 		</table>
          	 	</div>
          	 </div>
         </form>
      </div>
   </div>
   
   <div class="panel-body panel-form">
      <div class="form-group">
      	 <div class="col-md-12">
      	 	<div><span class="notice">[설명]</span> 관리자의 접속상황 입니다.</div>
      	 	<div><span class="notice">[설명]</span> 패스워드는 암호화된 내용이라 확인할 수 없으며, 참고정보로 활용하시면 됩니다.</div>
      	 </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>

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
			url: 'log_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
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