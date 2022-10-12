<div id="preset_div" name="preset_div" preset="100106">


<?
	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("98") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>
<div class="addoptbox_div2">  
  <table class="addoptbox2" style="width: 99%">
  <tr>
	<?if($cid == $cfg_center[center_cid] || $checkRegCid) {?>	
    <td colspan="4"><?=_("주문 제목 사용 여부")?> : &nbsp;&nbsp; <?=MakeUseflagSelect('use_flag_98', $clsExtraOption->GetOptionKindUse("98"))?>
      <input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_98','98');return false;" />
    </td>
	<?} else {?>  	
	<td colspan="4"><?=_("주문 제목 사용 여부")?> : &nbsp;&nbsp;<?=MakeUseflag($clsExtraOption->GetOptionKindUse("98"))?></td>
    <?}?>
  </tr>
  </table>
</div>
<?
	}
?>

<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("규격 & 수량 옵션")?></td>
  </tr>

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_1', $clsExtraOption->GetOptionKindUse("1"))?>
      <input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_1','1');return false;" />
<?  } ?>       
    </td>
    <th><?=$clsExtraOption->GetDisplayName("1")?>&nbsp;<a href="#" onclick="openDisplayName('1','<?=$clsExtraOption->GetDisplayName("1")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("1")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <a href="javascript:;" onclick="openAddOption('1','DOCUMENT','0','<?=$clsExtraOption->GetDisplayName("1")?>','<?=$clsExtraOption->GetExtraData1("1")?>','<?=$clsExtraOption->GetExtraData2("1")?>','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('DOCUMENT','1',this); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>


  <tr>
    <td width="105" align="center">
    </td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno],"OCNT","order_cnt_select_OCNT","false")?>
    	<!--<?=$clsExtraOption->GetOptionPriceType("FIXOPTION");?>-->
    	<div style="display: none;">
    		<span id="cnt_rule_OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"OCNT")?></span>
    	</div>
    	x
    	<?=$clsExtraOption->MakeUnitCntSelect($_GET[goodsno])?>
    	<div style="display: none;">
    		<span id="unit_cnt_rule_OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"OCNT","Y")?></span>
    		<span id="user_cnt_rule_name_OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno])?></span>
    		<span id="user_unit_cnt_rule_name_OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"OCNT","Y")?></span>
    		<span id="user_cnt_input_flag_OCNT"><?=$clsExtraOption->GetUserCntInputFlage($_GET[goodsno])?></span>    		
    	</div>
    	<!--수량(단위)-->
		<span name="unit_order_cnt"><?=$clsExtraOption->GetUserCntName($_GET[goodsno], "OCNT", "Y")?></span>
    </td>
    <td width="150" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'OCNT','<?=$clsExtraOption->GetOptionPriceType("FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
	  <a href="javascript:;" onclick="openAddPageOption('page_cnt',this,'OCNT'); return false;"><u><?=_("수량설정")?></u></a>
<?  } ?>            
    </td>
  </tr>
  
  </table>
</div>

<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("종이 옵션")?></td>
  </tr>

<?
  //종이 관련 옵션 처리하기. 
  //key:kind_index, value:item_index, sub_option_kind_index, sub_sub_option_kind_index
  //C-PP
  $C_FIXOPTION_Data = $arrayName = array("2" => "1,7,12", "3"=>"2,8,13", "4"=>"3,9,14", "5"=>"4,10,15", "6"=>"5,11,16");
  
  $rowspan = 5;  
  if ($cid != $cfg_center[center_cid] && !$checkRegCid) { //몰에서 옵션 사용여부에 따라 출력 처리함.
  	$rowspan = 0;
  	foreach ($C_FIXOPTION_Data as $key => $value) {
	  	if($clsExtraOption->GetOptionKindUse($key) == "Y")
	  	$rowspan++;
  	}
  }
  
  foreach ($C_FIXOPTION_Data as $key => $value) {
    $option_arr = explode(",", $value);
    $displayData = $key.'|'.$option_arr[1].'|'.$option_arr[2];

	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse($key) == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>
	
  <tr>  	
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_'.$key, $clsExtraOption->GetOptionKindUse($key))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_<?=$key?>','<?=$displayData?>');return false;" />-->
<? } ?>
    </td>

