<?

/*
* @date : 20190207
* @author : kdk
* @brief : 재주문 요청 (임시 사용).
* @brief : PODs 파일 복사 및 장바구니 처리.
* @request : 픽스토리
* @desc :
* @todo :
*/

set_time_limit(0);
include_once "../lib/library.php";

$msg = "";
$url = "";

$db->start_transaction();
try {

    $ret[SetReOrderResult] = readurl("http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/SetReOrder2.aspx?storageid=$_GET[storageid]&orderStateFlag=Y");
    $ret[SetReOrderResult] = explode("|",$ret[SetReOrderResult]);
    
    if ($ret[SetReOrderResult][0] == "success"){
        list ($goodsno, $optno, $addoptno) = $db->fetch("select goodsno, optno, addoptno from exm_edit where storageid = '$_GET[storageid]'",1);

        $db->query(
        "insert into exm_edit (storageid,goodsno,optno,addoptno,cid,mid,title,pods_use,podsno,podskind,catno,open_gallery,state,_hide,updatedt)
        select 
        '{$ret[SetReOrderResult][1]}',goodsno,optno,addoptno,cid,mid,title,pods_use,podsno,podskind,catno,open_gallery,2,1,now() 
        from exm_edit where storageid = '$_GET[storageid]';
        ");        

        $url = "cart.php?mode=cart&mode2=reorder&goodsno=$goodsno&optno=$optno&addopt=$addoptno&storageid={$ret[SetReOrderResult][1]}";
        $msg = "편집보관함에 저장 후 장바구니로 이동합니다";
    
    }else{
        $url = $_SERVER[HTTP_REFERER];
        $msg = $ret[SetReOrderResult][1];
    }    

    $db->query("commit");

} catch(Exception $e) {
    
    $db->query("rollback");
    echo "FAIL:".$e;
    exit;

}
$db->end_transaction();

if($url && $msg) msg($msg,$url);

?>