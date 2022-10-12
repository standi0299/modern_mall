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
         <?=_("그룹관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("그룹관리")?> <small><?=_("가입된 회원들의 그룹관리.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("그룹 목록")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="form1" method="post" action="indb.php">
                  <input type="hidden" name="mode">
                  <div class="panel-body">
                    <div class="table-responsive">
                       <table id="data-table" class="table table-striped table-bordered">
                          <thead>
                             <tr>
                                 <th><?=_("그룹번호")?></th>
                                 <th><?=_("그룹명")?></th>
                                 <th><?=_("할인율")?></th>
                                 <th><?=_("적립률")?></th>
                                 <th><?=_("레벨")?></th>
                                 <th><?=_("회원수")?></th>
                                 <th><?=_("수정")?></th>
                                 <th><?=_("삭제")?></th>
                             </tr>
                          </thead>
                       </table>
                    </div>

                     <div class="form-group">
                        <button type="button" class="btn btn-sm btn-success" onclick="group_popup();">
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
//추가, 수정 팝업
function group_popup(grpno) {
	if(grpno)
    	popup('group_popup.php?mode=modGrp&grpno='+grpno,700,550);
   	else
    	popup('group_popup.php',700,550);
}

//삭제
function group_delete(grpno) {
	//alert('<?=_("준비중입니다.")?>');
   if(confirm('<?=_("정말 삭제하시겠습니까?")?>'))
      location.href = "indb.php?mode=delGrp&grpno="+grpno;
}
</script>
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
			"sPaginationType" : "bootstrap",
			"aaSorting" : [[1, "desc"]],
			"bFilter" : false,
			"oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
			},
			"aoColumns": [
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
		 "processing": true,
         "serverSide": true,
         "bAutoWidth": false,
			"ajax": $.fn.dataTable.pipeline({
            url: './group_list_page.php',
            pages: 5 // number of pages to cache
         })
      });
   });
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>