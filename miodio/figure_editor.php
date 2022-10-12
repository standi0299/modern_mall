<?

/*
* @date : 20180905
* @author : kdk
* @brief : 미오디오용 피규어 에디터.
* @desc :
*/

include "../lib/library.php";
include "../lib/class.pods.service.php";

$pods_use = $_GET[pods_use];
$podskind = $_GET[podskind];
$podsno = $_GET[podsno];
$mode = $_GET[mode];
$goodsno = $_GET[goodsno];
$optno = $_GET[optno];
$addopt = $_GET[addopt];
$impoptno = $_GET[impoptno];
$productid = $_GET[productid];
$optionid = $_GET[optionid];
$ea = $_GET[ea];
$vdp = $_GET[vdp];
$templatesetid = $_GET[templatesetid];
$templateid = $_GET[templateid];
$rurl = $_GET[rurl];
$editdate = $_GET[editdate];
$pod_signed = $_GET[pod_signed];
$editor_type = $_GET[editor_type];

$m_goods = new M_goods();

//debug($_GET);

# pods editor class
$pods = new PodsEditor($goodsno);

//상품에 설정된 pods 정보.
$editor_arr = $pods->GetPodsEditor();
//debug($editor_arr);

if($editor_arr) {
    foreach ($editor_arr as $key => $val) {
        if($val[pods_use] == $pods_use && $val[podskind] == $podskind && $val[podsno] == $podsno) {
            $editor = $val;
        }
    }
}
//debug($editor);

//pods편집기별 호출에 필요한 data 생성.
$info = $pods->GetPodsEditorData($editor);

# goods data check
if (!$info){
    msg(_("상품정보가 없습니다."));
}

if ($mode == "view") {
    //storageKey
    $micro = explode(" ",microtime());
    $storageKey = date("Ymd")."-".substr($micro[1].sprintf("%03d",floor($micro[0]*1000)), -4);
    $ucid = strtoupper(substr($cid,0,2));
    $grd = genRandom2();
    
    //storageid (MIFI-20180906-2029JR3YW)
    $storageid = $ucid."FI-".$storageKey.$grd;
    //debug($storageid);
    
    $info[storageid] = $storageid;
}    
else if($mode == "edit" || $_GET[storageid]) {
    $info[storageid] = $_GET[storageid];
}
//debug($info);
//exit;
?>

<form name="wpod_editor_form" action="/miodio/figure/figure.php"  method="POST">
    <input type="hidden" name="mode" value="<?=$mode?>">
    <input type="hidden" name="goodsno" value="<?=$goodsno?>">
    <input type="hidden" name="storageid" value="<?=$info[storageid]?>">
    <input type="hidden" name="productid" value="<?=$info[productid]?>">
    <input type="hidden" name="siteid" value="<?=$info[siteid]?>">
    <input type="hidden" name="product_siteid" value="<?=$info[center_podsid]?>">
    <input type="hidden" name="userid" value="<?=$info[userid]?>">
    <input type="hidden" name="defaultpage" value="<?=$info[defaultpage]?>">
    <input type="hidden" name="minpage" value="10<?=$info[minpage]?>">
    <input type="hidden" name="maxpage" value="10<?=$info[maxpage]?>">
    <input type="hidden" name="options" value="<?=$info[optionid]?>">
    <input type="hidden" name="param" value="<?=$info[param]?>">
    <input type="hidden" name="pname" value="<?=$info[pname]?>">
    <input type="hidden" name="introurl" value="<?=$info[introurl]?>">
    <input type="hidden" name="logourl" value="<?=$info[logourl]?>">
    <input type="hidden" name="displayproductname" value="<?=$info[pname]?>">
    <input type="hidden" name="displayprice" value="<?=$info[displayprice]?>">    
    <input type="hidden" name="displaycount" value="<?=$info[displaycount]?>">
    <input type="hidden" name="requestuser" value="<?=$info[requestuser]?>">
    <input type="hidden" name="adminmode" value="<?=$info[adminmode]?>">
    <input type="hidden" name="templatesetid" value="<?=$info[templatesetid]?>">
    <input type="hidden" name="templateid" value="<?=$info[templateid]?>">
    <input type="hidden" name="pid" value="<?=$info[podsno]?>">
    <input type="hidden" name="opt" value="<?=$info[optionid]?>">
</form>
