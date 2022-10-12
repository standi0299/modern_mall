
$j(function(){
	$j("#zipcode").removeAttr("readonly");
	$j("#cust_zipcode").removeAttr("readonly");
	$j("#orderer_zipcode").removeAttr("readonly");
	$j("#receiver_zipcode").removeAttr("readonly");
	$j("#receiver_zipcode2").removeAttr("readonly");
		
	//$j(".select-box").hide();
	//회원가입 우편번호 버튼
	$j("#btn_zipcode").html('郵便番号から住所入力');
	$j("#btn_cust_zipcode").html('郵便番号から住所入力');
	$j("#btn_zipcode").css('width', '35%');
	$j("#btn_cust_zipcode").css('width', '35%');
	
	//주문 페이지 우편번호 버튼
	$j("#btn_order_zipcode").html('郵便番号から住所入力');
	$j("#btn_order_zipcode2").html('郵便番号から住所入力');
	
});


/*** 일본 우편번호 처리 ***/
function popupZipcodeja_JP(rfunc,arrIndex)
{	
	if (!rfunc)
	{
		rfunc = "zipcode_return";
	}
	
	if (rfunc == "zipcode_return")
		zipcode_name = "zipcode";
	else if (rfunc == "zipcode_return_cust")
		zipcode_name = "cust_zipcode";
	else if (rfunc == "_zcrn_o")
		zipcode_name = "orderer_zipcode";
	else if (rfunc == "_zcrn_r")
	{
		if (arrIndex)
			zipcode_name = "receiver_zipcode_" + arrIndex;
		else
			zipcode_name = "receiver_zipcode";
	}
	else if (rfunc == "_zcrn_r2")
		zipcode_name = "receiver_zipcode";

	var $zip = $j('#' + zipcode_name);
  var zip = $zip.val();
    
  var resultAddress = '';
  // 일본 우편번호는 7자리로 고정되어있다.
  // sample 6800001
  if(zip && zip.length === 7)
  {
  	ZA.request(zip, function(data, err){	    
	    if(err){
	      $zip.focus();
	      alert(data.message);
	      return;
	  	}
	    //$('#address1').val(data.pref);
	    //$('#address2').val(data.city);
	    //$('#address3').val(data.town);	            
	   	resultAddress = data.fullAddress;
	   	
	   	if (resultAddress)
	   	{
		   	if (rfunc == "zipcode_return")
					zipcode_return(zip,resultAddress);
				else if (rfunc == "zipcode_return_cust")
					zipcode_return_cust(zip,resultAddress);	
				else if (rfunc == "_zcrn_o")
					_zcrn_o(zip,resultAddress);	
				else if (rfunc == "_zcrn_r")
					_zcrn_r(zip,resultAddress);
				else if (rfunc == "_zcrn_r2")
					_zcrn_r2(zip,resultAddress);
			}
		
		});
	}else{
  	alert('郵便番号に誤りがあります。');
    $zip.focus();
	}
}
