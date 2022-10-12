<?php


//유니큐브 인증키
//가맹점 인증키: 9B2E4860-635C-45AB-A56F-391B1D40CACC 
//버튼 인증키: BE83F116-DECB-4D8F-86D6-5BFA6FA915A5
//네이버공통인증키: s_58588ff5129f


//item data를 생성한다. 
	class ItemStack { 
		var $id; 
		var $name; 
		var $tprice; 
		var $uprice; 
		var $option; 
		var $count; 
				
		var $ITEM_OPTION_CODE;
  
		//option이 여러 종류라면, 선택된 옵션을 슬래시(/)로 구분해서 표시하는 것을 권장한다. 
		function ItemStack($_id, $_name, $_tprice, $_uprice, $_option, $_count, $_storage_id = '') { 
			$this->id = $_id; 
			$this->name = $_name; 
			$this->tprice = $_tprice; 
			$this->uprice = $_uprice; 
			$this->option = $_option; 
			$this->count = $_count; 
			
			$this->ITEM_OPTION_CODE = $_storage_id;
		}
  
		function makeQueryString() { 
			$ret .= 'ITEM_ID=' . urlencode($this->id); 
			$ret .= '&ITEM_NAME=' . urlencode($this->name); 
			$ret .= '&ITEM_COUNT=' . $this->count; 
			$ret .= '&ITEM_OPTION=' . urlencode($this->option); 
			$ret .= '&ITEM_TPRICE=' . $this->tprice; 
			$ret .= '&ITEM_UPRICE=' . $this->uprice; 
			
			//보관함 코드를 저장한다.			
			if ($this->ITEM_OPTION_CODE)
				$ret .= '&ITEM_OPTION_CODE=' . $this->ITEM_OPTION_CODE;
			return $ret; 
		}
	}; 



function get_naverpay_return_info($mb_id)
{
    global $default;

    $data = '';
    $address1 = trim($default['de_admin_company_addr']);
    $address2 = ' ';

    $data .= '<returnInfo>';
    $data .= '<zipcode><![CDATA['.$default['de_admin_company_zip'].']]></zipcode>';
    $data .= '<address1><![CDATA['.$address1.']]></address1>';
    $data .= '<address2><![CDATA['.$address2.']]></address2>';
    $data .= '<sellername><![CDATA['.$default['de_admin_company_name'].']]></sellername>';
    $data .= '<contact1><![CDATA['.$default['de_admin_company_tel'].']]></contact1>';
    $data .= '</returnInfo>';

    return $data;
}

function return_error2json($str, $fld='error')
{
    $data = array();
    $data[$fld] = trim($str);

    die(json_encode($data));
}
?>