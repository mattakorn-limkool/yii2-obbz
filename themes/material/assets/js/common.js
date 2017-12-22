var Obbz = {
    /** toast alert **/
    alert : {
        message : function(type, message, title){
            $.growl({
                title: title ? title : '',
                message: message,
                url: ''
            },{
                type: type,

                offset: {
                    x: 20,
                    y: 85
                },
                url_target: '_blank',
                mouse_over: true,
                icon_type: 'class'

            });
        },

        success : function(message, title){
            Obbz.alert.message('success', message, title);
        },

        error : function(message, title){
            Obbz.alert.message('danger', message, title);
        },

        info : function(message, title){
            Obbz.alert.message('info', message, title);
        },

        warning : function(message, title){
            Obbz.alert.message('warning', message, title);
        }
    },

    /** loading indicator **/
    beginLoading: function(){
        $(".page-loader").show();
    },
    endLoading: function(){
        $(".page-loader").hide();
    }
};

$('.gallery-img-detail').lightGallery({
    thumbnail:true
    //enableTouch: true
});
$(".disabled-link").click(function(event) {
    event.preventDefault();
});
//if(typeof(CKEDITOR) !== 'undefined'){
//    CKEDITOR.config.toolbar = [
//        //['Styles','Format','Font','FontSize'],
//        //'/',
//        //['Bold','Italic','Underline','StrikeThrough','-','Undo','Redo','-','Cut','Copy','Paste','Find','Replace','-','Outdent','Indent','-','Print'],
//        ['Bold','Italic','Underline','StrikeThrough','-','Undo','Redo','-','Cut','Copy','Paste','-','Outdent','Indent'],
//        //'/',
//        ['NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
//        //['Image','Table','-','Link','Flash','Smiley','TextColor','BGColor','Source']
//        ['Image','-','Link','TextColor','BGColor']
//    ] ;
//
//}
