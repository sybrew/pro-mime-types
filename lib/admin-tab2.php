<?php

//* Tab 2 section info
function pmt_settings_section_callback_tab_2() { 
	echo __( 'These are the currently active Mime Types.', 'promimetypes' );
	echo '<br />';
	$shortcode = hmpl_showmimetypes_shortcode();
	echo $shortcode;
}