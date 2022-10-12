//공통.


function calcuPriceProc(form_name, action_url) {
    $.ajax({
        url: action_url,
        type: "POST",
        data: $("#" + form_name).serialize(),
        async: false,
        cache: false,
        dataType: "json",
        success: function(data) {
            if (data.error) {
                alert(data.error);
                return false;
            }

            //$("#product_price1").html(data.opt_price);
            //$("#product_price2").html(data.sale_price);

            /*
            결제금액 : 공급가격: 3,000원 ( 할인가: 2,700원 ) + 부가세: 270원 = 2,970
            공급가격 : opt_price
            할인가격 : dc_price
            부가세 : vat_price
            판매가격 : sale_price
            */

            $("#opt_price").html(comma(data.opt_price));
            $("#est_opt_price").val(data.opt_price);

            if (data.dc_price) {
                $(".dc").show();
                $("#dc_price").html(comma(data.dc_price));
                $("#est_dc_price").val(data.dc_price);
            } else {
                $(".dc").hide();
            }

            if (data.vat_price) {
                $(".vat").show();
                $("#vat_price").html(comma(data.vat_price));
                $("#est_vat_price").val(data.vat_price);
            } else {
                $(".vat").hide();
            }

            if (data.sale_price == 0) $("#sale_price").html("");
            else $("#sale_price").html(comma(data.sale_price) + "원");

            $("#est_sale_price").val(data.sale_price);
			
			//alaskprint_cart.htm / alaskprint_book.htm
			$("#select_option_sum_price").val(data.sale_price);
			
            $("#sale_price_detail").html(comma(data.sale_price_detail));

            if (data.real_cover_size) {
                $("#real_cover_size").val(data.real_cover_size);
            }

            //알래스카 스킨 우측 가격 표기에 사용함.
            $.each(data, function(key, value) {
                if ($("span[name='" + key + "']").length > 0) {
                    //console.log(key + ": " + data[key]);
                    if (data[key] == 0) {
                    	$("span[name='" + key + "']").html("");
                    	
                    	$("li[name='" + key + "']").hide();
                    } else {
                    	$("span[name='" + key + "']").html(comma(data[key]));
                    	
                    	$("li[name='" + key + "']").show();                    	
                    }                    
                }
            });

        },
        error: function() {
            $("#product_price1").html("0");
            $("#product_price2").html("0");
        }
    });
}

function isValidOption() {
    var result = true;

    $.ajax({
        url: "/print/option_valid_check.php",
        type: "POST",
        data: $("#fm_print").serialize(),
        async: false,
        cache: false,
        dataType: "json",
        success: function(data) {
            if (data.error) {
                alert(data.error);
                result = false;
            }

            if (data.alert_msg) {
                alert(data.alert_msg);
                result = false;
            }

            if (data.caution_msg) {
                alert(data.caution_msg);
                result = true;
            }

            //$("#product_price1").html(data.opt_price);
            //$("#product_price2").html(data.sale_price);

        },
        error: function() {
            $("#product_price1").html("0");
            $("#product_price2").html("0");
            result = false;
        }
    });

    return result;
}

//현수막,실사출력
function isValidOptionPr() {
    var result = true;

    $.ajax({
        url: "/print/option_valid_check_pr.php",
        type: "POST",
        data: $("#fm_print").serialize(),
        async: false,
        cache: false,
        dataType: "json",
        success: function(data) {
            if (data.error) {
                alert(data.error);
                result = false;
            }

            if (data.alert_msg) {
                alert(data.alert_msg);
                result = false;
            }

            if (data.caution_msg) {
                alert(data.caution_msg);
                result = true;
            }
        },
        error: function() {
            result = false;
        }
    });

    return result;
}

function input_check(obj) {

}

function dynamicImgChange(obj) {
    var image_source = "";
    var opt_key = $(obj).val();

    if (print_img) {
        for (i = 0; i < print_img.length; i++) {
            if (opt_key == print_img[i].key) {
                image_source = print_img[i].url;
            }
        }
    }

    //image_source = "/skin/modern/img/noimg.png";
    if (image_source != "") {
        var strHtml = "<img width='421' height='480' src='" + image_source + "' onerror=\"$(this).attr('src','/skin/modern/img/noimg.png')\">";
        $("#dynamic_img").html(strHtml);
    }

    if ($('#opt_size :selected').val() == "USER") {
        $("#opt_size_cut_x").attr("disabled", false);
        $("#opt_size_cut_y").attr("disabled", false);

		if ($('input[name="print_goods_type"]').val() == "PR01") {
			if ($("#opt_size_cut_x").val() == "") $("#opt_size_cut_x").val("5000");
        	if ($("#opt_size_cut_y").val() == "") $("#opt_size_cut_y").val("1800");
		}
		else if ($('input[name="print_goods_type"]').val() == "PR02") {
        	if ($("#opt_size_cut_x").val() == "") $("#opt_size_cut_x").val("5000");
        	if ($("#opt_size_cut_y").val() == "") $("#opt_size_cut_y").val("3600");
		}
		else {
        	$('input[name="work_width"]').val("");
        	$('input[name="work_height"]').val("");
       	}
       	
       	$("#form_dec").hide();
    } else {
        $("#opt_size_cut_x").attr("disabled", true);
        $("#opt_size_cut_y").attr("disabled", true);

        $('input[name="cut_width"]').val("");
        $('input[name="cut_height"]').val("");

        getWorkOptSize($('#opt_size option:selected').text());
        
        $("#form_dec").show();
    }

}

