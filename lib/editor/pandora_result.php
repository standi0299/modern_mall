<html>
<head>
<script language="javascript">
	var title = "<?=addslashes($_POST["title"])?>";
	var url = "<?=$_POST["url"]?>";
	var thumbnail = "<?=$_POST["thumb"]?>";
	var regDate = "<?=$_POST["reg_date"]?>";
	var chkAdult = "<?=$_POST["adult_check"]?>";
	//var prgid = "<?=$_POST["prgid"]?>";
	//var content = "<?=str_replace("\r", "\\r", str_replace("\n", "\\n", addslashes($_POST["body"])))?>";
	if(opener) {
		try{
			if(opener.oPandora) {
				opener.oPandora.setVodInfo(url, title, thumbnail, regDate, chkAdult);//, prgid, content
				window.close();
			}
		} catch(e) {
			alert('<?=_("오류")?>!\n\n<?=_("오프너를 잃어 버렸거나")?>\n<?=_("판도라 TV upload API를 참조할 수 없습니다.")?>');
		}
	}
</script>
</head>
<body>
</body>
</html>