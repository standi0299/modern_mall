<?

include "../_header.php";
include "../_left_menu.php";

$m_config = new M_config();
$r_paymethod = array("c"=>"신용카드", "o"=>"계좌이체", "v"=>"가상계좌", "h"=>"휴대폰");
$r_lgd_custom_skin = array("red", "blue", "cyan", "green", "yellow");

//입금계좌
$addWhere = "where cid='$cid'";
$orderby = "order by bankno desc";
$data = $m_config->getBankInfo($cid, '', $addWhere, $orderby);

if ($cfg[pg][paymethod]) {
	foreach ($cfg[pg][paymethod] as $k=>$v) {
		$checked[paymethod][$v] = "checked";
	}
}

if ($cfg[pg][e_paymethod]) {
	foreach ($cfg[pg][e_paymethod] as $k=>$v) {
		$checked[e_paymethod][$v] = "checked";
	}
}

if (!$cfg[pg][paymethod]) $cfg[pg][paymethod] = array();

$selected[module][$cfg[pg][module]] = "selected";
$selected[kcp_skin_indx][$cfg[pg][kcp_skin_indx]] = "selected";
$selected[lgd_custom_skin][$cfg[pg][lgd_custom_skin]] = "selected";

if (!$cfg[pg][cash_receipt]) $checked[cash_receipt][0] = "checked";
if (!$cfg[pg][escrow]) $checked[escrow][$cfg[pg][module]][0] = "checked";
if (!$cfg[pg][kcp_noint]) $checked[kcp_noint][0] = "checked";

$checked[cash_receipt][$cfg[pg][cash_receipt]+0] = "checked";
$checked[escrow][$cfg[pg][module]][$cfg[pg][escrow]+0] = "checked"; //kcp와 이니시스를 동시에 같은 radio 이름을 써서 문제가 발생. PG사별 check 값으로 저장한다.      20141209    chunter 
$checked[kcp_noint][$cfg[pg][kcp_noint]+0] = "checked";

if (in_array("b", $cfg[pg][paymethod])) $checked[paymethod_bank][1] = "checked";
else $checked[paymethod_bank][0] = "checked";

