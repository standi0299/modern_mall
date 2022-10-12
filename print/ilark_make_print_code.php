<?
	include_once '../_header.php'; 
	include_once 'lib_print.php';
    include_once '../a/print/lib_util_print_admin.php';	
		
	//$r_ipro_print_code 배열 만드는 파일. 웹에서 페이지 실행후 내용 긁어서 배열에 할당 
	//lib_const_print.php 파일의  $r_ipro_print_code 배열 만들기
	
	$optionData = adminGetOptionAllItems("SIZE","", " and (opt_prefix = 'A' or opt_prefix = 'B')");
	if (is_array($optionData))
	{
		foreach ($optionData as $key => $value) {
			$result .= "\"$key\" => \"$value\",";
		}
		$result .= "\t\t\t//일반 규격\r";
	}
	
	
	$optionData = adminGetOptionAllItems("SIZE","SZ");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//명함 규격\r";
	
	$optionData = adminGetOptionAllItems("SIZE","SO");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//스티커 원형\r";
	
	$optionData = adminGetOptionAllItems("SIZE","SE");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//스티커 타원\r";
	
	
	$optionData = adminGetOptionAllItems("SIZE","SR");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//스티커 라운드\r";
	
	
	$optionData = adminGetOptionAllItems("PRINT", "OC");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//단면 인쇄 -칼라 \r";
	$optionData = adminGetOptionAllItems("PRINT", "OB");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//단면인쇄 - 흑백\r";
	
	$optionData = adminGetOptionAllItems("PRINT", "DC");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//양면인쇄 -칼러\r";
	$optionData = adminGetOptionAllItems("PRINT", "DB");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//양면인쇄 -흑백\r";
	
	$optionData = adminGetOptionAllItems("PRINT", "ET");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//별색인쇄 \r";
	
	$optionData = adminGetOptionAllItems("GLOSS");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//코팅 \r";
	
	$optionData = adminGetOptionAllItems("PUNCH");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//타공 \r";
	
	$optionData = adminGetOptionAllItems("OSHI");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//오시 \r";
	
	$optionData = adminGetOptionAllItems("MISSING");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//미싱 \r";
	
	$optionData = adminGetOptionAllItems("ROUND");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//귀도리 \r";
	
	$optionData = adminGetOptionAllItems("DOMOO");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//도무송 \r";
	
	$optionData = adminGetOptionAllItems("BARCODE");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//바코드 \r";
	
	$optionData = adminGetOptionAllItems("NUMBER");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//넘버링 \r";
	
	$optionData = adminGetOptionAllItems("STAND");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//스탠드 \r";
	
	$optionData = adminGetOptionAllItems("DANGLE");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//댕글 \r";
	
	$optionData = adminGetOptionAllItems("TAPE");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//양면테잎 \r";
	
	$optionData = adminGetOptionAllItems("ADDRESS");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//주소인쇄 \r";
	
	$optionData = adminGetOptionAllItems("SC");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//스코딕스 \r";
	
	$optionData = adminGetOptionAllItems("SCB");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//스코딕스박 \r";
	
	$optionData = adminGetOptionAllItems("WING");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//날개 \r";
	
	$optionData = adminGetOptionAllItems("BIND");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//제본 \r";
	
	$optionData = adminGetOptionAllItems("BINDTYPE");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//제본 타입 \r";
	
	
	$optionData = adminGetOptionAllItems("CUTTING");
	foreach ($optionData as $key => $value) {
		$result .= "\"$key\" => \"$value\",";
	}
	$result .= "\t\t\t//재단 \r";


    $optionData = adminGetOptionAllItems("INSTANT");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//즉석명함 \r";
    
    
    $optionData = adminGetOptionAllItems("HOLDING");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//접지(옵셋) \r";
    
    
    $optionData = adminGetOptionAllItems("PRESS");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//형압 \r";    
    
    $optionData = adminGetOptionAllItems("FOIL");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//박(옵셋) \r";    
    
    $optionData = adminGetOptionAllItems("UVC");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//부분UV(옵셋) \r";

    $optionData = adminGetOptionAllItems("SIZE","SPR");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//현수막 실사출력 사이즈\r";

    $optionData = adminGetOptionAllItems("COATING");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//코팅(현수막 실사출력) \r";

    $optionData = adminGetOptionAllItems("CUT");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//재단(현수막 실사출력) \r";
    
    $optionData = adminGetOptionAllItems("DESIGN");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//디자인(현수막 실사출력) \r";

    $optionData = adminGetOptionAllItems("PROCESSING");
    foreach ($optionData as $key => $value) {
        $result .= "\"$key\" => \"$value\",";
    }
    $result .= "\t\t\t//가공&마감(현수막 실사출력) \r";

	debug($result);
	
		
?>	
	
