jQuery( function ( $ ) {
    $( '#siteorigin-admin-bar' ).show();

    var position = function () {
        $( '#siteorigin-admin-bar' ).css( 'top', 0 );
        if ( $( 'body' ).hasClass( 'branch-3-3' ) ) {
            // WordPress 3.3 has a slightly different layout
            $( '#siteorigin-admin-bar' ).css( 'top', $( '#wpadminbar' ).outerHeight() );
            $( '#wpcontent, #adminmenu' ).css( 'padding-top', $( '#siteorigin-admin-bar' ).outerHeight() + $( '#wpadminbar' ).outerHeight() );
        }
        else {
            $( '#wpcontent, #adminmenu' ).css( 'padding-top', $( '#siteorigin-admin-bar' ).outerHeight() );
        }
    }

    position();

    var interval = setInterval( position, 250 );

    $( '#siteorigin-admin-bar .dismiss' ).click( function () {
        clearInterval( interval );
        $( '#siteorigin-admin-bar' ).slideUp( 'fast' );
        if ( $( 'body' ).hasClass( 'branch-3-3' ) ) {
            $( '#wpcontent, #adminmenu' ).animate( {'padding-top':$( '#wpadminbar' ).outerHeight()}, 'fast' );
        }
        else {
            $( '#wpcontent, #adminmenu' ).animate( {'padding-top':0}, 'fast' );
        }

        // Send the message to the server to dismiss this bar
        $.post(
            ajaxurl + '?action=siteorigin_admin_dismiss_bar',
            { bar:$( '#siteorigin-admin-bar' ).attr( 'data-type' ) }
        );

        return false;
    } );
} );