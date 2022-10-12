<?
include "../_header.php";
include "../_left_menu.php";

$m_board = new M_board();

$data = $m_board->getBoardSetList($cid);
foreach ($data as $key => $value) {
	$r_board[$value[board_id]] = $value[board_name];
}

$where = "";
$board_id = "";
if ($_GET[regdt][0]) $where .= " and regdt >= '{$_GET[regdt][0]}'";
if ($_GET[regdt][1]) $where .= " and regdt < ADDDATE('{$_GET[regdt][1]}',interval 1 day)";
if ($_GET[sword]) $where .= " and concat(mid,name,category,subject) like '%$_GET[sword]%'";
if ($_GET[board]) $board_id = $_GET[board];

$list = $m_board->getBoardList($cid, $board_id, $where, 'no desc limit 0, 10');
$totalCnt = $m_board->getBoardCount($cid, $board_id, $where);

$selected[board][$_GET[board]] = "selected";
$param = "regdt[]=".$_GET[regdt][0]."&regdt[]=".$_GET[regdt][1]."&sword=".$_GET[sword]."&board=".$_GET[board];
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
         <?=_("게시글 관리")?>
      </li>
   </ol>
   <h1 class="page-header"><?=_("게시글 관리")?> <small><?=_("등록된 게시글 관리")?></small></h1>
   
   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
              	   <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;"  class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
            	<h4 class="panel-title"><?=_("게시글 관리")?></h4>
            </div>    
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered">               
              	   <div class="form-group">
                     <div class="col-md-12">
                        <label class="col-md-2 control-label"><?=_("게시판 선택")?></label>
                        <div class="col-md-2">
                           <select name="board" class="form-control">
                             <option value=""><?=_("전체")?></option>
                              <?foreach($r_board as $k=>$v){?>
                                 <option value="<?=$k?>" <?=$selected[board][$k]?>><?=$v?>
                              <?}?>
                           </select>
                        </div>
                     
                        <div class="col-md-3">
                           <input type="text" class="form-control" name="sword">
                        </div>
                 	   </div>
              	   </div>
              	
              	   <div class="form-group">
                     <div class="col-md-12">
                        <label class="col-md-2 control-label"><?=_("작성일자")?></label>
                        <div class="col-md-4">
                           <div class="input-group input-daterange">
                              <input type="text" class="form-control" name="regdt[]" placeholder="Date Start" value="<?=$_GET[regdt][0]?>" />
                              <span class="input-group-addon"> ~ </span>
                              <input type="text" class="form-control" name="regdt[]" placeholder="Date End" value="<?=$_GET[regdt][1]?>" />
                           </div>
                        </div>
                        
                        <div class="col-md-4">
                           <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdt('today','regdt[]');"><?=_("오늘")?>
                           </button>
                           <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdt('tdays','regdt[]');"><?=_("3일")?>
                           </button>
                           <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdt('week','regdt[]');"><?=_("1주일")?>
                           </button>
                           <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdt('month','regdt[]');"><?=_("1달")?>
                           </button>                      
                        </div>
                        <div class="col-md-2">
                           <button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
                        </div>
                     </div>
                  </div>
                
                  <div class="form-group">
                     <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-default" onclick="location.href='board_write.php?board=<?=$_GET[board]?>';"><?=_("새글작성하기")?> </button>
                     </div>
                  </div>
               </form>
            </div>
                  
            <!-- 기존 유치원 소스 붙이기 -->
            <div class="panel-body">                     
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
                           <? foreach ($list as $key => $value) {
										if ($value[notice] == "-1") $notice_tag = "[" ._("알림"). "] ";
										else $notice_tag = "";

										if ($value[secret] == "1") $secret_tag = "[" ._("비밀"). "] ";
										else $secret_tag = "";
                           ?>
                           <tr>
                              <td><?=$key+1?></td>
                              <td><?=$notice_tag?><?=$secret_tag?><a href='board_write.php?board_id=<?=$value[board_id]?>&mode=view&no=<?=$value[no]?>'><?=$value[subject]?></a></td>
                              <td><?=$value[name]?></td>
                              <td><?=$value[regdt]?></td>
                              <td><?=$value[hit]?></td>
                              <td>
                                 <button type="button" class="btn btn-xs btn-primary" onclick=location.href='board_write.php?board_id=<?=$value[board_id]?>&mode=modify&no=<?=$value[no]?>'>
                                    <?=_("수정")?>
                                 </button>
                              </td>
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
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

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
            url: './board_page.php?<?=$param?>',
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