var Obbz = {
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
    }
};
