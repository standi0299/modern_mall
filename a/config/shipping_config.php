<?

include "../_header.php";
include "../_left_menu.php";

// 미오디오,픽스토리만 사용가능
if($GLOBALS[cfg][skin_theme]=="M2" ||  $GLOBALS[cfg][skin_theme]=="P1" || $_SERVER[REMOTE_ADDR]=="210.96.184.229" || (strpos($_SERVER[SERVER_ADDR], "192.168.1.") > -1) ) {

// 배송 순위 및 정책 설정 페이지 20190619 jtkim
$ship_cfg = new M_config();
$ship_cfg_type = $ship_cfg->getConfigInfo($cid,"ship_cfg_type");						//D : 기존형태 , N : 신규 정책, S : 순서선택형태
//$ship_cfg_list = $ship_cfg->getConfigInfo($cid,"ship_cfg_list");						//배송순위 순서
//$ship_cfg_next = $ship_cfg->getConfigInfo($cid,"ship_cfg_next");						//상위정책에 따른 하위정책 여부
$ship_cfg_dc = $ship_cfg->getConfigInfo($cid,"ship_cfg_dc");							//기준금액 결정(상품금액,결제금액)

if(!$ship_cfg_type[value]) $checked[ship_cfg_type]["D"] = "checked";
// if($ship_cfg_list[value]){
// 	$ship_cfg_list_arr = explode(",",$ship_cfg_list[value]);							//M:몰정책 , G:상품정책, R:제작사정책
// }

 $checked[ship_cfg_type][$ship_cfg_type[value]] = "checked";
// $checked[ship_cfg_next][$ship_cfg_next[value]] = "checked";
$checked[ship_cfg_dc][$ship_cfg_dc[value]] = "checked";


?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" id="shipping_config_form" name="fm" method="POST" action="indb.php">
   <input type="hidden" name="mode" value="ship_cfg" />
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("할인시 배송 설정")?></h4>
      </div>

      <div class="panel-body panel-form">
      	 <div class="form-group">
			   <label class="col-md-2 control-label"><?=_("배송 타입 설정")?></label>
			   <div class="col-md-10">
					<input type="radio" class="radio-inline" name="ship_cfg_type" value="D" <?=$checked[ship_cfg_type]["D"]?>> <?=_("기존 배송 정책")?>
					<input type="radio" class="radio-inline" name="ship_cfg_type" value="N" <?=$checked[ship_cfg_type]["N"]?>> <?=_("신규 배송 정책")?><br/><br/>
					<span class="warning">[주의]</span><b><?=_(" 신규 배송정책의 경우 픽스토리,미오디오 스킨만 적용가능합니다.")?></b><br/>
					<span class="notice">[설명]</span> 기존 배송정책(현재 사용중인 배송비 계산방식) <br/>
					1순위 : 상품정책이 개별배송일 경우 <br/>
					2순위 : 몰 정책이 사용중 일 경우 (1순위에 해당되지 않을 시) <br/>
					3순위 : 상품 정책이 일반배송일 때 제작사 정책으로 변경,무료배송일시 무료배송으로 변경 (1,2순위에 해당되지 않을 시)
				</div>
		</div>
		<div class="notView" id="ship_cfg_div">
			<div class="form-group">
				<label class="col-md-2 control-label"><?=_("할인 시 배송비 설정")?></label>
				<div class="col-md-10"><input type="radio" class="radio-inline" name="ship_cfg_dc" value="1" <?=$checked[ship_cfg_dc][1]?>> <?=_("주문기준")?>
						<input type="radio" class="radio-inline" name="ship_cfg_dc" value="0" <?=$checked[ship_cfg_dc][0]?>> <?=_("상품기준")?><br/><br/>
					<span class="notice">예)</span> 조건부 배송설정 (주문금액이 25,000원미만일경우 배송비2500원추가) 설정 한 경우,<br/>
					주문기준(할인 적용 후 주문금액) : 상품금액 28,000원 - 할인금액 5,000원(적립금사용:5,000원) - 배송비 2,500원 = 결제금액 : 25,500원<br/>
					상품기준(할인 적용 전 주문금액) : 상품금액 28,000원 - 할인금액 5,000원(적립금사용:5,000원) - 배송비 0원 = 결제금액 : 23,000원
				</div>
			</div>
		</div>
      </div>
   </div>   
   </form>
   		<div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
      	 	<div class="col-md-10">
				   <button type="submit" class="btn btn-sm btn-success" onClick="check_value()"><?=_("저장")?></button>
      	 	</div>
      	 </div> 
</div>

<script type="text/javascript">

$j(function() {
	
	if($j("input[name=ship_cfg_type]:checked").val()=="N") $j("input[name=ship_cfg_type]:checked").trigger("click");
});

$j("[name=ship_cfg_type]").click(function() {
	if ($j(this).val() == 'N') {
		$j("#ship_cfg_div").slideDown();
	} else {
		$j("#ship_cfg_div").slideUp();
	}
});

function check_value(){
	var used = $("input[name=ship_cfg_type]:checked").val();
	var dc = $("input[name=ship_cfg_dc]").is(":checked");
	if(used == "N" && dc == false){
		//사용함 체크후 설정갑 미입력시
		alert("할인 설정을 선택해주세요");
		return;
	}
	if(!confirm("적용하시겠습니까?")) return;
	$('#shipping_config_form')[0].submit();
};
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>
<? 
//미오디오,픽스토리가 아닐시 이전으로 이동
}else{
	//echo $_SERVER['HTTP_REFERER'];
	//header('Location:$_SERVER["HTTP_REFERER"]');
	echo "<script>
		history.go(-1);
	</script>"; 
	
} 
?>