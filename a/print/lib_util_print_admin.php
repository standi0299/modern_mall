<?
/*
* @date : 20190320
* @author : kdk
* @brief : 견적 상품 추가 현수막(Placard),실사출력(RealPrint)
* @desc : 현수막 (PR01),실사출력(PR02)
*/

/*
 * @date : 20180404
 * @author : kdk
 * @brief : 인터프로 자동견적 관리자용 함수.
 * @desc :
 */

//지류 정보 조회.
function GetPaper($addWhere = '')
{
    $result;
    
    $m_print = new M_print();
    
    $add_where = "and opt_group = 'paper'";
    if ($addWhere) {
        $add_where .= " ".$addWhere;
    }
    
    //지류 정보 조회.
    $result = $m_print->getOptionItemsList($add_where, "order by opt_prefix,id,opt_key");

    return $result;
}

//규격 정보 조회.
function GetSize($addWhere = '')
{
    $result;
    
    $m_print = new M_print();
    
    //규격 정보 조회.
    $result = $m_print->getOptionSizeList($addWhere);

    return $result;
}

//옵션 정보 조회.
function GetItem($opt_group, $addWhere = '')
{
    $result;
    
    $m_print = new M_print();
    
    //옵션 정보 조회.
    $result = $m_print->getOptionItemsList("and opt_group = '$opt_group' $addWhere");

    return $result;
}

//일반 인쇄 설정 명함,스티커 정보 조회.
function GetNormalItem($opt_mode, $goodsno = '')
{
    global $print_goods_item;
    
    $m_print = new M_print();
    
    $result;
    
    //debug($print_goods_item);
    $data = $print_goods_item[$opt_mode];

    if ($goodsno) {
        //debug("goodsno : ".$goodsno);
        if (file_exists("../../data/print/goods_items/".$goodsno.".php"))
        {
            include_once "../../data/print/goods_items/".$goodsno.".php";
            $print_goods_item[$goodsno] = json_decode($goods_item, 1);
            //debug($print_goods_item[$param_goods_no]);
            //debug(json_decode($goods_item, 1));
        }
        //debug($print_goods_item[$goodsno]);
        $data = $print_goods_item[$goodsno];
    }

    if ($data) {
        //규격 ,아이템 정보 조회.
        foreach ($data as $key => $val) {
            //debug($key);
            $opt_sub_items = "";
            
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    //debug($v);
                    $Ukey = strtoupper($key);           //대문자 key
                    
                    if ($key == "paper") {
                        $s = $k;
                        
                        //사용자가 선택한 gram.
                        //$opt_sub_items = $v[0];
                        
                        if (is_array($v)) $v = implode("|", $v);
                        //debug($v);                        
                        $opt_sub_items = $v;
                    }
                                    
                    //인쇄 항목의 경우 칼라, 흑백인디고, 흑백누비라, 별색 만 가격 설정한다. 
                    //단면, 양면,  흑백, 칼라 모든 설정값을 가져오도록 수정          20180625        chunter (0622 김기웅발신 메일 요청)
                    else if ($key == "print")
                    {
                        //echo $v."<BR>";
                        $opt_sub_items = "";
                        if (substr($v, 0, 2) == "ET")
                            $s = $v;
                        else if(substr($v, 2, 2) == "1" || substr($v, 2, 2) == "2" || substr($v, 2, 2) == "3" || substr($v, 2, 2) == "6" || substr($v, 2, 2) == "7" || substr($v, 2, 2) == "8")
                            $s = $v;
                        else 
                            continue;
                    }
                     
                    //일반 후가공들은 있는 경우만 가격 설정한다. 
                    else if ($Ukey == "SC" || $Ukey == "DOMOO" || $Ukey == "CUTTING" || $Ukey == "INSTANT" || $Ukey == "BARCODE" || $Ukey == "NUMBER" || $Ukey == "STAND" || $Ukey == "DANGLE" || $Ukey == "TAPE" || $Ukey == "ADDRESS" || $Ukey == "PRESS" || $Ukey == "UVC")
                    {                                                                   
                        if(substr($v, 2, 2) == "2")
                            $s = $v;
                        else 
                            continue;
                    }
										
					//박은 없음 제외			20180820		chunter
					else if ($Ukey == "FOIL")
                    {                                                                   
                        if(substr($v, 2, 2) == "1")
                        	continue;
                        else
					       $s = $v;
                    }                                   
                                    
                    else $s = $v;

                    //사용자 사이즈 직접입력.
                    if ($s == "USER") continue;
                   
                    //아이템 정보 조회.    
                    $data = $m_print->getOptionItemsList("and opt_group = '$key' and concat(opt_prefix,opt_key) = '$s'");
                    
                    //사용자가 선택한 gram만 사용함.
                    if ($opt_sub_items) $data[0]['opt_sub_items'] = $opt_sub_items;
                    
                    $result[$key][] = $data[0];
                }                
            }
        }
    }

    return $result;
}

//옵셋 인쇄 설정 정보 조회.
function GetOpsetSize($opt_mode)
{
		//규격 정보 설정.
    $size = array(
    	'A1' => array('ID' => "", 'opt_key' => "1", 'opt_value' => "A1 (국전지)", 'opt_prefix' => "A", 'opt_order' => "", 'opt_use' => "", 'opt_desc' => "", 'opt_group' => "SIZE", 'opt_sub_items' => ""), 
      'B2' => array('ID' => "", 'opt_key' => "2", 'opt_value' => "B2 (46전지 2절)", 'opt_prefix' => "B", 'opt_order' => "", 'opt_use' => "", 'opt_desc' => "", 'opt_group' => "SIZE", 'opt_sub_items' => "")
  	);            
    $result = $size;
    return $result;
}

//옵셋 인쇄 설정 정보 조회.
//사용하지 않음 			20180530		chunter
function GetOpsetItem($opt_mode)
{
    global $print_goods_item;
    
    $result;
    
    $m_print = new M_print();
    $data = $print_goods_item[$opt_mode];
    
    if ($data) {
        //아이템 정보 조회.
        foreach ($data as $key => $val) {
            if($key != "print") continue;            
            //debug($val);
            foreach ($val as $k => $v) {
                //debug($v);
               
                //아이템 정보 조회.    
                $data = $m_print->getOptionItemsList("and opt_group = '$key' and concat(opt_prefix,opt_key) = '$v'");
                //debug($data);
                
                $result[] = $data[0];
            }
        }
    }
    
    return $result;
}

function GetItemTitleDataArr($size_data, $item_data)
{
    $result = "";
    
    if($size_data && $item_data) {
        foreach ($item_data as $k => $v) {
            $key = $v[opt_prefix].$v[opt_key];
            $result[$key] = array("key" => $v[opt_prefix].$v[opt_key], "value" => $v[opt_value]);
    
            foreach ($size_data as $k2 => $v2) {
                $result[$key][size][] = array("key" => $v2[opt_prefix].$v2[opt_key], "value" => $v2[opt_value]);
            }
        }
    }
    
    return $result;
}

function GetItemPriceDataArr($opt_mode, $size_data, $item_data)
{
    global $cid;

    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_". $opt_mode .".php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_". $opt_mode .".php";
        $file_data = json_decode(${"r_ipro_".$opt_mode}, 1);
				
		if (is_array($file_data))
		{
	        foreach ($file_data as $k => $v) {
	            //debug($k);
	            //debug($v);
	            foreach ($v as $k1 => $v1) {
	                //debug($k1);
	                //debug($v1);
	                foreach ($v1 as $k2 => $v2) {
	                    $result[$k1][$k2][$k] = $v2;
	                }
	            }
	        }
		}
    }
    else {
        if($size_data && $item_data) {
            foreach ($item_data as $k => $v) {
                //debug($k);
                //debug($v);
                $key = $v[opt_prefix].$v[opt_key];
                foreach ($size_data as $k2 => $v2) {
                    $result[1][$key][$v2[opt_prefix].$v2[opt_key]] = "0";
                }
            }
        }
    }

    return $result;
}    

