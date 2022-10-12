
/* 후가공 옵션 박스 추가 */
function add_addoptbox(){

	//판매중인 몰 확인 2014.08.26 kdk
	var chk = checkGoodsMallUseFlag();
	if(!chk) return false;

	var div = document.createElement("div");
	$j(div).attr("class","addoptbox_div");
	$j(div).appendTo("#addoptbox_div");
	var addoptbox_idx = $j(".addoptbox_div").index(div);
	$j(div).attr("addoptbox_idx",addoptbox_idx);
	$j(div).attr("style","width:100%;");

	var tb = document.createElement("table");
	$j(tb).attr("class","addoptbox");
	$j(tb).attr("style","width:99%;");
	
	var tr = document.createElement("tr");
	$j(tr).appendTo(tb);

	var td = document.createElement("td");
	var selectTag = "<select name=\"addopt_group_view["+addoptbox_idx+"]\"><option value=\"Y\">사용</option><option value=\"N\">" + tls("미사용") + "</option></select>";
	//selectTag += "<select name=\"addopt_price_view["+addoptbox_idx+"]\"><option value=\"CNT\"><?=$r_est_item_price_type[CNT]?></option><option value=\"TIME\"><?=$r_est_item_price_type[TIME]?></option><option value=\"SIZE\"><?=$r_est_item_price_type[SIZE]?></option></select>";	
	$j(td).html(selectTag);		
		
	
	$j(td).appendTo(tr);
	$j(td).attr("width","180");
	$j(td).attr("align","center");

	var th = document.createElement("th");
	$j(th).html(tls("옵션명"));
	$j(th).appendTo(tr);

	var td = document.createElement("td");
	$j(td).html("<input type=\"text\" name=\"addopt_group_name["+addoptbox_idx+"]\" class=\"w200\" pt=\"_pt_txt\" required/>");
	$j(td).appendTo(tr);

	var td = document.createElement("td");
	//$j(td).html("<img src=\"../img/bt_optadd_s.png\" class=\"hand absmiddle\" onclick=\"add_addopt(this)\"><img src=\"../img/bt_del_group_s.png\" class=\"hand absmiddle\" onclick=\"remove_addoptbox(this)\"/><img src=\"../img/bt_apply_s.png\" class=\"hand absmiddle\" onclick=\"save_addoptbox(this)\"/>");	
	
	$j(td).html("<input type=\"image\" src=\"../img/bt_set_item_s.png\" style=\"vertical-align: middle; width: 51px; height: 25px;\" onclick=\"saveEtcOptionData(this);return false;\" /><input type=\"image\" src=\"../img/bt_optadd_s.png\" style=\"vertical-align: middle; width: 51px; height: 25px;\" onclick=\"add_addopt(this);return false;\" /><input type=\"image\" src=\"../img/bt_del_group_s.png\" style=\"vertical-align: middle; width: 51px; height: 25px;\" onclick=\"remove_addoptbox(this);return false;\" />");
	
	//$j(td).html("<button onclick=\"saveEtcOptionData(this);return false;\">적용</button><button onclick=\"add_addopt(this);return false;\">옵션추가</button><button onclick=\"remove_addoptbox(this);return false;\">묶음삭제</button>");
	
	$j(td).appendTo(tr);	
	$j(td).attr("width","160");
	$j(td).attr("align","center");
	$j(tb).appendTo($j(div));

	var tb = document.createElement("table");
	$j(tb).attr("class","addoptbox");
	$j(tb).attr("style","width:99%;");
	$j(tb).appendTo($j(div));
	
	add_addopt(tb);

	_pt_set();

}

// 추가옵션 묶음 삭제
function remove_addoptbox(obj){
	$j(obj).closest(".addoptbox_div").remove();
}

