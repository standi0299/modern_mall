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
         <?=_("모바일푸쉬알림관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("모바일푸쉬알림관리")?> <small><?=_("모바일을 사용하는 소비자에게 푸쉬알림을 보내실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("모바일푸쉬알림 목록")?></h4>
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
                                  <th><?=_("제목")?></th>
                                  <th><?=_("내용")?></th>
                                  <th><?=_("등록일")?></th>
                                  <th><?=_("최종발송일시")?></th>
                                  <th><?=_("발송")?></th>
                                  <th><?=_("오류건")?><br><?=_("재발송")?></th>
                                  <th><?=_("수정")?></th>
                                  <th><?=_("삭제")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>

                     <div class="form-group">
                        <button type="button" class="btn btn-sm btn-success" onclick="mobile_push_popup();">
                           <?=_("추가")?>
                        </button>
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
//추가, 수정
function mobile_push_popup (pushno) {
	if (pushno)
    	popup('mobile_push_popup.php?mode=modPush&pushno=' + pushno, 700, 310);
   	else
    	popup('mobile_push_popup.php', 700, 310);
}

//삭제
function mobile_push_delete (pushno) {
   if (confirm('<?=_("정말 삭제하시겠습니까?")?>'))
   	   location.href = "indb.php?mode=delPush&pushno=" + pushno;
}

//발송
function mobile_push_send (pushno) {
	location.href = "indb.php?mode=sendPush&pushno=" + pushno;
}

//재발송
function mobile_push_resend (pushno) {
	location.href = "indb.php?mode=resendPush&pushno=" + pushno;
}
</script>

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
         { "bSortable": true },
         { "bSortable": false },
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
            url: './mobile_push_list_page.php',
            pages: 5 // number of pages to cache
         })
      });
   });
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>