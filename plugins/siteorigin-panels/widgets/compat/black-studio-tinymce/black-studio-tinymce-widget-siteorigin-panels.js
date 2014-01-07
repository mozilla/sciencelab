/**
 * @copyright Black Studio http://www.blackstudio.it
 * @license GPL 2.0
 */

(function($) {

	// Set wpActiveEditor variables used when adding media from media library dialog
	$(document).on('click', '.editor_media_buttons a', function() {
		var $widget_inside = $(this).closest('div.ui-dialog')
		wpActiveEditor = $('textarea[id^=widget-black-studio-tinymce]', $widget_inside).attr('id');	
	});

	// Deactivate visual editor
	function black_studio_deactivate_visual_editor_site_origin_panels(id) {
		if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
			if (typeof(tinyMCE.get(id)) == "object" && typeof( tinyMCE.get(id).getContent ) == "function" ) {
				var content = tinyMCE.get(id).getContent();
				tinyMCE.execCommand("mceRemoveControl", false, id);
				$('textarea#'+id).val(content);
			}
		}
	}
	
	// Activate visual editor upon dialog opening
	$(document).on('panelsopen', function() {
		var $text_area = $('.ui-dialog:visible textarea[id^=widget-black-studio-tinymce]');
		$('.ui-dialog:visible input[id^=widget-black-studio-tinymce][id$=type][value=visual]').each(function() {
			$('.ui-dialog:visible a[id^=widget-black-studio-tinymce][id$=visual]').click();
		});

	});
	
	// Deactivate visual editor upon dialog saving ("Done" button)
	$(document).on('panelsdone', function() {
		var $text_area = $('.ui-dialog:visible textarea[id^=widget-black-studio-tinymce]');
		if ($text_area.length > 0) {
			var id = $text_area.attr('id');
			black_studio_deactivate_visual_editor_site_origin_panels(id);
		}
	});
	
	// Deactivate visual editor upon dialog closing ("X" button)
	$(document).on('dialogbeforeclose', function() {
        var $text_area = $('.ui-dialog:visible textarea[id^=widget-black-studio-tinymce]');
		if ($text_area.length > 0) {
			var id = $text_area.attr('id');
			black_studio_deactivate_visual_editor_site_origin_panels(id);
		}
	});

})(jQuery); // end self-invoked wrapper function