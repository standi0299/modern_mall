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
               <a href="javascript:window.close();opener.parent.location.reload();" class="navbar-brand"><span class="navbar-logo"></span><?=_("입금 등록")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body">
            <form class="form-horizontal form-bordered" method="POST" action="indb.php" onsubmit="return submitForm(this)">
                <input type="hidden" name="mode" value="deposit_form_popup"/>
                <input type="hidden" name="no" value="<?=$_GET[no]?>"/>    
                <input type="hidden" name="mid" value="<?=$_GET[mid]?>"/>
               <div>
                  <table class="table table-striped table-bordered">
                     <tbody>
                        <tr class="active">
                            <th style="width: 30%;"><?=_("입금일자")?></th>
                            <td>
                                <div class="input-daterange">
                                    <input type="text" class="form-control" name="deposit_date" placeholder="입금일자" value="<?=$_GET[start_date]?>" style="width: 120px;">
                                </div>
                            </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("입금방법")?></th>
                           <td>
                                <select name="deposit_method" class="form-control" style="width: 200px;" required>
                                    <option value=""><?=_("선택")?>
                                    <?foreach($r_deposit_method as $k=>$v){?>
                                        <option value="<?=$k?>" <?=$selected[deposit_method][$k]?>><?=$v?></option>
                                    <?}?>
                                </select>
                           </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("(선)입금액")?></th>
                           <td><input type="text" class="form-control" name="deposit_money" value="<?=$data[deposit_money]?>" style="width: 120px;" onkeypress="onlynumber()" required></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("현금영수증발행일자")?></th>
                            <td>
                                <div class="input-daterange">
                                    <input type="text" class="form-control" name="cashreceipt_date" placeholder="현금영수증발행일자" value="<?=$data[cashreceipt_date]?>" style="width: 120px;">
                                </div>
                            </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("계산서발행일자")?></th>
                            <td>
                                <div class="input-daterange">
                                    <input type="text" class="form-control" name="taxbill_date" placeholder="계산서발행일자" value="<?=$data[taxbill_date]?>" style="width: 120px;">
                                </div>
                            </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("선발행입금액(부가세포함)")?></th>
                           <td><input type="text" class="form-control" name="pre_deposit_money" value="<?=$data[pre_deposit_money]?>" style="width: 120px;" onkeypress="onlynumber()" required></td>
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

<script>
function submitForm(formObj) {
    try {
        if (form_chk(formObj)) {
            var deposit_money = parseInt(formObj.deposit_money.value);
            var pre_deposit_money = parseInt(formObj.pre_deposit_money.value);

            if(isNaN(deposit_money)) deposit_money = 0;
            if(isNaN(pre_deposit_money)) pre_deposit_money = 0;
            
            //console.log("deposit_money : " + deposit_money);
            //console.log("pre_deposit_money : " + pre_deposit_money);

            if(pre_deposit_money<0) {
                alert('<?=_("선발행입금액은 (-)를 입력할 수 없습니다.")?>');
                formObj.pre_deposit_money.value = "";
                return false;
            }
            
            if(pre_deposit_money>=deposit_money) {
                alert('<?=_("선발행입금액은 선입금액을 초과할 수 없습니다.")?>');
                return false;
            }
            
            var cashreceipt_date = formObj.cashreceipt_date.value; //현금영수증발행일자
            var taxbill_date = formObj.taxbill_date.value; //계산서발행일자
            
            if(cashreceipt_date != "" && taxbill_date != "") {
                alert('<?=_("현금영수증발행과 계산서발행은 동시에 입력할 수 없습니다.")?>');
                formObj.cashreceipt_date.value = "";
                formObj.taxbill_date.value = "";
                return false;
            }
            
            if(taxbill_date != "") {
                if(pre_deposit_money<=0) {
                    alert('<?=_("선발행입금액이 있을 경우에만 계산서발행일자를 입력할 수 없습니다.")?>');
                    return false;
                }
            }

            return true;
        }
        else {
            return false;
        }
    } catch(e) {return false;}
}
</script>

<? include "../_footer_app_exec.php"; ?>