/**
 * Initializes embedded videos - (c) Greg Priday, freely distributable under the terms of the GPL 2.0 license.
 */
jQuery(function($){
    if( typeof $.fn.fitVids != 'undefined') {
        // Only use FitVids if it's been defined.
        $('.siteorigin-fitvids').fitVids();
    }
});