//낱장 인쇄컬러 백색 선택시 처리.
function printColorClick(obj) {
    //console.log("printColorClick!");
    //console.log(obj);

    $('#m_color01, #m_color02').click(function() { //컬러,흑백
        $('.spot_color .s_white').css('display', 'inline-block');
    });

    $('#w_color01').click(function() { //백색
        $('input[value=ET4]').attr("checked", true);
        $('.spot_color .s_white').css('display', 'none');
    });
}

//낱장 인쇄컬러 별색추가 선택시 처리.
function print3ColorClick(obj) {
    if ($(obj).is(":checked")) {
        $('.order_left .spot_color_sec').fadeIn();
    } else {
        $('.order_left .spot_color_sec').fadeOut();
    }
}

//옵셋 낱장 인쇄 컬러 백색 선택시 처리.
function spotWhiteHide(obj) {
    //console.log("printColorClick!");
    //console.log(obj);

    $('#m_04, #m_05').click(function() { //컬러,흑백
        $('.spot_color .b_s_white').css('display', 'inline-block');
    });

    $('#w_04').click(function() { //백색
        $('input[value=ET4]').attr("checked", false);
        $('.spot_color .b_s_white').css('display', 'none');
    });
}

//옵셋  북 ,낱장 인쇄컬러 별색추가 선택시 보이기 처리.
function spotColorSecShow(obj, id) {
    if ($(obj).is(":checked")) {
        $('#' + id).fadeIn();
    } else {
        $('#' + id).fadeOut();
    }
}

//북 인쇄컬러 컬러,흑백 선택시 별색추가 숨김 처리.
function spotColorSecHide(id, cid) {
    $('#' + id).fadeOut();
    //$('#'+cid).prop('checked', false);
}

//견적서 출력.
function printOrder() {
    var data = "";
    if (isValidOption()) {
        $('input[name="option_json"]').val(makeOptionJson());

        var pop_title = "popupOpener";
        window.open("", pop_title, 'width=800,height=800,scrollbars=1,toolbar=no,status=no,resizable=no,menubar=no');

        var frmData = document.fm_print;
        frmData.action = "../print/estimate.php";
        frmData.target = pop_title;
        frmData.submit();
    }
}

//견적서 출력.프리셋100114
function printOrder2() {
    var data = "";

    $('input[name="option_json"]').val(makeOptionJson());

    var pop_title = "popupOpener";
    window.open("", pop_title, 'width=800,height=800,scrollbars=1,toolbar=no,status=no,resizable=no,menubar=no');

    var frmData = document.fm_print;
    frmData.action = "../print/estimate.php";
    frmData.target = pop_title;
    frmData.submit();
}

//장바구니 .
function goPrintCart() {
    if (isValidOption()) {
        $('input[name="option_json"]').val(makeOptionJson());
        procPrintOrder();
    }
}

//주문하기.
function submitPrintOrder() {
    if (isValidOption()) {
        $('input[name="option_json"]').val(makeOptionJson());
        procPrintOrder();
    }
}

//주문하기.프리셋100114
function submitPrintOrder2() {
    $('input[name="option_json"]').val(makeOptionJson());
    //console.log($('input[name="option_json"]').val());
    var est_order_memo = $('#est_order_memo').val();
    est_order_memo = est_order_memo.replace(/(?:\r\n|\r|\n)/g, '<br>');
    //console.log(est_order_memo);
    var orderJson = {}; //order_inspection 검수기능사용(시안요청).
    orderJson['order_inspection'] = "N";
    orderJson['msg'] = est_order_memo; //요청사항.
    orderJson['tsidx'] = $('input[name="templateSetIdx"]').val(); //templateSetIdx.
    orderJson['tidx'] = $('input[name="templateIdx"]').val(); //templateIdx.
    orderJson['tname'] = $('input[name="templateName"]').val(); //templateName.
    orderJson['turl'] = $('input[name="templateURL"]').val(); //templateURL.
    orderJson['storageid'] = $('input[name="storageid"]').val();
    //console.log(orderJson);

    $('input[name="ext_json_data"]').val(JSON.stringify(orderJson));
    //console.log(JSON.stringify(orderJson));

    $('#est_order_memo').val(""); //주문메모 초기화.
    procPrintOrder2();
}

function submitPrintOrder3() {
        $('input[name="option_json"]').val(makeOptionJson());
        procPrintOrder();
}
//폼 전송.
function procPrintOrder() {
    //장바구니 이동	
    var fm = document.fm_print;
    fm.action = "../order/cart.php";
    fm.mode.value = "cart";
    fm.target = "_self";
    fm.submit();
}

