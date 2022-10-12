<?
include "../_header.php";
include "../_left_menu.php";

$m_pretty = new M_pretty();

$list = $m_pretty->getFAQ($cid, 'limit 10');

$totalCnt = $m_pretty->getFAQCount($cid);
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
         <?=_("FAQ")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("FAQ")?> <small><?=_("FAQ 관리")?>.</small></h1>
   
   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("FAQ")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered">
               	  <div class="form-group">
                     <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-success" onClick="faq_popup('');">
                           <?=_("추가")?>
                        </button>
                     </div>
                  </div>
               	
                  <!-- 기존 유치원 소스 붙이기 -->
                  <div class="panel-body">
                     <form name="form1" method="post" action="indb.php">
                        <input type="hidden" name="mode">
                        <div class="table-responsive">
                           <table id="data-table" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                    <th><?=_("번호")?></th>
                                    <th><?=_("분류")?></th>
                                    <th><?=_("질문")?></th>
                                    <th><?=_("수정")?></th>
                                    <th><?=_("삭제")?></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <? if($list) {?>
                                    <? foreach ($list as $key => $value) { ?>
                                    <tr>
                                       <td><?=$key+1?></td>
                                       <td><?=$value[catnm]?></td>
                                       <td><?=$value[q]?></td>
                                       <td>
                                          <button type="button" class="btn btn-xs btn-primary" onclick="faq_popup('<?=$value[no]?>');">
                                             <?=_("수정")?>
                                          </button>
                                       </td>
                                       <td>
                                          <button type="button" class="btn btn-xs btn-danger" onclick="faq_delete('<?=$value[no]?>');">
                                             <?=_("삭제")?>
                                          </button>
                                       </td>
                                    </tr>
                                    <? } ?>
                                 <? } ?>
                              </tbody>
                           </table>
                        </div>
                     </form>
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

<!-- ================== END PAGE LEVEL JS ================== -->

<script>
//faq 수정 팝업
function faq_popup(no) {
   popup('faq_modify_popup.php?no='+no,700,550);
}

//faq삭제
function faq_delete(no) {
   if(confirm('<?=_("정말 삭제하시겠습니까?")?>'))
      location.href = "indb.php?mode=faq_del&no="+no;
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
         "aLengthMenu": [10, 25, 50],
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": true,
         "serverSide": true,
         "deferLoading": <?=$totalCnt?>,
         "ajax": $.fn.dataTable.pipeline( {
            url: './faq_page.php?',
            pages: 5 // number of pages to cache
         })
      });
   });
</script>
<script src="../js/datatable_page.js"></script>
<? include "../_footer_app_exec.php"; ?>