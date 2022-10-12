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

<?
//옵셋 후가공 중철이 아니면.
if ($opt_mode != "bind_BD3_opset") { 
?>
    <div class="addoptbox_div3">
    <div style="text-align: right; margin: 2px 10px 10px 0;" class="xls_div">
        <button type="button" class="btn btn-xs btn-info" onclick="openFile(event,this,1);" ><?=_("엑셀 불러오기")?></button>
        <button type="button" class="btn btn-xs btn-inverse" onclick="location.href='<?=$url?>';" ><?=_("엑셀 저장")?></button>
        
<?	if ($opt_mode == "print_digital" || $opt_mode == "print_book_inside_digital" || $opt_mode == "gloss_digital" || $opt_mode == "oshi_digital" ||
		$opt_mode == "missing_digital" || $opt_mode == "domoo_digital" || $opt_mode == "sc_digital" || $opt_mode == "scb_digital") {	?>       
        <button type="button" class="btn btn-xs btn-warning" onclick="calcuAuto();" ><?=_("자동계산")?></button>
<?	}	?>
    </div>
    <table class="addoptbox4">
    <?
    echo $tableTitleContent;
    echo $tablePriceContent;
    ?>
    </table>
    <div style="text-align: right; margin: 2px 10px 10px 0;" class="xls_div">
        <button type="button" class="btn btn-xs btn-info" onclick="openFile(event,this,1);" ><?=_("엑셀 불러오기")?></button>
        <button type="button" class="btn btn-xs btn-inverse" onclick="location.href='<?=$url?>';" ><?=_("엑셀 저장")?></button>
<?	if ($opt_mode == "print_digital" || $opt_mode == "print_book_inside_digital" || $opt_mode == "gloss_digital" || $opt_mode == "oshi_digital" ||
		$opt_mode == "missing_digital" || $opt_mode == "domoo_digital" || $opt_mode == "sc_digital" || $opt_mode == "scb_digital") {	?>        
        <button type="button" class="btn btn-xs btn-warning" onclick="calcuAuto();" ><?=_("자동계산")?></button>
<?	}	?>        
    </div>

<?}?>
    
<?
//옵셋 후가공 예외.
if ($opt_mode == "domoo_opset" || $opt_mode == "press_opset" || $opt_mode == "foil_opset" || $opt_mode == "uvc_opset" ||
        $opt_mode == "bind_BD1_opset" || $opt_mode == "bind_BD3_opset") { 
?>
    <br />
    <input type="hidden" name="opt_mode2" value="<?=$opt_mode2?>">
    <? include '_option_opset_html.php'; ?>
<? } ?>
    
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
        <!-- filter:alpha(opacity=30); opacity:0.3; -moz-opacity:0.3;background-color:#fff; -->
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


<script>
	function calcuAuto(){
		//하리꾸미 수량은 저장된 설정을 따른다.
		var b3up = '<?=$r_ipro_standard_print_digital['B3']['B2']?>';
		var b4up = '<?=$r_ipro_standard_print_digital['B4']['A3']?>';
		var b5up = '<?=$r_ipro_standard_print_digital['B5']['A3']?>';
		var b6up = '<?=$r_ipro_standard_print_digital['B6']['A3']?>';
		
		var a2up = '<?=$r_ipro_standard_print_digital['A2']['B2']?>';
		var a4up = '<?=$r_ipro_standard_print_digital['A4']['A3']?>';
		var a5up = '<?=$r_ipro_standard_print_digital['A5']['A3']?>';
		var a6up = '<?=$r_ipro_standard_print_digital['A6']['A3']?>';
		
		var c8up = '<?=$r_ipro_standard_print_digital['C8']['A3']?>';
		var c9up = '<?=$r_ipro_standard_print_digital['C9']['A3']?>';		
				
		if (confirm('B2, A3 가격으로 자동계산하시겠습니까? 입력된 데이타가 손실됩니다.'))
		{
			var inputTag = document.getElementsByTagName('input');	
			for(var i = 0; i < inputTag.length; i ++){
		
				var tagName = inputTag[i].name;
				var res = tagName.split("_");
				
				if (res.length == 3)
				{
					if (res[2] != "B2" && res[2] != "A3")
					{
						var sourceB2 = res[0] + "_" + res[1] + "_B2";
						var sourceA3 = res[0] + "_" + res[1] + "_A3";
										
						if (res[2] == "A2" || res[2] == "B2" || res[2] == "B3")
						{
							var myvalue = $("input[name="+ sourceB2 + "]").attr("value");
							
							var myTempID = "cnt_" + res[0];
							var myTempValue = $("input[name="+ myTempID + "]").attr("value");
							var stadCnt = 0;
							
							if (res[2] == "A2")
								stadCnt = Math.ceil(myTempValue / a2up);
							if (res[2] == "B2")
								stadCnt = Math.ceil(myTempValue / b2up);
								
							if (res[2] == "B3")
								stadCnt = Math.ceil(myTempValue / b3up);
								//myvalue = Math.round(myvalue / 2);
							
							if (stadCnt > 0)
							{
								for(k=1; k < 1000000 ; k++)
								{
									var tempID = "cnt_" + k;
									var tempValue = $("input[name="+ tempID + "]").attr("value");
									
									if (tempValue != undefined && stadCnt <= tempValue)
									{
										var sourceID = k + "_" + res[1] + "_B2";
										myvalue = $("input[name="+ sourceID + "]").attr("value");
										break;		
									}
								}
							}
							
						}
						else
						{
							var myvalue = $("input[name="+ sourceA3 + "]").attr("value");
							
							
							var myTempID = "cnt_" + res[0];
							var myTempValue = $("input[name="+ myTempID + "]").attr("value");
							var stadCnt = 0;
								
							if (res[2] == "A4")															
								stadCnt = Math.ceil(myTempValue / a4up);							
							if (res[2] == "B5")
								stadCnt = Math.ceil(myTempValue / b5up);
							if (res[2] == "C8")
								stadCnt = Math.ceil(myTempValue / c8up);
							if (res[2] == "C9")
								stadCnt = Math.ceil(myTempValue / c9up);
									
							if (res[2] == "A5")
								stadCnt = Math.ceil(myTempValue / a5up);
							if (res[2] == "B6")
								stadCnt = Math.ceil(myTempValue / b6up);
							if (res[2] == "A6")
								stadCnt = Math.ceil(myTempValue / a6up);


							if (stadCnt > 0)
							{
								for(k=1; k < 1000000 ; k++)
								{
									var tempID = "cnt_" + k;
									var tempValue = $("input[name="+ tempID + "]").attr("value");
									
									if (tempValue != undefined && stadCnt <= tempValue)
									{
										var sourceID = k + "_" + res[1] + "_A3";
										myvalue = $("input[name="+ sourceID + "]").attr("value");
										break;		
									}
								}
							}
							
														
						}				
										
						$("input[name='" + tagName + "']" ).val(myvalue);				
					}				
				}			
			}		
			alert('자동 계산이 완료되었습니다.');
		}		
	}
</script>