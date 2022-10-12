<div id="preset_div" name="preset_div" preset="100112">

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
    <td colspan="4"><?=_("규격 & 수량")?></td>
  </tr>

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_1', $clsExtraOption->GetOptionKindUse("1"))?>
      <input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_1','1');return false;" />
<?  } ?>       
    </td>
    <th><?=$clsExtraOption->GetDisplayName("1")?>&nbsp;<a href="#" onclick="openDisplayName('1','<?=$clsExtraOption->GetDisplayName("1")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("1")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <a href="javascript:;" onclick="openAddOption('1','DOCUMENT','0','<?=$clsExtraOption->GetDisplayName("1")?>','<?=$clsExtraOption->GetExtraData1ByItem("1","DOCUMENT")?>','<?=$clsExtraOption->GetExtraData2ByItem("1","DOCUMENT")?>','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('DOCUMENT','1',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <?=MakeUseflagSelect('use_flag_2', $clsExtraOption->GetOptionKindUse("2"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("2")?>&nbsp;<a href="#" onclick="openDisplayName('2','<?=$clsExtraOption->GetDisplayName("2")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeSelectOptionTag("2")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('2','AFTEROPTION','0','<?=$clsExtraOption->GetDisplayName("2")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('AFTEROPTION','2',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>  

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <?=MakeUseflagSelect('use_flag_3', $clsExtraOption->GetOptionKindUse("3"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("3")?>&nbsp;<a href="#" onclick="openDisplayName('3','<?=$clsExtraOption->GetDisplayName("3")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeSelectOptionTag("3")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('3','AFTEROPTION','0','<?=$clsExtraOption->GetDisplayName("3")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('AFTEROPTION','3',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>

  <tr>
    <td width="105" align="center"></td>
    <th><?=_("부수")?></th>
    <td>
    	<?=$clsExtraOption->MakeUnitCntSelect($_GET[goodsno])?>
    	<div style="display: none;">
    		<span id="unit_cnt_rule_OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"OCNT","Y")?></span>
    		<span id="user_unit_cnt_rule_name_OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"OCNT","Y")?></span>
    	</div>
    	<!--수량(단위)-->
		<span name="unit_order_cnt"><?=$clsExtraOption->GetUserCntName($_GET[goodsno], "OCNT", "Y")?></span>    	
    </td>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page_cnt',this,'OCNT'); return false;"><u><?=_("설정")?></u></a>
<?  } ?>            
    </td>
  </tr>
  
  </table>
</div>

<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("표지")?></td>
  </tr>

  <tr>
    <td width="105" align="center">
    </td>
    <th>
    	<!--<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"C-OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('C-OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"C-OCNT")?>',this);return false;" /><img src='../img/bt_mod.png' class='hand absmiddle'/></a>-->
    </th>
    <td>
    	<!--
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno], "C-OCNT","order_cnt_select_C-OCNT")?>
    	-->
    	<div style="display: none;">
    		<span id="cnt_rule_C-OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"C-OCNT")?></span>
    		<span id="d_cnt_rule_C-OCNT"><?=$clsExtraOption->GetOrderDisplayCnt($_GET[goodsno],"C-OCNT")?></span>
 		    <span id="user_cnt_rule_name_C-OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"C-OCNT")?></span>
    		<span id="user_cnt_input_flag_C-OCNT"><?=$clsExtraOption->GetUserCntInputFlage($_GET[goodsno],"C-OCNT")?></span>
    	</div>    	
    	<!--표지수량(단위)-->
		<!--<span name="C-OCNT"><?=$clsExtraOption->getUserCntName($_GET[goodsno], "C-OCNT")?></span>-->
    </td>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'C-OCNT','<?=$clsExtraOption->GetOptionPriceType("C-FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>
    </td>
  </tr>

<?
  //표지 관련 옵션 처리하기. 
  //key:kind_index, value:item_index, sub_option_kind_index, sub_sub_option_kind_index
  //C-PP
	$key = "4";
    $option_arr = explode(",", "1,5,6");
    $displayData = $key.'|'.$option_arr[1].'|'.$option_arr[2];

	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse($key) == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>	
  <tr>  	
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_'.$key, $clsExtraOption->GetOptionKindUse($key))?>
<? } ?>
    </td>
    <th>
      <?=$clsExtraOption->GetDisplayName($key)?>&nbsp;<a href="#" onclick="openDisplayName('<?=$key?>','<?=$clsExtraOption->GetDisplayName($key)?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a>
    </th>    
    <td>
      <?=$clsExtraOption->MakePageSelectOptionTag($displayData)?>
    </td>
    <td width="120" align="center">
      <a href="javascript:;" onclick="openPageTree('C-FIX', '<?=$displayData?>'); return false;"><u><?=_("관리")?></u></a>
    </td>
  </tr>
