function popupLayer(src,mode){
	/*** 백그라운드 레이어 ***/
	if (!arguments[4]){
		var bg = document.createElement("div");
		$j(bg).css("position","absolute");
		$j(bg).css("zIndex","9999");
		$j(bg).css("position","absolute");
		$j(bg).css("left","0");
		$j(bg).css("top","0");
		$j(bg).css("background","#000000");
		$j(bg).width($j(document).width());
		$j(bg).height($j(document).height());
		$j(bg).fadeTo(0,0.3);
		$j(bg).attr("id","popupLayerBg");
		$j(bg).appendTo($j("body"));
		$("popupLayerBg").onmousewheel = function(){return false;}
	}
//	$j("body").css("overflow","hidden");

	var o = document.createElement("div");

	$j(o).attr("id","popupLayer");
	$j(o).appendTo($j("body"));

//var h = (arguments[3]) ? arguments[3]:500 에서 arguments[3]은 값이 존재하지 않아서 popupLayer창을 띄울때
//세로길이의 값이 먹히지 않음 세로길이 값이 넘어오는 arguments[1]로 변경함 / 14.05.30 / kjm
	var w = (arguments[2]) ? arguments[2]:700;
	var h = (arguments[1]) ? arguments[1]:500;
//	var h = 500;

	var posX = ($j(document).width() - w) / 2;
	var posY = (!mode) ? ($j(window).height() /2) - (h/2) : 10;
	posY += $j(window).scrollTop();

	$j(o).css("background","#FFFDE9");
	$j(o).css("position","absolute");
	$j(o).css("zIndex",99999999999);
	$j(o).css("left",posX);
	$j(o).css("top",posY);
	$j(o).css("border","1px solid #666666");
	$j(o).width(w);
	$j(o).height(h);
	if (arguments[4]){
		$j(o).css("padding",0);
		$j(o).css("width","1px");
		$j(o).css("height","1px");
		$j(o).css("overflow","hidden");
		$j(o).css("border","0");
	} else {
	}
	$j(o).html("<div style='height:28px;background:#28A5F9;color:#FFFFFF;font-weight:bold;padding:7px 5px 5px;' id='popupLayer_tit'></div><div style='position:absolute;right:10px;top:10px;'><a href='javascript:closeLayer()' onfocus=blur()><img src='/js/img/sbtn_close_layer.gif'></a></div><iframe name='popupLayerFrame' src='" + src + "' style='width:100%;height:" + (h-30) + "px;' frameborder=0></iframe>");
	$j.post("/module/indb.php", {mode:"get_title"}, function(data){
		if (data){
			var tit = data;
		} else {
			var tit = "BluePod";
		}
		$j("#popupLayer_tit").html(tit);
	});
}

function popupLayer_kids(src,mode){	
	var o = document.createElement("div");

	$j(o).attr("id","popupLayer");
	$j(o).appendTo($j("body"));

//var h = (arguments[3]) ? arguments[3]:500 에서 arguments[3]은 값이 존재하지 않아서 popupLayer창을 띄울때
//세로길이의 값이 먹히지 않음 세로길이 값이 넘어오는 arguments[1]로 변경함 / 14.05.30 / kjm
	var w = (arguments[2]) ? arguments[2]:700;
	var h = (arguments[1]) ? arguments[1]:500;
//	var h = 500;

	
	//현재 위치에 레이아웃 출력
	$j(".add").click(function(event){
    var x = event.clientX;
    var y = event.clientY;

	var posX = x;
	var posY = y;
	posY += $j(window).scrollTop();

	$j(o).css("background","#FFFDE9");
	$j(o).css("position","absolute");
	$j(o).css("zIndex",99999999999);
	$j(o).css("left",posX);
	$j(o).css("top",posY);
	$j(o).css("border","1px solid #666666");
	$j(o).width(w);
	$j(o).height(h);
	if (arguments[4]){
		$j(o).css("padding",0);
		$j(o).css("width","1px");
		$j(o).css("height","1px");
		$j(o).css("overflow","hidden");
		$j(o).css("border","0");
	} else {
	}
	$j(o).html("<div style='height:18px;background:#28A5F9;color:#FFFFFF;font-weight:bold;padding:7px 5px 5px;' id='popupLayer_tit'></div><div style='position:absolute;right:10px;top:10px;'><a href='javascript:closeLayer()' onfocus=blur()><img src='/js/img/sbtn_close_layer.gif'></a></div><iframe name='popupLayerFrame' src='" + src + "' style='width:100%;height:" + (h-30) + "px;' frameborder=0></iframe>");
	});
	$j.post("/module/indb.php", {mode:"get_title"}, function(data){
		if (data){
			var tit = data;
		} else {
			var tit = "BluePod";
		}
		$j("#popupLayer_tit").html(tit);
	});	
}

