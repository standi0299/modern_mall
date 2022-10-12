<?
include_once "../_pheader.php";

$m_goods = new M_goods();
$m_etc = new M_etc();

$r_addopt = array();
$r_addopt_bundle = array();
$r_addopt_bundle_no = array();
$r_addopt_bundle_opt = array();

if (!$_GET[goodsno]) {
    msg(_("상품코드가 존재하지 않습니다."),"close");
    exit;
}

//$data = $db->fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
$data = $m_goods->getInfo($_GET[goodsno]);
if (!$data){
    msg(_("상품데이터가 존재하지 않습니다."),"close");
    exit;
}

$res = $m_goods->getGoodsAddOptList($_GET[goodsno]);
//debug($res);
foreach ($res as $key => $val) {
    
    $r_addopt[$val[addopt_bundle_no]] = $val;
    
    if(!$val[addopt_bundle_img]) continue;
    //debug($val[addopt_bundle_img]);
    
    $r_addopt_bundle[$val[addopt_bundle_no]] = $val;
    $r_addopt_bundle_no[] = $val[addopt_bundle_no];

    $res2 = $m_goods->getGoodsAddOptNoList($val[addopt_bundle_no]);
    //debug($res2);
    
    foreach ($res2 as $k => $v) {
        $r_addopt_bundle[$val[addopt_bundle_no]][addopt][$v[addoptno]] = $v;
    }
}
//debug($r_addopt_bundle);

if ($r_addopt_bundle_no) {
	foreach ($r_addopt_bundle as $k=>$v) {
		foreach ($v[addopt] as $k2=>$v2) {
			//$r_addopt_bundle_opt[$v2[addopt_bundle_no]][][item_name] = $v2[addoptnm];
			$option_all_arr[$v2[addopt_bundle_no]][][item_name] = $v2[addoptnm];
		}
	}
}   

$option_indexs_arr[] = $r_addopt_bundle_no;
//debug($option_indexs_arr);
//debug($option_all_arr);

$InsertExtraOptionTable = insertOptionImgTableASItem($option_all_arr, $option_indexs_arr);
//debug($InsertExtraOptionTable);    

//이미지 초기화.
if ($_GET[all_delete] == "Y") {
    $m_etc->setAddOptImgDelete($cid, $_GET[goodsno]);
}
        
$opt_data = $m_etc->getAddOptImgList($cid, $_GET[goodsno]);
//debug($opt_data);

//등록된 정보가 있을 경우 설정
if ($opt_data) {
    foreach ($opt_data as $key => $value) {
        foreach ($InsertExtraOptionTable as $k => $v) {
            if($v[option_item] == $value[option_item]) {
                $InsertExtraOptionTable[$k][option_img] = $value[option_img];
            }
        }
    }
}

//debug($InsertExtraOptionTable);

function insertOptionImgTableASItem($option_all_arr, $option_indexs_arr) {
	$insert_option_item = array();
	$itemIndex = 0;
	$option_index = 0;

	foreach ($option_indexs_arr as $key => $value) {

		$max_kind_index = count($value);
		$option_index = 0;
//debug($max_kind_index);
		//옵션이 한개인경우 처리
		if ($max_kind_index == 1) {
			foreach ($option_all_arr[$value[$option_index]] as $itemkey => $itemvalue) {
				$insert_option_item[] = array("option_item" => $itemvalue[item_name], "option_img" => "");
			}

		} else {
			if ($max_kind_index > $option_index) {
				//debug($option_all_arr[$value[$option_index]]);
				foreach ($option_all_arr[$value[$option_index]] as $itemkey => $itemvalue) {
					//$insert_option_item[$itemIndex] .= $itemvalue[item_name] . "|";
					$insert_option_item_str = $itemvalue[item_name] . "|";
					$insert_option_code_str = $itemvalue[code] . "|";
					$option_index_sub = $option_index + 1;
					insertOptionImgTableASItemSubFunc($option_all_arr, $value, $insert_option_item, $insert_option_item_str, $option_index_sub, $itemIndex, $insert_option_code_str);
				}
			}
		}

		//debug($insert_option_item_str);
		//exit;
	}

	return $insert_option_item;
}

