var _editor_ = [];
var miniColorMode;
var miniPath = "/js/editor/";
var miniBase;

function miniEditor(path)
{
	var r_textarea = document.getElementsByTagName('textarea');
	for (var i=0;i<r_textarea.length;i++){
		if (r_textarea[i].getAttribute('type')=="editor"){
			_editor_[_editor_.length] = r_textarea[i];
			miniSetFrame(r_textarea[i]);
		}
	}
}

function miniSetFrame(obj)
{	
	with (obj.style){
		display = "none";
		background = "#ffffff";
		font = "9pt Courier New";
	}

	var toolbar = document.createElement('div');
	toolbar.innerHTML = miniSetToolbar();
	obj.parentNode.insertBefore(toolbar,obj);

	with (toolbar.style){
		width = obj.style.width;
		background = "#f0f0f0";
		borderLeft = "1px solid #cccccc";
		borderRight = "1px solid #cccccc";
		borderTop = "1px solid #cccccc";
		position = "relative";
		height = "24px";
		padding = "3px 0";
	}

	var _iframe = document.createElement('iframe');
	_iframe.setAttribute("id","objFrame");
	_iframe.setAttribute("frameBorder",0);
	with (_iframe.style){
		width = obj.style.width;
		height = obj.style.pixelHeight + 1 + "px";
		border = "1px solid #cccccc";
	}
	obj.parentNode.insertBefore(_iframe,obj);

	var _content = _iframe.contentWindow.document;
	_content.designMode = "on";
	_content.open();
	_content.write("\
	<html><head>\
	<meta http-equiv='content-type' content='text/html; charset=euc-kr'>\
	<style>body {margin:10px;white-space:-moz-pre-wrap;word-break:break-all;};body,table {font:x-small 돋움;}; img{border:0}; input {border:1px solid #cccccc}; p{margin:2px 0;}; pre.prettyprint { padding: 10px; border: 1px solid #cccccc; background:#f7f7f7; font:9pt Courier New; line-height:16px;}</style>\
	</head>\
	");
	if (miniBase) _content.write("<base href='" + miniBase + "'>");
	_content.close();
	_content.body.innerHTML = obj.value;
	/*td,th {border:0px #bfbfbf dotted;};*/
	
	if (document.all){
		_content.body.onblur = function(){miniHtml2Source(_content,obj)}

		_content.body.onclick = function(){miniReset(_iframe);miniTextareaPos(this);}
		_content.body.onkeyup = function(){miniTextareaPos(this)}
	} else {	
		_content.addEventListener("blur",function(){miniHtml2Source(_content,obj)},false);
		//_iframe.contentWindow.onblur = function(){miniHtml2Source(_content,obj)}
		//_content.onclick = function(){miniReset(_iframe)}
	}

	/*
	var color_els = $('miniColorBox').getElementsByTagName('dd');
	for (var i=0;i<color_els.length;i++){
		color_els[i].onclick = function(){
			//alert(this.style.backgroundColor);
			//miniCommand(this,'Color',this.style.backgroundColor);
		}
	}*/
	
	//bindEvent(_content.body,"blur",function(){miniHtml2Source(_content,obj)});
	//bindEvent(_content.body,"click",function(){miniReset(_iframe)});
}

function miniHtml2Source(html,source)
{
	if (html.body.innerHTML=="<P>&nbsp;</P>") html.body.innerHTML = "";
	var tmp = html.body.innerHTML;
	if (miniBase){
		tmp = tmp.str_replace(miniBase,"");
		var chk = miniBase.substr(0,miniBase.substr(0,miniBase.length-1).lastIndexOf("/")+1);
		tmp = tmp.str_replace(chk,"../");
	}
	source.value = tmp;
}