function GetItemTitleDataTag($data, $opt_mode = 'default')
{
	global $r_ipro_opt_mode_paper_unit;
    $result;
		
		//단위 타이틀		
		$unit_title = $r_ipro_opt_mode_paper_unit[$opt_mode];
		if (!$unit_title) $unit_title = "수량(장)";

    if ($data) {
        $tableTitle = "<tr>\r\n";
        $tableTitle .= "<th rowspan='2'>$unit_title</th>\r\n";
        $tableSize = "<tr>\r\n";
        foreach ($data as $key => $val) {
            $tableTitle .= "<th colspan='". count($val[size]) ."'>". $val[value] ."<br/>[". $key ."]</th>\r\n";
            foreach ($val[size] as $k => $v) {
                $tableSize .= "<th>". $v[value] ."<br/>[". $key ."_". $v[key] ."]</th>\r\n";
            }
        }
        $tableTitle .= "</tr>\r\n";
        $tableSize .= "</tr>\r\n";
    }

    $result = $tableTitle."\r\n".$tableSize;

    return $result;
}

function GetItemPriceDataTag($data)
{
    $result;
    
    if ($data) {
        $row = 1;

        foreach ($data as $cnt => $val) {    
            $result .= "<tr>\r\n";
            $result .= "<td align='center'><input type='text' name='cnt_".$row."' value='".$cnt."'></td>\r\n";

            foreach ($val as $k1 => $v1) {
                //debug($k1);
                //debug($v1);
                foreach ($v1 as $k2 => $v2) {
                    $result .= "<td align='center'><input type='text' name='".$row."_".$k1."_".$k2."' value='".$v2."'></td>\r\n";
                }
            }
            $row++;
            $result .= "</tr>\r\n";
        }
    }
    
    return $result;
}

function GetItemPriceDataXlsTag($file)
{
    $result = "";

    $sheet = 0;
    $ext = substr(strrchr($file, "."), 1);
    $ext = strtolower($ext);

    if ($ext == "xlsx") {
        // Reader Excel 2007 file
        include "../../lib/PHPExcel.php";
        $objReader = PHPExcel_IOFactory::createReader("Excel2007");
        $objReader -> setReadDataOnly(true);
        $objReader -> canRead($file);
        //$objReader->setReadFilter( new MyReadFilter() );

        $objPHPExcel = $objReader -> load($file);
        $objPHPExcel -> setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel -> getActiveSheet();
        $xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);

        //debug('count($xlsData): '.count($xlsData));

        foreach ($xlsData as $key => $value) {

            if ($key == 2) {
                foreach ($value as $k => $v) {
                    $inner[] = $v;
                }
                $xlsPrice[0] = $inner;
            }            
            
            if ($key > 2) {
                $outs_inner = array();
                foreach ($value as $k => $v) {
                    $v = ($v == '') ? '' : addslashes(htmlentities($v));
                    //$v = ($v == '') ? '' : addslashes($v);
                    
                    //if ($v !== '') $outs_inner[] = $v;
                    $outs_inner[] = $v;
                }
                
                //debug($outs_inner);
                $xlsPrice[] = $outs_inner;
            }            
        }
    } else {
        // Reader Excel 2003 file
        //$xlsData = new Spreadsheet_Excel_Reader("unit_price.xls");
        $xlsData = new Spreadsheet_Excel_Reader($file);
        
        for ($row = 2; $row <= $xlsData -> rowcount($sheet); $row++) {
            $outs_inner = array();
            for ($col = 1; $col <= $xlsData -> colcount($sheet); $col++) {
                if (!$xlsData -> sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
                    $val = $xlsData -> val($row, $col, $sheet);
                    $val = ($val == '') ? '' : addslashes(htmlentities($val));

                    //$outs_inner[] = "{$val}"; # Quote or not? #
                    $outs_inner[] = $val;
                }
            }
            $xlsPrice[] = $outs_inner;
        }
    }

    foreach ($xlsPrice[0] as $key => $val) {
        $val = str_replace("[", "", $val);
        $val = str_replace("]", "", $val);
        $val = str_replace("\n", "|", $val);
        if (strpos($val, "|") !== false) {
            $postArr = explode("|", $val); //split("|", $val);
            if(count($postArr) < 2) continue;
            $xlsPrice[0][$key] = $postArr[1];
        }
        
    }
    //debug($xlsPrice[0]);
    
    $xlsRow = 1;
    foreach ($xlsPrice as $key => $val) {
        if($key > 0) {
            //debug($val);
            $result .= "<tr>\r\n";
            foreach ($val as $k => $v) {
                //debug($v);
                if($k==0) {
                    $result .= "<td align='center'><input type='text' name='cnt_".$xlsRow."' value='".$v."'></td>\r\n";
                }   
                else {
                	$xlsPrice[0][$k] = str_replace(".", "#", $xlsPrice[0][$k]);			//"." 을 "#" 으로 변환			20180816	chunter
                    $result .= "<td align='center'><input type='text' name='".$xlsRow."_".$xlsPrice[0][$k]."' value='".$v."'></td>\r\n";
                }
            }
            $xlsRow++;
            $result .= "</tr>\r\n";
        }
    }
    
    return $result;
}

function GetItemDcDataArr($opt_mode, $size_data, $item_data)
{
    global $cid;
    
    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_". $opt_mode .".php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_". $opt_mode .".php";
        $file_data = json_decode(${"r_ipro_".$opt_mode}, 1);
        //debug($file_data);
    
        if($size_data && $item_data) {
            foreach ($item_data as $k => $v) {
                //debug($k);
                //debug($v);
                $key = $v[opt_prefix].$v[opt_key];
                foreach ($size_data as $k2 => $v2) {
                    $result[dc][$key][$v2[opt_prefix].$v2[opt_key]] = $file_data[$v2[opt_prefix].$v2[opt_key]][$key];
                }
            }
        }
    }
    else {
        if($size_data && $item_data) {
            foreach ($item_data as $k => $v) {
                //debug($k);
                //debug($v);
                $key = $v[opt_prefix].$v[opt_key];
                foreach ($size_data as $k2 => $v2) {
                    $result[dc][$key][$v2[opt_prefix].$v2[opt_key]] = "0";
                }
            }
        }
    } 

    return $result;
}    

function GetItemDcTitleDataTag($data)
{
    $result;

    if ($data) {
        $tableTitle = "<tr>\r\n";
        $tableTitle .= "<th rowspan='2'></th>\r\n";
        $tableSize = "<tr>\r\n";
        foreach ($data as $key => $val) {
            $tableTitle .= "<th colspan='". count($val[size]) ."'>". $val[value] ."<br/>[". $key ."]</th>\r\n";
            foreach ($val[size] as $k => $v) {
                $tableSize .= "<th>". $v[value] ."<br/>[". $key ."_". $v[key] ."]</th>\r\n";
            }
        }
        $tableTitle .= "</tr>\r\n";
        $tableSize .= "</tr>\r\n";
    }

    $result = $tableTitle."\r\n".$tableSize;
    
    return $result;
}

function GetItemDcDataTag($data)
{
    $result;
    
    if ($data) {
        $result .= "<tr>\r\n";
        $result .= "<td align='center'>할인율</td>\r\n";
        
        foreach ($data as $key => $val) {    
            foreach ($val as $k1 => $v1) {
                //debug($k1);
                //debug($v1);
                foreach ($v1 as $k2 => $v2) {
                    $result .= "<td align='center'><input type='text' name='".$k1."_".$k2."' value='".$v2."'></td>\r\n";
                }
            }
        }
        $result .= "</tr>\r\n";  
    }
    
    return $result;
}

