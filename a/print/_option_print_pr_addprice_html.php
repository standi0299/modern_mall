<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=$title?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <div class="panel-body">
             <div class="table-responsive">
                <!-- begin #content -->

<script type="text/javascript" src="/js/extra_option/jquery_client.js"></script>
<script type="text/javascript" src="/js/extra_option/jquery.ui.js"></script>
<script type="text/javascript" src="/js/extra_option/jquery.form.js"></script>

<link href="/css/table.css" rel="stylesheet">
<link href="/css/PopupLayer.css" rel="stylesheet">

<form name="frm1" id="frm1" action="indb_price.php" method="post">
    <input type="hidden" name="mode" value="<?=$mode?>">
    <input type="hidden" name="opt_mode" value="<?=$_GET[opt_mode]?>">
    <input type="hidden" name="opt_prefix" value="<?=$_GET[opt_prefix]?>">
    <input type="hidden" name="opt_group" value="<?=$_GET[opt_group]?>">
    <input type="hidden" name="paperJsonData" value="<?=$paperJsonData?>">

    <div class="addoptbox_div3">
    <div style="text-align: right; margin: 2px 10px 10px 0;" class="xls_div">
        <button type="button" class="btn btn-xs btn-info" onclick="openFile(event,this,1);" ><?=_("엑셀 불러오기")?></button>
        <button type="button" class="btn btn-xs btn-inverse" onclick="location.href='<?=$url?>';" ><?=_("엑셀 저장")?></button>
    </div>
    <table class="addoptbox4">
    <?
    //echo $tableTitleContent;
    //echo $tablePriceContent;
    ?>

    <tr>
    <th style="width: 50%;">mm당</th>
    <th>가격</th>
    </tr>

    <tr>
    <td align='center'>1mm당</td>
    <td align='center'><input type='text' name='mm2_1' value='<?=$file_data[1]?>'></td>
    </tr>

    </table>
    <div style="text-align: right; margin: 2px 10px 10px 0;" class="xls_div">
        <button type="button" class="btn btn-xs btn-info" onclick="openFile(event,this,1);" ><?=_("엑셀 불러오기")?></button>
        <button type="button" class="btn btn-xs btn-inverse" onclick="location.href='<?=$url?>';" ><?=_("엑셀 저장")?></button>
    </div>
    </div>
    <br /><br />
    <?
    $debug_data .= "7 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
    //echo $debug_data;
    ?>

    <div class="form-group">
        <label class="col-md-2 control-label"></label>
        <div class="col-md-8" style="text-align: center;">
            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('<?=_("입력된 가격이 그대로 저장되며, 추후 복원이 되지 않습니다. 저장하시겠습니까?")?>');"><?=_("등록")?></button>
            <button type="button" class="btn btn-sm btn-default" onclick="javascript:self.close()"><?=_("취소")?></button>
        </div>
        <div class="col-md-2" style="text-align: center;">
            <? if ($_SERVER[REMOTE_ADDR]=="210.96.184.229" || $_SERVER[REMOTE_ADDR]=="192.168.0.181") { ?>
            <button type="button" class="btn btn-sm btn-inverse" onclick="javascript:priceFileDelete('<?=$opt_mode?>')"><?=_("초기화")?></button>
            <? } ?>
        </div>
    </div>  
</form>

<script type="text/javascript">
function priceFileDelete(opt_mode) {
    if (confirm('<?=_("입력된 가격이 초기화됩니다. 추후 복원이 되지 않습니다.")?>' +"\n"+ '<?=_("초기화하시겠습니까?")?>')) {
        $('input[name="mode"]').val("print_price_init");
        $('#frm1').submit();
    }
}
</script>

<script type="text/javascript" src="/js/extra_option/goods.extra.option.admin.js"></script>
<script type="text/javascript" src="/js/extra_option/goods.extra.option.admin.action.js"></script>

<!--옵션추가 //-->
<div class="layer1">
    <div class="bg">

    </div>

    <!--loading-->
    <div id="loading_back" style="width:100%;height:100%;padding:0;margin:0;position:absolute;top:0;left:0;z-index:90;filter:alpha(opacity=0); opacity:0.0; -moz-opacity:0.0;background-color:#fff;">
        &nbsp;
    </div>
    <div id="loading_div" style="width:50px;height:50px;padding:0;position:absolute;top:50%;left:50%;margin:-25px 0 0 -25px;z-index:91;">
        <img src="../img/loading_s.gif" />
    </div>

    <!--가격항목엑셀파일불러오기 //-->
    <div id="dlayer-openfile" class="pop-layer">
        <div class="pop-container">
            <div class="pop-conts">
                <!--content //-->
                <form id="myForm" action="<?=$_SERVER[REQUEST_URI]?>" enctype="multipart/form-data" method="post">
                    <div>
                        <p class="ctxt mb20">
                            <?=_("파일 불러오기 창")?>.
                            <br>
                        </p>
                        <div>

                            <input type="file" id="excelFile" name="excelFile" />

                        </div>
                    </div>
                    <div class="btn-r">
                        <!--<input type="submit" value="확인" class="cbtn-c" />-->
                        <a href="#" class="cbtn-c" onclick="saveFile();"><?=_("확인")?></a>
                        <a href="#" class="cbtn"><?=_("취소")?></a>
                    </div>
                </form>
                <!--// content-->
            </div>
        </div>
    </div>
    <!--가격항목엑셀파일불러오기 //-->
</div>
<!--옵션추가 //-->

<script type="text/javascript">
hideLoading();
</script>
          
                <!-- end #content -->
             </div>
            </div>
         </div>
        <div class="form-group">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-9">
                <!--<button type="button" style="margin-bottom: 15px;" class="btn btn-default"onclick="window.close();">닫  기</button>-->
            </div>
        </div>         
      </div>
   </div>
</div>