function miniReset(obj)	/*** 크롬 안됨 - 체크요망 ***/ 
{
	/*** 테이블박스 숨기기 ***/
	var tmp = obj.previousSibling.childNodes[0].getElementsByTagName('dd')[0].childNodes[0];
	tmp.style.display = "none";

	var arr = new Array("Bold","Italic","Underline","StrikeThrough");
	var imgs = obj.previousSibling.getElementsByTagName('img');
	for (var i=0;i<arr.length;i++){
		if (obj.contentWindow.document.queryCommandValue(arr[i])) miniBtnDown(imgs[i]);
		else miniBtnClear(imgs[i]);
	}

	var arr = new Array("FontName","FontSize");
	var sels = obj.previousSibling.getElementsByTagName('select');
	for (var i=0;i<arr.length;i++){
		miniSetSelect(sels[i],obj.contentWindow.document.queryCommandValue(arr[i]));
	}

	if (obj.getAttribute('isBordered')==1){
		miniBtnDown(imgs[16]);
	} else {
		miniBtnClear(imgs[16]);
	}
}

function miniSetSelect(obj,ret)
{
	for (var i=0;i<obj.length;i++){
		if (obj.options[i].value==ret){
			obj.selectedIndex = i;
			break;
		}
	}
}

/*** 모드변환 ***/
function miniChgMode(obj)
{
	var toolbar = obj.parentNode.parentNode;
	var editor = toolbar.nextSibling;
	var textarea = editor.nextSibling;
	editor.style.display = (obj.checked) ? "none" : "block";
	textarea.style.display = (obj.checked) ? "block" : "none";
	toolbar.childNodes[0].style.display = (obj.checked) ? "none" : "block";
	toolbar.childNodes[1].style.display = (obj.checked) ? "block" : "none";
	if (obj.checked){
		textarea.focus();
		textarea.value = textarea.value + "";
	} else {
		editor.contentWindow.document.body.innerHTML = textarea.value;

		editor.contentWindow.document.execCommand("SelectAll", false, null);
		var rng = editor.contentWindow.document.selection.createRange();
		rng.collapse(false);
		rng.select();
		editor.contentWindow.focus();
	}
}


function UploadAfterMiniChgMode(obj_name)
{
	obj = document.getElementById(obj_name);
	miniChgMode(obj);
}


function miniLayer(obj,mode) {
	if (typeof(obj)!="object") obj = $(obj);
	if (typeof(obj)=="undefined" || obj==null) return;
	if (!mode) obj.style.display = (obj.style.display!="block") ? "block" : "none";
	else obj.style.display = mode;
}

/*** 명령어 처리 ***/
function miniCommand(obj,cmd,val)
{
	switch (cmd){

		case "InsertTable": return;
			var tmp = obj.parentNode.getElementsByTagName('dd')[0].childNodes[0];
			tmp.style.pixelLeft = get_objectLeft(obj);
			tmp.style.pixelTop = get_objectTop(obj) + 5;
			miniLayer(tmp);
			return; break;
		case "InsertImage":
			miniLayer(obj,miniPath + 'p.img.php');
			$('miniColorBox').style.display = "none";
			return; break;
		case "ShowBorder": return;
		case "CreateLink":
			var ifrm = obj.parentNode.parentNode.nextSibling;
			var editor = ifrm.contentWindow.document;
			//ifrm.contentWindow.focus();
			editor.execCommand(cmd, false, val);
			miniReset(ifrm);
			return; break;

		case "ForeColor": case "BackColor":
			if (miniColorMode==cmd){
				$('miniColorBox').style.display = "none";
				if ($('imageBox')) miniCloseLayer()
				miniColorMode = ""; return;
			}
			$('miniColorBox').style.display = "block";
			$('miniColorBox').style.left = (cmd=="ForeColor") ? "220px" : "240px";
			if ($('imageBox')) miniCloseLayer();
			//colorpicker(obj,eval("callback_" + cmd));
			miniColorMode = cmd;
			return; break;

		case "Color":
			$('miniColorBox').style.display = "none";
			if ($('imageBox')) miniCloseLayer();
			cmd = miniColorMode;
			miniColorMode = "";
						
			var ifrm = obj.parentNode.parentNode.nextSibling;
			var editor = ifrm.contentWindow.document;
			editor.execCommand(cmd, false, val);
			ifrm.contentWindow.focus();
			miniReset(ifrm);

			return; break;
	}

	var ifrm = obj.parentNode.parentNode.nextSibling;
	var editor = ifrm.contentWindow.document;
	ifrm.contentWindow.focus();
	editor.execCommand(cmd, false, val);
	miniReset(ifrm);
}

