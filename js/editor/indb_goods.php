<?

include "../../lib/library.php";

/***/switch ($_POST[mode]){/***/
case "imgUp":
	
	include "p.img_goods.php";
	exit; break;

case "imgUpLoad":

	$dir = "../../data/goods_detail/";
	if (!is_uploaded_file($_FILES[file][tmp_name])){
		msg(_('이미지 파일을 업로드해주세요'),-1);
		exit;
	}
	if (is_uploaded_file($_FILES[file][tmp_name])){
		$fsrc = md5(uniqid(rand(), true));
		move_uploaded_file($_FILES["file"]["tmp_name"],$dir.$fsrc);
	}

	$r_dir = "http://".$_SERVER[SERVER_NAME].dirname($_SERVER[PHP_SELF])."/".$dir.$fsrc;
	if ($fsrc) echo "<script>parent.setImage('$r_dir');parent.miniChgMode(parent.$('_chg_mode_input'));</script>";
	echo "<script>parent.miniCloseLayer();</script>";

	exit; break;

/***/}/***/


?>