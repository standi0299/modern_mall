<?
include dirname(__FILE__) . "/../library.php";
?>
<title><?=_("이미지 삽입")?></title>
<style>
body,table,input {font:9pt '굴림'}
input {border:1 solid #cccccc}
</style>
<script language="javascript">

var imgWidth = 200;
var imgHeight = 150;
var tmpImg = new Image();
var tmpCut;

function img_preview(src,cut)
{
	if ( document.getElementById('preview') != null ){
		tmpCut = cut;
		document.getElementById('preview').innerHTML = "<img id=prevImg onload='img_resize(this)' onerror=this.src='images/preview.gif'>";
		tmpImg.src = src;
		tmpImg.onload = loadImgSize;
		document.getElementById('prevImg').src = tmpImg.src;
	}
}

function loadImgSize()
{
	if (tmpCut==1) return;
	document.forms[0].imgWidth.value = tmpImg.width;
	document.forms[0].imgHeight.value = tmpImg.height;
}

function img_resize(obj)
{
	if (obj.width*2>obj.height*3){
		if (obj.width>imgWidth) obj.width = imgWidth;
	} else {
		if (obj.height>imgHeight) obj.height = imgHeight;
	}
}

function thumb()
{
	this.rate = [100,100];

	//document.write('');

	this.preview = function(obj,t2)
	{
		var t = document.getElementById('obj_thumb_preview');
		t.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + obj.value + "',sizingMethod=image)";
		var w = t.offsetWidth;
		var h = t.offsetHeight;

		document.forms[0].imgWidth.value = w;
		document.forms[0].imgHeight.value = h;

		if (this.rate[0]*h>this.rate[1]*w){
			var hf = this.rate[1];
			var wf = hf * w / h;
		} else {
			var wf = this.rate[0];
			var hf = wf * h / w;
		}

		if (typeof t2!="object") t2 = document.getElementById(t2);
		with (t2.style){
			filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + obj.value + "',sizingMethod=scale)";
			width = wf;
			height = hf;
		}
	}

}

</script>

<form method=post target=ifrm action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="InsertImage">
<input type=hidden name=id value="<?=$_GET[id]?>">
<input type=hidden name=dir value="<?=$_GET[dir]?>">
<input type=hidden name=depth value="<?=$_GET[depth]?>">

<div style="width:1px;height:1px;font:0;overflow:hidden;position:absolute;top:-999"><div id=obj_thumb_preview style="width:1px;height:1px;"></div></div>

<table width=100% align=center>
<tr>
	<td colspan=2>
	<img src="img/icn_chk.gif" align=absmiddle> <b><?=_("이미지 추가하기")?></b>
	</td>
</tr>
<tr>
	<td width=220 nowrap>
	<table><tr><td align=center style="width:200px;height:150px;background:#ffffff;border:1px solid #cccccc;background:url(img/preview.gif) no-repeat center"><div id=preview>&nbsp;</div></td></tr></table>
	</td>
	<td width=100% valign=top>

	<?=_("삽입할 이미지 선택")?> :
	<div style="padding:3px 0 5px 0"><input type=file name=mini_file style="width:100%" onchange="x.preview(this,'preview')"></div>

	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td><?=_("가로")?>: <input type=text name=imgWidth size=10></td>
		<td align=right><?=_("세로")?>: <input type=text name=imgHeight size=10></td>
	</tr>
	</table><p>

	<input type=submit value=<?=_("확인")?> style="width:100">
	<input type=button value=<?=_("닫기")?> style="width:100" onclick="parent.miniPopupLayerClose()">

	</td>
</tr>
</table>

</form>
<iframe name=ifrm style="display:none"></iframe>

<script>
var x = new thumb;
x.rate = [200,150];
//img_preview("img/preview.gif",1);
</script>