// JavaScript Document


$(document).ready(function() {
    //$('.finalDsgn').click(function(){
    //	$('#dsgnCnfrm').simplePopup();
    //});

    //$('.order-btn1').click(function() {
    //    $('#ordspChng').simplePopup();
    //});

    $('.closeSian').click(function() {
        $('.viewSian').hide();
        $('.registeredAsian ul li').removeClass('on');
    });

    $('.temimOver').click(function() {
        $('#templateDetails').simplePopup();
    });

    $('.filterTemplate a').click(function() {
        if ($(this).hasClass('on')) {
            $(this).removeClass('on');
        } else {
            $('.filterTemplate a').removeClass('on');
            $(this).addClass('on');
        }
    });
    $('.syansalt-lay dl dd button.bnt-month').click(function() {
        if ($(this).hasClass('on')) {
            $(this).removeClass('on');
        } else {
            $('.syansalt-lay dl dd button').removeClass('on');
            $(this).addClass('on');
        }
    });
    $('.bfrTmplt button').click(function() {
        if ($(this).hasClass('on')) {
            $(this).removeClass('on');
        } else {
            $('.bfrTmplt button').removeClass('on');
            $(this).addClass('on');
        }
    });

    $('.registeredAsian ul li').click(function() {
        if ($(this).hasClass('on')) {
            $(this).removeClass('on');
        } else {
            $('.registeredAsian ul li').removeClass('on');
            $(this).addClass('on');
        }
        $('.viewSian').show();
    });

})