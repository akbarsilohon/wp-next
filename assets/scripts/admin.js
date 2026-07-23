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

    // Footer Logo ----------------
    let footerLogo;
    $('#next_footer_logo_change').on('click', function( e ){
        e.preventDefault();
        if( footerLogo ){
            footerLogo.open();
            return;
        }

        footerLogo = wp.media({
            title: 'Change footer logo',
            button: {
                text: 'Use Image'
            },
            multiple: false
        });

        footerLogo.on('select', function(){
            const attachment = footerLogo.state().get('selection').first().toJSON();
            $('#next_footer_logo').val( attachment.url );
        });

        footerLogo.open();
    });
});