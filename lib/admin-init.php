<?php
//* Add network menu
//* Located under Settings
//* Super Admin (MS) / Admin (Single) only
//* Uses /lib/shortcode.php

if (is_multisite()) {
	add_action('network_admin_menu', 'pmt_add_menu_ms'); // multisite menu
} else {
	add_action('admin_menu', 'pmt_add_menu_single'); // single menu
}

//* Initiate options page
function pmt_add_menu_ms() {
	add_submenu_page( 'settings.php', 'Pro Mime Types', 'Pro Mime Types', 'manage_options', 'pmt_admin_page', 'pmt_admin_page' );
}
function pmt_add_menu_single() {
	add_submenu_page( 'options-general.php', 'Pro Mime Types', 'Pro Mime Types', 'manage_options', 'pmt_admin_page', 'pmt_admin_page' );
}

function pmt_wp_version( $version = '4.0.0', $compare = '>=' ) {
	global $wp_version;

	if ( version_compare( $wp_version, $version, $compare ) )
		return true;

	return false;
}


//* Render page
function pmt_admin_page() {
	global $promimes;

	if ( false == pmt_site_admin() ) {
		return false; // There you go. Only (super-)admin.
	}

	//* # Register settings
	foreach ($promimes as $mime) {
		$type = esc_attr($mime[0]);

		register_setting( 'pmt_tab_1', 'pmt_mime_type_' . $type );
		register_setting( 'pmt_tab_1', 'pmt_mime_type_' . $type . '_pro' );
	}

	//* # Tab 1
	add_settings_section(
		'pmt_pluginPage_section_tab_1', //id
		__( 'Pro Mime Types Options', 'promimetypes' ), //title
		'pmt_settings_section_callback_tab_1', //callback function
		'pmt_tab_1' //menu_slug
	);

	//* # Tab 2
	add_settings_section(
		'pmt_pluginPage_section_tab_2', //id
		__( 'Globally Active Mime Types', 'promimetypes' ), //title
		'pmt_settings_section_callback_tab_2', //callback function
		'pmt_tab_2' //menu_slug
	);

	$wp430 = pmt_wp_version( '4.3.0', '>=' );

	//* # Init settings
	?>
	<div class="wrap">
		<?php if ( $wp430 ) : ?>
	        <h1>Pro Mime Types</h1>
        <?php else : ?>
            <h2>Pro Mime Types</h2>
        <?php endif; ?>

		<?php if( isset( $_POST['pmt_submit'] ) ) { ?>
			<div id="message" class="updated notice is-dismissible">
				<p>
					<?php _e( 'Options are Saved', 'promimetypes' ) ?>
				</p>
			</div>
		<?php }

		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'display_options'; //save tab location
		?>

		<h2 class="nav-tab-wrapper">
			<a href="?page=pmt_admin_page&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Options', 'promimetypes' ); ?></a>
			<a href="?page=pmt_admin_page&tab=list_active_mime_types" class="nav-tab <?php echo $active_tab == 'list_active_mime_types' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'View globally active Mime Types', 'promimetypes' ); ?></a>
		</h2>

		<?php
			//* # display tab content
			if( $active_tab == 'display_options' ) {
				do_settings_sections( 'pmt_tab_1' );
			} else {
				do_settings_sections( 'pmt_tab_2' );
			}
		?>

	</div>
	<?php
}
