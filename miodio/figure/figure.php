<?
/*
* @date : 20180905
* @author : kdk
* @brief : 미오디오용 피규어 에디터.
* @desc :
*/

include "../../lib/library.php";

$m_goods = new M_goods();

$goodsno = $_REQUEST[goodsno];
$mode = $_REQUEST[mode];

if ($mode == "view") {
    $edit_mode = "new";
    $cart_mode = "cart";
}    
else if($mode == "edit" || $mode == "edit_admin") {
    $edit_mode = "open";
    $cart_mode = "cart_edit_figure";
}

$info[storageid] = $_REQUEST[storageid];

$info[siteid] = $_REQUEST[siteid];
//pods site id 사용안함 cid 를 사용하도록 설정함.
$info[siteid] = $cid;

$info[displayproductname] = $_REQUEST[displayproductname];
$info[displayprice] = number_format($_REQUEST[displayprice]);

//가족구성정보 조회(추가옵션에 설정됨)
$data = $m_goods->getInfo($goodsno);
//debug($data);
if ($data) {
    $adata = $m_goods->getGoodsAddOptList($goodsno);
    //debug($adata);
    
    if ($adata) {
        foreach ($adata as $key => $value) {
            $addata = $m_goods->getGoodsAddOptNoList($value[addopt_bundle_no]);
            //debug($addata);
            foreach ($addata as $k => $v) {
                //debug($v[addoptnm]);
                $family_member[] = array('id' => $v[addoptno], 'name' => $v[addoptnm]);
            }            
        }
    }

    $info[family_member] = stripslashes(to_han(json_encode($family_member)));
    //$info[family_member] = to_han(json_encode($family_member));
    //debug($info[family_member]);
    
    $info[family_member_count] = count($family_member);
    $info[image_ext] = ".jpeg,.jpg,.png";
}

//debug($info);
//exit;
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <script type="text/javascript" charset='utf-8' src="//engine.ilark.co.kr/dev/figure/core/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="//engine.ilark.co.kr/dev/figure/core/miodio.def.js"> </script>
    <script type="text/javascript" src="//engine.ilark.co.kr/dev/figure/core/miodio.tasks.js"> </script>
    <script type="text/javascript" src="//engine.ilark.co.kr/dev/figure/core/xml2json.js?v=6"> </script>
    
    <script type="text/javascript" src="//engine.ilark.co.kr/dev/figure/core/miodio.figure2.js"> </script> 
    <link rel="stylesheet" href="//engine.ilark.co.kr/dev/figure/css/pupa_figure.css">
    <link rel="stylesheet" href="/skin/modern/assets/css/vendors.css">
    <link rel="stylesheet" href="/skin/modern/assets/css/app.css">
</head>
<body>

