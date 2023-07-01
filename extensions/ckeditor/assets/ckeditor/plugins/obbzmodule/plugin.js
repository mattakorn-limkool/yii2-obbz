CKEDITOR.plugins.add( 'obbzmodule', {
    icons:'obbzmodule',
    requires: [ 'iframedialog' ],
    init: function( editor ) {
        var defaultConfig = {
            iframeParam : ''
        };
        var config = CKEDITOR.tools.extend(defaultConfig, editor.config.obbzmodule || {}, true);

        var mainDialogId = 'mainDialog';

        editor.addCommand( 'openFlexMainDialog', new CKEDITOR.dialogCommand( mainDialogId) );

        editor.addCommand( 'openFlexMainDialogCreate', {
            exec: function( editor, params ) {

                CKE_flexModuleIframeUrl = CKE_flexModuleIframeCreateUrl + config.iframeParam;
                console.log(CKE_flexModuleIframeUrl);
                editor.execCommand('openFlexMainDialog');
            }
        } );


        //editor.addCommand( 'openMainDialog', new CKEDITOR.dialogCommand( mainDialogId) );
        //editor.addCommand( 'obbzmodule', {
        //    exec: function( editor ) {
        //
        //        editor.insertHtml( '<div>test</div>' );
        //    }
        //});

        editor.ui.addButton( 'Obbzmodule', {
            label: 'Flexible Module',
            command: 'openFlexMainDialogCreate',
            toolbar: 'insert,1',
            //icon : this.path + 'icons/obbzmodule.png'
        });


        CKEDITOR.dialog.add( mainDialogId, this.path + 'dialogs/main.js' );
        editor.addContentsCss( this.path + 'css/style.css');

    }
});

