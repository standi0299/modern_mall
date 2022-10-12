<?
	//='+code+'&content_type='+content_type
	switch ($_GET[code]) {		
		case 'main_block_content_ilark':
		case 'main_block_content_istorybook':
		case 'main_block_content_interpro':
		case "main_block_content_pixstory":
		case "main_block_content_lotteria":	
		
			include_once dirname(__FILE__) ."/../config/mainpage_content.php";	
			break;
		
		case 'main_block_01':
		case 'main_block_02':
		case 'main_block_03':
		case 'main_block_04':
		case 'main_block_05':
		case 'main_block_06':
		case 'main_block_07':
								
			//$_GET[block_code] = "main_block_01";
			if (! $_POST[block_code])
				$_POST[block_code] = $_GET[code];
			include_once dirname(__FILE__) ."/../goods/goods_main_block.php";	
			break;
			
		default:
			
			break;
	}
	
 
?>