if (in_array("t", $cfg[pg][paymethod])) $checked[paymethod_credit][1] = "checked";
else $checked[paymethod_credit][0] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return submitContents(this)&&confirm('수정사항은 쇼핑몰 운영에 즉시 반영됩니다.\r\n적용하시겠습니까?')">
   <input type="hidden" name="mode" value="pg" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("기본설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("무통장입금 사용여부")?></label>
      	 	<div class="col-md-3">
      	 		<input type="checkbox" data-render="switchery" data-theme="blue" name="paymethod_bank" value="1" <?=$checked[paymethod_bank][1]?>>
      	 	</div>
      	 	<label class="col-md-2 control-label"><?=_("현금영수증 사용여부")?></label>
      	 	<div class="col-md-3">
      	 		<input type="checkbox" data-render="switchery" data-theme="blue" name="cash_receipt" value="1" <?=$checked[cash_receipt][1]?>>
      	 	</div>
      	 </div>
      	 
      	 <div class="form-group">
            <label class="col-md-2 control-label"><?=_("신용거래 사용여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="paymethod_credit" value="1" <?=$checked[paymethod_credit][1]?>>
            </div>
         </div>
      </div>
   </div>
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("무통장 입금계좌 설정")?></h4>
      </div>
      
      <div class="panel-body">
         <div class="table-responsive">
            <table id="data-table" class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th><?=_("번호")?></th>
                     <th><?=_("입금계좌")?></th>
                     <th><?=_("수정")?></th>
                     <th><?=_("삭제")?></th>
                  </tr>
               </thead>
               <tbody>
                  <? foreach ($data as $k=>$v) { ?>
                  <tr align="center">
                     <td width="80"><?=count($data)-$k?></td>
                     <td align="left"><?=$v[bankinfo]?></td>
                     <td width="50">
                        <button type="button" class="btn btn-xs btn-primary" onclick="popup('bankinfo_popup.php?bankno=<?=$v[bankno]?>', 630, 405)">수정</button>
                     </td>
                     <td width="50">
                        <a href="indb.php?mode=del_bankinfo&bankno=<?=$v[bankno]?>" onclick="return confirm('<?=_("정말로 삭제하시겠습니까?")?>')">
                           <span class="btn btn-xs btn-danger">삭제</span>
                        </a>
                     </td>
                  </tr>
                  <? } ?>
               </tbody>
            </table>
               
            <div class="form-group">
               <div class="col-md-12">
                  <button type="button" class="btn btn-sm btn-success" onclick="popup('bankinfo_popup.php', 630, 405)"><?=_("계좌추가")?></button>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("PG설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("PG사선택")?></label>
      	 	<div class="col-md-10 form-inline">
      	 		<select name="module" class="form-control" required onchange="swap_module(this.value);">
      	 			<option value="no"><?=_("PG사용안함")?></option>
      	 			<? foreach ($r_pg_kind as $key=>$value) { ?>
      	 				<option value="<?=$key?>" <?=$selected[module][$key]?>><?=$value?></option>
      	 			<? } ?>	
      	 		</select>
      	 		<a href="http://kcp.co.kr/technique.info.do" class="pg_url kcp_url" target="_blank">&nbsp;KCP 바로가기</a>
      	 		<a href="http://www.inicis.com" class="pg_url inicis_url" target="_blank">&nbsp;이니시스 바로가기</a>
      	 		<div class="pg_url kcp_url"><span class="notice">[설명]</span> KCP 정산관리자 페이지 바로가기 : <a href="https://admin8.kcp.co.kr/" target="_blank">https://admin8.kcp.co.kr</a></div>
      	 		<div class="pg_url kcp_url"><span class="notice">[설명]</span> KCP 기술지원 페이지 바로가기 : <a href="http://kcp.co.kr/technique.info.do" target="_blank">http://kcp.co.kr/technique.info.do</a></div>
      	 		<div class="pg_url inicis_url"><span class="notice">[설명]</span> 이니시스 정산관리자 페이지 바로가기 : <a href="https://iniweb.inicis.com/" target="_blank">https://iniweb.inicis.com</a></div>
      	 		<div class="pg_url inicis_url"><span class="notice">[설명]</span> 이니시스 기술지원 페이지 바로가기 : <a href="http://www.inicis.com/support_01.jsp" target="_blank">http://www.inicis.com/support_01.jsp</a></div>
      	 	</div>
      	 </div>
      	 
      	 <div class="swapbox" id="kcp_box">
	      	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("PG결제수단")?></label>
	            <div class="col-md-10 form-inline">
	               <? foreach ($r_paymethod as $key=>$value) { ?>
	               	  <label class="checkbox-inline">
	               	  	<input type="checkbox" name="paymethod[]" value="<?=$key?>" <?=$checked[paymethod][$key]?>><?=$value?>
                   	  </label>
	               <? } ?>
                   <div><span class="warning">[주의]</span> 반드시 KCP와 계약한 결제수단만 체크하세요.</div>
	            </div>
	        </div>
	         
	        <div class="form-group">
            	<label class="col-md-2 control-label"><?=_("KCP Code")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="code" value="<?=$cfg[pg][code]?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("KCP Key")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="key" value="<?=$cfg[pg]['key']?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("할부허용기간")?></label>
            	<div class="col-md-10 form-inline">
               		<input type="text" class="form-control" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" size="2" maxlength="2" type2="number" required> 개월까지
               		<div>
               			<span class="warning">[주의]</span> 할부 선택은 결제금액이 50000원 이상일 경우에만 가능하고 50000원 미만의 금액은 일시불로만 표기됩니다.
               			<div class="textIndent">예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능</div>
               		</div>
               		<div><span class="notice">[설명]</span> 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다. (0 ~ 18 까지 설정 가능)</div>
            	</div>
         	</div>
         	
         	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("가상계좌 입금통보 URL")?></label>
	            <div class="col-md-10 form-inline">
	               <b>http://<?=$_SERVER[HTTP_HOST]?>/pg/kcp/pay_return.php</b>
	               <div><span class="warning">[주의]</span> KCP 가맹점관리자 페이지 > 상점정보관리 > 정보변경 > 공통URL정보 > 공통URL 변경후에 위주소를 반드시 넣으셔야 합니다. [인코딩설정 : UTF-8]</div>
	            </div>
	        </div>
	         
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("에스크로 사용여부")?></label>
	            <div class="col-md-10 form-inline">
	               <input type="radio" class="radio-inline" name="escrow" value="0" <?=$checked[escrow][kcp][0]?>> 사용안함
	               <input type="radio" class="radio-inline" name="escrow" value="1" <?=$checked[escrow][kcp][1]?>> 사용
	               <div><span class="warning">[주의]</span> 반드시 KCP와의 계약내용과 일치하게 설정해주세요.</div>
	            </div>
	        </div>
	         
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("에스크로 결제수단")?></label>
	            <div class="col-md-10 form-inline">
	               <label class="checkbox-inline">
	               	  <input type="checkbox" name="e_paymethod[]" value="ve" <?=$checked[e_paymethod][ve]?>>가상계좌
                   </label>
	               <div><span class="warning">[주의]</span> 반드시 KCP와 계약한 결제수단만 체크하세요.</div>
	            </div>
	        </div>
	         
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("PG별도계약 무이자")?></label>
	            <div class="col-md-10 form-inline">
	               <input type="radio" class="radio-inline" name="kcp_noint" value="0" <?=$checked[kcp_noint][0]?>> 사용안함
	               <input type="radio" class="radio-inline" name="kcp_noint" value="1" <?=$checked[kcp_noint][1]?>> 사용<p>
	               <div id="kcp_noint_str" class="form-inline notView">
	      	 			무이자코드 : <input type="text" class="form-control" name="kcp_noint_str" value="<?=$cfg[pg][kcp_noint_str]?>"> 원 
	      	 		</div>	
	            </div>
	        </div>
	         
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("스킨타입")?></label>
	            <div class="col-md-10 form-inline">
	               <select class="form-control" name="kcp_skin_indx">
	               	  <? for ($i=1;$i<=7;$i++) { ?>
	               	  	<option value="<?=$i?>" <?=$selected[kcp_skin_indx][$i]?>>SKIN.<?=$i?></option>
	               	  <? } ?>
	               </select>
	            </div>
	        </div>
	         
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("로고이미지")?></label>
	            <div class="col-md-10 form-inline">
	               <input type="file" class="form-control" name="kcp_site_logo" size="40">
	               <div><span class="warning">[주의]</span> <b>150px * 50px</b> 이하 사이즈의 파일로 업로드해주세요.</div>
	            </div>
	        </div>
         </div>
         
         <div class="swapbox" id="inicis_box">
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("상점아이디")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="mid" value="<?=$cfg[pg][mid]?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("에스크로 상점아이디")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="mid_e" value="<?=$cfg[pg][mid_e]?>">
            	</div>
         	</div>
         	
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("키패스워드")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="admin" value="<?=$cfg[pg][admin]?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("PG결제수단")?></label>
	            <div class="col-md-10 form-inline">
	               <? foreach ($r_paymethod as $key=>$value) { ?>
	               	  <label class="checkbox-inline">
	               	  	<input type="checkbox" name="paymethod[]" value="<?=$key?>" <?=$checked[paymethod][$key]?>><?=$value?>
                   	  </label>
	               <? } ?>
                   <div><span class="warning">[주의]</span> 반드시 이니시스와 계약한 결제수단만 체크하세요.</div>
	            </div>
	        </div>
	        
	        <div class="form-group">
            	<label class="col-md-2 control-label"><?=_("할부허용기간")?></label>
            	<div class="col-md-10 form-inline">
               		<input type="text" class="form-control" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" size="2" maxlength="2" type2="number" required> 개월까지
               		<div>
               			<span class="warning">[주의]</span> 할부 선택은 결제금액이 50000원 이상일 경우에만 가능하고 50000원 미만의 금액은 일시불로만 표기됩니다.
               			<div class="textIndent">예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능</div>
               		</div>
               		<div><span class="notice">[설명]</span> 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다. (0 ~ 18 까지 설정 가능)</div>
            	</div>
         	</div>
         	
         	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("가상계좌 입금통보 URL")?></label>
	            <div class="col-md-10 form-inline">
	               <b>http://<?=$_SERVER[HTTP_HOST]?>/pg/INIpay50/vacctionput.php</b>
	               <div><span class="warning">[주의]</span> 이니시스 <a href="https://iniweb.inicis.com" target="_blank">상점관리자 페이지</a> > 거래내역 > 거래조회 > 가상계좌 > 입금통보방식선택 메뉴에서 위주소를 반드시 넣으셔야 합니다.</div>
	            </div>
	        </div>
	        
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("에스크로 사용여부")?></label>
	            <div class="col-md-10 form-inline">
	               <input type="radio" class="radio-inline" name="escrow" value="0" <?=$checked[escrow][inicis][0]?>> 사용안함
	               <input type="radio" class="radio-inline" name="escrow" value="1" <?=$checked[escrow][inicis][1]?>> 사용
	               <div><span class="warning">[주의]</span> 반드시 이니시스와의 계약내용과 일치하게 설정해주세요.</div>
	            </div>
	        </div>
	         
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("에스크로 결제수단")?></label>
	            <div class="col-md-10 form-inline">
	               <label class="checkbox-inline">
	               	  <input type="checkbox" name="e_paymethod[]" value="ve" <?=$checked[e_paymethod][ve]?>>가상계좌
                   </label>
	               <div><span class="warning">[주의]</span> 반드시 이니시스와 계약한 결제수단만 체크하세요.</div>
	            </div>
	        </div>
         </div>
         
         <div class="swapbox" id="inipaystdweb_box">
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("상점아이디")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="mid" value="<?=$cfg[pg][mid]?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("에스크로 상점아이디")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="mid_e" value="<?=$cfg[pg][mid_e]?>">
            	</div>
         	</div>
         	
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("Sign Key")?></label>
            	<div class="col-md-10 form-inline">
               		<input type="text" class="form-control" name="inipay_sign_key" value="<?=$cfg[pg][inipay_sign_key]?>" size="40" required>
               		<div><span class="notice">[설명]</span> signkey 발급 방법 : 관리자 페이지의 상점정보 > 계약정보 > 부가정보의 웹결제 signkey 생성조회버튼 클릭후 팝업창에서 생성버튼 클릭후 해당 값을 반영하시기 바랍니다.</div>
            	</div>
         	</div>
         	
         	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("PG결제수단")?></label>
	            <div class="col-md-10 form-inline">
	               <? foreach ($r_paymethod as $key=>$value) { ?>
	               	  <label class="checkbox-inline">
	               	  	<input type="checkbox" name="paymethod[]" value="<?=$key?>" <?=$checked[paymethod][$key]?>><?=$value?>
                   	  </label>
	               <? } ?>
                   <div><span class="warning">[주의]</span> 반드시 이니시스와 계약한 결제수단만 체크하세요.</div>
	            </div>
	        </div>
	        
	        <div class="form-group">
            	<label class="col-md-2 control-label"><?=_("할부허용기간")?></label>
            	<div class="col-md-10 form-inline">
               		<input type="text" class="form-control" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" size="2" maxlength="2" type2="number" required> 개월까지
               		<div>
               			<span class="warning">[주의]</span> 할부 선택은 결제금액이 50000원 이상일 경우에만 가능하고 50000원 미만의 금액은 일시불로만 표기됩니다.
               			<div class="textIndent">예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능</div>
               		</div>
               		<div><span class="notice">[설명]</span> 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다. (0 ~ 18 까지 설정 가능)</div>
            	</div>
         	</div>
         	
         	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("가상계좌 입금통보 URL")?></label>
	            <div class="col-md-10 form-inline">
	               <b>http://<?=$_SERVER[HTTP_HOST]?>/pg/INIPayStdWeb/vacctionput.php</b>
	               <div><span class="warning">[주의]</span> 이니시스 <a href="https://iniweb.inicis.com" target="_blank">상점관리자 페이지</a> > 거래내역 > 거래조회 > 가상계좌 > 입금통보방식선택 메뉴에서 위주소를 반드시 넣으셔야 합니다.</div>
	            </div>
	        </div>
	        
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("에스크로 사용여부")?></label>
	            <div class="col-md-10 form-inline">
	               <input type="radio" class="radio-inline" name="escrow" value="0" <?=$checked[escrow][inipaystdweb][0]?>> 사용안함
	               <input type="radio" class="radio-inline" name="escrow" value="1" <?=$checked[escrow][inipaystdweb][1]?>> 사용
	               <div><span class="warning">[주의]</span> 반드시 이니시스와의 계약내용과 일치하게 설정해주세요.</div>
	            </div>
	        </div>
	         
	        <div class="form-group">
	            <label class="col-md-2 control-label"><?=_("에스크로 결제수단")?></label>
	            <div class="col-md-10 form-inline">
	               <label class="checkbox-inline">
	               	  <input type="checkbox" name="e_paymethod[]" value="ve" <?=$checked[e_paymethod][ve]?>>가상계좌
                   </label>
	               <div><span class="warning">[주의]</span> 반드시 이니시스와 계약한 결제수단만 체크하세요.</div>
	            </div>
	        </div>
         </div>
         
         <div class="swapbox" id="lg_box">
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("상점아이디")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="lgd_mid" value="<?=$cfg[pg][lgd_mid]?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("상점키")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="lgd_mertkey" value="<?=$cfg[pg][lgd_mertkey]?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("스킨타입")?></label>
	            <div class="col-md-10 form-inline">
	               <select class="form-control" name="lgd_custom_skin">
	               	  <? foreach ($r_lgd_custom_skin as $value) { ?>
	               	  	<option value="<?=$value?>" <?=$selected[lgd_custom_skin][$value]?>><?=$value?></option>
	               	  <? } ?>
	               </select>
	            </div>
	        </div>
         	
         	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("PG결제수단")?></label>
	            <div class="col-md-10 form-inline">
	               <? foreach ($r_paymethod as $key=>$value) { ?>
	               	  <label class="checkbox-inline">
	               	  	<input type="checkbox" name="paymethod[]" value="<?=$key?>" <?=$checked[paymethod][$key]?>><?=$value?>
                   	  </label>
	               <? } ?>
                   <div><span class="warning">[주의]</span> 반드시 LG유플러스와 계약한 결제수단만 체크하세요.</div>
	            </div>
	        </div>
	        
	        <div class="form-group">
            	<label class="col-md-2 control-label"><?=_("할부허용기간")?></label>
            	<div class="col-md-10 form-inline">
               		<input type="text" class="form-control" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" size="2" maxlength="2" type2="number" required> 개월까지
               		<div>
               			<span class="warning">[주의]</span> 할부 선택은 결제금액이 50000원 이상일 경우에만 가능하고 50000원 미만의 금액은 일시불로만 표기됩니다.
               			<div class="textIndent">예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능</div>
               		</div>
               		<div><span class="notice">[설명]</span> 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다. (0 ~ 18 까지 설정 가능)</div>
            	</div>
         	</div>
         </div>
         
         <div class="swapbox" id="smartxpay_box">
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("상점아이디")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="lgd_mid" value="<?=$cfg[pg][lgd_mid]?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
            	<label class="col-md-2 control-label"><?=_("상점키")?></label>
            	<div class="col-md-3">
               		<input type="text" class="form-control" name="lgd_mertkey" value="<?=$cfg[pg][lgd_mertkey]?>" required>
            	</div>
         	</div>
         	
         	<div class="form-group">
	            <label class="col-md-2 control-label"><?=_("PG결제수단")?></label>
	            <div class="col-md-10 form-inline">
	               <? foreach ($r_paymethod as $key=>$value) { ?>
	               	  <label class="checkbox-inline">
	               	  	<input type="checkbox" name="paymethod[]" value="<?=$key?>" <?=$checked[paymethod][$key]?>><?=$value?>
                   	  </label>
	               <? } ?>
                   <div><span class="warning">[주의]</span> 반드시 LG유플러스와 계약한 결제수단만 체크하세요.</div>
	            </div>
	        </div>
	        
	        <div class="form-group">
            	<label class="col-md-2 control-label"><?=_("할부허용기간")?></label>
            	<div class="col-md-10 form-inline">
               		<input type="text" class="form-control" name="quotaopt" value="<?=$cfg[pg][quotaopt]?>" size="2" maxlength="2" type2="number" required> 개월까지
               		<div>
               			<span class="warning">[주의]</span> 할부 선택은 결제금액이 50000원 이상일 경우에만 가능하고 50000원 미만의 금액은 일시불로만 표기됩니다.
               			<div class="textIndent">예) value 값을 "5" 로 설정했을 경우 => 카드결제시 결제창에 일시불부터 5개월까지 선택가능</div>
               		</div>
               		<div><span class="notice">[설명]</span> 할부옵션 : Payplus Plug-in에서 카드결제시 최대로 표시할 할부개월 수를 설정합니다. (0 ~ 18 까지 설정 가능)</div>
            	</div>
         	</div>
         </div>
      </div>
   </div>
   
   <div class="row">
   	  <div class="col-md-12">
   	  	 <p class="pull-right">
   	  	 	<button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("저장")?></button>
   	  	 	<button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
   	  	 </p>
   	  </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>

<script type="text/javascript" src="../assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/form-slider-switcher.demo.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
});