function GetItemDcDataXlsTag($file)
{
    $result = "";


    return $result;
}

function GetNormalItemTitleDataArr($data)
{
    $result = "";

    if ($data) {
        foreach ($data as $key => $val) {
            if ($key == "size") continue;
            
            foreach ($val as $k => $v) {
                $itemKey = $v[opt_prefix].$v[opt_key];
                $itemVal = $v[opt_value];
                
                //지류 opt_sub_items이 있을 경우
                //debug($v[opt_sub_items]);
                if ($v[opt_sub_items]) {
                    $items = $v[opt_sub_items];
                    if (strpos($items, "|") !== false) {
                        $postArr = explode("|", $items);
                        foreach ($postArr as $k3 => $v3) {
                            if (strpos($v3, "-") !== false) {
                                $vArr = explode("-", $v3);
                                $sub = $vArr[0];
                            }
                            else $sub = $v3;
                            
                            $result[$itemKey][$sub] = array("key" => $itemKey, "value" => $itemVal);
                    
                            foreach ($data[size] as $k2 => $v2) {
                                $result[$itemKey][$sub][size][] = array("key" => $v2[opt_prefix].$v2[opt_key], "value" => $v2[opt_value]);
                            }
                        }
                    }
                    else {
                        if (strpos($items, "-") !== false) {
                            $vArr = explode("-", $items);
                            $sub = ($vArr[0]=="" ? "-" : $vArr[0]);
                        }
                        else $sub = $items;
                        
                        $result[$itemKey][$sub] = array("key" => $itemKey, "value" => $itemVal);

                        foreach ($data[size] as $k2 => $v2) {
                            $result[$itemKey][$sub][size][] = array("key" => $v2[opt_prefix].$v2[opt_key], "value" => $v2[opt_value]);
                        }                        
                    }                        
                }
                else {
                    $result[$itemKey][0] = array("key" => $itemKey, "value" => $itemVal);
            
                    foreach ($data[size] as $k2 => $v2) {
                        $result[$itemKey][0][size][] = array("key" => $v2[opt_prefix].$v2[opt_key], "value" => $v2[opt_value]);
                    }                    
                }
            }        
        }
    }
    
    return $result;
}

function GetNormalItemPriceDataArr($opt_mode, $data)
{
    global $cid;
    
    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_". $opt_mode .".php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_". $opt_mode .".php";
        $file_data = json_decode(${"r_ipro_".$opt_mode}, 1);
        //debug($file_data);
        foreach ($file_data as $k => $v) {
            //debug($k);
            //debug($v);
            
            //사용자 사이즈 직접입력일 경우 값이 없음.
            if ($k == "") continue;
            
            foreach ($v as $k1 => $v1) {
                //debug($k1);
                //debug($v1);
                foreach ($v1 as $k2 => $v2) {
                    if (is_array($v2)) {
                        foreach ($v2 as $k3 => $v3) {
                            $result[$k1][$k2][$k3][$k] = $v3;
                        }
                    }
                    else {
                        $result[$k1][$k2][0][$k] = $v2;
                    }
                }
            }
        }
    }
    else {
        if($data) {
            //debug($data);
            foreach ($data as $key => $val) {
                if ($key == "size") continue;
                
                foreach ($val as $k => $v) {
                    $itemKey = $v[opt_prefix].$v[opt_key];

                    foreach ($data[size] as $k2 => $v2) {
                        //지류 opt_sub_items이 있을 경우
                        //debug($v[opt_sub_items]);
                        if ($v[opt_sub_items]) {
                            $items = $v[opt_sub_items];
                            if (strpos($items, "|") !== false) {
                                $postArr = explode("|", $items);
                                foreach ($postArr as $k3 => $v3) {
                                    if (strpos($v3, "-") !== false) {
                                        $vArr = explode("-", $v3);
                                        $sub = $vArr[0];
                                    }
                                    else $sub = $v3;
                                    
                                    $result[1][$itemKey][$sub][$v2[opt_prefix].$v2[opt_key]] = "0";
                                }
                            }
                            else {
                                if (strpos($items, "-") !== false) {
                                    $vArr = explode("-", $items);
                                    $sub = ($vArr[0]=="" ? "-" : $vArr[0]);
                                }
                                else $sub = $items;
                                
                                $result[1][$itemKey][$sub][$v2[opt_prefix].$v2[opt_key]] = "0";
                            }                        
                        }
                        else {
                            $result[1][$itemKey][0][$v2[opt_prefix].$v2[opt_key]] = "0";
                        }
                    }            
                }        
            }            
        }    
    } 

    /*if ($data) {
        foreach ($data as $key => $val) {
            if ($key == "size") continue;

            foreach ($val as $k => $v) {
                $itemKey = $v[opt_prefix].$v[opt_key];
                foreach ($data[size] as $k2 => $v2) {
                    $result[1][$itemKey][$v2[opt_prefix].$v2[opt_key]] = "0";
                }            
            }        
        }
    }*/
    
    return $result;
}

function GetNormalItemTitleDataTag($data, $opt_mode = 'default')
{
	global $r_ipro_opt_mode_paper_unit;
 	$result;
		
		//단위 타이틀		
		$unit_title = $r_ipro_opt_mode_paper_unit[$opt_mode];
		if (!$unit_title) $unit_title = "수량(장)";

    if ($data) {
        $tableTitle = "<tr>\r\n";
        $tableTitle .= "<th rowspan='2'>$unit_title</th>\r\n";
        $tableSize = "<tr>\r\n";
        foreach ($data as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
            	$optGroupName = getOptionKeyToOptionName($k1);
							if ($optGroupName) $optGroupName = "[$optGroupName]";
                $tableTitle .= "<th colspan='". count($v2[size]) ."'>". $v2[value].$optGroupName."<br/>[". $k1 ."]". ($k2=="0" ? "" : "<br/>$k2") ."</th>\r\n";
                foreach ($v2[size] as $k3 => $v3) {
                    $tableSize .= "<th>". $v3[value] ."<br/>[". $k1 ."_". $k2 ."_". $v3[key] ."]</th>\r\n";
                }
            }
        }
        $tableTitle .= "</tr>\r\n";
        $tableSize .= "</tr>\r\n";
    }

    $result = $tableTitle."\r\n".$tableSize;
    
    return $result;
}

//옵션 key로 해당 옵션 그룹 명칭 구하기				20180820		chunter
function getOptionKeyToOptionName($opt_key)
{
	global $r_opt_prefix_group;
	
	$opt_key_sub = substr($opt_key, 0, 3);
	if ($r_opt_prefix_group[$opt_key_sub])
		return $r_opt_prefix_group[$opt_key_sub];
	else {
		$opt_key_sub = substr($opt_key, 0, 2);
		if ($r_opt_prefix_group[$opt_key_sub])
			return $r_opt_prefix_group[$opt_key_sub];
		else 
			return "";
	}
}

function GetNormalItemPriceDataTag($data)
{
    $result;
    
    if ($data) {
        $row = 1;
        foreach ($data as $cnt => $val) {    
            $result .= "<tr>\r\n";
            $result .= "<td align='center'><input type='text' name='cnt_".$row."' value='".$cnt."'></td>\r\n";
            
            foreach ($val as $k1 => $v1) {
                //debug($k1);
                //debug($v1);
                foreach ($v1 as $k2 => $v2) {
                    foreach ($v2 as $k3 => $v3) {
                    	$k2 = str_replace(".", "#",$k2);
                        $result .= "<td align='center'><input type='text' name='".$row."_".$k1."_".$k2."_".$k3."' value='".$v3."'></td>\r\n";
                    }                    
                }
            }
            $row++;
            $result .= "</tr>\r\n";
        }   
    }
    
    return $result;
}

