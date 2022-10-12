<?
/*
* @date : 20181030
* @author : kdk
* @brief : POD용 (알래스카) 입금관리 리스트.
* @request : 
* @desc :
* @todo :
*/
include_once "../_pheader.php";
$m_pod = new M_pod();

//회원미수금현황조회.
$data = $m_pod->getMemberRemainStatus($cid, $_GET[mid]);
if ($data) {
    $data[start_date] = substr($data[start_date],0,10);
    $data[promise_date] = substr($data[promise_date],0,10);
}
//debug($r_deposit_method);
//debug($sess_admin);
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("회원 미수금 관리")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body">
            <form class="form-horizontal form-bordered" method="POST" action="indb.php" onsubmit="return form_chk(this)">
                <input type="hidden" name="mode" value="remain_member_form_popup"/>
                <input type="hidden" name="mid" value="<?=$_GET[mid]?>"/>
               <div>
                  <table class="table table-striped table-bordered">
                     <tbody>
                        <tr class="active">
                           <th><?=_("미수담당자")?></th>
                           <td><input type="text" class="form-control" name="remainadmin" value="<?=$data[remainadmin]?>" style="width: 120px;" required></td>
                        </tr>                         
                        <tr class="active">
                            <th style="width: 30%;"><?=_("시작일자")?></th>
                            <td>
                                <div class="input-daterange">
                                    <input type="text" class="form-control" name="start_date" placeholder="시작일자" value="<?=$data[start_date]?>" style="width: 120px;">
                                </div>
                            </td>
                        </tr>
                        <tr class="active">
                            <th style="width: 30%;"><?=_("약속일자")?></th>
                            <td>
                                <div class="input-daterange">
                                    <input type="text" class="form-control" name="promise_date" placeholder="약속일자" value="<?=$data[promise_date]?>" style="width: 120px;">
                                </div>
                            </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("약속금액")?></th>
                           <td><input type="text" class="form-control" name="promise_money" value="<?=$data[promise_money]?>" style="width: 120px;" onkeypress="onlynumber()" required></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("비고")?></th>
                           <td><input type="text" class="form-control" name="memo" value="<?=$data[memo]?>"></td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="form-group">
                   <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-success"><?=_("전 송")?></button>
                    <button type="button" class="btn btn-sm btn-default" onclick="window.close();opener.parent.location.reload();"><?=_("닫  기")?></button>
                   </div>
               </div>
            </form>
         </div>
      </div>
      
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
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

<? include "../_footer_app_exec.php"; ?>