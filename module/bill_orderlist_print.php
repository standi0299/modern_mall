<?

include "../lib/library.php";
include "bill_orderlist.php";

?>

<center>
	<a href="javascript:orderlist_download();"><input type="button" value="<?=_("다운로드")?>" /></a>
	<a href="javascript:orderlist_print();"><img src="../admin/img/but_12.png"></a>
	<a href="javascript:self.close();"><img src="../admin/img/but_14.png"></a>	
</center>


<script>
var print_content = null;
function orderlist_print()
{
	window.onbeforeprint = before_print;
	window.onafterprint = after_print;
	window.print();
}
function before_print()
{
	print_content = document.body.innerHTML;
	document.body.innerHTML = bill.innerHTML;
}
function after_print()
{
	document.body.innerHTML = print_content;
}
function orderlist_download() {
	location.href = "bill_orderlist_download.php?payno=<?=$_REQUEST[payno]?>";
}
</script>
