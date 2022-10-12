<?
include "../_header.php";
include "../_left_menu.php";

//$query = "select *,(select count(*) from exm_board where cid = '$cid' and board_id=a.board_id) article from exm_board_set a where cid = '$cid'";
//$res = $db->query($query);

$m_board = new M_board();

$list = $m_board->getBoardSetList($cid);
$totalCnt = $m_board->getBoardSetCount($cid);
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
         <?=_("게시판 관리")?>
      </li>
   </ol>
  <h1 class="page-header"><?=_("게시판 관리")?> <small><?=_("생성된 게시판 관리")?></small></h1>
   
   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">            
            <div class="panel-heading">
              <div class="panel-heading-btn">
              	<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                <a href="javascript:;"  class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
              </div>
            	<h4 class="panel-title"><?=_("게시판 관리")?></h4>
            </div>
                
           	<!-- 
            <div class="panel-body panel-form">
            	<form class="form-horizontal form-bordered">               
              	<div class="form-group">
                	<div class="col-md-12">
                  	<button type="button" class="btn btn-sm btn-success" onClick="location.href='board_set_config.php';">
                    <?=_("게시판 추가")?>
                    </button>
                 	</div>
              	</div>
           		</form>
           	</div>
           -->
                  
            <!-- 기존 유치원 소스 붙이기 -->
            <div class="panel-body">                     
              <div class="table-responsive">
                 <table id="data-table" class="table table-striped table-bordered">
                    <thead>
                       <tr>
                          <th><?=_("번호")?></th>
                          <th><?=_("게시판 아이디")?></th>
                          <th><?=_("게시판명")?></th>
                          <th><?=_("스킨")?></th>
                          <th><?=_("게시물수")?></th>
                          <th><?=_("보기")?></th>
                          <th><?=_("수정")?></th>
                          <th><?=_("삭제")?></th>
                       </tr>
                    </thead>
                    <tbody>
                       
                          <? if($list) {?>
                           	<? foreach ($list as $key => $value) { ?>
                          <tr>
                             <td><?=$key++?></td>
                             <td><?=$value[board_id]?></td>
                             <td><?=$value[board_name]?></td>
                             <td><?=$value[board_skin]?></td>
                             <td><?=number_format($value[article])?></td>
                             <td><button type="button" class="btn btn-xs btn-info" onclick="window.open('../../board/list.php?board_id=<?=$value[board_id]?>');"><?=_("보기")?></button></td>
                             <td><button type="button" class="btn btn-xs btn-primary" onclick="location.href='board_set_config.php?board_id=<?=$value[board_id]?>';"><?=_("수정")?></button></td>
                             <td>
                             	<? if (!in_array($value[board_id],array('notice','qna'))){ ?>
                             	<button type="button" class="btn btn-xs btn-danger" onclick="board_delete('<?=$value[board_id]?>');"><?=_("삭제")?></button></td>
                             	<?	}	?>
                          </tr>
                          	<? } ?>
                       	  <? } ?>
                    </tbody>
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
         { "bSortable": false },
         { "bSortable": false },
         ],
      });
   });

	function board_delete(board_id)
	{
		if (confirm('<?=_("정말 삭제하시겠습니까?")?>'))
	   	{
				location.href="indb.php?mode=board_remove&board_id=" + board_id;
	   	}
	}
</script>

<? include "../_footer_app_exec.php"; ?>