function GetPaperPriceDataArr($data)
{
    global $cid;
    
    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_paper.php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_paper.php";
        $file_data = json_decode($r_ipro_paper, 1);
        //debug($file_data);
        $result = $file_data;
    }
    else {
        if($data) {
            foreach ($data as $k => $v) {
                //debug($k);
                //debug($v);
                $ItemKey = $v[opt_prefix].$v[opt_key];
                $Items = $v[opt_sub_items];
                //debug($Items);
                
                //if($ItemKey != "PPE01") continue;
                
                $paperItemsDataArr = array("name" => $v[opt_value], "group" => $v[opt_desc]);
                
                if (strpos($Items, "|") !== false) {
                    $postArr = explode("|", $Items);
                    foreach ($postArr as $key => $val) {
                        if (strpos($val, "-") !== false) {
                            $vArr = explode("-", $val);
                            $paperItemsDataArr[paper][$vArr[0]] = array("width" => $vArr[1], "B2"=>"0", "A3" => "0", "OB2" => "0", "OA1" => "0");
                        }
                        else {
                            $paperItemsDataArr[paper][$val] = array("width" => "", "B2"=>"0", "A3" => "0", "OB2" => "0", "OA1" => "0");
                        }
                    }
                }
                else {
                    if (strpos($Items, "-") !== false) {
                        $vArr = explode("-", $Items);
                        $paperItemsDataArr[paper][($vArr[0]=="" ? "-" : $vArr[0])] = array("width" => $vArr[1], "B2"=>"0", "A3" => "0", "OB2" => "0", "OA1" => "0");
                    }
                    else {
                        $paperItemsDataArr[paper][$Items] = array("width" => "", "B2"=>"0", "A3" => "0", "OB2" => "0", "OA1" => "0");
                    }
                }
    
                $result[$ItemKey] = $paperItemsDataArr;
            }
        }
    }

    return $result;
}

function GetPaperPriceDataTag($data)
{
    $result;
    
    if ($data) {
        foreach ($data as $key => $val) {
            if(count($val[paper]) == 1) {  
                $result .= "<tr>\r\n";
                $result .= "<td align='center'>$val[group]</td>\r\n";
                $result .= "<td align='center'>$val[name]</td>\r\n";
            }
            
            foreach ($val[paper] as $k1 => $v1) {
                if(count($val[paper])>1) {
                    $result .= "<tr>\r\n";
                    $result .= "<td align='center'>$val[group]</td>\r\n";
                    $result .= "<td align='center'>$val[name]</td>\r\n";
                }
                
                $result .= "<td align='center'>$k1</td>\r\n";
                $result .= "<td align='center'>$v1[width]</td>\r\n";
                
                //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk
                $k1 = str_replace(".", "#", $k1);

                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_B2' value='".$v1[B2]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_A3' value='".$v1[A3]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_OB2' value='".$v1[OB2]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_OA1' value='".$v1[OA1]."'></td>\r\n";
    
                if(count($val[paper])>1)
                    $result .= "</tr>\r\n";
            }
            
            if(count($val[paper]) == 1)
                $result .= "</tr>\r\n";
        }   
    }
    
    return $result;
}

function GetPaperPriceDataXlsTag($file)
{
    $result = "";

    $sheet = 0;
    $ext = substr(strrchr($file, "."), 1);
    $ext = strtolower($ext);

    if ($ext == "xlsx") {
        // Reader Excel 2007 file
        include "../../lib/PHPExcel.php";
        $objReader = PHPExcel_IOFactory::createReader("Excel2007");
        $objReader -> setReadDataOnly(true);
        $objReader -> canRead($file);
        //$objReader->setReadFilter( new MyReadFilter() );
        
        $objPHPExcel = $objReader -> load($file);

        $objPHPExcel -> setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel -> getActiveSheet();
        $xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
        //debug($xlsData);
        //debug('count($xlsData): '.count($xlsData));
        foreach ($xlsData as $key => $value) {
            if ($key >= 2) {
                $outs_inner = array();
                foreach ($value as $k => $v) {
                    //$v = ($v == '') ? '' : addslashes(htmlentities($v));
                    $v = ($v === '') ? '' : addslashes($v);
                    
                    //if ($v !== '') $outs_inner[] = $v;
                    $outs_inner[] = $v;
                }
                
                //debug($outs_inner);
                $xlsOptionPrice[] = $outs_inner;
            }
        }        
    } else {
        // Reader Excel 2003 file
        //$xlsData = new Spreadsheet_Excel_Reader("unit_price.xls");
        $xlsData = new Spreadsheet_Excel_Reader($excelImportFileName);

        for ($row = 2; $row <= $xlsData -> rowcount($sheet); $row++) {
            $outs_inner = array();
            for ($col = 1; $col <= $xlsData -> colcount($sheet); $col++) {
                if (!$xlsData -> sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
                    $val = $xlsData -> val($row, $col, $sheet);
                    //$val = ($val == '') ? '' : addslashes(htmlentities($val));
                    $val = ($val === '') ? '' : addslashes($val);

                    //$outs_inner[] = "{$val}"; # Quote or not? #
                    $outs_inner[] = $val;
                }
            }
            //$xlsPrice[] = implode(',', $outs_inner);
            $xlsOptionPrice[] = $outs_inner;
        }
    }
    //debug($xlsOptionPrice);

    $xlsRow = 1;
    foreach ($xlsOptionPrice as $itemKey => $itemValue) {
        if($itemKey > 0) {
            //debug($itemValue);
            
            $val = $itemValue[0];
            
            $val = str_replace("[", "", $val);
            $val = str_replace("]", "", $val);
            $val = str_replace("\n", "|", $val);

            if (strpos($val, "|") !== false) {
                $postArr = explode("|", $val); //split("|", $val);
                $group = $postArr[0];
                $key = $postArr[1];
            }            
            
            $result .= "<tr>\r\n";
            $result .= "<td align='center'>". htmlspecialchars($group, ENT_QUOTES) ."</td>\r\n";
            $result .= "<td align='center'>$itemValue[1]</td>\r\n";
        
            $result .= "<td align='center'>$itemValue[2]</td>\r\n";
            $result .= "<td align='center'>$itemValue[3]</td>\r\n";
            
            //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk 
            $key = str_replace(".", "#", $key);

            $itemValue[4] = ($itemValue[4] === '') ? '' : $itemValue[4];
            $itemValue[5] = ($itemValue[5] === '') ? '' : $itemValue[5];
            $itemValue[6] = ($itemValue[6] === '') ? '' : $itemValue[6];
            $itemValue[7] = ($itemValue[7] === '') ? '' : $itemValue[7];
            
            $result .= "<td align='center'><input type='text' name='".$key."_B2' value='".$itemValue[4]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_A3' value='".$itemValue[5]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_OB2' value='".$itemValue[6]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_OA1' value='".$itemValue[7]."'></td>\r\n";
            $result .= "</tr>\r\n";
        }
    }
    
    return $result;
}

function GetPaperDcDataArr($data)
{
    global $cid;
    
    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_paper_dc.php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_paper_dc.php";
        $r_ipro_paper_dc = json_decode($r_ipro_paper_dc, 1);
        //debug($r_ipro_paper_dc);
        
        $file_data = json_decode($r_ipro_paper_dc_data, 1);
        //debug($file_data);
        $result = $file_data;
    }
    else {
        if($data) {
            foreach ($data as $k => $v) {
                //debug($k);
                //debug($v);
                $ItemKey = $v[opt_prefix].$v[opt_key];
                
                $paperItemsDataArr = array("dc" => "", "name" => $v[opt_value], "group" => $v[opt_desc]);
                $result[$ItemKey] = $paperItemsDataArr;
            }
        }
    }

    return $result;
}

