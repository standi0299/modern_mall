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
         <?=_("쿠폰관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("쿠폰리스트")?> <small><?=_("쿠폰 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("쿠폰 목록")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="form1" method="post" action="indb.php">
                  <input type="hidden" name="mode">
                  <div class="panel-body">
                    <div class="table-responsive">
                       <table id="data-table" class="table table-striped table-bordered">
                          <thead>
                             <tr>
                                 <th><?=_("쿠폰식별코드")?></th>
                                 <th><?=_("쿠폰명")?></th>
                                 <th><?=_("쿠폰종류")?></th>
                                 <th><?=_("내용")?></th>
                                 <th><?=_("생성일")?></th>
                                 <th><?=_("수정")?></th>
                                 <th><?=_("삭제")?></th>
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
//추가, 수정 팝업
function coupon_popup(kind, code) {
    //popup('coupon_popup.php?kind=kind&coupon_code='+code,700,550);
    location.href = 'coupon_form.php?kind='+kind+'&coupon_code='+code;
}

//삭제
function coupon_delete(code) {
	//alert('<?=_("준비중입니다.")?>');
   if(confirm('<?=_("정말 삭제하시겠습니까?")?>'))
      location.href = "indb.php?mode=coupon_del&coupon_code="+code;
}
</script>
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
			"sPaginationType" : "bootstrap",
			"aaSorting" : [[4, "desc"]],
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
            url: './coupon_list_page.php?kind=<?=$_GET[kind]?>',
            pages: 10 // number of pages to cache
         })
      });
   });
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>