<?
	}
?>  

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("7") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <?=MakeUseflagSelect('use_flag_7', $clsExtraOption->GetOptionKindUse("7"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("7")?>&nbsp;<a href="#" onclick="openDisplayName('7','<?=$clsExtraOption->GetDisplayName("7")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("7")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('7','C-SELOPTION','0','<?=$clsExtraOption->GetDisplayName("7")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('C-SELOPTION','7',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>  
<?
  }
?>

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("8") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>   
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_8', $clsExtraOption->GetOptionKindUse("8"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("8")?>&nbsp;<a href="#" onclick="openDisplayName('8','<?=$clsExtraOption->GetDisplayName("8")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("8")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('8','C-SELOPTION','0','<?=$clsExtraOption->GetDisplayName("8")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('C-SELOPTION','8',this); return false;"><u><?=_("관리")?></u></a>            
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
  <table class="addoptbox2">
  <tr>
    <td colspan="3"><?=_("표지 필수 후가공")?></td>
    <td align="center"><a href="javascript:;" onclick="$('#afteroption_div').toggle();"><img src='../img/bt_mod.png' class='hand absmiddle'/><?=_("후가공더보기")?></a></td>
  </tr>

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <?=MakeUseflagSelect('use_flag_25', $clsExtraOption->GetOptionKindUse("25"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("25")?>&nbsp;<a href="#" onclick="openDisplayName('25','<?=$clsExtraOption->GetDisplayName("25")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeSelectOptionTag("25")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('25','AFTEROPTION','0','<?=$clsExtraOption->GetDisplayName("25")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('AFTEROPTION','25',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>  

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <?=MakeUseflagSelect('use_flag_26', $clsExtraOption->GetOptionKindUse("26"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("26")?>&nbsp;<a href="#" onclick="openDisplayName('26','<?=$clsExtraOption->GetDisplayName("26")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeSelectOptionTag("26")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('26','AFTEROPTION','0','<?=$clsExtraOption->GetDisplayName("26")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('AFTEROPTION','26',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <?=MakeUseflagSelect('use_flag_27', $clsExtraOption->GetOptionKindUse("27"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("27")?>&nbsp;<a href="#" onclick="openDisplayName('27','<?=$clsExtraOption->GetDisplayName("27")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeSelectOptionTag("27")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('27','AFTEROPTION','0','<?=$clsExtraOption->GetDisplayName("27")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('AFTEROPTION','27',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>

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
  
  <div class="addoptbox_div2" id="afteroption_div" style="display: none;">
  <table class="addoptbox" style="width: 99%">
  <tr>
    <td colspan="6"><?=_("표지 선택 후가공 (고급 옵션)")?>&nbsp;&nbsp;
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <!--<input type="image" src="../img/bt_optadd_s.png" style="vertical-align: middle; width: 51px; height: 25px;" onclick="add_addoptbox();return false;" />-->
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
      <?=MakeUseflagSelect('use_flag_' .$afterKindIndex, $clsExtraOption->GetOptionKindUse($afterKindIndex))?>
<?  } ?>
    </td>
    <th><?=$afterOptionDisplayName?>&nbsp;<a href="#" onclick="openDisplayName('<?=$afterKindIndex?>','<?=$afterOptionDisplayName?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag($afterKindIndex)?></td>
    
	<!--
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],$afterOptionKindCode)?>&nbsp;<a href="#" onclick="openCntDisplayName('<?=$afterOptionKindCode?>','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],$afterOptionKindCode)?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno], $afterOptionKindCode,"order_cnt_select_".$afterOptionKindCode)?>
    	<div style="display: none;">
    		<span id="cnt_rule_<?=$afterOptionKindCode?>"><?=$clsExtraOption->getOrderCnt($_GET[goodsno],$afterOptionKindCode)?></span>
    	</div>    	
    </td>    
   -->
    
    <td width="120" align="center">
    	<div>
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('<?=$afterKindIndex?>','AFTEROPTION','0','<?=$afterOptionDisplayName?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('AFTEROPTION','<?=$afterKindIndex?>',this); return false;"><u><?=_("관리")?></u></a>
		</div>
		<div>
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <!--
      <input type="image" src="../img/bt_price_type_s.png" style="vertical-align: middle; width: 51px; height: 25px;" onclick="openAddPageOption('page-after',this,'<?=$afterOptionKindCode?>','<?=$afterItemPriceType?>');return false;" />
      -->
<?  } ?>

<?  if ($cid == $cfg_center[center_cid] || $checkRegCid) { 
  		if (strpos($afterOptionKindCode, "ETC") !== false ) { ?>
      <a href="javascript:;" onclick="afterOptionMasterRegistFlag('<?=$afterKindIndex?>',this); return false;"><u><?=_("삭제")?></u></a>
<?  	} 
	}
?>
        </div>
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
    <!--<th></th>
    <td></td>-->    
    <td width="120" align="center"></td>
  </tr>

  </table>
  </div>

<!--기타 후가공 옵션추가 //-->
<div id="addoptbox_div" style="width: 100%"></div>
<!--기타 후가공 옵션추가 //-->
  
</div>

<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("내지")?></td>
  </tr>  	
  <tr>
    <td width="105" align="center">
    </td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno],"OCNT","order_cnt_select_OCNT","false")?>
    	<div style="display: none;">
    		<span id="cnt_rule_OCNT"><?=$clsExtraOption->getOrderCnt($_GET[goodsno],"OCNT")?></span>
    		<span id="d_cnt_rule_OCNT"><?=$clsExtraOption->getOrderDisplayCnt($_GET[goodsno],"OCNT")?></span>
 		    <span id="user_cnt_rule_name_OCNT"><?=$clsExtraOption->getUserCntName($_GET[goodsno],"OCNT")?></span>
    		<span id="user_cnt_input_flag_OCNT"><?=$clsExtraOption->getUserCntInputFlage($_GET[goodsno],"OCNT")?></span>    		
    	</div>
		<!--내지수량(단위)-->
		<span name="OCNT"><?=$clsExtraOption->getUserCntName($_GET[goodsno], "OCNT")?></span>		
    </td>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'OCNT','<?=$clsExtraOption->GetOptionPriceType("FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>            
    </td>
  </tr>

<?
  //내지 관련 옵션 처리하기. 
  //key:kind_index, value:item_index, sub_option_kind_index, sub_sub_option_kind_index
  //PP
	$key = "9";
    $option_arr = explode(",", "1,10,11");
    $displayData = $key.'|'.$option_arr[1].'|'.$option_arr[2];
//debug($option_arr);
//debug($displayData);

	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse($key) == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>	
  <tr>  	
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_'.$key, $clsExtraOption->GetOptionKindUse($key))?>
<? } ?>
    </td>
    <th>
      <?=$clsExtraOption->GetDisplayName($key)?>&nbsp;<a href="#" onclick="openDisplayName('<?=$key?>','<?=$clsExtraOption->GetDisplayName($key)?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a>
    </th>    
    <td>
      <?=$clsExtraOption->MakePageSelectOptionTag($displayData)?>
    </td>
    <td width="120" align="center">
      <a href="javascript:;" onclick="openPageTree('F-FIX', '<?=$displayData?>'); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>
