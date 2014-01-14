jQuery(function($){
    var link = $('<a></a>')
        .addClass('nav-tab')
        .html(siteoriginAdminTab.text)
        .attr('href', siteoriginAdminTab.url);

    $('.nav-tab-wrapper a' ).last().after(link);

    if($('#typeselector' ).val() == 'author' && $('#s' ).val() == 'gpriday'){
        $('.nav-tab-wrapper a' ).removeClass('nav-tab-active');
        link.addClass('nav-tab-active');

        // hide the parts of the UI that aren't required
        $('.subsubsub, .tablenav.top.themes' ).hide();
        
        // Remove themes that don't support the page builder very well
        $('h3:contains(Pitch)' ).closest('.available-theme' ).remove();
    }
});