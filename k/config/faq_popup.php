<?
include_once "../_pheader.php";

$m_pretty = new M_pretty();

$query = "select catnm from exm_faq where cid = '$cid' group by catnm";
$res = $db->query($query);
while ($data = $db->fetch($res)){
   $r_catnm[] = $data[catnm];
}

if ($_GET[no]){
   $addQuery = "and no = '$_GET[no]'";
   $data = $m_pretty->getFAQ($cid, $addQuery);
   $data = $data[0];
}

?>

<style>
#ord_box {margin-left:5px;margin-right:5px}
</style>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("FAQ")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" method="post" action="indb.php">
               <input type="hidden" name="mode" value="faq"/>
               <input type="hidden" name="no" value="<?=$_GET[no]?>"/>

               <div id="ord_box">
                  <table class="table table-striped table-bordered">
                     <tbody>

                        <tr class="active">
                           <th><?=_("분류")?></th>
                           <td>
                           <select onchange="$j([name=catnm]).val($j(this).val())">
                           <option value=""><?=_("직접입력")?>
                           <? foreach ($r_catnm as $v){ ?>
                           <option value="<?=$v?>"> <?=$v?>
                           <? } ?>
                           </select>
                           <input type="text" name="catnm" value="<?=$data[catnm]?>" required/>
                           </td>
                        </tr>
                        <tr class="active">
                           <th><?=_("질문")?></th>
                           <td><input type="text" name="q" value="<?=$data[q]?>" style="width: 100%" required/></td>
                        </tr>
                        <tr class="active">
                           <th><?=_("답변")?></th>
                           <td><textarea name="a" required rows="15" style="width: 100%"><?=$data[a]?></textarea></td>
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