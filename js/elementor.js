// elementor.js
// Theme Name:     Unavoidable Disaster
// Theme URI:      https://UnavoidableDisaster.com
// Author:         Wes Modes (wmodes@gmail.com)
// Author URI:     https://modes.io
// Template:       understrap
// Version:        0.1.0
// Dependencies:   jQuery4

// wait for everything to be loaded
jQuery(document).ready(function(){

	console.log("elementor JS working");
	// set default for Insert Page to template
	jQuery(".insertpage-format-select").val("template");
	jQuery(".insertpage-template-select").val("loop-templates/content-thing.php");
	// set default position
	if (jQuery("select[data-setting=_element_width]").val() === "") {
		jQuery("select[data-setting=_element_width]").val("auto");
	}
	if (jQuery("select[data-setting=_position]").val() === "") {
		jQuery("select[data-setting=_position]").val("absolute");
	}

})