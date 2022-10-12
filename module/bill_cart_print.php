<?

include "../lib/library.php";
include "bill_cart.php";

?>

<center>
    <a href="javascript:cart_download();"><input type="button" value="<?=_("다운로드")?>" /></a>
    <a href="javascript:cart_print();"><input type="button" value="<?=_("인쇄하기")?>" /></a>
    <a href="javascript:self.close();"><input type="button" value="<?=_("취소")?>" /></a>
	<!--
    <a href="javascript:cart_print();"><img src="../admin/img/but_12.png"></a>
    <a href="javascript:self.close();"><img src="../admin/img/but_14.png"></a>
    -->	
</center>


<script>
var print_content = null;
function cart_print()
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
function cart_download() {
	if ("<?=$_REQUEST[cartno]?>") location.href = "bill_cart_download.php?cartno=<?=$_REQUEST[cartno]?>";
	else if ("<?=$_REQUEST[payno]?>") location.href = "bill_cart_download.php?payno=<?=$_REQUEST[payno]?>";
}
</script>
