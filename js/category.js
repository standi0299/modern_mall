function category(id,catno){

	var cid = "";
	if (arguments[2]){
		cid = arguments[2];
	}
	var front;
	if (arguments[3]){
		front = 1;
	}

	if (!catno) var catno = "";
	var div = document.getElementById(id);
	var select = div.getElementsByTagName("select");
	var len = select.length;

	var idx = -1;
	var _self = this;

	this.value = "";

	for (var i=0;i<len;i++){
		select[i].setAttribute('idx',i+1);
		select[i].onchange = function(){_self.updateData(this);};
	}

	this.updateData = updateData;
	this.updateData('',1);

	function updateData(obj,f){

		if (!f) catno = "";

		idx++;
		var data = "mode=category&cid="+cid+"&front="+front;

		if (obj){
			idx = obj.getAttribute('idx');
			data += "&catno="+obj.value+"&depth="+idx;
		}
		if (idx) _self.value = select[idx-1].value;
		if (idx==len) return;

		for (var i=len-1;i>=idx;i--) select[i].length = 1;

		$j.ajax({
			type: "POST",
			url: "../../ajax.php",
			data: data,
			success: function(msg){
				var ret = evalJSON(msg);
				for (var x in ret){
					var div = ret[x].split("|");
					if (div[1]) select[idx].options[select[idx].options.length] = new Option(div[1],div[0]);
				}
				select[idx].scrollTop = 0;

				if (flag=catno.substring(0,idx*3+3)){
					if (flag.length==idx*3+3){
						select[idx].value = flag;
						if (idx<len-1) updateData(select[idx],1);
					}
				}
			}
		});
	}
}