<?  if ($key == "2")  { ?>
    <th rowspan="<?=$rowspan?>">
      <?=$clsExtraOption->GetDisplayName($key)?>&nbsp;<a href="#" onclick="openDisplayName('2|3|4|5|6','<?=$clsExtraOption->GetDisplayName($key)?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a>
    </th>
<?  } ?>
    
    <td>&nbsp;&nbsp;
      <b><?=$clsExtraOption->GetOptionItemValue($key, $option_arr[0])?>&nbsp;<a href="#" onclick="openItemNameUpdate('<?=$key?>','<?=$option_arr[0]?>','<?=$option_arr[1]?>','<?=$clsExtraOption->GetOptionItemValue($key, $option_arr[0])?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></b>
      &nbsp;&nbsp;&nbsp;<?=$clsExtraOption->MakeSelectOptionTag($option_arr[1], $clsExtraOption->GetOptionItemValue($key, $option_arr[0]))?> <?=$clsExtraOption->MakeChildSelectOptionTag($option_arr[2], $option_arr[1], "")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('<?=$option_arr[1]?>','FIXOPTION','1','<?=$clsExtraOption->GetDisplayName($option_arr[1])?>','','','<?=$option_arr[2]?>',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','<?=$option_arr[1]?>',this); return false;"><u><?=_("관리")?></u></a>  
    </td>
    
  </tr>

<?
	}
  }
?>  

  <tr class="addoptbox_div">
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
    	<input type="image" class="btnBatchSaveDisplayFlag" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="return false;" />
<? } ?>
    </td>
    <th></th>
    <td></td>
    <td></td>
  </tr>
 
  </table>
</div>

<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("인쇄 옵션")?></td>
  </tr>

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("17") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <?=MakeUseflagSelect('use_flag_17', $clsExtraOption->GetOptionKindUse("17"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_17','17');return false;" />--> 
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("17")?>&nbsp;<a href="#" onclick="openDisplayName('17','<?=$clsExtraOption->GetDisplayName("17")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("17")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('17','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("17")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','17',this); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>  
<?
  }
?>

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("18") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>   
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_18', $clsExtraOption->GetOptionKindUse("18"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_18','18');return false;" />-->
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("18")?>&nbsp;<a href="#" onclick="openDisplayName('18','<?=$clsExtraOption->GetDisplayName("18")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("18")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('18','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("18")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','18',this); return false;"><u><?=_("관리")?></u></a>     
    </td>
  </tr>
<?
  }
?>  

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("19") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_19', $clsExtraOption->GetOptionKindUse("19"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_19','19');return false;" />--> 
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("19")?>&nbsp;<a href="#" onclick="openDisplayName('19','<?=$clsExtraOption->GetDisplayName("19")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("19")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('19','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("19")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','19',this); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>  
<?
  }
?>
<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("20") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_20', $clsExtraOption->GetOptionKindUse("20"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_20','20');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("20")?>&nbsp;<a href="#" onclick="openDisplayName('20','<?=$clsExtraOption->GetDisplayName("20")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("20")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('20','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("20")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','20',this); return false;"><u><?=_("관리")?></u></a>        
    </td>
  </tr>
<?
  }
?>

  <tr class="addoptbox_div">
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
    	<input type="image" class="btnBatchSaveDisplayFlag" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="return false;" />
<? } ?>
    </td>
    <th></th>
    <td></td>
    <td></td>
  </tr>

  </table>
</div>


<div id="addoptbox_div2">
  
  <div class="addoptbox_div2">
  <table class="addoptbox" style="width: 99%">
  <tr>
    <td colspan="6"><?=_("후가공 옵션")?>&nbsp;&nbsp;
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <input type="image" src="../img/bt_optadd_s.png" style="vertical-align: middle; width: 51px; height: 25px;" onclick="add_addoptbox();return false;" />
<?  } ?>      
      </td>
  </tr>

