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
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("보낸이메일")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered">
               <div>
                  <table class="table table-striped table-bordered">
                     <tbody>
                        <tr class="active">
                           	<th style="width: 30%;"><?=_("전송일시")?></th>
                           	<td><?=$data[regdt]?></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("전송건수")?></th>
                           <td><?=$data[cnt]?></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("받는사람")?></th>
                           <td><?=$data[to][0]?> <?if($cnt>0){?>외 <?=$cnt?>명<?}?></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("제목")?></th>
                           <td><?=$data[subject]?></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("내용")?></th>
                           <td><?=$data[contents]?></td>
                        </tr>                        
                        <tr>
                           <td style="text-align: center;" colspan="2">
                              <button type="submit" class="btn btn-sm btn-success" onclick="window.close();"><?=_("확인")?></button>
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