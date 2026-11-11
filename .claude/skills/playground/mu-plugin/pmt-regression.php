<?php
/**
 * @package Pro_Mime_Types\Playground
 */

defined( 'ABSPATH' ) or die;

/**
 * Pro Mime Types plugin
 * Copyright (C) 2026 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
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

add_filter(
	'wp_plugin_regression_frame',
	'pmt_playground_frame',
	10,
	2,
);
add_action(
	'template_redirect',
	'pmt_playground_mime_dump',
	0,
);

/**
 * Accepts the default reading frame so capture can run.
 *
 * @since 2.2.0
 *
 * @param bool   $handled Whether a consumer already applied the frame.
 * @param string $name    Frame name.
 * @return bool
 */
function pmt_playground_frame( $handled, $name ) {

	if ( $handled )
		return $handled;

	return 'blog' === $name;
}

/**
 * Prints a stable plain-text MIME dump and exits.
 *
 * Capture stores the whole body because the response is not HTML.
 * The route is `/?pmt-regression=mimes`.
 *
 * @since 2.2.0
 *
 * @return void
 */
function pmt_playground_mime_dump() {

	$which = filter_input( INPUT_GET, 'pmt-regression' );

	if ( 'mimes' !== $which ) return;

	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );

	if ( ! defined( 'Pro_Mime_Types\\VERSION' ) ) {
		echo "plugin: inactive\n";
		exit;
	}

	$version  = \Pro_Mime_Types\VERSION;
	$network  = '0';
	$db       = '';
	$settings = '';

	if (
		function_exists(
			'Pro_Mime_Types\\is_network_mode',
		)
		&& \Pro_Mime_Types\is_network_mode()
	) {
		$network = '1';
	}

	if ( function_exists(
		'Pro_Mime_Types\\get_db_version',
	) ) {
		$db = (string) \Pro_Mime_Types\get_db_version();
	}

	if ( function_exists(
		'Pro_Mime_Types\\get_allowed_mime_types_settings',
	) ) {
		$stored = \Pro_Mime_Types\get_allowed_mime_types_settings( false );

		if ( is_string( $stored ) )
			$settings = $stored;
	}

	echo "version: $version\n";
	echo "network: $network\n";
	echo "db: $db\n";
	echo "settings: $settings\n";

	$map = [];

	if (
		defined(
			'Pro_Mime_Types\\SUPPORTED_MIME_TYPES',
		)
		&& function_exists(
			'Pro_Mime_Types\\get_allowed_mime_types',
		)
	) {
		$map = \Pro_Mime_Types\get_allowed_mime_types();
	}

	if ( ! is_array( $map ) )
		$map = [];

	ksort( $map, SORT_STRING );

	foreach ( $map as $extension => $mime )
		echo "allowed: $extension $mime\n";

	exit;
}
