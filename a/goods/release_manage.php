<?
include "../_header.php";
include "../_left_menu.php";

$postData = base64_encode(json_encode($_POST));
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
         <?=_("제작사관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("제작사관리")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("제작사리스트")?></h4>
            </div>
          
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm">
                  <div class="form-group">
                     <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-danger" onClick="location.href='release_add.php';"><?=_("제작사 추가")?></button>
                     </div>
                  </div>
               </form>
            </div>
            
            <div class="panel-body">
               <div class="table-responsive">
                  <table id="data-table" class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th><?=_("번호")?></th>
                           <th><?=_("아이디")?></th>
                           <th><?=_("회사명")?></th>
                           <th><?=_("별칭")?></th>
                           <th><?=_("연락처")?></th>
                           <!--<th><?=_("주소")?></th>-->
                           <th><?=_("수정")?></th>
                           <th><?=_("삭제")?></th>
                           <th><?=_("추가배송비")?></th>
                        </tr>
                     </thead>
                  </table>
               </div>
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
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         "aaSorting" : [[1, "desc"]],
         "bFilter" : true,
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": false },
         //{ "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
         "ajax": $.fn.dataTable.pipeline({
            url: './release_manage_page.php?postData=<?=$postData?>',
            pages: 5 // number of pages to cache
         })
      });
   });
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>