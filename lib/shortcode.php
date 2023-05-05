<?php
//* Show allowed mime types through shortcode:
//* Super Admin (MS) / Admin (Single) only
//* Usage: [superadmin_showmimetypes]

function hmpl_showmimetypes_shortcode($params = array(), $content = null){
	if ( pmt_site_admin() ){
		$mimes = get_allowed_mime_types();
		$types = array();
		
		foreach ($mimes as $mime) {
			$types[] = '<li>' . str_replace('|', ', ', $mime ) . '</li>';
		}
		
		$output = '<p><h3>'
				. __('Currently allowed mime types:', 'promimetypes')
				. '</h3>'
				. '<ul style="font-size:120%;font-weight:600;">' . implode('', $types) . '</ul>'
				. '</p>'
				;
		
		return $output;
	} else {
		return;
	}
}
add_shortcode('superadmin_showmimetypes', 'hmpl_showmimetypes_shortcode' );