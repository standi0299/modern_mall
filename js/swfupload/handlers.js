/* Demo Note:  This demo uses a FileProgress class that handles the UI for displaying the file name and percent complete.
The FileProgress class is not part of SWFUpload.
*/


/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */
function fileQueued(file) {
	try {
	} catch (ex) {
		this.debug(ex);
	}
}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("멀티업로드가 가능한 파일수는 " + this.settings.file_upload_limit + "개입니다");
			//alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}
		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			alert("파일 사이즈가 너무 큽니다\n업로드는 " + this.settings.file_size_limit + "까지 가능합니다");
			//this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			return;
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:

			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:

			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:

			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {

		/* I want auto start the upload and I can do that here */
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

function uploadStart(file) {
	try {
	}
	catch (ex) {}
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var obj = document.getElementById(this.customSettings.fileBox);
		var fname = file.name;
		if (fname.length>20) fname = fname.substr(0,20) + "..";
		fname += " (" + percent + "%)";

		if (!obj.options.length || obj.options[obj.options.length-1].value!=file.id){
			obj.options[obj.options.length] = new Option(fname,file.id);
		} else {
			obj.options[obj.options.length-1].text = fname;
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadProgress_preview(file, bytesLoaded, bytesTotal) {
	alert(file);
}

function uploadSuccess(file, serverData) {
	try {
		if (typeof uploadUserfunc == "function") uploadUserfunc(file,evalJSON(serverData));
		if (serverData){
			var obj = document.getElementById(this.customSettings.fileBox);
			var data = evalJSON(serverData);
			for (var i=0;i<obj.options.length;i++){
				if (obj.options[i].value == file.id){
					obj.options[i].fno = data.no;
					obj.options[i].fsrc = data.fsrc;
					obj.options[i].ftype = data.ftype;
					obj.selectedIndex = i;
					file_preview_img(obj,'preview_img');
					break;
				}
			}
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	try {

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:

			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			break;
		default:
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
}

/*** User Func. ***/
function file_preview_img(obj,t)
{
	$(t).innerHTML = (obj.options[obj.selectedIndex].ftype!=0) ? "<img src='" + swfu.settings.post_params.dir + obj.options[obj.selectedIndex].fsrc + "' width=79 height=79>" : "";
}
function file_delete(obj)
{
	obj = $(obj);
	var idx = obj.selectedIndex;
	if (idx<0){
		alert("삭제할 파일을 선택해주세요");
		return;
	}
	if (!confirm('정말 삭제하시겠습니까?')) return;
	var fno = obj[idx].fno;
	obj[idx] = null;
}
function file_selected_first(obj,t)
{
	obj = $(obj);
	if (obj.options.length){
		obj.selectedIndex = 0;
		file_preview_img(obj,t);
	}
}

function file_preview_large(obj)
{
	obj = $(obj);
	window.open(swfu.settings.post_params.dir + obj.options[obj.selectedIndex].fsrc ,'','width=500,height=500,scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');
}