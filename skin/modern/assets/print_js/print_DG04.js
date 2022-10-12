//디지털 낱장-스티커

function getList()
{
    var f = document.fm_print;
	var url = "/print/get_option_list.php";
	
	if (typeof (src) == undefined || src == null || src == "") {}
	else {url = src;}
	
    $.ajax({
    	url : url,
      	type : "POST",
      	data : $("#fm_print").serialize(),
      	async : false,
      	cache : false,
      	dataType : "json",
      	success : function(data) {
	      	if(data.error) {
	        	alert(data.error);
	          	return false;
	      	}

			//console.log(data);
			$.each(data , function(key, value) {
				//console.log(key + ": " + data[key]);
				
				if($("#"+key)) {
					if(key == "opt_size_cut_x" || key == "opt_size_cut_y" || key == "opt_size_work_x" || key == "opt_size_work_y") {
						$("#"+key).val(data[key]);
					}
					else {
						$("#"+key).html(data[key]);
					}	
				}
				
				//다이렉트 파일업로드 사용여부 처리.
				if(key == "opt_directupload_use") {
					if (data[key] == "N") $("#directupload_use").hide();
				}

				//알래스카 별색 사용여부 처리.
				if(key == "opt_print3_use") {
					if (data[key] == "N") $("#print3_use").hide();
				}
				
				//알래스카 책자 별색 사용여부 처리.
				if(key == "outside_print3_use") {
					if (data[key] == "N") $("#outside_print3_use").hide();
				}
				if(key == "inside_print3_0_use") {
					if (data[key] == "N") $("#inside_print3_0_use").hide();
				}
				if(key == "inpage_print3_0_use") {
					if (data[key] == "N") $("#inpage_print3_0_use").hide();
				}				
			});
  		}
  	});
}

function calcuPrice()
{
	//if (isValidOption()) {
		calcuPriceProc("fm_print", "/print/option_calcu_sticker.php");
	//}
}

