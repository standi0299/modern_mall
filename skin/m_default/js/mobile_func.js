//20150731 / minks / 모바일 네비게이션 메뉴 연동
function showMenu() {
    slideMenu();
}

//20150731 / minks / 모바일 장바구니 이동 연동
function goCartPage() {
    document.location.href = "/order/cart.php";
}

//20150731 / minks / 모바일 장바구니 상품 개수 연동
function getCartCount() {
    $j.ajax({
        type: "POST",
        url: "/ajax.php",
        data: "mode=mobile_get_cart_count",
        success: function(cart_cnt) {
            //window.android.onReceiveCartCount(cart_cnt);
            //window.android.setCartCount(cart_cnt);
            $j("#wrap header .mHeader a.cateBtn em").text(cart_cnt);
        }
    });
}

//20151014 / minks / 모바일 상단 타이틀 수정 연동
function getPageTitle() {
    var file_path = document.location.href;
    $j.ajax({
        type: "POST",
        url: "/ajax.php",
        data: "mode=mobile_get_page_title&file_path=" + encodeURIComponent(file_path),
        success: function(page_title) {
            //window.android.onReceivePageTitle(page_title);
            //window.android.setPageTitle(page_title);
            $j("#wrap header .mHeader h1").text(page_title);
        }
    });
}

//20180103 / minks / 사용자ID DB 연동
//iOS 사용자ID DB 연동 및 DB userAgent 정보 추가 / kdk
function setRegistrationID(uaType) {
    var registration_id = "";
    var userAgent = "";
    //window.alert(uaType);

    if (uaType.match(/Android/i) != null) {
        userAgent = "Android";
        registration_id = window.android.getPushToken();
    } else if (uaType.match(/iPhone|iPad|iPod/i) != null) {
        userAgent = "iOS";
        pushToken();
    }

    if (registration_id != "") {
        $j.ajax({
            type: "POST",
            url: "/ajax.php",
            data: "mode=mobile_set_registration_id&registration_id=" + registration_id + "&uatype=" + userAgent,
            success: function() {}
        });
    }
}


//iOS. 사용자ID DB 연동 / 20190213 / kdk
/**
 * PushToken을 가져온다. 
 * 리턴값은 onMessageListener() 함수로 전달된다. 
 **/
function pushToken() {
    document.location = "iosfunc://getPushToken";
}

/*
 * Api의 결과를 받는 메세지 리스너.
 */
function onMessageListener(key, value) {
    // PushToken 값을 수신받음
    //window.alert(key);
    //window.alert(value);
    if (key == "pushToken") {
        if (value != "") {
            $j.ajax({
                type: "POST",
                url: "/ajax.php",
                data: "mode=mobile_set_registration_id&registration_id=" + value + "&uatype=iOS",
                success: function() {}
            });
        }
    }
}