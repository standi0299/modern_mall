<?
/*
* @date : 20181030
* @author : kdk
* @brief : POD용 (알래스카) 회원 리스트.
* @request : 
* @desc : 기존 필드 사용 (결제방식:credit_member, 거래상태:rest_flag)
* @todo :
*/
?>
<?
include "../_header.php";
include "../_left_menu.php";

$m_pretty = new M_pretty();
$m_pod = new M_pod();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

### 회원그룹 추출
$r_grp = getMemberGrp();

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
    $addwhere .= " and manager_no like '%$_POST[manager_no]%'";
}

if(isset($_POST[grpno]) && $_POST[grpno] != "") {
    $addwhere .= " and grpno = '$_POST[grpno]'";
}

if(isset($_POST[credit_member]) && $_POST[credit_member] != "") {
    $addwhere .= " and credit_member = '$_POST[credit_member]'";
}

if(isset($_POST[rest_flag]) && $_POST[rest_flag] != "") {
    $addwhere .= " and rest_flag = '$_POST[rest_flag]'";
}

if(isset($_POST[sword]) && $_POST[sword] != "") { //아이디,회원명,사업자명,사업자/주민등록번호,일반전화,휴대전화
    $addwhere .= " and concat(mid,name,cust_name,resno,cust_no,phone,mobile) like '%$_POST[sword]%'";
}

$orderby = "order by regdt desc";
//$limit = "limit $_POST[start], $_POST[length]";
$query = $m_pretty -> getMemberListA($cid, $addwhere, $orderby, "", true);

$postData = base64_encode(json_encode($_POST));

### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($query));
$url_query = "/a/order/indb.php?query=".$_query;
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
### form 전송 취약점 개선 20160128 by kdk

$selected[manager_no][$_POST[manager_no]] = "selected";
$checked[grpno][$_POST[grpno]] = "checked";
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
         <?=_("회원관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("회원리스트")?> <small><?=_("등록된 회원들의 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("회원 리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="post" action="member_list_pod.php">
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
                         <label class="col-md-1 control-label"><?=_("회원구분")?></label>
                         <div class="col-md-2 form-inline">
                            <input type="radio" name="grpno" value="" <?=$checked[grpno][""]?> /> <?=_("전체")?>
                            <?foreach($r_grp as $k=>$v){?>
                            <input type="radio" name="grpno" value="<?=$k?>" <?=$checked[grpno][$k]?>/><?=$v?>
                            <?}?>                            
                         </div>
                         <label class="col-md-1 control-label"><?=_("결제방식")?></label>
                         <div class="col-md-2 form-inline">
                            <input type="radio" name="credit_member" value="" <?=$checked[credit_member][""]?> /> <?=_("전체")?>
                            <input type="radio" name="credit_member" value="0" <?=$checked[credit_member][0]?>/><?=_("선결제")?>
                            <input type="radio" name="credit_member" value="1" <?=$checked[credit_member][1]?>/><?=_("후결제")?>
                         </div>
                         <label class="col-md-1 control-label"><?=_("거래상태")?></label>
                         <div class="col-md-2 form-inline">
                            <input type="radio" name="rest_flag" value="" <?=$checked[rest_flag][""]?> /> <?=_("전체")?>
                            <input type="radio" name="rest_flag" value="0" <?=$checked[rest_flag][0]?>/> <?=_("승인")?>
                            <input type="radio" name="rest_flag" value="1" <?=$checked[rest_flag][1]?>/> <?=_("정지")?>                            
                         </div>
                      </div>                      
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("검색")?></label>
                         <div class="col-md-6 form-inline">
                            <input type="text" name="sword" class="form-control" value="<?=$_POST[sword]?>" size="100" placeholder='<?=_("아이디,회원명,사업자명,사업자/주민등록번호,일반전화,휴대전화를 입력해주세요.")?>'>
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
                                 <th><?=_("아이디")?></th>
                                 <th><?=_("회원명")?></th>
                                 <th><?=_("회원구분")?></th>
                                 <th><?=_("사업자명")?></th>
                                 <th><?=_("사업자번호/<br>주민번호")?></th>
                                 <th><?=_("휴대전화")?></th>
                                 <th><?=_("일반전화")?></th>
                                 <th><?=_("결제방식")?></th>
                                 <th><?=_("거래상태")?></th>
                                 <th><?=_("미수금")?></th>
                                 <th><?=_("입금")?></th>
                                 <th><?=_("영업담당자")?></th>
                                 <th><?=_("문자발송")?></th>
                                 <th><?=_("가입일")?></th>
                                 <th><?=_("주문접수")?></th>
                                 <th><?=_("수정")?></th>
                                 <th><?=_("삭제")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                     <div class="form-group">
                        <button type="button" class="btn btn-sm btn-success" onClick="location.href='member_form_pod.php';"><?=_("회원등록")?></button>
                        <!--<button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>-->
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

<!-- ### 엑셀데이타추출용쿼리문 -->
<input type="hidden" name="_query" value="<?=$_query?>&pod_signed=<?=$pod_signed?>&pod_expired=<?=$pod_expired?>">

<script>
function dnExcel() {
    var query = document.getElementsByName("_query")[0].value;
    hidden_frm.location.href = "../order/indb.php?mode=dnExcel&kind=member_pod&query=" + query;
}
</script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
			"sPaginationType" : "bootstrap",
			"aaSorting" : [[13, "desc"]],
			"bFilter" : false,
			"pageLength": 10,
			"oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
			},
			"aoColumns": [
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
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
		 "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
			"ajax": $.fn.dataTable.pipeline({
            url: './member_list_page.php?postData=<?=$postData?>',
            pages: 1 // number of pages to cache
         })
      });
   });
</script>

<script>
   function mid_delete(mid){
      if(confirm('<?=_("정말 삭제하시겠습니까?")?>') == true){
         location.href = "indb.php?mode=delMember&mid="+mid;
      }
   }
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>