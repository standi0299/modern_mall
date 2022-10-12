<?

/*
* @date : 20180705
* @author : kdk
* @brief : 관리자 출력 파일 추가.
* @request :
* @desc : 
* @todo : 
*/

include dirname(__FILE__) . "/../_pheader.php";

if (!$_GET[orditem_key]) {
    msg("주문 코드가 넘어오지 못했습니다!", "close");
}

if (!$_GET[storageid]) {
    msg("저장소 코드가 넘어오지 못했습니다!", "close");
}

$optionArr = array();
$m_goods = new M_goods();
$m_order = new M_order();
$m_print = new M_print();

//오아시스 라우터  md_oasis_router 정보 조회.
$oasis_data = $m_print->getOasisRouter($cid);
if ($oasis_data) 
{
    //debug($oasis_data);
    foreach ($oasis_data as $key => $val) {
        //debug($key);
        //debug($val);
        //$oasis_router_machine[$val[machine_id]] = $val[machine_id];

        //debug($val['router_info']);
        //$oasis_router = json_decode(stripslashes($val['router_info']),1);
        $oasis_router = json_decode($val['router_info'],1);
        //debug($oasis_router);
        
        //debug($oasis_router[pname]);
        $oasis_router_machine[$val[machine_id]] = $oasis_router[pname];

        if ($oasis_router[folder_data]) {    
            foreach ($oasis_router[folder_data] as $k => $v) {
                //debug($v);
                //$oasis_router_folder[$val[uniquekey]] = $val[folder];
                
                $oasis_router_folder[$val[machine_id]][$v[uniquekey]] = $v[folder];
            }
        }
    }
}
//debug($oasis_router_machine);
//debug($oasis_router_folder);

$orditem = split("_", $_GET[orditem_key]);
if (is_array($orditem)) 
{
    $payno = $orditem[0];
    $ordno = $orditem[1];
    $ordseq = $orditem[2];
}
//exm_ord_item 정보 조회.
$data = $m_order->getOrdItemInfo($payno, $ordno, $ordseq);
//debug($data);

if ($data[est_order_data])
{
    $est_order_data = json_decode(stripslashes($data[est_order_data]),1);
}

//exm_goods 정보 조회.
$goods_data = $m_goods->getInfo($data[goodsno]);
//debug($goods_data);

if ($goods_data[goods_group_code] == "60") {
    //$order_cnt = $est_order_data[cnt]; //건수 조회.
    if ($goods_data[extra_option] == "DG05" || $goods_data[extra_option] == "OS02")
        $order_cnt = 1;
    else
        $order_cnt = $est_order_data[cnt]; //건수 조회.
}
//debug($order_cnt);

if ($data[est_order_option_desc])
{
    $data[est_order_option_desc] = str_replace("[]", "", $data[est_order_option_desc]);
    $option_desc = explode(";]", $data[est_order_option_desc]);
    $option_desc = str_replace("[", "", $option_desc);
    $option_desc = str_replace(";", ",", $option_desc);
    
    foreach ($option_desc as $key => $val) {
        if ($val) {
            //debug($key);
            //debug($val);
            
            if (strpos($val, "제목:") !== false) {
                $optionArr['title'] = str_replace("제목:", "", $val);
            }
            else if (strpos($val, "규격:") !== false) {
                $optionArr['size'] = $val;
            }
            else if (strpos($val, "표지::") !== false) {
                $val = str_replace("표지::", "", $val);
                $optionArr['outside'] = $val;
            }
            else if (strpos($val, "내지::") !== false) {
                $val = str_replace("내지::", "", $val);
                $val = explode("||", $val);
                $optionArr['inside'] = $val;
            }
            else if (strpos($val, "간지/면지::") !== false) {
                $val = str_replace("간지/면지::", "", $val);
                $val = explode("||", $val);
                $optionArr['inpage'] = $val;
            }
            else if (strpos($val, "후가공::") !== false) {
                $optionArr['after'] = str_replace("후가공::", "", $val);
            }
            else if (strpos($val, "메모:") !== false) {
                $optionArr['memo'] = str_replace("메모:", "", $val);
            }
        }
        
    }

    //debug($optionArr);
}

//exm_pay 정보 조회.
$pay = $m_order->getPayInfo($payno);
//debug($pay);

//exm_ord_upload_file 사용자 업로드 파일 정보 조회.
$upload_file_data = $m_print->getOrdUploadFile($_GET[storageid]);
//debug($upload_file_data);

//md_print_upload_file 정보 조회.
$print_data = $m_print->getPrintUploadFile($_GET[storageid]);
//debug($print_data);
if ($print_data)
{
    
}