// 추가옵션 추가
function add_addopt(obj){
	var addoptbox_idx = $j(obj).closest(".addoptbox_div").attr("addoptbox_idx");
	
	var tb = $j(obj).closest(".addoptbox_div").children(".addoptbox:last");
	var tr = document.createElement("tr");
	$j(tr).appendTo(tb);
	$j(tr).attr("align","center");
	
	var td = document.createElement("td");
	$j(td).attr("width","180");
	$j(td).appendTo(tr);

	//var td = document.createElement("td");
	//$j(td).html("<select name=\"addopt_view["+addoptbox_idx+"][]\"><option value=\"0\">노출</option><option value=\"1\">숨김</option></select>");
	//$j(td).attr("align","center");
	//$j(td).appendTo(tr);

	var th = document.createElement("th");
	$j(th).html(tls("항목명"));
	$j(th).appendTo(tr);
	
	var td = document.createElement("td");
	$j(td).html("<input type=\"text\" name=\"addopt_name["+addoptbox_idx+"][]\"/>");
	$j(td).attr("align","left");
	$j(td).appendTo(tr);

	var td = document.createElement("td");
	$j(td).html("<img src=\"../img/bt_del_s_.png\" class=\"hand absmiddle\" onclick=\"remove_addopt(this)\">");
	//$j(td).html("<button onclick=\"remove_addopt(this);return false;\">삭제</button>");	
	$j(td).attr("width","160");
	$j(td).attr("align","center");
	$j(td).appendTo(tr);

	_pt_set();
}

// 추가옵션 삭제
function remove_addopt(obj){
	$j(obj).closest("tr").remove();
}

// 구간 추가
function add_addcnt(obj){

	var n1 = Number($j(".addcntbox input[name^='cnt_end']").length);
	var v1 = $j(".addcntbox input[name='cnt_end["+ n1 +"]']").val();
		
	var addcntbox_idx = n1 + 1;
	var cnt_start = Number(v1) + 1;
	
	var tb = $j(".addcntbox:last");
	
	var tr = document.createElement("tr");
	$j(tr).appendTo(tb);	
	$j(tr).attr("align","center");

	var td = document.createElement("td");
	$j(td).html("&nbsp;");
	$j(td).appendTo(tr);

//<span name=\"cnt_start["+addcntbox_idx+"]\">" + cnt_start + "(에서)</span>
// pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"
	var td = document.createElement("td");
	$j(td).html("<input type=\"text\" style=\"width:30px;\" name=\"cnt_start["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\"/>(" + tls("에서") + ") ~ <input type=\"text\" style=\"width:30px;\" name=\"cnt_end["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("까지") + ") - <input type=\"text\" style=\"width:30px;\" name=\"cnt_step["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("씩 증가") + ")");
	$j(td).attr("align","right");
	$j(td).appendTo(tr);

	var td = document.createElement("td");
	$j(td).html("<button onclick=\"remove_addcnt(this);return false;\">" + tls("삭제") + "</button>");	
	$j(td).attr("align","center");
	$j(td).appendTo(tr);

	_pt_set();
}

// 구간 추가 (프린트홈)
function add_addcnt_printhome(obj){

	var n1 = Number($j(".addcntbox input[name^='cnt_end']").length);
	var v1 = $j(".addcntbox input[name='cnt_end["+ n1 +"]']").val();
		
	var addcntbox_idx = n1 + 1;
	var cnt_start = Number(v1) + 1;
	
	var tb = $j(".addcntbox:last");
	
	var tr = document.createElement("tr");
	$j(tr).appendTo(tb);	

	var td = document.createElement("td");
	$j(td).html("&nbsp;");
	$j(td).appendTo(tr);

	var td = document.createElement("td");
	$j(td).html("<input type=\"text\" style=\"width:30px;\" name=\"cnt_start["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\"/>(" + tls("에서") + ") ~ <input type=\"text\" style=\"width:30px;\" name=\"cnt_end["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("까지") + ") - <input type=\"text\" style=\"width:30px;\" name=\"cnt_step["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("씩 증가") + ")");
	$j(td).appendTo(tr);

	var td = document.createElement("td");
	$j(td).html("<button class=\"btn btn-sm btn-default\" onclick=\"remove_addcnt(this);return false;\">" + tls("삭제") + "</button>");	
	$j(td).appendTo(tr);

	_pt_set();
}

// 구간 삭제
function remove_addcnt(obj){
	$j(obj).closest("tr").remove();
}

