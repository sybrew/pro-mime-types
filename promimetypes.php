<?php
/**
 * Pro Mime Types
 *
 * @package   Pro_Mime_Types
 * @author    Sybre Waaijer
 * @copyright 2024 CyberWire B.V. (https://cyberwire.nl/)
 * @license   GPL-3.0
 * @link      https://github.com/sybrew/pro-mime-types
 *
 * @wordpress-plugin
 * Plugin Name: Pro Mime Types - Manage file media types
 * Plugin URI: https://wordpress.org/plugins/pro-mime-types/
 * Description: Enable or block MIME types and file extensions for media / file / attachment uploads through a nifty (network) admin menu.
 * Version: 2.1.1
 * Author: Sybre Waaijer
 * Author URI: https://cyberwire.nl/
 * License: GPLv3
 * Text Domain: pro-mime-types
 * Domain Path: /language
 * Requires at least: 5.3
 * Requires PHP: 7.4.0
 */

namespace Pro_Mime_Types;

\defined( 'ABSPATH' ) or die;

/**
 * Pro Mime Types plugin
 * Copyright (C) 2015 - 2024 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
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
 * The plugin version.
 *
 * @since 2.0.0
 */
const VERSION = '2.1.1';

/**
 * The plugin database version.
 *
 * @since 2.1.0
 */
const DB_VERSION = 2100;

/**
 * The plugin base file.
 *
 * @since 2.0.0
 */
const PLUGIN_BASE_FILE = __FILE__;

/**
 * The plugin base dir path.
 *
 * @since 2.0.0
 */
const PLUGIN_DIR_PATH = __DIR__ . '/';

/**
 * The MIME type danger level integers by name.
 *
 * @since 2.0.0
 */
const MIME_DANGER_LEVEL = [
	'safe'      => 0,
	'low-risk'  => 1,
	'high-risk' => 2,
	'dangerous' => 3,
];

/**
 * The MIME type option name.
 *
 * @since 2.0.0
 */
const ALLOWED_MIME_TYPES_OPTIONS_NAME = 'pro_mime_types_settings';

/**
 * The Pro Mime Types upgrade option name.
 *
 * @since 2.1.0
 */
const DB_VERSION_OPTION_NAME = 'pro_mime_types_db_version';

\add_action( 'plugins_loaded', __NAMESPACE__ . '\_plugin_init' );
/**
 * Initializes the plugin.
 *
 * @since 2.0.0
 * @access private
 */
function _plugin_init() {

	// Define late to allow plugin developers add filters.
	_define_supported_mime_types();

	if ( get_db_version() < DB_VERSION ) {
		require PLUGIN_DIR_PATH . 'inc/upgrade.php';

		// If upgrade/registration failed, halt further execution of plugin.
		if ( ! Upgrade\_register_or_upgrade_settings() ) return;
	}

	require PLUGIN_DIR_PATH . 'inc/init.php';

	\add_filter( 'mime_types', 'Pro_Mime_Types\Main\_register_mime_types' );
	\add_filter( 'upload_mimes', 'Pro_Mime_Types\Main\_register_allowed_upload_mimes' ); // Override at default.
	\add_filter( 'ext2type', 'Pro_Mime_Types\Main\_register_ext2type' ); // Override at default.

	\add_filter( 'getimagesize_mimes_to_exts', 'Pro_Mime_Types\Main\_register_all_imagesize_extensions' ); // Override at default.
	\add_filter( 'wp_video_extensions', 'Pro_Mime_Types\Main\_register_all_video_extensions' ); // Override at default.
	\add_filter( 'wp_audio_extensions', 'Pro_Mime_Types\Main\_register_all_audio_extensions' ); // Override at default.

	\add_filter( 'wp_check_filetype_and_ext', 'Pro_Mime_Types\Main\_allow_real_filetype_and_ext', 10, 5 ); // Override at default.

	\add_filter( 'post_mime_types', 'Pro_Mime_Types\Main\_register_post_mime_types' ); // Override at default.

	// Stop init here, all calls below are admin-only stuff.
	if ( ! \is_admin() ) return;

	require PLUGIN_DIR_PATH . 'inc/admin.php';

	$basename = \plugin_basename( PLUGIN_BASE_FILE );

	\load_plugin_textdomain( 'pro-mime-types', false, "$basename/language" );

	if ( is_network_mode() ) {
		\add_action( 'network_admin_menu', 'Pro_Mime_Types\Admin\_register_network_admin_menu' );
		\add_filter( "network_admin_plugin_action_links_$basename", 'Pro_Mime_Types\Admin\_add_plugin_network_action_links' );
	} else {
		\add_action( 'admin_menu', 'Pro_Mime_Types\Admin\_register_admin_menu' );
	}

	// Also add link when in network mode. This will then go the network settings page.
	\add_filter( "plugin_action_links_$basename", 'Pro_Mime_Types\Admin\_add_plugin_action_links' );

	\add_action( 'admin_post_' . Admin\SAVE_ACTION, 'Pro_Mime_Types\Admin\_process_settings_submission' );
	\add_action( 'pmt_admin_tab_content', 'Pro_Mime_Types\Admin\_output_tab_content' );
}

