<?php
/**
 * @package Pro_Mime_Types\Main
 */

namespace Pro_Mime_Types\Main;

\defined( 'Pro_Mime_Types\VERSION' ) or die;

use const \Pro_Mime_Types\SUPPORTED_MIME_TYPES;

use function \Pro_Mime_Types\get_allowed_mime_types_settings;

/**
 * Pro Mime Types plugin
 * Copyright (C) 2023 Sybre Waaijer, CyberWire (https://cyberwire.nl/)
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
 * Registers unrecognized MIME types for WordPress to interpret.
 *
 * @since 2.0.0
 * @access private
 *
 * @param string[] $mime_types Array of MIME types keyed by the file extension regex corresponding to those types.
 * @return string[] Array of MIME types keyed by the file extension regex corresponding to those types.
 */
function _register_mime_types( $mime_types ) {

	// Doesn't necessarily add "support" as in "usable," but "recognized."
	// Do not remove/overwrite, but add, as WordPress's filter requires.
	foreach ( SUPPORTED_MIME_TYPES as [ $extension_regex, $mime ] )
		$mime_types[ $extension_regex ] ??= $mime;

	return $mime_types;
}

/**
 * Registers mimes that are allowed.
 *
 * Resets the default values at action 10.
 * Use action 11 and up to override Pro Mime Types.
 *
 * Unlike Core, this does not consider user role.
 * TODO add that feature?
 *    1. This would force us to upgrade the options (set minimum `upload_files` capability).
 *    2. Populate the second parameter to get a $user object.
 *
 * @since 2.0.0
 *
 * @param array $t Mime types keyed by the file extension regex corresponding to those types.
 * @return array The MIME types.
 */
function _register_upload_mimes( $t = [] ) {

	// Reset.
	$t = [];

	// This creates [ 'jpg|jpeg|jpe' => 'image/jpeg' ], aka [ extension_regex => mime ];
	$mimes = array_column( SUPPORTED_MIME_TYPES, 1, 0 );

	foreach (
		explode(
			',',
			get_allowed_mime_types_settings()
		)
		as $extension_regex
	) {
		if ( isset( $mimes[ $extension_regex ] ) )
			$t[ $extension_regex ] = $mimes[ $extension_regex ];
	}

	return $t;
}

/**
 * Registers extra types for WordPress to recognize handling image sizes.
 *
 * @since 2.0.0
 * @access private
 *
 * @param array $mime_to_ext Array of image mime types and their matching extensions.
 * @return array
 */
function _register_imagesize_extensions( $mime_to_ext ) {

	// Gets all types of 'image':
	$mimes = array_intersect_key(
		// This creates [ 'jpg|jpeg|jpe' => 'image/jpeg' ], aka [ extension_regex => mime ];
		array_column( SUPPORTED_MIME_TYPES, 1, 0 ),
		array_intersect(
			// This creates [ 'jpg|jpeg|jpe' => 'image' ], aka [ extension_regex => type ];
			array_column( SUPPORTED_MIME_TYPES, 4, 0 ),
			[ 'image' ]
		)
	);

	foreach (
		explode(
			',',
			get_allowed_mime_types_settings()
		)
		as $extension_regex
	) {
		if ( isset( $mimes[ $extension_regex ] ) )
			$mime_to_ext[ $mimes[ $extension_regex ] ] = strtok( $extension_regex, '|' );
	}

	return $mime_to_ext;
}

/**
 * Registers extra extensions for WordPress to recognize.
 *
 * @since 2.0.0
 * @access private
 *
 * @param array[] $ext2type Multi-dimensional array of file extensions types keyed by the type of file.
 * @return array[]
 */
function _register_ext2type( $ext2type ) {

	// Reset.
	$ext2type = [];

	// This creates [ 'jpg|jpeg|jpe' => 'image' ], aka [ extension_regex => type ];
	$mimes = array_column( SUPPORTED_MIME_TYPES, 4, 0 );

	foreach ( $mimes as $extension_regex => $type )
		foreach ( explode( '|', $extension_regex ) as $extension )
			$ext2type[ $type ][] = $extension;

	return $ext2type;
}

/**
 * Filters the "real" file type of the given file.
 *
 * @since 2.0.0
 * @access private
 *
 * @param array        $wp_check_filetype_and_ext {
 *     Values for the extension, mime type, and corrected filename.
 *
 *     @type string|false $ext             File extension, or false if the file doesn't match a mime type.
 *     @type string|false $type            File mime type, or false if the file doesn't match a mime type.
 *     @type string|false $proper_filename File name with its correct extension, or false if it cannot be determined.
 * }
 * @param string       $file                      Full path to the file.
 * @param string       $filename                  The name of the file (may differ from $file due to
 *                                                $file being in a tmp directory).
 * @param string[]     $mimes                     Array of mime types keyed by their file extension regex.
 * @param string|false $real_mime                 The actual mime type or false if the type cannot be determined.
 */
function _allow_plaintext_filetype_and_ext( $wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime ) {

	$ext             = $wp_check_filetype_and_ext['ext'];
	$type            = $wp_check_filetype_and_ext['type'];
	$proper_filename = $wp_check_filetype_and_ext['proper_filename'];

	if (
		( $ext && $type ) // Already passed.
		|| ! $real_mime   // Unsupported file ext.; cannot check real file type and still failed!
		|| 'text/plain' !== $real_mime // This is what we're gonna test, after all.
	) return compact( 'ext', 'type', 'proper_filename' );

	// Redo basic extension validation and MIME mapping.
	$wp_filetype = \wp_check_filetype( $filename, $mimes );
	$ext         = $wp_filetype['ext'];
	$type        = $wp_filetype['type'];

	if ( $type ) {
		$allowed = \get_allowed_mime_types();

		if ( ! \in_array( $type, $allowed, true ) ) {
			$type = false;
			$ext  = false;
		}
	}

	return compact( 'ext', 'type', 'proper_filename' );
}
