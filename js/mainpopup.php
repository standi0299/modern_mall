<?
include_once dirname(__FILE__)."/../conf/language_locale.php";				//다국어 파일 처리. 다국어 처리하는곳에만 실제 처리 로직을 넣는다.
?>
var _zindex = 99999;
function mainpopup(popupno,width,height,top,left){
	
	var wrap = $j('body');

	/* 팝업객체 생성 */
	var _id = "mainpopup_"+popupno;
	var obj = document.createElement("div");
	obj.id = _id;
	/* 팝업객체 css 정의 */
	$j(obj).css("border","1px solid #000000");
	$j(obj).css("z-index",_zindex+1);
	$j(obj).css("position","absolute");
	$j(obj).css("width",width+"px");
	$j(obj).css("height",(parseInt(height)+23)+"px");
	$j(obj).css("top",(wrap.offset().top+parseInt(top))+"px");
	$j(obj).css("left",(wrap.offset().left+parseInt(left))+"px");
	$j(obj).css("overflow","hidden");
	$j(obj).css("display","none");
	$j(obj).css("background","#FFFFFF");
	$j(obj).addClass("mainpopup");
	$j(obj).appendTo(wrap);

	$j.ajax({
		type: "POST",
		url: "../ajax.php",
		data: "mode=mainpopup&popupno=" + popupno,
		success: function(msg){

			var ret = eval('(' + msg + ')');

			/* 상단 */
			var top = document.createElement("div");
			$j(top).css("background","#333333");
			$j(top).css("color","#FFFFFF");
			$j(top).css("font","8pt 돋움");
			$j(top).css("padding","5px 0 0 10px");
			$j(top).css("height","15px");
			$j(top).css("cursor","move");
			top.innerHTML = ret.title;
			$j(top).toggleClass("mainpage_handle");
//			$j(top).appendTo($j(obj));
			var close = document.createElement("div");
			$j(close).css("color","#FFFFFF");
			$j(close).css("position","absolute");
			$j(close).css("right","10px");
			$j(close).css("top","3px");
			close.innerHTML = "<b style='font:bold 13pt tahoma;cursor:pointer;' onclick='close_mainpopup(\""+_id+"\")'>×</b>";
//			$j(close).appendTo($j(top));
			
			/* 내용 */
			var content = document.createElement("div");
			content.innerHTML = ret.content;
			$j(content).css("width",width+"px");
			//$j(content).css("height",(height-3)+"px");
			//$j(content).css("overflow","auto");
			$j(content).css("height",height+"px");
			$j(content).css("overflow","hidden");
			$j(content).appendTo($j(obj));
			$j("p").css("margin", "0");

			/* 하단 */
			var bot = document.createElement("div");
			$j(bot).css("background","#666666");
			$j(bot).css("color","#FFFFFF");
			$j(bot).css("font","10pt 돋움");
			$j(bot).css("padding","5px 0 0 10px");
			$j(bot).css("height","23px");
			$j(bot).css("text-align","center");
			$j(bot).css("position","absolute");
			$j(bot).css("bottom","0");
			$j(bot).css("width",width+"px");
			$j(bot).attr("id","main_popup_bot");
			$j(bot).css("cursor","move");
			bot.innerHTML = "<div style='position:absolute;left:10px;'><span onclick='close_mainpopup(\""+_id+"\",1)' style='cursor:pointer;'>" + '<?echo _("오늘 하루 이창을 열지 않음")?>' + "</span></div>" + '<?echo _("이동")?>' + "<div style='position:absolute;right:5px;top:5px;'><span onclick='close_mainpopup(\""+_id+"\",\"\")' style='cursor:pointer;'><span style='border:1px solid #FFFFFF;padding-right:1px;font:10pt 돋움;width:12px;height:12px;display:block;'>X</span></span></div>";
			$j(bot).appendTo($j(obj));

			$j(obj).slideDown('fast');
			$j(obj).draggable({handle:bot});
			/*
			$j(obj).drag(function( ev, dd ){
				$j(this).fadeTo(0,0.5);
				$j( this ).css({
					top: dd.offsetY,
					left: dd.offsetX
				});
			});
			*/

			$j(obj).drop(function(){
				$j(this).fadeTo(0,1);
			});

		}
	});

}

function close_mainpopup(id,mode){
	$j("#"+id).fadeOut('normal');
	var pw = getCookie('mainpopup');

	if (mode){
		var r_pw = pw.split(",");
		if (!r_pw[0]) r_pw = [];
		if (!in_array(id,r_pw)) r_pw.push(id);
		var now = new Date();
		var expire = new Date(now.getTime() + 60*60*24*1000);
		setCookie('mainpopup',r_pw,expire, "/");
	}
}