/**
 * Returns true when the plugin is in network mode; false otherwise.
 *
 * @since 2.0.0
 * @access public
 *
 * @return bool
 */
function is_network_mode() {
	static $memo;
	return $memo
		??= \is_multisite() && isset(
			\get_site_option( 'active_sitewide_plugins' )[ \plugin_basename( PLUGIN_BASE_FILE ) ]
		);
}

/**
 * Returns an arrow of allowed MIME types from the settings.
 *
 * @since 2.1.0
 * @access public
 *
 * @return int The current database version of Pro Mime Types.
 */
function get_db_version() {
	return is_network_mode()
		? (int) \get_site_option( DB_VERSION_OPTION_NAME )
		: (int) \get_option( DB_VERSION_OPTION_NAME );
}

/**
 * Returns a list of allowed MIME types from the settings.
 *
 * Must be called after plugins_loaded 10, or it will crash (PHP 8.0+).
 *
 * @see WordPress function get_allowed_mime_types().
 * @since 2.1.0
 * @access public
 *
 * @return string[] Array of mime types keyed by the file extension regex corresponding to those types.
 *                  e.g. 'jpg|jpeg|jpe|jif|jfif' => 'image/jpeg'.
 */
function get_allowed_mime_types() {
	return array_column(
		array_intersect_key(
			SUPPORTED_MIME_TYPES,
			array_flip( get_allowed_mime_types_settings( true ) ),
		),
		1, // $mime
		0, // $extension_regex
	);
}

/**
 * Returns allowed MIME type settings, comma separated.
 *
 * @since 2.0.0
 * @since 2.1.0 1: Added a first paramter ($as_array).
 *              2: Can no longer return false.
 * @access public
 *
 * @param bool $as_array Set to true to return an array.
 * @return string|string[] A comma separated list of allowed MIME types options,
 *                         or exploded to an array when $as_array is set.
 */
function get_allowed_mime_types_settings( $as_array = false ) {

	$settings = (
		is_network_mode()
			? \get_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME )
			: \get_option( ALLOWED_MIME_TYPES_OPTIONS_NAME )
		) ?: '';

	return $as_array
		? explode( ',', $settings )
		: $settings;
}

/**
 * Defines the `Pro_Mime_Types\SUPPORTED_MIME_TYPES` constant using filter `pmt_supported_mime_types`.
 *
 * @since 2.0.0
 * @access private
 */