//popuplayer 호출 후 닫으면 기존 창 새로고침 / 14.05.21 / kjm
function popupLayer_reload(src,mode){
	/*** 백그라운드 레이어 ***/
	if (!arguments[4]){
		var bg = document.createElement("div");
		$j(bg).css("position","absolute");
		$j(bg).css("zIndex","9999");
		$j(bg).css("position","absolute");
		$j(bg).css("left","0");
		$j(bg).css("top","0");
		$j(bg).css("background","#000000");
		$j(bg).width($j(document).width());
		$j(bg).height($j(document).height());
		$j(bg).fadeTo(0,0.3);
		$j(bg).attr("id","popupLayerBg");
		$j(bg).appendTo($j("body"));
		$("popupLayerBg").onmousewheel = function(){return false;}
	}
//	$j("body").css("overflow","hidden");

	var o = document.createElement("div");

	$j(o).attr("id","popupLayer");
	$j(o).appendTo($j("body"));

	
//var h = (arguments[3]) ? arguments[3]:500 에서 arguments[3]은 값이 존재하지 않아서 popupLayer창을 띄울때
//세로길이의 값이 먹히지 않음 세로길이 값이 넘어오는 arguments[1]로 변경함 / 14.05.30 / kjm
	var w = (arguments[2]) ? arguments[2]:700;
	var h = (arguments[1]) ? arguments[1]:350;
//	var h = 500;

	var posX = ($j(document).width() - w) / 2;
	var posY = (!mode) ? ($j(window).height() /2) - (h/2) : 10;
	posY += $j(window).scrollTop();

	$j(o).css("background","#FFFDE9");
	$j(o).css("position","absolute");
	$j(o).css("zIndex",99999999999);
	$j(o).css("left",posX);
	$j(o).css("top",posY);
	$j(o).css("border","1px solid #666666");
	$j(o).width(w);
	$j(o).height(h);
	if (arguments[4]){
		$j(o).css("padding",0);
		$j(o).css("width","1px");
		$j(o).css("height","1px");
		$j(o).css("overflow","hidden");
		$j(o).css("border","0");
	} else {
	}
	$j(o).html("<div style='height:18px;background:#28A5F9;color:#FFFFFF;font-weight:bold;padding:7px 5px 5px;' id='popupLayer_tit'></div><div style='position:absolute;right:10px;top:10px;'><a href='javascript:closeLayer_reload()' onfocus=blur()><img src='/js/img/sbtn_close_layer.gif'></a></div><iframe name='popupLayerFrame' src='" + src + "' style='width:100%;height:" + (h-30) + "px;' frameborder=0></iframe>");
	$j.post("/module/indb.php", {mode:"get_title"}, function(data){
		if (data){
			var tit = data;
		} else {
			var tit = "BluePod";
		}
		$j("#popupLayer_tit").html(tit);
	});
}

function closeLayer(obj,no){
//	$j("body").css("overflow","auto");
	$j('#popupLayer').remove();
	$j('#popupLayerBg').remove();
}

function closeLayer_reload(obj,no){
//	$j("body").css("overflow","auto");
	$j('#popupLayer').remove();
	$j('#popupLayerBg').remove();
	//location.reload();
	history.go(0);
}

//편집왕 미리보기
function popupLayer2(src,mode){
	/*** 백그라운드 레이어 ***/
	var bg = document.createElement("div");

	$j(bg).css("position","absolute");
	$j(bg).css("zIndex","99");
	$j(bg).css("position","absolute");
	$j(bg).css("left","0");
	$j(bg).css("top","0");
	$j(bg).css("background","#000000");
	$j(bg).width($j(document).width());
	$j(bg).height($j(document).height());
	$j(bg).fadeTo(0,0.3);
	$j(bg).attr("id","popupLayerBg");
	$j(bg).appendTo($j("body"));
	$j("body").css("overflow","hidden");

	document.getElementById("popupLayerBg").onmousewheel = function(){ return false; }

	var o = document.createElement("div");

	$j(o).attr("id","popupLayer");
	$j(o).appendTo($j("body"));

	var w = (arguments[2]) ? arguments[2] : 1000;
	var h = (arguments[3]) ? arguments[3] : 850;
	//var h = 585;

	var posX = ($j(document).width() - w) / 2;
	var posY = (!mode) ? ($j(window).height() / 2) - (h / 2) : 10;
	posY += $j(window).scrollTop();

	//$j(o).css("background","#ffffff");
	$j(o).css("position","absolute");
	$j(o).css("zIndex",99999999999);
	$j(o).css("left",posX);
	$j(o).css("top",posY);
	//$j(o).css("border","1px solid #666666");
	$j(o).width(w);
	$j(o).height(h);
	$j(o).html("<iframe name='popupLayerFrame' src='" + src + "' style='width:1000px;height:750px;' frameborder=0></iframe>");
	$j.post("/module/indb.php", {mode:"get_title"}, function(data){
		if (data){
			var tit = data;
		} else {
			var tit = "BluePod";
		}
		$j("#popupLayer_tit").html(tit);
	});
	
}

