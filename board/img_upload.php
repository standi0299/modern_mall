<?

include "../lib/library.php";

$m_board = new M_board();

$dir = "../data/tmp/";
if (!is_dir($dir)) {
	@mkdir($dir);
}

$dir = "../data/tmp/board/";
if (!is_dir($dir)) {
	@mkdir($dir);
}

$ls = ls($dir);
foreach ($ls as $v) {
	list($time) = explode("_", $v);
	if ($time < time() - 60*60) {
		unlink($dir.$v);
	}
}

# 이미지 파일여부 결정
$imginfo = getImageSize($_FILES[file][tmp_name][0]);
if (!in_array($imginfo[2] ,array(1,2,3))) {
	msg(_("업로드 가능한 파일형식은 gif,jpg,png의 이미지파일입니다."));
?>

	<script>
	parent.reset_file("<?=$_POST[idx]?>");
	</script>

<?
	exit;
}

$board = $m_board->getBoardSetInfo($cid, $_POST[board_id]);

$filesize = $_FILES[file][size][0];
if ($filesize > $board[limit_filesize]*1024 && $board[limit_filesize]) {
	msg(_("업로드 가능한 파일최대용량을 초과했습니다.")." (".$board[limit_filesize]."KByte)");
?>

	<script>
	parent.reset_file("<?=$_POST[idx]?>");
	</script>

<?
	exit;
}

$fname = time()."_".uniqid();
move_uploaded_file($_FILES[file][tmp_name][0], $dir.$fname);

?>

<script>
parent.fileupload_preview("<?=$_POST[idx]?>", "<?=$dir.$fname?>", "<?=$imginfo[0]?>");
</script>