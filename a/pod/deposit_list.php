<?
/*
* @date : 20181106
* @author : kdk
* @brief : POD용 (알래스카) 입금관리 리스트.
* @request : 
* @desc :
* @todo :
*/
?>
<?
include "../_header.php";
include "../_left_menu.php";

$m_pod = new M_pod();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

### 결제방식:credit_member (선결제,후결제)
### 거래상태:rest_flag (승인,정지)
### 미수금 가져오기
### 입금 가져오기
### 영업담당자 가져오기

$addwhere = "";

/*$search_data = $_POST[search][value];
if ($search_data) {
    $addwhere .= " and (name like '%$search_data%' or mid like '%$search_data%')";
}*/

if(isset($_POST[sword]) && $_POST[sword] != "") { //아이디,회원명,사업자명
    $addwhere .= " and concat(mid,name,cust_name) like '%$_POST[sword]%'";
}

if (array_notnull($_POST[deposit_money])){
   if ($_POST[deposit_money][0]+0) $addwhere .= " and (select sum(deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid)>='{$_POST[deposit_money][0]}'";
   if ($_POST[deposit_money][1]+0) $addwhere .= " and (select sum(deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid)<='{$_POST[deposit_money][1]}'";
}

if (array_notnull($_POST[pre_deposit_money])){
   if ($_POST[pre_deposit_money][0]+0) $addwhere .= " and (select sum(pre_deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid)>='{$_POST[pre_deposit_money][0]}'";
   if ($_POST[pre_deposit_money][1]+0) $addwhere .= " and (select sum(pre_deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid)<='{$_POST[pre_deposit_money][1]}'";
}

if (array_notnull($_POST[remain_money])){
   if ($_POST[remain_money][0]+0) $addwhere .= " and (select sum(remain_price) from pod_pay where cid = a.cid and mid = a.mid)>='{$_POST[remain_money][0]}'";
   if ($_POST[remain_money][1]+0) $addwhere .= " and (select sum(remain_price) from pod_pay where cid = a.cid and mid = a.mid)<='{$_POST[remain_money][1]}'";
}

$orderby = "order by regdt desc";
//$limit = "limit $_POST[start], $_POST[length]";
$query = $m_pod->getMemberList($cid, $addwhere, $orderby, "", true);

$postData = base64_encode(json_encode($_POST));

### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($query));
$url_query = "/a/order/indb.php?query=".$_query;
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
### form 전송 취약점 개선 20160128 by kdk

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
         <?=_("입금관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("입금관리 리스트")?> <small><?=_("입금된 회원들의 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("입금관리 리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="post" action="deposit_list.php">
                  <div class="panel-body">
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("선입금액")?></label>
                         <div class="col-md-4 form-inline">
                            <input type="text" class="form-control" name="deposit_money[]" value="<?=$_POST[deposit_money][0]?>" onkeypress="onlynumber()"/> <?=_("원")?> ~
                            <input type="text" class="form-control" name="deposit_money[]" value="<?=$_POST[deposit_money][1]?>" onkeypress="onlynumber()"/> <?=_("원")?>
                         </div>
                      </div>
                      
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("선발행입금액")?></label>
                         <div class="col-md-4 form-inline">
                            <input type="text" class="form-control" name="pre_deposit_money[]" value="<?=$_POST[pre_deposit_money][0]?>" onkeypress="onlynumber()"/> <?=_("원")?> ~
                            <input type="text" class="form-control" name="pre_deposit_money[]" value="<?=$_POST[pre_deposit_money][1]?>" onkeypress="onlynumber()"/> <?=_("원")?>
                         </div>
                      </div>

                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("미수금액")?></label>
                         <div class="col-md-4 form-inline">
                            <input type="text" class="form-control" name="remain_money[]" value="<?=$_POST[remain_money][0]?>" onkeypress="onlynumber()"/> <?=_("원")?> ~
                            <input type="text" class="form-control" name="remain_money[]" value="<?=$_POST[remain_money][1]?>" onkeypress="onlynumber()"/> <?=_("원")?>
                         </div>
                      </div>
                                                                  
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("검색")?></label>
                         <div class="col-md-6 form-inline">
                            <input type="text" name="sword" class="form-control" value="<?=$_POST[sword]?>" size="100" placeholder='<?=_("아이디,회원명,사업자명을 입력해주세요.")?>'>
                         </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-inverse"><?=_("검색")?></button>
                            <button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>
                        </div>
                      </div>
                     
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("입금기준일자")?></label>
                         <div class="col-md-3 form-inline input-daterange">
                            <input type="text" class="form-control" id="start_date" name="start_date" placeholder="입금기준일자" value="<?=$_POST[start_date]?>" />
                         </div>
                      </div>
                     <br>
                      
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("선택")?></th> 
                                 <th><?=_("아이디")?></th>
                                 <th><?=_("회원명")?></th>
                                 <th><?=_("사업자명")?></th>
                                 <th><?=_("거래상태")?></th>
                                 <th><?=_("영업담당자")?></th>                                 
                                 <th><?=_("미수금액")?></th>
                                 <th><?=_("선입금액")?></th>
                                 <th><?=_("선발행입금액")?></th>
                                 <th><?=_("입금현황")?></th>
                                 <th><?=_("입금입력")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                     
                     <div class="form-group">
                        <button type="button" class="btn btn-xl btn-danger" onClick="memberDepositProc();"><?=_("선택 회원 주문 입금처리")?></button>
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

<!-- ### 엑셀데이타추출용쿼리문 -->
<input type="hidden" name="_query" value="<?=$_query?>&pod_signed=<?=$pod_signed?>&pod_expired=<?=$pod_expired?>">

<script>
//선택 회원 주문 입금처리
function memberDepositProc() {
    var tmp = [];
    var cnt_member = 0;

    var c = document.getElementsByName('chk[]');
    for (var i=0;i<c.length;i++){
        if (c[i].checked) tmp[tmp.length] = c[i].value;
    }
    if (tmp[0]){
        //f.mquery.value = Base64.encode("select * from exm_member where cid = '<?=$cid?>' and mid in ('" + tmp.join("','") + "')");
        cnt_member = tmp.length;
    }else{
        alert('<?=_("선택된 회원이 없습니다.")?>',-1);
        return false;
    }

    if (confirm('<?=_("선택한 회원 주문을 입금처리 하시겠습니까?")?>' + "\n" + '<?=_("처리된 데이터는 즉시 반영됩니다.")?>')) {
        $j.ajax({
            type: "POST",
            url: "indb.php",
            data: "mode=member_deposit_proc&mid=" + tmp.join(","),
            success: function(ret){
                console.log(ret);
                if (ret == "OK") {
                    alert('<?=_("입금처리가 완료되었습니다.")?>');
                    document.location.reload();
                }
                else {
                    alert('<?=_("입금처리중 오류가 발생했습니다. 다시 시도해 주세요.")?>');
                }
            }
        });
    }
    else {
        return false;
    }

}

function dnExcel() {
    var query = document.getElementsByName("_query")[0].value;
    hidden_frm.location.href = "../order/indb.php?mode=dnExcel&kind=deposit_list_pod&query=" + query;
}

function depositForm(mid){
    popup('deposit_form_popup.php?mid='+mid+'&start_date='+$("input[name=start_date]").val(),700,650);
}
</script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
            "sPaginationType" : "bootstrap",
            "aaSorting" : [[1, "desc"]],
            "bFilter" : false,
            "pageLength": 10,
            "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
            },
            "aoColumns": [
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
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
            url: './deposit_list_page.php?postData=<?=$postData?>',
            pages: 1 // number of pages to cache
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