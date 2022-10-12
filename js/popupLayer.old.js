/*** 레이어 팝업창 띄우기 ***/
function popupLayer(s,w,h)
{
	var type;
	if (w=="img"){
		type = "img";
		var img = document.createElement("div");
		img.innerHTML = "<img src='" + s + "' style='cursor:pointer'>";
		img.style.position = "absolute";
		img.style.top = -9999;
		img.onclick = closeLayer;
		document.body.appendChild(img);
		w = img.childNodes[0].offsetWidth;
		h = img.childNodes[0].offsetHeight;
	} else {	
		if (!w) w = 600;
		if (!h) h = 400;
	}

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = document.body.clientWidth;
	var bodyH = document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어 ***/
	var bg = document.createElement("div");
	with (bg.style){
		position = "absolute";
		left = 0;
		top = 0;
//		width = document.body.clientWidth;
		width = "100%";
//		height = document.body.scrollHeight;
		height = "100%";
		//height = document.body.clientHeight + document.body.scrollHeight;
		backgroundColor = "#000000";
		filter = "Alpha(Opacity=50)";
	}

	bg.id = "objPopupLayerBg";
	document.body.appendChild(bg);

	/*** 내용프레임 레이어 ***/
	var obj = document.createElement("div");
	var _y = posY + document.body.scrollTop;
	with (obj.style){
		position = "absolute";
		background = "#ffffff";
		left = posX + document.body.scrollLeft;
		top = (_y>1) ? _y : 1;
		width = w;
		height = h;
		border = "3px solid #000000";
	}
	obj.id = "objPopupLayer";
	document.body.appendChild(obj);

	/*** 타이틀바 레이어 ***/
	var bottom = document.createElement("div");
	with (bottom.style){
		position = "absolute";
		width = w;
		height = titleHeight;
		left = 0;
		//top = h - titleHeight - pixelBorder * 3;
		bottom = "0";
		padding = "2px 0 0 0";
		textAlign = "right";
		backgroundColor = "#000000";
		color = "#ffffff";
		font = "bold 11px tahoma";
	}
	bottom.innerHTML = "<a href='javascript:closeLayer()'><font color=#ffffff>close</font></a>&nbsp;&nbsp;&nbsp;";
	obj.appendChild(bottom);

	if (type=="img"){
		img.style.position = "relative";
		img.style.top = 0;
		obj.appendChild(img);
		height = (document.body.scrollHeight>document.body.clientHeight) ? document.body.scrollHeight : document.body.clientHeight;
		bg.style.height = height;
	} else {	
		/*** 아이프레임 ***/
		var ifrm = document.createElement("iframe");
		with (ifrm.style){
			width = w - 6;
			height = h - pixelBorder * 2 - titleHeight - 3;
			//border = "3 solid #000000";
		}
		ifrm.frameBorder = 0;
		ifrm.src = s;
		ifrm.scrolling = (arguments[3]) ? "yes" : "no";
		//ifrm.className = "scroll";
		obj.appendChild(ifrm);
	}
	document.body.style.overflow = "hidden";
}
function closeLayer()
{
	hiddenSelectBox('visible');
	document.getElementById('objPopupLayer').removeNode(true);
	document.getElementById('objPopupLayerBg').removeNode(true);
	document.body.style.overflow = "auto";
}
function hiddenSelectBox(mode)
{
	var obj = document.getElementsByTagName('select');
	for (i=0;i<obj.length;i++){
		obj[i].style.visibility = mode;
	}
}