function GetPaperDcDataTag($data)
{
    $result = "";
    
    if ($data) {
        foreach ($data as $key => $val) {
            $result .= "<tr>\r\n";
            $result .= "<td align='center'>$val[group]</td>\r\n";
            $result .= "<td align='center'>$val[name]</td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."' value='".$val[dc]."'></td>\r\n";
            $result .= "</tr>\r\n";
        }   
    }
    
    return $result;
}

function GetPaperDcDataXlsTag($file)
{
    $result = "";

    return $result;
}

function GetCtpOpsetDataArr()
{
    global $cid;
    
    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_ctp_opset.php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_ctp_opset.php";
        $file_data = json_decode($r_ipro_ctp_opset, 1);
        //debug($file_data);
        $result = $file_data;
    }
    else {
        $result[1] = array('cnt' => "0", 'val' => "0");
    }

    return $result;
}

function GetCtpOpsetDataTag($data)
{
    $result;
    
    if ($data) {
        $row = 1;

        foreach ($data as $key => $val) {    
            $result .= "<tr>\r\n";
            $result .= "<td align='center'><input type='text' name='cnt_".$row."' value='".$val[cnt]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='val_".$row."' value='".$val[val]."'></td>\r\n";
            $row++;
            $result .= "</tr>\r\n";
        }
    }
    
    return $result;
}

function GetCtpOpsetDataXlsTag($file)
{
    $result = "";

    $sheet = 0;
    $ext = substr(strrchr($file, "."), 1);
    $ext = strtolower($ext);

    if ($ext == "xlsx") {
        // Reader Excel 2007 file
        include "../../lib/PHPExcel.php";
        $objReader = PHPExcel_IOFactory::createReader("Excel2007");
        $objReader -> setReadDataOnly(true);
        $objReader -> canRead($file);
        //$objReader->setReadFilter( new MyReadFilter() );

        $objPHPExcel = $objReader -> load($file);
        $objPHPExcel -> setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel -> getActiveSheet();
        $xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);

        //debug('count($xlsData): '.count($xlsData));

        foreach ($xlsData as $key => $value) {

            if ($key == 2) {
                foreach ($value as $k => $v) {
                    $inner[] = $v;
                }
                $xlsPrice[0] = $inner;
            }            
            
            if ($key > 2) {
                $outs_inner = array();
                foreach ($value as $k => $v) {
                    $v = ($v == '') ? '' : addslashes(htmlentities($v));
                    //$v = ($v == '') ? '' : addslashes($v);
                    
                    //if ($v !== '') $outs_inner[] = $v;
                    $outs_inner[] = $v;
                }
                
                //debug($outs_inner);
                $xlsPrice[] = $outs_inner;
            }            
        }
    } else {
        // Reader Excel 2003 file
        //$xlsData = new Spreadsheet_Excel_Reader("unit_price.xls");
        $xlsData = new Spreadsheet_Excel_Reader($file);
        
        for ($row = 2; $row <= $xlsData -> rowcount($sheet); $row++) {
            $outs_inner = array();
            for ($col = 1; $col <= $xlsData -> colcount($sheet); $col++) {
                if (!$xlsData -> sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
                    $val = $xlsData -> val($row, $col, $sheet);
                    $val = ($val == '') ? '' : addslashes(htmlentities($val));

                    //$outs_inner[] = "{$val}"; # Quote or not? #
                    $outs_inner[] = $val;
                }
            }
            $xlsPrice[] = $outs_inner;
        }
    }
    //debug($xlsPrice);

    $xlsRow = 1;
    foreach ($xlsPrice as $key => $val) {
        //debug($val);
        $result .= "<tr>\r\n";
        
        $result .= "<td align='center'><input type='text' name='cnt_".$key."' value='".$val[0]."'></td>\r\n";
        $result .= "<td align='center'><input type='text' name='val_".$key."' value='".$val[1]."'></td>\r\n";
        
        /*foreach ($val as $k => $v) {
            //debug($v);
            if($k==0) {
                $result .= "<td align='center'><input type='text' name='cnt_".$xlsRow."' value='".$v."'></td>\r\n";
            }   
            else {
                $result .= "<td align='center'><input type='text' name='".$xlsRow."_".$xlsPrice[0][$k]."' value='".$v."'></td>\r\n";
            }
        }*/
        $xlsRow++;
        $result .= "</tr>\r\n";
    }
    //debug($result);
    //exit;
    return $result;
}

