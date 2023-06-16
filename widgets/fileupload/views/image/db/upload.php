<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
     <div class="col-md-3 template-upload fade">
         <div class="thumbnail">
            <span class="preview"></span>

            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            <span class="label label-danger">Error:</span>
            <strong class="error text-danger"></strong>
            <div class="button-tools">
            {% if (!i) { %}
               <button class="btn btn-danger cancel" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="fa fa-times"></i>
                </button>
            {% } %}
            </div>
         </div>
     </div>

{% } %}
</script>