<?
foreach ($afterOptionKindIndex as $afterKindIndex) 
{
  $afterOptionDisplayName = $clsExtraOption->GetDisplayName($afterKindIndex); 
  $afterOptionKindCode = $clsExtraOption->GetOptionKindCode($afterKindIndex);
  $afterItemPriceType = $clsExtraOption->GetAfterOptionPriceType($afterOptionKindCode);

	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse($afterKindIndex) == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {  
?>  
  <tr class="addoptbox_div">
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
	<!--	    	
    <?=MakeUseflagSelect('use_flag_' .$afterKindIndex, $clsExtraOption->GetOptionKindUse($afterKindIndex))?>
    <?=MakePriceTypeSelect('price_type_' .$afterKindIndex, $clsExtraOption->getOptionPriceType($afterKindIndex))?>
    <input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlagNitemPriceType('use_flag_<?=$afterKindIndex?>','price_type_<?=$afterKindIndex?>','<?=$afterKindIndex?>');return false;" />
    -->
      <?=MakeUseflagSelect('use_flag_' .$afterKindIndex, $clsExtraOption->GetOptionKindUse($afterKindIndex))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_<?=$afterKindIndex?>','<?=$afterKindIndex?>');return false;" />-->
<?  } ?>
    </td>
    <th><?=$afterOptionDisplayName?>&nbsp;<a href="#" onclick="openDisplayName('<?=$afterKindIndex?>','<?=$afterOptionDisplayName?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag($afterKindIndex)?></td>
    

    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],$afterOptionKindCode)?>&nbsp;<a href="#" onclick="openCntDisplayName('<?=$afterOptionKindCode?>','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],$afterOptionKindCode)?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno], $afterOptionKindCode,"order_cnt_select_".$afterOptionKindCode)?>
    	<!--<?=$afterItemPriceType?>-->
    	<div style="display: none;">
    		<span id="cnt_rule_<?=$afterOptionKindCode?>"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],$afterOptionKindCode)?></span>
    	</div>    	
    </td>    
    
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('<?=$afterKindIndex?>','AFTEROPTION','0','<?=$afterOptionDisplayName?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('AFTEROPTION','<?=$afterKindIndex?>',this); return false;"><u><?=_("관리")?></u></a>

<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page-after',this,'<?=$afterOptionKindCode?>','<?=$afterItemPriceType?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>

<?  if ($cid == $cfg_center[center_cid] || $checkRegCid) { 
  		if (strpos($afterOptionKindCode, "ETC") !== false ) { ?>
      		<a href="javascript:;" onclick="afterOptionMasterRegistFlag('<?=$afterKindIndex?>',this); return false;"><u><?=_("삭제")?></u></a>
<?  	} 
	}
?>
    </td>
  </tr>
<?
	}
  }
?>

  <tr class="addoptbox_div">
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
    	<input type="image" class="btnBatchSaveDisplayFlag" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="return false;" />
<? } ?>
    </td>
    <th></th>
    <td></td>
    <th></th>
    <td></td>    
    <td width="150" align="center"></td>
  </tr>

  </table>
  </div>

<!--기타 후가공 옵션추가 //-->
<div id="addoptbox_div" style="width: 100%"></div>
<!--기타 후가공 옵션추가 //-->

<?
	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("99") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>
<div class="addoptbox_div2">  
  <table class="addoptbox2" style="width: 99%">
  <tr>
	<?if($cid == $cfg_center[center_cid] || $checkRegCid) {?>		
    <td colspan="4"><?=_("주문 메모 사용 여부")?> : &nbsp;&nbsp; <?=MakeUseflagSelect('use_flag_99', $clsExtraOption->GetOptionKindUse("99"))?>
      <input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_99','99');return false;" />
    </td>	
	<?} else {?>    
	<td colspan="4"><?=_("주문 메모 사용 여부")?> : &nbsp;&nbsp;<?=MakeUseflag($clsExtraOption->GetOptionKindUse("99"))?></td>
    <?}?>
  </tr>
  </table>
</div>
<?
	}
?>
  
</div>

<?
//옵션,항목 추가
include "extra_option_preset_add_item.php";
?>

</div>
