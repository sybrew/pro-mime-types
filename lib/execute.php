<?php
//* Executing the code to something that works

function pmt_upload_mimes( $existing_mimes = array() ) {
	global $wpdb, $blog_id, $promimes;
	
	$prolevelsql = '';

	//* run code
	foreach($promimes as $mime) {
		$type = esc_attr( $mime[0] );
		$label = esc_attr( $mime[1] );
		$danger = esc_attr( $mime[2] );
		
		if  ($danger == 0 ) {
			$default = '1';
		} else {
			$default = '2';
		}
		
		${'pmt_mime_type_'.$type} = get_site_option( 'pmt_mime_type_' . $type, $default );
		${'pmt_mime_type_'.$type.'_pro'} = get_site_option( 'pmt_mime_type_' . $type . '_pro', 0 );
		
		if ( is_prosites_active() ) {
			$prolevelsql = wp_cache_get('hmpl_pro_level_sql_' . $blog_id, 'hmpl_mainblog' );
			
			if ( false === $prolevelsql ) {
				$prolevelsql = $wpdb->get_var($wpdb->prepare("SELECT level FROM {$wpdb->base_prefix}pro_sites WHERE blog_ID = %d", $blog_id));
				wp_cache_set( 'hmpl_pro_level_sql_' . $blog_id, $prolevelsql, 'hmpl_mainblog', 14400 );
			}
			
		}
		
		//* Global active if pro sites isn't activated/installed.
		if (!is_prosites_active()) {
			if ( ${'pmt_mime_type_'.$type} == 1 ) {
				$existing_mimes[$type] = $label;
			} else if (${'pmt_mime_type_'.$type} == 2 ) {
				unset( $existing_mimes[$type] );
			} else {
				unset( $existing_mimes[$type] );
			}
		} else {
			if ( ( ${'pmt_mime_type_'.$type} == 1 ) && ${'pmt_mime_type_'.$type.'_pro'} <= $prolevelsql ) {
				$existing_mimes[$type] = $label;
			} else if ( ( ${'pmt_mime_type_'.$type} == 1 ) && ${'pmt_mime_type_'.$type.'_pro'} >= $prolevelsql ) {
				unset( $existing_mimes[$type] );
			} else if (${'pmt_mime_type_'.$type} == 2 ) {
				unset( $existing_mimes[$type] );
			} else {
				unset( $existing_mimes[$type] );
			}
		}
	}


	return $existing_mimes;
}
add_filter('upload_mimes', 'pmt_upload_mimes' );
