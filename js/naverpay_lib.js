

var fm = document.fm_cart;
	fm.mode.value = "npay_order";	
	fm.action = '/pg/naverpay/naverpay_order.php';
	
	function npay_form_check(frm)
	{
		return true;
	}

	function buy_npay()
	{
    var f = document.fm_cart;
		f.mode.value = "npay_order";	
		
    var check = npay_form_check(f);    
    if ( check ) {
			//네이버페이로 주문 정보를 등록하는 가맹점 페이지로 이동.
      //해당 페이지에서 주문 정보 등록 후 네이버페이 주문서 페이지로 이동.
      //location.href=url;

      //var win_buy_nc = window.open("_blank", "win_buy_nc", "scrollbars=yes,width=900,height=700,top=10,left=10");
      //f.action = "'.SERVER_DOMAIN.'/pg/naverpay/npay_order.php";
      //f.target = "win_buy_nc";
      //f.submit();
      //return false;

      $.ajax({
      	url : "/pg/naverpay/naverpay_order.php",
        type : "POST",
        data : $(f).serialize(),
        async : false,
        cache : false,
        dataType : "json",
        success : function(data) {
        	if(data.error) {
          	alert(data.error);
            return false;
        	}
        	
          //document.location.href = data.ORDER_URL + "?ORDER_ID="+data.ORDER_ID+"&SHOP_ID="+data.SHOP_ID+"&TOTAL_PRICE="+data.TOTAL_PRICE;
          window.open(data.ORDER_URL + "?ORDER_ID="+data.ORDER_ID+"&SHOP_ID="+data.SHOP_ID+"&TOTAL_PRICE="+data.TOTAL_PRICE, "_blank");
    		}
    	});
		}

    return false;
	}
	
	
	function wishlist_nc(url)
	{
  	var f = document.fm_cart;

    // 네이버페이로 찜 정보를 등록하는 가맹점 페이지 팝업 창 생성.
    // 해당 페이지에서 찜 정보 등록 후 네이버페이 찜 페이지로 이동.
    //'.($is_mobile_order ? '' : 'var win_wishlist_nc = window.open(url,"win_wishlist_nc","scrollbars=yes,width=400,height=267");'.PHP_EOL.'f.target = "win_wishlist_nc";').'
    f.action = "/pg/naverpay/npay_wish.php";
    f.submit();
    return false;
	}

	function not_buy_nc()
	{
  	alert("죄송합니다. 네이버페이로 구매가 불가한 상품입니다.");
		return false;
	}

