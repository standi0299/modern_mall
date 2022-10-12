<div id="preset_div" name="preset_div" preset="100104">


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
    <th></th>
    <td>
    	<?=$clsExtraOption->MakeUnitCntSelect($_GET[goodsno])?>
    	<div style="display: none;">
    		<span id="unit_cnt_rule_OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"OCNT","Y")?></span>
    		<span id="user_unit_cnt_rule_name_OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"OCNT","Y")?></span>
    	</div>
    	<!--수량(단위)-->
		<span name="unit_order_cnt"><?=$clsExtraOption->GetUserCntName($_GET[goodsno], "OCNT", "Y")?></span>
    </td>
    <td width="150" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page_cnt',this,'OCNT'); return false;"><u><?=_("수량설정")?></u></a>
<?  } ?>            
    </td>
  </tr>
  
  </table>
</div>

<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("표지 옵션")?></td>
  </tr>

<?
  //표지 관련 옵션 처리하기. 
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

  <tr>
    <td width="105" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
    	<input type="image" class="btnBatchSaveDisplayFlag" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="return false;" />
<? } ?>
    </td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"C-OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('C-OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"C-OCNT")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno], "C-OCNT","order_cnt_select_C-OCNT")?>
    	<!--<?=$clsExtraOption->GetOptionPriceType("C-FIXOPTION");?>-->
    	<div style="display: none;">
    		<span id="cnt_rule_C-OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"C-OCNT")?></span>    		
    		<span id="d_cnt_rule_C-OCNT"><?=$clsExtraOption->GetOrderDisplayCnt($_GET[goodsno],"C-OCNT")?></span>
    		<span id="user_cnt_rule_name_C-OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"C-OCNT")?></span>
    		<span id="user_cnt_input_flag_C-OCNT"><?=$clsExtraOption->GetUserCntInputFlage($_GET[goodsno],"C-OCNT")?></span>    		
    	</div>
    	<!--표지수량(단위)-->
		<span name="C-OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno], "C-OCNT")?></span>    	
    </td>
    <td width="150" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'C-OCNT','<?=$clsExtraOption->GetOptionPriceType("C-FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>
    </td>
  </tr>  
  </table>
</div>

<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("내지 옵션")?></td>
  </tr>

<?
  //내지 관련 옵션 처리하기. 
  //key:kind_index, value:item_index, sub_option_kind_index, sub_sub_option_kind_index
  //pp
  $FIXOPTION_Data = $arrayName = array("19" => "1,24,29", "20"=>"2,25,30", "21"=>"3,26,31", "22"=>"4,27,32", "23"=>"5,28,33");

  $rowspan = 5;
  if ($cid != $cfg_center[center_cid] && !$checkRegCid) { //몰에서 옵션 사용여부에 따라 출력 처리함.
  	$rowspan = 0;
  	foreach ($FIXOPTION_Data as $key => $value) {
	  	if($clsExtraOption->GetOptionKindUse($key) == "Y")
	  	$rowspan++;
  	}
  }
  
  foreach ($FIXOPTION_Data as $key => $value) {
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
<?  } ?>       
    </td>

<?  if ($key == "19")  { ?>
    <th rowspan="<?=$rowspan?>">
      <?=$clsExtraOption->GetDisplayName($key)?>&nbsp;<a href="#" onclick="openDisplayName('19|20|21|22|23','<?=$clsExtraOption->GetDisplayName($key)?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a>
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

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("34") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_34', $clsExtraOption->GetOptionKindUse("34"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_34','34');return false;" />--> 
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("34")?>&nbsp;<a href="#" onclick="openDisplayName('34','<?=$clsExtraOption->GetDisplayName("34")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("34")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('34','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("34")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','34',this); return false;"><u><?=_("관리")?></u></a>     
    </td>
  </tr>  
<?
  }
