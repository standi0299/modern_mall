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
         <?=_("휴면계정복원")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("휴면회원 목록")?> <small><?=_("휴면회원 처리된 회원정보를 보실 수 있습니다.")?>.</small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("휴면회원 목록")?></h4>
            </div>
             
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="post" action="indb.php">
                  <input type="hidden" name="mode" value="restore_account">
                  <input type="hidden" name="mid">

                  <div class="panel-body">
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("아이디")?></th>
                                 <th><?=_("이름")?></th>
                                 <th><?=_("최종 로그인 날짜")?></th>
                                 <th><?=_("휴면 전환 날짜")?></th>
                                 <th><?=_("복원")?></th>
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
<script>
//복원
function restore(mid) {
	alert('<?=_("준비중입니다.")?>');
	//var fm = document.fm;
   	//if(confirm('<?=_("정말 삭제하시겠습니까?")?>')) {
    //  	fm.mid.value = mid;
   	//	fm.submit();
   	//}
}
</script>
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
			"sPaginationType" : "bootstrap",
			"aaSorting" : [[2, "asc"]],
			"bFilter" : true,
			"oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
			},
			"aoColumns": [
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": false },
         ],
		 "processing": true,
         "serverSide": true,
         "bAutoWidth": false,
			"ajax": $.fn.dataTable.pipeline({
            url: './quiescence_account_list_page.php',
            pages: 10 // number of pages to cache
         })
      });
   });
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>