function closeLayer2(obj,no){
	$j("body").css("overflow","");
	$j('#popupLayer').remove();
	$j('#popupLayerBg').remove();
}

function popupLayerFileUpload(src, mode){
	/*** 백그라운드 레이어 ***/
	if (!arguments[4]){
		var bg = document.createElement("div");
		$j(bg).css("position","absolute");
		$j(bg).css("zIndex","9999");
		$j(bg).css("position","absolute");
		$j(bg).css("left","0");
		$j(bg).css("top","0");
		$j(bg).css("background","#000000");
		$j(bg).width($j(document).width());
		$j(bg).height($j(document).height());
		$j(bg).fadeTo(0,0.3);
		$j(bg).attr("id","popupLayerBg");
		$j(bg).appendTo($j("body"));
		$("popupLayerBg").onmousewheel = function(){return false;}
	}
//	$j("body").css("overflow","hidden");

	var o = document.createElement("div");

	$j(o).attr("id","popupLayer");
	$j(o).appendTo($j("body"));

//var h = (arguments[3]) ? arguments[3]:500 에서 arguments[3]은 값이 존재하지 않아서 popupLayer창을 띄울때
//세로길이의 값이 먹히지 않음 세로길이 값이 넘어오는 arguments[1]로 변경함 / 14.05.30 / kjm
	var w = (arguments[2]) ? arguments[2]:900;
	var h = (arguments[1]) ? arguments[1]:600;
//	var h = 500;

	var posX = ($j(document).width() - w) / 2;
	var posY = (!mode) ? ($j(window).height() /2) - (h/2) : 10;
	posY += $j(window).scrollTop();

	//$j(o).css("background","#FFFDE9");
	$j(o).css("position","absolute");
	$j(o).css("zIndex",99999999999);
	$j(o).css("left",posX);
	$j(o).css("top",posY);
	$j(o).css("border","1px solid #666666");
	$j(o).width(w);
	$j(o).height(h);
	if (arguments[4]){
		$j(o).css("padding",0);
		$j(o).css("width","1px");
		$j(o).css("height","1px");
		$j(o).css("overflow","hidden");
		$j(o).css("border","0");
	} else {
	}
	//$j(o).html("<iframe name='popupLayerFrame' src='" + src + "' style='width:100%;height:" + (h-30) + "px;' frameborder=0></iframe>");
	$j(o).html("<iframe name='popupLayerFrame' src='" + src + "' style='width:100%;height:" + (h-2) + "px;' frameborder=0></iframe>");
}

function popupLayerInterFileUpload(src, mode){
	/*** 백그라운드 레이어 ***/
	var bg = document.createElement("div");
	$j(bg).css("position","absolute");
	$j(bg).css("zIndex","9999");
	$j(bg).css("position","absolute");
	$j(bg).css("left","0");
	$j(bg).css("top","0");
	$j(bg).css("overflow","hidden");
	$j(bg).css("background","rgba(0,0,0,0.9)");
	$j(bg).width($j(document).width());
	$j(bg).height($j(document).height());
	//$j(bg).fadeTo(0,0.3);
	$j(bg).attr("id","popupLayerBg");
	$j(bg).appendTo($j("body"));
	//$j("popupLayerBg").onmousewheel = function(){return false;}
	//$j("body").css("overflow","hidden");

	var o = document.createElement("div");

	$j(o).attr("id","popupLayer");
	$j(o).appendTo($j("body"));

//var h = (arguments[3]) ? arguments[3]:500 에서 arguments[3]은 값이 존재하지 않아서 popupLayer창을 띄울때
//세로길이의 값이 먹히지 않음 세로길이 값이 넘어오는 arguments[1]로 변경함 / 14.05.30 / kjm
	var w = $j(document).width();
	var h = $j(window).height();
//	var h = 500;

	var posX = ($j(document).width() - w) / 2;
	var posY = (!mode) ? ($j(window).height() /2) - (h/2) : 10;
	posY += $j(window).scrollTop();

	//$j(o).css("background","#FFFDE9");
	$j(o).css("position","absolute");
	$j(o).css("zIndex",99999999999);
	$j(o).css("left",posX);
	$j(o).css("top",posY);
	//$j(o).css("border","1px solid #666666");
	$j(o).width(w);
	$j(o).height(h);

	$j(o).html("<iframe name='popupLayerFrame' src='" + src + "' style='overflow-y:hidden; width:100%;height:" + (h-2) + "px;' frameborder=0></iframe>");
}
