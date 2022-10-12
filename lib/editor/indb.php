<?

switch ($_POST[mode]){

	case "InsertImage":

		$_POST[dir] = trim($_POST[dir]);
		for ($i=0;$i<$_POST[depth];$i++) $redir .= "../";
		if (!$_POST[dir]) $_POST[dir] = "editor";
		$dir = "../../data/$_POST[dir]/";

		if (!preg_match("/^image/",$_FILES[mini_file][type]) && !preg_match("/flash$/",$_FILES[mini_file][type])){
			echo "<script>alert('{$_FILES[mini_file][type]} "._("이미지 파일이나 플래시 파일만 업로드가 가능합니다")."');</script>";
			exit;
		}

		if (is_uploaded_file($_FILES[mini_file][tmp_name])){
			$div = explode(".",$_FILES[mini_file][name]);
			$filename = time().".".$div[count($div)-1];
			move_uploaded_file($_FILES[mini_file][tmp_name],$dir.$filename);
			chmod($dir.$filename,0777);
		}
		//$src = "http://".$_SERVER[SERVER_NAME].dirname($_SERVER[PHP_SELF])."/".$dir.$filename;
		$src = $redir.$dir.$filename;
		if ($_POST[imgWidth] && $_POST[imgHeight]) $size = " width='$_POST[imgWidth]' height='$_POST[imgHeight]'";

		if ($filename) echo "<script>parent.parent.miniSetHtml(parent.parent.document.getElementById('miniIframe_{$_POST[id]}'),\"<img src='$src' $size>\");</script>";
		echo "<script>parent.parent.miniPopupLayerClose();</script>";
		break;

}

?>