/*** 이미지 업로드 ***/
function miniCloseLayer(){
	document.getElementById('imageBox').style.display = "none";
}

function setImage(file){
	if (parent.frm_category){
		var r_textarea = parent.frm_category.document.getElementsByTagName('textarea');
	} else {
		var r_textarea = parent.document.getElementsByTagName('textarea');
	}
	
	for (var i=0;i<r_textarea.length;i++){
		if (r_textarea[i].getAttribute('type')=="editor"){
			var ret = r_textarea[i];
		}
	}
	ret.value = ret.value + "<img src='" + file + "'>";
}

function miniLayer(obj,src){
	if (!document.getElementById('imageBox')){	
		var divImg = document.createElement('iframe');
		divImg.setAttribute('id','imageBox');
		divImg.setAttribute("frameBorder",0);
		with (divImg.style){
			width = "350px";
			height = "150";
			border = "1px solid #dedede";
			display = "block";
			position = "absolute";
			left = "20px";
			top = "35px";
		}
		obj.parentNode.insertBefore(divImg,obj);
	} else {
		divImg = document.getElementById('imageBox');
		divImg.style.display = "block";
	}
	divImg.src = miniPath + "p.img.php";
}


function miniColorBox_old(){
	var ret = "";
	var r_ret = [];
	var r_color = new Array(
			"#FFFFFF","#FAEDD4","#FFF3B4","#FFFFBE","#FFEAEA","#FFEAF8","#E6ECFE","#D6F3F9","#E0F0E9","#EAF4CF",
			"#E8E8E8","#E7C991","#F3D756","#F3D756","#F9B4CB","#DFB7EE","#B1C4FC","#87C6DA","#B1DAB7","#B8D63D",
			"#C2C2C2","#D18E0A","#EC9C2C","#FF8B16","#F3709B","#AF65DD","#7293FA","#49B5D5","#6ABB9A","#5FB636",
			"#8E8E8E","#9D6C08","#C84205","#E31600","#C8056A","#801FBF","#3058D2","#0686A8","#318561","#2B8400",
			"#474747","#654505","#8C3C04","#840000","#8C044B","#57048C","#193DA9","#004C5F","#105738","#174600",
			"#000000","#463003","#612A03","#5B0000","#610334","#320251","#112A75","#002630","#082C1C","#113400"
			);
	for (var i=0;i<r_color.length;i++){
		r_ret[i] = "<dd style='cursor:pointer;width:15px;height:15px;float:left;margin:0 1px 1px 0;background:" + r_color[i] + "' onclick=\"miniCommand(this.parentNode,'Color','" + r_color[i] + "')\"></dd>";
	}
	ret = "<div id=miniColorBox style='position:absolute;width:160px;display:none;border:2px solid #efefef;padding:3px;background:#f7f7f7'>" + r_ret.join('') + "</div>";
	return ret;
}

