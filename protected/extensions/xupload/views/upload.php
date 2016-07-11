<!-- Bootstrap styles -->

<?php Yii::app()->clientScript->registerCssFile('/css/list.css');?>
<div class="container">
    <!-- The file upload form used as target for the file upload widget -->
    <form id="fileupload" action="/upload/saveFiles" method="POST" enctype="multipart/form-data" onsubmit="return subForm()">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->       
        <!-- The table listing the files available for upload/download -->
        <div  style='width: 542px;height: 211px;overflow-y: scroll;overflow-x: hidden;border: solid 1px #d6d7d9;margin-left:-15px;'>
     	    <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
        </div>      
        <div class="row fileupload-buttonbar">
            <div  >
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button" style="background:#e1e9ec url(/images/rz_button1.gif) no-repeat ;border-color:#e1e9ec;height:33px;width:80px;" id='data_span'>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start" style="background:#e1e9ec url(/images/rz_button2.gif) no-repeat ;border-color:#e1e9ec;height:33px;width:80px;">
                </button>
                    <input type='submit' name='btn' id='sub' class='rzbutton3'  style='float: none;margin-left:10px;margin-top:10px;' value='提交'>
            </div>
            <!-- The global progress state -->
        </div>
    </form>
</div>
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
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade" >
        <td style='width:140px;'>
            <span class="preview"></span>
        </td>
        <td style='width:240px;'>
            <p class="name" style='width:240px;'>{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td style='width:100px;text-align:center;'>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled style="background:#e1e9ec url(/images/up.png) no-repeat ;border-color:#e1e9ec;height:20px;width:16px;padding:6px 8px;">
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel" style="background:#e1e9ec url(/images/del.png) no-repeat ;border-color:#e1e9ec;height:20px;width:16px;padding:6px 8px;">
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade" >
        <td style='width:140px;'>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td style='width:240px;'>
            <p class="name" style='width:240px;'>
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
        <td style='width:100px;text-align:center;'>
            {% if (file.deleteUrl) { %}
                <button onclick='delFileName(this);' class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %} style="background:#e1e9ec url(/images/del.png) no-repeat ;border-color:#e1e9ec;height:20px;width:16px;padding:6px 8px;" >
                </button>
            {% } else { %}
                <button class="btn btn-warning cancel" style="background:#e1e9ec url(/images/del.png) no-repeat ;border-color:#e1e9ec;height:20px;width:16px;padding:6px 8px;">
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<script>
	function delFileName(a){
		var delName = $(a).parents('tr').children('td:eq(1)').find('a').html();
		$("#myform input[class='fileNames']").each(function(){
			if($(this).val()==delName){
				$(this).remove();
			}
		})
	}
</script>

