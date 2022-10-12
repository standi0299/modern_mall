<?

include "../lib/class.cart.php";

function f_getcntCart(){

	$cart = new Cart();
	return $cart->totea;

}

?>