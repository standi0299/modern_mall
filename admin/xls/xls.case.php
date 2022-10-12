<?

include "../_pheader.php";

/*
GET을 통해 얻는 정보
mode : mysql 테이블의 mod 값을 결정짓는 역할을 한다. 엑셀의 종류
query : xls 파일을 작성할 기초데이터의 쿼리문 이다. 전달방식은 기본쿼리문의 base64_encode 를 준수한다.
*/

# 기초환경 설정 ----------------------------------------------------------------------------------------------------------------

#엑셀의 환경값을 저장할 테이블을 선언한다.
$exm_xls_case		= "exm_excel";

# 엑셀의 조건 필드 (샵아이디)
$cid			= $cid;
if (!$cid) print_xls_err(_("샵아이디가 선언되지 않음"));

# 엑셀의 조건 필드 (엑셀의 종류)
$mode			= $_GET[mode];

if (!$mode) print_xls_err(_("엑셀모드가 선언되지 않음"));

# 엑셀의 컬럼을 저장한 파일의 경로 (파일 내용은 mysql column명을 키로 갖고 명칭을 value로 갖는 형식을 취해야 한다.)
$column_file	= "column/xls.column.".$_GET[mode].".php";
if (!is_file($column_file)) print_xls_err(_("컬럼파일")." : '<b class='red'>$column_file</b>' "._("없음"));

# 엑셀 컬럼파일 참조
include $column_file;

if (!$r_column){
	print_xls_err(_("컬럼정보가 없습니다."));
}

# 에러 출력용 함수
function print_xls_err($str){
	echo "<div style='padding:30px;text-align:center;'>ERROR! ".$str."</div>";
	exit;
}

# /기초환경 설정 ---------------------------------------------------------------------------------------------------------------
//주문리스트, <미입금>리스트에서 엑셀 저장시 mode가 order_original로 넘어와서 order로 바꿔줌 / 14.05.14 / kjm
//$mode가 order_original이면 쿼리문에서 mode를 $mode값이 아닌 $column값으로 찾음
if($mode == 'order_original')
{
    $column = 'order_original';
    $query = "select * from exm_xls_case where cid = '$cid' and mode = '$column' order by `default`";
} elseif($mode == 'order_cash')
{
    $column = 'order_cash';
    $query = "select * from exm_xls_case where cid = '$cid' and mode = '$column' order by `default`";
} else {
    $query = "select * from exm_xls_case where cid = '$cid' and mode = '$mode' order by `default`";
}
$res = $db->query($query);

$loop = array();
while ($data = $db->fetch($res)){
	$loop[$data[no]] = $data;
	if (($data['default']==-1 && !$_GET[no]) || $_GET[no]==$data[no]){
		$_GET[no] = $data[no];
		$default = $data;
	}
}
$b_column = array();
if ($_GET[no]){
	$b_column = array_notnull(explode("|@|",$default[columns]));
}
$base_column = array_diff(array_keys($r_column),$b_column);

$selected[no][$_GET[no]] = "selected";

$_GET[addquery_] = urldecode(base64_decode($_GET[addquery]));
$_GET[addquery_] = explode("&",$_GET[addquery_]);
$addquery = array();
foreach ($_GET[addquery_] as $k=>$v){
	$v = array_map("trim",explode("=",$v));
	if (!$v[0] || !$v[1]) continue;
	$addquery[$v[0]] = $v[1];
}

?>

<div class="stit"><?=_("엑셀다운로드")?></div>

<form method="post" action="indb.php" onsubmit="return form_chk_xls(this)" name="fm">
<input type="hidden" name="mode" value="xls"/>
<input type="hidden" name="mode_opt" id="mode_opt_input"/>
<input type="hidden" name="case" value="<?=$mode?>"/>
<input type="hidden" name="column" value="<?=$column?>"/>
<input type="hidden" name="query" value="<?=$_GET[query]?>"/>
<!--### form 전송 취약점 개선 20160128 by kdk-->
<input type="hidden" name="pod_signed" value="<?=$_GET[pod_signed]?>">
<input type="hidden" name="pod_expired" value="<?=$_GET[pod_expired]?>">

<? foreach ($addquery as $k=>$v){ ?>
<input type="hidden" name="<?=$k?>" value="<?=$v?>"/>
<? } ?>

