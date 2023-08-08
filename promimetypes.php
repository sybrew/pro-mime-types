<?php
/**
 * Pro Mime Types
 *
 * @package   Pro_Mime_Types
 * @author    Sybre Waaijer
 * @copyright 2023 CyberWire B.V. (https://cyberwire.nl/)
 * @license   GPL-3.0
 * @link      https://github.com/sybrew/pro-mime-types
 *
 * @wordpress-plugin
 * Plugin Name: Pro Mime Types - Manage file media types
 * Plugin URI: https://wordpress.org/plugins/pro-mime-types/
 * Description: Enable or block MIME types and file extensions for media / file / attachment uploads through a nifty (network) admin menu.
 * Version: 2.0.1
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
 * Copyright (C) 2015 - 2023 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
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
const VERSION = '2.0.0';

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

	if ( ! get_allowed_mime_types_settings() ) {
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
	// TODO Is current user initialized here? If so, we could further halt processing early here.

	require PLUGIN_DIR_PATH . 'inc/admin.php';

	\load_plugin_textdomain(
		'pro-mime-types',
		false,
		\plugin_basename( PLUGIN_BASE_FILE ) . '/language'
	);

	if ( is_network_mode() ) {
		\add_action( 'network_admin_menu', 'Pro_Mime_Types\Admin\_register_network_admin_menu' );
	} else {
		\add_action( 'admin_menu', 'Pro_Mime_Types\Admin\_register_admin_menu' );
	}

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
		??= \is_multisite()
		 && isset( \get_site_option( 'active_sitewide_plugins' )[ \plugin_basename( PLUGIN_BASE_FILE ) ] );
}

/**
 * Returns an arrow of allowed MIME types from the settings.
 *
 * @since 2.0.0
 * @access public
 *
 * @return string A comma separated list of allowed MIME types.
 */
function get_allowed_mime_types_settings() {
	return is_network_mode()
		? \get_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME )
		: \get_option( ALLOWED_MIME_TYPES_OPTIONS_NAME );
}

/**
 * Defines the `Pro_Mime_Types\SUPPORTED_MIME_TYPES` constant using filter `pmt_supported_mime_types`.
 *
 * @since 2.0.0
 * @access private
 */
