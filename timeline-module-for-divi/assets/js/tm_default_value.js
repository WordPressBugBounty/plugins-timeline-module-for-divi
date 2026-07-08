jQuery(function($) {

    var is_vb = $("body").hasClass("et-fb");
    
    $(window).on('load',()=>{

        is_vb &&
            window.ETBuilderBackend &&
            window.ETBuilderBackend.defaults && 
            ((window.ETBuilderBackend.defaults.tmdivi_timeline_story = {
                content:'Your description here...'
            })
        )

    });

});