<?
	}
?>

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("12") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_12', $clsExtraOption->GetOptionKindUse("12"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("12")?>&nbsp;<a href="#" onclick="openDisplayName('12','<?=$clsExtraOption->GetDisplayName("12")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("12")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('12','SELOPTION','0','<?=$clsExtraOption->GetDisplayName("12")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('SELOPTION','12',this); return false;"><u><?=_("관리")?></u></a>            
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

<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("96") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>
<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("면지")?>
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_96', $clsExtraOption->GetOptionKindUse("96"))?>
<?  } ?>
    </td>
  </tr>
  
  <tr>
    <td width="105" align="center">
    </td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"M-OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('M-OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"M-OCNT")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno],"M-OCNT","order_cnt_select_M-OCNT")?>
    	<div style="display: none;">
    		<span id="cnt_rule_M-OCNT"><?=$clsExtraOption->getOrderCnt($_GET[goodsno],"M-OCNT")?></span>
    		<span id="d_cnt_rule_M-OCNT"><?=$clsExtraOption->getOrderDisplayCnt($_GET[goodsno],"M-OCNT")?></span>
 		    <span id="user_cnt_rule_name_M-OCNT"><?=$clsExtraOption->getUserCntName($_GET[goodsno],"M-OCNT")?></span>
    		<span id="user_cnt_input_flag_M-OCNT"><?=$clsExtraOption->getUserCntInputFlage($_GET[goodsno],"M-OCNT")?></span>    		
    	</div>
		<!--면지수량(단위)-->
		<span name="M-OCNT"><?=$clsExtraOption->getUserCntName($_GET[goodsno], "M-OCNT")?></span>
    </td>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'M-OCNT','<?=$clsExtraOption->GetOptionPriceType("M-FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>            
    </td>
  </tr>