function _define_supported_mime_types() {

	$is_svg_i18n        = \__( 'XML file formats can be executed by the browser when interpreted in HTML.', 'pro-mime-types' );
	$is_macro_i18n      = \__( 'Can contain macros which office software may execute.', 'pro-mime-types' );
	$is_compressed_i18n = \__( 'Compressed file format, can contain unwanted stuff.', 'pro-mime-types' );

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
		 * @param array['extension_regex','mime','danger','comment','type'] $mime_types : {
		 *     'extension_regex' => string of pipe separated extension names
		 *     'mime'            => exact mime type as recognized by PHP's fileinfo extension.
		 *     'danger'          => On a scale of 0-3, see const Pro_Mime_Types\MIME_DANGER_LEVEL.
		 *     'comment'         => string of reason why it is considered not safe.
		 *     'type'            => string of file type as recognized by WordPress's file sorter (or custom like 'misc/other').
		 * }
		 */
		\apply_filters(
			'pmt_supported_mime_types',
			[
				// Image formats.
				// [ 'avif', 'image/avif', MIME_DANGER_LEVEL['safe'], '', 'image' ], // Causes corruption. Let's figure that out later.
				[ 'bmp', 'image/bmp', MIME_DANGER_LEVEL['safe'], '', 'image' ],
				[ 'gif', 'image/gif', MIME_DANGER_LEVEL['safe'], '', 'image' ],
				[ 'heic|heif', 'image/heic', MIME_DANGER_LEVEL['safe'], '', 'image' ], // heic is image/heif, heif is image/heic. :D
				[ 'ico', 'image/x-icon', MIME_DANGER_LEVEL['safe'], '', 'image' ],
				[ 'jpg|jpeg|jpe|jif|jfif', 'image/jpeg', MIME_DANGER_LEVEL['safe'], '', 'image' ],
				[ 'png', 'image/png', MIME_DANGER_LEVEL['safe'], '', 'image' ],
				[ 'svg', 'image/svg+xml', MIME_DANGER_LEVEL['dangerous'], \__( 'XML file formats can be executed by the browser when interpreted in HTML. SVG is always interpreted in HTML.', 'pro-mime-types' ), 'image' ], // Dangerous: This XML type is always executed.
				[ 'tif|tiff', 'image/tiff', MIME_DANGER_LEVEL['safe'], '', 'image' ],
				[ 'webp', 'image/webp', MIME_DANGER_LEVEL['safe'], '', 'image' ],

				// Audio formats.
				[ 'aac', 'audio/aac', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'ac3', 'audio/ac3', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'aff|aif|aiff', 'audio/aiff', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'flac', 'audio/flac', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'mid|midi', 'audio/midi', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'mka', 'audio/x-matroska', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'mp1|mp2|mp3|m3a|m4a|m4b', 'audio/mpeg', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'ogg|oga', 'audio/ogg', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'ra|ram', 'audio/x-realaudio', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'wav', 'audio/wav', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'wax', 'audio/x-ms-wax', MIME_DANGER_LEVEL['safe'], '', 'audio' ],
				[ 'wma', 'audio/x-ms-wma', MIME_DANGER_LEVEL['safe'], '', 'audio' ],

				// Video formats.
				[ '3g2|3gp2', 'video/3gpp2', MIME_DANGER_LEVEL['safe'], '', 'video' ], // Can also be audio
				[ '3gp|3gpp', 'video/3gpp', MIME_DANGER_LEVEL['safe'], '', 'video' ], // Can also be audio
				[ 'asf|asx', 'video/x-ms-asf', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'avi', 'video/avi', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'divx', 'video/divx', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'mkv', 'video/x-matroska', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'mov|qt', 'video/quicktime', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'mp4|m4v', 'video/mp4', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'mpeg|mpg|mpe|mpv|vob', 'video/mpeg', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'ogv|ogm', 'video/ogg', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'rm', 'application/vnd.rn-realmedia', MIME_DANGER_LEVEL['safe'], '', 'video' ], // Can also be audio
				[ 'webm', 'video/webm', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'wm', 'video/x-ms-wm', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'wmv', 'video/x-ms-wmv', MIME_DANGER_LEVEL['safe'], '', 'video' ],
				[ 'wmx', 'video/x-ms-wmx', MIME_DANGER_LEVEL['safe'], '', 'video' ],

				// Document formats.
				[ 'doc', 'application/msword', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'document' ],
				[ 'docm', 'application/vnd.ms-word.document.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'document' ],
				[ 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'document' ],
				[ 'dotm', 'application/vnd.ms-word.template.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'document' ],
				[ 'dotx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'document' ],
				[ 'odt', 'application/vnd.oasis.opendocument.text', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'document' ],
				[ 'oxps', 'application/oxps', MIME_DANGER_LEVEL['safe'], '', 'document' ],
				[ 'pages', 'application/vnd.apple.pages', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'document' ],
				[ 'pdf', 'application/pdf', MIME_DANGER_LEVEL['low-risk'], \__( 'Can exploit vulnerabilities when opened in browsers.', 'pro-mime-types' ), 'document' ],
				[ 'psd', 'image/vnd.adobe.photoshop', MIME_DANGER_LEVEL['safe'], '', 'document' ],
				[ 'ai', 'application/postscript', MIME_DANGER_LEVEL['safe'], '', 'document' ],
				[ 'rtf', 'application/rtf', MIME_DANGER_LEVEL['safe'], '', 'document' ],
				[ 'wri', 'application/vnd.ms-write', MIME_DANGER_LEVEL['safe'], '', 'document' ],
				[ 'wp|wpd', 'application/wordperfect', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'document' ],
				[ 'xcf', 'image/x-xcf', MIME_DANGER_LEVEL['safe'], '', 'document' ],
				[ 'xps', 'application/vnd.ms-xpsdocument', MIME_DANGER_LEVEL['safe'], '', 'document' ],

				// Spreadsheet formats.
				[ 'numbers', 'application/vnd.apple.numbers', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'spreadsheet' ],
				[ 'ods', 'application/vnd.oasis.opendocument.spreadsheet', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'spreadsheet' ],
				[ 'xla|xls|xlt|xlw', 'application/vnd.ms-excel', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'spreadsheet' ],
				[ 'xlam', 'application/vnd.ms-excel.addin.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'spreadsheet' ],
				[ 'xlsb', 'application/vnd.ms-excel.sheet.binary.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'spreadsheet' ],
				[ 'xlsm', 'application/vnd.ms-excel.sheet.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'spreadsheet' ],
				[ 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'spreadsheet' ],
				[ 'xltm', 'application/vnd.ms-excel.template.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'spreadsheet' ],
				[ 'xltx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.template', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'spreadsheet' ],

				// Interactive formats.
				[ 'key', 'application/vnd.apple.keynote', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'interactive' ],
				[ 'odp', 'application/vnd.oasis.opendocument.presentation', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'interactive' ],
				[ 'pot|pps|ppt', 'application/vnd.ms-powerpoint', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'interactive' ],
				[ 'potm', 'application/vnd.ms-powerpoint.template.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'interactive' ],
				[ 'potx', 'application/vnd.openxmlformats-officedocument.presentationml.template', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'interactive' ],
				[ 'ppam', 'application/vnd.ms-powerpoint.addin.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'interactive' ],
				[ 'ppsm', 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'interactive' ],
				[ 'ppsx', 'application/vnd.openxmlformats-officedocument.presentationml.slideshow', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'interactive' ],
				[ 'pptm', 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'interactive' ],
				[ 'pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'interactive' ],
				[ 'sldm', 'application/vnd.ms-powerpoint.slide.macroEnabled.12', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'interactive' ],
				[ 'sldx', 'application/vnd.openxmlformats-officedocument.presentationml.slide', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'interactive' ],

				// Text formats.
				[ 'csv', 'text/csv', MIME_DANGER_LEVEL['safe'], '', 'text' ],
				[ 'ics', 'text/calendar', MIME_DANGER_LEVEL['safe'], '', 'text' ],
				[ 'md', 'text/markdown', MIME_DANGER_LEVEL['safe'], '', 'text' ],
				[ 'rtx', 'text/richtext', MIME_DANGER_LEVEL['safe'], '', 'text' ],
				[ 'tsv', 'text/tab-separated-values', MIME_DANGER_LEVEL['safe'], '', 'text' ],
				[ 'txt|asc|c|cc|h|srt', 'text/plain', MIME_DANGER_LEVEL['safe'], '', 'text' ],
				[ 'vtt', 'text/vtt', MIME_DANGER_LEVEL['safe'], '', 'text' ],

				// Archive formats.
				[ '7z', 'application/x-7z-compressed', MIME_DANGER_LEVEL['high-risk'], $is_compressed_i18n, 'archive' ],
				[ 'cab', 'application/vnd.ms-cab-compressed', MIME_DANGER_LEVEL['high-risk'], $is_compressed_i18n, 'archive' ],
				[ 'gz|gzip', 'application/x-gzip', MIME_DANGER_LEVEL['dangerous'], \__( 'Compressed file format, can contain unwanted stuff. Executes in browser.', 'pro-mime-types' ), 'archive' ],
				[ 'img|2mg|smi|dmg', 'application/x-apple-diskimage', MIME_DANGER_LEVEL['high-risk'], $is_compressed_i18n, 'archive' ],
				[ 'rar', 'application/rar', MIME_DANGER_LEVEL['high-risk'], $is_compressed_i18n, 'archive' ],
				[ 'tar', 'application/x-tar', MIME_DANGER_LEVEL['high-risk'], $is_compressed_i18n, 'archive' ],
				[ 'zip', 'application/zip', MIME_DANGER_LEVEL['high-risk'], $is_compressed_i18n, 'archive' ],

				// Code formats.
				[ 'css', 'text/css', MIME_DANGER_LEVEL['high-risk'], \__( 'CSS can import external resources in the browser.', 'pro-mime-types' ), 'code' ],
				[ 'dfxp', 'application/ttaf+xml', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'code' ],
				[ 'htm|html', 'text/html', MIME_DANGER_LEVEL['dangerous'], \__( 'Can run in iframes through shortcodes. Can import javascript. Can import CSS.', 'pro-mime-types' ), 'code' ],
				[ 'js', 'application/javascript', MIME_DANGER_LEVEL['dangerous'], \__( 'Can execute code in browser.', 'pro-mime-types' ), 'code' ],
				[ 'xml', 'application/xhtml+xml', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'code' ],
				// [ 'php', 'application/x-httpd-php', MIME_DANGER_LEVEL['dangerous'], \__( 'This server is built to execute these file types as-is. Do not allow uploading of this file type.', 'pro-mime-types' ), 'code' ], // Let's not. This 'feature' will block us from many hosts.

				// Misc application formats.
				[ 'class', 'application/x-httpd-java', MIME_DANGER_LEVEL['high-risk'], \__( 'Can be executed by some servers.', 'pro-mime-types' ), 'misc' ],
				[ 'exe', 'application/x-msdownload', MIME_DANGER_LEVEL['dangerous'], \__( 'Can install unwanted software.', 'pro-mime-types' ), 'misc' ],
				[ 'mdb', 'application/vnd.ms-access', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'misc' ],
				[ 'mpp', 'application/vnd.ms-project', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'misc' ],
				[ 'odb', 'application/vnd.oasis.opendocument.database', MIME_DANGER_LEVEL['low-risk'], $is_macro_i18n, 'misc' ],
				[ 'odc', 'application/vnd.oasis.opendocument.chart', MIME_DANGER_LEVEL['low-risk'], $is_svg_i18n, 'misc' ],
				[ 'odf', 'application/vnd.oasis.opendocument.formula', MIME_DANGER_LEVEL['safe'], '', 'misc' ],
				[ 'odg', 'application/vnd.oasis.opendocument.graphics', MIME_DANGER_LEVEL['safe'], '', 'misc' ],
				[ 'onetoc|onetoc2|onetmp|onepkg', 'application/onenote', MIME_DANGER_LEVEL['safe'], '', 'misc' ],
			],
		)
	);
}