<div>
<table class="tb1">
<tr>
	<th><?=_("엑셀출력양식 선택")?></th>
	<td style="border-right:0">
	<select name="no" id="no_select">
		<option value="" <?=$selected[no]['new']?>/><?=_("새로운 양식")?>
		<? foreach ($loop as $k=>$v){ ?>
		<option value="<?=$v[no]?>" <?=$selected[no][$v[no]]?>/><?=$v[name]?>
		<? } ?>
	</select>
	<div class="desc" id="update_div">
	<input type="checkbox" name="save" value="1" style="width:12px;vertical-align:middle;" id="no_chk"/><span style="vertical-align:middle;"><?=_("변경하신 사항을 추가 / 업데이트 하시겠습니까?")?></span>
	</div>
	<div class="desc" id="default_div">
	<input type="checkbox" name="default" value="-1" style="width:12px;vertical-align:middle;" <? if ($default['default']=="-1"){?>checked<?}?>/><span style="vertical-align:middle;"><?=_("기본양식으로 설정")?></span>
	</div>
	<div class="desc" id="new_ins_div"><input type="text" name="name" label='<?=_("양식명")?>' required disabled/> <?=_("새로운 양식명을 입력")?></div>
	<div class="desc" id="delete_div">
	<input type="checkbox" name="delete" value="1" style="width:12px;vertical-align:middle;" id="no_del"/><span style="vertical-align:middle;" class="red"><?=_("선택된 양식을 삭제")?></span>
	</div>
	</td>
	<td style="padding:0;border:0;font-size:0;" valign="bottom" align="right">
	<input type="image" src="../img/bt_submit_l.png" id="bt_mod" onclick="$j('#mode_opt_input').val('')"/>
	<input type="image" src="../img/bt_excel_l.png" onclick="$j('#mode_opt_input').val('download')"/>
	</td>
</tr>
<? if($mode == "order_original" || $mode == "order"){ ?>
<tr>
	<th>셀 병합 해제</th>
	<td><input type="checkbox" name="rowspan_chk" value="1" style="width:12px;vertical-align:middle;"><span style="vertical-align:middle;">체크시 셀 병합이 해제 됩니다.</span></td>
</tr>
<? } ?>
<tr>
	<th><?=_("설명")?></th>
	<td colspan="2" class="red">
	<div class="small"><?=_("확인 버튼 : 설정사항을 추가/수정/삭제 하게됩니다.")?></div>
	<div class="desc"><?=_("엑셀저장 버튼 : 확인 버튼 + 엑셀데이터다운로드가 이루어 집니다.")?></div>
	</td>
</tr>
</table>

</div>

<div id="column_container">

<div id="column_select_div_left" class="column_select_div">
	<div class="column_select_title"><?=_("전체항목")?></div>
	<select multiple="true" class="column_select" id="column_select_left">
	<? foreach ($base_column as $v){ ?>
	<option value="<?=$v?>"><?=$r_column[$v]?>
	<? } ?>
	</select>
</div>

<div id="column_bt_div">
<img src="../img/bt_leftedge_s.png" mode="del" obj="all"/>
<img src="../img/bt_left_s.png" mode="del" obj="selected"/>
<img src="../img/bt_right_s.png" mode="add" obj="selected"/>
<img src="../img/bt_rightedge_s.png" mode="add" obj="all"/>
</div>

<div id="column_select_div_right" class="column_select_div">
<div class="column_select_title"><?=_("엑셀출력항목")?></div>
<select name="columns[]" multiple="true" class="column_select" label='<?=_("엑셀출력항목")?>' id="column_select" style="height:268px">
<? foreach ($b_column as $v){ ?>
<option value="<?=$v?>"><?=$r_column[$v]?>
<? } ?>
</select>
<div id="dirbt_div" align="center">
<img src="../img/bt_top_s.png" direct="top"/>
<img src="../img/bt_up_s.png" direct="-1"/>
<img src="../img/bt_down_s.png" direct="1"/>
<img src="../img/bt_bottom_s.png" direct="bottom"/>
</div>
</div>

</div>

</form>

