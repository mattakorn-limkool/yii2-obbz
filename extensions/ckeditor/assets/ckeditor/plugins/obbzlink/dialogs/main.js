
CKEDITOR.dialog.add( 'mainDialog', function( editor ) {
    function commitAttributes( element ) {
        var val = this.getValue();
        //element.attributes[ 'type' ] = 'button';
        element.attributes[ 'class' ] = 'btn-blog-default';
        element.attributes[ 'target' ] = '_blank';

        if ( val ) {
            if ( this.id == 'value' ){
                element.setHtml(val);
            }else{
                element.attributes[ this.id ] = val;
            }
            //    element.attributes[ 'data-cke-saved-name' ] = val;
        } else {
            delete element.attributes[ this.id ];
            if ( this.id == 'value' ){
                element.setHtml(val);
            }
            //if ( this.id == 'name' )
            //    delete element.attributes[ 'data-cke-saved-name' ];
        }
    }

    return {
        title: 'Dynamic Link',
        minWidth: 350,
        minHeight: 150,
        getModel: function( editor ) {
            var element = editor.getSelection().getSelectedElement();

            if ( element && element.is( 'input' ) ) {
                var type = element.getAttribute( 'type' );
                if ( type in { button: 1, reset: 1, submit: 1 } ) {
                    return element;
                }
            }

            return null;
        },
        onShow: function() {
            var element = this.getModel( this.getParentEditor() );

            if ( element ) {
                this.setupContent( element );
            }
        },
        onOk: function() {
            var editor = this.getParentEditor(),
                element = this.getModel( editor ),
                isInsertMode = this.getMode( editor ) == CKEDITOR.dialog.CREATION_MODE;

            var fake = element ? CKEDITOR.htmlParser.fragment.fromHtml( element.getOuterHtml() ).children[ 0 ] : new CKEDITOR.htmlParser.element( 'a' );
            this.commitContent( fake );

            var writer = new CKEDITOR.htmlParser.basicWriter();
            fake.writeHtml( writer );
            var newElement = CKEDITOR.dom.element.createFromHtml( writer.getHtml(), editor.document );

            if ( isInsertMode )
                editor.insertElement( newElement );
            else {
                newElement.replace( element );
                editor.getSelection().selectElement( newElement );
            }
        },
        contents: [ {
            id: 'info',
            label: 'Info',
            title:  'Info',
            elements: [
                {
                    id: 'value',
                    type: 'text',
                    bidi: true,
                    label: 'Display Text',
                    'default': '',
                    setup: function( element ) {
                        //this.setValue( element.getAttribute( 'value' ) || '' );
                        //this.setValue( element.data( 'cke-saved-name' ) || element.getAttribute( 'name' ) || '' );
                    },
                    validate: CKEDITOR.dialog.validate.notEmpty("Display Text field cannot be empty."),
                    commit: commitAttributes
                },
                {
                    id: 'href',
                    type: 'text',
                    label:  'Url',
                    //accessKey: 'V',
                    'default': '',
                    setup: function( element ) {
                        //this.setValue( element.getAttribute( 'cke-saved-url' ) || '' );
                    },
                    validate: CKEDITOR.dialog.validate.notEmpty("Url field cannot be empty."),
                    commit: commitAttributes
                },
                //{
                //    id: 'type',
                //    type: 'select',
                //    label: 'Type',
                //    'default': 'button',
                //    accessKey: 'T',
                //    items: [
                //        [ 'Button Link', 'button' ],
                //    ],
                //    setup: function( element ) {
                //        this.setValue( element.getAttribute( 'type' ) || '' );
                //    },
                //    commit: commitAttributes
                //}
            ]
        } ]
    };
} );