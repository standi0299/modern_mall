<?
	$payUrl = $_POST[PayUrl];
	foreach ($_POST as $key => $value) {
		$_POST[$key] = iconv("utf-8", "euc-kr", $value);
	}

?>
<form name="pay_form" method="post" action="<?=$payUrl?>">
<?	
	foreach ($_POST as $key => $value) {
		if ($key != "PayUrl")
			echo "<input type='hidden' name='$key' value='$value'>";		
	}

?>    
</form>

<script>
	document.pay_form.submit();
</script>
