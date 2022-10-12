<?

/*
* @date : 20190124
* @author : kdk
* @brief : 시안관리 기능 추가.
* @request : 태웅.
* @desc : 
* @todo : 
*/

include_once "../_pheader.php";
$m_modern = new M_modern();
$m_print = new M_print();

if (!$_GET[payno]) {
    msg(_("필수 정보가 넘어오지 못했습니다!"), "close");
}

### 시안요청정보.
$data = $m_modern->getDesignPayno($_GET[payno]);
//debug($data);

if ($data[ext_json_data]) {
    $ext_json = json_decode($data[ext_json_data],1);
    //debug($ext_json);
}

### 파일정보. md_print_upload_file
if ($data[storageid]) {
    $file_data = $m_print->getPrintUploadFile($data[storageid]);
    //debug($file_data);
    if($file_data[0]) $file1 = $file_data[0];
    if($file_data[1]) $file2 = $file_data[1];
    if($file_data[2]) $file3 = $file_data[2];
}

###확정시안 정보.
if ($data[design_fix]) {
    //debug($data[design_fix]);
    foreach ($file_data as $key => $val) {
        if ($val[id] == $data[design_fix]) {
            $design_fix = "시안". ($key+1);
        }
    }
}
//debug($design_fix);

###시안요청 댓글 조회.
$comment_data = $m_modern->getDesignComment($_GET[payno]);
//debug($comment_data);
?>