//폼 전송.
function procPrintOrder2() {
    //장바구니 이동	
    var fm = document.fm_print;
    fm.action = "../order/cart.php";
    fm.mode.value = "buy";
    fm.design_draft_flag.value = "Y";
    fm.target = "_self";
    fm.submit();
}

//jquery-file-upload 업로더 처리
function initFileUploadOrder(orderType, storageKey, fileJson) {
    //파일모드 - 업로드,파일없음 (UPLOAD, NOFILES)
    order_type = orderType;

    $("input[name=storageid]").val(storageKey);
    $("input[name=est_order_type]").val(orderType);
    $("input[name=est_file_upload_json]").val(fileJson);

    //submitPrintOrder();
    //console.log(fileJson);

    //파일정보 저장.
    if (fileJson) {
        var params = "storageid=" + storageKey + "&goodsnm=" + $j("input[name=goodsnm]").val() + "&file_json=" + fileJson;

        //ajax 전송
        $j.ajax({
            type: "POST",
            url: "/fileupload/indb.php",
            data: "mode=ajax_file_json&" + params,
            success: function(data) {
                //console.log("data : "+data);
                if (data == "FAIL") {
                    alert("파일 정보 등록에 실패하였습니다.");
                } else {
                    alert("파일 정보가 등록되었습니다.");
                    //location.href = '/order/cart.php';					
                }
            },
            error: function(e) {
                alert(e.responseText);
            }
        });
    }

    var fileDataJson = JSON.parse(fileJson);
    //console.log(fileDataJson);

    if (typeof(cfg_layout_top) == undefined || cfg_layout_top == null || cfg_layout_top == "") cfg_layout_top = "";
    if (typeof(cfg_skin_theme) == undefined || cfg_skin_theme == null || cfg_skin_theme == "") cfg_skin_theme = "";

    if (cfg_layout_top == "alaskaprint" || cfg_skin_theme == "B1") {
        //alert("cfg_layout_top:"+cfg_layout_top);
        //파일 리스트 초기화.
        $("#file_list").empty();

        var li = "";
        for (var key in fileDataJson) {
            var file = fileDataJson[key];
            //console.log(file['file_name']);
            //console.log(file['file_size']);
            //console.log(file['server_path']);

            //<li><span class="file_txt">filename.zip</span> <a href="#" class="file_btn"></a></li>
            //deleteFile(storageKey, fileName, obj);
            li += "<li>";
            li += "<span class='file_txt fl'>" + file['file_name'] + "</span>";
            li += "<a href='javascript:void(0)' class='file_btn' onclick=\"deleteFile('" + storageKey + "', '" + file['file_name'] + "', this);\"></a>";
            li += "</li>";
        }

        //alaskprint_100114.htm
        var obj_length = Object.keys(fileDataJson).length;
        //console.log(obj_length);
        if (obj_length > 1) {
            $("#finfo").html(obj_length + " files selected");
        } else {
            //console.log(fileDataJson[0]['file_name']);
            $("#finfo").html(fileDataJson[0]['file_name']);
        }

        //console.log(li);
        $("#file_list").append(li);

        //첨부파일 보기.
        btnView("U");
    } else {
        //파일 리스트 초기화.
        $("#file_upload").empty();

        var li = "";
        for (var key in fileDataJson) {
            var file = fileDataJson[key];
            //console.log(file['file_name']);
            //console.log(file['file_size']);
            //console.log(file['server_path']);

            li += "<li>";
            li += "<div class='file_name fl'><p>" + file['file_name'] + "</p></div>";
            li += "<div class='file_close fr'>";
            li += "<a href='javascript:void(0)' onclick=\"delLayerOpen('" + storageKey + "', '" + file['file_name'] + "', this);\"><img src='/skin/modern/assets/interpro/img/file_close.png' width='10' alt=''></a>";
            li += "</div>";
            li += "<div class='file_size fr'><p class='fl'>" + formatSizeUnits(file['file_size']) + "</p><div class='size_unit fl'>KB</div></div>";
            li += "</li>";
        }

        //console.log(li);

        $("#file_upload").append(li);

        //다른 업로드 비활성화.
        if ($("input[name=est_goodsnm]").val() == "FILEUPLOAD")
            $("#directupload_use").attr('class', 'upload_disable');
        else
            $("#fileupload_use").attr('class', 'upload_disable');
    }

}

function formatSizeUnits(bytes) {
    if (bytes >= 1073741824) { bytes = (bytes / 1073741824).toFixed(2); } //+' GB'
    else if (bytes >= 1048576) { bytes = (bytes / 1048576).toFixed(2); } //+' MB'
    else if (bytes >= 1024) { bytes = (bytes / 1024).toFixed(2); } //+' KB'
    else if (bytes > 1) { bytes = bytes; } //+' bytes'
    else if (bytes == 1) { bytes = bytes; } //+' byte'
    else { bytes = '0'; } //'0 byte'
    return bytes;
}