<?
  //면지 관련 옵션 처리하기. 
  //key:kind_index, value:item_index, sub_option_kind_index, sub_sub_option_kind_index
  //M-PP
	$key = "13";
    $option_arr = explode(",", "1,14,15");
    $displayData = $key.'|'.$option_arr[1].'|'.$option_arr[2];
//debug($option_arr);
//debug($displayData);

	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse($key) == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>	
  <tr>  	
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_'.$key, $clsExtraOption->GetOptionKindUse($key))?>
<? } ?>
    </td>
    <th>
      <?=$clsExtraOption->GetDisplayName($key)?>&nbsp;<a href="#" onclick="openDisplayName('<?=$key?>','<?=$clsExtraOption->GetDisplayName($key)?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a>
    </th>    
    <td>
      <?=$clsExtraOption->MakePageSelectOptionTag($displayData)?>
    </td>
    <td width="120" align="center">
      <a href="javascript:;" onclick="openPageTree('M-FIX', '<?=$displayData?>'); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>
<?
	}
?>
  
<?
  if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("16") == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
  else {
?>
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_16', $clsExtraOption->GetOptionKindUse("16"))?>
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("16")?>&nbsp;<a href="#" onclick="openDisplayName('16','<?=$clsExtraOption->GetDisplayName("16")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("16")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('16','M-SELOPTION','0','<?=$clsExtraOption->GetDisplayName("16")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('M-SELOPTION','16',this); return false;"><u><?=_("관리")?></u></a>            
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
    <td colspan="4"><?=_("간지")?>
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_97', $clsExtraOption->GetOptionKindUse("97"))?>
<?  } ?>
    </td>
  </tr>
  
  <tr>
    <td width="105" align="center">
    </td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"G-OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('G-OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"G-OCNT")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
    	<?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno],"G-OCNT","order_cnt_select_G-OCNT")?>
    	<div style="display: none;">
    		<span id="cnt_rule_G-OCNT"><?=$clsExtraOption->getOrderCnt($_GET[goodsno],"G-OCNT")?></span>
    		<span id="d_cnt_rule_G-OCNT"><?=$clsExtraOption->getOrderDisplayCnt($_GET[goodsno],"G-OCNT")?></span>
 		    <span id="user_cnt_rule_name_G-OCNT"><?=$clsExtraOption->getUserCntName($_GET[goodsno],"G-OCNT")?></span>
    		<span id="user_cnt_input_flag_G-OCNT"><?=$clsExtraOption->getUserCntInputFlage($_GET[goodsno],"G-OCNT")?></span>    		
    	</div>
		<!--간지수량(단위)-->
		<span name="G-OCNT"><?=$clsExtraOption->getUserCntName($_GET[goodsno], "G-OCNT")?></span>
    </td>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'G-OCNT','<?=$clsExtraOption->GetOptionPriceType("G-FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
<?  } ?>
    </td>
  </tr>  
<?
  //간지 관련 옵션 처리하기. 
  //key:kind_index, value:item_index, sub_option_kind_index, sub_sub_option_kind_index
  //G-PP
	$key = "17";
    $option_arr = explode(",", "1,18,19");
    $displayData = $key.'|'.$option_arr[1].'|'.$option_arr[2];
//debug($option_arr);
//debug($displayData);

	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse($key) == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>	
  <tr>  	
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_'.$key, $clsExtraOption->GetOptionKindUse($key))?>
<? } ?>
    </td>
    <th>
      <?=$clsExtraOption->GetDisplayName($key)?>&nbsp;<a href="#" onclick="openDisplayName('<?=$key?>','<?=$clsExtraOption->GetDisplayName($key)?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a>
    </th>    
    <td>
      <?=$clsExtraOption->MakePageSelectOptionTag($displayData)?>
    </td>
    <td width="120" align="center">
      <a href="javascript:;" onclick="openPageTree('G-FIX', '<?=$displayData?>'); return false;"><u><?=_("관리")?></u></a>            
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
<?  } ?>
    </td>
    <th><?=$clsExtraOption->GetDisplayName("20")?>&nbsp;<a href="#" onclick="openDisplayName('20','<?=$clsExtraOption->GetDisplayName("20")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("20")?>
    </td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
		<a href="javascript:;" onclick="openAddOption('20','G-SELOPTION','0','<?=$clsExtraOption->GetDisplayName("20")?>','','','',this); return false;"><u><?=_("추가")?></u></a>      
<?//  } ?>
      <a href="javascript:;" onclick="openEdiOption('G-SELOPTION','20',this); return false;"><u><?=_("관리")?></u></a>
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
<?
  }
?>


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

<?
//옵션,항목 추가
include "extra_option_preset_add_item.php";
?>

</div>
