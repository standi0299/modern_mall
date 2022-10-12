<?
//낱장 견적 기본 프리셋 (100114) 2017.10.17 by kdk
?>
<div id="preset_div" name="preset_div" preset="100114">


<?
	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("98") == "N" || $extra_stu_order == "UPL") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>
<!--<div class="addoptbox_div2">  
  <table class="addoptbox2" style="width: 99%">
  <tr>
    <td colspan="4">주문 제목 사용 여부 : &nbsp;&nbsp; <?=MakeUseflagSelect('use_flag_98', $clsExtraOption->GetOptionKindUse("98"))?>
      <input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_98','98');return false;" />
      </td>
  </tr>
  </table>
</div>-->
<?
	}
?>


<div class="addoptbox_div2">
  <table class="addoptbox2">
  <tr>
    <td colspan="4"><?=_("기본 옵션")?></td>
  </tr>

  <!--<tr>
    <td width="120" align="center">필수</td>
    <th><?=$clsExtraOption->GetDisplayName("11")?>&nbsp;<a href="#" onclick="openDisplayName('11','<?=$clsExtraOption->GetDisplayName("11")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("11","","documentOnchange")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
      <a href="javascript:;" onclick="openAddOption('11','DOCUMENT','0','<?=$clsExtraOption->GetDisplayName("11")?>','<?=$clsExtraOption->GetExtraData1("11")?>','<?=$clsExtraOption->GetExtraData2("11")?>','',this); return false;"><u>추가</u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('DOCUMENT','11',this); return false;"><u>관리</u></a>      
    </td>
  </tr>-->     

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>        
      <?=MakeUseflagSelect('use_flag_1', $clsExtraOption->GetOptionKindUse("1"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_1','1');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("1")?>&nbsp;<a href="#" onclick="openDisplayName('1','<?=$clsExtraOption->GetDisplayName("1")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("1")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('1','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("1")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','1',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>
  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>        
      <?=MakeUseflagSelect('use_flag_2', $clsExtraOption->GetOptionKindUse("2"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_2','2');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("2")?>&nbsp;<a href="#" onclick="openDisplayName('2','<?=$clsExtraOption->GetDisplayName("2")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("2")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('2','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("2")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','2',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>
  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>        
      <?=MakeUseflagSelect('use_flag_3', $clsExtraOption->GetOptionKindUse("3"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 23px; height: 13px;" onclick="saveDisplayFlag('use_flag_3','3');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("3")?>&nbsp;<a href="#" onclick="openDisplayName('3','<?=$clsExtraOption->GetDisplayName("3")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("3")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('3','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("3")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','3',this); return false;"><u><?=_("관리")?></u></a>       
    </td>
  </tr>
  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>        
      <?=MakeUseflagSelect('use_flag_4', $clsExtraOption->GetOptionKindUse("4"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_4','4');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("4")?>&nbsp;<a href="#" onclick="openDisplayName('4','<?=$clsExtraOption->GetDisplayName("4")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("4")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('4','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("4")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','4',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>        
      <?=MakeUseflagSelect('use_flag_5', $clsExtraOption->GetOptionKindUse("5"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_5','5');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("5")?>&nbsp;<a href="#" onclick="openDisplayName('5','<?=$clsExtraOption->GetDisplayName("5")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("5")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('5','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("5")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','5',this); return false;"><u><?=_("관리")?></u></a>         
    </td>
  </tr>
  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_6', $clsExtraOption->GetOptionKindUse("6"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_6','6');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("6")?>&nbsp;<a href="#" onclick="openDisplayName('6','<?=$clsExtraOption->GetDisplayName("6")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("6")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('6','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("6")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>    	
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','6',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>
  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_7', $clsExtraOption->GetOptionKindUse("7"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_7','7');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("7")?>&nbsp;<a href="#" onclick="openDisplayName('7','<?=$clsExtraOption->GetDisplayName("7")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("7")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('7','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("7")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>    	
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','7',this); return false;"><u><?=_("관리")?></u></a>       
    </td>
  </tr>
  
  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_8', $clsExtraOption->GetOptionKindUse("8"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_8','8');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("8")?>&nbsp;<a href="#" onclick="openDisplayName('8','<?=$clsExtraOption->GetDisplayName("8")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("8")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('8','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("8")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>    	
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','8',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>    	
      <?=MakeUseflagSelect('use_flag_9', $clsExtraOption->GetOptionKindUse("9"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_9','9');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("9")?>&nbsp;<a href="#" onclick="openDisplayName('9','<?=$clsExtraOption->GetDisplayName("9")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("9")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('9','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("9")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>    	
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','9',this); return false;"><u><?=_("관리")?></u></a>         
    </td>
  </tr>

  <tr>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>        
      <?=MakeUseflagSelect('use_flag_10', $clsExtraOption->GetOptionKindUse("10"))?>
      <!--<input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_10','10');return false;" />--> 
<?  } ?>    
    </td>
    <th><?=$clsExtraOption->GetDisplayName("10")?>&nbsp;<a href="#" onclick="openDisplayName('10','<?=$clsExtraOption->GetDisplayName("10")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td><?=$clsExtraOption->MakeSelectOptionTag("10")?></td>
    <td width="120" align="center">
<?// if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddOption('10','FIXOPTION','0','<?=$clsExtraOption->GetDisplayName("10")?>','','','',this); return false;"><u><?=_("추가")?></u></a>
<?//  } ?>      
      <a href="javascript:;" onclick="openEdiOption('FIXOPTION','10',this); return false;"><u><?=_("관리")?></u></a>            
    </td>
  </tr>

  <tr>
    <td width="105" align="center"><?=_("필수")?></td>
    <th><?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT")?>&nbsp;<a href="#" onclick="openCntDisplayName('OCNT','<?=$clsExtraOption->MakeOrderCntDisplayName($_GET[goodsno],"OCNT")?>',this);return false;"><img src='../img/bt_mod.png' class='hand absmiddle'/></a></th>
    <td>
        <?=$clsExtraOption->MakeOrderCntSelect($_GET[goodsno],"OCNT","order_cnt_select_OCNT","false")?>
        <!--<?=$clsExtraOption->GetOptionPriceType("FIXOPTION");?>-->
        <div style="display: none;">
            <span id="cnt_rule_OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"OCNT")?></span>
            <span id="user_cnt_rule_name_OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno])?></span>
            <span id="user_cnt_input_flag_OCNT"><?=$clsExtraOption->GetUserCntInputFlage($_GET[goodsno])?></span>           
        </div>
        &nbsp;x&nbsp;       
        <?=$clsExtraOption->MakeUnitCntSelect($_GET[goodsno])?>
        <div style="display: none;">
            <span id="unit_cnt_rule_OCNT"><?=$clsExtraOption->GetOrderCnt($_GET[goodsno],"OCNT","Y")?></span>
            <span id="user_unit_cnt_rule_name_OCNT"><?=$clsExtraOption->GetUserCntName($_GET[goodsno],"OCNT","Y")?></span>          
        </div>
    </td>
    <td width="120" align="center">
<? if ($cid == $cfg_center[center_cid] || $checkRegCid) { ?>      
      <a href="javascript:;" onclick="openAddPageOption('page',this,'OCNT','<?=$clsExtraOption->GetOptionPriceType("FIXOPTION");?>'); return false;"><u><?=_("견적방식")?></u></a>
      <a href="javascript:;" onclick="openAddPageOption('page_cnt',this,'OCNT'); return false;"><u><?=_("수량설정")?></u></a>
<?  } ?>            
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
    <td align="center"><a href="javascript:;" onclick="window.open('img_extopt_popup.php?goodsno=<?=$_GET[goodsno]?>','','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no'); return false;"><u><?=_("이미지 관리")?></u></a></td>
  </tr>
  
  </table>
</div>

<?
	if ($cid != $cfg_center[center_cid] && !$checkRegCid && $clsExtraOption->GetOptionKindUse("99") == "N" || $extra_stu_order == "UPL") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
?>
<!--<div class="addoptbox_div2">  
  <table class="addoptbox2" style="width: 99%">
  <tr>
    <td colspan="4">주문 메모 사용 여부 : &nbsp;&nbsp; <?=MakeUseflagSelect('use_flag_99', $clsExtraOption->GetOptionKindUse("99"))?>
      <input type="image" src="../img/sbtn_apply.gif" style="vertical-align: middle; width: 27px; height: 17px;" onclick="saveDisplayFlag('use_flag_99','99');return false;" />
      </td>
  </tr>
  </table>
</div>-->
<?
	}
?>
  
</div>

<?
//옵션,항목 추가
include "extra_option_preset_add_item.php";
?>

</div>

<script>
	if ("<?=$extra_stu_order?>" == "UPL") { //스튜디어견적 업로드일 경우 같은 가격 항목 설정 사용못함. 
		$j("#same").hide();
	}
</script>
