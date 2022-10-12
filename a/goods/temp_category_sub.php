<?
include "../_pheader.php";

$m_goods = new M_goods();
$r_addtion_goods_kind = array("hot");

if ($_GET[catno]) $data = $db->fetch("select * from md_template_category where cid = '$cid' and catno = '$_GET[catno]'");
else $data[catnm] = _("전체분류");

if (!is_numeric($data[cells])) {
   $data[cells] = $cfg[cells];
   $data[rows] = $cfg[rows];
}

list($data[cnt]) = $db->fetch("select count(*) from md_template_category_link where cid = '$cid' and catno like '$data[catno]%'",1);
?>

<style type="text/css">
<!--
body {background-color:transparent}
-->
</style>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<? if ($data[catno]) { ?>
<form name="fm" method="post" action="indb.php" target="hiddenIfrm" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="temp_category_mod">
<input type="hidden" name="catno" value="<?=$data[catno]?>">

<? } ?>

<table class="table table-bordered" bgcolor="red">
   <colgroup>
      <col width="200" />
      <col />
   </colgroup>
   
   <? if ($_GET[catno]) { ?>
   <tr>
      <th><?=_("카테고리번호")?></th>
      <td><b><?=$data[catno]?></b></td>
   </tr>
   <? } ?>
  
   <tr>
      <th><?=_("카테고리명")?></th>
      <td>
      <? if ($data[catno]) { ?>
      <input type="text" class="form-control" name="catnm" value="<?=$data[catnm]?>" required/>
      <? } else { ?>
      <b><?=$data[catnm]?></b>
      <? } ?>
      </td>
   </tr>

   <? if ($data[catno]) { ?>
   <tr>
      <th><?=_("매치상품")?></th>
      <td><b><?=number_format($data[cnt])?></b><?=_("개의 상품이 매칭되어 있습니다.")?></td>
   </tr>
   <? } ?>
  
</table>

<? if ($data[catno]) { ?>
<div class="btn">
<button type="submit" class="btn btn-warning"><?=_("확인")?></button>
</div>

</form>
<? } ?>

<? include "../_pfooter.php"; ?>