?>
<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("35") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_35', $clsExtraOption->GetOptionKindUse("35"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_35','35');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("35")?>&nbsp;<a href="#" onclick="openDisplayName('35','<?=$clsExtraOption->GetDisplayName("35")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("35")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('35','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("35")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','35',this); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>
<?
  }
?>

  <tr>
    <td width="105" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
    	<input type="image" class="btnBatchSaveDisplayFlag" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="return false;" />
<? } ?>
    </td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno],"OCNT","order_cnt_select_OCNT","false")?>
    	<!--<?=$clsExtraOption->GetOptionPriceType("FIXOPTION");?>-->
    	<div style="display: none;">
    		<span id="cnt_rule_OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"OCNT")?></span>
    		<span id="d_cnt_rule_OCNT"><?=$clsExtraOption->GetOrderDisplayCnt($_GET[goodsno],"OCNT")?></span>
    		<span id="user_cnt_rule_name_OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"OCNT")?></span>
    		<span id="user_cnt_input_flag_OCNT"><?=$clsExtraOption->GetUserCntInputFlage($_GET[goodsno],"OCNT")?></span>    		
    	</div>
		<!--내지수량(단위)-->
		<span name="OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno], "OCNT")?></span>
    </td>
    <td width="150" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'OCNT','<?=$clsExtraOption->GetOptionPriceType("FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>            
    </td>
  </tr>
  </table>
</div>

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("96") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>
<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("면지 옵션")?>
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_96', $clsExtraOption->GetOptionKindUse("96"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_96','96');return false;" />-->        
<?  } ?>
    </td>
  </tr>

<?
  //면지 관련 옵션 처리하기. 
  //key:kind_index, value:item_index, sub_option_kind_index, sub_sub_option_kind_index
  //M-PP
  $M_FIXOPTION_Data = $arrayName = array("36" => "1,41,46", "37"=>"2,42,47", "38"=>"3,43,48", "39"=>"4,44,49", "40"=>"5,45,50");  

  $rowspan = 5;
  if ($cid != $cfg_center[center_cid] && !$checkRegCid) { //몰에서 옵션 사용여부에 따라 출력 처리함.
  	$rowspan = 0;
  	foreach ($M_FIXOPTION_Data as $key => $value) {
	  	if($clsExtraOption->GetOptionKindUse($key) == "Y")
	  	$rowspan++;
  	}
  }
  
  foreach ($M_FIXOPTION_Data as $key => $value) {
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
<?  } ?>
    </td>

<?  if ($key == "36")  { ?>
    <th rowspan="<?=$rowspan?>">
      <?=$clsExtraOption->GetDisplayName($key)?>&nbsp;<a href="#" onclick="openDisplayName('36|37|38|39|40','<?=$clsExtraOption->GetDisplayName($key)?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a>
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

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("51") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_51', $clsExtraOption->GetOptionKindUse("51"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_51','51');return false;" />--> 
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("51")?>&nbsp;<a href="#" onclick="openDisplayName('51','<?=$clsExtraOption->GetDisplayName("51")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("51")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('51','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("51")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','51',this); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>
<?
  }
?>
<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("52") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>    
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_52', $clsExtraOption->GetOptionKindUse("52"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_52','52');return false;" />--> 
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("52")?>&nbsp;<a href="#" onclick="openDisplayName('52','<?=$clsExtraOption->GetDisplayName("52")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("52")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('52','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("52")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','52',this); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>  
<?
  }
?>

  <tr>
    <td width="105" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
    	<input type="image" class="btnBatchSaveDisplayFlag" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="return false;" />
