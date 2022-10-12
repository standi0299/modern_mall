<?

include "../lib/library.php";
include "bill_proc_extra_cart.php";

?>

<center>
	<a href="javascript:estimate_print();"><img src="../admin/img/but_12.png"></a>
	<a href="javascript:self.close();"><img src="../admin/img/but_14.png"></a>	
</center>


<script>
var print_content = null;
function estimate_print()
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
</script>