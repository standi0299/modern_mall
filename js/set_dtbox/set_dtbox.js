function set_dtbox(obj){

	// 우측 자동 세팅 박스 값 설정
	var dt_autobox_arr = {
		"dtbox_yesterday.jpg"	:1,
		"dtbox_today.jpg"		:0,
		"dtbox_week.jpg"		:7,
		"dtbox_day15.jpg"		:15,
		"dtbox_day30.jpg"		:30,
		"dtbox_day90.jpg"		:90,
		"dtbox_reset.jpg"		:"reset"
		};
	// input name 설정
	var dtname = $j(obj).attr("dtname").split(",");
	if (!dtname[0]){
		var name_1 = "";
		var name_2 = "";
	} else if (dtname.length==1){
		var name_1 = dtname+"[]";
		var name_2 = dtname+"[]";
	} else {
		var name_1 = dtname[0];
		var name_2 = dtname[1];
	}
	// 달력이미지 경로
	var imgsrc = "/admin/img/bt_calendar.png";

	// 기본값 설정
	var val = $j(obj).attr("val");
	if (val==undefined){
		val = "";
	}
	val = val.split(",");
	var val_1 = val[0];
	var val_2 = val[1];

	// input 박스 div 세팅
	var dt_input_div = document.createElement("div");
	$j(dt_input_div).addClass("dt_input_div");
	$j(dt_input_div).appendTo(obj);

	// 1번째 input 세팅
	var dt_input_1 = document.createElement("input");
	if (name_1) $j(dt_input_1).attr("name",name_1);
	$j(dt_input_1).appendTo(dt_input_div);
	$j(dt_input_1).val(val_1);
	if ($j.datepicker){
		$j(dt_input_1).datepicker({
			changeMonth: true,
			changeYear: true
		});
	}
	// 1번째 달력이미지 세팅
	if (imgsrc){
		var dt_input_img_1 = document.createElement("img");
		$j(dt_input_img_1).attr("src",imgsrc);
		$j(dt_input_img_1).appendTo(dt_input_div);
	}

	// 중앙특수문자
	var dt_space = document.createElement("span");
	$j(dt_space).html("~");
	$j(dt_space).css("margin","0 5px");
	$j(dt_space).appendTo(dt_input_div);

	// 2번째 input 세팅
	var dt_input_2 = document.createElement("input");
	if (name_2) $j(dt_input_2).attr("name",name_2);
	$j(dt_input_2).appendTo(dt_input_div);
	$j(dt_input_2).val(val_2);
	if ($j.datepicker){
		$j(dt_input_2).datepicker({
			changeMonth: true,
			changeYear: true
		});
	}

	// 2번째 달력이미지 세팅
	if (imgsrc){
		var dt_input_img_2 = document.createElement("img");
		$j(dt_input_img_2).attr("src",imgsrc);
		$j(dt_input_img_2).appendTo(dt_input_div);
	}

	// 우측버튼셋 세팅
	if (dt_autobox_arr){

		var dt_set_div = document.createElement("div");
		$j(dt_set_div).appendTo(obj);
		$j(dt_set_div).addClass("dt_set_div");

		$j.each(dt_autobox_arr,function(key,value){
			var dt_set_bt = document.createElement("img");
			$j(dt_set_bt).attr("src","../img/"+key);

			$j(dt_set_bt).css("cursor","hand");
			$j(dt_set_bt).css("margin","0 5px;");
			$j(dt_set_bt).addClass("absmiddle");

			$j(dt_set_bt).focus(function(){
				$j(this).trigger("blur");
			});
			$j(dt_set_bt).appendTo(dt_set_div);
			$j(dt_set_bt).click(function(){

				if (value=="reset")	{
					$j(dt_input_1).val("");
					$j(dt_input_2).val("");
					return;
				}

				var dt =  (parseInt(new Date().getTime().toString().substr(0,10)) - (value * 24 * 60 * 60)) * 1000;
				var dt1 = getDateFormat(dt);
				var dt2 = getDateFormat();
				if (value==1){
					dt2 = dt1;
				}
				$j(dt_input_1).val(dt1);
				$j(dt_input_2).val(dt2);

			});
		});

	}

	var dt_manual_div = document.createElement("div");
	$j(dt_manual_div).appendTo(obj);
	$j(dt_manual_div).addClass("dt_manual_div");

	// 연월 세팅 : year
	var dt_manual_year_div = document.createElement("div");
	$j(dt_manual_year_div).appendTo(dt_manual_div);
	$j(dt_manual_year_div).addClass("dt_manual_year_div");

	var dt_maunal_year_dirMinus = document.createElement("img");
	$j(dt_maunal_year_dirMinus).attr("src","../img/dtbox_left_arrow.jpg");

	$j(dt_maunal_year_dirMinus).addClass("dt_maunal_dir");
	$j(dt_maunal_year_dirMinus).appendTo(dt_manual_year_div);
	$j(dt_maunal_year_dirMinus).click(function(){
		if (parseInt($j(dt_manual_year).html()) < 1) return;
		$j(dt_manual_year).html(parseInt($j(dt_manual_year).html())-1);
	});

	var dt_manual_year = document.createElement("span");
	$j(dt_manual_year).html(new Date().getFullYear());
	$j(dt_manual_year).addClass("dt_manual_year");
	$j(dt_manual_year).appendTo(dt_manual_year_div);

	var dt_maunal_year_dirPlus = document.createElement("img");
	$j(dt_maunal_year_dirPlus).attr("src","../img/dtbox_right_arrow.jpg");
	$j(dt_manual_month_div).addClass("dt_manual_month_div");
	$j(dt_maunal_year_dirPlus).addClass("dt_maunal_dir");
	$j(dt_maunal_year_dirPlus).appendTo(dt_manual_year_div);
	$j(dt_maunal_year_dirPlus).click(function(){
		$j(dt_manual_year).html(parseInt($j(dt_manual_year).html())+1);
	});

	// 연월 세팅 : month
	var dt_manual_month_div = document.createElement("div");
	$j(dt_manual_month_div).appendTo(dt_manual_div);
	$j(dt_manual_month_div).addClass("dt_manual_month_div");

	var dt_maunal_month_dirMinus = document.createElement("img");
	//$j(dt_maunal_month_dirMinus).html("<");
	$j(dt_maunal_month_dirMinus).attr("src","../img/dtbox_left_arrow.jpg");
	$j(dt_maunal_month_dirMinus).addClass("dt_maunal_dir");
	$j(dt_maunal_month_dirMinus).appendTo(dt_manual_month_div);
	$j(dt_maunal_month_dirMinus).click(function(){
		var m = $j(dt_manual_month).html();
		if (m.substr(0,1)=="0"){
			m = m.substr(1,1);
		}
		m = parseInt(m)-1;
		if (m < 1) m = 12;
		m = getZerofill(m,2);
		$j(dt_manual_month).html(m);
	});

	var dt_manual_month = document.createElement("span");
	$j(dt_manual_month).html(getZerofill(new Date().getMonth()+1,2));
	$j(dt_manual_month).addClass("dt_manual_month");
	$j(dt_manual_month).appendTo(dt_manual_month_div);

	var dt_maunal_month_dirPlus = document.createElement("img");
	//$j(dt_maunal_month_dirPlus).html(">");
	$j(dt_maunal_month_dirPlus).attr("src","../img/dtbox_right_arrow.jpg");
	$j(dt_maunal_month_dirPlus).addClass("dt_maunal_dir");
	$j(dt_maunal_month_dirPlus).appendTo(dt_manual_month_div);
	$j(dt_maunal_month_dirPlus).click(function(){
		var m = $j(dt_manual_month).html();
		if (m.substr(0,1)=="0"){
			m = m.substr(1,1);
		}
		m = parseInt(m)+1;
		if (m > 12) m = 1;
		m = getZerofill(m,2);
		$j(dt_manual_month).html(m);
	});

	// 세팅 적용
	var dt_manual_bt = document.createElement("input");
	$j(dt_manual_bt).attr("type","image");
	$j(dt_manual_bt).focus(function(){
		$j(this).trigger("blur");
	});
	$j(dt_manual_bt).attr("src","../img/dtbox_date.jpg");
	$j(dt_manual_bt).appendTo(dt_manual_div);
	$j(dt_manual_bt).click(function(){
		var y = $j(dt_manual_year).html();
		var m = $j(dt_manual_month).html();
		if (m.substr(0,1)=="0"){
			m = m.substr(1,1);
		}
		m = parseInt(m)-1;
		var dt1 = getDateFormat(new Date(y,m));
		$j(dt_input_1).val(dt1);
		var dt2 = getDateFormat((parseInt(new Date(y,m+1).getTime().toString().substr(0,10)) - (1 * 24 * 60 * 60)) * 1000);
		$j(dt_input_2).val(dt2);
//		firstDay = firstDay.getDay();
		return false;
	});
	
	$j("button",$j(dt_input_div)).click(function(){
		alert(1);
//		return false;
	});

}

function getZerofill(val,length){
	for (var i=0;i<length-val.toString().length;i++){
		val = "0"+val;
	}
	return val;
}

function getDateFormat(ts){
	if (ts){
		var dt = new Date(ts);
	} else {
		var dt = new Date();
	}
	
	var y = dt.getFullYear();
	var m = dt.getMonth()+1;
	if (m<10){
		m = "0"+m;
	}
	var d = dt.getDate();
	if (d<10){
		d= "0"+d;
	}
	var str =  y + "-" + m + "-" + d;

	return str;
}