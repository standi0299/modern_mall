// JavaScript source code

//SLIDE
/*
var num = 1;
function slideroll() {
    num += 1;
    if (num > 5) {
        num = 1;
    };
    $(".slide_in a .slide_" + num).animate({ "left": "0" }).addClass('main_slide');
    $(".slide_in a .slide_" + num).parent("a").siblings("a").children(".slide_img").css({"left":"1000px"}).removeClass('main_slide');
    $(".slide_wrap").css({ backgroundImage: "url(../img/slide_BG_" + num + ".jpg)" });
    $(".slide_btn div:first-child").appendTo($(".slide_btn"));
    $(".slide_btn div:eq(2)").addClass('btn_center');
    $(".slide_btn div:eq(2)").siblings("div").removeClass('btn_center');
};
var timing = setInterval("slideroll()", 3000);*/

//RECOMMEND
var count = 1;
function recommendroll() {
    count += 1;
    if (count > 3) {
        count = 1;
    };
    $("#recommend .rec_wrap article .new_" + count).animate({ "left": "0" });
    $("#recommend .rec_wrap article .new_" + count).parent("a").siblings("a").children(".prod_img").css({ "left": "210px" }, 0);

    $("#recommend .rec_wrap article .hot_" + count).animate({ "left": "0" });
    $("#recommend .rec_wrap article .hot_" + count).parent("a").siblings("a").children(".prod_img").css({ "left": "210px" }, 0);

    $("#recommend .rec_wrap article .sale_" + count).animate({ "left": "0" });
    $("#recommend .rec_wrap article .sale_" + count).parent("a").siblings("a").children(".prod_img").css({ "left": "210px" }, 0);
};
var rec_timing = setInterval("recommendroll()", 4000);



$(function () {
    //NAV
    $(".menu_slide").hide();

    $(".sub_menu .menu_name").mouseover(function () {
        $(this).parent().siblings(".sub_menu").children(".menu_slide").hide();
        $(this).siblings(".menu_slide").show();
        
        //하위 메뉴가 있을경우만 서브카테고리 영역을 표시한다.
        var child = $(this).siblings(".menu_slide");
        //$(child).css('z-index', 100);
        //child.style.zIndex = 1000;
        if (child.length > 0)
        	$(".main_menu").stop().animate({ height: "190px" }, 200);
        else {
        	$(".menu_slide").hide();
        	$(".main_menu").stop().animate({ height: "33px" }, 200);
        }             
    });
    //$(".menu_1,.menu_8,.menu_9").mouseover(function () {
    //    $(this).parent().siblings(".sub_menu").children(".menu_slide").hide();
    //    $(".main_menu").stop().animate({ height: "33px" }, 200);
    //});
    $(".main_menu").mouseleave(function () {
        $(".menu_slide").hide();
        $(".main_menu").stop().animate({ height: "33px" }, 200);
    });
     

    //SLIDE btn   
    /*$(".slide_Lbtn").click(function () {
        num -= 1;
        if (num < 1) {
            num = 5;
        };

        $(".slide_in .slide_" + num).animate({ "left": "0" }).addClass('main_slide');
        $(".slide_in a .slide_" + num).parent("a").siblings("a").children(".slide_img").animate({ "left": "1000px" }).removeClass('main_slide');
        $(".slide_wrap").css({ backgroundImage: "url(../img/slide_BG_" + num + ".jpg)" });
        $(".slide_btn div:last-child").prependTo($(".slide_btn"));
        $(".slide_btn div:eq(2)").addClass('btn_center');
        $(".slide_btn div:eq(2)").siblings("div").removeClass('btn_center');
    });
    $(".slide_Rbtn").click(function () {
        num += 1;
        if (num > 5) {
            num = 1;
        };

        $(".slide_in .slide_" + num).animate({ "left": "0" }).addClass('main_slide');
        $(".slide_in a .slide_" + num).parent("a").siblings("a").children(".slide_img").animate({ "left": "1000px" }).removeClass('main_slide');
        $(".slide_wrap").css({ backgroundImage: "url(../img/slide_BG_" + num + ".jpg)" });
        $(".slide_btn div:first-child").appendTo($(".slide_btn"));
        $(".slide_btn div:eq(2)").addClass('btn_center');
        $(".slide_btn div:eq(2)").siblings("div").removeClass('btn_center');
    });
    
    $(".slide_in, .btn_wrap").stop().hover(function () {
        clearInterval(timing);
    }, function () {
            timing = setInterval("slideroll()", 3000);
        });*/
    

}); 