<div class="figure_web">
  <div class="figure_block"></div>
    <div id="figure">
        <a href="javascript:xs_ret('1|close');" class="figure-close">닫기</a>
        <div class="top">
            <h2>피규어 사진 업로드</h2>
            <p class="figure-name"><?=$info[displayproductname]?> (<?=$info[family_member_count]?>명)</p>
            <p class="figure-price"><?=$info[displayprice]?><span>원</span></p>
        </div>
        
        <div class="open-browser">
            <input  class="figure_orgFile" type="file" name="userfile" accept=".jpg, .jpeg,.png" > 
            <input  class="figure_orgFile2" type="file" name="userfile" accept=".mp4" > 
        <div class="content family">
            <div class="content-inner clearfix">
                <div class="family-list">
                    <h3>가족구성원을 선택해 주세요.</h3>
                </div>
                <div class="sample-wrap clearfix">
                    <div class="sample-top">
                        <p>Sample photo</p>
                        <ul>
                            <li class="on"><a href="#">남</a></li>
                            <li><a href="#">여</a></li>
                        </ul>
                    </div>
                    <!-- 남자 -->
                    <div class="sample-img sample-male" style="background-image:url(//engine.ilark.co.kr/dev/figure/images//figure_sample1.jpg);"></div>
                    <!-- 여자 -->
                    <div class="sample-img sample-female" style="background-image:url(//engine.ilark.co.kr/dev/figure/images/figure_sample2.jpg);"></div>
                </div>

                <div class="figure-attach-wrap">
                    <!-- 이미지일때 -->
                    <div class="img-drag-wrap">

                        <div class="img-drag" id="img-drag" >   
                            
                            <a href="#" class="more"></a>
                            <p class="exp1">사진선택, <img src="//engine.ilark.co.kr/dev/figure/images/icon_plus.png" alt="" />버튼을 클릭해 사진을 선택해 주세요.</p>
                            <p class="exp2">(크롬 브라우저에서는 해당영역으로 사진을<br />
                            <u>드래그&amp;드롭</u> 할 수 있습니다.)</p>
                            
                        </div>
                        <div class="figure-attach" id="figure-attach">
                            <p></p>
                            <ul>
                                <li><a href="#" class="btn_select">사진 선택</a> </li>
                                <li><a href="#" class="btn_delete">사진 삭제</a></li>

                            </ul>
                        </div>
                    </div>
                    <!-- 동영상일때 -->
                    <div class="video-wrap" style="display:none;">
                        <div class="video">
                            <p><img src="//engine.ilark.co.kr/dev/figure/images/icon_movie.png" alt=""></p>
                            <p class="exp1">빙글빙글 돌아가는 회전의자와 스마트폰를 활용하여<br>얼굴의 앞모습 부터 뒷모습까지 나오는<br>동영상을 올려주세요.</p>
                            <p class="exp2">동영상을 올려주시면 피규어 제품의 퀄리티가 더 좋아집니다.<br>(10MB 이내)</p>
                        </div>
                        <div class="figure-attach" id="figure-attach2">
                            <p></p>
                            <ul>
                                    <li><a href="#" class="btn_select2">동영상 선택</a></li>
                                    <li><a href="#" class="btn_delete2">파일 삭제</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom" id = "bottom">
            <!-- 진행중이면 <li class="ing"> -->
            <!-- 완료되면 <li class="on"> -->
            <div class="figure_Progress" >
                    <div class="figure_ProgressBar">
                        <div class="figure_ProgressLabel">0%</div>
                    </div>
            </div>
            <div class="finish" style="position: relative; z-index: 2;">
                <!-- 사진선택전 -->
                <div class="temporarily">
                    <h3>피규어 제작시 필요한 5개의 사진을 빠짐없이 다 넣으시면 <br>‘사진업로드’ 버튼이 활성화 됩니다.</h3>
                    <p>동영상을 올려주시면 피규어 제품의 퀄리티가 더 좋아집니다. (선택)</p>
                    <a href="#" class="btn_temporarily">임시저장</a>
                </div>
                
                <!-- 사진선택후 -->
                <div class="upload" style="display:none;">
                    <h3>사진을 빠짐없이 다 넣으셨나요?<br>‘사진업로드’를 눌러주세요.</h3>
                    <p>동영상을 올려주시면 피규어 제품의 퀄리티가 더 좋아집니다. (선택)</p>
                    <a href="#" class="btn_upload">사진 업로드</a>
                </div>
                
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
    // 샘플포토 남녀 전환
    $('.sample-top ul li').each(function(idx)
    {
        $(this).click(function(){
                $('.sample-top ul li').removeClass('on');
                $(this).addClass('on');
                $('.sample-img').hide();
                $('.sample-img').eq(idx).show();
            });
        });
    
    });


    var option ={
        title: "피규어 (통합형)",
        config: {
            "storage" :
            {  
                "saveCartStartup" : "http://storage.wecard.co.kr/figure/saveCartStartup.ashx" ,
                "saveCartUpdate" : "http://storage.wecard.co.kr/figure/saveCartUpdate.ashx" ,
                "uploadcartimage" : "http://storage.wecard.co.kr/figure/uploadCartImage.ashx" ,
                "saveCartComplete" : "http://storage.wecard.co.kr/figure/saveCartComplete.ashx" ,
                "openCartFiles" : "http://storage.wecard.co.kr/figure/openCartFiles.ashx"
            },
            "product":
            {  
                "family_member": <?=$info[family_member]?>,
                "product_info" : ["<?=$info[displayproductname]?>", "<?=$info[displayprice]?>"]
            }

        },

        mode : "<?=$edit_mode?>",
        siteid : "<?=$info[siteid]?>",
        rsid : "<?=$info[storageid]?>"
    };

    var editor = new  miodio.figure.Editor(option);
        editor.run({
            fatal  : function(error){
                //alert(JSON.stringify(error))
                //error 내용 출력 
                //console.log(error);
                //alert("오류가 발생했습니다."+ error);
                //alert('<?=_("편집을 취소하셨습니다.")?>'+'\n'+'<?=_("오늘도 좋은 하루 보내세요.")?>'+'\n'+'<?=_("감사합니다.")?>'+' ^..^');
            },
            save  :  function(appResult){
                // save 처리만 한다. 임시 저장
                //exit_type: "cart"
                //storageId: "ST292201809073055Z8BL3"
                alert("편집정보가 임시저장되었습니다.");
                //console.log(appResult);
                window.parent.exec2("<?=$cart_mode?>", "<?=$info[storageid]?>", JSON.stringify(appResult));
            },
            cart  : function(appResult){
                // save 처리 후 장바구니로 이동한다. 임시저장
                
                //exit_type: "save"
                //storageId: "ST292201809073055Z8BL3"
                if("<?=$mode?>" == "edit_admin") { //관리자 페이지에서 오픈
                    //parent.closeLayer();
                    alert('<?=_("편집이 정상적으로 종료되었습니다.")?>');
                    //window.close();
                    window.parent.closeWebEditor();
                }
                else {
                    alert('<?=_("편집이 정상적으로 종료되었습니다.")?>'+'\n'+'<?=_("장바구니로 이동합니다.")?>');
                    //console.log("<?=$info[storageid]?>");
                    //console.log(appResult);
                    window.parent.exec2("<?=$cart_mode?>", "<?=$info[storageid]?>", JSON.stringify(appResult));
                    //console.log(appResult);
                }
            }

        });

    function xs_ret(ret){
        var mode = "<?=$mode?>";
    
        var err = ret.slice(0,1);
        var result = ret.slice(2);
    
        if (err == "1"){
            alert('<?=_("편집을 취소하셨습니다.")?>'+'\n'+'<?=_("오늘도 좋은 하루 보내세요.")?>'+'\n'+'<?=_("감사합니다.")?>'+' ^..^');
          
            if(mode == "edit_admin") { //관리자 페이지에서 오픈
                window.parent.closeWebEditor();
            }
            else {
                window.parent.location.reload();
            }
       }
    }
</script>
</body>
</html>



