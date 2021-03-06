/**
 * (c) Greg Priday, freely distributable under the terms of the GPL 2.0 license.
 */

jQuery( function ( $ ) {
    var minPrice = Number( $('#theme-upgrade input[name=variable_pricing_custom]').attr('min') );

    // Handle clicking the play button
    $('#theme-upgrade #click-to-play').click(function(){
        // Only load the video from Vimeo when the user clicks play
        $(this).replaceWith('<iframe src="http://player.vimeo.com/video/' + $(this).data('video-id') + '?title=0&byline=0&portrait=0&autoplay=1" width="640" height="362" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');
        return false;
    })

    $( '#theme-upgrade .buy-button').click(function(e){
        e.preventDefault();
        $(this).closest('form').submit();
        $( '#theme-upgrade-info' ).slideDown();
        $( 'html, body' ).animate( {'scrollTop':0} );
        return false;
    });

    $('#theme-upgrade #purchase-form').submit(function(){
        window.open('', 'paymentwindow', 'width=960,height=800,resizeable,scrollbars');
        this.target = 'paymentwindow';
    });

    $('#theme-upgrade #purchase-form .options input[type=radio]').change(function(){
        var val = $(this).val();
        if($(this).hasClass('custom-price')) {
            val = $('#theme-upgrade #purchase-form .options input[name=variable_pricing_custom]').val();
            val = parseFloat(val).toFixed(2);
            if(isNaN(val)) val = minPrice;
            val = Math.max(val,minPrice);
        }

        $('#theme-upgrade #purchase-form input[name=amount]').val(val);
        $('#theme-upgrade #purchase-form .variable-pricing-submit em').html('$'+val);

        if(val >= 15) $('#theme-upgrade .support-message').slideUp();
        else $('#theme-upgrade .support-message').slideDown();
    });

    $('#theme-upgrade #purchase-form .options input[name=variable_pricing_custom]').keyup(function(){
        var val = $(this).val().replace(/[^0-9.]/g, '');
        val = parseFloat(val).toFixed(2);
        if(isNaN(val)) val = minPrice;
        val = Math.max(val,minPrice);

        $(this).closest('form').find('.custom-price').click();

        $('#theme-upgrade #purchase-form input[name=amount]').val(val);
        $('#theme-upgrade #purchase-form .variable-pricing-submit em').html('$'+val);

        if(val >= 15) $('#theme-upgrade .support-message').slideUp();
        else $('#theme-upgrade .support-message').slideDown();
    }).change(function(){ $(this).keyup(); });


    // Display the form
    $( '#theme-upgrade-already-paid' ).click( function () {
        $( '#theme-upgrade-info' ).slideToggle();
        return false;
    } );

} );