//파일업로드.
function fileUploadOpenLayer(mode) {
    //검수 기능 추가. 파일업로드:검수함, 다이렉트주문:검수안함 
    //파일 검수를  할 경우는 est_goodsnm=FILEUPLOAD 검수대기.
    //파일 검수를 안할 경우는 est_goodsnm=DIRECTUPLOAD 검수안함(완료).
    $("input[name=est_goodsnm]").val(mode);

    //가격 계산 다시 호출하기.
    if (mode == "DIRECTUPLOAD")
        $("input[name=order_direct]").val("Y");
    else
        $("input[name=order_direct]").val("N");

    calcuPrice();

    popupLayerInterFileUpload("/fileupload/inter_upload_popup.php?mode=" + mode);
}

//파일삭제.
function deleteFile(storageKey, fileName, obj) {
    //console.log(obj);

    //ajax 전송
    $j.ajax({
        type: "POST",
        url: "/fileupload/indb.php",
        data: "mode=ajax_file_delete&storage_code=" + storageKey + "&file_name=" + fileName,
        success: function(data) {
            //console.log("data : "+data);
            if (data == "FAIL") {
                alert("파일 삭제에 실패하였습니다.");
            } else {
                $(obj).closest("li").remove();
                alert("파일 삭제가 완료되었습니다.");

                //파일이 없으면 모두 활성화.
                if (typeof(cfg_layout_top) == undefined || cfg_layout_top == null || cfg_layout_top == "") cfg_layout_top = "";
                if (cfg_layout_top == "alaskaprint") {
                    if ($("#file_list li").length < 1) {
                        btnView("N");
                    }
                } else {
                    if ($("#file_upload li").length < 1) {
                        $("#fileupload_use").attr('class', 'upload');
                        $("#directupload_use").attr('class', 'upload');
                    }
                }
            }
        },
        error: function(e) {
            alert(e.responseText);
        }
    });
}