function getExcelColName($num) {
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($num / 26);
    if ($num2 > 0) {
        return getExcelColName($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}

function makePriceDataPHP($code, $data) {
    global $cid;
    
    $logfile = fopen("../../data/print/goods_items/". $cid ."_". $code .".php", "w" );
    fwrite( $logfile, "<?\n");

    foreach ($data as $key => $val) {
        fwrite( $logfile, "\$". $key ."='". $val ."';\n");
    }

    fwrite( $logfile, "?>");
    fclose( $logfile );
}

function bb($data)
{
    $result;
    
    if ($data) {
        
    }
    
    return $result;
}

function adminGetOptionItems($addWhere)
{
    global $db;

    $sql = "select * from md_print_option_items where opt_use = 'Y' $addWhere";
    //debug($sql);

    return $db->listArray($sql);
}

function adminGetOptionAllItems($opt_group, $opt_prefix = '', $addWhere = '')
{
	global $db;
	if ($opt_prefix) {
		$prefixArr = explode(",", $opt_prefix);
		$addWhere .= " and (opt_prefix = '' "; 
		foreach ($prefixArr as $value) {
			$addWhere .= " or opt_prefix ='$value' ";	
		}		
		$addWhere .= ")";
	}
	$sql = "select * from md_print_option_items where opt_use = 'Y' and  opt_group ='$opt_group' $addWhere order by opt_order";
	//debug($sql);
	$result = $db->listArray($sql);
	foreach ($result as $key => $value) {
		if ($value[opt_prefix])
			$opt_key = $value[opt_prefix].$value[opt_key];
		else 
			$opt_key = $value[opt_key];
		
		$optionData[$opt_key] = $value[opt_value];
	}
	return $optionData;
}

function adminGetOptionPaperItems($opt_prefix)
{
	global $db;
	$sql = "select * from md_print_option_items where opt_use = 'Y' and  opt_prefix ='$opt_prefix' order by opt_order";
	$result = $db->listArray($sql);
	foreach ($result as $key => $value) {
		if ($value[opt_prefix])
			$opt_key = $value[opt_prefix].$value[opt_key];
		else 
			$opt_key = $value[opt_key];
		
		$optionData[$opt_key] = array($value[opt_value], $value[opt_sub_items]);
	}
	return $optionData;
}

function adminMakeOptionCheckTag($optionData, $selData, $tagName, $addStype = '')
{
	$result = "";
	//$result = "<select>";	
	if (is_array($optionData))
	{
		foreach ($optionData as $key => $value) 
		{
			$selected = "";
			if (is_array($selData))
			{
				foreach ($selData as $selkey => $selvalue) {
					if ($key == $selvalue) $selected = " checked";
				}
			} 
			else 
			{
				if ($key == $selData) $selected = " checked";
			}
			//$result .= "<input type='checkbox' name='".$tagName."[]' value='$key' $selected $addStype >$value<BR>";
			$result .= "<input type='checkbox' name='".$tagName."[]' value='$key' $selected $addStype >$value<BR>";
		}
	}
	//$result .= "</select>";
	return $result;
}

function adminMakePaperOptionCheckTag($optionData, $selData, $tagName, $bHyphen = true)
{
	$result = "";
	//$result = "<select>";

	if (is_array($optionData))
	{
		foreach ($optionData as $key => $value) 
		{
            //debug($key);
            //debug($selData[$key]);
            //debug($optionData[$key]);
            
		    $selected = "";
            if (is_array($selData) && array_key_exists($key, $selData)) $selected = " checked";
            
			$result .= "<input type='checkbox' name='".$tagName."[]' value='$key' $selected>{$value[0]}<BR>";
			
			$sub_items = explode("|", $value[1]);
			if (is_array($sub_items))
			{
				foreach ($sub_items as $subvalue) 
				{
				    $subSelected = "";
					if ($subvalue == "-")
					{
					    if ($bHyphen)
						  $result .= "<input type='checkbox' name='".$tagName."_".$key."[]' value='0'>-";
					}
					else if ($subvalue)
					{					
						$paper_gram = explode("-", $subvalue);
						
                        if (is_array($selData[$key])) {
                            foreach ($selData[$key] as $selkey => $selvalue) {
                                if ($selvalue == $paper_gram[0]) $subSelected = " checked";
                            }
                        }
                        //debug($subSelected);
                        
						$result .= "<input type='checkbox' name='".$tagName."_".$key."[]' value='{$paper_gram[0]}' $subSelected>{$paper_gram[0]}";
					}
				}
				$result .= "<BR>";
			}	
		}
	}
	
	return $result;
}

// 옵셋 후가공.(규격사용안함)
function GetOpsetItemTitleDataArr($item_data)
{
    $result = "";

    if($item_data) {
        foreach ($item_data as $k => $v) {
            $key = $v[opt_prefix].$v[opt_key];
            $result[$key] = array("key" => $v[opt_prefix].$v[opt_key], "value" => $v[opt_value]);
        }
    }
    
    return $result;
}

function GetOpsetItemPriceDataArr($opt_mode, $item_data)
{
    global $cid;

    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_". $opt_mode .".php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_". $opt_mode .".php";
        $file_data = json_decode(${"r_ipro_".$opt_mode}, 1);

        if (is_array($file_data))
        {
            /*foreach ($file_data as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $result[$k1][$k] = $v1;
                }
            }*/
            $result = $file_data;
        }         
    }
    else {
        if($item_data) {
            foreach ($item_data as $k => $v) {
                //debug($k);
                //debug($v);
                $key = $v[opt_prefix].$v[opt_key];
                $result[1][$key] = "0";
            }
        }
    }

    return $result;
}    

function GetOpsetItemTitleDataTag($data, $opt_mode = 'default')
{
    global $r_ipro_opt_mode_paper_unit;
    $result;
        
    //단위 타이틀        
    $unit_title = $r_ipro_opt_mode_paper_unit[$opt_mode];
    if (!$unit_title) $unit_title = "연당";

    if ($data) {
        $tableTitle = "<tr>\r\n";
        $tableTitle .= "<th>$unit_title</th>\r\n";
        foreach ($data as $key => $val) {
            $tableTitle .= "<th>". $val[value] ."<br/>[". $key ."]</th>\r\n";
        }
        $tableTitle .= "</tr>\r\n";
    }

    $result = $tableTitle."\r\n";

    return $result;
}

function GetOpsetItemPriceDataTag($data)
{
    $result;

    if ($data) {
        $row = 1;

        foreach ($data as $cnt => $val) {    
            $result .= "<tr>\r\n";
            $result .= "<td align='center'><input type='text' name='cnt_".$row."' value='".$cnt."'></td>\r\n";

            foreach ($val as $k1 => $v1) {
                //debug($k1);
                //debug($v1);
                $result .= "<td align='center'><input type='text' name='".$row."_".$k1."' value='".$v1."'></td>\r\n";
            }
            $row++;
            $result .= "</tr>\r\n";
        }
    }
    
    return $result;
}

function GetOpsetItemPriceDataXlsTag($file)
{
    $result = "";

    $sheet = 0;
    $ext = substr(strrchr($file, "."), 1);
    $ext = strtolower($ext);

    if ($ext == "xlsx") {
        // Reader Excel 2007 file
        include "../../lib/PHPExcel.php";
        $objReader = PHPExcel_IOFactory::createReader("Excel2007");
        $objReader -> setReadDataOnly(true);
        $objReader -> canRead($file);
        //$objReader->setReadFilter( new MyReadFilter() );

        $objPHPExcel = $objReader -> load($file);
        $objPHPExcel -> setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel -> getActiveSheet();
        $xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);

        //debug('count($xlsData): '.count($xlsData));
        foreach ($xlsData as $key => $value) {
            if ($key == 1) {
                foreach ($value as $k => $v) {
                    $inner[] = $v;
                }
                $xlsPrice[0] = $inner;
            }            
            
            if ($key > 1) {
                $outs_inner = array();
                foreach ($value as $k => $v) {
                    $v = ($v == '') ? '' : addslashes(htmlentities($v));
                    //$v = ($v == '') ? '' : addslashes($v);
                    
                    //if ($v !== '') $outs_inner[] = $v;
                    $outs_inner[] = $v;
                }
                
                //debug($outs_inner);
                $xlsPrice[] = $outs_inner;
            }            
        }
    } else {
        // Reader Excel 2003 file
        //$xlsData = new Spreadsheet_Excel_Reader("unit_price.xls");
        $xlsData = new Spreadsheet_Excel_Reader($file);
        for ($row = 1; $row <= $xlsData -> rowcount($sheet); $row++) {
            $outs_inner = array();
            for ($col = 1; $col <= $xlsData -> colcount($sheet); $col++) {
                if (!$xlsData -> sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
                    $val = $xlsData -> val($row, $col, $sheet);
                    $val = ($val == '') ? '' : addslashes(htmlentities($val));

                    //$outs_inner[] = "{$val}"; # Quote or not? #
                    $outs_inner[] = $val;
                }
            }
            $xlsPrice[] = $outs_inner;
        }
    }
//exit;
    //debug($xlsPrice[0]);
    foreach ($xlsPrice[0] as $key => $val) {
        $val = str_replace("[", "", $val);
        $val = str_replace("]", "", $val);
        $val = str_replace("\n", "|", $val);
        if (strpos($val, "|") !== false) {
            $postArr = explode("|", $val); //split("|", $val);
            if(count($postArr) < 2) continue;
            $xlsPrice[0][$key] = $postArr[1];
        }
        
    }
    //debug($xlsPrice[0]);
    //debug($xlsPrice);
    $xlsRow = 1;
    foreach ($xlsPrice as $key => $val) {
        if($key > 0) {
            //debug($val);
            $result .= "<tr>\r\n";
            foreach ($val as $k => $v) {
                //debug($v);
                if($k==0) {
                    $result .= "<td align='center'><input type='text' name='cnt_".$xlsRow."' value='".$v."'></td>\r\n";
                }   
                else {
                    $result .= "<td align='center'><input type='text' name='".$xlsRow."_".$xlsPrice[0][$k]."' value='".$v."'></td>\r\n";
                }
            }
            $xlsRow++;
            $result .= "</tr>\r\n";
        }
    }
    
    return $result;
}

