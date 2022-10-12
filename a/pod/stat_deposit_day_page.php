<?
/*
* @date : 20181116
* @author : kdk
* @brief : POD용 (알래스카) 일일입금현황.
* @request : 
* @desc :
* @todo :
*/

include "../lib.php";

$m_member = new M_member();
$m_pod = new M_pod();

$addwhere="";

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
    if (array_notnull($postData[deposit_date])){
       if ($postData[deposit_date][0]) $addwhere .= " and deposit_date>='{$postData[deposit_date][0]}'";
       if ($postData[deposit_date][1]) $addwhere .= " and deposit_date<='{$postData[deposit_date][1]}'";
    }
}
//debug($addwhere);

$order_column_arr = array("", "", "", "", "", "", "", "", "", "", "");
$order_data = $_POST[order][0];

$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$list = $m_pod->getStatDepositDay($cid, $addwhere);
$totalCnt = count($list);

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {
    $pdata = array();

    $pdata[] = $value[dt];

    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=1',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method1])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=2',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method2])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=3',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method3])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=4',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method4])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=5',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method5])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=6',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method6])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=7',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method7])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=8',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method8])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=9',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method9])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=10',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method10])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=11',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method11])."</a>";
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=12',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[method12])."</a>";
    
    //합계
    //$pdata[] = number_format($value[remain_price]);
    //$pdata[] = number_format($value[tot_deposit_money]);
    $pdata[] = "<a class='blue' onclick=\"popup('stat_deposit_day_popup.php?sdate=".$value[dt]."&method=',1200,750)\" class=\"hand\" style=\"cursor:pointer;\">".number_format($value[tot_deposit_money])."</a>";

    $psublist[] = $pdata;
}

$plist[data] = $psublist;
echo json_encode($plist)
?>