function miniColorBox()
{
	var ret = "";
	var r_color = new Array(
			"#FFFFFF","#FAEDD4","#FFF3B4","#FFFFBE","#FFEAEA","#FFEAF8","#E6ECFE","#D6F3F9","#E0F0E9","#EAF4CF",
			"#E8E8E8","#E7C991","#F3D756","#F3D756","#F9B4CB","#DFB7EE","#B1C4FC","#87C6DA","#B1DAB7","#B8D63D",
			"#C2C2C2","#D18E0A","#EC9C2C","#FF8B16","#F3709B","#AF65DD","#7293FA","#49B5D5","#6ABB9A","#5FB636",
			"#8E8E8E","#9D6C08","#C84205","#E31600","#C8056A","#801FBF","#3058D2","#0686A8","#318561","#2B8400",
			"#474747","#654505","#8C3C04","#840000","#8C044B","#57048C","#193DA9","#004C5F","#105738","#174600",
			"#000000","#463003","#612A03","#5B0000","#610334","#320251","#112A75","#002630","#082C1C","#113400"
			);
	for (var i=0;i<r_color.length;i++){
		if (i && i%10==0) ret += "</tr><tr>";
		ret += "<td unselectable=on nowrap style='width:20px;height:15px;border-top:1 solid #cccccc;font:0;border-left:1 solid #cccccc;cursor:pointer;background:" + r_color[i] + "' onclick=\"miniCommand(this.parentNode.parentNode.parentNode.parentNode,'Color','" + r_color[i] + "')\">&nbsp;</td>";
	}
	ret = "<div id='miniColorBox' style='position:absolute;width:200px;display:none;border:2px solid #efefef;padding:3px;background:#f7f7f7'><table><tr>" + ret + "</tr></table></div>";
	return ret;
}

function callback_ForeColor(color,obj)
{
	var ifrm = obj.parentNode.parentNode.nextSibling;
	var editor = ifrm.contentWindow.document;
	//ifrm.contentWindow.focus();
	if (editor.body.pos){ editor.body.pos.text = ""; editor.body.pos.select(); }
	editor.execCommand("ForeColor", false, color);
	miniReset(ifrm);
}
function callback_BackColor(color,obj)
{
	var ifrm = obj.parentNode.parentNode.nextSibling;
	var editor = ifrm.contentWindow.document;
	//ifrm.contentWindow.focus();
	if (editor.body.pos){ editor.body.pos.text = ""; editor.body.pos.select(); }
	editor.execCommand("BackColor", false, color);
	miniReset(ifrm);
}

/*** 툴바 생성 ***/
function miniSetToolbar()
{
	return "<div style='padding-left:5px;text-align:left'>" 
	+ miniSetFont() 
	+ miniSetSize() 
	+ miniSetButton("Bold") 
	+ miniSetButton("Italic")
	+ miniSetButton("Underline")
	+ miniSetButton("StrikeThrough")
	+ "<img src='" + miniPath + "img/seperator.gif' align=absmiddle hspace=4>"
	+ miniSetButton("ForeColor")
	+ miniSetButton("BackColor")
	+ "<img src='" + miniPath + "img/seperator.gif' align=absmiddle hspace=4>"
	+ miniSetButton("JustifyLeft")
	+ miniSetButton("JustifyCenter")
	+ miniSetButton("JustifyRight")
	+ "<img src='" + miniPath + "img/seperator.gif' align=absmiddle hspace=4>"
//	+ miniSetButton("InsertTable")
//	+ miniSetButton("CreateLink")
	+ miniSetButton("InsertImage")
	+ "<img src='" + miniPath + "img/seperator.gif' align=absmiddle hspace=4>"
//	+ miniSetButton("ShowBorder")
	+ miniTableBox()
	+ miniColorBox()
	+ "</div><div style='display:none;font:bold 8pt tahoma;padding:5px 10px'>miniEditor v1.0</div><div style='position:absolute;right:10px;top:7px'>"
	+ "<input id=_chg_mode_input type=checkbox onclick='miniChgMode(this)' onfocus=blur()> <label for=_chg_mode_input>HTML</label>"
	+ "</div>";
}

function miniSetFont()
{
	var name = ["굴림","돋움","바탕","궁서","굴림체","Arial","Courier","Tahoma"];
	var ret = "<select style='font:8pt tahoma' onchange=\"miniCommand(this,'FontName',this[this.selectedIndex].value)\"><option>Font";
	for (var i=0;i<name.length;i++){
		ret += "<option value='" + name[i] + "'>" + name[i];
	}
	ret += "</select>";
	return ret;
}

function miniSetSize()
{
	var ret = " <select style='font:8pt tahoma' onchange=\"miniCommand(this,'FontSize',this[this.selectedIndex].value)\"><option>Size";
	for (var i=1;i<=7;i++){
		ret += "<option value='" + i + "'>" + i;
	}
	ret += "</select> ";
	return ret;
}

