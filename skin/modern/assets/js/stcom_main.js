// JavaScript source code

//SLIDE

var num =1;
function slideroll() {
    num += 1;
    if (num > 5) {
        num = 1;
    };
    $(".Main_slide_0" + num).appendTo(".Main_slide_list");
};
var timing = setInterval("slideroll()", 3000);



$(function () {

    if (".search_box:active" == true) {
        $(".search").css({ borderBottom: "1px solid #535353" });
    } else {
        $(".search").css({ borderBottom: "1px solid #afafaf" });
    };



    //SLIDE
    $(".Main_slide_L_btn").click(function () {
        num -= 1;
        if (num < 1) {
            num = 5;
        };
        $(".Main_slide_0" + num).prependTo(".Main_slide_list");
    });

    $(".Main_slide_R_btn").click(function () {
        num += 1;
        if (num > 5) {
            num = 1;
        };
        $(".Main_slide_0" + num).appendTo(".Main_slide_list");
    });

    //slide hover
    $(".Main_slide").stop().hover(function () {
        clearInterval(timing);
    }, function () {
        timing = setInterval("slideroll()", 3000);
    });

    //SLIDE BTN
    $(".Main_slide_L_btn").hover(function () {
        $(this).children().first().addClass("Main_slide_btn_off");
        $(this).children().last().removeClass("Main_slide_btn_off");
    }, function () {
        $(this).children().last().addClass("Main_slide_btn_off");
        $(this).children().first().removeClass("Main_slide_btn_off");
        });

    $(".Main_slide_R_btn").hover(function () {
        $(this).children().first().addClass("Main_slide_btn_off");
        $(this).children().last().removeClass("Main_slide_btn_off");
    }, function () {
        $(this).children().last().addClass("Main_slide_btn_off");
        $(this).children().first().removeClass("Main_slide_btn_off");
    });


    //PRODUCTS AREA
    $(".main_products").hover(function () {
        $(this).children(".main_prod_off").css({ display: "none" });
    }, function () {
        $(this).children(".main_prod_off").css({ display:"" });
        });

    //PRODUCT:OVER ICON:OVER
    $(".icon_btn").hover(function () {
        $(this).children('img').last('img').removeClass("hidden");
    }, function () {
        $(this).children('img').last('img').addClass("hidden");
        });

    //PRODUCT:OVER ICON:CLICK
    $(".icon_btn").toggle(function () {
        $(this).prepend("<img src='images/main_pro_btn_01_count.png' class='icon_btn_on'/>");
        $(this).children(".icon_btn_on").nextAll().addClass("hidden");
    }, function () {
        $(this).children(".icon_btn_on").next().removeClass("hidden");
        $(this).children(".icon_btn_on").remove();
        
    });
    


    
});