/*현수막,실사출력 추가 */
function GetPaperPrPriceDataArr($data)
{
    global $cid;
    
    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_paper_pr.php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_paper_pr.php";
        $file_data = json_decode($r_ipro_paper_pr, 1);
        //debug($file_data);
        $result = $file_data;
    }
    else {
        if($data) {
            foreach ($data as $k => $v) {
                //debug($k);
                //debug($v);
                $ItemKey = $v[opt_prefix].$v[opt_key];
                $Items = $v[opt_sub_items];
                //debug($Items);
                
                //if($ItemKey != "PPE01") continue;
                                
                $paperItemsDataArr = array("name" => $v[opt_value], "group" => $v[opt_desc]);
                
                if (strpos($Items, "|") !== false) {
                    $postArr = explode("|", $Items);
                    foreach ($postArr as $key => $val) {
                        if (strpos($val, "-") !== false) {
                            $vArr = explode("-", $val);
                            $paperItemsDataArr[paper][$vArr[0]] = array("width" => $vArr[1], "SPR1" => "0","SPR2" => "0","SPR3" => "0","SPR4" => "0","SPR5" => "0","SPR6" => "0","SPR7" => "0","SPR8" => "0","SPR9" => "0");
                        }
                        else {
                            $paperItemsDataArr[paper][$val] = array("width" => "", "SPR1" => "0","SPR2" => "0","SPR3" => "0","SPR4" => "0","SPR5" => "0","SPR6" => "0","SPR7" => "0","SPR8" => "0","SPR9" => "0");
                        }
                    }
                }
                else {
                    if (strpos($Items, "-") !== false) {
                        $vArr = explode("-", $Items);
                        $paperItemsDataArr[paper][($vArr[0]=="" ? "-" : $vArr[0])] = array("width" => $vArr[1], "SPR1" => "0","SPR2" => "0","SPR3" => "0","SPR4" => "0","SPR5" => "0","SPR6" => "0","SPR7" => "0","SPR8" => "0","SPR9" => "0");
                    }
                    else {
                        $paperItemsDataArr[paper][$Items] = array("width" => "", "SPR1" => "0","SPR2" => "0","SPR3" => "0","SPR4" => "0","SPR5" => "0","SPR6" => "0","SPR7" => "0","SPR8" => "0","SPR9" => "0");
                    }
                }
    
                $result[$ItemKey] = $paperItemsDataArr;
            }
        }
    }

    return $result;
}

function GetPaperPrPriceDataTag($data)
{
    $result;
    
    if ($data) {
        foreach ($data as $key => $val) {
            if(count($val[paper]) == 1) {  
                $result .= "<tr>\r\n";
                $result .= "<td align='center'>$val[group]</td>\r\n";
                $result .= "<td align='center'>$val[name]</td>\r\n";
            }
            
            foreach ($val[paper] as $k1 => $v1) {
                if(count($val[paper])>1) {
                    $result .= "<tr>\r\n";
                    $result .= "<td align='center'>$val[group]</td>\r\n";
                    $result .= "<td align='center'>$val[name]</td>\r\n";
                }
                
                $result .= "<td align='center'>$k1</td>\r\n";
                $result .= "<td align='center'>$v1[width]</td>\r\n";
                
                //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk
                $k1 = str_replace(".", "#", $k1);

                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR1' value='".$v1[SPR1]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR2' value='".$v1[SPR2]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR3' value='".$v1[SPR3]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR4' value='".$v1[SPR4]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR5' value='".$v1[SPR5]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR6' value='".$v1[SPR6]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR7' value='".$v1[SPR7]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR8' value='".$v1[SPR8]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_SPR9' value='".$v1[SPR9]."'></td>\r\n";
                
                if(count($val[paper])>1)
                    $result .= "</tr>\r\n";
            }
            
            if(count($val[paper]) == 1)
                $result .= "</tr>\r\n";
        }   
    }
    
    return $result;
}

function GetPaperPrPriceDataXlsTag($file)
{
    $result = "";

    $sheet = 0;
    $ext = substr(strrchr($file, "."), 1);
    $ext = strtolower($ext);

    if ($ext == "xlsx") {
        // Reader Excel 2007 file
        include "../../lib/PHPExcel.php";
        $objReader = PHPExcel_IOFactory::createReader("Excel2007");
        $objReader -> setReadDataOnly(true);
        $objReader -> canRead($file);
        //$objReader->setReadFilter( new MyReadFilter() );
        
        $objPHPExcel = $objReader -> load($file);

        $objPHPExcel -> setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel -> getActiveSheet();
        $xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
        //debug($xlsData);
        //debug('count($xlsData): '.count($xlsData));
        foreach ($xlsData as $key => $value) {
            if ($key >= 2) {
                $outs_inner = array();
                foreach ($value as $k => $v) {
                    //$v = ($v == '') ? '' : addslashes(htmlentities($v));
                    $v = ($v === '') ? '' : addslashes($v);
                    
                    //if ($v !== '') $outs_inner[] = $v;
                    $outs_inner[] = $v;
                }
                
                //debug($outs_inner);
                $xlsOptionPrice[] = $outs_inner;
            }
        }        
    } else {
        // Reader Excel 2003 file
        //$xlsData = new Spreadsheet_Excel_Reader("unit_price.xls");
        $xlsData = new Spreadsheet_Excel_Reader($excelImportFileName);

        for ($row = 2; $row <= $xlsData -> rowcount($sheet); $row++) {
            $outs_inner = array();
            for ($col = 1; $col <= $xlsData -> colcount($sheet); $col++) {
                if (!$xlsData -> sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
                    $val = $xlsData -> val($row, $col, $sheet);
                    //$val = ($val == '') ? '' : addslashes(htmlentities($val));
                    $val = ($val === '') ? '' : addslashes($val);

                    //$outs_inner[] = "{$val}"; # Quote or not? #
                    $outs_inner[] = $val;
                }
            }
            //$xlsPrice[] = implode(',', $outs_inner);
            $xlsOptionPrice[] = $outs_inner;
        }
    }
    //debug($xlsOptionPrice);

    $xlsRow = 1;
    foreach ($xlsOptionPrice as $itemKey => $itemValue) {
        if($itemKey > 0) {
            //debug($itemValue);
            
            $val = $itemValue[0];
            
            $val = str_replace("[", "", $val);
            $val = str_replace("]", "", $val);
            $val = str_replace("\n", "|", $val);

            if (strpos($val, "|") !== false) {
                $postArr = explode("|", $val); //split("|", $val);
                $group = $postArr[0];
                $key = $postArr[1];
            }            
            
            $result .= "<tr>\r\n";
            $result .= "<td align='center'>". htmlspecialchars($group, ENT_QUOTES) ."</td>\r\n";
            $result .= "<td align='center'>$itemValue[1]</td>\r\n";
        
            $result .= "<td align='center'>$itemValue[2]</td>\r\n";
            $result .= "<td align='center'>$itemValue[3]</td>\r\n";
            
            //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk 
            $key = str_replace(".", "#", $key);

            $itemValue[4] = ($itemValue[4] === '') ? '' : $itemValue[4];
            $itemValue[5] = ($itemValue[5] === '') ? '' : $itemValue[5];
            $itemValue[6] = ($itemValue[6] === '') ? '' : $itemValue[6];
            $itemValue[7] = ($itemValue[7] === '') ? '' : $itemValue[7];
            $itemValue[8] = ($itemValue[8] === '') ? '' : $itemValue[8];
            $itemValue[9] = ($itemValue[9] === '') ? '' : $itemValue[9];
            $itemValue[10] = ($itemValue[10] === '') ? '' : $itemValue[10];
            $itemValue[11] = ($itemValue[11] === '') ? '' : $itemValue[11];
            $itemValue[12] = ($itemValue[12] === '') ? '' : $itemValue[12];
            
            $result .= "<td align='center'><input type='text' name='".$key."_SPR1' value='".$itemValue[4]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_SPR2' value='".$itemValue[5]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_SPR3' value='".$itemValue[6]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_SPR4' value='".$itemValue[7]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_SPR5' value='".$itemValue[8]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_SPR6' value='".$itemValue[9]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_SPR7' value='".$itemValue[10]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_SPR8' value='".$itemValue[11]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_SPR9' value='".$itemValue[12]."'></td>\r\n";
            $result .= "</tr>\r\n";
        }
    }
    
    return $result;
}