$j(function() {
	swap_module("<?=$cfg[pg][module]?>");
	
	$j("[name=kcp_noint][value=<?=$cfg[pg][kcp_noint]+0?>]").trigger("click");
	
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});

function submitContents(fm) {
	/* 선택된 결제수단 */
	if (!($j("[name=paymethod_bank][value=1][checked=true]").length + $j("[name=paymethod_credit][value=1][checked=true]").length + $j("input[type=checkbox][name=paymethod[]][checked=true]").length)) {
		alert("하나 이상의 결제수단이 선택되어야합니다.");
		return false;
	}

	if (!form_chk(fm)) {
		return false;
	}

	if (fm["escrow"][1].checked == true) {
		var obj = fm["e_paymethod[]"];
		var ret = false;
		
		for (var i=0;i<obj.length;i++) {
			if (obj[i].checked == true){
				ret = true;
			}
		}
		
		if (!ret) {
			alert("에스크로가 활성화 되어있습니다.하나이상의 에스크로 결제수단을 선택해주세요.");
			return false;
		}
	}
	
	return true;
}

function swap_module(module) {
	$j(".swapbox").hide();
	$j("input",".swapbox").attr("disabled", true);

	$j("#" + module + "_box").show();
	$j("input","#" + module + "_box").attr("disabled", false);
	
	$j(".pg_url").hide();
	$j("." + module + "_url").show();
}

$j("[name=kcp_noint]").click(function() {
	if ($j(this).val() == 1) {
		$j('#kcp_noint_str').show();
	} else {
		$j('#kcp_noint_str').hide();
	}
});
</script>

<? include "../_footer_app_exec.php"; ?>