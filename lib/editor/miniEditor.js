var miniPath;
var miniMode;
var miniDir = miniBase = "";
var miniDepth = 0;
var miniTextareaEditor = new Array();
var miniColorMode = new Array();
var miniSelectRng = new Array();
var miniTextareaAll = document.getElementsByTagName('textarea');
var mini_bHeader = "\
<html><head>\
<meta http-equiv='content-type' content='text/html; charset=euc-kr'>\
<style>td,th {border:0px #bfbfbf dotted;};body {margin:10px;white-space:-moz-pre-wrap;word-break:break-all;};body,table {font:x-small 돋움;}; img{border:0}; input {border:1px solid #cccccc}</style>\
</head>\
";
var mini_bHeader2 = "\
<html><head>\
<meta http-equiv='content-type' content='text/html; charset=euc-kr'>\
<style>body {margin:10px;white-space:-moz-pre-wrap;word-break:break-all;};body,table {font:9pt Courier New;}; img{border:0}; p{margin:0}</style>\
</head>\
";

function miniEditor(path,dir,base,depth)
{
	miniPath = path;
	if (dir) miniDir = dir;
	if (base) miniBase = base;
	if (depth) miniDepth = depth;
	for (var i=0;i<miniTextareaAll.length;i++){
		if (miniTextareaAll[i].getAttribute('type')=="editor"){
			miniTextareaEditor[miniTextareaEditor.length] = miniTextareaAll[i];
			miniSetFrame(miniTextareaEditor[miniTextareaEditor.length-1]);
		}
	}
	document.write("<div id=miniDiv style='position:absolute;left:0;top:-999;border:1px solid #cccccc'><iframe id=miniIframe frameborder=0></iframe></div>");
}

function miniSetFrame(obj)
{
	if (obj.getAttribute('isEditorLoad')==true) return;
	var name = obj.getAttribute('name');

	obj.setAttribute('isEditorLoad',true);
	with (obj.style){
		font = "x-small Courier New";
		padding = "10px";
		backgroundColor = "#414141";
		color = "#FFFFE0";
		display = "none";
		border = "1px solid #cccccc";
	}

	var miniEditorIframe = document.createElement('iframe');
	miniEditorIframe.setAttribute("id","miniIframe_"+name);
	miniEditorIframe.setAttribute("frameBorder",0);
	with (miniEditorIframe.style){
		width = obj.style.width;
		height = obj.style.height.replace("px","") - 2;
		border = "1px solid #cccccc";
		marginTop = "1px";
	}
	obj.parentNode.insertBefore(miniEditorIframe,obj);

	var miniEditorContent = miniEditorIframe.contentWindow.document;
	miniEditorContent.designMode = "on";
	miniEditorContent.open();
	miniEditorContent.write(mini_bHeader);
	if (miniBase) miniEditorContent.write("<base href='" + miniBase + "'>");
	miniEditorContent.close();


	/** 편집용 **/
	var miniEditorIframe2 = document.createElement('iframe');
	miniEditorIframe2.setAttribute("id","miniIframeEdt_"+name);
	miniEditorIframe2.setAttribute("frameBorder",0);
	with (miniEditorIframe2.style){
		width = obj.style.width;
		height = obj.style.height.replace("px","") - 2;
		border = "1px solid #cccccc";
		marginTop = "1px";
		display = "none";
	}
	obj.parentNode.insertBefore(miniEditorIframe2,obj);

	var miniEditorContent2 = miniEditorIframe2.contentWindow.document;
	miniEditorContent2.designMode = "on";
	miniEditorContent2.open();
	miniEditorContent2.write(mini_bHeader2);
	miniEditorContent2.close();
	/************/

	var objToolbar = document.createElement("div");
	objToolbar.innerHTML = miniSetToolbar(name,obj.toolbar);
	miniEditorIframe.parentNode.insertBefore(objToolbar,miniEditorIframe);
	objToolbar.style.width = obj.style.width;
	objToolbar.style.background = "#f0f0f0";

	if (document.attachEvent){
		miniEditorContent.body.attachEvent("onclick",function(){miniReset(miniEditorIframe)},false);
		miniEditorContent.body.attachEvent("onblur",function(){miniHtml2Source(miniEditorContent,obj)},false);
		miniEditorContent2.body.attachEvent("onblur",function(){miniEmp2Source(miniEditorContent2,obj)},false);
		miniEditorContent.attachEvent("onkeypress",function(){miniEnterPress(miniEditorIframe.contentWindow)},false);
    }

	if (obj.getAttribute("mode")=="source"){
		var x = objToolbar.getElementsByTagName('table')[1].rows[0].cells[0].getElementsByTagName('img')[1];
		miniEditorMode(x,"source");
		miniMode = "source";
	} else {
		miniEditorContent.body.innerHTML = obj.value;
		miniMode = "editor";
	}

	miniCommand(objToolbar.getElementsByTagName('table')[2].getElementsByTagName('img')[16],'ShowBorder');
}

