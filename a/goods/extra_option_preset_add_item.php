
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
    
    <!--항목 추가 //-->
    <div id="dlayer-addoptiondata" class="pop-layer">
        <div class="pop-container">
            <div class="pop-conts">                
                <div>
                  <p class="ctxt mb20">
                      <span id="title"></span>&nbsp;<?=_("항목 입력 창")?>.
                  </p>
          <ul id="optionDataList" style="list-style-type:none;margin:0;padding:0">
            <li id="item">
              <table style="width:450px;">
                <tr>
                  <th><?=_("항목 입력")?></th>
                  <td>
                    <input type='text' name='f_item_name' id='f_item_name' size='35' style='width:200px;' />
                    <br>(<?=_("구분자는 ',' 콤마입니다.")?>)
                  </td>
                </tr>
              </table>
            </li>
            <li id="extra">
              <table style="width:450px;">
                <tr>
                  <th><?=_("실제 항목 입력")?></th>
                  <td>
                    <span id="ex1"></span>
                    <input type='text' name='f_extra_data1' id='f_extra_data1' size='5' style='width:30px;' /> <span id="ex2">x</span> 
                    <input type='text' name='f_extra_data2' id='f_extra_data2' size='5' style='width:30px;' /> <span id="ex3">(mm)</span> 
                  </td>
                </tr>
              </table>
            </li>
            <li id="same">
              <table style="width:450px;">
                <tr>
                  <th><?=_("같은 가격 항목 설정")?></th>
                  <td>
                    <select name="f_same_item" id="f_same_item" onchange="SameItemSelected(this);">
                      <option value=''><?=_("없음")?></option>
                    </select>
                  </td>
                </tr>
              </table>
            </li>
            <li id="item2" class="itemChild">
              <table style="width:450px;">
                <tr>
                  <th><?=_("2차 항목 설정")?></th>
                  <td>
                    <textarea name='f_item_name2' id='f_item_name2' class="fItemName" cols="40" rows="5"></textarea>
                    <br>(<?=_("구분자는 ',' 콤마입니다.")?>)
                  </td>
                </tr>
              </table>
            </li>
            <li id="item3" class="itemChild">
              <table style="width:450px;">
                <tr>
                  <th><?=_("3차 항목 설정")?></th>
                  <td>
                    <textarea name='f_item_name3' id='f_item_name3' class="fItemName" cols="40" rows="5"></textarea>
                    <br>(<?=_("구분자는 ',' 콤마입니다.")?>)
                  </td>
                </tr>
              </table>
            </li>
            <li id="item4" class="itemChild">
              <table style="width:450px;">
                <tr>
                  <th><?=_("4차 항목 설정")?></th>
                  <td>
                    <textarea name='f_item_name4' id='f_item_name4' class="fItemName" cols="40" rows="5"></textarea>
                    <br>(<?=_("구분자는 ',' 콤마입니다.")?>)
                  </td>
                </tr>
              </table>
            </li>
            <li id="item5" class="itemChild">
              <table style="width:450px;">
                <tr>
                  <th><?=_("5차 항목 설정")?></th>
                  <td>
                    <textarea name='f_item_name5' id='f_item_name5' class="fItemName" cols="40" rows="5"></textarea>
                    <br>(<?=_("구분자는 ',' 콤마입니다.")?>)
                  </td>
                </tr>
              </table>
            </li>
                  </ul>
              </div>
                <div class="btn-r">
          			<a href="#" class="cbtn-c" onclick="saveOptionData();"><?=_("확인")?></a>
                    <a href="#" class="cbtn"><?=_("취소")?></a>
                </div>
          </div>            
      </div>
  </div>                    
    <!--항목 추가 //-->
        
    <!--페이지 item_select_PP_3 //-->
    <div id="dlayer-page" class="pop-layer">
        <div class="pop-container">
            <div class="pop-conts">
                <!--content //-->
                <div>
                  <p class="ctxt mb20">
                  	<h3><?=_("견적 가격관리 에서 수량 구간별로 입력하는 값의 의미를 결정하세요.")?></h3>    
                  </p>                                  	
                  <p class="ctxt mb20">
		          <input type="radio" id="price_type" name="price_type" value="CNT" class="absmiddle" style="height:12px"><span class="absmiddle"><?=_("페이지 1장 당 가격으로 계산 (페이지수량 x 입력가격 = 최종 견적 금액)")?></span><br/>
		          <input type="radio" id="price_type" name="price_type" value="TIME" class="absmiddle" style="height:12px" checked><span class="absmiddle"><?=_("구간 내에서는 어떤 수량이라도 입력된 가격으로 계산 (입력가격 = 각 구간별 최종 견적 금액)")?></span>
		          </p>
		          
				  <div id="div_d_cnt_rule" style="display: none;">
		          <hr size="1px" />		          
                  <p class="ctxt mb20">
                      <h3><?=_("고객이 옵션으로 선택하게 되는 수량의 한계치를 입력하세요.")?></h3>
                  </p>
                  <p class="ctxt mb20">
		            <table style="width:450px;">
		            <tr>
		              <td align="center">
		              	<input type="text" style="width:30px;" id="d_cnt_rule" name="d_cnt_rule" />
		              	(<?=_("설정된 값 이하만 표시됩니다.")?>)
		              </td>
		            </tr>
		            </table>
		          </p>
		          </div>		          
		          
		          <hr size="1px" />		          
                  <p class="ctxt mb20">
                      <h3><?=_("고객이 옵션으로 선택하게 되는 수량을 입력하세요.")?></h3>
                  </p>
                  <p class="ctxt mb20">
		          <input type="radio" id="cnt_kind" name="cnt_kind" value="1" class="absmiddle" style="height:12px" onclick="CntKindChange('page_cnt1')" checked><span class="absmiddle"><?=_("각 구간별 수량을 직접 하나 하나 입력")?></span><br/>
		          <input type="radio" id="cnt_kind" name="cnt_kind" value="2" class="absmiddle" style="height:12px" onclick="CntKindChange('page_cnt2')"><span class="absmiddle"><?=_("각 구간별 수량을 수식으로 설정하여 자동으로 입력")?></span>
		          </p>
		          
                  <p class="ctxt mb20">
		            <table style="width:450px;">
		            <tr>
		              <td align="left">
		              	<input type="checkbox" id="user_cnt_input_flag" name="user_cnt_input_flag" /><?=_("직접 입력(사용자화면)")?>
		              	&nbsp;&nbsp;&nbsp;&nbsp;
		              	<?=_("단위")?> : <input type="text" style="width:30px;" id="user_cnt_rule_name" name="user_cnt_rule_name" />
		              	(<?=_("예: 매, 장, 페이지")?>)
		              </td>
		            </tr>
		            </table>
		          </p>
		          
		          <div id="page_cnt1" class="page_cnt">
		            <table class="addoptbox" style="width:450px;">
		            <tr>
		              <th><?=_("수량 항목 입력")?></th>
		              <td>
		                <textarea name='f_cnt_rule' id='f_cnt_rule' cols="50" rows="3"></textarea>
		                <br>(<?=_("구분자는 ';' 세미콘론입니다. 예: 100;200;300")?>)
		              </td>
		            </tr>
		            </table>
		          </div>
		          <div id="page_cnt2" class="page_cnt" style="display: none;">
		              
		            <table class="addcntbox" style="width:450px;" addcntbox_idx="1">
		            <tr class="addcntbox_div">
		              <td align="center"><button onclick="add_addcnt(this);"><?=_("추가")?></button></td>
		              <td align="right">
		                <!--<span name="cnt_start[1]">0</span>-->
		                <input type="text" style="width:30px;" name="cnt_start[1]" value="1" pt="_pt_numplus" onkeydown="_pattern(this);" />(<?=_("에서")?>) ~ 
		                <input type="text" style="width:30px;" name="cnt_end[1]" value="100" pt="_pt_numplus" onkeydown="_pattern(this);" onchange="numZeroChk(this);" />(<?=_("까지")?>) - 
		                <input type="text" style="width:30px;" name="cnt_step[1]" value="10" pt="_pt_numplus" onkeydown="_pattern(this);" onchange="numZeroChk(this);" />(<?=_("씩 증가")?>)
		              </td>
		              <td>&nbsp;</td>
		            </tr>
		            </table>            
		          </div>            
		        </div>
                <div class="btn-r">
          			<a href="#" class="cbtn-c" onclick="savePageOptionData('cnt');"><?=_("확인")?></a>
                    <a href="#" class="cbtn"><?=_("취소")?></a>
                </div>
                <!--// content-->
            </div>
        </div>
    </div>
    <!--페이지 item_select_PP_3 //-->

    <!--페이지 item_select_PP_3 //-->
    <div id="dlayer-page-after" class="pop-layer">
        <div class="pop-container">
            <div class="pop-conts">
                <!--content //-->
                <div>
                  <p class="ctxt mb20">
                  	<h3><?=_("견적 가격관리 에서 수량 구간별로 입력하는 값의 의미를 결정하세요.")?></h3>    
                  </p>                                  	
                  <p class="ctxt mb20">
		          <input type="radio" id="after_price_type" name="after_price_type" value="CNT" class="absmiddle" style="height:12px"><span class="absmiddle"><?=_("페이지 1장 당 가격으로 계산 (페이지수량 x 입력가격 = 최종 견적 금액)")?></span><br/>
		          <input type="radio" id="after_price_type" name="after_price_type" value="TIME" class="absmiddle" style="height:12px" checked><span class="absmiddle"><?=_("구간 내에서는 어떤 수량이라도 입력된 가격으로 계산 (입력가격 = 각 구간별 최종 견적 금액)")?></span>
		          </p>
		          <hr size="1px" />
		          
                  <p class="ctxt mb20">
                      <h3><?=_("고객이 옵션으로 선택하게 되는 수량을 입력하세요.")?></h3>
                  </p>
                  <p class="ctxt mb20">
		          <input type="radio" id="after_cnt_kind" name="after_cnt_kind" value="1" class="absmiddle" style="height:12px" onclick="CntKindChange('page_cnt1-after')" checked><span class="absmiddle"><?=_("각 구간별 수량을 직접 하나 하나 입력")?></span><br/>
		          <input type="radio" id="after_cnt_kind" name="after_cnt_kind" value="2" class="absmiddle" style="height:12px" onclick="CntKindChange('page_cnt2-after')"><span class="absmiddle"><?=_("각 구간별 수량을 수식으로 설정하여 자동으로 입력")?></span>
		          </p>
		          <div id="page_cnt1-after" class="page_cnt">
		            <table class="addoptbox" style="width:450px;">
		            <tr>
		              <th><?=_("수량 항목 입력")?></th>
		              <td>
		                <textarea name='after_f_cnt_rule' id='after_f_cnt_rule' cols="50" rows="3"></textarea>
		                <br>(<?=_("구분자는 ';' 세미콘론입니다. 예: 100;200;300")?>)
		              </td>
		            </tr>
		            </table>
		          </div>
		          <div id="page_cnt2-after" class="page_cnt" style="display: none;">
		            <table class="addcntbox_after" style="width:450px;" addcntbox_idx="1">
		            <tr class="addcntbox_div">
		              <td align="center"><button onclick="add_addcnt_after(this);"><?=_("추가")?></button></td>
		              <td align="right">
		                <!--<span name="after_cnt_start[1]">0</span>-->
		                <input type="text" style="width:30px;" name="after_cnt_start[1]" value="1" pt="_pt_numplus" onkeydown="_pattern(this);" />(<?=_("에서")?>) ~ 
		                <input type="text" style="width:30px;" name="after_cnt_end[1]" value="100" pt="_pt_numplus" onkeydown="_pattern(this);" onchange="numZeroChk(this);" />(<?=_("까지")?>) - 
		                <input type="text" style="width:30px;" name="after_cnt_step[1]" value="10" pt="_pt_numplus" onkeydown="_pattern(this);" onchange="numZeroChk(this);" />(<?=_("씩 증가")?>)
		              </td>
		              <td>&nbsp;</td>
		            </tr>
		            </table>            
		          </div>            
		        </div>
                <div class="btn-r">
          			<a href="#" class="cbtn-c" onclick="savePageOptionDataAfter();"><?=_("확인")?></a>
                    <a href="#" class="cbtn"><?=_("취소")?></a>
                </div>
                <!--// content-->
            </div>
        </div>
    </div>
    <!--페이지 item_select_PP_3 //-->

    <!--페이지 item_select_PP_3 //-->
    <div id="dlayer-page_cnt" class="pop-layer">
        <div class="pop-container">
            <div class="pop-conts">
                <!--content //-->
                <div>
                  <p class="ctxt mb20">
                      <h3><?=_("고객이 옵션으로 선택하게 되는 수량을 입력하는 창")?>.</h3>
                  </p>
                  <p class="ctxt mb20">
		          <input type="radio" id="unit_cnt_kind" name="unit_cnt_kind" value="1" class="absmiddle" style="height:12px" onclick="UnitCntKindChange('page_unit_cnt1')" checked><span class="absmiddle"><?=_("각 구간별 수량을 직접 하나 하나 입력")?></span><br/>
		          <input type="radio" id="unit_cnt_kind" name="unit_cnt_kind" value="2" class="absmiddle" style="height:12px" onclick="UnitCntKindChange('page_unit_cnt2')"><span class="absmiddle"><?=_("각 구간별 수량을 수식으로 설정하여 자동으로 입력")?></span>
		          </p>
		          
                  <p class="ctxt mb20">
		            <table class="addoptbox" style="width:450px;">
		            <tr>
		              <td align="left">
		              	<?=_("수량 단위")?> : <input type="text" style="width:30px;" id="user_unit_cnt_rule_name" name="user_unit_cnt_rule_name" />
		              	(<?=_("예: 부, 건")?>)
		              </td>
		            </tr>
		            </table>
		          </p>
		          
		          <div id="page_unit_cnt1" class="page_cnt">
		            <table class="addoptbox" style="width:450px;">
		            <tr>
		              <th><?=_("수량 항목 입력")?></th>
		              <td>
		                <textarea name='f_unit_cnt_rule' id='f_unit_cnt_rule' cols="50" rows="3"></textarea>
		                <br>(<?=_("구분자는 ';' 세미콘론입니다. 예: 1;2;3")?>)
		              </td>
		            </tr>
		            </table>
		          </div>
		          <div id="page_unit_cnt2" class="page_cnt" style="display: none;">		              
		            <!--<table style="width:470px;">
		            <tr>
		              <th>수량 항목 입력</th>
		              <td>
		                 <input type="text" style="width:30px;" id="unit_cnt_start" name="unit_cnt_start" pt="_pt_numplus" onkeydown="_pattern(this);" />(에서) ~
		                 <input type="text" style="width:30px;" id="unit_cnt_end" name="unit_cnt_end" pt="_pt_numplus" onkeydown="_pattern(this);" onchange="numZeroChk(this);" />(까지) ~ 
		                 <input type="text" style="width:30px;" id="unit_cnt_step" name="unit_cnt_step" pt="_pt_numplus" onkeydown="_pattern(this);" onchange="numZeroChk(this);" />(씩 증가)
		              </td>
		            </tr>
		            </table>-->
		            <table class="addunitcntbox" style="width:450px;" addcntbox_idx="1">
		            <tr class="addcntbox_div">
		              <td align="center"><button onclick="add_addunitcnt(this);"><?=_("추가")?></button></td>
		              <td align="right">
		                <!--<span name="cnt_start[1]">0</span>-->
		                <input type="text" style="width:30px;" name="unit_cnt_start[1]" value="1" pt="_pt_numplus" onkeydown="_pattern(this);" />(<?=_("에서")?>) ~ 
		                <input type="text" style="width:30px;" name="unit_cnt_end[1]" value="100" pt="_pt_numplus" onkeydown="_pattern(this);" onchange="numZeroChk(this);" />(<?=_("까지")?>) - 
		                <input type="text" style="width:30px;" name="unit_cnt_step[1]" value="1" pt="_pt_numplus" onkeydown="_pattern(this);" onchange="numZeroChk(this);" />(<?=_("씩 증가")?>)
		              </td>
		              <td>&nbsp;</td>
		            </tr>
		            </table>		            
		          </div>
		        </div>
                <div class="btn-r">
          			<a href="#" class="cbtn-c" onclick="savePageOptionData('unit');"><?=_("확인")?></a>
                    <a href="#" class="cbtn"><?=_("취소")?></a>
                </div>
                <!--// content-->
            </div>
        </div>
    </div>
    <!--페이지 item_select_PP_3 //-->
    
  <!--항목관리 //-->
    <div id="dlayer-edioptionitem" class="pop-layer">
        <div class="pop-container">
            <div class="pop-conts">
                <!--content //-->
              <div>
                  <p class="ctxt mb20">
                      <?=_("항목 관리 창")?>.<br>
                  </p>
	          <div>           
	            <ul id="selectedResourceList" style="list-style-type:none;margin:0;padding:0">
	              
	            </ul>               
	          </div>
	        </div>
              <div class="btn-r">
          		<a href="#" class="cbtn-c" onclick="saveOrderIndex();"><?=_("확인")?></a>
                  <a href="#" class="cbtn"><?=_("취소")?></a>
              </div>
                <!--// content-->
            </div>
        </div>
    </div>  
  <!--항목관리 //-->
  
  
    <!--명칭변경 //-->    
    <div id="dlayer-displayname" class="pop-layer">
      <div class="pop-container">
        <div class="pop-conts">                
          <div>
            <p class="ctxt mb20">
              <span id="title"></span>&nbsp;<?=_("옵션 그룹명  변경")?>.
            </p>
            
            <ul id="optionDataList" style="list-style-type:none;margin:0;padding:0">
              <li id="item">
                <table style="width:450px;">
                  <tr>
                    <th><?=_("변경 항목 입력")?></th>
                    <td>
                      <input type='text' name='f_display_name' id='f_display_name' size='35' style='width:200px;' />
                    </td>
                  </tr>
                </table>
              </li>
            </ul>    
          </div>
        </div>
        <div class="btn-r">
          <a href="#" class="cbtn-c" onclick="saveDisplayName();"><?=_("확인")?></a>
          <a href="#" class="cbtn"><?=_("취소")?></a>
        </div>            
      </div>
    </div>      
  <!--항목관리 //-->

    <!--명칭변경 //-->    
    <div id="dlayer-cntdisplayname" class="pop-layer">
      <div class="pop-container">
        <div class="pop-conts">                
          <div>
            <p class="ctxt mb20">
              <span id="title"></span>&nbsp;<?=_("옵션 그룹명  변경")?>.
            </p>
            
            <ul id="optionDataList" style="list-style-type:none;margin:0;padding:0">
              <li id="item">
                <table style="width:450px;">
                  <tr>
                    <th><?=_("변경 그룹명 입력")?></th>
                    <td>
                      <input type='text' name='f_cnt_display_name' id='f_cnt_display_name' size='35' style='width:200px;' />
                    </td>
                  </tr>
                </table>
              </li>
            </ul>    
          </div>
        </div>
        <div class="btn-r">
          <a href="#" class="cbtn-c" onclick="saveCntDisplayName();"><?=_("확인")?></a>
          <a href="#" class="cbtn"><?=_("취소")?></a>
        </div>            
      </div>
    </div>      
  <!--명칭변경 //-->  
  
  <!--명칭변경 //-->    
    <div id="dlayer-itemnameupdate" class="pop-layer">
      <div class="pop-container">
        <div class="pop-conts">                
          <div>
            <p class="ctxt mb20">
              <span id="title"></span>&nbsp;<?=_("옵션 항목 명칭 변경")?>.
            </p>
            
            <ul id="optionDataList" style="list-style-type:none;margin:0;padding:0">
              <li id="item">
                <table style="width:450px;">
                  <tr>
                    <th><?=_("변경 항목 입력")?></th>
                    <td>
                      <input type='text' name='f_update_item_name' id='f_update_item_name' size='35' style='width:200px;' />
                    </td>
                  </tr>
                </table>
              </li>
            </ul>    
          </div>
        </div>
        <div class="btn-r">
          <a href="#" class="cbtn-c" onclick="saveItemNameUpdate();"><?=_("확인")?></a>
          <a href="#" class="cbtn"><?=_("취소")?></a>
        </div>            
      </div>
    </div>      
  <!--항목관리 //-->

  <!--명칭변경 //-->    
    <div id="dlayer-itemnameupdateS2" class="pop-layer">
      <div class="pop-container">
        <div class="pop-conts">                
          <div>
            <p class="ctxt mb20">
              <span id="title"></span>&nbsp;<?=_("옵션 항목 명칭 변경")?>.
            </p>
            
            <ul id="optionDataList" style="list-style-type:none;margin:0;padding:0">
              <li id="item">
                <table style="width:450px;">
                  <tr>
                    <th><?=_("변경 항목 입력")?></th>
                    <td>
                      <input type='text' name='f_update_item_nameS2' id='f_update_item_nameS2' size='35' style='width:200px;' />
                    </td>
                  </tr>
                </table>
              </li>
            </ul>    
          </div>
        </div>
        <div class="btn-r">
          <a href="#" class="cbtn-c" onclick="saveItemNameUpdateS2();"><?=_("확인")?></a>
          <a href="#" class="cbtn"><?=_("취소")?></a>
        </div>            
      </div>
    </div>      
  <!--항목관리 //-->  

  <!--명칭변경 //-->    
    <div id="dlayer-itemnameupdateS3" class="pop-layer">
      <div class="pop-container">
        <div class="pop-conts">                
          <div>
            <p class="ctxt mb20">
              <span id="title"></span>&nbsp;<?=_("옵션 항목 명칭 변경")?>.
            </p>
            
            <ul id="optionDataList" style="list-style-type:none;margin:0;padding:0">
              <li id="item">
                <table style="width:450px;">
                  <tr>
                    <th><?=_("변경 항목 입력")?></th>
                    <td>
                      <input type='text' name='f_update_item_nameS3' id='f_update_item_nameS3' size='35' style='width:200px;' />
                    </td>
                  </tr>
	                <tr>
	                  <th><?=_("실제 항목 입력")?></th>
	                  <td>
	                    <span id="ex1S3"></span>
	                    <input type='text' name='f_extra_data1S3' id='f_extra_data1S3' size='5' style='width:30px;' /> <span id="ex2S3">x</span> 
	                    <input type='text' name='f_extra_data2S3' id='f_extra_data2S3' size='5' style='width:30px;' /> <span id="ex3S3">(mm)</span> 
	                  </td>
	                </tr>                  
                </table>
              </li>
            </ul>    
          </div>
        </div>
        <div class="btn-r">
          <a href="#" class="cbtn-c" onclick="saveItemNameUpdateS3();"><?=_("확인")?></a>
          <a href="#" class="cbtn"><?=_("취소")?></a>
        </div>            
      </div>
    </div>      
  <!--항목관리 //-->
  
  <!--가격항목엑셀파일불러오기 //-->
    <div id="dlayer-openfile" class="pop-layer">
        <div class="pop-container">
            <div class="pop-conts">
                <!--content //-->
              <form id="myForm" action="extra_option_goods.w.php?mode=modGoods&goodsno=<?=$_GET[goodsno]?>" enctype="multipart/form-data" method="post">
              <div>
                  <p class="ctxt mb20">
                      <?=_("파일 불러오기 창")?>.<br>
                  </p>
          <div>
                        
            <input type="file" id="excelFile" name="excelFile" />
            <input type="text" id="excelMode" name="excelMode" />
            
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
	//initUploadDiv('{podskind}');

	//수량 사용자 직접 입력 박스 숨기기
	$("input[name^='input_']").each(function(){
		$(this).attr("style","display:none;");
	});
</script>