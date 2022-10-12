<?
	include_once dirname(__FILE__)."/../lib/library.php";
	include_once dirname(__FILE__)."/lib_const_print.php";     //옵션 항목 설정 및 가격설정
	include_once dirname(__FILE__)."/lib_const_print_opset.php";     //옵셋 관련 항목 가격설정
	//include_once dirname(__FILE__)."/lib_const_paper.php";     //지류  항목 설정 및 가격설정
	
	include_once dirname(__FILE__)."/lib_calcu_digital.php";     //디지털 가격계산
	include_once dirname(__FILE__)."/lib_calcu_opset.php";     //옵셋 가격계산
	include_once dirname(__FILE__)."/lib_calcu_normal.php";     //일반인쇄  가격계산
	include_once dirname(__FILE__)."/lib_calcu_sticker.php";     //스티커 낱장  가격계산
	
	include_once dirname(__FILE__)."/lib_calcu_pr.php";     //현수막 실사출력  가격계산
	
	include_once dirname(__FILE__)."/lib_util_print.php";
	//include_once dirname(__FILE__)."/lib_util_print_admin.php";			//사용하지 않으
	
	$param_goods_no = $_GET['goodsno'];
	if (!$param_goods_no) $param_goods_no = $_POST['goodsno'];
	
	if ($param_goods_no && file_exists("../data/print/goods_items/".$param_goods_no.".php"))
	{
		include_once "../data/print/goods_items/".$param_goods_no.".php";
		$print_goods_item[$param_goods_no] = json_decode($goods_item, 1);
        //debug($print_goods_item[$param_goods_no]);
		//debug(json_decode($goods_item, 1));
	}
    //debug($print_goods_item[$param_goods_no]);
   
    //상품 정보 스킨 매핑정보 (책자,전단,명함,스티커,포스터등등)
    /*$r_goods_view_mapping = array(
        '4717' => array('type' => "CARD", 'html' => "print/inter_poster.htm"), // 4717 인터프로_전단 테스트용 
        '4718' => array('type' => "CARD", 'html' => "print/inter_namecard.htm"), // 4718 인터프로_명함 테스트용
        '4719' => array('type' => "BOOK", 'html' => "print/inter_book.htm"), // 4719 인터프로_책자견적(디지털) 테스트용
        '1200' => array('type' => "CARD", 'html' => "print/inter_sticker.htm"), // 개발용
        '1201' => array('type' => "CARD", 'html' => "print/inter_namecard.htm"), // 개발용
        
        //mdev.bluepod.kr test.
        '4789' => array('type' => "CARD", 'html' => "print/inter_poster.htm"), // 4789 인터프로_전단 테스트용 
        '4790' => array('type' => "CARD", 'html' => "print/inter_namecard.htm"), // 4790 인터프로_명함 테스트용
        '4791' => array('type' => "BOOK", 'html' => "print/inter_book.htm"), // 4791 인터프로_책자견적(디지털) 테스트용
    );*/		
		
	//일반상품의 경우 명함인지 스티커인지 구분한다.
	if ($param_goods_no == "4718")
		$data[product_type] = "namecard";

    //debug($r_ipro_paper);
    
	//가격계산이 필요한 경우만 가격설정 파일을 불러온다.
	$calcu_mode = "Y";
	if ($calcu_mode == "Y")
	{		
		//일반상품 가격 불러오기	
		if ($_POST[print_goods_type] == "DG01" || $_POST[print_goods_type] == "DG02")
		{
			if (file_exists("../data/print/goods_items/$cid"."_normal_".$param_goods_no.".php"))
			{
				include_once "../data/print/goods_items/$cid"."_normal_".$param_goods_no.".php";
				$r_iro_normal_product = json_decode(${"r_ipro_normal_".$param_goods_no}, 1);
				//debug($r_iro_normal_product);
			}
		}
		
		//옵션별 가격설정 파일 읽어오기
		if ($_POST[print_goods_type] == "DG03" || $_POST[print_goods_type] == "DG04"|| $_POST[print_goods_type] == "DG05"|| $_POST[print_goods_type] == "DG06")
		{
			$r_digital_print_include_file = array('print','print_dc','print_book_inside', 'print_book_inside_dc', 'gloss', 'punch', 'oshi', 
				'missing', 'round', 'domoo','domoo_sticker','domoo_sticker_other', 'domoo_sticker_square', 'barcode', 'number', 'stand', 'dangle', 'tape', 'address', 'sc', 'scb', 'wing', 'bind', 'instant');
			
			foreach ($r_digital_print_include_file as $value) 
			{
				$include_file_name = $cid."_".$value."_digital.php";
				if (file_exists("../data/print/goods_items/$include_file_name"))
				{
					include_once "../data/print/goods_items/$include_file_name";
					${"r_ipro_".$value."_digital"} = json_decode(${"r_ipro_".$value."_digital"}, 1);	                
					//if ($include_file_name == $cid."_print_digital.php")
	        //	debug(${"r_ipro_print_".$value."_digital"});
				}
			}
		}
        
        //디지털 윤전 추가 / 20181210 / kdk
		if ($_POST[print_goods_type] == "DG07" || $_POST[print_goods_type] == "DG08")
        {
            $r_digital_print_include_file = array('print_rotary','print_book_inside_rotary', 'gloss', 'punch', 'oshi', 
                'missing', 'round', 'domoo','domoo_sticker','domoo_sticker_other', 'domoo_sticker_square', 'barcode', 'number', 'stand', 'dangle', 'tape', 'address', 'sc', 'scb', 'wing', 'bind', 'instant');

            foreach ($r_digital_print_include_file as $value) 
            {
                $include_file_name = $cid."_".$value."_digital.php";
                if (file_exists("../data/print/goods_items/$include_file_name"))
                {
                    //debug($include_file_name);
                    include_once "../data/print/goods_items/$include_file_name";

                    ${"r_ipro_".$value."_digital"} = json_decode(${"r_ipro_".$value."_digital"}, 1);
                    
                    //디지털 윤전 추가 / 20181210 / kdk
                    if ($value == "print_rotary") {
                        ${"r_ipro_print_digital"} = ${"r_ipro_".$value."_digital"};
                    }
                    else if ($value == "print_book_inside_rotary") {
                        ${"r_ipro_print_book_inside_digital"} = ${"r_ipro_".$value."_digital"};
                    }
                                   
                    //if ($include_file_name == $cid."_print_digital.php")
                        //debug(${"r_ipro_print_".$value."_digital"});
                }
            }
        }
				
		//옵셋 상품인경우 
		if ($_POST[print_goods_type] == "OS01" || $_POST[print_goods_type] == "OS02")
		{
			$r_opset_print_include_file = array('print','gloss', 'punch', 'oshi','domoo','domoo_mm2','press','press_mm2','foil','foil_mm2','uvc','uvc_mm2',
				'missing', 'round',  'barcode', 'sc', 'scb', 'bind','bind_DB1', 'ctp', 'holding');
			
			foreach ($r_opset_print_include_file as $value) 
			{
				$include_file_name = $cid."_".$value."_opset.php";
				if (file_exists("../data/print/goods_items/$include_file_name"))
				{
					include_once "../data/print/goods_items/$include_file_name";
	       	//$r_ipro_print_dangle_opset
					${"r_ipro_".$value."_opset"} = json_decode(${"r_ipro_".$value."_opset"}, 1);
					//debug(${"r_ipro_print_".$value."_opset"});
				}	
			}
			
			//제본 관련한 설정파일 불러오기
			$r_opset_print_bind_include_file = array('bind_BD1_default', 'bind_BD1_page_gram', 'bind_BD3_default', 'bind_BD3_page_gram');
			foreach ($r_opset_print_bind_include_file as $value) 
			{
				$include_file_name = $cid."_".$value.".php";
				if (file_exists("../data/print/goods_items/$include_file_name"))
				{
					include_once "../data/print/goods_items/$include_file_name";
	       	//$r_ipro_bind_BD1_default
					${"r_ipro_".$value} = json_decode(${"r_ipro_".$value}, 1);
				}	
			}			
		}

        //현수막 실사출력 상품 가격 불러오기  
        if ($_POST[print_goods_type] == "PR01" || $_POST[print_goods_type] == "PR02")
        {
            // 현수막 실사출력 / 20190325 / kdk
            if (file_exists("../data/print/goods_items/$cid"."_paper_pr.php")) {
                include_once "../data/print/goods_items/$cid"."_paper_pr.php";
                $r_ipro_paper_pr = json_decode($r_ipro_paper_pr, 1);
                //debug($r_ipro_paper_pr);
            }        
    
            if (file_exists("../data/print/goods_items/$cid"."_paper_pr_dc.php")) {
                include_once "../data/print/goods_items/$cid"."_paper_pr_dc.php";
                $r_ipro_paper_pr_dc = json_decode($r_ipro_paper_pr_dc, 1);
                //debug($r_ipro_paper_pr_dc);
            }
            
            //현수막 실사출력 용지비,인쇄비,코팅비 1m2당 단가를 1mm당 단가
            if (file_exists("../data/print/goods_items/". $cid ."_paper_pr_price_1mm.php")) {
                include_once "../data/print/goods_items/". $cid ."_paper_pr_price_1mm.php";
                $r_ipro_paper_pr_price_1mm = json_decode($r_ipro_paper_pr_price_1mm, 1);
                //debug($r_ipro_paper_pr_price_1mm);
            }
                    
            //현수막 실사출력 옵션별 가격설정 파일 읽어오기
            $r_option_pr_include_file = array('coating', 'cut', 'design', 'processing');
            foreach ($r_option_pr_include_file as $value) 
            {
                $include_file_name = $cid."_".$value."_pr.php";
                if (file_exists("../data/print/goods_items/$include_file_name")) {
                    include_once "../data/print/goods_items/$include_file_name";
                    ${"r_ipro_".$value."_pr"} = json_decode(${"r_ipro_".$value."_pr"}, 1);
                    //debug(${"r_ipro_".$value."_pr"});
                }
            }
            
            //현수막 실사출력 인쇄비 가격설정 파일 읽어오기
            if (file_exists("../data/print/goods_items/$cid"."_print_pr_$_POST[print_goods_type].php")) {
                include_once "../data/print/goods_items/$cid"."_print_pr_$_POST[print_goods_type].php";
                ${"r_ipro_print_pr_".$_POST[print_goods_type]} = json_decode(${"r_ipro_print_pr_".$_POST[print_goods_type]}, 1);
                //debug(${"r_ipro_print_pr_".$_POST[print_goods_type]});
            }
            
            //현수막 실사출력 추가 인쇄비 가격설정 파일 읽어오기
            if (file_exists("../data/print/goods_items/$cid"."_print_pr_addprice_$_POST[print_goods_type].php")) {
                include_once "../data/print/goods_items/$cid"."_print_pr_addprice_$_POST[print_goods_type].php";
                ${"r_ipro_print_pr_addprice_".$_POST[print_goods_type]} = json_decode(${"r_ipro_print_pr_addprice_".$_POST[print_goods_type]}, 1);
                //debug(${"r_ipro_print_pr_addprice_".$_POST[print_goods_type]});
            }
        }

		//지류는 디지털과 옵셧 구분이 없다.		
		if (file_exists("../data/print/goods_items/$cid"."_paper.php")) {			
			include_once "../data/print/goods_items/$cid"."_paper.php";
			$r_ipro_paper = json_decode($r_ipro_paper, 1);
			//debug($r_ipro_paper);
		}
        
		if (file_exists("../data/print/goods_items/$cid"."_paper_dc.php")) {
			include_once "../data/print/goods_items/$cid"."_paper_dc.php";
            $r_ipro_paper_dc = json_decode($r_ipro_paper_dc, 1);
            //debug($r_ipro_paper_dc);
		}

		if (file_exists("../data/print/goods_items/$cid"."_normal_namecard.php")) {
			include_once "../data/print/goods_items/$cid"."_normal_namecard.php";
            $r_ipro_print_normal_namecard = json_decode($r_ipro_print_normal_namecard, 1);
            //debug($r_ipro_print_normal_namecard);
		}
		
		if (file_exists("../data/print/goods_items/$cid"."_normal_sticker.php")) {
			include_once "../data/print/goods_items/$cid"."_normal_sticker.php";
            $r_ipro_print_normal_sticker = json_decode($r_ipro_print_normal_sticker, 1);
            //debug($r_ipro_print_normal_sticker);
		}
	}
?>