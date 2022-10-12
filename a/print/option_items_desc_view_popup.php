<?
include "../../_header.php";

if ($_GET[id]) {
	$m_print = new M_print();
	
	$data = $m_print->getOptionInfoDesc($_GET[id]);
	
}

?>

<?= $data[opt_desc]?>
