<div class="row">
   <div class="col-md-12">
      <div class="panel panel-<?=$pannel_color?>">
         <div class="panel-heading">
            <div class="panel-heading-btn">
               <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
               <a href="javascript:;"  class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
               <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
            </div>
            <h4 class="panel-title"><?=$pannel_title?></h4>
         </div>

         <? setAdminIncudeFile("order_list_search_A"); ?>

         <!-- 기존 유치원 소스 붙이기 -->
         <div class="panel panel-inverse">
            <div class="panel-body panel-form">
               <form name="fm" class="form-horizontal form-bordered" method="post" action="indb.php">
                  <input type="hidden" name="mode" value="">
                  <div class="panel-body">
                     <div class="table-responsive">
                        <table id="data-table" class="table table-bordered">
                           <thead>
                              <tr class="info">
                                 <? if($_GET[itemstep] && $_GET[itemstep] != 5) { ?>
                                    <th><a href="javascript:chkBox('chk[]','rev')"><?=_("전체 선택")?></a></th>
                                 <? } ?>
                                 <th><?=_("결제번호")?></th>
                                 <th><?=_("주문일")?></th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
               </form>

               <? if($_GET[itemstep] != '' && $_GET[itemstep] != '5') { ?>
                  <div class="col-md-12 col-sm-6">
                     <div>
                         <table style="width:100%" height="60">
                            <tr>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="order_proc();"><?=_("주문 넘기기")?></button></td>
                            </tr>
                         </table>
                     </div>
                  </div>
               <? } ?>
            </div>
         </div>
      </div>
   </div>
</div>