$file_type_arr = array('outside' => "표지", "inside" => "내지", "inpage" => "간지", "" => "낱장");
$self = $_SERVER["HTTP_HOST"];
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
    <!-- begin #content -->
    <div id="content" class="content">
        <!-- begin #header -->
        <div id="header" class="header navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span>출력 파일 등록</a>
                </div>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">주문 내용</h4>
            </div>
            <div class="panel-body panel-form">
            <form class="form-horizontal form-bordered" name="frm" method="POST" action="indb.php" enctype="multipart/form-data">
                <input type="hidden" name="mode" value="inspection_upload">
                <input type="hidden" name="storageid" value="<?=$_GET[storageid]?>">
                <input type="hidden" name="payno" value="<?=$payno?>">
                <input type="hidden" name="cartno" value="<?=$data[cartno]?>">

                <div class="form-group">
                    <label class="col-md-3 control-label">상품명</label>
                    <div class="col-md-9">
                        <label class="control-label text-left">
                            <?=$data[goodsnm]?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">사용자 업로드 파일</label>
                    <div class="col-md-9">
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>파일명</th>
                                    <th>크기</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?
                            $cnt = 1;
                            foreach ($upload_file_data as $key => $val)
                            {                                
                            ?>                                
                                <tr>
                                    <td><?=$cnt++?></td>
                                    <td><?=$val[upload_file_name]?></td>
                                    <td><?=round($val[file_size]/1024,2)?> KByte</td>
                                    <td><a href="download.php?src=<?=$val[server_path]?>" class="btn-primary btn-xs m-r-5">다운로드</a></td>
                                </tr>
                            <?                                
                            }
                            ?> 
                            </tbody>
                        </table>                        
                        
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">주문 정보 (제목)</label>
                    <div class="col-md-9">
                        <label class="control-label text-left">
                            <?=$optionArr['title']?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">주문 정보 (규격 / 수량)</label>
                    <div class="col-md-9">
                        <label class="control-label text-left">
                            <b><?=$optionArr['size']?></b>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">주문 정보 (표지)</label>
                    <div class="col-md-9">
                        <label class="control-label text-left">
                            <?=$optionArr['outside']?>                            
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">주문 정보 (내지)</label>
                    <div class="col-md-9">
                        <label class="control-label text-left">
                            <?
                            if ($optionArr['inside']) {
                                foreach ($optionArr['inside'] as $key => $val) {
                            ?>
                                <?=$val?><br>
                            <?
                                }
                            }
                            ?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">주문 정보 (간지/면지)</label>
                    <div class="col-md-9">
                        <label class="control-label text-left">
                            <?
                            if ($optionArr['inpage']) {
                                foreach ($optionArr['inpage'] as $key => $val) {
                            ?>
                                <?=$val?><br>
                            <?
                                }
                            }
                            ?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">주문 정보 (후가공)</label>
                    <div class="col-md-9">
                        <label class="control-label text-left">
                            <?=$optionArr['after']?>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-3 control-label">주문 정보 (메모)</label>
                    <div class="col-md-9">
                        <label class="control-label text-left">
                            <?=$optionArr['memo']?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">관리자 출력 파일 업로드</label>
                    <div class="col-md-9">
                        <?if ($print_data) {?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>파일명</th>
                                    <th>타입</th>
                                    <th>크기</th>
                                    <th>장비</th>
                                    <th>폴더</th>
                                    <th>등록일</th>
                                    <th style="width: 140px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?
                            $cnt = 1;
                            foreach ($print_data as $key => $val)
                            {                                
                            ?>                                
                                <tr>
                                    <td><?=$cnt++?></td>
                                    <td><?=$val[upload_file_name]?></td>
                                    <td><?=$file_type_arr[$val[file_type]]?></td>
                                    <td><?=round($val[file_size]/1024,2)?> KByte</td>
                                    <td><?=$oasis_router_machine[$val[machine_id]]?></td>
                                    <td><?=$oasis_router_folder[$val[machine_id]][$val[uniquekey]]?></td>
                                    <td><?=$val[regist_date]?></td>
                                    <td>
                                        <a href="download.php?src=<?=$val[server_path]?>" class="btn-primary btn-xs m-r-5">다운로드</a>
                                        <a href="javascript:void(0);" onclick="inspectionFileDelete(this,'<?=$val[id]?>','<?=$val[storageid]?>','<?=$val[upload_file_name]?>');" class="btn-danger btn-xs m-r-5">삭제</a>
                                        
                                    </td>
                                </tr>
                            <?                                
                            }
                            ?> 
                            </tbody>
                        </table>
                        <br>
                        <?}?>
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>옵션명</th>
                                    <th width="100px">파일</th>
                                    <th>장비</th>
                                    <th>폴더</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            <?$cnt = 0;?>                           
                            <?if ($optionArr['outside']) {?>
                                <tr>
                                    <td><?=$cnt+1?></td>
                                    <td>표지:<?=$optionArr['outside']?></td>
                                    <td><input type="file" name="file[]" /></td>
                                    <td>
                                        <select name="machine_outside_<?=$cnt?>" class="machine">
                                            <option value="">선택</option>
                                            <?foreach ($oasis_router_machine as $key => $val) {?>
                                                <option value="<?=$key?>"><?=$val?></option>    
                                            <?}?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="folder_outside_<?=$cnt?>">
                                            <option value="">선택</option>
                                        </select>
                                    </td>
                                </tr>
                            <?
                                $cnt++;
                            }
                            ?>

                            <?
                            if ($optionArr['inside']) {
                                
                                //낱장상품은 건수 만큼 파일 업로드를 한다.$order_cnt
                                for ($i=1; $i <= $order_cnt; $i++) {
                                    foreach ($optionArr['inside'] as $key => $val) {
                            ?>
                                <tr>
                                    <td><?=$cnt+1?></td>
                                    <td>내지:<?=$val?></td>
                                    <td><input type="file" name="file[]" /></td>
                                    <td>
                                        <select name="machine_inside_<?=$cnt?>" class="machine">
                                            <option value="">선택</option>
                                            <?foreach ($oasis_router_machine as $key => $val) {?>
                                                <option value="<?=$key?>"><?=$val?></option>    
                                            <?}?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="folder_inside_<?=$cnt?>">
                                            <option value="">선택</option>
                                        </select>
                                    </td>
                                </tr>
                            <?
                                    $cnt++;
                                    }
                                }

                            }    
                            ?>
                            
                            <?
                            if ($optionArr['inpage']) {
                                foreach ($optionArr['inpage'] as $key => $val) {
                            ?>
                                <tr>
                                    <td><?=$cnt+1?></td>
                                    <td>간지/면지:<?=$val?></td>
                                    <td><input type="file" name="file[]" /></td>
                                    <td>
                                        <select name="machine_inpage_<?=$cnt?>" class="machine">
                                            <option value="">선택</option>
                                            <?foreach ($oasis_router_machine as $key => $val) {?>
                                                <option value="<?=$key?>"><?=$val?></option>    
                                            <?}?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="folder_inpage_<?=$cnt?>">
                                            <option value="">선택</option>
                                        </select>
                                    </td>
                                </tr>
                            <?
                                    $cnt++;
                                }
                            }
                            ?>
                           
                            </tbody>
                        </table>                        
                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">관리자 입력 정보</label>
                    <div class="col-md-2">
                        <select name="oasis_router_print_direction">
                            <option value="">선택</option>
                            <option value="STAND" <?if($data[oasis_router_print_direction] == "STAND"){?>selected<?}?>>세움</option>
                            <option value="LAY" <?if($data[oasis_router_print_direction] == "LAY"){?>selected<?}?>>눕힘</option>    
                        </select>
                    </div>
                    <label class="col-md-2 control-label">관리자 작업 메모</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="request2" value="<?=$pay[request2]?>" size="250">
                    </div>                    
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <button type="submit" class="btn btn-sm btn-success">출력 파일 등록</button>
                        <button type="button" class="btn btn-sm btn-default" onclick="window.close();">닫  기</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<script>
