<iframe name="hidden_frm" style="display:none;width:100%;height:300px;"></iframe>

<?
//로그인 시 메뉴 설정을 쿠키에 저장할 때 오류발생으로 인해 임시조치함.(lib.common.php -> _admin_login)
//관리자 설정 메뉴가 있다면..
//관리자 설정 메뉴 사용여부 설정에 따라 보이기 숨기기.
$priv = array();
$data = $db->fetch("select priv from exm_admin where cid='$cid' and mid='$sess_admin[mid]'");
//debug($data);
if ($data[priv]) { 
    $r_priv = unserialize($data[priv]); //menu_priv
    //debug($r_priv);

	foreach ($r_priv as $key => $val) {
		foreach ($val as $ke=>$va) {
			foreach ($va as $k=>$v) {			
				$priv[$k] = $v;
			}
		}	
	}
	//debug($priv);
}	
?>

<script src="../assets/plugins/bootstrap-3.1.1/js/bootstrap.min.js"></script>
<!-- ================== BEGIN PAGE LEVEL JS ================== -->  
<script src="../assets/js/apps.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   $(document).ready(function() {
      App.init();

		//관리자 설정 메뉴 사용여부 설정에 따라 보이기 숨기기.
		<?foreach ($priv as $key => $val) {?>			
			var span = $("span[name='<?=$key?>']");
			//console.log(span);
	   		var li = span.parent().parent();
	   		//console.log(li);

			var str = "<?=$val?>";
	   		//console.log(str);
			if (str == "N"){
				li.hide();
			}
		<?}?>
   });
</script>
  
<? if (CURRENT_FILE_PATH ."/". CURRENT_FILE_NAME == "/a/main/index.php") { ?>  
   <script src="../assets/plugins/gritter/js/jquery.gritter.js"></script>
   <link href="../assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" /> 
 
   <script>
      var handleGritterNotificationPoint = function() {
         $(window).load(function() {

         if(getCookie('gritter_point') != 'Y') {
            setTimeout(function() {
               $.gritter.add({
                  title: '<?=_("BluePod에서 알려드립니다!")?>',
                  text: '<?=_("BluePod 포인트가 부족합니다.")?>' + '<BR><a href=/a/order/point_list.php>' + '<?=_("포인트 관리")?>' + '</a> ' + '<?=_("메뉴에서 포인트를 충전해 주세요.")?>' + '<BR>' + '<?=_("포인트가 부족하면 주문처리가 되지 않습니다.")?>',
                  //text: '웹하드™ 비밀번호가 설정되지 않았습니다.<BR><a href=/mng/config/config.f.php>기본설정</a> 메뉴에서 입력되지 않은 항목들을 등록해주세요.<BR><input type=checkbox id=gritter_check>오늘보지않기',
                  image: '../assets/img/openbay_icon13.png',
                  sticky: true,
                  position : 'top-left',
                  time: '',
                  before_close : function(e, manual_close) {
                     var checked = $("input:checkbox[id='gritter_check']").is(":checked"); 
                     if (checked) {
                        //쿠키 저장한다.
                        var date = new Date();
                        date.setTime(date.getTime() + (1 * 24 * 60 * 60 * 1000));
                        setCookie('gritter_point', 'Y', date, '/');
                     } else {
                     //alert(getCookie('gritter_webhard'));
                     }
                  },
                  //class_name: 'gritter-item'
               });
            }, 500);
         }
      });
   };

   var handleGritterNotificationSTDateNotice = function() {
      $(window).load(function() {
         setTimeout(function() {
            $.gritter.add({
               title: '<?=_("BluePod에서 알려드립니다!")?>',
               text: '<?=_("BluePod 웹하드 사용기간이 만료되었습니다.")?>' + '<br><a href=/a/order/storage_list.php>' + '<?=_("웹하드 관리")?>' + '</a>' + '<?=_("메뉴에서 웹하드 기간을 연장해 주세요.")?>' + '<BR>' + '<?=_("웹하드 기간이 만료되면 유치원에서 파일 업로드 및 상품편집이 되지 않습니다.")?>',
               image: '../assets/img/information.png',
               sticky: true,
               position : 'top-left',
               //time: '4000',
               //class_name: 'gritter-item'
            });
         }, 500);
      });
   };
  
   var handleGritterNotificationSTSizeNotice = function() {
      $(window).load(function() {
         setTimeout(function() {
            $.gritter.add({
               title: '<?=_("BluePod에서 알려드립니다!")?>',
               text: '<?=_("BluePod 웹하드 사용량이 110% 초과 되었습니다.")?>' + '<br><a href=/a/order/storage_list.php>' + '<?=_("웹하드 관리")?>' + '</a>' + '<?=_("메뉴에서 웹하드 용량을 추가해 주세요.")?>' + '<BR>' + '<?=_("웹하드 용량이 부족하면 유치원에서 파일 업로드 및 상품편집이 되지 않습니다.")?>',
               image: '../assets/img/information.png',
               sticky: true,
               position : 'top-left',

               //sticky: false,
               //time: '4000',
               //class_name: 'gritter-item'
            });
         }, 500);
      });
   };
   <?
   if ($bIlarkNoticeFlag) {
      $ilarkNotiIndex = 1;
      $m_board = new M_board();
      $b_data = $m_board->getBoardDataListNLimit($cfg_center[center_cid], "notice", 3);
      foreach ($b_data as $key => $value) {
         $b_title = $value[subject];
         $b_content = $value[content];
   ?>
  
   var handleGritterNotificationCenterNotice_<?=$ilarkNotiIndex?> = function() {
      $(window).load(function() {
         setTimeout(function() {
            $.gritter.add({
               title: '<?=$b_title?>',
               text: '<?=$b_content?>',
               image: '../assets/img/information.png',
               sticky: true,
               position : 'top-left',

               //sticky: false,
               //time: '4000',
               //class_name: 'gritter-item'
            });
         }, 500);
      });
   };
   <?
      echo "handleGritterNotificationCenterNotice_$ilarkNotiIndex();";
      $ilarkNotiIndex++;
      }
   }	
   ?>

   <? if ($cfg[pretty_point_notice] == "Y") echo "handleGritterNotificationPoint();"; ?>  
   <? if ($cfg[storage_date_notice] == "Y") echo "handleGritterNotificationSTDateNotice();"; ?>
   <? if ($cfg[storage_size_notice] == "Y") echo "handleGritterNotificationSTSizeNotice();"; ?>
   
   </script>

<? } ?>

</body>
</html>