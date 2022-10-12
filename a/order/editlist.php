<?
include "../_header.php";
include "../_left_menu.php";

$m_goods = new M_goods();

### 배송업체정보추출
$r_shipcomp = get_shipcomp();

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

$r_state = array("0"=>_("편집중"),"1"=>_("편집완료"),"9"=>_("주문접수"));

if ($_POST[catno]){
   $_POST[catno] = array_notnull($_POST[catno]);
   list($_POST[catno]) = array_slice($_POST[catno],-1);
}

$checked[state][$_POST[state]] = "checked";

if(!$_POST[start] && !$_POST[end] ) {
	$_POST[start] = date("Y-m-d");
	$_POST[end] = date("Y-m-d");
}

if($_POST) {
   if($_POST[start]) $addWhere .= " and a.updatedt > '{$_POST[start]}'";
   if($_POST[end]) $addWhere .= " and a.updatedt < adddate('{$_POST[end]}',interval 1 day)";
   if($_POST[state] != null) $addWhere .= " and a.state = '$_POST[state]'";
   
   if($_POST[catno]){
      if (is_numeric($_POST[catno])){
         $addWhere .= " and e.catno like '$_POST[catno]%'";
      }
   }

	$search_data = $_POST[search][value];
	if ($search_data) {
	   $addWhere .= " and (storageid like '%$search_data%' or goodsnm like '%$search_data%' or concat(a.mid,b.name) like '%$search_data%')";
	}
}

$query = "select b.name,c.goodsnm,d.*,
    #if(d.podsno < 1 or d.podsno = '' or d.podsno is null,c.podsno,d.podsno) podsno,a.* 
    storageid,a.goodsno,a.optno,addoptno,a.cid,a.mid,a.editkey,a.updatedt,a.state,a.title,a.est_order_data,a.est_order_option_desc,a.est_file_down_full_path,
    a.est_order_type,a.est_cost,a.est_supply,a.est_price,a.est_rid,a.est_goodsnm,a.est_fullpost,a.est_pods_version,a._hide,a._hidelog,
    ifnull(a.pods_use,c.pods_use) pods_use,
    ifnull(a.podsno,c.podsno) podsno,
    ifnull(a.podskind,c.podskind) podskind,
    a.catno
    from exm_edit a
	left join exm_member b on a.cid = b.cid and b.mid = a.mid
    left join exm_goods c on c.goodsno = a.goodsno
    left join exm_goods_opt d on d.goodsno = a.goodsno and d.optno = a.optno
    left join exm_goods_link e on e.cid = a.cid and e.goodsno = a.goodsno
    where a.cid = '$cid' $addWhere group by storageid order by a.updatedt desc 
";
//debug($query);

### 데이타 추출용 전체쿼리
$_query = $query;

### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($_query));
$url_query = "/admin/xls/indb.php?query=".$_query;
//debug($url_query);
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
//debug($pod_signed);
//debug($pod_expired);

$postData = base64_encode(json_encode($_POST));
//debug($_POST);
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
         <?=_("편집리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("편집리스트")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("편집리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST">
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품분류")?></label>
                     <div class="col-md-10 form-inline">
		         		 <select name="catno[]" class="form-control">
		         			 <option value="">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $_POST[catno])?>
		         		 </select>
		         	 </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("최종수정날짜")?></label>
                     <div class="col-md-3">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST[end]?>" />
                        </div>
                     </div>
                     <div class="col-md-5">
                        <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');">
                           <?=_("오늘")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');">
                           <?=_("3일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');">
                           <?=_("1주일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');">
                           <?=_("1달")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start', 'all'); regdtOnlyOne('today','end');">
                           <?=_("전체")?>
                        </button>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("현재상태")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="radio" name="state" class="form-control"  value="" checked><?=_("전체")?>
                        <? foreach ($r_state as $k=>$v){ ?>
                        <input type="radio" name="state" class="radio-inline" value="<?=$k?>" <?=$checked[state][$k]?>><?=$v?>
                        <? } ?>
                     </div>
                  </div>

                  <div class="form-group">
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="xls_case('editlist','<?=$_query?>&pod_signed=<?=$pod_signed?>&pod_expired=<?=$pod_expired?>')"><?=_("엑셀저장")?></button>
                    </div>
                  </div>
                  
                  <div class="form-group">
                     <div class="col-md-12 form-inline">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("상품번호")?></th>
                                 <th><?=_("상품명")?></th>
                                 <th><?=_("회원아이디")?></th>
                                 <th><?=_("현재상태")?></th>
                                 <th><?=_("최종수정일시")?></th>
                                 <th><?=_("데이터상태")?></th>
                                 <th><?=_("처리")?></th>
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

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="/js/pods_editor.js"></script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         //"aaSorting" : [[0, "desc"]],
         "bFilter" : true,
         "aLengthMenu": [10, 25, 50, 100],
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": true },
         ],
         "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
         "ajax": $.fn.dataTable.pipeline({
            url: './editlist_page.php?postData=<?=$postData?>',
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