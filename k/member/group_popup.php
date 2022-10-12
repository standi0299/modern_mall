<?
include_once "../_pheader.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

if (!$_GET[mode]) $_GET[mode] = "addGrp";
if (is_numeric($_GET[grpno])) {
	$tableName = "exm_member_grp";
	$selectArr = "*";
	$whereArr = array("cid" => "$cid", "grpno" => "$_GET[grpno]");
	$data = SelectInfoTable($tableName, $selectArr, $whereArr);
}
$checked[base][$data[base]] = "checked";
$selected[grplv][$data[grplv]] = "selected";
//debug($data);
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("그룹관리")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" method="post" action="indb.php" name="fm" onsubmit="return form_chk(this)">
				<input type="hidden" name="mode" value="<?=$_GET[mode]?>">
				<input type="hidden" name="grpno" value="<?=$_GET[grpno]?>">
               <div>
                  <table class="table table-striped table-bordered">
                     <tbody>
                        <tr class="active">
                           	<th style="width: 30%;"><?=_("그룹명")?></th>
                           	<td>
                            	<input type="text" class="form-control" name="grpnm" value="<?=$data[grpnm]?>" style="width: 30%" required />
                        	</td>
                        </tr>
                        <tr class="active">
                           <th><?=_("할인율")?></th>
                           <td>
                           		<input type="text" class="form-control" name="grpdc" value="<?=$data[grpdc]?>" pt="_pt_numdot2" size="5" style="width: 15%" required />
                           </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("적립률")?></th>
                           <td>
                           		<input type="text" class="form-control" name="grpsc" value="<?=$data[grpsc]?>" pt="_pt_numdot2" size="5" style="width: 15%" required />
                           </td>
                        </tr>                        
                        <tr class="active">
                           <th><?=_("레벨")?></th>
                           <td>
                              	<select name="grplv" required>
								<? foreach (range(1,15) as $v){ ?>
								<option value="<?=$v?>" <?=$selected[grplv][$v]?>><?=$v?>
								<? } ?>
								</select>
							    <span id="vgrplv" class="desc" style="color:#28a5f9"><?=_("(레벨이 높을수록 권한이 높습니다)")?></span>
                           </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("기본그룹")?></th>
                           <td>
                           		<input type="checkbox" name="base"  <?=$checked[base][1]?> />
                           		<span id="vbase" class="desc" style="color:#28a5f9"><?=_("(회원가입시 기본그룹이 됩니다)")?></span>
                           </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("관리자메모")?></th>
                           <td><textarea name="adminmemo" style="width:400px;height:100px;"><?=$data[adminmemo]?></textarea></td>
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