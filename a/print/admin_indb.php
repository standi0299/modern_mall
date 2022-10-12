<?
/*
* @date : 20190322
* @author : kdk
* @brief : 견적 상품 추가 현수막(Placard),실사출력(RealPrint)
* @desc : 후가공 옵션 추가 opt_coating[],opt_processing[],opt_design[]
*/
 
	include_once '../../lib/library.php';	
    include_once '../../print/lib_print.php';
    	
	if (!$_POST[goods_no]) msg("필수값이 없습니다.", -1);
	
	if ($_POST[mode] == "goods_opt_sel")	
	{		
				
		if (is_array($_POST[opt_paper]))
		{
			foreach ($_POST[opt_paper] as $key => $value) {				
				//$paper[$value] = $_POST[$value];
				$paper[$value] = $_POST["opt_paper_".$value];
			}
		}

		if (is_array($_POST[opt_size]))
			$result['size'] = $_POST[opt_size];
		
		if (is_array($paper))
			$result['paper'] = $paper;
		
		if (is_array($_POST[opt_print]))
			$result['print'] = $_POST[opt_print];
				
		
		if (is_array($_POST[opt_gloss]))
			$result['gloss'] = $_POST[opt_gloss];
		
		
		if (is_array($_POST[opt_sc]))
			$result['sc'] = $_POST[opt_sc];
		if (is_array($_POST[opt_scb]))
			$result['scb'] = $_POST[opt_scb];
		if (is_array($_POST[opt_bind]))
			$result['bind'] = $_POST[opt_bind];
        if (is_array($_POST[opt_bind_type]))
            $result['bind_type'] = $_POST[opt_bind_type];
		if (is_array($_POST[opt_wing]))
			$result['wing'] = $_POST[opt_wing];
		
		if (is_array($_POST[opt_oshi]))
			$result['oshi'] = $_POST[opt_oshi];
		if (is_array($_POST[opt_punch]))
			$result['punch'] = $_POST[opt_punch];
		if (is_array($_POST[opt_missing]))
			$result['missing'] = $_POST[opt_missing];
		if (is_array($_POST[opt_round]))
			$result['round'] = $_POST[opt_round];
		if (is_array($_POST[opt_domoo]))
			$result['domoo'] = $_POST[opt_domoo];
		if (is_array($_POST[opt_barcode]))
			$result['barcode'] = $_POST[opt_barcode];
		if (is_array($_POST[opt_number]))
			$result['number'] = $_POST[opt_number];
		
		if (is_array($_POST[opt_stand]))
			$result['stand'] = $_POST[opt_stand];
		
		if (is_array($_POST[opt_dangle]))
			$result['dangle'] = $_POST[opt_dangle];
			
		if (is_array($_POST[opt_tape]))
			$result['tape'] = $_POST[opt_tape];
		if (is_array($_POST[opt_address]))
			$result['address'] = $_POST[opt_address];

        if (is_array($_POST[opt_instant]))
            $result['instant'] = $_POST[opt_instant];

        if (is_array($_POST[opt_cutting]))
            $result['cutting'] = $_POST[opt_cutting];

        if (is_array($_POST[opt_foil]))
            $result['foil'] = $_POST[opt_foil];

        if (is_array($_POST[opt_holding]))
            $result['holding'] = $_POST[opt_holding];
        
        if (is_array($_POST[opt_press]))
            $result['press'] = $_POST[opt_press];

        if (is_array($_POST[opt_uvc]))
            $result['uvc'] = $_POST[opt_uvc];
        
        //전단지용 건수 사용여부.
        if ($_POST[opt_cnt_use])
            $result['cnt_use'] = $_POST[opt_cnt_use];

        //현수막,실사출력 부가세 포함여부.
        if ($_POST[opt_vat_use])
            $result['vat_use'] = $_POST[opt_vat_use];
        
        //현수막,실사출력 후가공 옵션
        if (is_array($_POST[opt_coating]))
            $result['coating'] = $_POST[opt_coating];

        if (is_array($_POST[opt_processing]))
            $result['processing'] = $_POST[opt_processing];

        if (is_array($_POST[opt_design]))
            $result['design'] = $_POST[opt_design];
        if (is_array($_POST[opt_cut]))
            $result['cut'] = $_POST[opt_cut];

        
		//debug($result);
		//debug($print_goods_item[1200]);
	} 
	else if ($_POST[mode] == "goods_book_opt_sel")
	{
		
		if (is_array($_POST[outside_opt_paper]))
		{
			foreach ($_POST[outside_opt_paper] as $key => $value) {				
				$outside_paper[$value] = $_POST["outside_opt_paper_".$value];
			}
		}
		
		if (is_array($_POST[inside_opt_paper]))
		{
			foreach ($_POST[inside_opt_paper] as $key => $value) {				
				$inside_paper[$value] = $_POST["inside_opt_paper_".$value];
			}
		}
		
		if (is_array($_POST[inpage_opt_paper]))
		{
			foreach ($_POST[inpage_opt_paper] as $key => $value) {				
				$inpage_paper[$value] = $_POST["inpage_opt_paper_".$value];			
			}
		}
		
		if (is_array($_POST[opt_size]))
			$result['size'] = $_POST[opt_size];
		
		//표지 옵션처리
		if (is_array($outside_paper))
			$result['outside_paper'] = $outside_paper;
		
		if (is_array($_POST[outside_opt_print]))
			$result['outside_print'] = $_POST[outside_opt_print];

        //옵셋 책자 인쇄(뒷면)
        if (is_array($_POST[outside_opt_print_back]))
            $result['outside_print_back'] = $_POST[outside_opt_print_back];

		if (is_array($_POST[outside_opt_gloss]))
			$result['outside_gloss'] = $_POST[outside_opt_gloss];
				
		if (is_array($_POST[outside_opt_sc]))
			$result['outside_sc'] = $_POST[outside_opt_sc];
		if (is_array($_POST[outside_opt_scb]))
			$result['outside_scb'] = $_POST[outside_opt_scb];
		if (is_array($_POST[outside_opt_wing]))
			$result['outside_wing'] = $_POST[outside_opt_wing];
		
		//내지 옵션
		if (is_array($inside_paper))
			$result['inside_paper'] = $inside_paper;
		
		if (is_array($_POST[inside_opt_print]))
			$result['inside_print'] = $_POST[inside_opt_print];

        if (is_array($_POST[opt_sc]))
            $result['sc'] = $_POST[opt_sc];
        if (is_array($_POST[opt_scb]))
            $result['scb'] = $_POST[opt_scb];
		
		//간지 옵션
		if (is_array($inpage_paper))
			$result['inpage_paper'] = $inpage_paper;
		
		if (is_array($_POST[inpage_opt_print]))
			$result['inpage_print'] = $_POST[inpage_opt_print];
		
		//기타 옵션		
		if (is_array($_POST[opt_bind]))
			$result['bind'] = $_POST[opt_bind];
        if (is_array($_POST[opt_bind_type]))
            $result['bind_type'] = $_POST[opt_bind_type];
                
		if (is_array($_POST[opt_wing]))
			$result['wing'] = $_POST[opt_wing];
		
        if (is_array($_POST[opt_gloss]))
            $result['gloss'] = $_POST[opt_gloss];
        
		if (is_array($_POST[opt_oshi]))
			$result['oshi'] = $_POST[opt_oshi];
		if (is_array($_POST[opt_punch]))
			$result['punch'] = $_POST[opt_punch];
		if (is_array($_POST[opt_missing]))
			$result['missing'] = $_POST[opt_missing];
		if (is_array($_POST[opt_round]))
			$result['round'] = $_POST[opt_round];
		if (is_array($_POST[opt_domoo]))
			$result['domoo'] = $_POST[opt_domoo];
		if (is_array($_POST[opt_barcode]))
			$result['barcode'] = $_POST[opt_barcode];
		if (is_array($_POST[opt_number]))
			$result['number'] = $_POST[opt_number];
		
		if (is_array($_POST[opt_stand]))
			$result['stand'] = $_POST[opt_stand];
		
		if (is_array($_POST[opt_dangle]))
			$result['dangle'] = $_POST[opt_dangle];
			
		if (is_array($_POST[opt_tape]))
			$result['tape'] = $_POST[opt_tape];
		if (is_array($_POST[opt_address]))
			$result['address'] = $_POST[opt_address];

        if (is_array($_POST[opt_instant]))
            $result['instant'] = $_POST[opt_instant];
        
        if (is_array($_POST[opt_cutting]))
            $result['cutting'] = $_POST[opt_cutting];
        
        if (is_array($_POST[opt_foil]))
            $result['foil'] = $_POST[opt_foil];

        if (is_array($_POST[opt_holding]))
            $result['holding'] = $_POST[opt_holding];
        
        if (is_array($_POST[opt_press]))
            $result['press'] = $_POST[opt_press];
        
        if (is_array($_POST[opt_uvc]))
            $result['uvc'] = $_POST[opt_uvc];
	}

    //다이렉트파일업로드 사용여부.	
    if ($_POST[opt_directupload_use])
        $result['directupload_use'] = $_POST[opt_directupload_use];

    
	//debug($result);
	//exit;
	$print_goods_item[$_POST[goods_no]] = $result;

	$logfile = fopen("../../data/print/goods_items/".$_POST[goods_no].".php", "w" );
	fwrite( $logfile, "<?\n");
	fwrite( $logfile, "\$goods_item='" .json_encode($result)."';\n");
	fwrite( $logfile, "?>");
	fclose( $logfile );
		
	/*
	//명함	
	$result  = "
		\"{$_POST[goods_no]}\" => array(
			\"size\" => array("A3", "A4", "B2", "B4", "USER"),
			\"paper\" => array("NR001"=>array("90", "100"), "NR002" => array("100", "110"), "LU001"=> array("100", "110"), "LC001"=> array("90", "110"), "PK001"=> array("100", "110"), "SP001"=> array("100", "110")),
			\"print\" => array("OC4", "DC8", "OI1", "DI2"),
			\"gloss\" => array("NG_one", "BG_one", "NG_double", "BG_double"),
			\"punch\" => array("1H", "2H", "3H"),
			\"oshi\" => array("1R", "2R", "3R"),	
		),";
	*/
	
	
	//debug($_POST);
    //debug($print_goods_item[$_POST[goods_no]] = $result);

    //echo "<br><button type=\"button\" onclick=\"location.href='".$_SERVER[HTTP_REFERER]."'\">확인</button>";
    //exit;

if (!$_POST[rurl])
    $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
	
?>