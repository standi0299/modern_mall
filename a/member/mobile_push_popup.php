<?
include_once "../_pheader.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

if (!$_GET[mode]) $_GET[mode] = "addPush";

if (is_numeric($_GET[pushno])) {
	$tableName = "exm_mobile_push";
	$selectArr = "*";
	$whereArr = array("cid" => "$cid", "pushno" => "$_GET[pushno]");
	$data = SelectInfoTable($tableName, $selectArr, $whereArr);
}
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("모바일푸쉬알림관리")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" method="post" action="indb.php" name="fm" onsubmit="return form_chk(this)">
			   <input type="hidden" name="mode" value="<?=$_GET[mode]?>">
			   <input type="hidden" name="pushno" value="<?=$_GET[pushno]?>">
               <div>
                  <table class="table table-striped table-bordered">
                     <tbody>
                        <tr class="active">
                           	<th style="width: 10%;"><?=_("제목")?></th>
                           	<td>
                            	<input type="text" class="form-control" name="push_title" value="<?=$data[push_title]?>" style="width: 92.7%" />
                        	</td>
                        </tr>
                        <tr class="active">
                           	<th style="width: 10%;"><?=_("내용")?></th>
                           	<td>
                            	<input type="text" class="form-control" name="push_message" value="<?=$data[push_message]?>" style="width: 92.7%" />
                        	</td>
                        </tr>
                        
                        <tr>
                           <td style="text-align: center;" colspan="2">
                              <button type="submit" class="btn btn-sm btn-success"><?=_("확인")?></button>
                              <button type="button" class="btn btn-sm btn-warning" onclick="window.close();"><?=_("취소")?></button>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<? include_once "../_pfooter.php"; ?>