//전체 주문 json 만들기.
function makeOptionJson() {
    var orderJson = {}; //key정보 저장.
    var orderOption = {}; //value정보 저장.

    //select 상자의 값들을 취합.
    $("select").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name);
        if (name.indexOf('inside_') > -1 || name.indexOf('inpage_') > -1)
            return true;
        else
            orderJson[name] = $(this).val();
    });

    //input 상자의 값들을 취합.
    $("input[type='text']").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name +" : "+ val);
        if (name.indexOf('inside_') > -1 || name.indexOf('inpage_') > -1)
            return true;
        else
            orderJson[name] = $(this).val();
    });

    //input 상자의 값들을 취합.
    $("input[type='hidden']").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name +" : "+ val);
        if (name.indexOf('work_width') > -1 || name.indexOf('work_height') > -1)
            orderJson[name] = $(this).val();
    });

    //radio 상자의 값들을 취합.
    $("input[type='radio']:checked").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name +" : "+ val);
        if (name.indexOf('inside_') > -1 || name.indexOf('inpage_') > -1)
            return true;
        else
            orderJson[name] = $(this).val();
    });

    //checkbox 상자의 값들을 취합.
    var cColor = "";
    var eColor = "";
    var optScb = ""; //스코딕스 박.
    $("input[type='checkbox']:checked").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name +" : "+ val);
        if (name.indexOf('inside_') > -1 || name.indexOf('inpage_') > -1)
            return true;
        else if (name.indexOf('outside_print3[]') > -1) {
            //표지 별색 처리.
            cColor += $(this).val() + ",";
        } else if (name.indexOf('opt_print3[]') > -1) {
            //별색 처리.
            eColor += $(this).val() + ",";
        } else if (name.indexOf('opt_scb[]') > -1) {
            //스코딕스 박 처리.
            optScb += $(this).val() + ",";
        } else
            orderJson[name] = $(this).val();
    });

    if (cColor != "") orderJson['outside_print3'] = cColor.slice(0, -1);
    if (eColor != "") orderJson['opt_print3'] = eColor.slice(0, -1);
    if (optScb != "") orderJson['opt_scb'] = optScb.slice(0, -1);

    //내지 처리.
    var insideCnt = 0;
    $("input[name='inside_page[]']").each(function(index) {
        orderJson['inside_page_' + index] = $(this).val();
        insideCnt = index;
    });
    $("select[name='inside_paper_group[]']").each(function(index) {
        orderJson['inside_paper_group_' + index] = $(this).val();
    });
    $("select[name='inside_paper[]']").each(function(index) {
        orderJson['inside_paper_' + index] = $(this).val();
    });
    $("select[name='inside_paper_gram[]']").each(function(index) {
        orderJson['inside_paper_gram_' + index] = $(this).val();
    });
    $("input[type='radio']:checked").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name +" : "+ val);
        if (name.indexOf('inside_') > -1)
            orderJson[name] = $(this).val();
    });
    $("input[type='checkbox']:checked").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name +" : "+ val);
        if (name.indexOf('inside_') > -1) {
            //별색 제외.
            if (name.indexOf('[]') > -1) return true;

            orderJson[name] = $(this).val();
        }
    });
    //내지 별색 처리.
    for (i = 0; i <= insideCnt; i++) {
        var insideColor = "";

        $("input[name='inside_print3_" + i + "[]']:checked").each(function(index) {
            //별색 처리.
            insideColor += $(this).val() + ",";
        });

        if (insideColor != "") orderJson['inside_print3_' + i] = insideColor.slice(0, -1);
    }

    //간지/면지  처리.
    var inpageCnt = 0;
    $("input[name='inpage_page[]']").each(function(index) {
        orderJson['inpage_page_' + index] = $(this).val();
        inpageCnt = index;
    });
    $("select[name='inpage_paper_group[]']").each(function(index) {
        orderJson['inpage_paper_group_' + index] = $(this).val();
    });
    $("select[name='inpage_paper[]']").each(function(index) {
        orderJson['inpage_paper_' + index] = $(this).val();
    });
    $("select[name='inpage_paper_gram[]']").each(function(index) {
        orderJson['inpage_paper_gram_' + index] = $(this).val();
    });
    $("input[type='radio']:checked").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name +" : "+ val);
        if (name.indexOf('inpage_') > -1)
            orderJson[name] = $(this).val();
    });
    $("input[type='checkbox']:checked").each(function(index) {
        var name = $(this).attr('name');

        //console.log(name +" : "+ val);
        if (name.indexOf('inpage_') > -1) {
            //별색 제외.
            if (name.indexOf('[]') > -1) return true;

            orderJson[name] = $(this).val();
        }
    });
    //간지/면지 별색 처리.
    for (i = 0; i <= inpageCnt; i++) {
        var inpageColor = "";

        $("input[name='inpage_print3_" + i + "[]']:checked").each(function(index) {
            //별색 처리.
            inpageColor += $(this).val() + ",";
        });

        if (inpageColor != "") orderJson['inpage_print3_' + i] = inpageColor.slice(0, -1);
    }

    //메모
    var est_memo = $("textarea[name='est_order_memo']").val();
    est_memo = est_memo.replace(/(?:\r\n|\r|\n)/g, '<br>');
    orderJson['est_order_memo'] = est_memo;

    var strSize = "";
    var strCutSize = "";
    var strCnt = "";
    var strTitle = "";
    var strMemo = "";

    var strOutside = "";
    var strInside = [];
    var strInpage = [];

    var strPage = "";
    var strPrint = "";

    var strAfter = "";
    //console.log( orderJson );
    for (var key in orderJson) {
        //console.log( key + '=>' + orderJson[key] );

        if (orderJson[key] == null) continue;

        //규격.
        if (key == "opt_size") {
            strSize = "규격:" + orderJson[key] + ";";
        } else if (key == "cut_width") {
            if (orderJson[key] != "") strCutSize += "재단사이즈 가로:" + orderJson[key];
        } else if (key == "cut_height") {
            if (orderJson[key] != "") strCutSize += "세로:" + orderJson[key] + ";";
        }

        //수량.
        else if (key == "opt_page") {
            strCnt = "수량:" + orderJson[key] + "매" + ";";
        } else if (key == "cnt") {
            strCnt += "수량:" + orderJson[key] + "건" + ";";
        }

        //제목/메모.
        else if (key == "est_title") {
            if (orderJson[key] != "") strTitle = "제목:" + orderJson[key] + ";";
        } else if (key == "est_order_memo") {
            if (orderJson[key] != "") strMemo = "메모:" + orderJson[key] + ";";
        }

        //표지.
        else if (key == "outside_wing") {
            if (orderJson[key] != "") strOutside = "표지날개:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "outside_wing_width") {
            if (orderJson[key] != "") strOutside += "날개크기:" + orderJson[key] + ";";
        } else if (key == "outside_page") {
            if (orderJson[key] != "") strOutside += "페이지수:" + orderJson[key] + ";";
        } else if (key == "outside_paper_group") {
            if (orderJson[key] != "") strOutside += "표지용지:" + orderJson[key];
        } else if (key == "outside_paper") {
            if (orderJson[key] != "") strOutside += "/" + $("select[name='outside_paper']").find("option[value='" + orderJson[key] + "']").text();
        } else if (key == "outside_paper_gram") {
            if (orderJson[key] != "") strOutside += "/" + orderJson[key] + "g" + ";";
        }

        //표지 인쇄컬러,양면/단면.
        else if (key == "outside_print1") {
            if (orderJson[key] != "") strOutside += "인쇄컬러:" + r_ipro_print_sub_item[orderJson[key]] + ";";
        } else if (key == "outside_print2") {
            if (orderJson[key] != "") strOutside += "인쇄면:" + r_ipro_print_sub_item[orderJson[key]] + ";";
        } else if (key == "outside_print3") {
            if (orderJson[key] != "") {
                var prints = orderJson[key].split(',');
                var str = "";

                for (var i in prints) {
                    str += r_ipro_print_code[prints[i]] + ",";
                }

                strOutside += "별색:" + str.slice(0, -1) + ";";
            }
        }

        //일반 용지.
        else if (key == "opt_paper_group") {
            strPage = "용지:" + orderJson[key];
        } else if (key == "opt_paper") {
            strPage += "/" + $("select[name='opt_paper']").find("option[value='" + orderJson[key] + "']").text();
        } else if (key == "opt_paper_gram") {
            strPage += "/" + orderJson[key] + "g" + ";";
        }

        //인쇄컬러,양면/단면.
        else if (key == "opt_print1") {
            if (orderJson[key] != "") strPrint = "인쇄컬러:" + r_ipro_print_sub_item[orderJson[key]] + ";";
        } else if (key == "opt_print2") {
            if (orderJson[key] != "") strPrint += "인쇄면:" + r_ipro_print_sub_item[orderJson[key]] + ";";
        } else if (key == "opt_print3") {
            if (orderJson[key] != "") {
                var prints = orderJson[key].split(',');
                var str = "";

                for (var i in prints) {
                    str += r_ipro_print_code[prints[i]] + ",";
                }

                strPrint += "별색:" + str.slice(0, -1) + ";";
            }
        }

//현수막 실사출력
//{"opt_size":"USER","opt_paper":"EPR01","opt_coating":"ECT1","opt_cut":"ECU1","opt_design":"EDS2","opt_processing":"EPC1","opt_page":"1","est_title":"제목입니다.","cut_width":"900","cut_height":"600","cnt":"1","work_width":"","work_height":"","est_order_memo":"요청사항입니다.","order_option_desc":"[제목:제목입니다.;][규격:USER;재단사이즈 가로:900세로:600;수량:1매;수량:1건;][내지::/현수막천][메모:요청사항입니다.;]","est_inside_cnt":0,"est_inpage_cnt":0,"real_cover_size":"","est_supply_price":"13810","est_price":"15100

        //후가공.
        else if (key == "opt_coating") {
            if (orderJson[key] != "") strAfter += "코팅:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_cut") {
            if (orderJson[key] != "") strAfter += "재단:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_design") {
            if (orderJson[key] != "") strAfter += "디자인:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_processing") {
            if (orderJson[key] != "") strAfter += "가공&마감:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_instant") {
            if (orderJson[key] != "") strAfter += "즉석명함:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "outside_gloss") {
            if (orderJson[key] != "") strAfter += "코팅(표지):" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_gloss") {
            if ($("input[name='print_goods_type']").val() == "DG05" || $("input[name='print_goods_type']").val() == "OS02") {
                if (orderJson[key] != "") strAfter += "코팅(내지):" + r_ipro_print_code[orderJson[key]] + ";";
            } else {
                if (orderJson[key] != "") strAfter += "코팅:" + r_ipro_print_code[orderJson[key]] + ";";
            }
        } else if (key == "opt_punch") {
            if (orderJson[key] != "") strAfter += "타공:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_oshi") {
            if (orderJson[key] != "") strAfter += "오시:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_miss") {
            if (orderJson[key] != "") strAfter += "미싱:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_round") {
            if (orderJson[key] != "") strAfter += "귀도리:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_domoo") {
            if (orderJson[key] != "") strAfter += "도무송:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_barcode") {
            if (orderJson[key] != "") strAfter += "바코드:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_number") {
            if (orderJson[key] != "") strAfter += "넘버링:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_stand") {
            if (orderJson[key] != "") strAfter += "스탠드(미니배너):" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_dangle") {
            if (orderJson[key] != "") strAfter += "댕글(와블러):" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_tape") {
            if (orderJson[key] != "") strAfter += "양면테잎(봉투):" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_address") {
            if (orderJson[key] != "") strAfter += "주소인쇄(봉투):" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_sc") {
            if (orderJson[key] != "") strAfter += "스코딕스:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_scb") {
            //if (orderJson[key] != "") strAfter += "스코딕스 박:" + r_ipro_print_code[orderJson[key]] +";";
            if (orderJson[key] != "") {
                var scbs = orderJson[key].split(',');
                var str = "";

                for (var i in scbs) {
                    str += r_ipro_print_code[scbs[i]] + ",";
                }

                strAfter += "스코딕스 박:" + str.slice(0, -1) + ";";
            }
        } else if (key == "opt_wing") {
            if (orderJson[key] != "") strAfter += "날개(책자):" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_bind") {
            if (orderJson[key] != "") strAfter += "제본(책자):" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_bind_type") {
            if (orderJson[key] != "") strAfter += "제본방향(책자):" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_cutting") {
            if (orderJson[key] != "") strAfter += "재단:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_foil") {
            if (orderJson[key] != "") strAfter += "박:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_press") {
            if (orderJson[key] != "") strAfter += "형압:" + r_ipro_print_code[orderJson[key]] + ";";
        } else if (key == "opt_holding") {
            if (orderJson[key] != "") strAfter += "접지:" + r_ipro_print_code[orderJson[key]] + ";";
        } else {
            //console.log( key + '=>' + orderJson[key] );

            //내지 처리.
            for (i = 0; i <= insideCnt; i++) {
                //console.log( key + '=>' + orderJson[key] );

                if (key == "inside_page_" + i) {
                    if (orderJson[key] != "") strInside[i] = "페이지수:" + orderJson[key] + ";";
                } else if (key == "inside_paper_group_" + i) {
                    if (orderJson[key] != "") strInside[i] += "내지용지:" + orderJson[key];
                } else if (key == "inside_paper_" + i) {
                    if (orderJson[key] != "") strInside[i] += "/" + $("select[name='inside_paper[]']").eq(i).find("option[value='" + orderJson[key] + "']").text();
                } else if (key == "inside_paper_gram_0") {
                    if (orderJson[key] != "") strInside[i] += "/" + orderJson[key] + "g" + ";";
                }

                //인쇄컬러,양면/단면.
                else if (key == "inside_print1_" + i) {
                    if (orderJson[key] != "") strInside[i] += "인쇄컬러:" + r_ipro_print_sub_item[orderJson[key]] + ";";
                } else if (key == "inside_print2_" + i) {
                    if (orderJson[key] != "") strInside[i] += "인쇄면:" + r_ipro_print_sub_item[orderJson[key]] + ";";
                } else if (key == "inside_print3_" + i) {
                    if (orderJson[key] != "") {
                        var prints = orderJson[key].split(',');
                        var str = "";

                        for (var j in prints) {
                            str += r_ipro_print_code[prints[j]] + ",";
                        }

                        strInside[i] += "별색:" + str.slice(0, -1) + ";";
                    }
                }
            }

            //간지/면지  처리.
            for (i = 0; i <= inpageCnt; i++) {
                //console.log( key + '=>' + orderJson[key] );

                if (key == "inpage_page_" + i) {
                    if (orderJson[key] != "") strInpage[i] = "페이지수:" + orderJson[key] + ";";
                } else if (key == "inpage_paper_group_" + i) {
                    if (orderJson[key] != "") strInpage[i] += "간지/면지용지:" + orderJson[key] + ";";
                } else if (key == "inpage_paper_" + i) {
                    if (orderJson[key] != "") strInpage[i] += "/" + $("select[name='inpage_paper[]']").eq(i).find("option[value='" + orderJson[key] + "']").text();
                } else if (key == "inpage_paper_gram_0") {
                    if (orderJson[key] != "") strInpage[i] += "/" + orderJson[key] + "g" + ";";
                }

                //인쇄컬러,양면/단면.
                else if (key == "inpage_print1_" + i) {
                    if (orderJson[key] != "") strInpage[i] += "인쇄컬러:" + r_ipro_print_sub_item[orderJson[key]] + ";";
                } else if (key == "inpage_print2_" + i) {
                    if (orderJson[key] != "") strInpage[i] += "인쇄면:" + r_ipro_print_sub_item[orderJson[key]] + ";";
                } else if (key == "inpage_print3_" + i) {
                    if (orderJson[key] != "") {
                        var prints = orderJson[key].split(',');
                        var str = "";

                        for (var j in prints) {
                            str += r_ipro_print_code[prints[j]] + ",";
                        }

                        strInpage[i] += "별색:" + str.slice(0, -1) + ";";
                    }
                }
            }

        }
    }

    var strOption = "";

    //if (strTitle != "") strOption += "[" + strTitle + "]";
    //if (strMemo != "") strOption += "[" + strMemo + "]";
    strOption += "[" + strTitle + "]";

    //if (strSize != "") strOption += "[" + strSize + "]";
    //if (strCutSize != "") strOption += "[" + strCutSize + "]";
    //if (strCnt != "") strOption += "[" + strCnt + "]";	
    strOption += "[" + strSize + strCutSize + strCnt + "]";

    if ($("input[name='print_goods_type']").val() == "DG05" || $("input[name='print_goods_type']").val() == "OS02") {
        if (strOutside != "") strOption += "[표지::" + strOutside + "]";
        if (strInside != "") strOption += "[내지::" + strInside.join("||") + "]";
        if (strInpage != "") strOption += "[간지/면지::" + strInpage.join("||") + "]";
    } else {
        strOption += "[내지::" + strPage + strPrint + "]";
    }

    if (strAfter != "") strOption += "[후가공::" + strAfter + "]";

    strOption += "[" + strMemo + "]";

    //console.log(strOption);
    if (strOption != "") orderJson['order_option_desc'] = strOption;

    orderJson['est_inside_cnt'] = insideCnt;
    orderJson['est_inpage_cnt'] = inpageCnt;

    //real_cover_size
    orderJson['real_cover_size'] = $("input[name='real_cover_size']").val();

    //cart.php에서 사용하는 변수명으로
    orderJson['est_supply_price'] = $("input[name='est_opt_price']").val();
    orderJson['est_price'] = $("input[name='est_sale_price']").val();
    orderJson["est_order_option"] = JSON.stringify(orderJson);
    orderJson['est_order_option_desc'] = orderJson['order_option_desc'];
    //console.log(orderJson);

    return JSON.stringify(orderJson);
}

