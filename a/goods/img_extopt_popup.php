<?

/*
* @date : 20180117 (20180118)
* @author : kdk
* @brief : 견적 옵션 상품 제한설정 추가.
* @request : 
* @desc : $limit_flag = '0'
* @todo :
*/

include_once "../_pheader.php";

$m_goods = new M_goods();
$m_extra_option = new M_extra_option();

if (!$_GET[goodsno]) {
    msg(_("상품코드가 존재하지 않습니다."), "close");
    exit ;
}

//$data = $db->fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
$data = $m_goods -> getInfo($_GET[goodsno]);
if (!$data) {
    msg(_("상품데이터가 존재하지 않습니다."), "close");
    exit ;
}

//옵션 정보.
$opt_data = $m_extra_option -> getOptionPriceList($cid, $cfg_center[center_cid], $_GET[goodsno]);
//debug($opt_data);

//이미지 초기화.
if ($_GET[all_delete] == "Y") {
    $m_etc -> setAddOptImgDelete($cid, $_GET[goodsno]);
}

//이미지 정보.
$img_data = $m_extra_option -> getOptImgList($cid, $cfg_center[center_cid], $_GET[goodsno]);
//debug($img_data);

//등록된 정보가 있을 경우 설정
if ($img_data) {
    foreach ($img_data as $key => $val) {
        foreach ($opt_data as $k => $v) {
            if ($v[option_item] == $val[option_item]) {
                $opt_data[$k][option_img] = $val[option_img];
                $opt_data[$k][limit_flag] = $val[limit_flag];
            }
        }
    }
}
//debug($opt_data);
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("옵션 이미지 설정") ?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
          
        <div class="panel-heading">
            <h4 class="panel-title"><?=_("옵션 이미지 설정 (옵션 이미지(jpg,png,gif) 파일은 100개 이하만 업로드 됩니다.)") ?></h4>
        </div>
            <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb_extopt_img.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
                <input type="hidden" name="goodsno" value="<?=$_GET[goodsno] ?>"/>
                <input type="hidden" name="url" value="<?=$_SERVER[HTTP_REFERER] ?>"/>
         <div class="panel-body panel-form">                
            <div class="panel-body">
	         <div class="table-responsive">
	         	<!-- begin #content -->
	
                    <table class="table table-striped table-bordered">
                    <tr>
                       <th><?=_("옵션명") ?></th>
                       <th><?=_("사용") ?></th>
                       <th><?=_("옵션이미지 (옵션 이미지(jpg,png,gif) 파일은 100개 이하만 업로드 됩니다.)") ?></th>
                    </tr>
                    <? foreach ($opt_data as $itemKey => $itemValue){ ?>
                    <tr align="center">
                       <td>
                           <input type="text" class="form-control" name="item[]" value="<?=$itemValue[option_item] ?>"/>
                       </td>
                       <td>
                           <input type="checkbox" name="limitflag[]" value="<?=$itemValue[option_item] ?>" <?if($itemValue[limit_flag]){?>checked<?}?>><span><?=_("제한") ?>
                       </td>
                       <td align="left">
                           <input type="file" class="form-control" name="img[]"/>
                           <? if ($itemValue[option_img]){ ?>
                           <img src="../img/bt_preview.png" align="absmiddle" onclick="vLayer(this.nextSibling)" class="hand absmiddle"/><div style="display:none;"><img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/l/<?=$_GET[goodsno]?>/<?=$itemValue[option_img] ?>"></div>
                           <? } ?>
                           <input type="checkbox" name="delimg[]" value="<?=$itemValue[option_item] ?>"><span><?=_("삭제") ?></span>
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

<script></script>

<?
    include_once "../_pfooter.php";
 ?>