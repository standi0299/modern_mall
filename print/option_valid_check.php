<?
/*
* @date : 20180608
* @author : kdk
* @brief : 옵션 제약사항 체크.
* @desc : 낱장,책자,옵셋별 처리를 해야하고 개발후 검증이 필요함.
*/
?>
<? 
	include_once 'lib_print.php';

    //상품 구분.
    /*
    "DG01" => "디지털 일반-명함",
    "DG02" => "디지털 일반-스티커",
 
    "DG03" => "디지털 낱장-일반",
    "DG04" => "디지털 낱장-스티커",
    "DG05" => "디지털 책자",
 
    "OS01" => "옵셋 낱장",
    "OS02" => "옵셋 책자",
    */    
    
    //낱장 제약사항.
    /*
    * 전단/포스터 등의 스코딕스 옵션 (모든 상품 공통)
      - 코팅을 안 하면 랑데부, 몽블랑, 아르떼, 스노우, 아트지, 앙상블, 반누보만 가능
            랑데부 PLU04,PLU05,PLU06,PLU07
            몽블랑 PLU12
            아르떼 PLU23,PLU24
            스노우 PML03,PNM09,PPR02,PNM10
            아트지 PST02,PNM11,PNM12
            앙상블 PLU25
            반누보 PLU15
    
      - 코팅을 하면 지종에 관계 없이 모두 가능
    
    * 전단/포스터 등의 재단 옵션 선택 가능 사이즈 제한 (모든 상품 공통)
      - 선택(입력)한 규격의 어느 한 쪽이 50mm보다 작으면 재단 옵션 선택 불가
    */        

    // * 스코딕스 옵션 (모든 상품 공통) -코팅을 안 하면 (랑데부,몽블랑,아르떼,스노우,아트지,앙상블,반누보)만 가능.
    $sc_paper = array(
        'PLU04', 'PLU05','PLU06','PLU07', //랑데부
        'PLU12', //몽블랑
        'PLU23','PLU24', //아르떼
        'PML03','PNM09','PPR02','PNM10', //스노우
        'PST02','PNM11','PNM12', //아트지
        'PLU25', //앙상블
        'PLU15', //반누보
    );

    // * 디지털 명함+백색명함 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, PET지)만 가능.
    $card_w_paper = array(
        'PPE01', //PET지
        'PPE02', //투명 PET지
        'PPE03', //화이트 PET지            
        'PLC06', //스튜디오 컬렉션 뉴블랙
        'PLC07', //스튜디오 컬렉션 딥블루
        'PLC08', //스튜디오 컬렉션 레드
        'PLU19', //스튜디오 컬렉션 뉴블랙
        'PLU20', //스튜디오 컬렉션 레드    
    );

    // * 전단지 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, PET지, 크라프트지, 투명스티커)만 가능.
    $jundan_w_paper = array(
        'PPE01', //PET지
        'PPE02', //투명 PET지
        'PPE03', //화이트 PET지            
        'PLC06', //스튜디오 컬렉션 뉴블랙
        'PLC07', //스튜디오 컬렉션 딥블루
        'PLC08', //스튜디오 컬렉션 레드
        'PLU19', //스튜디오 컬렉션 뉴블랙
        'PLU20', //스튜디오 컬렉션 레드
        'PST05', //투명 리무벌 스티커
        'PST06', //투명 스티커
        'PLC09', //크라프트지            
    );
    
    // * 전단지 - 별색 화이트 선택시 사이즈 (B2,B3,A2) 용지 제약 (딤플지, 크라프트지)만 가능.
    $jundan_w_s_paper = array(
        'PLC02', //딤플 블랙
        'PLC03', //딤플 커피브라운
        'PLC04', //딤플 포레스트그린
        'PLC05', //딤플 프리미어레드
        'PLC09', //크라프트지            
    );
    
    // * 리플렛 - A3(420) 이상 용지 (슈퍼파인, 마쉬멜로우, 썬샤인, 빌리지, 스튜디오컬렉션) 사용불가.
    $leaflet_s_paper = array(
        'PLU18', //슈퍼파인 스무스
        'PLU11', //마쉬맬로우 백색
        'PLU22', //썬샤인
        'PLU16', //빌리지 네추럴
        'PLU17', //빌리지 순백
        'PLC06', //스튜디오 컬렉션 뉴블랙
        'PLC07', //스튜디오 컬렉션 딥블루
        'PLC08', //스튜디오 컬렉션 레드
        'PLU19', //스튜디오 컬렉션 뉴블랙
        'PLU20', //스튜디오 컬렉션 레드            
    );

    // * 책자 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, 딤플지, 크라프트지)만 가능.(모든 용지별)
    $book_w_paper = array(
        'PLC06', //스튜디오 컬렉션 뉴블랙
        'PLC07', //스튜디오 컬렉션 딥블루
        'PLC08', //스튜디오 컬렉션 레드
        'PLU19', //스튜디오 컬렉션 뉴블랙
        'PLU20', //스튜디오 컬렉션 레드
        'PLC02', //딤플 블랙
        'PLC03', //딤플 커피브라운
        'PLC04', //딤플 포레스트그린
        'PLC05', //딤플 프리미어레드
        'PLC09', //크라프트지            
    );
    
    // * 책자- 인쇄컬러-누베라 선택시 모조지 100,120g 가능. (내지만)
    $book_n_paper = array(
        'PNM05', //미색모조
        'PNM07', //백색모조
        'PNM06', //미색모조.
        'PNM08', //백색모조.
    );
    
    $print_goods_type = $_POST[print_goods_type]; //상품구분
    //debug($print_goods_item[$param_goods_no]);

    try {
        
        $response[print_goods_type] = $print_goods_type;

        $max_size = 0;
        $min_size = 0;
        
        // * 규격 확인.
        if ($_POST[opt_size]) 
        {
            if ($_POST[opt_size] == "USER") { //사이즈 직접입력. 
                $size_x = $_POST[work_width];
                $size_y = $_POST[work_height];
            }
            else {
                $size_x = $r_ipro_standard_size[$_POST[opt_size]][size_x];
                $size_y = $r_ipro_standard_size[$_POST[opt_size]][size_y];
            }
            
            if ($size_x > $size_y) {
                $max_size = $size_x;
                $min_size = $size_y;
            }
            else {
                $max_size = $size_y;
                $min_size = $size_x;
            }
        }
        else 
        {
            $response[alert_msg] = "규격정보를 선택(입력)해주세요.";
        }				
				
		//낱장, 책자 상품경우 규격과 지류의 상관관계에 따라 체크한다.			20180707		chunter
		if ($print_goods_type == "DG03" || $print_goods_type == "DG05") 
		{					
			//사용자 규격일경우 사이즈에 맞는 종이 규격을 찾는다.
			if ($_POST['opt_size'] == "USER")					
				$opt_size_key = getPaperWithDocumentSize($_POST['cut_width'], $_POST['cut_height']);
			else
				$opt_size_key = $_POST['opt_size'];
			
			if ($opt_size_key && $_POST['opt_paper'] && $_POST['opt_paper_gram'])
			{							
				if (!checkPaperInOrderSize($opt_size_key, $_POST['opt_paper'], $_POST['opt_paper_gram']))
				{
					$response[alert_msg] = "해당 규격에 사용할 수 없는 지류입니다. 지류를 다시 선택해 주세요.";
					echo json_encode($response);
					exit;
				}
			}
		}
                
        ### 디지털 일반-명함, 디지털 일반-스티커, 디지털 낱장-일반, 디지털 낱장-스티커.
        // * 스코딕스 옵션 (모든 상품 공통) -코팅을 안 하면 (랑데부,몽블랑,아르떼,스노우,아트지,앙상블,반누보)만 가능.
        // * 재단 옵션 선택 가능 사이즈 제한 (모든 상품 공통) - 선택(입력)한 규격의 어느 한 쪽이 50mm보다 작으면 재단 옵션 선택 불가.
        // * 디지털 별색 조합 (모든 상품 공통) -그린+바이올렛 선택불가.
        if ($print_goods_type == "DG01" || $print_goods_type == "DG02" || $print_goods_type == "DG03" || $print_goods_type == "DG04" || $print_goods_type == "DG06") { //디지털 일반-명함, 디지털 일반-스티커, 디지털 낱장-일반, 디지털 낱장-스티커.
            // * 스코딕스 옵션 (모든 상품 공통) -코팅을 안 하면 (랑데부,몽블랑,아르떼,스노우,아트지,앙상블,반누보)만 가능.
            
            if ($_POST[opt_sc_check] == "Y" && $_POST[opt_gloss_check] != "Y") 
						{
	            if ($_POST[opt_sc] == "SC2" && !$_POST[opt_gloss]) 
	            {
	                if (!in_array($_POST[opt_paper], $sc_paper))
	                    $response[alert_msg] = "스코딕스와 코딩 옵션을 사용할 안 할 경우 용지는 랑데부,몽블랑,아르떼,스노우,아트지,앙상블,반누보만 가능합니다.";
	            }
						}
            
            // * 재단 옵션 선택 가능 사이즈 제한 (모든 상품 공통) - 선택(입력)한 규격의 어느 한 쪽이 50mm보다 작으면 재단 옵션 선택 불가.
            if ($_POST[opt_cutting_check] == "Y" && $_POST[opt_cutting] == "CU2") 
            {                
                if ($min_size < 50)
                    $response[alert_msg] = "선택(입력)한 규격의 어느 한 쪽이 50mm보다 작으면 재단 옵션 선택이 불가합니다.";
            }
            
            // * 디지털 별색 조합 (모든 상품 공통) -그린+바이올렛 선택불가.            
            if ($_POST[opt_print3_check] == "Y") 
            {
	            if ($_POST[opt_print3] && is_array($_POST[opt_print3])) {
	                $print3_cnt = 0;
	                foreach ($_POST[opt_print3] as $key => $val) {
	                    if ($val == "ET1") $print3_cnt++; //그린
	                    if ($val == "ET2") $print3_cnt++; //바이올렛
	                }
	                
	                if ($print3_cnt == 2) 
	                    $response[alert_msg] = "별색은 (그린 + 바이올렛) 조합으로 선택할 수 없습니다.";
	            }
						}
            
            ### 디지털 일반-명함 .
            // * 디지털 일반 명함+백색명함 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, PET지)만 가능.
            if ($print_goods_type == "DG01") //디지털 일반-명함 
            {
                if ($_POST[opt_print3] && in_array("ET4", $_POST[opt_print3])) //별색 화이트 
                {
                    if (!in_array($_POST[opt_paper], $card_w_paper))
                        $response[alert_msg] = "별색 화이트를 선택 할 경우에는 용지는 스튜디오 컬렉션, PET지 만 가능합니다.";
                }                
            }
            
            ### 디지털 낱장-일반.
            if ($print_goods_type == "DG03") { //디지털 낱장-일반.
                // * 코팅 선택시 용지 150g 미만 불가.(공통)
                if ($_POST[opt_gloss_check] == "Y" && $_POST[opt_gloss]) {
                    if ($_POST[opt_paper_gram] < 150)
                        $response[alert_msg] = "용지가 150g 미만을 경우 코팅을 선택할 수 없습니다.";
                }

                // * 도무송 선택시 용지 120g 이하 불가.(공통)
                if ($_POST[opt_domoo_check] == "Y" && $_POST[opt_domoo]) {
                    if ($_POST[opt_paper_gram] <= 120)
                        $response[alert_msg] = "용지가 120g 이하일  경우 도무송을 선택할 수 없습니다.";
                }
                
                // * 오시는 코팅 선택시 가능.(공통)
                if ($_POST[opt_oshi_check] == "Y" && $_POST[opt_oshi]) {
                    if ($_POST[opt_gloss_check] == "Y" && $_POST[opt_gloss]) {
                        // * 오시 선택시 용지 150g 미만 불가.(공통)
                        if ($_POST[opt_paper_gram] < 150)
                            $response[alert_msg] = "용지가 150g 미만을 경우 오시를 선택할 수 없습니다.";
                    }
                    else
                        $response[alert_msg] = "오시는 코팅을 같이 선택해야 합니다.";
                }
                
                ## 포스터
                // * 포스터 - 사이즈 직접입력. 입력한 사이즈가 b3 미만인 경우. 'B3 미만 사이즈는 전단에서 확인하실 수 있습니다.' 
                if (in_array($_POST[goodsno], $r_inpro_print_goodsno['PO'])) //포스터상품코드                
                {
                    if ($_POST[opt_size] == "USER" && $mim_size < "364") {
                        $response[alert_msg] = "B3 미만 사이즈는 전단에서 확인하실 수 있습니다.";
                        echo json_encode($response);
                        exit;
                    }                    
                }
                
                
                ## 전단지
                // * 전단지 - 제작 최대 사이즈 - 440*310, 최소사이즈- 85*50.
                // * 전단지 - 별색 화이트 선택시 사이즈 (B2,B3,A2) 용지 제약 (딤플지, 크라프트지)만 가능.
                // * 전단지 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, PET지, 크라프트지, 투명스티커)만 가능.
                // * 전단지 - 재단 50mm 미만.
                if (in_array($_POST[goodsno], $r_inpro_print_goodsno['JD'])) //전단지상품코드
                {
                    if ($max_size <= 440 && $min_size >= 50) // * 전단지 - 제작 최대 사이즈 - 440*310, 최소사이즈- 85*50.
                        $response[alert_msg] = "제작에 필요한 최대 사이즈는 440, 최소 사이즈 50 입니다.";

                    if ($_POST[opt_print3] && in_array("ET4", $_POST[opt_print3])) //별색 화이트 
                    {
                        if ($_POST[opt_size] == "B2" || $_POST[opt_size] == "B3" || $_POST[opt_size] == "A2") { //사이즈 B2,B3,A2. 
                            // * 전단지 - 별색 화이트 선택시 사이즈 (B2,B3,A2) 용지 제약 (딤플지, 크라프트지)만 가능.
                            if (!in_array($_POST[opt_paper], $jundan_w_s_paper))
                                $response[alert_msg] = "별색 화이트를 선택 할 경우에는 용지는 딤플지, 크라프트지 만 가능합니다.";
                        }
                        else {
                            // * 전단지 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, PET지, 크라프트지, 투명스티커)만 가능.
                            if (!in_array($_POST[opt_paper], $jundan_w_paper))
                                $response[alert_msg] = "별색 화이트를 선택 할 경우에는 용지는 스튜디오 컬렉션, PET지, 크라프트지, 투명스티커 만 가능합니다.";
                        }                        
                    }

                    // * 전단지 - 재단 50mm 미만.
                    if ($min_size < 50)
                        $response[alert_msg] = "선택(입력)한 규격의 어느 한 쪽이 50mm보다 작으면 도무송을 선택할 수 없습니다.";
                    
                    // * 전단지 - 사이즈 직접입력. 입력한 사이즈가 a3 초과인 경우. 'A3 초과 사이즈는 포스터에서 확인하실 수 있습니다.' 
                    if ($_POST[opt_size] == "USER" && $mim_size <= "420") {
                        $response[alert_msg] = "A3 초과 사이즈는 포스터에서 확인하실 수 있습니다.";
                        echo json_encode($response);
                        exit;
                    }
                }
                
                ## 초대장.엽서
                // * 초대장.엽서 - 제작 최대 사이즈 - 210*297, 최소사이즈- 85*50.
                if (in_array($_POST[goodsno], $r_inpro_print_goodsno['PC'])) //초대장.엽서상품코드 
                {
                    // * 초대장.엽서 - 제작 최대 사이즈 - 210*297, 최소사이즈- 85*50.
                    if ($max_size <= 440 && $min_size >= 50)
                        $response[alert_msg] = "제작에 필요한 최대 사이즈는 440, 최소 사이즈 50 입니다.";
                }

                ## 포토 프린트
                // * 포토 프린트 - 제작 최대 사이즈 - 440*310, 최소사이즈- 85*50.
                if (in_array($_POST[goodsno], $r_inpro_print_goodsno['PP'])) //포토프린트상품코드

               {
                    // * 포토 프린트 - 제작 최대 사이즈 - 440*310, 최소사이즈- 85*50.    
                    if ($max_size <= 440 && $min_size >= 50)
                        $response[alert_msg] = "제작에 필요한 최대 사이즈는 440, 최소 사이즈 50 입니다.";
                }
                
                ## 리플렛
                // * 리플렛 - 제작 최대 사이즈 - 740*510, 최소사이즈- 85*50.
                // * 리플렛 - A3(420) 이상 용지 (슈퍼파인, 마쉬멜로우, 썬샤인, 빌리지, 스튜디오컬렉션) 사용불가. 
                // * 리플렛 - 도무송: 50mm 미만 불가.
                if (in_array($_POST[goodsno], $r_inpro_print_goodsno['LF'])) //리플렛상품코드 
                {
                    // * 포토 프린트 - 제작 최대 사이즈 - 740*510, 최소사이즈- 85*50.    
                    if ($max_size <= 740 && $min_size >= 50)
                        $response[alert_msg] = "제작에 필요한 최대 사이즈는 740, 최소 사이즈 50 입니다.";

                    // * 리플렛 - A3(420) 이상 용지 (슈퍼파인, 마쉬멜로우, 썬샤인, 빌리지, 스튜디오컬렉션) 사용불가.
                    if (!in_array($_POST[opt_paper], $leaflet_s_paper)) {
                        //$response[alert_msg] = "A3이상 제작할 경우에는 용지는 슈퍼파인, 마쉬멜로우, 썬샤인, 빌리지, 스튜디오 컬렉션 만 가능합니다.";
                        $response[alert_msg] = "선택하신 용지의 최대 인쇄사이즈는 A3입니다.";
                    }
                            
                    // * 리플렛 - 도무송: 50mm 미만 불가.
                    if ($_POST[opt_domoo_check] == "Y" && $_POST[opt_domoo] && $min_size < 50)
                        $response[alert_msg] = "선택(입력)한 규격의 어느 한 쪽이 50mm보다 작으면 도무송을 선택할 수 없습니다.";
                }                    
                    
                ## 패키지
                // * 패키지 - 인쇄 최대사이즈-740*510, 최소 사이즈 - 85*50.
                // * 패키지 - 도무송 최대 사이즈-720*500, 최소 사이즈 - 85*50.
                if (in_array($_POST[goodsno], $r_inpro_print_goodsno['PK'])) //패키지상품코드 
                {
                    // * 패키지 - 도무송 최대 사이즈-720*500, 최소 사이즈 - 85*50.
                    if ($_POST[opt_domoo_check] == "Y" && $_POST[opt_domoo])
                    {
                        if ($max_size <= 720 && $min_size >= 50)
                            $response[alert_msg] = "제작에 필요한 최대 사이즈는 720, 최소 사이즈 50 입니다.";
                    }
                    else 
                    {
                        // * 패키지 - 인쇄 최대사이즈-740*510, 최소 사이즈 - 85*50.    
                        if ($max_size <= 740 && $min_size >= 50)
                            $response[alert_msg] = "제작에 필요한 최대 사이즈는 740, 최소 사이즈 50 입니다.";                            
                    }                        
                }
                
                ## 미니배너(미니,미니투명)
                
                ## 와블러
                // * 와블러 - 제작 최대 사이즈 - 720*500, 최소사이즈- 85*50.
                if (in_array($_POST[goodsno], $r_inpro_print_goodsno['WB'])) //와블러상품코드 
                {
                    // * 와블러 - 제작 최대 사이즈 - 720*500, 최소사이즈- 85*50.    
                    if ($max_size <= 720 && $min_size >= 50)
                        $response[alert_msg] = "제작에 필요한 최대 사이즈는 720, 최소 사이즈 50 입니다.";
                }
                
                ## 자석스티커
                // * 자석스티커 - 제작 최대 사이즈 - 440*310, 최소사이즈- 10*10.
                // * 자석스티커 - 인쇄 가능 별색 : 오렌지.
                if (in_array($_POST[goodsno], $r_inpro_print_goodsno['SM'])) //자석스티커상품코드 
                {
                    // * 자석스티커 - 제작 최대 사이즈 - 440*310, 최소사이즈- 10*10.    
                    if ($max_size <= 440 && $min_size >= 10)
                        $response[alert_msg] = "제작에 필요한 최대 사이즈는 440, 최소 사이즈 10 입니다.";
                    
                    // * 자석스티커 - 인쇄 가능 별색 : 오렌지.(상품옵션구성)
                }
                
            }

            ### 디지털 일반-스티커.
            if ($print_goods_type == "DG02") { //디지털 일반-스티커.
                //원형,타원형,사각라운드형

                //사용자 입력 규격의 경우 비규격 사이즈가  width, height 설정한다.
                if ($_POST['opt_size'] == "USER") {

                    if (in_array($_POST[goodsno], $r_inpro_print_goodsno['SO'])) //스티커원형상품코드 
                    {
                        $prefix = "SO"; //SO 스티커 원형.
                        
                        if ($max_size <= 100 && $min_size >= 10)
                            $response[alert_msg] = "입력하신 제작에 필요한 최대 사이즈는 100, 최소 사이즈 10 입니다.";
                    }
                    else if (in_array($_POST[goodsno], $r_inpro_print_goodsno['SE'])) //스티커타원상품코드 
                    {
                        $prefix = "SE"; //SE 스티커 타원.
                        
                        if ($max_size <= 115 && $min_size >= 15)
                            $response[alert_msg] = "제작에 필요한 최대 사이즈는 115, 최소 사이즈 15 입니다.";
                    }
                    else if (in_array($_POST[goodsno], $r_inpro_print_goodsno['SR'])) //스티커라운드상품코드 
                    {
                        $prefix = "SR"; //SR 스티커 라운드.
                        
                        if ($max_size <= 70 && $min_size >= 10)
                            $response[alert_msg] = "제작에 필요한 최대 사이즈는 70, 최소 사이즈 10 입니다.";
                    }
                    
                    //사용자규격 입력범위 제약사항.
                    $size_key = getOptionSizeWithCalcuPriceData($r_iro_normal_product, $_POST[cut_width], $_POST[cut_height]);
                    //debug($_POST[cut_width] .":". $_POST[cut_height]);
                    //debug($size_key);
                    //debug($r_ipro_print_code[$size_key]);
                    if ($size_key == "")
                        $response[alert_msg] = "제작할 수 없는 사용자 입력범위입니다.";
                                        
                    /*
                    $size_key = getStickerPrintOptSizeWithPrefixCustom($prefix, $size_x, "");
                    //debug($size_key);
                    //debug($r_ipro_print_code[$size_key]);
                    
                    $diffSize = explode("*", $r_ipro_print_code[$size_key]);
                    if ($diffSize) 
                    {
                        if ($diffSize[0] > $diffSize[1]) {
                            $diffMaxSize = $diffSize[0];
                            $diffMinSize = $diffSize[1];
                        }
                        else {
                            $diffMaxSize = $diffSize[1];
                            $diffMinSize = $diffSize[0];
                        }                        
                    }
                    
                    if ($max_size <= $diffMaxSize && $diffMinSize >= 5)
                        $response[alert_msg] = "제작에 필요한 최대 사이즈는 $diffMaxSize, 최소 사이즈 $diffMinSize 입니다.";
                    
                    */
                }
                
            }
            
            ### 디지털 낱장-스티커.
            // * 디지털 낱장-스티커 - 최대 사이즈 - 400*280, 최소 사이즈 - 5*5.
            // * 디지털 낱장-스티커 - 가능 별색 - 오렌지, 백색(스티커 사이즈 400*280만까지 가능).
            if ($print_goods_type == "DG04" || $print_goods_type == "DG06") {
                // * 디지털 낱장-스티커 - 최대 사이즈 - 400*280, 최소 사이즈 - 5*5.
                if ($max_size <= 400 && $min_size >= 5)
                    $response[alert_msg] = "제작에 필요한 최대 사이즈는 400, 최소 사이즈 5 입니다.";

                // * 디지털 낱장-스티커 - 가능 별색 - 오렌지, 백색(스티커 사이즈 400*280만까지 가능).
                if (in_array("ET4", $_POST[opt_print3]) || in_array("ET3", $_POST[opt_print3])) //별색 화이트, 오렌지 
                {
                    if ($max_size <= 400 && $min_size >= 280)
                        $response[alert_msg] = "별색 (오렌지, 화이트)는 최대 사이즈는 400, 최소 사이즈 280 입니다.";
                }
                 
            }
        }
        else if ($print_goods_type == "DG05") { //디지털 책자.
            ### 디지털 책자.
            // * 후가공 옵션은 표지에만 해당함.
            // * -(확인필요!)제작 최대사이즈 :  제본 가능 최대사이즈 판형까지 (사용자규격사용시체크)
            // * -(확인필요!)중철제본-150g 미만 용지 48페이지까지 가능,150g 이상 용지 28페이지까지 가능 (내지만???)
            // * -(확인필요!)무선제본최대사이즈: 세로-320mm
            // * -(확인필요!)중철제본최대사이즈:460*320
            
            // * 스코딕스 옵션 (모든 상품 공통) -코팅을 안 하면 (랑데부,몽블랑,아르떼,스노우,아트지,앙상블,반누보)만 가능 (표지만 적용).
            // * A3,B4 사이즈 무선제본 불가.(opt_bind:BD1,BD2)            
            // * 코팅 선택시 용지 150g 미만 불가.(표지,내지)
            // * 도무송 선택시 용지 120g 이하 불가.(표지)            
            // * 책자 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, 딤플지, 크라프트지)만 가능.(모든 용지별)
            // * 인쇄컬러-누베라 선택시 모조지 100,120g 가능. (내지만)
            
            // * 스프링제본- 내지 두께 28mm 까지 가능. (양/단면으로 페이지수를 장당 계산함-양면 : 페이지수 / 2 하고 올림) 
            // * 무선제본세네카- 내지 두께 50mm까지 가능. (양/단면으로 페이지수를 장당 계산함-양면 : 페이지수 / 2 하고 올림)

            // * - 무선제본 과 중철제본
            // * # 무선제본은 가로든 세로든 제본하는 방향 기준으로 320mm까지만 제본 가능
            // * # 중철제본은 펼침면 기준으로 최대 사이즈가 460mm x 320mm까지 허용
            // * -> 따라서 실제 주문 규격은 230mm x 320mm 이하이거나 460mm x 160mm 이하여야 함 (한쪽이 230mm 초과일 때 다른 한 쪽도 160mm 초과이면 주문 불가)
            
            // * - 중철제본의 내지 페이지 수 제한
            // * # 내지가 150g 미만 용지일 때 내지의 페이지수는 48페이지까지 가능
            // * # 내지가 150g 이상 용지일 때 내지의 페이지수는 28페이지까지 가능
            // * -> 참고) 4p 1장이므로 48p는 12장, 28p는 7장
            
            // * - 책등(세네카) 두께 계산은 제본 방법 및 표지 지종별 두께에 상관 없이 내지 지종별 두께의 합으로만 계산 (표지 무관)
            
            // * - 날개 있음 혹은 날개 크기를 선택했을 때 선택한 규격이 B4 이상일 때  B4 이상의 규격에서는 날개를 사용할 수 없습니다.364            
            if ($_POST[outside_wing] == "WI2") {
                if ($max_size >= "364") {
                    $response[alert_msg] = "B4 이상의 규격에서는 날개를 사용할 수 없습니다.";
                    echo json_encode($response);
                    exit;
                }
            }
            
            // * 스코딕스 옵션 (모든 상품 공통) -코팅을 안 하면 (랑데부,몽블랑,아르떼,스노우,아트지,앙상블,반누보)만 가능.
            if ($_POST[opt_sc_check] == "Y" && $_POST[opt_gloss_check] != "Y") 
						{
	            if ($_POST[opt_sc] == "SC2" && !$_POST[opt_gloss]) 
	            {
	                if (!in_array($_POST[opt_paper], $sc_paper))
	                    $response[alert_msg] = "스코딕스와 코딩 옵션을 사용할 안 할 경우 용지는 랑데부,몽블랑,아르떼,스노우,아트지,앙상블,반누보만 가능합니다.";
	            }
						}
            
            // * A3,B4 사이즈 무선제본 불가.(opt_bind:BD1,BD2)
            if ($_POST[opt_bind] == "BD1" || $_POST[opt_bind] == "BD2") 
            {
                if ($_POST[opt_size] == "A3" || $_POST[opt_size] == "B4")
                    $response[alert_msg] = "A3,B4 규격은 무선 제본을 사용할 수 없습니다.\n별도견적 문의바랍니다.";
            }
            
            // * 도무송 선택시 용지 120g 이하 불가.(표지)
            if ($_POST[opt_domoo_check] == "Y" && $_POST[opt_domoo]) {
                if ($_POST[outside_paper_gram] <= 120)
                    $response[alert_msg] = "표지 용지가 120g 이하일  경우 도무송을 선택할 수 없습니다.";
            }            
            
            // * 코팅 선택시 용지 150g 미만 불가.(표지)
            if ($_POST[outside_gloss_check] == "Y" && $_POST[outside_gloss]) {
                if ($_POST[outside_paper_gram] < 150)
                    $response[alert_msg] = "표지 용지가 150g 미만을 경우 코팅(표지)을 선택할 수 없습니다.";
            }

            // * 코팅 선택시 용지 150g 미만 불가.(내지)
            if ($_POST[opt_gloss_check] == "Y" && $_POST[opt_gloss]) {
                foreach ($_POST[inside_paper_gram] as $key => $val) {
                    if ($val < 150) {
                        $response[alert_msg] = "내지 용지가 150g 미만을 경우 코팅(내지)을 선택할 수 없습니다.";
                        break;
                    }
                }
            }
            
            // * 책자 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, 딤플지, 크라프트지)만 가능.(모든 용지별)
            if ($_POST[outside_print3] && in_array("ET4", $_POST[outside_print3])) //표지 별색 화이트. 
            {
                if (!in_array($_POST[outside_paper], $book_w_paper))
                    $response[alert_msg] = "별색 화이트를 선택 할 경우에는 용지는 스튜디오 컬렉션, 딤플지, 크라프트지 만 가능합니다.";
            }


            $page_cnt = 0; //총 페이지수.
            
            if ($_POST[outside_page] > 0) $page_cnt += $_POST[outside_page];
            
            $paper_width = array();
            
            //내지.
            foreach ($_POST['inside_page'] as $key => $value) 
            {
                $inside_print1 = "inside_print1_" . $key;
                $inside_print2 = "inside_print2_" . $key;
                $inside_print3 = "inside_print3_" . $key;
                $inside_print3_check = "inside_print3_check_" . $key;

                $inside_page = $_POST['inside_page'][$key];
                $inside_paper = $_POST['inside_paper'][$key];
                $inside_paper_gram = $_POST['inside_paper_gram'][$key];
                
                if ($inside_page > 0) $page_cnt += $inside_page;
                
                // * 책자 - 내지용지 160g이상 선택시 ,무선제본이 선택되었을때 '내지용지가 두꺼워 제본이 떨어질 수 있습니다.'
                if ($_POST[opt_bind] == "BD1" || $_POST[opt_bind] == "BD2") 
                {
                    if ($inside_paper_gram > 160)
                        $response[caution_msg] = "내지용지가 두꺼워 제본이 떨어질 수 있습니다.";                        
                }

                // * 책자 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, 딤플지, 크라프트지)만 가능.(내지)
                if ($_POST[$inside_print3] && in_array("ET4", $_POST[$inside_print3])) //내지 별색 화이트. 
                {
                    if (!in_array($_POST['inside_paper'][$key], $book_w_paper)) {
                        $response[alert_msg] = "별색 화이트를 선택 할 경우에는 용지는 스튜디오 컬렉션, 딤플지, 크라프트지 만 가능합니다.";
                        break;
                    }
                }
                
                // * 인쇄컬러-누베라 선택시 모조지 100,120g 가능. (내지만)
                if ($_POST[$inside_print1] == "N") //흑백(누베라). 
                {
                    if (in_array($_POST['inside_paper'][$key], $book_n_paper)) {
                        if ($_POST['inside_paper_gram'][$key] <= 120 && $_POST['inside_paper_gram'][$key] >= 100) {
                            $response[alert_msg] = "인쇄컬러-흑백(누베라)를 선택할 경우 용지는 모조지 100,120g 만 가능합니다.";
                            break;
                        }
                    }
                    else 
                        $response[alert_msg] = "인쇄컬러-흑백(누베라)를 선택할 경우 용지는 모조지 만 가능합니다.";
                }
                
                //페이지수 입력.
                if (!$_POST['inside_page'][$key]) 
                {
                    $response[alert_msg] = "내지 페이지 수를 입력하세요.";
                    break;
                }
                
                //양면/단면 선택.
                if (!$_POST[$inside_print2]) 
                {
                    $response[alert_msg] = "내지 양면/단면을 선텍해 주세요.";
                    break;
                }

                //장수 계산.
                if ($_POST[$inside_print2] == "D") //양면이면 입력한 페이지수를 2로 나누고 올림처리함.
                {
                    $inside_page = ceil($inside_page / 2);
                }

                //용지 두께  계산.    
                $p_width = $r_ipro_standard_paper_width[$inside_paper][$inside_paper_gram];
                $paper_width[] = ($inside_page * $p_width);
            }

            //간지,면지.
            foreach ($_POST['inpage_page'] as $key => $value) 
            {       
                $inpage_print1 = "inpage_print1_" . $key;
                $inpage_print2 = "inpage_print2_" . $key;
                $inpage_print3 = "inpage_print3_" . $key;
                $inpage_print3_check = "inpage_print3_check_" . $key;

                $inpage_page = $_POST['inpage_page'][$key];
                $inpage_paper = $_POST['inpage_paper'][$key];
                $inpage_paper_gram = $_POST['inpage_paper_gram'][$key];
                
                if ($inpage_page > 0) $page_cnt += $inpage_page;

                // * 책자 - 별색 화이트 선택시 용지 제약 (스튜디오 컬렉션, 딤플지, 크라프트지)만 가능.(간지,면지)
                if ($_POST[$inpage_print3] && in_array("ET4", $_POST[$inpage_print3])) //간지,면지 별색 화이트. 
                {
                    if (!in_array($_POST['inpage_paper'][$key], $book_w_paper)) {
                        $response[alert_msg] = "별색 화이트를 선택 할 경우에는 용지는 스튜디오 컬렉션, 딤플지, 크라프트지 만 가능합니다.";
                        break;
                    }
                }
                                
                //페이지수 입력.
                if (!$_POST['inpage_page'][$key]) 
                {
                    $response[alert_msg] = "간지/면지 페이지 수를 입력하세요.";
                    break;
                }
                
                //양면/단면 선택.
                if (!$_POST[$inpage_print2]) 
                {
                    $response[alert_msg] = "간지/면지 양면/단면을 선텍해 주세요.";
                    break;
                }

                //장수 계산.
                if ($_POST[$inpage_print2] == "D") //양면이면 입력한 페이지수를 2로 나누고 올림처리함.
                {
                    $inpage_page = ceil($inpage_page / 2);
                }

                //용지 두께  계산.    
                $p_width = $r_ipro_standard_paper_width[$inpage_paper][$inpage_paper_gram];
                $paper_width[] = ($inpage_page * $p_width);
            }
            
            //debug($paper_width);
            //용지 두께  합 계산.
            if (is_array($paper_width)) {
                foreach ($paper_width as $key => $val) {
                    $paperWidth += $val; 
                }
            }
            //debug($paperWidth);
            
            // * 제본 선택시 A3,B4 선택시   '별도견적 문의바랍니다.'            
            if ($_POST[opt_bind] && $_POST[opt_bind] != "BD7") 
            {
                if ($_POST[opt_size] == "A3" || $_POST[opt_size] == "B4")
                    $response[alert_msg] = "별도견적 문의바랍니다.";
            }
            
            // * 스프링제본- 내지 두께 28mm 까지 가능. (양/단면으로 페이지수를 장당 계산함-양면 : 페이지수 / 2 하고 올림)
            if ($_POST[opt_bind] == "BD5" || $_POST[opt_bind] == "BD6") 
            {
                if ($paperWidth > 28)
                    $response[alert_msg] = "스프링제본은 내지 용지 두께가 28mm 까지만 가능합니다.";
            } 
            
            // * 무선제본세네카- 내지 두께 50mm까지 가능. (양/단면으로 페이지수를 장당 계산함-양면 : 페이지수 / 2 하고 올림)            
            else if ($_POST[opt_bind] == "BD1" || $_POST[opt_bind] == "BD2") 
            {
                if ($paperWidth > 50)
                    $response[alert_msg] = "무선제본세네카는 내지 용지 두께가 50mm 까지만 가능합니다.";
                
                //A3,B4 선택시  '별도견적 문의바랍니다.'
                
            }

            // * # 중철제본은 펼침면 기준으로 최대 사이즈가 460mm x 320mm까지 허용
            // * -> 따라서 실제 주문 규격은 230mm x 320mm 이하이거나 460mm x 160mm 이하여야 함 (한쪽이 230mm 초과일 때 다른 한 쪽도 160mm 초과이면 주문 불가)
            else if ($_POST[opt_bind] == "BD3") 
            {
                if ($max_size <= 230 && $min_size >= 160)
                    $response[alert_msg] = "중철제본 제작 범위를 초과했습니다.";
                
                // * 책자 - 중철제본 선택시    페이지 수 48페이지 초과시 '페이지수 초과로 중철제본이 어렵습니다.'
                if ($page_cnt > 48)
                    $response[alert_msg] = "페이지수 초과로 중철제본이 어렵습니다.";
            }
            
        }
        else if ($print_goods_type == "OS01") { //옵셋 낱장.
            // * 코팅 선택시 용지 150g 미만 불가.(공통)
            if ($_POST[opt_gloss_check] == "Y" && $_POST[opt_gloss]) {
                if ($_POST[opt_paper_gram] < 150)
                    $response[alert_msg] = "용지가 150g 미만을 경우 코팅을 선택할 수 없습니다.";
            }

        }
        else if ($print_goods_type == "OS02") { //옵셋 책자.
            // * 코팅 선택시 용지 150g 미만 불가.(공통)
            if ($_POST[opt_gloss_check] == "Y" && $_POST[opt_gloss]) {
                if ($_POST[opt_paper_gram] < 150)
                    $response[alert_msg] = "용지가 150g 미만을 경우 코팅을 선택할 수 없습니다.";
            }
            // * 오시 선택시 용지 150g 미만 불가.(공통)
            if ($_POST[opt_oshi_check] == "Y" && $_POST[opt_oshi]) {
                if ($_POST[opt_paper_gram] < 150)
                    $response[alert_msg] = "용지가 150g 미만을 경우 코팅을 선택할 수 없습니다.";
            }

            // * 넘버링 가능한 사이즈:A4,A5,B5,B6
            if ($_POST[opt_number_check] == "Y" && $_POST[opt_number] == "NR2") 
            {
                if ($_POST[opt_size] != "A4" || $_POST[opt_size] != "A5" || $_POST[opt_size] != "B5" || $_POST[opt_size] != "B6")
                    $response[alert_msg] = "넘버링 가능한 사이즈:A4,A5,B5,B6 입니다.";
            }            
        }

    }
    catch (Exception $e) {
        $response[error] = $e->getMessage();
    }

    echo json_encode($response);
?>