<?
  //센터별, 몰별, 스킨별, 페이지별 템플릿 구조를 변경하고 싶을때..       20150812    chunter
  
  
  $_r_skin_config = array(
    
    "pretty" => array(
      "*|*" => array(
        "/member/register.php" => array(
          'header' => 'layout/layout_info.htm?header',
          'footer' => 'layout/layout.htm?footer'
        ),
        "/module/preview.php" => array(
          'tpl' => 'module/preview_slide.htm',          
        )
      ),
          
      
      "sajinory|service,bluepoddemo|chunter" => array(
        "/member/register.php,/board/list.php,/board/view.php" => array(
          'header' => 'layout_dnp/layout_info.htm?header',
          'footer' => 'layout_dnp/layout_info.htm?footer',
          'top' => 'layout_dnp/top.info.htm',
          'left' => 'layout_dnp/left.info04.htm',
          'bottom' => 'layout_dnp/bottom.default.htm',
          'right_banner' => 'layout_dnp/left.info04.banner.htm'
        ),
          
        "/member/register.php" => array(          
          'fm_member' => 'service_dnp/fm.member.htm',
        )
        
      )        
        
    ),
    
    
    "kids" => array(
      "*|*" => array(
        "/member/register.php" => array(
          'header' => 'layout/layout.htm?header',
          'footer' => 'layout/layout.htm?footer'
        )
      ),
    ),
    
		
		"pretty_home" => array(
      "*|*" => array(
        "/member/register.php" => array(
          'top_sub' => 'layout/top.sub.membership.htm'
        )
      ),
    ),
    
    
    "panda" => array(
      "*|*" => array(
        "/goods/view.php" => array(        
          'slide_exe_info' => 'module/slide_goods_editor_exe_info.htm'
        ),
        
				"/member/payment.php" => array(        
          'tpl' => 'member/payment/payment_01.htm'
        )
      ),
    )
  );
  
  
    

//if($cfg[skin] == "kids") $tpl->define('header', 'layout/layout.htm?header');
//else if($cfg[skin] == "pretty") $tpl->define('header', 'layout/layout_info.htm?header');

  
  
  
  
  //설정중 모든 몰, 모든 페이지 적용은 "*" 로 구분한다.  
  function setSkinTemplateDefine()
  {
    global $_r_skin_config, $tpl, $cfg, $cfg_center;
    $bProcStep1 = false;
    foreach ($_r_skin_config as $skinKey => $skinValue) 
    {
      $skin_arr = explode(',', $skinKey);
      
      //해당 스킨에 대해서만 처리. bProc 가 true 인경우
      foreach ($skin_arr as $key => $value) 
      {
        if ($value == "*" || $cfg[skin] == $value)
        {
          $bProcStep1 = true;
          //break;
        }
      
        if ($bProcStep1)
        {
          //debug($skinValue);
          $bProcStep2 = false;     //다시 초기화
          foreach ($skinValue as $mallKey => $mallValue) 
          {
            $mall_arr = explode(',', $mallKey);
            
            //설정된 몰에 대해서만.. 센터아이디|몰아이디 형태로 구성된. 센터아이디|* -> 센터의 모든몰    *|*   -> 모든센터|모든몰
            foreach ($mall_arr as $key => $value) 
            {
              if ($value == "*|*" || $value == $cfg_center[center_cid]."|*" || $value == $cfg_center[center_cid]."|".$cid)
              {
                $bProcStep2 = true;
                //break;
              }
                    
            
              if ($bProcStep2)
              {
                //debug($mallValue);
                $bProcStep3 = false;     //다시 초기화
                foreach ($mallValue as $pageKey => $pageValue) 
                {
                  $page_arr = explode(',', $pageKey);
                  
                  //설정된 페이지에 대해서만 처리.   member/*  -> member 폴더의 모든 파일
                  foreach ($page_arr as $key => $value) 
                  {
                    if ($value == CURRENT_FILE_PATH."/*" || $value == CURRENT_FILE_PATH."/".CURRENT_FILE_NAME)
                    {
                      $bProcStep3 = true;
                      //break;
                    }
                  
                  
                    if ($bProcStep3)
                    {
                      foreach ($pageValue as $configKey => $configValue) {
                        $tpl->define($configKey, $configValue);
                      }        
                    }
                  }
                  $bProcStep3 = false;             
                  
                }
              }
            }
            $bProcStep2 = false;          
          }        
        }      
      }
      $bProcStep1 = false;  
    }
  }

  //debug($tpl);
    

?>