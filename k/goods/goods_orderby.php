<?
include_once "../_header.php";
include_once "../_left_menu.php";

$m_goods = new M_goods();

if($_POST['sort']) {
   $selected['sort'][$_POST['sort']] = "selected";
} else {
   $_POST['sort']['popular'] = "all";
}

if($_POST[catno]) {
   $selected[catno][$_POST[catno]] = "selected";
}

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

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
         <?=_("상품 우선순위 설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("상품 우선순위 설정")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("상품리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post">
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("등록일")?></label>
                     <div class="col-md-3">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start_date" placeholder="Date Start" value="<?=$_POST[start_date]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end_date" placeholder="Date End" value="<?=$_POST[end_date]?>" />
                        </div>
                     </div>                     
                  </div>

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("카테고리 선택")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="catno" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $_POST[catno])?></select>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("우선순위 목록")?></label>
                     <div class="col-md-2">
                        <select name="sort" class="form-control">
                           <? foreach($_r_mdn_goods_sort as $k => $v) { ?>
                              <option value="<?=$k?>" <?=$selected['sort'][$k]?>><?=$v?></option>
                           <? } ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-1">
                     <button type="submit" class="btn btn-sm btn-success">
                        <?=_("조 회")?>
                     </button>
                  </div>
               </form>
            </div>

            <div class="panel-body">
               <form name="fm" method="post" action="indb.php">
                  <input type="hidden" name="mode" value="sort_priority">
                  <input type="hidden" name="sort" value="<?=$_POST['sort']?>">
                  <input type="hidden" name="catno" value="<?=$_POST[catno]?>">
                  <div class="table-responsive">
                     <table id="data-table" class="table table-striped table-bordered">
                        <thead>
                           <tr>
                              <!--<th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>-->
                              <th><?=_("번호")?></th>
                              <th><?=_("이미지")?></th>
                              <th><?=_("상품명")?></th>
                              <th><?=_("등록일")?></th>
                              <th><?=_("우선순위")?></th>
                           </tr>
                        </thead>
                     </table>
                  </div>
                  
                  <div class="col-md-12 col-sm-6">
                     <div>
                        <button type="submit" class="btn btn-sm btn-primary"><?=_("저장하기")?></button>
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
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script>
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
         { "bSortable": true },
         ],
         "processing": true,
         "serverSide": true,
         "bAutoWidth": false,
         //"scrollX" : true,
         "ajax": $.fn.dataTable.pipeline({
            url: './goods_orderby_page.php?postData=<?=$postData?>',
            pages: 5 // number of pages to cache
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