<style>
#ord_box {margin-left:5px;margin-right:5px}
</style>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("시안관리")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <div class="panel-body">

               <form name="fm" class="form-horizontal form-bordered" method="post" action="indb.php" onsubmit="return submitForm(this);" enctype="multipart/form-data">
                  <input type="hidden" name="mode" value="design_draft">
                  <input type="hidden" name="payno" value="<?=$data[payno]?>">
                  <input type="hidden" name="mid" value="<?=$data[mid]?>">
                  <input type="hidden" name="storageid" value="<?=$data[storageid]?>">

                  <div class="panel panel-inverse">
                     <div class="panel-heading">
                        <h4 class="panel-title"><?=_("시안요청정보")?></h4>
                     </div>

                     <div class="panel-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="150"><?=_("결제번호")?></th>
                              <td><b style="color:#28a5f9"><?=$data[payno]?></b></td>
                           </tr>

                            <?
                            if ($data[mid]) {
                                $info_member = $data[mid];
                            } else {
                                $info_member = _("비회원");
                            }
                            ?>

                           <tr>
                              <th><?=_("아이디")?></th>
                              <td><?=$info_member?></td>
                           </tr>
                           <tr>
                              <th><?=_("요청일자")?></th>
                              <td><?=$data[regist_date]?></td>
                           </tr>
                           <tr>
                              <th><?=_("요청상태")?></th>
                              <td><b style="color:#FF0000"><?=$r_step[$data[state]]?></b></td>
                           </tr>                           
                           <tr>
                              <th><?=_("주문상품")?></th>
                              <td><?=$data[est_order_option_desc]?></td>
                           </tr>
                           <tr>
                              <th><?=_("디자인샘플")?></th>
                              <td><?=_("템플릿셋")?>: <?=($ext_json[tsidx])?"$ext_json[tsidx]":""?>,<?=_("템플릿")?>: <?=($ext_json[tidx])?"$ext_json[tidx]":""?>,<?=_("템플릿명")?>: <?=($ext_json[tname])?"$ext_json[tname]":""?>
                                  <br><br><br>
                                  <?if ($ext_json[turl]) {?>
                                      <img src="<?=$ext_json[turl]?>" onclick='window.open("<?=$ext_json[turl]?>");'  alt="<?=($ext_json[tname])?"$ext_json[tname]":""?>" style="cursor: pointer;">                                      
                                  <?}?>
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("요청사항")?></th>
                              <td><?=$ext_json[msg]?></td>
                           </tr>                           
                           <tr>
                              <th><?=_("첨부파일")?></th>
                              <td>
                                  <?
                                  if($data[est_file_upload_json]) { 
                                     $files = json_decode($data[est_file_upload_json],1);
                                        if($files) {
                                            foreach ($files as $key => $val) {
                                  ?>
                                    <div style="padding-top: 10px;">
                                        <span><a target="_blank" href="<?=$val[server_path]?>"><u><?=$val[file_name]?></u></a> (<?=round($val[file_size]/1024,2)?> KByte)</span>
                                        <a href="../order/download.php?src=<?=$val[server_path]?>" class="btn-primary btn-xs m-r-5"><?=_("다운로드")?></a>
                                        <!--<?=$val[server_path]?>-->                                        
                                    </div>                                  
                                  <?           
                                            }
                                        }
                                  } 
                                  ?>                                  
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("확정시안")?></th>
                              <td><b style="color:#28a5f9"><?=$design_fix?></b> (<?=$data[design_fix]?>)</td>
                           </tr>                           
                        </table>
                     </div>
                  </div>
   
                  <div class="panel panel-inverse">
                     <div class="panel-heading">
                        <h4 class="panel-title"><?=_("시안관리정보")?></h4>
                     </div>
   
                     <?
                     if ($pay[mid]) {
                         $info_member = $pay[mid];
                     } else {
                         $info_member = _("비회원");
                     }
                     ?>
                     
                     <div class="panel-body">
                        <table class="table table-bordered form-inline">
                           <tr>
                              <th width="150"><?=_("관리자 메모")?></th>
                              <td><textarea class="form-control" name="response_comment" cols="100" rows="5"><?=$data[response_comment]?></textarea></td>
                           </tr>
                                                      
                           <tr>
                              <th><?=_("시안1")?></th>
                              <td>
                                  <? if($file1) { ?>
                                    <span><?=_("등록일")?>: <?=$file1[regist_date]?></span>  
                                    <div>
                                        <span><a target="_blank" href="<?=$file1[server_path]?>"><u><?=$file1[upload_file_name]?></u></a> (<?=round($file1[file_size]/1024,2)?> KByte)</span>
                                        <!--<a href="../order/download.php?src=<?=$file1[server_path]?>" class="btn-primary btn-xs m-r-5"><?=_("다운로드")?></a>-->
                                        <a href="javascript:void(0);" onclick="fileDelete(this,'<?=$file1[id]?>','<?=$file1[storageid]?>','<?=$file1[upload_file_name]?>');" class="btn-danger btn-xs m-r-5"><?=_("삭제")?></a>
                                        <!--<?=$file1[server_path]?>-->
                                    </div>
                                  <? } else { ?>
                                      <input type="file" class="form-control" name="file1" />
                                  <? } ?>                                  
                              </td>
                           </tr>

                           <tr>
                              <th><?=_("시안2")?></th>
                              <td>
                                  <? if($file2) { ?>
                                    <span><?=_("등록일")?>: <?=$file2[regist_date]?></span>
                                    <div>
                                        <span><a target="_blank" href="<?=$file2[server_path]?>"><u><?=$file2[upload_file_name]?></u></a> (<?=round($file2[file_size]/1024,2)?> KByte)</span>
                                        <!--<a href="../order/download.php?src=<?=$file2[server_path]?>" class="btn-primary btn-xs m-r-5"><?=_("다운로드")?></a>-->
                                        <a href="javascript:void(0);" onclick="fileDelete(this,'<?=$file2[id]?>','<?=$file2[storageid]?>','<?=$file2[upload_file_name]?>');" class="btn-danger btn-xs m-r-5"><?=_("삭제")?></a>
                                        <!--<?=$file2[server_path]?>-->
                                    </div>
                                  <? } else { ?>
                                      <input type="file" class="form-control" name="file2" />
                                  <? } ?>                                  
                              </td>
                           </tr>
                           
                           <tr>
                              <th><?=_("시안3")?></th>
                              <td>
                                  <? if($file3) { ?>
                                    <span><?=_("등록일")?>: <?=$file3[regist_date]?></span>
                                    <div>
                                        <span><a target="_blank" href="<?=$file3[server_path]?>"><u><?=$file3[upload_file_name]?></u></a> (<?=round($file3[file_size]/1024,2)?> KByte)</span>
                                        <!--<a href="../order/download.php?src=<?=$file3[server_path]?>" class="btn-primary btn-xs m-r-5"><?=_("다운로드")?></a>-->
                                        <a href="javascript:void(0);" onclick="fileDelete(this,'<?=$file3[id]?>','<?=$file3[storageid]?>','<?=$file3[upload_file_name]?>');" class="btn-danger btn-xs m-r-5"><?=_("삭제")?></a>
                                        <!--<?=$file1[server_path]?>-->
                                    </div>
                                  <? } else { ?>
                                      <input type="file" class="form-control" name="file3" />
                                  <? } ?>                                  
                              </td>
                           </tr>                           
                        </table>
                        <div>
                            <button type="submit" class="btn btn-sm btn-danger"><?=_("수정")?></button>
                        </div>                        
                     </div>
                  </div>

               </form>

               <form name="frm" class="form-horizontal form-bordered" method="post" action="indb.php" onsubmit="return submitFormComment(this);">
                  <input type="hidden" name="mode" value="design_addComment">
                  <input type="hidden" name="payno" value="<?=$data[payno]?>">
                  <input type="hidden" name="mid" value="<?=$sess_admin[mid]?>">
                  
                 <div class="panel panel-inverse">
                     <div class="panel-heading">
                        <h4 class="panel-title"><?=_("댓글정보")?></h4>
                     </div>
                     
                     <div class="panel-body">
                        <table class="table table-bordered form-inline">
                           <?
                           foreach ($comment_data as $key => $val) {
                           ?>
                           <tr>
                              <th width="150">
                                  <? if($val[admin]){ ?>  
                                  <b style="color:#28a5f9"><?=$val[name]?></b>
                                  <?} else {?>                                  
                                  <?=$val[name]?>
                                  <? } ?>
                              </th> 
                              <td colspan="2">
                                <span><?=_("등록일")?>: <?=$val[regdt]?></span>  
                                <div style="margin-top: 10px;"><?=$val[comment]?></div>
                              </td>
                           </tr>
                           <?
                           }
                           ?>
                           <tr>
                              <td width="150">
                                <input type="text" name="name" value="<?=$sess_admin[name]?>" style="width:100px;">                
                              </td>
                              <td><textarea class="form-control" name="comment" cols="80" rows="3"></textarea></td>
                              <td><button type="submit" class="btn btn-sm btn-danger"><?=_("등록")?></button></td>
                           </tr>
                        </table>
                        <div>
                            <button type="submit" class="btn btn-sm btn-danger"><?=_("등록")?></button>
                        </div>                        
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
        if (confirm('<?=_("시안관리 정보를 수정하시겠습니까? 상태도 시안 검수요청으로 변경됩니다.")?>')) {

            formObj.response_comment.value = Base64.encode(formObj.response_comment.value);
            
            return true;
        }
        else {
            return false;
        }
    } catch(e) {return false;}
}

//댓글 등록.
function submitFormComment(formObj) {
    try {
        if (confirm('<?=_("댓글을 등록하시겠습니까?")?>')) {

            formObj.comment.value = Base64.encode(formObj.comment.value);
            
            return true;
        }
        else {
            return false;
        }
    } catch(e) {return false;}
}

//file삭제
function fileDelete(obj,id,storageKey,fileName) {
    if(confirm('<?=_("정말 삭제하시겠습니까?")?>')) {
        //location.href = "indb.php?mode=inspection_file_delete&no="+id+"&storage_code="+sid+"&file_name="+fname;
        //ajax 전송
        $j.ajax({
            type : "POST",
            url : "/a/order/indb.php",
            data : "mode=inspection_file_delete&id="+ id +"&storage_code="+ storageKey +"&file_name="+fileName,
            success:function(data){
                //console.log("data : "+data);
                if(data == "FAIL") {
                    alert("파일 삭제에 실패하였습니다.");
                }
                else {
                    //$j(obj).closest("tr").remove();
                    alert("파일 삭제가 완료되었습니다.");
                    location.reload();
                }
            },   
            error:function(e){
                alert(e.responseText);
            }
        });

    }
}
</script>

<? include_once "../_pfooter.php"; ?>