//재단 사이즈.
function getWorkOptSize(size) {
    //ajax 전송
    $j.ajax({
        url: "/print/indb.php",
        type: "POST",
        data: "mode=getWorkOptSize&opt_size=" + size,
        async: false,
        cache: false,
        dataType: "json",
        success: function(data) {
            //console.log("data.size_x : "+data.size_x);
            //console.log("data.size_y : "+data.size_y);
            if (data != null) {
                $j('input[name="work_width"]').val(data.size_x);
                $j('input[name="work_height"]').val(data.size_y);

                //alaska
                $j("#form_dec").html("* 작업사이즈 " + data.size_x + " X " + data.size_y + "mm");
            }
        },
        error: function(e) {
            alert(e.responseText);
            return false;
        }
    });
}

//작업 사이즈.
function getWorkUserSize(obj, tar) {
    var val = 0;

    if (_pattern(obj)) {
        if ($j(obj).val() != "") {
            val = Number($j(obj).val());

            if (val > 0) {
                $j("#" + tar).val(val + 6);
            }
        }
    }
}

//세네카.
function getSenaka() {
    $("input[name=mode]").val("getSenaka");
    //ajax 전송
    $j.ajax({
        url: "/print/indb.php",
        type: "POST",
        data: $("#fm_print").serialize(),
        async: false,
        cache: false,
        dataType: "json",
        success: function(data) {
            //console.log(data);
            $.each(data, function(key, value) {
                //console.log(key + ": " + data[key]);
                if ($("#" + key)) {
                    if (data[key] != "0")
                        $("#" + key).html(data[key]);
                }
            });

        },
        error: function(e) {
            alert(e.responseText);
            return false;
        }
    });
    $("input[name=mode]").val("");
}

