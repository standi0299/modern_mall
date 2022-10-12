<?
include "../_header.php";
include "../_left_menu.php";

$m_pretty = new M_pretty();

$list = $m_pretty->getNoticeBoardList($cid, 'notice', 'order by no desc limit 0, 10');

$totalCnt = $m_pretty->getNoticeBoardCount($cid, 'notice');

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
         <?=_("공지사항")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("공지사항")?> <small><?=_("공지사항 관리")?>.</small></h1>
   
   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("공지사항")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered">
                  <div class="form-group">
                     <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-success" onClick="location.href='notice.w.php?board_id=notice&mode=write';">
                           <?=_("글쓰기")?>
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
                                    <th><?=_("제목")?></th>
                                    <th><?=_("이름")?></th>
                                    <th><?=_("날짜")?></th>
                                    <th><?=_("조회")?></th>
                                    <th><?=_("수정")?></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <? if($list) {?>
                                    <? foreach ($list as $key => $value) { ?>
                                    <tr>
                                       <td><?=$key+1?></td>
                                       <td><a href='notice.w.php?board_id=notice&mode=view&no=<?=$value[no]?>'><?=$value[subject]?></a></td>
                                       <td><?=$value[name]?></td>
                                       <td><?=$value[regdt]?></td>
                                       <td><?=$value[hit]?></td>
                                       <td>
                                          <button type="button" class="btn btn-xs btn-primary" onclick=location.href='notice.w.php?board_id=notice&mode=modify&no=<?=$value[no]?>'>
                                             <?=_("수정")?>
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
         { "bSortable": true },
         { "bSortable": true },
         { "bSortable": false },
         ],
         "processing": true,
         "serverSide": true,
         "deferLoading": <?=$totalCnt?>,
         "ajax": $.fn.dataTable.pipeline( {
            url: './notice_page.php',
            pages: 5 // number of pages to cache
         })
      });
   });
</script>
<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>