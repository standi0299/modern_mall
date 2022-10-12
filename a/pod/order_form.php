<?
/*
* @date : 20181108
* @author : kdk
* @brief : POD용 (알래스카) 회원주문접수.
* @request :
* @desc :
* @todo :
*/

include "../_header.php";
include "../_left_menu.php";
$m_goods = new M_goods();
$m_pod = new M_pod();

if (!trim($_GET[mid])) {
    //msg(_("회원정보가 선택되지 않았습니다."), -1);
    msgNlocationReplace(_("회원정보가 선택되지 않았습니다."), "member_list_pod.php");
    exit ;
}

### 상품정보 조회.
$goods_data = $m_goods -> getAdminGoodsList($cid, "", "order by a.goodsno");

### 선발행입금액 정보 조회.
$pre_deposit_money = $m_pod->getPreDepositMoney($cid, $_GET[mid]);

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

### 회원정보 추출
$tableName = "exm_member";
$selectArr = "*";
$whereArr = array("cid" => "$cid", "mid" => "$_GET[mid]");
$data = SelectInfoTable($tableName, $selectArr, $whereArr);

if ($data[manager_no]) {
    $data[manager_no] = explode(",",$data[manager_no]);

    foreach ($data[manager_no] as $key => $val) {
        $selected[manager_no][$val] = "selected";
    }    
}

//기본 선택 상품코드.
$basicGoodsno = "1836"; //업체에서 정해주면 수정해야함.

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("주문접수")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("주문접수")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("주문접수")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitForm(this);" enctype="multipart/form-data">
                  <input type="hidden" name="mode" value="order_form_pod">
                  <input type="hidden" id="goodsnm" name="goodsnm">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("아이디")?></label>
                     <div class="col-md-10 form-inline">
                        <?=$data[mid]?>
                        <input type="hidden" name="mid" required id="mid" value="<?=$data[mid]?>" msg='<?=_("아이디를 확인해주세요.")?>'>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("이름")?></label>
                     <div class="col-md-4 form-inline">
                        <?=$data[name]?>
                        <input type="hidden" class="form-control" name="name" value="<?=$data[name]?>" required>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("사업자명")?></label>
                     <div class="col-md-4 form-inline">
                        <?=$data[cust_name]?>
                        <input type="hidden" class="form-control" name="cust_name" value="<?=$data[cust_name]?>">
                     </div>
                  </div>

                  <div class="form-group">    
                     <label class="col-md-2 control-label"><?=_("상품")?></label>
                     <div class="col-md-4 form-inline">
                        <select id="goodsno" name="goodsno" class="form-control" onchange="selectGoods();" required>
                        <option value=""><?=_("상품선택")?></option>
                        <?foreach($goods_data as $k=>$v){?>
                        <option value="<?=$v[goodsno]?>" <?if ($v[goodsno] == $basicGoodsno) {?>selected<?}?>><?=$v[goodsnm]?></option>
                        <?}?>
                        </select>
                     </div>
                  </div>

                  <div class="form-group red">
                     <label class="col-md-2 control-label"><?=_("주문제목")?></label>
                     <div class="col-md-3">
                        <input type="text" class="form-control" name="order_title" required>
                     </div>
                  </div>

                  <div class="form-group red">
                     <label class="col-md-2 control-label"><?=_("공급가격")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="payprice" onkeypress="onlynumber();" onfocusout="autovat();" required>
                     </div>
                  </div>

                  <div class="form-group red">
                     <label class="col-md-2 control-label"><?=_("배송비")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="ship_price" onkeypress="onlynumber();" onfocusout="autovat();" required>
                     </div>
                  </div>

                  <div class="form-group red">
                     <label class="col-md-2 control-label"><?=_("부가세")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="vat_price" onkeypress="onlynumber();" required>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("선발행입금사용금액")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" class="form-control" name="pre_deposit_price" onkeypress="onlynumber();">
                        <input type="hidden" class="form-control" name="pre_deposit_money" value="<?=$pre_deposit_money?>">
                        [사용 가능한 선발행입금액 : <span class="desc" style="color:#28a5f9"><?=$pre_deposit_money?></span>]
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("별도견적여부선택")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="checkbox" name="extopt_flag" value="1" checked="checked" class="form-control" /> <?=_("별도견적")?>
                     </div>
                  </div>

                  <div class="form-group">    
                     <label class="col-md-2 control-label"><?=_("영업담당자")?></label>
                     <div class="col-md-4 form-inline">
                        <select name="manager_no" class="form-control">
                        <option value=""><?=_("영업담당자선택")?></option>
                        <?foreach($r_manager as $k=>$v){?>
                        <option value="<?=$v[mid]?>" <?=$selected[manager_no][$v[mid]]?>><?=$v[name]?></option>
                        <?}?>
                        </select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("견적파일첨부")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="file" name="file" />
                     </div>
                  </div>
                  
                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("주문타입")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="radio" class="form-control" name="order_type" value="off-line" checked><?=_("오프라인주문접수")?>
                        <input type="radio" class="form-control" name="order_type" value="on-line"><?=_("온라인주문접수")?>
                     </div>
                  </div>-->
                  <input type="hidden" name="order_type" value="off-line">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("추가메모(주문사양)")?></label>
                     <div class="col-md-3">
                        <textarea name="memo" class="form-control" rows="5"> </textarea>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("등록")?></button>
                        <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