//알래스카프린트 템플릿리스트 팝업. 
//editor_type=web,come_back=web_view 수정.
function templateListOpen(catno, goodsno, editortype, comeback) {
    //견적항목 처리.
    $('input[name="option_json"]').val(makeOptionJson());
    //window.open("/goods/list.php?editor_type=web_view&catno="+ catno +"&goodsno="+ goodsno,"web_view");
    //window.open("/goods/view_template.alaskaprint.php?editor_type=web_view&catno="+ catno +"&goodsno="+ goodsno,"web_view");

    //submitPrintOrder2("/goods/view_template.alaskaprint.php?editor_type=web_view&catno="+ catno +"&goodsno="+ goodsno);
    //submitPrintOrder2("/goods/view_template.alaskaprint.php?editor_type="+editortype+"&catno="+ catno +"&goodsno="+ goodsno +"&come_back="+ comeback);
    var url = "/goods/view_template.alaskaprint.php?editor_type=" + editortype + "&catno=" + catno + "&goodsno=" + goodsno + "&come_back=" + comeback;

    //템플릿 리스트로 이동.	
    var fm = document.fm_print;
    fm.action = url;
    fm.mode.value = "cart";
    fm.target = "_self";
    fm.submit();
}

//알래스카프린트 편집기 리턴(상품상세페이지 직접디자인하기).
function retWpodAction(sid) {
    //alert("sid:"+ sid);
    $("input[name=storageid]").val(sid);
    btnView("Y");
}