function miniSetButton(mode)
{
	return "<img src='" + miniPath + "img/btn_" + mode + ".gif' onClick=\"miniCommand(this,'" + mode + "')\" style='border:1px solid #f0f0f0;cursor:pointer' onmouseover=miniBtnOver(this) onmouseout=miniBtnOut(this) onmouseup=miniBtnUp(this) onmousedown=miniBtnDown(this) align=absmiddle>";
}

/*** 테이블박스 생성 ***/
function miniTableBox()
{
	var ret = "<table border=1 bordercolor=#cccccc style='border-collapse:collapse'>";
	for (var i=0;i<10;i++){
		ret += "<tr>";
		for (var j=0;j<7;j++) ret += "<td unselectable=on nowrap style='width:15px;height:15px;font-size:0;cursor:pointer' onmouseover='miniChkTable(this," + j + "," + i + ")' onclick='miniSetTable(this," + j + "," + i + ",1)'></td>";
		ret += "</tr>";
	}
	ret += "</table><div style='font:8pt tahoma;border:1px solid #cccccc;width:230px;height:20px;margin-top:3px;padding-top:2px;background:#f7f7f7' align=center></div>";
	ret = "<dd><div style='position:absolute;display:none;border:2px solid #efefef;padding:3px;background:#ffffff'>" + ret + "</div></dd>";
	return ret;
}

function miniChkTable(obj,x,y)
{
	obj = obj.parentNode.parentNode.parentNode;
	for (var i=0;i<10;i++){
		for (var j=0;j<7;j++){
			obj.rows[i].cells[j].style.background = (j<=x && i<=y) ? "#316AC5" : "#ffffff";
		}
	}
	obj.parentNode.getElementsByTagName('div')[0].innerHTML = "<b>" + (x+1) + "</b> cells X <b>" + (y+1) + "</b> rows Table";
}

function miniSetTable(obj,x,y)
{
	obj = obj.parentNode.parentNode.parentNode.parentNode;
	
	var ret = "<table border=1 bordercolor=#cccccc style='border-collapse:collapse' width=99%>";
	for (var i=0;i<=y;i++){
		ret += "<tr>";
		for (var j=0;j<=x;j++) ret += "<td>&nbsp;</td>";
		ret += "</tr>";
	}
	ret += "</table>";
	obj.style.display = "none";
	
	obj = obj.parentNode.parentNode.parentNode.nextSibling;
	miniInsertHTML(obj,ret);
}

/*** HTML 소스 추가 ***/
function miniInsertHTML(ifrm,str)
{
	var editor = ifrm.contentWindow.document;
	ifrm.contentWindow.focus();

	if (editor.selection.type=="Control") editor.selection.clear();
	var rng = editor.selection.createRange();
	rng.pasteHTML(str);
}

/*** 버튼 스타일 처리 ***/
function miniBtnOver(obj)
{
	if (obj.style.borderBottom != "buttonhighlight 1px solid") miniBtnUp(obj);
}

function miniBtnOut(obj)
{
	if (obj.style.borderBottom != "buttonhighlight 1px solid") miniBtnClear(obj);
}

function miniBtnClear(obj)
{
	if (obj){
		obj.style.borderColor = "#f0f0f0";
	}
}

function miniBtnUp(obj)
{
	with (obj.style){
		borderBottom	= "buttonshadow 1px solid";
		borderLeft		= "buttonhighlight 1px solid";
		borderRight		= "buttonshadow 1px solid";
		borderTop		= "buttonhighlight 1px solid";
	}
}

function miniBtnDown(obj)
{
	with (obj.style){
		borderBottom	= "buttonhighlight 1px solid";
		borderLeft		= "buttonshadow 1px solid";
		borderRight		= "buttonhighlight 1px solid";
		borderTop		= "buttonshadow 1px solid";
	}
}

function miniTextareaPos(obj)
{
	obj.pos = document.selection.createRange().duplicate();
}