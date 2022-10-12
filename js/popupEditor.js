function popupLayer(src,mode){
	/*** 백그라운드 레이어 ***/
	var bg = document.createElement("div");

	$j(bg).css("position","absolute");
	$j(bg).css("zIndex","9999");
	$j(bg).css("position","absolute");
	$j(bg).css("left","0");
	$j(bg).css("top","0");
	$j(bg).css("background","transparent");
	$j(bg).width($j(document).width());
	$j(bg).height($j(document).height());
	$j(bg).fadeTo(0,0.3);
	$j(bg).attr("id","popupLayerBg");
	$j(bg).appendTo($j("body"));
//	$j("body").css("overflow","hidden");

	$("popupLayerBg").onmousewheel = function(){return false;}

	var o = document.createElement("div");

	$j(o).attr("id","popupLayer");
	$j(o).appendTo($j("body"));

	var w = (arguments[2]) ? arguments[2]:700;
	var h = (arguments[3]) ? arguments[3]:500;
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

	$j(o).width(0);
	$j(o).height(0);
	$j(o).html("<div style='height:18px;background:#28A5F9;color:#FFFFFF;font-weight:bold;padding:7px 5px 5px;' id='popupLayer_tit'></div><div style='position:absolute;right:10px;top:10px;'><a href='javascript:closeLayer()' onfocus=blur()><img src='/js/img/sbtn_close_layer.gif'></a></div><iframe name='popupLayerFrame' src='" + src + "' style='width:100%;height:" + (h-30) + "px;' frameborder=0></iframe>");

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