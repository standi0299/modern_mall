
   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;"  class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("포인트 내역")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post" action="point_list.php">
                  <input type="hidden" name="date_buton_type" id="date_buton_type">

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("처리 일자")?></label>
                     <div class="col-md-3">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST[end]?>" />
                        </div>
                     </div>
                     <div class="col-md-4">
                        <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');"><?=_("오늘")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');"><?=_("3일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');"><?=_("1주일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');"><?=_("1달")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start', 'all'); regdtOnlyOne('today','end');"><?=_("전체")?>
                        </button>
                     </div>
                     <div class="col-md-3 form-inline">
                           <?=_("구분")?>&nbsp;&nbsp;
                           <select name="select" class="form-control">
                           <option value=""><?=_("전체보기")?></option>
                           <? foreach($r_pretty_point_account_flag as $k => $v) { ?>
                              <option value="<?=$k?>" <?=$selected[account][$k]?>><?=$v?></option>
                           <? } ?>
                        </select>
                     </div>
                     <div class="col-md-1">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
                     </div>
                  </div>
               </form>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <table id="data-table" class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th><?=_("일자(시간)")?></th>
                           <th><?=_("구분")?></th>
                           <th><?=_("충전금액")?></th>
                           <th><?=_("사용금액")?></th>
                           <th><?=_("잔액")?></th>
                           <th><?=_("사용내역")?></th>
                           <th><?=_("결제번호")?></th>
                        </tr>
                     </thead>
 
                     <tbody>
                        <?if($list) { ?>
                           <? foreach ($list as $key => $value) { ?>
                              <tr class="<?=$tr_class?>">
                                 <td><?=substr($value[regist_date],0,10)?></td>
                                 <td><?=$r_pretty_point_account_flag[$value[account_flag]]?></td>
                                 <td>
                                 <?
                                    if(!in_array($value[account_flag], $r_pretty_point_list_chk_flag))
                                       echo number_format($value[account_point]);
                                    else
                                       echo "-";
                                 ?>
                                 </td>
                                 <td>
                                 <?
                                    if(in_array($value[account_flag], $r_pretty_point_list_chk_flag))
                                       echo number_format($value[account_point]);
                                    else
                                       echo "-";
                                 ?>
                                 </td>
                                 <td><?=number_format($value[mall_point])?></td>
                                 <!-- 유치원 -->
                                 <td><?=$value[memo]?><?=$value[m_name]?></td>
                                 <!-- 결제번호 -->
                                 <td>
                                 <? if ($value[account_flag] < 20) {?>
                                    <a href="javascript:popup('popup_order_detail.php?payno=<?=$value[payno]?>&mid=<?=$value[mid]?>',1010,800);"><?=$value[payno]?></a>
                                 <? } else { ?>
                                    <a href="javascript:popup('popup_charge_detail.php?tno=<?=$value[tno]?>&mid=<?=$value[mid]?>',300,210);"><?=$value[tno]?></a>
                                 <? } ?>
                              </td>
                           </tr>
                        <? } } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
