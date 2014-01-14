<?php

/**
 * Changes the Affiliate link for MetaSlider - a recommended plugin within Page Builder. Performed at priority 5 so themes take preference.
 *
 * @return string SiteOrigin affiliate link.
 * @filter metaslider_hoplink
 */
function siteorigin_panels_metaslider_hoplink(){
	return 'http://sorig.in/metaslider';
}
add_filter('metaslider_hoplink', 'siteorigin_panels_metaslider_hoplink', 5);