<script>
$j(window).load(function(){
	$j("#no_chk").click(function(){
		form_chk_mode();
	});
	$j("#no_select").change(function(){
		
		var no = $j(this).val();
		if (!no){
			no = "add";
		}
		//<!--### form 전송 취약점 개선 20160128 by kdk-->
		location.href = "xls.case.php?mode=<?=$_GET[mode]?>&query=<?=$_GET[query]?>&pod_signed=<?=$_GET[pod_signed]?>&pod_expired=<?=$_GET[pod_expired]?>&no="+no;
		return;
		form_chk_mode();
	});
	$j("#no_del").click(function(){
		if ($j(this).attr("checked")){
			$j("#new_ins_div").hide();
			$j("#default_div").hide();
			$j("#update_div").hide();
			$j("#column_select").attr("disabled",true);
			$j("#bt_mod").show();
		} else {
			form_chk_mode();
			$j("#update_div").show();
			$j("#column_select").attr("disabled",false);
		}
	});
	form_chk_mode();

	/* 좌우 샐렉트 박스의 연계액션 */
	$j("img","#column_bt_div").click(function(){

		var from;
		var to;
		var opts;
		var mode = $j(this).attr("mode");
		var obj = $j(this).attr("obj");
	
		switch (mode){
			case "add":
				from = $j("#column_select_left");
				to = $j("#column_select");
				switch (obj){
					case "all":
						opts = $j("option",from);
						break;
					case "selected":
						opts = $j("option:selected",from);
						break;
				}

				break;

			case "del":
				from = $j("#column_select");
				to = $j("#column_select_left");
				switch (obj){
					case "all":
						opts = $j("option",from);
						break;
					case "selected":
						opts = $j("option:selected",from);
						break;
				}

				break;
		}

		if (!opts.length){
			return;
		}

		opts.each(function(){
			var chk = $j("option[value="+$j(this).val()+"]",to).length;
			if (!chk){
				$j(this).appendTo(to);
            }
	   });
    });
	/* 선택된 셀렉트 박스의 액션 */
	$j("img","#dirbt_div").click(function(){

		var opt = $j("option:selected","#column_select");
		if (!opt.length){
			alert('<?=_("선택사항이 없습니다")?>');
			return;
		}
		if (opt.length > 1){
			alert('<?=_("이동하실 항목을 하나만 선택한 상태에서 시도해주세요.")?>');
			return;
		}

		var direct = $j(this).attr("direct");
		var idx = $j("option","#column_select").index(opt);
		var dest;

		switch (direct){
			case "top":
			
				dest = 0;
				break;

			case "-1":

				dest = idx -1;
				if (dest < 0) dest = 0;

				break;

			case "1":

				dest = idx +1;
				if (dest < 0) dest = 0;

				break;

			case "bottom":

				dest = $j("option","#column_select").index($j("option:last","#column_select"));
				
				break;
		}

		switch (direct){
			case "top": case "-1":
				if (idx!=dest){
					opt.insertBefore($j("option:eq("+dest+")","#column_select"));
				}
				break;
			case "bottom": case "1":
				if (idx!=dest){
					opt.insertAfter($j("option:eq("+dest+")","#column_select"));
				}
				break;
		}

	});

});

function form_chk_mode(){
	if ($j("#no_chk").attr("checked") && !$j("#no_select").val()){
		$j("#new_ins_div").show();
		$j("#default_div").show();
		$j("#bt_mod").show();
		$j("input","#new_ins_div").attr("disabled",false);
	} else if ($j("#no_chk").attr("checked")){
		$j("#new_ins_div").hide();
		$j("#default_div").show();
		$j("#bt_mod").show();
		$j("input","#new_ins_div").attr("disabled",true);
	} else {
		$j("#new_ins_div").hide();
		$j("#default_div").hide();
		$j("#bt_mod").hide();
		$j("input","#new_ins_div").attr("disabled",true);
	}

	if ($j("#no_select").val()){
		$j("#delete_div").show();
	} else {
		$j("#delete_div").hide();
	}
}

function form_chk_xls(fm){
	if (!form_chk(fm)){
		return false;
	}
	if (!$j("#column_select").attr("disabled") && !$j("option","#column_select").length){
		alert('<?=_("엑셀출력항목을 지정해주세요")?>');
		return false;
	}

	$j("option","#column_select").attr("selected",true);
	return true;
}

</script>

<style>
#new_ins_div,#default_div {display:none;}
#column_container {position:relative;/*background:red;*/}
#column_bt_div {font-size:0;padding-top:100px;margin:0 auto; width:31px;}
#column_bt_div img {display:block;margin-bottom:10px;cursor:pointer;}
.column_select_div {position:absolute;width:150px;padding:3px;}
.column_select_title {color:#FFFFFF;font:8pt 돋움;padding:5px;}
.column_select {width:150px;height:300px;padding:5px;}
#column_select_div_left {top:10px;left:50px;background:black;}
#column_select_div_right {top:10px;right:50px;background:#336699;}
#dirbt_div {font-size:0;background:#FFFFFF;margin:1px;padding:5px;}
#dirbt_div img{margin:0 5px;cursor:pointer;}
</style>