//알래스카프린트 버튼.
function btnView(mode) {
    if (mode == "Y") {
        //첨부파일 보기 숨기기.
        $(".pd_upload_filelist").hide();

        //내 파일 가져오기 숨기기.
        $(".file_upload").hide();

        //직접 디자인하기 숨기기.
        $(".design_upload").hide();

        //편집내용  미리보기 보이기.
        $(".design_preview").show();

        //디자인 의뢰하기 숨기기.
        //$(".direct_upload").hide();

        //주문하기 버튼 보이기.		
        $(".order_btn").show();
    } else if (mode == "U") {
        //첨부파일 보기 보이기.
        $(".pd_upload_filelist").show();
        //$(".pd_upload_filelist").hide();

        //내 파일 가져오기 숨기기.
        $(".file_upload").hide();

        //직접 디자인하기 숨기기.
        $(".design_upload").hide();

        //편집내용  미리보기 숨기기.
        $(".design_preview").hide();

        //디자인 의뢰하기 숨기기.
        //$(".direct_upload").hide();

        //주문하기 버튼 보이기.		
        $(".order_btn").show();
    } else {
        //첨부파일 보기 숨기기.
        $(".pd_upload_filelist").hide();

        //내 파일 가져오기 보이기.
        $(".design_upload").show();

        //직접 디자인하기 보이기.
        $(".file_upload").show();

        //편집내용  미리보기 숨기기.
        $(".design_preview").hide();

        //디자인 의뢰하기 보이기.
        //$(".direct_upload").show();

        //주문하기 버튼 숨기기.		
        //$(".order_btn").hide();
        $(".order_btn").show();
    }
}