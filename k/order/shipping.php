<?
include "../_header.php";
include "../_left_menu.php";

### 배송업체정보추출
$r_shipcomp = get_shipcomp();
?>

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("운송장번호업로드")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("운송장번호업로드")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("운송장번호업로드")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.shipping.php" enctype="multipart/form-data" target="ifrmProc">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("배송업체")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="shipcomp" class="form-control">
                        <option value="">배송업체선택
                        <? foreach ($r_shipcomp as $k=>$v){ ?>
                        <option value="<?=$k?>"><?=$v[compnm]?>
                        <? } ?>
                        </select>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("엑셀파일")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="file" name="file" class="form-control"/>
                        <a href="../_sample/shipcode.csv" class="btn btn-primary btn-xs m-r-5">예제 다운</a>
                        <div class="desc"><span class="warning">[주의]</span> csv파일을 업로드 하셔야 합니다.</div>
                     </div>
                  </div>

                  <div class="form-group">
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("등록")?></button>
                    </div>
                  </div>
                  
                  <div class="form-group">
                     <div class="col-md-12 form-inline">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("번호")?></th>
                                 <th><?=_("결제번호")?></th>
                                 <th><?=_("송장번호")?></th>
                                 <th><?=_("메세지")?></th>
                              </tr>
                           </thead>
                        </table>

                        <iframe name="ifrmProc" width="99.8%" height="400" frameborder="0" style="border:1px solid #cccccc"></iframe>
                     </div>
                  </div>

               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>