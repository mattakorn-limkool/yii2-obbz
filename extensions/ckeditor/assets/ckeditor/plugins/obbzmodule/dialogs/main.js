var width = 1000, height = 480;
//var baseUrl = 'http://localhost/kamol/backend/web/editor/flexible-module/create';
//var baseUrl = CKE_flexModuleIframeUrl;
var dialogTitle = 'Flexible Module';
//console.log(CKE_flexModuleIframeUrl);
CKEDITOR.dialog.addIframe('mainDialog', dialogTitle, CKE_flexModuleIframeUrl, width, height,
    function() { // onContentLoad

        //console.log(this);
        iframeid=this._.frameId;/*get the iframe*/
        //editorObj = this._.editor;
    },
    { // userDefinition
        onOk : function(event)// Dialog onOk callback.
        {

            event.data.hide = false;
            // submit dialog iframe
            iframContent = $('#' + iframeid).contents();
            form = iframContent.find('form');
            $(form).submit();

            //this._.editor;
        },
        //onCancel: function(event){
        //    console.log(event);
        //},
        onShow: function(){
            //var oEditor = window.parent.InnerDialogLoaded();
            //var myVariable = CKEDITOR.Config['myVariable'];
            //console.log(CKEDITOR.editor);
            this.definition.getContents('iframe').elements[0].src = CKE_flexModuleIframeUrl;

        },

        // custom for  iframe return to editor
        onDataSuccess: function(htmlData, action, modelId){

            dialog = CKEDITOR.dialog.getCurrent();
            editor = dialog._.editor;

            if(action == 'insert'){
                editor.insertHtml(htmlData);
            }else{
                var editorList = editor.document.$.all;
                for (var i = 0; i < editorList.length; i++) {
                    //$('iframe.obbz-flexible-module',editorList[i]).each(function(){
                    //   var flexId = $(this).data('flexmodule-id');
                    //    var url = new URL(CKE_flexModuleIframeUrl);
                    //    var curId = url.searchParams.get("id");
                    //
                    //    if(curId === flexId){
                    //        console.log(flexId);
                    //        $(this).attr( 'src', function ( i, val ) { return val; });
                    //    }
                    //});
                    // todo - just reload own element
                    $('iframe.obbz-flexible-module',editorList[i]).attr( 'src', function ( i, val ) { return val; });
                    //console.log($(editorList[i]).html());
                }

                //console.log($("iframe.flexible-module-iframe",editor.document.$.all).html());
                $(editor.document.$.activeElement).attr( 'src', function ( i, val ) { return val; });
            }

            dialog.hide();

        },
        onDataError: function(data){

        }
    }
);


//CKEDITOR.dialog.add('mainDialog', function (editor) {
//    return {
//        title: 'Insert Flexible Module',
//        minWidth: 600,
//        minHeight: 400,
//        contents: [
//            {
//                id: 'tab-slide',
//                label: 'Slide Images',
//                elements: [
//                    {
//                        type : 'iframe',
//                        src : 'http://localhost/kamol/backend/web/content/create?key=page',
//                        //width : width,
//                        //height : CKEDITOR.env.ie ? height - 10 : height ,
//                        onContentLoad : function()
//                        {
//                        }
//                    },
//                    //{
//                    //    type: 'text',
//                    //    id: 'abbr',
//                    //    label: 'Abbreviation',
//                    //    validate: CKEDITOR.dialog.validate.notEmpty("Abbreviation field cannot be empty.")
//                    //}
//                ]
//            }
//
//        ],
//        onOk: function () {
//
//            var dialog = this;
//
//            //var abbr = editor.document.createElement('div');
//            //abbr.setText(dialog.getValueOf('tab-basic', 'abbr'));
//            //
//            //editor.insertElement(abbr);
//        }
//    };
//});