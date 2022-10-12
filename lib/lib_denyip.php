<?
/**
 *  특정 ip 차단 
 *  SQL injection(35 server dblog/20200624_err , 20200623_err) 참고
 */

$check_ip = $_SERVER["REMOTE_ADDR"];  //2020.06.24 kkwon 특정 ip 차단
$deny_ip = array(
    "211.150.255",
    "211.153",
);
foreach($deny_ip as $k => $v){
    if(strpos($check_ip, $v)!==false){
        exit();
    }
}
?>