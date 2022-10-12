var miniTextareaEditor = [];
var miniColorMode = [];
var miniTextareaAll = document.getElementsByTagName('textarea');
var mini_bHeader = "\
<html><head>\
<meta http-equiv='content-type' content='text/html; charset=euc-kr'>\
<style>\
td,th {border:0px #bfbfbf dotted;};\
body {margin:10px;white-space:-moz-pre-wrap;word-break:break-all;};\
body,table {font:9pt Courier New;};\
img{border:0};\
input {border:1px solid #cccccc}\
div.code {border: 1px solid #cccccc;background: #f7f7f7;padding:10px;font:9pt Courier New;margin:5px 0;}\
</style>\
</head>\
";
var miniPath = "";

function miniEditor()
{
	for (var i=0;i<miniTextareaAll.length;i++){
		if (miniTextareaAll[i].getAttribute('type')=="editor"){
			miniTextareaEditor[miniTextareaEditor.length] = miniTextareaAll[i];
			miniSetFrame(miniTextareaEditor[miniTextareaEditor.length-1]);
		}
	}
}

function miniSetFrame(obj)
{
	var name = obj.getAttribute('name');
	
	with (obj.style){
		font = "9pt Courier New";
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
	miniEditorContent.close();

	miniEditorContent.body.innerHTML = obj.value;

	var objToolbar = document.createElement("div");
	objToolbar.innerHTML = miniSetToolbar(name,obj.toolbar);
	miniEditorIframe.parentNode.insertBefore(objToolbar,miniEditorIframe);
	objToolbar.style.width = obj.style.width;
	//objToolbar.style.background = "#f0f0f0";

	if (document.attachEvent){
		miniEditorContent.body.attachEvent("onblur",function(){miniHtml2Source(miniEditorContent,obj)},false);
		miniEditorContent.attachEvent("onkeypress",function(){miniEnterPress(miniEditorIframe.contentWindow)},false);
    }
}

function miniSetToolbar(name,vtollbar)
{	
	var ret = '\
	<div id=miniToolbar_' + name + '>' + miniSetButton("InsertBox") + miniSetButton("ForeColor") + '</div>\
	<div id=miniToolbarInner_' + name + '>' + miniColorBox() + '</div>\
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
		ret += "<td unselectable=on nowrap style='width:16px;height:13px;border-top:1 solid #cccccc;font:0;border-left:1 solid #cccccc;cursor:pointer;background:" + arr[i] + "' onclick=\"miniCommand(this.parentNode.parentNode.parentNode.parentNode,'Color','" + arr[i] + "')\">&nbsp;</td>";
	}
	ret = "<div style='position:absolute;display:none;border:2px solid #efefef;padding:3px;background:#f7f7f7'><table><tr>" + ret + "</tr></table></div>";
	return ret;
}

function miniSetButton(mode)
{
	//return "<button onClick=\"miniCommand(this,'" + mode + "')\" style='border:1px solid #cccccc;background:#333333;color:#ffffff;font:bold 8pt tahoma'>" + mode + "</button>";
	return "<a href='javascript:void(0)' onClick=\"miniCommand(this,'" + mode + "')\"><b class=eng>[" + mode + "]</b></a> ";
}

function miniLayer(obj)
{
	obj.style.display = (obj.style.display!="block") ? "block" : "none";
}

function miniCommand(obj,cmd,val)
{
	var id = obj.parentNode.getAttribute("id");
	id = id.replace("miniToolbarInner_","");
	id = id.replace("miniToolbar_","");
	
	switch (cmd){
		case "InsertBox":
			var ret = "<div class=code></div>";
			id = "miniIframe_" + id;
			var obj = document.getElementById(id);
			miniSetHtml(obj,ret);
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
	}

	id = "miniIframe_" + id;
	obj = document.getElementById(id).contentWindow.document;
	obj.execCommand(cmd, false, val);
	document.getElementById(id).contentWindow.focus();
}

function miniSetHtml(obj,str)
{
	var miniEditorContent = obj.contentWindow.document;

	obj.contentWindow.focus();
	if (miniEditorContent.selection.type=="Control") miniEditorContent.selection.clear();
	var rng = miniEditorContent.selection.createRange();
	rng.pasteHTML(str);
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

function miniEditorMode(obj,mode)
{
	switch (mode){
		case "editor":

			break;
		case "source":
			miniTextareaEditor.value = miniEditorContent.body.innerHTML;
			break;
	}
}

function miniHtml2Source(html,source)
{
	if (html.body.innerHTML=="<P>&nbsp;</P>") html.body.innerHTML = "";
	var tmp = html.body.innerHTML;
	source.value = tmp;
}