<? } ?>
    </td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"M-OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('M-OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"M-OCNT")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno],"M-OCNT","order_cnt_select_M-OCNT")?>
    	<!--<?=$clsExtraOption->GetOptionPriceType("M-FIXOPTION");?>-->
    	<div style="display: none;">
    		<span id="cnt_rule_M-OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"M-OCNT")?></span>
    		<span id="d_cnt_rule_M-OCNT"><?=$clsExtraOption->GetOrderDisplayCnt($_GET[goodsno],"M-OCNT")?></span>
    		<span id="user_cnt_rule_name_M-OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"M-OCNT")?></span>
    		<span id="user_cnt_input_flag_M-OCNT"><?=$clsExtraOption->GetUserCntInputFlage($_GET[goodsno],"M-OCNT")?></span>    		
    	</div>
		<!--면지수량(단위)-->
		<span name="M-OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno], "M-OCNT")?></span>
    </td>
    <td width="150" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'M-OCNT','<?=$clsExtraOption->GetOptionPriceType("M-FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>            
    </td>
  </tr>
  
  </table>
</div>
<?
  }
?>

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("97") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>
<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("간지 옵션")?>
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_97', $clsExtraOption->GetOptionKindUse("97"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_97','97');return false;" />-->        
<?  } ?>
    </td>
  </tr>

<?
  //간지 관련 옵션 처리하기. 
  //key:kind_index, value:item_index, sub_option_kind_index, sub_sub_option_kind_index
  //G-PP
  $G_FIXOPTION_Data = $arrayName = array("53" => "1,58,63", "54"=>"2,59,64", "55"=>"3,60,65", "56"=>"4,61,66", "57"=>"5,62,67");

  $rowspan = 5;
  if ($cid != $cfg_center[center_cid] && !$checkRegCid) { //몰에서 옵션 사용여부에 따라 출력 처리함.
  	$rowspan = 0;
  	foreach ($G_FIXOPTION_Data as $key => $value) {
	  	if($clsExtraOption->GetOptionKindUse($key) == "Y")
	  	$rowspan++;
  	}
  }
  
  foreach ($G_FIXOPTION_Data as $key => $value) {
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
<?  } ?>
    </td>

<?  if ($key == "53")  { ?>
    <th rowspan="<?=$rowspan?>">
      <?=$clsExtraOption->GetDisplayName($key)?>&nbsp;<a href="#" onclick="openDisplayName('53|54|55|56|57','<?=$clsExtraOption->GetDisplayName($key)?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a>
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

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("68") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>    
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_68', $clsExtraOption->GetOptionKindUse("68"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_68','68');return false;" />--> 
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("68")?>&nbsp;<a href="#" onclick="openDisplayName('68','<?=$clsExtraOption->GetDisplayName("68")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("68")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('68','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("68")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','68',this); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>  
<?
  }
?>
<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("69") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>    
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_69', $clsExtraOption->GetOptionKindUse("69"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_69','69');return false;" />--> 
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("69")?>&nbsp;<a href="#" onclick="openDisplayName('69','<?=$clsExtraOption->GetDisplayName("69")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("69")?>
    </td>
    <td width="150" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('69','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("69")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','69',this); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>
<?
  }
?> 

  <tr>
    <td width="105" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
    	<input type="image" class="btnBatchSaveDisplayFlag" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="return false;" />
<? } ?>
    </td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"G-OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('G-OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"G-OCNT")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno],"G-OCNT","order_cnt_select_G-OCNT")?>
    	<!--<?=$clsExtraOption->GetOptionPriceType("G-FIXOPTION");?>-->
    	<div style="display: none;">
    		<span id="cnt_rule_G-OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"G-OCNT")?></span>
    		<span id="d_cnt_rule_G-OCNT"><?=$clsExtraOption->GetOrderDisplayCnt($_GET[goodsno],"G-OCNT")?></span>
    		<span id="user_cnt_rule_name_G-OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"G-OCNT")?></span>
    		<span id="user_cnt_input_flag_G-OCNT"><?=$clsExtraOption->GetUserCntInputFlage($_GET[goodsno],"G-OCNT")?></span>    		
    	</div>
		<!--간지수량(단위)-->
		<span name="G-OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno], "G-OCNT")?></span>
    </td>
    <td width="150" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'G-OCNT','<?=$clsExtraOption->GetOptionPriceType("G-FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>
    </td>
  </tr>
   
  </table>
</div>
<?
  }
?>

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
