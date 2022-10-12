   
   <div class="row">
      <!-- begin col-3 -->
     
      <div class="col-md-2 col-sm-6">
         <div class="widget widget-state bg-red">
            <div class="state-icon">
               <i class="fa fa-desktop"></i>
            </div>
            <div class="state-info">
               <h4><?=_("통합리스트")?></h4>
               <p>
                  <?=number_format($count[0])?><?=_("건")?>
               </p>
            </div>
            <div class="state-link">
               <a href="order_list.php"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
     
      <!-- end col-3 -->

      <!-- begin col-2 -->
      <div class="col-md-2 col-sm-6">
         <div class="widget widget-state bg-primary">
            <div class="state-icon">
               <i class="fa fa-users"></i>
            </div>
            <div class="state-info">
               <h4><?=_("무통장-미입금")?></h4>
               <p><?=number_format($count[1])?><?=_("건")?></p>
            </div>
            <div class="state-link">
               <a href="order_list.php?itemstep=1"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-2 -->
     
      <!-- begin col-2 -->
      <div class="col-md-2 col-sm-6">
         <div class="widget widget-state bg-green-lighter">
            <div class="state-icon">
               <i class="fa fa-gavel"></i>
            </div>
            <div class="state-info">
               <h4><?=_("신용거래-승인요청")?></h4>
               <p><?=number_format($count[91])?><?=_("건")?></p>
            </div>
            <div class="state-link">
               <a href="order_list.php?itemstep=91"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-2 -->
     
      <!-- begin col-2 -->
      <div class="col-md-2 col-sm-6">
         <div class="widget widget-state bg-aqua">
            <div class="state-icon">
               <i class="fa fa-truck"></i>
            </div>
            <div class="state-info">
               <h4><?=_("제작중")?></h4>
               <p><?=number_format($count[3])?><?=_("건")?></p>
            </div>
            <div class="state-link">
               <a href="order_list.php?itemstep=3"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-2 -->
     
      <!-- begin col-3 -->
      <div class="col-md-2 col-sm-6">
         <div class="widget widget-state bg-black">
            <div class="state-icon">
               <i class="fa fa-chain-broken"></i>
            </div>
            <div class="state-info">
               <h4><?=_("발송대기")?></h4>
               <p><?=number_format($count[4])?><?=_("건")?></p>
            </div>
            <div class="state-link">
               <a href="order_list.php?itemstep=4"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-3 -->
      
      <!-- begin col-3 -->
      <div class="col-md-2 col-sm-6">
         <div class="widget widget-state bg-blue">
            <div class="state-icon">
               <i class="fa fa-wrench"></i>
            </div>
            <div class="state-info">
               <h4><?=_("발송완료")?></h4>
               <p><?=number_format($count[5])?><?=_("건")?></p>
            </div>
            <div class="state-link">
               <a href="order_list.php?itemstep=5"><?=_("내역을 보려면 여기를 클릭하세요")?> <i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
         </div>
      </div>
      <!-- end col-3 -->
   </div>   