$j(document).ready(function() {
    $j(".machine").change(function() {
        var target = $(this).attr("name").replace(/\machine/g, "folder");
        //console.log(target);

        $j("select[name='"+ target +"'] option").remove();
        $j("select[name='"+ target +"']").append("<option value=''>선택</option>");

        <?foreach ($oasis_router_folder as $key => $val) {?>
            var option = "";

            if ($(this).val() == "<?=$key?>") {
                <?foreach ($val as $k => $v) {?>
                    option += "<option value='<?=$k?>'><?=$v?></option>";
                <?}?>
                if (option !== "") {
                    $j("select[name='"+ target +"'] option").remove();
                    $j("select[name='"+ target +"']").append("<option value=''>선택</option>");
                    $j("select[name='"+ target +"']").append(option);
                }
            }
        <?}?>
    });
});

//file삭제
function inspectionFileDelete(obj,id,storageKey,fileName) {
    if(confirm('<?=_("정말 삭제하시겠습니까?")?>')) {
        //location.href = "indb.php?mode=inspection_file_delete&no="+id+"&storage_code="+sid+"&file_name="+fname;
        //ajax 전송
        $j.ajax({
            type : "POST",
            url : "indb.php",
            data : "mode=inspection_file_delete&id="+ id +"&storage_code="+ storageKey +"&file_name="+fileName,
            success:function(data){
                //console.log("data : "+data);
                if(data == "FAIL") {
                    alert("파일 삭제에 실패하였습니다.");
                }
                else {
                    $j(obj).closest("tr").remove();
                    alert("파일 삭제가 완료되었습니다.");
                }
            },   
            error:function(e){
                alert(e.responseText);
            }
        });

    }
}
</script>

<?
include dirname(__FILE__) . "/../_pfooter.php";
?>