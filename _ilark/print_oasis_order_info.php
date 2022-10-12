<?
/*
* @date : 20180709
* @author : chunter
* @brief : 오아시스 연동. 주문 내역 전송. 
* @desc : 2018년 자동견적 상품 전용
*/
?>
<? 
include "../lib/library.php";
header("Content-type: text/xml; charset=utf-8");

# 1. 주문상품코드 전달 여부 체크
if (!trim($_REQUEST[order_product_code])){
	$ret[success] = false;
	$ret[error] = "주문 상품 코드 누락";
	echo return_fail_xml($ret);
	exit;
}


//$_REQUEST[order_code]

//파일 정보 조회
$ret = FileInfo($_REQUEST[order_product_code]);

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<Photo version=\"1.0\">\n";
echo "\t<Delivery ordNum=\"" .$_REQUEST[order_code]. "\" ordPrdNum=\"" .$_REQUEST[order_product_code]. "\" ordDate=\"\"/>\n";
echo "\t<Download>\n";
echo $ret;
echo "\t</Download>\n";
echo "</Photo>\n";
exit;

function return_fail_xml($data){
    $result = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $result .= "<result>\n";
    if ($data[success] == FALSE)
    {
        $result .= "\t<response_id response_id=\"fail\">$data[error]</response_id>";
    }
    $result .= "</result>\n";
	
	return $result;	
}


function FileInfo($order_product_code)
{
	global $db;	
	$biSb = "";
		
	$query = "select est_goodsnm from exm_ord_item where storageid='$order_product_code';";
	list($est_order_type) = $db->fetch($query, 1);	
	//debug($est_order_type);	
	
	//검수 확인 주문인경우
	if ($est_order_type == "FILEUPLOAD")
	{
		$query = "select * from md_print_upload_file where regist_flag = 'Y' and storageid='$order_product_code';";
		//debug($query);
		$fileList = $db->listArray($query);
		//debug($fileList);
		if (is_array($fileList))
		{
			$pageFileList = array();
			//표지와 내지  master_option_key 처리한다.
			foreach ($fileList as $key => $value) 
			{
				$machineTag = "";
				if ($value['machine_id'] && $value['uniquekey'])
					$machineTag = "machine_id='{$value['machine_id']}' uniquekey='{$value['uniquekey']}'";
									
				if ($value[file_type] == "outside_0")
				{					
					$biSb = "<file type=\"cover\" filename=\"{$value[upload_file_name]}\" $machineTag master_option_key=\"router_cover_option\"><![CDATA[{$value[server_path]}]]></file>";
				} else if (substr($value[file_type], 0, 7) == "inside_"){					
					$subPageIndex = intval(substr($value[file_type], 7));			
					$pageFileList[$subPageIndex] = "<file type=\"page\" filename=\"{$value[upload_file_name]}\" $machineTag master_option_key=\"router_page_option\"><![CDATA[{$value[server_path]}]]></file>";					
				} else if (substr($value[file_type], 0, 7) == "inpage_"){
					$subPageIndex = intval(substr($value[file_type], 7));
					$pageFileList[$subPageIndex] = "<file type=\"page\" filename=\"{$value[upload_file_name]}\" $machineTag master_option_key=\"router_page_option\"><![CDATA[{$value[server_path]}]]></file>";				
				} else{
					$pageFileList[] = "<file type=\"page\" filename=\"{$value[upload_file_name]}\" $machineTag master_option_key=\"router_page_option\"><![CDATA[{$value[server_path]}]]></file>";					
				}
			}
								
			//print_r($pageFileList);
			ksort($pageFileList);
			$pageIndex = 1;
			foreach ($pageFileList as $key => $value) {
				$fileInfoStr = str_replace("router_page_option", "router_page_option".$pageIndex, $value);
				$biSb .= "\t\t\t\t$fileInfoStr\n";
				$pageIndex++;
			}		
			
			
		}
				
	//다이렉트 고객 주문 처리.	
	} else if ($est_order_type == "DIRECTUPLOAD") {
		$query = "select * from exm_ord_upload_file where upload_order_product_code='$order_product_code';";
		$fileList = $db->listArray($query);	
		
		if (is_array($fileList))
		{
			$pageIndex = 1;
			foreach ($fileList as $key => $value) {
				$biSb .= "\t\t\t\t<file type=\"page\" filename=\"{$value[upload_file_name]}\" master_option_key=\"router_page_option$pageIndex\"><![CDATA[{$value[server_path]}]]></file>\n";				
				$pageIndex++;
			}
		}		
	}
			
	//router_cover_option
	//router_page_option1
		
	//debug($biSb);
	//master_option_key="router_page_option1">
	
	return $biSb;
}	


?>