function miniEnterPress(obj)
{
	var event = obj.event;
	if (event.keyCode == 13){
		if (event.shiftKey == false){
			var sel = document.selection.createRange();
			sel.pasteHTML('<br>');
			event.cancelBubble = true;
			event.returnValue = false;
			sel.select();
			return false;
		} else {
			return event.keyCode = 13;
		}
	}
}

function miniRemoveFrame(obj)
{
	obj.setAttribute('isEditorLoad',false);
	with (obj.style){
		font = "9pt Courier New";
		padding = "0px";
		backgroundColor = "#ffffff";
		color = "#008000";
		display = "block";
		border = "1px solid #d9d9d9";
	}
	obj.parentNode.removeChild(obj.previousSibling);
	obj.parentNode.removeChild(obj.previousSibling);
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

function miniEmp2Source(html,source)
{
	source.value = html.body.innerText;
}

function miniSetToolbar(name,vtollbar)
{	
	var _vtollbar = (vtollbar=="no") ? 'style="display:none"' : '';
	var ret = '\
	<table id=miniToolbar_' + name + ' width=100% cellpadding=0 cellspacing=0 ' + _vtollbar + '>\
	<tr>\
		<td style="padding-top:5px">\
		<table width=100% cellpadding=0 cellspacing=0>\
		<tr>\
			<td style="padding:0 10px" nowrap height=27 unselectable=on>\
			<img src="' + miniPath + 'img/btn_editor.gif" border=0 align=absmiddle style="display:none;cursor:pointer" onclick="miniEditorMode(this,\'editor\')">\
			<img src="' + miniPath + 'img/btn_source.gif" border=0 align=absmiddle style="cursor:pointer" onclick="miniEditorMode(this,\'source\')">\
			</td>\
			<td id=miniToolbarInner_' + name + ' width=100% unselectable=on>\
			<table cellpadding=2 cellspacing=0>\
			<tr>\
				<td>' + miniSetFont() + '</td>\
				<td>' + miniSetSize() + '</td>\
				<td>' + miniSetButton("Bold") + miniSetButton("Italic") + miniSetButton("Underline") + miniSetButton("StrikeThrough") + '</td>\
				<td><img src="' + miniPath + 'img/seperator.gif"></td>\
				<td>' + miniSetButton("ForeColor") + miniSetButton("BackColor") + '</td>\
				<td><img src="' + miniPath + 'img/seperator.gif"></td>\
				<td>' + miniSetButton("JustifyLeft") + miniSetButton("JustifyCenter") + miniSetButton("JustifyRight") + '</td>\
				<td><img src="' + miniPath + 'img/seperator.gif"></td>\
				<td>' + miniSetButton("InsertTable") + miniSetButton("CreateLink") + miniSetButton("InsertImage") + '</td>\
				<td><img src="' + miniPath + 'img/seperator.gif"></td>\
				<td>' + miniSetButton("ShowBorder") + '</td>\
				<!--<td>' + miniSetButton("InsertMovie") + '</td>-->\
			</tr>\
			<tr>\
				<td colspan=3></td>\
				<td>' + miniColorBox() + '</td>\
				<td colspan=2></td>\
				<td>' + miniTableBox() + '</td>\
			</tr>\
			</table>\
			</td>\
		</tr>\
		</table>\
		</td>\
	</tr>\
	</table>\
	';
	return ret;
}

function miniColorBox()
{
	var ret = "";
	var arr = new Array(
			"#FFC0C0","#FFF000","#FFFFE0","#E0FFE0","#C0E0FF","#30C0FF","#F0C0FF","#FFFFFF",
			"#FF8080","#FFC000","#FFFF80","#80FFC0","#C0E0FF","#2080D0","#FF80FF","#C0C0C0",
			"#FF0000","#FF8000","#FFFF00","#00FF00","#00FFFF","#0000FF","#FF00FF","#808080",
			"#800000","#604800","#808000","#008000","#008080","#000080","#800080","#000000"
			);
	for (var i=0;i<arr.length;i++){
		if (i && i%8==0) ret += "</tr><tr>";
		ret += "<td unselectable=on nowrap style='width:16px;height:13px;border-top:1 solid #cccccc;font:0;border-left:1 solid #cccccc;cursor:pointer;background:" + arr[i] + "' onclick=\"miniCommand(this,'Color','" + arr[i] + "')\">&nbsp;</td>";
	}
	ret = "<div style='position:absolute;display:none;border:2px solid #efefef;padding:3px;background:#f7f7f7'><table><tr>" + ret + "</tr></table></div>";
	return ret;
}

function miniEditorMode(obj,mode)
{
	miniMode = mode;

	var id = obj.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.getAttribute("id");
	id = id.replace("miniToolbar_","");
	var miniTextareaEditor = document.getElementsByName(id)[0];
	var miniEditorIframe = document.getElementById("miniIframe_" + id);
	var miniEditorContent = miniEditorIframe.contentWindow.document;

	var miniEditorIframe2 = document.getElementById("miniIframeEdt_" + id);
	var miniEditorContent2 = miniEditorIframe2.contentWindow.document;

	switch (mode){
		case "editor":
			//miniTextareaEditor.style.display = "none";
			miniEditorIframe2.style.display = "none";
			miniEditorIframe.style.display = "block";
			miniEditorContent.body.innerHTML = miniTextareaEditor.value;
			obj.style.display = "none";
			obj.parentNode.getElementsByTagName('img')[1].style.display = "block";
			obj.parentNode.parentNode.getElementsByTagName('td')[1].style.display = "block";
			
			miniEditorContent.execCommand("SelectAll", false, null);
			var rng = miniEditorContent.selection.createRange();
			rng.collapse(false);
			rng.select();
			miniEditorIframe.contentWindow.focus();
			break;
		case "source":
			miniEditorContent2.body.innerHTML = sEMP_HTML(tagSort(miniTextareaEditor.value));
			//miniEditorContent2.body.innerHTML = sEMP_HTML(miniTextareaEditor.value);

			//miniTextareaEditor.style.display = "block";
			miniEditorIframe2.style.display = "block";
			miniEditorIframe.style.display = "none";
			obj.style.display = "none";
			obj.parentNode.getElementsByTagName('img')[0].style.display = "block";
			obj.parentNode.parentNode.getElementsByTagName('td')[1].style.display = "none";
			//miniTextareaEditor.focus();
			//miniTextareaEditor.value += "";
			miniEditorIframe2.contentWindow.focus();
			break;
	}
}

function miniSetButton(mode)
{
	return "<img src='" + miniPath + "img/btn_" + mode + ".gif' onClick=\"miniCommand(this,'" + mode + "')\" style='border:1px solid #f0f0f0;cursor:pointer' onmouseover=miniBtnOver(this) onmouseout=miniBtnOut(this) onmouseup=miniBtnUp(this) onmousedown=miniBtnDown(this)>";
}

function miniSetFont()
{
	var name = new Array("굴림","돋움","바탕","궁서","굴림체","Arial","Courier","Tahoma");
	var ret = "<select style='font:8pt tahoma' onchange=\"miniCommand(this,'FontName',this[this.selectedIndex].value)\"><option>Font";
	for (var i=0;i<name.length;i++){
		ret += "<option value='" + name[i] + "'>" + name[i];
	}
	ret += "</select>";
	return ret;
}

function miniSetSize()
{
	var ret = "<select style='font:8pt tahoma' onchange=\"miniCommand(this,'FontSize',this[this.selectedIndex].value)\"><option>Size";
	for (var i=1;i<=7;i++){
		ret += "<option value='" + i + "'>" + i;
	}
	ret += "</select>";
	return ret;
}

function miniCommand(obj,cmd,val)
{
	if (cmd=="Color") obj = obj.parentNode.parentNode.parentNode.parentNode;
	var id = obj.parentNode.parentNode.parentNode.parentNode.parentNode.getAttribute("id");
	id = id.replace("miniToolbarInner_","");
	
	switch (cmd){
		case "InsertMovie":
			upload_open(id);
			return; break;
		case "InsertTable":
			var inner = document.getElementById('miniToolbarInner_'+id).getElementsByTagName('div');
			miniLayer(inner[1]);
			return; break;
		case "InsertImage":
			//window.open(miniPath + "popup.img.php?id=" + id,"","width=400,height=510");
			miniPopupLayer(obj.parentNode.parentNode,miniPath + "popup.img.php?dir=" + miniDir + "&id=" + id + "&depth=" + miniDepth,500,215);
			return; break;
		case "ForeColor": case "BackColor":
			var inner = document.getElementById('miniToolbarInner_'+id).getElementsByTagName('div');
			if (inner[0].style.display!="block" || miniColorMode[id]==cmd) miniLayer(inner[0]);
			miniColorMode[id] = cmd;
			return; break;
		case "Color":
			var inner = document.getElementById('miniToolbarInner_'+id).getElementsByTagName('div');
			miniLayer(inner[0]);
			cmd = miniColorMode[id];
			break;
		case "ShowBorder":
			id = "miniIframe_" + id;
			obj = document.getElementById(id);
			var styleSheet = obj.contentWindow.document.styleSheets;
			var isBordered = (styleSheet[0].rules[0].style.border=="#bfbfbf 1px dotted") ? 0 : 1;
			obj.setAttribute('isBordered',isBordered)
			
			styleSheet[0].rules[0].style.border = (isBordered) ? "#bfbfbf 1px dotted" : "#bfbfbf 0px dotted";
			styleSheet[0].rules[1].style.border = (isBordered) ? "#bfbfbf 1px dotted" : "#bfbfbf 0px dotted";
			miniReset(obj);
			return; break;
	}
	
	id = "miniIframe_" + id;
	obj = document.getElementById(id).contentWindow.document;
	obj.execCommand(cmd, false, val);
	document.getElementById(id).contentWindow.focus();
	miniReset(document.getElementById(id));
}

function miniLayer(obj)
{
	obj.style.display = (obj.style.display!="block") ? "block" : "none";
}

function miniTableBox()
{
	var ret = "<table border=1 bordercolor=#cccccc style='border-collapse:collapse'>";
	for (var i=0;i<10;i++){
		ret += "<tr>";
		for (var j=0;j<7;j++) ret += "<td unselectable=on nowrap style='width:20px;height:15px;font-size:0;cursor:pointer' onmouseover='miniChkTable(this," + j + "," + i + ")' onclick='miniSetTable(this," + j + "," + i + ",1)'></td>";
		ret += "</tr>";
	}
	ret += "</table><div style='font:8pt tahoma;border:1px solid #cccccc;width:100%;height:20px;margin-top:3px;padding-top:2px;background:#f7f7f7' align=center></div>";
	ret = "<div id=x style='position:absolute;display:none;border:2px solid #efefef;padding:3px;background:#ffffff'>" + ret + "</div>";
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
	
	var ret = "<table width=99%>";
	for (var i=0;i<=y;i++){
		ret += "<tr>";
		for (var j=0;j<=x;j++) ret += "<td></td>";
		ret += "</tr>";
	}
	ret += "</table>";
	miniLayer(obj);
	obj = obj.parentNode.parentNode.parentNode.parentNode.parentNode;
	var id = obj.getAttribute("id");
	id = id.replace("miniToolbarInner_","");
	id = "miniIframe_" + id;
	obj = document.getElementById(id);
	miniSetHtml(obj,ret);
}

function miniSetHtml(obj,str)
{
	var miniEditorContent = obj.contentWindow.document;

	obj.contentWindow.focus();
	if (miniEditorContent.selection.type=="Control") miniEditorContent.selection.clear();
	var rng = miniEditorContent.selection.createRange();
	rng.pasteHTML(str);
}

function miniReset(obj)
{
	var arr = new Array("Bold","Italic","Underline","StrikeThrough");
	var id = obj.getAttribute("id");
	id = "miniToolbarInner_" + id.replace("miniIframe_","");
	var imgs = document.getElementById(id).getElementsByTagName('img');
	for (var i=0;i<arr.length;i++){
		if (obj.contentWindow.document.queryCommandValue(arr[i])) miniBtnDown(imgs[i]);
		else miniBtnClear(imgs[i]);
	}

	var arr = new Array("FontName","FontSize");
	var sels = document.getElementById(id).getElementsByTagName('select');
	for (var i=0;i<arr.length;i++){
		miniSetSelect(sels[i],obj.contentWindow.document.queryCommandValue(arr[i]));
	}

	if (obj.getAttribute('isBordered')==1) miniBtnDown(imgs[16]);
	else miniBtnClear(imgs[16]);
	
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
	obj.style.borderColor = "#f0f0f0";
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

function miniPopupLayer(obj,src,w,h)
{
	var miniDiv = document.getElementById('miniDiv');
	var miniIframe = document.getElementById('miniIframe');

	miniDiv.style.pixelLeft = getObjLeft(obj);
	miniDiv.style.pixelTop = getObjTop(obj) + obj.offsetHeight;

	miniIframe.style.width = w;
	miniIframe.style.height = h;
	miniIframe.src = src;
}

function miniPopupLayerClose()
{
	var miniDiv = document.getElementById('miniDiv');
	miniDiv.style.pixelTop = -999;
}

function getObjTop(obj){
	if (obj.offsetParent == document.body) return obj.offsetTop;
	else return obj.offsetTop + getObjTop(obj.offsetParent);
}

function getObjLeft(obj){
	if (obj.offsetParent == document.body) return obj.offsetLeft;
	else return obj.offsetLeft + getObjLeft(obj.offsetParent);
}

sEMP_REG_HTML = new RegExp();
sEMP_REG_HTML_ATTRIBUTE = new RegExp();

sEMP_REG_HTML.compile(/\&lt;(\/?\w+)((?:[^>&]|&(?!lt;))*)>/g);
sEMP_REG_HTML_ATTRIBUTE.compile(/(?:\s*([^=]*)\s*=\s*((?:\"[^\"]*\")|(?:\'[^\']*\')|(?:[^\s]*)))|(\w+)/g);

function sEMP_HTML(str)
{
	str = str.replace(/&/g,"&amp;");
	str = str.replace(/\x20/g," ")

	str = str.replace(/</g,"&lt;");
	str = str.replace(sEMP_REG_HTML,
		function($0,$1,$2) {
			if($2) $2 = sEMP_HTMLATTRIBUTE($2);
			str = "<font color=0000ff>&lt;"+$1+$2+"&gt;</font>"
			return str;
		}
	);
	str = str.replace(/\r/g,"<br />");
	return str;
}

function sEMP_HTMLATTRIBUTE(str)
{
	str=str.replace(sEMP_REG_HTML_ATTRIBUTE,
		function($0,$1,$2,$3) {
			if($3) return " <font color=ff0000>" + $3 + "</font>";
			else return " <font color=ff0000>"+$1+"</font>=<font color=ff00ff>"+$2+"</font>";
		});
	return str;
}

function tagSort(str)
{
	//str=str.replace(/(<\/\w+>)/g,"$1\r");
	return str;
}

function upload_open(id) {
    var oIFrame = document.frames("miniIframe_" + id); // 내용 입력용 위지웍 편집 영역 ["content__Frame"은 Iframe 이름값 ]
    //var oForm = document.forms["fmBoard"]; // 게시물 입력용 폼 객체 ["fmBoard"은 폼 네임 값 ]
    var key = "1o2b0z2y8u2a3t2v6a6"; // 판도라TV에서 발급한 인증키 ["xxxxxxxxxxx"은 발급 받은 인증키 값 ]
    var userid = " guest"; // 게시판 사이트의 사용자 아이디 ["guest"은 회원 아이디 값 ]
    var returnPath = "http://firstmall.mirrh.com/lib/editor/pandora_result.php"; // 판도라TV에서 제공되는 리턴 페이지의 경로 [다운로드 한 페이지의 Full URL ]
    //var title = oForm.title.value; // 게시물 제목(판도라TV에 등록되는 동영상의 제목) ["title"은 제목 필드 값]
	var title = "title";

    //아래의 두개 중에 맞는것을 쓰시면 됩니다. 
    //var content = oIFrame.document.frames[0].document.body.innerText; // 게시물 내용(판도라TV에 등록되는 동영상의 내용) [
    var content = oIFrame.document.body.innerText; // 게시물 내용(판도라TV에 등록되는 동영상의 내용) [
    oPandora.rtnFunction = pandoraLink; // 업로드 완료시 호출되는 함수 치환 
    oPandora.open(key, userid, returnPath, title, content); // 업로드 창 오픈 
}
function pandoraLink() { // 업로드 완료 시 호출될 함수 
    //var oIFrame = document.frames("miniIframe_contents"); // 내용 입력용 위지웍 편집 영역 
    //var oForm = document.forms["fmBoard"]; // 게시물 입력용 폼 객체 
    var sEmbedTag = oPandora.getEmbedTag(1); // 동영상 플레이 관련 HTML tag

    // 아래의 두개중에 맞는것을 쓰시면 됩니다. 
    //oIFrame.document.frames[0].document.body.innerHTML += sEmbedTag; // 게시판 내용에 동영관 플레이 HTML tag 삽입 
	//oIFrame.document.body.innerHTML += sEmbedTag; // 게시판 내용에 동영관 플레이 HTML tag 삽입 

	miniSetHtml(document.getElementById('miniIframe_contents'),sEmbedTag);
}