function _define_supported_mime_types() {

	// Extract often recurring sentences to reduce translation operations.
	$is_svg_i18n        = \__( 'XML file formats can be executed by the browser when interpreted in HTML.', 'pro-mime-types' );
	$is_macro_i18n      = \__( 'Can contain macros which office software may execute.', 'pro-mime-types' );
	$is_compressed_i18n = \__( 'Compressed file format, can contain unwanted stuff.', 'pro-mime-types' );

	// Extract levels to prevent excessive array access opcodes.
	$safe      = MIME_DANGER_LEVEL['safe'];
	$lowrisk   = MIME_DANGER_LEVEL['low-risk'];
	$highrisk  = MIME_DANGER_LEVEL['high-risk'];
	$dangerous = MIME_DANGER_LEVEL['dangerous'];

	/**
	 * @since 2.0.0
	 * array['extension_regex','mime','danger','comment','type']
	 */
	\define(
		'Pro_Mime_Types\SUPPORTED_MIME_TYPES',
		/**
		 * Removing a MIME type here will also prevent uploading, even if the setting is stored.
		 *
		 * @since 2.0.0
		 * @since 2.1.0 Now keyed the array values.
		 * @param array['extension_regex','mime','danger','comment','type'] $mime_types : {
		 *     'option_name' => {
		 *        'extension_regex' => string of pipe separated extension names
		 *        'mime'            => exact mime type as recognized by PHP's fileinfo extension.
		 *        'danger'          => On a scale of 0-3, see const Pro_Mime_Types\MIME_DANGER_LEVEL.
		 *        'comment'         => string of reason why it is considered not safe.
		 *        'type'            => string of file type as recognized by WordPress's file sorter (or custom like 'misc/other').
		 *     },
		 * }
		 */
		\apply_filters(
			'pmt_supported_mime_types',
			[
				// Image formats.
				'avif'    => [ 'avif|avifs', 'image/avif', $safe, '', 'image' ], // Requires WP 6.5 or later, PHP 8.1 or later, and GD support.
				'bpm'     => [ 'bmp', 'image/bmp', $safe, '', 'image' ],
				'gif'     => [ 'gif', 'image/gif', $safe, '', 'image' ],
				'heic'    => [ 'heic|heif', 'image/heic', $safe, '', 'image' ], // heic is image/heif, heif is image/heic. :D
				'ico'     => [ 'ico', 'image/x-icon', $safe, '', 'image' ],
				'jpg'     => [ 'jpg|jpeg|jpe|jif|jfif', 'image/jpeg', $safe, '', 'image' ],
				'png'     => [ 'png', 'image/png', $safe, '', 'image' ],
				'svg'     => [ 'svg', 'image/svg+xml', $dangerous, \__( 'XML file formats can be executed by the browser when interpreted in HTML. SVG is always interpreted in HTML.', 'pro-mime-types' ), 'image' ], // Dangerous: This XML type is always executed.
				'tif'     => [ 'tif|tiff', 'image/tiff', $safe, '', 'image' ],
				'webp'    => [ 'webp', 'image/webp', $safe, '', 'image' ],

				// Audio formats.
				'aac'     => [ 'aac', 'audio/aac', $safe, '', 'audio' ],
				'ac3'     => [ 'ac3', 'audio/ac3', $safe, '', 'audio' ],
				'aff'     => [ 'aff|aif|aiff', 'audio/aiff', $safe, '', 'audio' ],
				'flac'    => [ 'flac', 'audio/flac', $safe, '', 'audio' ],
				'midi'    => [ 'mid|midi', 'audio/midi', $safe, '', 'audio' ],
				'mka'     => [ 'mka', 'audio/x-matroska', $safe, '', 'audio' ],
				'mp3'     => [ 'mp1|mp2|mp3|m3a|m4a|m4b', 'audio/mpeg', $safe, '', 'audio' ],
				'ogg'     => [ 'ogg|oga', 'audio/ogg', $safe, '', 'audio' ],
				'ram'     => [ 'ra|ram', 'audio/x-realaudio', $safe, '', 'audio' ],
				'wav'     => [ 'wav', 'audio/wav', $safe, '', 'audio' ],
				'wax'     => [ 'wax', 'audio/x-ms-wax', $safe, '', 'audio' ],
				'wma'     => [ 'wma', 'audio/x-ms-wma', $safe, '', 'audio' ],

				// Video formats.
				'3g2'     => [ '3g2|3gp2', 'video/3gpp2', $safe, '', 'video' ], // Can also be audio
				'3gp'     => [ '3gp|3gpp', 'video/3gpp', $safe, '', 'video' ], // Can also be audio
				'asf'     => [ 'asf|asx', 'video/x-ms-asf', $safe, '', 'video' ],
				'avi'     => [ 'avi', 'video/avi', $safe, '', 'video' ],
				'divx'    => [ 'divx', 'video/divx', $safe, '', 'video' ],
				'mkv'     => [ 'mkv', 'video/x-matroska', $safe, '', 'video' ],
				'mov'     => [ 'mov|qt', 'video/quicktime', $safe, '', 'video' ],
				'mp4'     => [ 'mp4|m4v', 'video/mp4', $safe, '', 'video' ],
				'mpg'     => [ 'mpeg|mpg|mpe|mpv|vob', 'video/mpeg', $safe, '', 'video' ],
				'ogv'     => [ 'ogv|ogm', 'video/ogg', $safe, '', 'video' ],
				'rm'      => [ 'rm', 'application/vnd.rn-realmedia', $safe, '', 'video' ], // Can also be audio
				'webm'    => [ 'webm', 'video/webm', $safe, '', 'video' ],
				'wm'      => [ 'wm', 'video/x-ms-wm', $safe, '', 'video' ],
				'wmv'     => [ 'wmv', 'video/x-ms-wmv', $safe, '', 'video' ],
				'wmx'     => [ 'wmx', 'video/x-ms-wmx', $safe, '', 'video' ],

				// Document formats.
				'ai'      => [ 'ai', 'application/postscript', $safe, '', 'document' ],
				'doc'     => [ 'doc', 'application/msword', $lowrisk, $is_macro_i18n, 'document' ],
				'docm'    => [ 'docm', 'application/vnd.ms-word.document.macroEnabled.12', $lowrisk, $is_macro_i18n, 'document' ],
				'docx'    => [ 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', $lowrisk, $is_svg_i18n, 'document' ],
				'dotm'    => [ 'dotm', 'application/vnd.ms-word.template.macroEnabled.12', $lowrisk, $is_macro_i18n, 'document' ],
				'dotx'    => [ 'dotx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', $lowrisk, $is_svg_i18n, 'document' ],
				'odt'     => [ 'odt', 'application/vnd.oasis.opendocument.text', $lowrisk, $is_macro_i18n, 'document' ],
				'oxps'    => [ 'oxps', 'application/oxps', $safe, '', 'document' ],
				'pages'   => [ 'pages', 'application/vnd.apple.pages', $lowrisk, $is_macro_i18n, 'document' ],
				'pdf'     => [ 'pdf', 'application/pdf', $lowrisk, \__( 'Can exploit vulnerabilities when opened in browsers.', 'pro-mime-types' ), 'document' ],
				'psd'     => [ 'psd', 'image/vnd.adobe.photoshop', $safe, '', 'document' ],
				'rtf'     => [ 'rtf', 'application/rtf', $safe, '', 'document' ],
				'wpd'     => [ 'wp|wpd', 'application/wordperfect', $lowrisk, $is_macro_i18n, 'document' ],
				'wri'     => [ 'wri', 'application/vnd.ms-write', $safe, '', 'document' ],
				'xcf'     => [ 'xcf', 'image/x-xcf', $safe, '', 'document' ],
				'xps'     => [ 'xps', 'application/vnd.ms-xpsdocument', $safe, '', 'document' ],

				// Spreadsheet formats.
				'numbers' => [ 'numbers', 'application/vnd.apple.numbers', $lowrisk, $is_macro_i18n, 'spreadsheet' ],
				'ods'     => [ 'ods', 'application/vnd.oasis.opendocument.spreadsheet', $lowrisk, $is_macro_i18n, 'spreadsheet' ],
				'xlam'    => [ 'xlam', 'application/vnd.ms-excel.addin.macroEnabled.12', $lowrisk, $is_macro_i18n, 'spreadsheet' ],
				'xls'     => [ 'xla|xls|xlt|xlw', 'application/vnd.ms-excel', $lowrisk, $is_macro_i18n, 'spreadsheet' ],
				'xlsb'    => [ 'xlsb', 'application/vnd.ms-excel.sheet.binary.macroEnabled.12', $lowrisk, $is_macro_i18n, 'spreadsheet' ],
				'xlsm'    => [ 'xlsm', 'application/vnd.ms-excel.sheet.macroEnabled.12', $lowrisk, $is_macro_i18n, 'spreadsheet' ],
				'xlsx'    => [ 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $lowrisk, $is_svg_i18n, 'spreadsheet' ],
				'xltm'    => [ 'xltm', 'application/vnd.ms-excel.template.macroEnabled.12', $lowrisk, $is_macro_i18n, 'spreadsheet' ],
				'xltx'    => [ 'xltx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.template', $lowrisk, $is_svg_i18n, 'spreadsheet' ],

				// Interactive formats.
				'key'     => [ 'key', 'application/vnd.apple.keynote', $lowrisk, $is_macro_i18n, 'interactive' ],
				'odp'     => [ 'odp', 'application/vnd.oasis.opendocument.presentation', $lowrisk, $is_macro_i18n, 'interactive' ],
				'pot'     => [ 'pot|pps|ppt', 'application/vnd.ms-powerpoint', $lowrisk, $is_macro_i18n, 'interactive' ],
				'potm'    => [ 'potm', 'application/vnd.ms-powerpoint.template.macroEnabled.12', $lowrisk, $is_macro_i18n, 'interactive' ],
				'potx'    => [ 'potx', 'application/vnd.openxmlformats-officedocument.presentationml.template', $lowrisk, $is_svg_i18n, 'interactive' ],
				'ppam'    => [ 'ppam', 'application/vnd.ms-powerpoint.addin.macroEnabled.12', $lowrisk, $is_macro_i18n, 'interactive' ],
				'ppsm'    => [ 'ppsm', 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', $lowrisk, $is_macro_i18n, 'interactive' ],
				'ppsx'    => [ 'ppsx', 'application/vnd.openxmlformats-officedocument.presentationml.slideshow', $lowrisk, $is_svg_i18n, 'interactive' ],
				'pptm'    => [ 'pptm', 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', $lowrisk, $is_macro_i18n, 'interactive' ],
				'pptx'    => [ 'pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', $lowrisk, $is_svg_i18n, 'interactive' ],
				'sldm'    => [ 'sldm', 'application/vnd.ms-powerpoint.slide.macroEnabled.12', $lowrisk, $is_macro_i18n, 'interactive' ],
				'sldx'    => [ 'sldx', 'application/vnd.openxmlformats-officedocument.presentationml.slide', $lowrisk, $is_svg_i18n, 'interactive' ],

				// Text formats.
				'csv'     => [ 'csv', 'text/csv', $safe, '', 'text' ],
				'ics'     => [ 'ics', 'text/calendar', $safe, '', 'text' ],
				'md'      => [ 'md', 'text/markdown', $safe, '', 'text' ],
				'rtx'     => [ 'rtx', 'text/richtext', $safe, '', 'text' ],
				'tsv'     => [ 'tsv', 'text/tab-separated-values', $safe, '', 'text' ],
				'txt'     => [ 'txt|asc|c|cc|h|srt', 'text/plain', $safe, '', 'text' ],
				'vtt'     => [ 'vtt', 'text/vtt', $safe, '', 'text' ],

				// Archive formats.
				'7z'      => [ '7z', 'application/x-7z-compressed', $highrisk, $is_compressed_i18n, 'archive' ],
				'cab'     => [ 'cab', 'application/vnd.ms-cab-compressed', $highrisk, $is_compressed_i18n, 'archive' ],
				'dmg'     => [ 'img|2mg|smi|dmg', 'application/x-apple-diskimage', $highrisk, $is_compressed_i18n, 'archive' ],
				'gz'      => [ 'gz|gzip', 'application/x-gzip', $dangerous, \__( 'Compressed file format, can contain unwanted stuff. Executes in browser.', 'pro-mime-types' ), 'archive' ],
				'rar'     => [ 'rar', 'application/rar', $highrisk, $is_compressed_i18n, 'archive' ],
				'tar'     => [ 'tar', 'application/x-tar', $highrisk, $is_compressed_i18n, 'archive' ],
				'zip'     => [ 'zip', 'application/zip', $highrisk, $is_compressed_i18n, 'archive' ],

				// Code formats.
				'css'     => [ 'css', 'text/css', $highrisk, \__( 'CSS can import external resources in the browser.', 'pro-mime-types' ), 'code' ],
				'dfxp'    => [ 'dfxp', 'application/ttaf+xml', $lowrisk, $is_svg_i18n, 'code' ],
				'html'    => [ 'htm|html', 'text/html', $dangerous, \__( 'Can run in iframes through shortcodes. Can import javascript. Can import CSS.', 'pro-mime-types' ), 'code' ],
				'js'      => [ 'js', 'application/javascript', $dangerous, \__( 'Can execute code in browser.', 'pro-mime-types' ), 'code' ],
				// Let's not. This 'feature' will block us from many hosts:
				// 'php'     => [ 'php', 'application/x-httpd-php', $dangerous, \__( 'This server is built to execute these file types as-is. Do not allow uploading of this file type.', 'pro-mime-types' ), 'code' ],
				'xml'     => [ 'xml', 'application/xhtml+xml', $lowrisk, $is_svg_i18n, 'code' ],

				// Misc application formats.
				'exe'     => [ 'exe', 'application/x-dosexec', $dangerous, \__( 'Can install unwanted software.', 'pro-mime-types' ), 'misc' ],
				'java'    => [ 'class', 'application/x-httpd-java', $highrisk, \__( 'Can be executed by some servers.', 'pro-mime-types' ), 'misc' ],
				'mdb'     => [ 'mdb', 'application/vnd.ms-access', $lowrisk, $is_macro_i18n, 'misc' ],
				'mpp'     => [ 'mpp', 'application/vnd.ms-project', $lowrisk, $is_macro_i18n, 'misc' ],
				'odb'     => [ 'odb', 'application/vnd.oasis.opendocument.database', $lowrisk, $is_macro_i18n, 'misc' ],
				'odc'     => [ 'odc', 'application/vnd.oasis.opendocument.chart', $lowrisk, $is_svg_i18n, 'misc' ],
				'odf'     => [ 'odf', 'application/vnd.oasis.opendocument.formula', $safe, '', 'misc' ],
				'odg'     => [ 'odg', 'application/vnd.oasis.opendocument.graphics', $safe, '', 'misc' ],
				'onetoc'  => [ 'onetoc|onetoc2|onetmp|onepkg', 'application/onenote', $safe, '', 'misc' ],
			],
		)
	);
}
