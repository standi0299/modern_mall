<?
include "../_header.php";
include "../_left_menu.php";

if(!$_POST[status]) $_POST[status] = 0;
if($_POST[sword] && !$_POST[stype]) unset($_POST[sword]);

$m_pretty = new M_pretty();

$addQuery = '';
if ($_POST[stype] && $_POST[sword]) $addQuery .= " and $_POST[stype] like '%$_POST[sword]%'";
if ($_POST[status]) $addQuery .= " and status = '$_POST[status]'";
if ($_POST[start]) $addQuery .= " and regdt >= '$_POST[start] 00:00:00'";
if ($_POST[end]) $addQuery .= " and regdt <= '$_POST[end] 23:59:59'";

$addQuery .= " order by regdt desc limit 10";
$list = $m_pretty->getMycs($cid, "qna", $addQuery);

$checked[status][$_POST[status]] = "checked";
$selected[stype][$_POST[stype]]  = "selected";

$postData = base64_encode(json_encode($_POST));

$totalCnt = $m_pretty->getMycsCount($cid, "qna", $addQuery);
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
         <?=_("상품문의")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("상품문의")?> <small><?=_("상품문의 정보를 보실 수 있습니다.")?>.</small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn"></div>
               <h4 class="panel-title"><?=_("상품문의")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post" action="qna.php">

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("검색")?></label>
                     
                     <div class="col-md-10 form-inline">
                        <select name="stype" class="form-control">
                        <option value=""><?=_("선택")?>
                        <option value="name" <?=$selected[stype][name]?>><?=_("작성자")?>
                        <option value="subject" <?=$selected[stype][subject]?>><?=_("제목")?>
                        <option value="content" <?=$selected[stype][content]?>><?=_("내용")?>
                        </select>
                        <input type="text" class="form-control" name="sword" value="<?=$_POST[sword]?>">
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("작성일")?></label>
                     <div class="col-md-4">
                       <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST[end]?>" />
                        </div>
                     </div>
                     <div class="col-md-4">
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');"><?=_("오늘")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');"><?=_("3일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');"><?=_("1주일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');"><?=_("1달")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('all','start', 'all'); regdtOnlyOne('today','end');"><?=_("전체")?>
                        </button>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("답변여부")?></label>
                     <div class="col-md-10">
                        <input type="radio" name="status" value="0" <?=$checked[status][0]?>/> <?=_("전체")?>
                     	<input type="radio" name="status" value="1" <?=$checked[status][1]?>/> <?=_("대기")?>
                     	<input type="radio" name="status" value="2" <?=$checked[status][2]?>/> <?=_("완료")?>
                     </div>
                     <div class="col-md-1">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
                     </div>
                  </div>
               </form>

               <div class="panel-body">
                  <form name="form1" method="post" action="indb.php">
                     <input type="hidden" name="mode">
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("번호")?></th>
                                 <th><?=_("질문")?></th>
                                 <th><?=_("작성자")?></th>
                                 <th><?=_("작성일자")?></th>
                                 <th><?=_("답변여부")?></th>
                                 <th><?=_("수정")?></th>
                                 <th><?=_("삭제")?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <? if($list) {?>
                                 <? foreach ($list as $key => $value) { ?>
                                 <tr>
                                    <td><?=$key+1?></td>
                                    <td><a href='qna.w.php?no=<?=$value[no]?>'><?=$value[subject]?></a></td>
                                    <td><a href="javascript:;" <?if($value[mid]!="admin"){?>onclick="popup('../member/member_detail_popup.php?mode=member_modify&mid=<?=$value[mid]?>',1100,800)"<?}?>><?=$value[name]?></a><?if($value[mid]){?><br>(<?=$value[mid]?>)<?}?></td>
                                    <td><?=$value[regdt]?></td>
                                    <td><b <?if($value[status]==2){?>style="color:#28a5f9"<?}?>><?=$r_cs[$value[status]]?></b></td>
                                    <td>
                                       <button type="button" class="btn btn-xs btn-primary" onclick=location.href='qna.w.php?no=<?=$value[no]?>'>
                                          <?=_("수정")?>
                                       </button>
                                    </td>
                                    <td>
                                       <button type="button" class="btn btn-xs btn-danger" onclick="qna_delete('<?=$value[no]?>');">
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

<!-- ================== END PAGE LEVEL JS ================== -->

<script>
   function qna_delete(no){
      if(confirm('<?=_("정말 삭제하시겠습니까?")?>'))
         location.href = "indb.php?mode=qna_del&no="+no;
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
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": true,
         "serverSide": true,
         "deferLoading": <?=$totalCnt?>,
         "ajax": $.fn.dataTable.pipeline( {
            url: './qna_page.php?postData=<?=$postData?>',
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