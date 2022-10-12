<?
include_once "../_header.php";

$micro = explode(" ",microtime());
$storageKey = date("Ymd")."-".substr($micro[1].sprintf("%03d",floor($micro[0]*1000)), -6);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <title>File Upload</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    
    <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet" />
    <link href="assets/css/style.min.css" rel="stylesheet" />
    <link href="assets/css/style-responsive.min.css" rel="stylesheet" />
    <link href="assets/css/theme/default.css" rel="stylesheet" id="theme" />
    <!-- ================== END BASE CSS STYLE ================== -->
    
    <!-- ================== BEGIN PAGE LEVEL CSS STYLE ================== -->
    <link href="assets/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet" />
    <link href="assets/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" />
    <link href="assets/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" />
    <!-- ================== END PAGE LEVEL CSS STYLE ================== -->
    
    <!-- ================== BEGIN BASE JS ================== -->
    <script src="assets/plugins/pace/pace.min.js"></script>
    <!-- ================== END BASE JS ================== -->
</head>
<body>
    <!-- begin #page-loader -->
    <div id="page-loader" class="fade in"><span class="spinner"></span></div>
    <!-- end #page-loader -->
    
    <!-- begin #page-container -->
    
    <div id="page-container" class="page-without-sidebar page-header-fixed">
        
        <!-- begin #content -->
        <div id="content" class="content">
          <!-- begin #header -->
          <div id="header" class="header navbar navbar-default navbar-fixed-top">
             <div class="container-fluid">
                <div class="navbar-header">
                   <a href="javascript:parent.closeLayer();" class="navbar-brand"><span class="navbar-logo"></span><?=_("파일 업로드")?></a>
                </div>
             </div>
          </div>            

            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title"><?=_("파일 업로드")?></h4>
                </div>
                <div class="panel-body">            
                    <blockquote class="f-s-14">
                        <p>
                        <?=_("업로드 할 수 있는 최대 파일 크기는")?> <strong>150MB</strong> <?=_("입니다.")?><br>
                        <?=_("기본 파일")?> (<strong>ZIP, PDF</strong>), <?=_("이미지 파일")?> (<strong>JPG, GIF, PNG</strong>) <?=_("만 허용됩니다.")?>                        
                        </p>
                    </blockquote>
                    <form id="fileupload" action="http://files.ilark.co.kr/portal_upload/estm/file/jqupload.aspx" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="storage_code" value="<?=$storageKey?>" />
                        <input type="hidden" name="center_id" value="<?=$cfg_center[center_cid]?>" />
                        <input type="hidden" name="mall_id" value="<?=$cid?>" />
                 
                        <div class="row fileupload-buttonbar">
                            <div class="col-md-7">
                                <span class="btn btn-success fileinput-button">
                                    <i class="fa fa-plus"></i>
                                    <span><?=_("추가")?>...</span>
                                    <input type="file" name="files[]" multiple>
                                </span>
                                <button type="submit" class="btn btn-primary start">
                                    <i class="fa fa-upload"></i>
                                    <span><?=_("업로드")?></span>
                                </button>
                                <button type="reset" class="btn btn-warning cancel">
                                    <i class="fa fa-ban"></i>
                                    <span><?=_("취소")?></span>
                                </button>
                                <button type="button" class="btn btn-danger delete">
                                    <i class="glyphicon glyphicon-trash"></i>
                                    <span><?=_("삭제")?></span>
                                </button>
                                <!-- The global file processing state -->
                                <span class="fileupload-process"></span>
                            </div>
                            <!-- The global progress state -->
                            <div class="col-md-5 fileupload-progress fade">
                                <!-- The global progress bar -->
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                </div>
                                <!-- The extended global progress state -->
                                <div class="progress-extended">&nbsp;</div>
                            </div>
                        </div>
                        <!-- The table listing the files available for upload/download -->
                        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
                    </form>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label"></label>
                <div class="col-md-7">
                    <button type="button" style="margin-bottom: 15px;" class="btn btn-primary" onclick="orderSubmit();"><?=_("주문하기")?></button>
                    <button type="button" style="margin-bottom: 15px;" class="btn btn-default" onclick="parent.closeLayer();"><?=_("닫  기")?></button>
                </div>
            </div>            
        </div>
        <!-- end #content -->
        
        <!-- The blueimp Gallery widget -->
        <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
            <div class="slides"></div>
            <h3 class="title"></h3>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <a class="close">×</a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
        </div>
        
        <!-- begin scroll to top btn -->
        <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
        <!-- end scroll to top btn -->

    </div>
    <!-- end page container -->
    
    <!-- The template to display files available for upload -->
    <script id="template-upload" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-upload fade">
                <td class="col-md-1">
                    <span class="preview"></span>
                </td>
                <td>
                    <p class="name">{%=file.name%}</p>
                    <strong class="error text-danger"></strong>
                </td>
                <td>
                    <p class="size">Processing...</p>
                    <div class="progress progress-striped active"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
                </td>
                <td>
                    {% if (!i && !o.options.autoUpload) { %}
                        <button class="btn btn-primary btn-sm start" disabled>
                            <i class="fa fa-upload"></i>
                            <span><?=_("업로드")?></span>
                        </button>
                    {% } %}
                    {% if (!i) { %}
                        <button class="btn btn-white btn-sm cancel">
                            <i class="fa fa-ban"></i>
                            <span><?=_("취소")?></span>
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
                <td>
                    <span class="preview">
                        {% if (file.thumbnailUrl) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                        {% } %}
                    </span>
                </td>
                <td>
                    <p class="name">
                        {% if (file.url) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                        {% } else { %}
                            <span>{%=file.name%}</span>
                        {% } %}
                    </p>
                    {% if (file.error) { %}
                        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                    {% } %}
                </td>
                <td>
                    <span class="size">{%=o.formatFileSize(file.size)%}</span>
                </td>
                <td>
                    {% if (file.deleteUrl) { %}
                        <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                            <i class="glyphicon glyphicon-trash"></i>
                            <span><?=_("삭제")?></span>
                        </button>
                        <input type="checkbox" name="delete" value="1" class="toggle">
                    {% } else { %}
                        <button class="btn btn-warning cancel">
                            <i class="glyphicon glyphicon-ban-circle"></i>
                            <span><?=_("취소")?></span>
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
    
    <!-- ================== BEGIN BASE JS ================== -->
    <script src="assets/plugins/jquery/jquery-1.9.1.min.js"></script>
    <script src="assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
    <script src="assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!--[if lt IE 9]>
        <script src="assets/crossbrowserjs/html5shiv.js"></script>
        <script src="assets/crossbrowserjs/respond.min.js"></script>
        <script src="assets/crossbrowserjs/excanvas.min.js"></script>
    <![endif]-->
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="assets/plugins/jquery-cookie/jquery.cookie.js"></script>
    <!-- ================== END BASE JS ================== -->
    
    <!-- ================== BEGIN PAGE LEVEL JS ================== -->
    <script src="assets/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/vendor/tmpl.min.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/vendor/load-image.min.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
    <script src="assets/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/jquery.fileupload-process.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/jquery.fileupload-image.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/jquery.fileupload-audio.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/jquery.fileupload-video.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
    <script src="assets/plugins/jquery-file-upload/js/jquery.fileupload-ui.js"></script>
    <!--[if (gte IE 8)&(lt IE 10)]>
        <script src="assets/plugins/jquery-file-upload/js/cors/jquery.xdr-transport.js"></script>
    <![endif]-->
    <script src="assets/js/form-multiple-upload.demo.js"></script>
    <script src="assets/js/apps.min.js"></script>
    <!-- ================== END PAGE LEVEL JS ================== -->
    
    <script>
        $(document).ready(function() {
            App.init();
            FormMultipleUpload.init();
        });

        function orderSubmit() {
            //console.log($("input[name='delete']").length);
            //console.log('<?=$storageKey?>');

            if($("input[name='delete']").length > 0) {
                parent.initJQOrder('UPLOAD','NEW','<?=$storageKey?>')
            }
            else {
                //파일이 없어도 주문이 가능하게 처리 2015.06.24 by kdk            
                //메세지 출력 처리 2015.12.23 by kdk
                if (confirm('첨부된 파일이 없습니다.\n파일없이 주문하시겠습니까?')) {
                    parent.initJQOrder('NOFILES','NEW','')
                }
                else {
                    return false;
                }
            }
        }
    </script>

</body>
</html>

<? //include_once "../_pfooter.php"; ?>