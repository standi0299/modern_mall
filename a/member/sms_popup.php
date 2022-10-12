<?
include_once "../_pheader.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

$tableName = "exm_log_email";
$selectArr = "*";
$whereArr = array("cid" => "$cid", "no" => "$_GET[no]");
$data = SelectInfoTable($tableName, $selectArr, $whereArr);

$data[to] = explode(";",$data[to]);
$cnt = count($data[to])-1;

//debug($data);
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("SMS 보내기")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body">
            <form class="form-horizontal form-bordered" method="POST" action="indb.php" onsubmit="return form_chk(this)">
                <input type="hidden" name="mode" value="sendsms"/>
                <input type="hidden" name="vtype" value="to"/>    
               <div>
                  <table class="table table-striped table-bordered">
                     <tbody>
                        <tr class="active">
                           	<th style="width: 30%;"><?=_("보내는번호")?></th>
                           	<td><input type="text" class="form-control" name="from" value="<?=$cfg[smsAdmin]?>"></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("받는번호")?></th>
                           <td><input type="text" class="form-control" name="to" value="<?=$_GET[mobile]?>"></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("문자입력")?></th>
                           <td>
                                <textarea name="sms_msg[]" rows="6" class="form-control" onkeyup="chkSmsByte(this)" required></textarea><br>
                                <span id="byte">0 </span> byte
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="form-group">
                   <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-success"><?=_("전 송")?></button>
                    <button type="button" class="btn btn-sm btn-default" onclick="window.close();"><?=_("닫  기")?></button>
                   </div>
               </div>
            </form>
         </div>
      </div>
      
   </div>
</div>

<? include_once "../_pfooter.php"; ?>