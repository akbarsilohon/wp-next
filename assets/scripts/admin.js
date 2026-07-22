jQuery( document ).ready( function( $ ){
    let nextMainLogo;
    $('#next_logo').on('click', function( e ){
        e.preventDefault();
        if( nextMainLogo ){
            nextMainLogo.open();
            return;
        }

        nextMainLogo = wp.media({
            title: 'Change Logo',
            button: {
                text: 'Use Image'
            },
            multiple: false
        });

        nextMainLogo.on('select', function(){
            const attachment = nextMainLogo.state().get('selection').first().toJSON();
            $('#next_main_logo').val( attachment.url );
        });

        nextMainLogo.open();
    });
});