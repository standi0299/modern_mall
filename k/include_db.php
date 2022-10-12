<?
Header("p3p: CP=\"CAO DSP AND SO ON\" policyref=\"/w3c/p3p.xml\""); 

include dirname(__FILE__)."/../lib/class.db.php";
$db = new DB(dirname(__FILE__)."/../conf/conf.db.php");


if (strpos($_SERVER[SERVER_ADDR], "192.168.1.") > -1 || $_SERVER[SERVER_ADDR] == "127.0.0.1"){
  $db->query("set names utf8");
} 

include dirname(__FILE__)."/../lib/lib_util.php";      //추가 유틸 함수    20131212  chunter
include dirname(__FILE__)."/../lib/lib.common.php";
?>