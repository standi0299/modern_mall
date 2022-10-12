<?
/*
* @date : 20181114
* @author : kdk
* @brief : POD용 (알래스카) 송장정보수정 폼.
* @request : 
* @desc :
* @todo :
*/
include_once "../_pheader.php";
$m_pod = new M_pod();
$m_order = new M_order();

### 배송업체정보추출
$r_shipcomp = get_shipcomp();

$data = $m_order->getOrdItemInfo($_GET[payno], $_GET[ordno], $_GET[ordseq]);

$selected[shipcomp][$data[shipcomp]] = "selected";
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
               <a href="javascript:window.close();opener.parent.location.reload();" class="navbar-brand"><span class="navbar-logo"></span><?=_("송장정보 수정")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body">
            <form class="form-horizontal form-bordered" method="POST" action="indb.php">
                <input type="hidden" name="mode" value="modify_shipcode"/>
                <input type="hidden" name="payno" value="<?=$_GET[payno]?>"/>
                <input type="hidden" name="ordno" value="<?=$_GET[ordno]?>"/>
                <input type="hidden" name="ordseq" value="<?=$_GET[ordseq]?>"/>
                
               <div>
                  <table class="table table-striped table-bordered">
                     <tbody>
                        <tr class="active">
                           <th><?=_("배송업체")?></th>
                           <td>
                                <select name="shipcomp" class="form-control">
                                    <option value=""><?=_("선택")?></option>
                                    <? foreach ($r_shipcomp as $k=>$v) { ?>
                                        <option value="<?=$k?>" <?=$selected[shipcomp][$k]?>><?=$v[compnm]?></option>
                                    <? } ?> 
                                </select>
                           </td>
                        </tr>
                        <tr class="active">
                            <th style="width: 30%;"><?=_("운송장번호")?></th>
                            <td>
                                <input type="text" class="form-control" name="shipcode" placeholder="운송장번호" value="<?=$data[shipcode]?>" style="width: 120px;">
                            </td>
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