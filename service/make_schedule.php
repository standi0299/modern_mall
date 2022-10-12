<?
include "../_header.php";

$c_month = $month = $_GET["month"];
$year = date("Y");
if (!$month)
{
	$c_month = date("n");
	$month = date("m");
}
//$month = "08";
$day = $year ."-". $month ."-01";
//debug($day);

$loop = array();
$toDay = date("d");
$startIndex = date('w', strtotime($day));
$endDay = date("t", strtotime($day));
//debug($startIndex);
//debug($endDay);
for ($i=1; $i < $startIndex; $i++) { 
	$loop[] = "NOT";
}

for ($i=1; $i <= $endDay; $i++) {
	
	$displayDay = $year ."-". $month ."-".$i;
		
	$weekDay = date('N', strtotime($displayDay));
	
	$addDay = "10";
	if ($weekDay == "3" || $weekDay == "4" || $weekDay == "5")
		$addDay = "12";
	else if ($weekDay == "6")
		$addDay = "12";
	else if ($weekDay == "7")
		$addDay = "11";
	
	$deliveryDay =  date('n/d', strtotime($displayDay. " + " .$addDay. " days"));
	if ($toDay == $i && date("n") == $c_month)
		$loop[] = "<span style='color:coral;'>". $i."<br>$deliveryDay<span>";
	else
	$loop[] = $i."<br>$deliveryDay";
}
//debug($loop);

/*
$blankIndex = count($loop) % 7;
for ($i=$blankIndex; $i < 7; $i++) { 
	$loop[$i] = "NOT";
}
debug($loop);
*/

$tpl->assign("c_month",$c_month);
$tpl->assign("loop",$loop);
$tpl->print_('tpl');

?>