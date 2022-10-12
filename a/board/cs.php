<?
include "../_header.php";
include "../_left_menu.php";

if(!$_POST[status]) $_POST[status] = 0;
if($_POST[sword] && !$_POST[stype]) unset($_POST[sword]);
if(!$_POST[category]) $_POST[category] = 0;

$m_pretty = new M_pretty();

$addQuery = '';
if ($_POST[stype] && $_POST[sword]) $addQuery .= " and $_POST[stype] like '%$_POST[sword]%'";
if ($_POST[status]) $addQuery .= " and status = '$_POST[status]'";
if ($_POST[start]) $addQuery .= " and regdt >= '$_POST[start] 00:00:00'";
if ($_POST[end]) $addQuery .= " and regdt <= '$_POST[end] 23:59:59'";
if ($_POST[category]) $addQuery .= " and category = '$_POST[category]'";

$addQuery .= " order by no desc limit 10";
$list = $m_pretty->getMycs($cid, "cs", $addQuery);

$checked[status][$_POST[status]] = "checked";
$selected[stype][$_POST[stype]]  = "selected";
$selected[category][$_POST[category]] = "selected";

$postData = base64_encode(json_encode($_POST));

$totalCnt = $m_pretty->getMycsCount($cid, "cs", $addQuery);
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
         <?=_("1:1문의")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("1:1문의")?> <small><?=_("1:1문의 관리")?>.</small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("1:1문의")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post" action="cs.php">
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("검색")?></label>
                     <div class="col-md-4 form-inline">
                        <select name="stype" class="form-control">
                        <option value=""><?=_("선택")?></option>
                        <option value="name" <?=$selected[stype][name]?>><?=_("작성자")?></option>
                        <option value="subject" <?=$selected[stype][subject]?>><?=_("제목")?></option>
                        <option value="content" <?=$selected[stype][content]?>><?=_("내용")?></option>
                        </select>
                        <input type="text" class="form-control" name="sword" value="<?=$_POST[sword]?>">
                     </div>
                     
                     <label class="col-md-1 control-label"><?=_("문의유형")?></label>
                     <div class="col-md-6 form-inline">
                        <select name="category" class="form-control">
                        <option value="0" <?=$selected[category][0]?>><?=_("전체")?></option>
                        <? foreach ($r_cs_category as $category_k=>$category_v) { ?>
                        <option value="<?=$category_k?>" <?=$selected[category][$category_k]?>><?=$category_v?></option>
                        <? } ?>
                        </select>
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
                     <div class="col-md-7">
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
                  
                  <div class="form-group">
                     <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-default" onclick="popup('cstime_popup.php', 630, 310)">
                           <?=_("운영시간설정")?>
                        </button>
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
                                 <th width="75"><?=_("문의유형")?></th>
                                 <th><?=_("제목")?></th>
                                 <th><?=_("작성자")?></th>
                                 <th width="50"><?=_("날짜")?></th>
                                 <th width="50"><?=_("답변여부")?></th>
                                 <th><?=_("답변")?></th>
                                 <th><?=_("삭제")?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <? if($list) {?>
                                 <? foreach ($list as $key => $value) { ?>
                                 <tr>
                                    <td><?=$key+1?></td>
                                    <td><?=$r_cs_category[$value[category]]?></td>
                                    <td><a href='cs.w.php?no=<?=$value[no]?>'><?=stripslashes($value[subject])?></a></td>
                                    <td><a href="javascript:;" <?if($value[mid]!="admin"){?>onclick="popup('../member/member_detail_popup.php?mode=member_modify&mid=<?=$value[mid]?>',1100,800)"<?}?>><?=$value[name]?></a><?if($value[mid]){?><br>(<?=$value[mid]?>)<?}?></td>
                                    <td><?=$value[regdt]?></td>
                                    <td><b <?if($value[status]==2){?>style="color:#28a5f9"<?}?>><?=$r_cs[$value[status]]?></b></td>
                                    <td>
                                       <button type="button" class="btn btn-xs btn-primary" onclick=location.href='cs.w.php?no=<?=$value[no]?>'>
                                          <?=_("답변달기")?>
                                       </button>
                                    </td>
                                    <td>
                                       <button type="button" class="btn btn-xs btn-danger" onclick="cs_delete('<?=$value[no]?>');">
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
   function cs_delete(no){
      if(confirm('<?=_("정말 삭제하시겠습니까?")?>'))
         location.href = "indb.php?mode=cs_del&no="+no;
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
            url: './cs_page.php?postData=<?=$postData?>',
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