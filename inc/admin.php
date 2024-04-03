<?php
/**
 * @package Pro_Mime_Types\Admin
 */

namespace Pro_Mime_Types\Admin;

\defined( 'Pro_Mime_Types\VERSION' ) or die;

use const \Pro_Mime_Types\{
	ALLOWED_MIME_TYPES_OPTIONS_NAME,
	PLUGIN_DIR_PATH,
};

use function \Pro_Mime_Types\is_network_mode;

/**
 * Pro Mime Types plugin
 * Copyright (C) 2023 - 2024 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as published
 * by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The admin page hook.
 *
 * @since 2.0.0
 */
const PAGE_HOOK = 'pro-mime-types';

/**
 * The save action name.
 *
 * @since 2.0.0
 */
const SAVE_ACTION = 'pmt_save_settings';

/**
 * The saved action response name.
 *
 * @since 2.0.0
 */
const SAVED_RESPONSE = 'pmt_updated';

/**
 * The save nonce name and action.
 *
 * @since 2.0.0
 */
const SAVE_NONCE = [
	'name'   => '_pmt_nonce',
	'action' => '_pmt_nonce_save_settings',
];

/**
 * Tells whether the current user can manage Pro Mime Types's options.
 *
 * @since 2.1.0
 * @access private
 *
 * @return bool
 */
function _current_user_can_manage_settings() {
	return \current_user_can( \is_multisite() ? 'manage_network' : 'manage_options' );
}

/**
 * Binds the plugin on admin page load.
 *
 * @since 2.0.0
 * @access private
 *
 * @param string $page The submenu page name.
 */
function _bind_admin_hook( $page ) {
	\add_action( "load-$page", __NAMESPACE__ . '\_init_admin_page' );
}

/**
 * Registers the multisite menu.
 *
 * @hook network_admin_menu 10
 * @since 2.0.0
 * @access private
 */
function _register_network_admin_menu() {
	_bind_admin_hook(
		\add_submenu_page(
			'settings.php',
			'Pro Mime Types',
			'Pro Mime Types',
			'manage_network',
			PAGE_HOOK,
			__NAMESPACE__ . '\_display_admin_page',
		)
	);
}

/**
 * Registers the single-site menu.
 *
 * @hook admin_menu 10
 * @since 2.0.0
 * @access private
 */
function _register_admin_menu() {
	_bind_admin_hook(
		\add_submenu_page(
			'options-general.php',
			'Pro Mime Types',
			'Pro Mime Types',
			\is_multisite() ? 'manage_network' : 'manage_options',
			PAGE_HOOK,
			__NAMESPACE__ . '\_display_admin_page',
		)
	);
}

/**
 * Initializes all that's necessary for the admin page.
 *
 * @hook load-settings_page_pro-mime-types 10
 * @since 2.0.0
 * @access private
 */
function _init_admin_page() {
	\add_filter(
		'admin_body_class',
		/**
		 * Adds a class to the body HTML tag.
		 *
		 * Filters the body class string for admin pages and adds our own class for easier styling.
		 *
		 * @since 2.0.0
		 *
		 * @param string $body_class The body class string.
		 * @return string The modified body class string.
		 */
		fn( $body_class ) => "$body_class pmt-settings ",
	);

	\add_action(
		'admin_enqueue_scripts',
		function () {
			$dir_url = \plugin_dir_url( \Pro_Mime_Types\PLUGIN_BASE_FILE );
			$min     = \defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			\wp_enqueue_style(
				'pmt-admin-styles',
				"{$dir_url}lib/admin{$min}.css",
				[ 'dashicons', 'common', 'forms' ],
				\Pro_Mime_Types\VERSION,
			);
			\wp_enqueue_script(
				'pmt-admin-script',
				"{$dir_url}lib/admin{$min}.js",
				[],
				\Pro_Mime_Types\VERSION,
				true
			);
		}
	);
}

/**
 * Outputs the administrative page.
 *
 * @see _register_admin_menu()
 * @see _register_network_admin_menu()
 * @since 2.0.0
 * @access private
 */
function _display_admin_page() {
	include PLUGIN_DIR_PATH . 'views/admin.php';
}

/**
 * Outputs the administrative page tab content.
 *
 * @hook pmt_admin_tab_content 10
 * @since 2.0.0
 * @access private
 *
 * @param string $current_tab The current admin tab.
 */
function _output_tab_content( $current_tab ) {
	switch ( $current_tab ) {
		case '':
			include PLUGIN_DIR_PATH . 'views/tab-options.php';
			break;
		case 'allowed-types':
			include PLUGIN_DIR_PATH . 'views/tab-allowed-types.php';
	}
}

/**
 * Processes settings submission, and redirects user back to admin page.
 *
 * @hook admin_post_pmt_save_settings 10
 * @since 2.0.0
 * @access private
 */
function _process_settings_submission() {

	\check_admin_referer( SAVE_NONCE['action'], SAVE_NONCE['name'] );

	if ( ! _current_user_can_manage_settings() )
		\wp_die(
			\esc_html__( 'Sorry, you are not allowed to update Pro Mime Types settings.', 'pro-mime-types' ),
			403
		);

	// Filter all non-true values in POST, get all keys, and then comma-separate them.
	$settings = implode(
		',',
		array_keys(
			array_filter(
				(array) ( $_POST[ ALLOWED_MIME_TYPES_OPTIONS_NAME ] ?? [] )
			)
		)
	);

	if ( is_network_mode() ) {
		$result = \get_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, $settings ) !== $settings
			? (int) \update_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, $settings )
			: 2;
	} else {
		$result = \get_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, $settings ) !== $settings
			? (int) \update_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, $settings )
			: 2;
	}

	\wp_safe_redirect( \add_query_arg( SAVED_RESPONSE, $result, \wp_get_referer() ) );
	exit;
}

/**
 * Adds various links to the plugin row on the plugin's screen.
 *
 * @hook plugin_action_links_pro-mime-types/promimetypes.php
 * @since 2.1.0
 * @access private
 *
 * @param array $links The current links.
 * @return array The plugin links.
 */
function _add_plugin_action_links( $links ) {
	return _current_user_can_manage_settings()
		? array_merge(
			[
				'settings' => sprintf(
					'<a href="%s">%s</a>',
					is_network_mode()
						? \esc_url( \network_admin_url( 'settings.php?page=' . PAGE_HOOK ) )
						: \esc_url( \admin_url( 'admin.php?page=' . PAGE_HOOK ) ),
					\esc_html__( 'Settings', 'pro-mime-types' ),
				),
			],
			$links,
		)
		: $links;
}

/**
 * Adds various links to the plugin row on the plugin's screen.
 *
 * @hook network_admin_plugin_action_links_pro-mime-types/promimetypes.php
 * @since 2.1.0
 * @access private
 *
 * @param array $links The current links.
 * @return array The plugin links.
 */
function _add_plugin_network_action_links( $links ) {
	// No need to check for capabilities here. activate_plugins implies manage_options on single site. Technically, a bug.
	return array_merge(
		[
			'settings' => sprintf(
				'<a href="%s">%s</a>',
				\esc_url( \network_admin_url( 'settings.php?page=' . PAGE_HOOK ) ),
				\esc_html__( 'Settings', 'pro-mime-types' ),
			),
		],
		$links,
	);
}