function submitForm(formObj) {
    try {
        if (form_chk(formObj)) {

            var payprice = parseInt(formObj.payprice.value);
            var vat_price = parseInt(formObj.vat_price.value);
            var pre_deposit_price = parseInt(formObj.pre_deposit_price.value);
            var pre_deposit_money = parseInt(formObj.pre_deposit_money.value);

            if(isNaN(payprice)) payprice = 0;
            if(isNaN(vat_price)) vat_price = 0;
            if(isNaN(pre_deposit_price)) pre_deposit_price = 0;
            if(isNaN(pre_deposit_money)) pre_deposit_money = 0;
            
            //console.log("payprice : " + payprice);
            //console.log("vat_price : " + vat_price);
            //console.log("pre_deposit_price : " + pre_deposit_price);
            //console.log("pre_deposit_money : " + pre_deposit_money);
            
            if(vat_price>=payprice) {
                alert('<?=_("부가세가 공급가격을 초과할 수 없습니다.")?>');
                return false;
            }
            
            if(pre_deposit_price<0) {
                alert('<?=_("선발행입금사용금액은 (-)를 입력할 수 없습니다.")?>');
                formObj.pre_deposit_price.value = "";
                return false;
            }

            if(pre_deposit_price>pre_deposit_money) {
                alert('<?=_("사용가능한 금액을 초과하였습니다.")?>');
                return false;
            }

            formObj.memo.value = Base64.encode(formObj.memo.value);
            
            return true;
        }
        else {
            return false;
        }
    } catch(e) {return false;}
}

//상품선택.
function selectGoods() {
    try {
        //console.log($("#goodsno").val());
        $("#goodsnm").val("");
        if($("#goodsno").val() != "") {
            //console.log($("#goodsno option:selected").text());
            $("#goodsnm").val($("#goodsno option:selected").text())
        }
        
    } catch(e) {return false;}
}

//부가세 계산.
function autovat() {
    try {
        var price_tax = 0;
        var price_sum = 0;
        var payprice = 0;
        var shipprice = 0;

        payprice = parseInt($("input[name='payprice']").val());
        shipprice = parseInt($("input[name='ship_price']").val());

        //console.log("payprice : " + payprice);
        //console.log("shipprice : " + shipprice);

        if(isNaN(payprice)) {
            payprice = 0;
            //console.log("payprice : " + payprice);
        }

        if(isNaN(shipprice)) {
            shipprice = 0;
            //console.log("shipprice : " + shipprice);
        }
        
        price_sum = Math.floor(payprice + shipprice);
        //console.log("price_sum : " + price_sum);
        if (price_sum > 0) {
            price_tax = Math.floor(price_sum * 0.1);
            //console.log("price_tax : " + price_tax);
            $("input[name='vat_price']").val(price_tax);
        }
    } catch(e) {return false;}
}

$(function (){
    if($("#goodsno").val() != "") {
        //console.log($("#goodsno option:selected").text());
        $("#goodsnm").val($("#goodsno option:selected").text())
    }
});
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>