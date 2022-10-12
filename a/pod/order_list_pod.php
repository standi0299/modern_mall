<?
/*
* @date : 20181106
* @author : kdk
* @brief : POD용 (알래스카) 주문관리 리스트.
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

if(isset($_POST[manager_no]) && $_POST[manager_no] != "") {
    $addwhere .= " and a.manager_no like '%$_POST[manager_no]%'";
}

if (array_notnull($_POST[payprice])){
   if ($_POST[payprice][0]+0) $addwhere .= " and a.payprice>='{$_POST[payprice][0]}'";
   if ($_POST[payprice][1]+0) $addwhere .= " and a.payprice<='{$_POST[payprice][1]}'";
}

if (array_notnull($_POST[orddt])){
   if ($_POST[orddt][0]) $addwhere .= " and a.orddt>='{$_POST[orddt][0]} 00:00:00'";
   if ($_POST[orddt][1]) $addwhere .= " and a.orddt<='{$_POST[orddt][1]} 23:59:59'";
}

if (array_notnull($_POST[receiptdt])){
   if ($_POST[receiptdt][0]) $addwhere .= " and a.receiptdt>='{$_POST[receiptdt][0]}'";
   if ($_POST[receiptdt][1]) $addwhere .= " and a.receiptdt<='{$_POST[receiptdt][1]}'";
}

if(isset($_POST[sword]) && $_POST[sword] != "") { //아이디,회원명,사업자명,접수담당자,주문번호,주문명
    $addwhere .= " and concat(a.mid,b.name,b.cust_name,a.receiptadmin,a.payno,a.order_title) like '%$_POST[sword]%'";
}

$orderby = "order by a.orddt desc";
//$limit = "limit $_POST[start], $_POST[length]";
$query = $m_pod->getOrderList($cid, $addwhere, $orderby, "", true);

$postData = base64_encode(json_encode($_POST));

### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($query));
$url_query = "/a/order/indb.php?query=".$_query;
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
### form 전송 취약점 개선 20160128 by kdk

$selected[manager_no][$_POST[manager_no]] = "selected";
$checked[credit_member][$_POST[credit_member]] = "checked";
$checked[rest_flag][$_POST[rest_flag]] = "checked";
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
         <?=_("주문관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("주문관리 리스트")?> <small><?=_("주문정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <form class="form-horizontal form-bordered" name="fm" method="post" action="order_list_pod.php">
            <input type="hidden" name="mode" value=""/>
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("주문관리 리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               
                  <div class="panel-body">
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("영업담당자")?></label>
                         <div class="col-md-2 form-inline">
                            <select name="manager_no" class="form-control">
                                <option value=""><?=_("선택")?>
                                <?foreach($r_manager as $k=>$v){?>
                                    <option value="<?=$v[mid]?>" <?=$selected[manager_no][$v[mid]]?>><?=$v[name]?></option>
                                <?}?>
                            </select>                            
                         </div>
                      </div>
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("주문(결제)금액")?></label>
                         <div class="col-md-4 form-inline">
                            <input type="text" class="form-control" name="payprice[]" value="<?=$_POST[payprice][0]?>" onkeypress="onlynumber()"/> <?=_("원")?> ~
                            <input type="text" class="form-control" name="payprice[]" value="<?=$_POST[payprice][1]?>" onkeypress="onlynumber()"/> <?=_("원")?>
                         </div>
                      </div>
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("주문일")?></label>
                         <div class="col-md-3 form-inline">
                            <div class="input-group input-daterange">
                               <input type="text" class="form-control" name="orddt[]" placeholder="Date Start" value="<?=$_POST[orddt][0]?>" />
                               <span class="input-group-addon"> ~ </span>
                               <input type="text" class="form-control" name="orddt[]" placeholder="Date End" value="<?=$_POST[orddt][1]?>" />
                            </div>
                         </div>
                      </div>
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("접수일")?></label>
                         <div class="col-md-3 form-inline">
                            <div class="input-group input-daterange">
                               <input type="text" class="form-control" name="receiptdt[]" placeholder="Date Start" value="<?=$_POST[receiptdt][0]?>" />
                               <span class="input-group-addon"> ~ </span>
                               <input type="text" class="form-control" name="receiptdt[]" placeholder="Date End" value="<?=$_POST[receiptdt][1]?>" />
                            </div>
                         </div>
                      </div>
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("검색")?></label>
                         <div class="col-md-6 form-inline">
                            <input type="text" name="sword" class="form-control" value="<?=$_POST[sword]?>" size="100" placeholder='<?=_("아이디,회원명,사업자명,접수담당자,주문번호,주문명을 입력해주세요.")?>'>
                         </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-inverse"><?=_("검색")?></button>
                            <button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>
                        </div>
                      </div>
                     <br>
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("선택")?></th>
                                 <th><?=_("주문번호")?></th>
                                 <th><?=_("사업자명")?></th> 
                                 <th><?=_("사업자번호")?></th>
                                 <th><?=_("회원명")?></th>
                                 <th><?=_("아이디")?></th>
                                 <th><?=_("주문명")?></th>
                                 <th><?=_("상품명")?></th>
                                 <th><?=_("주문사양")?></th>
                                 <th><b class="red"><?=_("주문금액")?></b></th>
                                 <th><b class="red"><?=_("입금액")?></b></th>
                                 <th><b class="red"><?=_("선발행입금사용금액")?></b></th>
                                 <th><b class="red"><?=_("미수금액")?></b></th>
                                 <th><b class="red"><?=_("영업담당자")?></b></th>
                                 <th><b class="red"><?=_("주문일시")?></b></th>
                                 <th><b class="red"><?=_("접수담당자")?></b></th>
                                 <th><b class="red"><?=_("접수일시")?></b></th>
                                 <th><b class="red"><?=_("출고담당자")?></b></th>
                                 <th><b class="red"><?=_("출고일시")?></b></th>
                                 <th><b class="red"><?=_("진행상태")?></b></th>
                                 <th><?=_("상태갱신일시")?></th>
                                 <th><?=_("자동입금처리제외")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>

            </div>
         </div>

         <div class="panel panel-inverse">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#default-tab-1" data-toggle="tab"><?=_("주문접수")?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="default-tab-1">

                    <div class="panel-body panel-form">    
                        <div class="panel-body">
                            <div class="form-group">
                               <label class="col-md-1 control-label"><?=_("접수일") ?></label>
                               <div class="col-md-2 input-daterange">
                                    <input type="text" class="form-control" id="receipt_dt" name="receipt_dt" placeholder="Date Start" value="<?=date("Y-m-d")?>" />
                               </div>
                               <label class="col-md-1 control-label"><?=_("접수담당자") ?></label>
                               <div class="col-md-2">
                                    <select id="receiptadmin" name="receiptadmin" class="form-control">
                                        <option value=""><?=_("선택")?>
                                        <?foreach($r_manager as $k=>$v){?>
                                            <option value="<?=$v[mid]?>" <?if($v[mid]==$sess_admin[mid]){?>selected<?}?>><?=$v[name]?></option>
                                        <?}?>
                                    </select>
                               </div>
                            </div>
                        </div>
                        <div class="btn">
                           <button type="button" class="btn btn-danger m-r-5 m-b-5" onclick="order_proc();"><?=_("선택주문 일괄접수")?></button>
                        </div>
                        
                    </div>
                </div>
            </div>
         </div>
         </form>
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
function dnExcel() {
    var query = document.getElementsByName("_query")[0].value;
    hidden_frm.location.href = "../order/indb.php?mode=dnExcel&kind=order_list_pod&query=" + query;
}

function order_proc(){
    var fm = document.fm;

    var chk;
    var mode = "92to3"; //관리자 상품제작중 처리 : 접수완료
    var chkNum = 0;
    var c = document.getElementsByName('chk[]');

    for (var i = 0; i < c.length; i++) {
        if (c[i].checked) {
            chk = true;
            chkNum++;
        }
    }

    var step = "<?=$_GET[itemstep]?>";

    /*if(step == 1) mode = "1to2";
    else if(step == 2) mode = "2to3";
    else if(step == 3) mode = "3to4";
    else if(step == 4) mode = "4to5";
    else if(step == 91) mode = "91to92";
    else if(step == 92) mode = "92to3";*/
     
    if (chk) {
        if($("#receipt_dt").val() == "") {
            alert('<?=_("접수일을 선택해주세요.")?>');
            $("#receipt_dt").focus();
            return false;
        }
        if($("#receiptadmin").val() == "") {
            alert('<?=_("접수담당자를 선택해주세요.")?>');
            $("#receiptadmin").focus();
            return false;
        }

        if(confirm('<?=_("주문 진행단계를 다음 접수완료 단계로 변경하시겠습니까?")?>') == true) {
            fm.action = "indb.php";
            fm.mode.value = mode;
            fm.submit();
        }
    } else {
        alert('<?=_("주문을 선택해주세요.")?>');
    }
}

function get_param(f)
{
    if (!form_chk(f)){
        return false;
    }

    var tmp = [];
    var cnt_member = 0;
    f.range.value = (document.getElementsByName('range')[0].checked) ? 'selmember' : 'allmember';

    var c = document.getElementsByName('chk[]');
    for (var i=0;i<c.length;i++){
        if (c[i].checked) tmp[tmp.length] = c[i].value;
    }
    if (tmp[0]){
        f.mquery.value = Base64.encode("select * from exm_member where cid = '<?=$cid?>' and mid in ('" + tmp.join("','") + "')");
        cnt_member = tmp.length;
    }else{
        alert('<?=_("선택된 회원이 없습니다.")?>',-1);
        return false;
    }

}
</script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
            "sPaginationType" : "bootstrap",
            "aaSorting" : [[14, "desc"]],
            "bFilter" : false,
            "pageLength": 10,
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
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
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
            url: './order_list_page.php?postData=<?=$postData?>',
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