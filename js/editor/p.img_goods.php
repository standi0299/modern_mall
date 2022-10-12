<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?=_("파일업로드")?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="imagetoolbar" content="no">
<meta name="keywords" content="">

<style>
body,table,input {font:9pt '돋움'}
form input[type=text],input[type=password],input[type=file],textarea {border: 1px solid #c4cad1; background: #ffffff; color:#444;}
#bar {background:#333333; color:#ffffff; font:bold 8pt tahoma; padding:2px 6px 4px;}
.btn {border:0px;padding:3px 8px;background:#000;color:#fff;cursor:pointer; font:bold 8pt tahoma;}
</style>
</head>

<body style="margin:0">

<div id="bar">File Upload</div>
<div style="padding:5px 10px">

	<form method="post" action="indb_goods.php" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="imgUpLoad">

	<div style="border:1px solid #cccccc; background:#f7f7f7; padding:10px; margin:5px 0;">
	<input type="file" name="file" style="width:100%">
	</div>

	<div align="right">
	<input type="submit" value="Upload" class="btn"> 
	<input type="button" value="Close" class="btn" onclick="parent.miniCloseLayer()">
	</div>

	</form>

</div>

</body>
</html>