// 구간 추가
function add_addcnt_after(obj){

	var n1 = Number($j(".addcntbox_after input[name^='after_cnt_end']").length);
	var v1 = $j(".addcntbox_after input[name='after_cnt_end["+ n1 +"]']").val();
		
	var addcntbox_idx = n1 + 1;
	var cnt_start = Number(v1) + 1;
	
	var tb = $j(".addcntbox_after:last");
	
	var tr = document.createElement("tr");
	$j(tr).appendTo(tb);	
	$j(tr).attr("align","center");

	var td = document.createElement("td");
	$j(td).html("&nbsp;");
	$j(td).appendTo(tr);

//<span name=\"cnt_start["+addcntbox_idx+"]\">" + cnt_start + "(에서)</span>
	var td = document.createElement("td");
	$j(td).html("<input type=\"text\" style=\"width:30px;\" name=\"after_cnt_start["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\"/>(" + tls("에서") + ") ~ <input type=\"text\" style=\"width:30px;\" name=\"after_cnt_end["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("까지") + ") - <input type=\"text\" style=\"width:30px;\" name=\"after_cnt_step["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("씩 증가") + ")");
	$j(td).attr("align","right");
	$j(td).appendTo(tr);

	var td = document.createElement("td");
	$j(td).html("<button onclick=\"remove_addcnt_after(this);return false;\">" + tls("삭제") + "</button>");	
	$j(td).attr("align","center");
	$j(td).appendTo(tr);

	_pt_set();
}

// 구간 추가 (프린트홈)
function add_addcnt_after_printhome(obj){

	var n1 = Number($j(".addcntbox_after input[name^='after_cnt_end']").length);
	var v1 = $j(".addcntbox_after input[name='after_cnt_end["+ n1 +"]']").val();
		
	var addcntbox_idx = n1 + 1;
	var cnt_start = Number(v1) + 1;
	
	var tb = $j(".addcntbox_after:last");
	
	var tr = document.createElement("tr");
	$j(tr).appendTo(tb);	

	var td = document.createElement("td");
	$j(td).html("&nbsp;");
	$j(td).appendTo(tr);

//<span name=\"cnt_start["+addcntbox_idx+"]\">" + cnt_start + "(에서)</span>
	var td = document.createElement("td");
	$j(td).html("<input type=\"text\" style=\"width:30px;\" name=\"after_cnt_start["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\"/>(" + tls("에서") + ") ~ <input type=\"text\" style=\"width:30px;\" name=\"after_cnt_end["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("까지") + ") - <input type=\"text\" style=\"width:30px;\" name=\"after_cnt_step["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("씩 증가") + ")");
	$j(td).appendTo(tr);

	var td = document.createElement("td");
	$j(td).html("<button class=\"btn btn-sm btn-default\" onclick=\"remove_addcnt_after(this);return false;\">" + tls("삭제") + "</button>");	
	$j(td).appendTo(tr);

	_pt_set();
}

// 구간 삭제
function remove_addcnt_after(obj){
	$j(obj).closest("tr").remove();
}

// 구간 추가 (수량,부수,권수)
function add_addunitcnt(obj){

	var n1 = Number($j(".addunitcntbox input[name^='unit_cnt_end']").length);
	var v1 = $j(".addunitcntbox input[name='unit_cnt_end["+ n1 +"]']").val();
		
	var addcntbox_idx = n1 + 1;
	var cnt_start = Number(v1) + 1;
	
	var tb = $j(".addunitcntbox:last");
	
	var tr = document.createElement("tr");
	$j(tr).appendTo(tb);	
	$j(tr).attr("align","center");

	var td = document.createElement("td");
	$j(td).html("&nbsp;");
	$j(td).appendTo(tr);

//<span name=\"cnt_start["+addcntbox_idx+"]\">" + cnt_start + "(에서)</span>
// pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"
	var td = document.createElement("td");
	$j(td).html("<input type=\"text\" style=\"width:30px;\" name=\"unit_cnt_start["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\"/>(" + tls("에서") + ") ~ <input type=\"text\" style=\"width:30px;\" name=\"unit_cnt_end["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("까지") + ") - <input type=\"text\" style=\"width:30px;\" name=\"unit_cnt_step["+addcntbox_idx+"]\" pt=\"_pt_numplus\" onkeydown=\"_pattern(this);\" onchange=\"numZeroChk(this);\"/>(" + tls("씩 증가") + ")");
	$j(td).attr("align","right");
	$j(td).appendTo(tr);

	var td = document.createElement("td");
	$j(td).html("<button onclick=\"remove_addcnt(this);return false;\">" + tls("삭제") + "</button>");	
	$j(td).attr("align","center");
	$j(td).appendTo(tr);

	_pt_set();
}