# 지류에서 수량으로 변경됨.수량,할인율.
function GetPaperPrDcDataArr()
{
    global $cid;
    
    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_paper_pr_dc.php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_paper_pr_dc.php";
        $r_ipro_paper_pr_dc = json_decode($r_ipro_paper_pr_dc, 1);
        //debug($r_ipro_paper_pr_dc);
        
        $file_data = $r_ipro_paper_pr_dc;
        $result = $file_data;
        
        ksort($result);
    }
    else {
        $result[1] = array('cnt' => "0", 'val' => "0");
    }

    return $result;
}

# 1mm당 단가 설정
function GetPaperPrPrice1mmDataArr($data)
{
    global $cid;
    
    $result = "";

    //파일 로딩.
    if (file_exists("../../data/print/goods_items/". $cid ."_paper_pr_price_1mm.php"))
    {
        include_once "../../data/print/goods_items/". $cid ."_paper_pr_price_1mm.php";
        $file_data = json_decode($r_ipro_paper_pr_price_1mm, 1);
        //debug($file_data);
        $result = $file_data;
    }
    else {
        if($data) {
            foreach ($data as $k => $v) {
                //debug($k);
                //debug($v);
                $ItemKey = $v[opt_prefix].$v[opt_key];
                $Items = $v[opt_sub_items];
                //debug($Items);
                
                //if($ItemKey != "PPE01") continue;
                                
                $paperItemsDataArr = array("name" => $v[opt_value], "group" => $v[opt_desc]);
                
                if (strpos($Items, "|") !== false) {
                    $postArr = explode("|", $Items);
                    foreach ($postArr as $key => $val) {
                        if (strpos($val, "-") !== false) {
                            $vArr = explode("-", $val);
                            $paperItemsDataArr[paper][$vArr[0]] = array("width" => $vArr[1], "paper1mm" => "0","print1mm" => "0","coating1mm" => "0");
                        }
                        else {
                            $paperItemsDataArr[paper][$val] = array("width" => "", "paper1mm" => "0","print1mm" => "0","coating1mm" => "0");
                        }
                    }
                }
                else {
                    if (strpos($Items, "-") !== false) {
                        $vArr = explode("-", $Items);
                        $paperItemsDataArr[paper][($vArr[0]=="" ? "-" : $vArr[0])] = array("width" => $vArr[1], "paper1mm" => "0","print1mm" => "0","coating1mm" => "0");
                    }
                    else {
                        $paperItemsDataArr[paper][$Items] = array("width" => "", "paper1mm" => "0","print1mm" => "0","coating1mm" => "0");
                    }
                }
    
                $result[$ItemKey] = $paperItemsDataArr;
            }
        }
    }

    return $result;
}

function GetPaperPrPrice1mmDataTag($data)
{
    $result;

    if ($data) {
        foreach ($data as $key => $val) {
            if(count($val[paper]) == 1) {  
                $result .= "<tr>\r\n";
                $result .= "<td align='center'>$val[group]</td>\r\n";
                $result .= "<td align='center'>$val[name]</td>\r\n";
            }
            
            foreach ($val[paper] as $k1 => $v1) {
                if(count($val[paper])>1) {
                    $result .= "<tr>\r\n";
                    $result .= "<td align='center'>$val[group]</td>\r\n";
                    $result .= "<td align='center'>$val[name]</td>\r\n";
                }
                
                $result .= "<td align='center'>$k1</td>\r\n";
                $result .= "<td align='center'>$v1[width]</td>\r\n";
                
                //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk
                $k1 = str_replace(".", "#", $k1);

                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_paper1mm' value='".$v1[paper1mm]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_print1mm' value='".$v1[print1mm]."'></td>\r\n";
                $result .= "<td align='center'><input type='text' name='".$key."_".$k1."_coating1mm' value='".$v1[coating1mm]."'></td>\r\n";
                
                if(count($val[paper])>1)
                    $result .= "</tr>\r\n";
            }
            
            if(count($val[paper]) == 1)
                $result .= "</tr>\r\n";
        }   
    }
    
    return $result;
}

function GetPaperPrPrice1mmDataXlsTag($file)
{
    $result = "";

    $sheet = 0;
    $ext = substr(strrchr($file, "."), 1);
    $ext = strtolower($ext);

    if ($ext == "xlsx") {
        // Reader Excel 2007 file
        include "../../lib/PHPExcel.php";
        $objReader = PHPExcel_IOFactory::createReader("Excel2007");
        $objReader -> setReadDataOnly(true);
        $objReader -> canRead($file);
        //$objReader->setReadFilter( new MyReadFilter() );
        
        $objPHPExcel = $objReader -> load($file);

        $objPHPExcel -> setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel -> getActiveSheet();
        $xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
        //debug($xlsData);
        //debug('count($xlsData): '.count($xlsData));
        foreach ($xlsData as $key => $value) {
            if ($key >= 2) {
                $outs_inner = array();
                foreach ($value as $k => $v) {
                    //$v = ($v == '') ? '' : addslashes(htmlentities($v));
                    $v = ($v === '') ? '' : addslashes($v);
                    
                    //if ($v !== '') $outs_inner[] = $v;
                    $outs_inner[] = $v;
                }
                
                //debug($outs_inner);
                $xlsOptionPrice[] = $outs_inner;
            }
        }        
    } else {
        // Reader Excel 2003 file
        //$xlsData = new Spreadsheet_Excel_Reader("unit_price.xls");
        $xlsData = new Spreadsheet_Excel_Reader($excelImportFileName);

        for ($row = 2; $row <= $xlsData -> rowcount($sheet); $row++) {
            $outs_inner = array();
            for ($col = 1; $col <= $xlsData -> colcount($sheet); $col++) {
                if (!$xlsData -> sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']) {
                    $val = $xlsData -> val($row, $col, $sheet);
                    //$val = ($val == '') ? '' : addslashes(htmlentities($val));
                    $val = ($val === '') ? '' : addslashes($val);

                    //$outs_inner[] = "{$val}"; # Quote or not? #
                    $outs_inner[] = $val;
                }
            }
            //$xlsPrice[] = implode(',', $outs_inner);
            $xlsOptionPrice[] = $outs_inner;
        }
    }
    //debug($xlsOptionPrice);

    $xlsRow = 1;
    foreach ($xlsOptionPrice as $itemKey => $itemValue) {
        if($itemKey > 0) {
            //debug($itemValue);
            
            $val = $itemValue[0];
            
            $val = str_replace("[", "", $val);
            $val = str_replace("]", "", $val);
            $val = str_replace("\n", "|", $val);

            if (strpos($val, "|") !== false) {
                $postArr = explode("|", $val); //split("|", $val);
                $group = $postArr[0];
                $key = $postArr[1];
            }            
            
            $result .= "<tr>\r\n";
            $result .= "<td align='center'>". htmlspecialchars($group, ENT_QUOTES) ."</td>\r\n";
            $result .= "<td align='center'>$itemValue[1]</td>\r\n";
        
            $result .= "<td align='center'>$itemValue[2]</td>\r\n";
            $result .= "<td align='center'>$itemValue[3]</td>\r\n";
            
            //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk 
            $key = str_replace(".", "#", $key);

            $itemValue[4] = ($itemValue[4] === '') ? '' : $itemValue[4];
            $itemValue[5] = ($itemValue[5] === '') ? '' : $itemValue[5];
            $itemValue[6] = ($itemValue[6] === '') ? '' : $itemValue[6];
            
            $result .= "<td align='center'><input type='text' name='".$key."_paper1mm' value='".$itemValue[4]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_print1mm' value='".$itemValue[5]."'></td>\r\n";
            $result .= "<td align='center'><input type='text' name='".$key."_coating1mm' value='".$itemValue[6]."'></td>\r\n";
            $result .= "</tr>\r\n";
        }
    }
    
    return $result;
}

?>