function insertOptionImgTableASItemSubFunc($option_all_arr, $option_indexs_arr_value, &$insert_option_item, $insert_option_item_str, $option_index, &$itemIndex) {
	$max_kind_index = count($option_indexs_arr_value);

	//debug("max_kind_index : ".$max_kind_index);
	//debug("option_index : ".$option_index);
	//debug($insert_option_item);
	//debug("insert_option_item_str : ".$insert_option_item_str);
	//debug("insert_option_code_str : ".$insert_option_code_str);

	$last_item_name_arr = explode("|", $insert_option_item_str);
	//부모 옵션 확인
	foreach ($last_item_name_arr as $key => $val) {
		if ($val) {
			$last_item_name = $val;
		}
	}
	//debug("last_item_name : -".$last_item_name."-");

	$last_code_arr = explode("|", $insert_option_code_str);
	//부모 옵션 코드 확인
	foreach ($last_code_arr as $key => $val) {
		if ($val) {
			$last_code = $val;
		}
	}
	//debug("last_code : -".$last_code."-");

	if ($max_kind_index > $option_index) {
		foreach ($option_all_arr[$option_indexs_arr_value[$option_index]] as $itemkey => $itemvalue) {
			//$insert_option_item[$itemIndex] .= $itemvalue[item_name] . "|";
			//$insert_option_item_str .= $itemvalue[item_name] . "|";
			//debug($itemvalue);

			if (($max_kind_index - 1) == $option_index) {
				$insert_option_item[$itemIndex] = array("option_item" => $insert_option_item_str . $itemvalue[item_name], "option_img" => "");
				//$insert_option_item[$itemIndex] = $insert_option_item_str;
				$itemIndex = $itemIndex + 1;
				//break;
				//debug($insert_option_item);
				//exit;				
			} else {
				//debug($insert_option_item_str_sub);
				//exit;
				//$insert_option_item_str .= $itemvalue[item_name] . "|";
				$insert_option_item_str_sub = $insert_option_item_str . trim($itemvalue[item_name]) . "|";
				$option_index_sub = $option_index + 1;
				insertOptionImgTableASItemSubFunc($option_all_arr, $option_indexs_arr_value, $insert_option_item, $insert_option_item_str_sub, $option_index_sub, $itemIndex);
			}
		}
	}
}
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("추가 옵션 이미지 설정")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
          
        <div class="panel-heading">
            <h4 class="panel-title"><?=_("추가 옵션 이미지 설정 (옵션 이미지(jpg,png,gif) 파일은 100개 이하만 업로드 됩니다.)")?></h4>
        </div>          
        <div class="panel-body">
        <form class="form-horizontal" name="fm1" method="post" action="indb.php">
            <input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>" />
            <input type="hidden" name="mode" value="addopt_bundle_img" />  
            <div class="form-group">
                <label class="col-md-2 control-label"><?=_("이미지 추가할 옵션 선택")?></label>
                <label class="col-md-1 control-label"></label>
                <div class="col-md-6">
                    <? foreach ($r_addopt as $k=>$v) { ?>
                        <input type="checkbox"name="addopt_bundle_no[]" value="<?=$v[addopt_bundle_no]?>" class="absmiddle private" <?=($v[addopt_bundle_img]) ? "checked" : "";?> /><span class="stxt absmiddle"><?=$v[addopt_bundle_name]?></span>&nbsp;&nbsp;&nbsp;
                    <? } ?>
                </div>                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-ms btn-info"><?=_("저 장")?></button>
                </div>
            </div>
        </form>                            
        </div>          
          
          
            <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb_addopt_img.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
                <input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>"/>
                <input type="hidden" name="url" value="<?=$_SERVER[HTTP_REFERER]?>"/>
         <div class="panel-body panel-form">                
            <div class="panel-body">
	         <div class="table-responsive">
	         	<!-- begin #content -->
	
                    <table class="table table-striped table-bordered">
                    <tr>
                       <th><?=_("옵션명")?></th>
                       <th><?=_("옵션이미지 (옵션 이미지(jpg,png,gif) 파일은 100개 이하만 업로드 됩니다.)")?></th>
                    </tr>
                    <? foreach ($InsertExtraOptionTable as $itemKey => $itemValue){ ?>
                    <tr align="center">
                       <td><input type="text" class="form-control" name="item[]" value="<?=$itemValue[option_item]?>"/></td>
                       <td align="left">
                           <input type="file" class="form-control" name="img[]"/>
                           <? if ($itemValue[option_img]){ ?>
                           <img src="../img/bt_preview.png" align="absmiddle" onclick="vLayer(this.nextSibling)" class="hand absmiddle"/><div style="display:none;"><img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/l/<?=$_GET[goodsno]?>/<?=$itemValue[option_img]?>"></div>
                           <? } ?>
                           <input type="checkbox" name="delimg[]" value="<?=$itemValue[option_item]?>"><span><?=_("삭제")?></span>
                       </td>
                    </tr>
                    <? } ?>
                    </table>
		  
				<!-- end #content -->
	         </div>
            </div>
         </div>
	    <div class="form-group">
	        <label class="col-md-3 control-label"></label>
	        <div class="col-md-9">
	            <button type="submit" style="margin-bottom: 15px;" class="btn btn-success" onclick="return confirm('<?=_("선택한 이미지 그대로 저장되며, 삭제된 이미지는 추후 복원이 되지 않습니다. 저장하시겠습니까?")?>');"><?=_("등 록")?></button>
	            <button type="button" style="margin-bottom: 15px;" class="btn btn-default" onclick="window.close();"><?=_("닫  기")?></button>
	        </div>
	    </div> 
            </form>	            
      </div>
   </div>
</div>

<script>

</script>

<? include_once "../_pfooter.php"; ?>