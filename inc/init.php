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
 * Registers unrecognized MIME types for WordPress to interpret.
 *
 * @hook mime_types 10
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
 * @hook upload_mimes 10
 * @since 2.0.0
 *
 * @return array MIME types keyed by the file extension regex corresponding to those types.
 */
function _register_allowed_upload_mimes() {
	return \Pro_Mime_Types\get_allowed_mime_types();
}

/**
 * Registers extra types for WordPress to recognize handling image sizes.
 *
 * @hook getimagesize_mimes_to_exts 10
 * @since 2.0.0
 * @access private
 *
 * @param array $mime_to_ext Array of image mime types and their matching extensions.
 * @return array
 */
function _register_all_imagesize_extensions( $mime_to_ext ) {

	// reset
	$mime_to_ext = [];

	// Gets all types of 'image':
	$image_mimes = array_intersect_key(
		// This creates [ 'jpg|jpeg|jpe' => 'image/jpeg' ], aka [ extension_regex => mime ];
		array_column( SUPPORTED_MIME_TYPES, 1, 0 ),
		array_intersect(
			// This creates [ 'jpg|jpeg|jpe' => 'image' ], aka [ extension_regex => type ];
			array_column( SUPPORTED_MIME_TYPES, 4, 0 ),
			[ 'image' ]
		)
	);

	foreach ( $image_mimes as $extension_regex => $mime )
		$mime_to_ext[ $mime ] = strtok( $extension_regex, '|' );

	return $mime_to_ext;
}

/**
 * Registers extra types for WordPress to recognize handling video files.
 *
 * @hook wp_video_extensions 10
 * @since 2.0.0
 * @access private
 *
 * @param string[] $extensions An array of supported video formats. Defaults are
 *                             'mp4', 'm4v', 'webm', 'ogv', 'flv'.
 * @return array
 */
function _register_all_video_extensions( $extensions ) {

	// Reset.
	$extensions = [];

	// Gets all types of 'video':
	$video_mimes = array_intersect(
		// This creates [ 'jpg|jpeg|jpe' => 'image' ], aka [ extension_regex => type ];
		array_column( SUPPORTED_MIME_TYPES, 4, 0 ),
		[ 'video' ]
	);

	foreach ( $video_mimes as $extension_regex => $type )
		foreach ( explode( '|', $extension_regex ) as $extension )
			$extensions[] = $extension;

	return $extensions;
}

/**
 * Registers extra types for WordPress to recognize handling audio files.
 *
 * @hook wp_audio_extensions 10
 * @since 2.0.0
 * @access private
 *
 * @param array $extensions An array of supported audio formats. Defaults are
 *                          'mp3', 'ogg', 'flac', 'm4a', 'wav'.
 * @return array
 */
function _register_all_audio_extensions( $extensions ) {

	// Reset.
	$extensions = [];

	// Gets all types of 'video':
	$audio_mimes = array_intersect(
		// This creates [ 'jpg|jpeg|jpe' => 'image' ], aka [ extension_regex => type ];
		array_column( SUPPORTED_MIME_TYPES, 4, 0 ),
		[ 'audio' ]
	);

	foreach ( $audio_mimes as $extension_regex => $type )
		foreach ( explode( '|', $extension_regex ) as $extension )
			$extensions[] = $extension;

	return $extensions;
}

/**
 * Registers extra extensions for WordPress to recognize.
 *
 * @hook ext2type 10
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
 * Registers extra types for WordPress to filter in the media library.
 *
 * @hook post_mime_types 10
 * @since 2.0.0
 * @access private
 * @see get_post_mime_types
 *
 * @param array $post_mime_types Default list of post mime types.
 * @return array
 */
function _register_post_mime_types( $post_mime_types ) {

	// Reset.
	$post_mime_types = [
		'image'       => [
			\__( 'Images', 'default' ),
			\__( 'Manage Images', 'default' ),
			/* translators: %s: Number of images. */
			\_n_noop(
				'Image <span class="count">(%s)</span>',
				'Images <span class="count">(%s)</span>',
				'default'
			),
		],
		'audio'       => [
			\_x( 'Audio', 'file type group', 'default' ),
			\__( 'Manage Audio', 'default' ),
			/* translators: %s: Number of audio files. */
			\_n_noop(
				'Audio <span class="count">(%s)</span>',
				'Audio <span class="count">(%s)</span>',
				'default'
			),
		],
		'video'       => [
			\_x( 'Video', 'file type group', 'default' ),
			\__( 'Manage Video', 'default' ),
			/* translators: %s: Number of video files. */
			\_n_noop(
				'Video <span class="count">(%s)</span>',
				'Video <span class="count">(%s)</span>',
				'default'
			),
		],
		'document'    => [
			\__( 'Documents', 'default' ),
			\__( 'Manage Documents', 'default' ),
			/* translators: %s: Number of documents. */
			\_n_noop(
				'Document <span class="count">(%s)</span>',
				'Documents <span class="count">(%s)</span>',
				'default'
			),
		],
		'spreadsheet' => [
			\__( 'Spreadsheets', 'default' ),
			\__( 'Manage Spreadsheets', 'default' ),
			/* translators: %s: Number of spreadsheets. */
			\_n_noop(
				'Spreadsheet <span class="count">(%s)</span>',
				'Spreadsheets <span class="count">(%s)</span>',
				'default'
			),
		],
		'text'        => [
			\_x( 'Text', 'file type group', 'pro-mime-types' ),
			\__( 'Manage Text', 'pro-mime-types' ),
			/* translators: %s: Number of text files. */
			\_n_noop(
				'Text <span class="count">(%s)</span>',
				'Text <span class="count">(%s)</span>',
				'pro-mime-types'
			),
		],
		'archive'     => [
			\_x( 'Archives', 'file type group', 'default' ),
			\__( 'Manage Archives', 'default' ),
			/* translators: %s: Number of archives. */
			\_n_noop(
				'Archive <span class="count">(%s)</span>',
				'Archives <span class="count">(%s)</span>',
				'default'
			),
		],
		'code'        => [
			\_x( 'Code', 'file type group', 'pro-mime-types' ),
			\__( 'Manage Code', 'pro-mime-types' ),
			/* translators: %s: Number of code files. */
			\_n_noop(
				'Code <span class="count">(%s)</span>',
				'Code <span class="count">(%s)</span>',
				'pro-mime-types'
			),
		],
		'misc'        => [
			\_x( 'Miscellaneous', 'file type group', 'pro-mime-types' ),
			\__( 'Manage Miscellaneous', 'pro-mime-types' ),
			/* translators: %s: Number of miscellaneous files. */
			\_n_noop(
				'Miscellaneous <span class="count">(%s)</span>',
				'Miscellaneous <span class="count">(%s)</span>',
				'pro-mime-types'
			),
		],
	];

	$ext_types  = \wp_get_ext_types();
	$mime_types = \wp_get_mime_types();

	foreach ( $post_mime_types as $group => $labels ) {
		// Always allow sorting of image, audio, and video.
		if ( \in_array( $group, [ 'image', 'audio', 'video' ], true ) )
			continue;

		if ( ! isset( $ext_types[ $group ] ) ) {
			unset( $post_mime_types[ $group ] );
			continue;
		}

		$group_mime_types = [];
		foreach ( $ext_types[ $group ] as $extension ) {
			foreach ( $mime_types as $exts => $mime ) {
				if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
					$group_mime_types[] = $mime;
					break;
				}
			}
		}
		$group_mime_types = implode( ',', array_unique( $group_mime_types ) );

		$post_mime_types[ $group_mime_types ] = $labels;
		unset( $post_mime_types[ $group ] );
	}

	return $post_mime_types;
}

/**
 * Filters the "real" file type of the given file.
 *
 * @hook wp_check_filetype_and_ext 10
 * @since 2.0.0
 * @access private
 * @see wp_check_filetype_and_ext(), this function replicates its behavior.
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
function _allow_real_filetype_and_ext( $wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime ) {

	$ext             = $wp_check_filetype_and_ext['ext'];
	$type            = $wp_check_filetype_and_ext['type'];
	$proper_filename = $wp_check_filetype_and_ext['proper_filename'];

	if (
		   ( $ext && $type ) // Already passed.
		|| ( ! $real_mime || ! \is_string( $real_mime ) ) // Unsupported mime check.
	) return compact( 'ext', 'type', 'proper_filename' );

	// Let's not leak through.
	$ext  = false;
	$type = false;

	$allowed = \Pro_Mime_Types\get_allowed_mime_types();

	if ( \str_starts_with( $real_mime, 'text/' ) ) {
		// Get all mime types of type text and code; these are assumed plaintext by PHP ($real_mime).
		// Then, test whether BOTH the MIME type AND extension are allowed. Because some
		// servers interpret plaintext files with certain extensions as-is, which is dangerous.
		$text_and_code_mimes = array_intersect_key(
			// This creates [ 'jpg|jpeg|jpe' => 'image/jpeg' ], aka [ extension_regex => mime ];
			array_column( SUPPORTED_MIME_TYPES, 1, 0 ),
			array_intersect(
				// This creates [ 'jpg|jpeg|jpe' => 'image' ], aka [ extension_regex => type ];
				array_column( SUPPORTED_MIME_TYPES, 4, 0 ),
				[ 'text', 'code' ],
			),
		);

		foreach ( $text_and_code_mimes as $extension_regex => $mime_type )
			if ( ! isset( $allowed[ $extension_regex ] ) )
				unset( $text_and_code_mimes[ $extension_regex ] );

		// Redo basic extension to MIME mapping. This
		$wp_filetype = \wp_check_filetype( $filename, $text_and_code_mimes );
		$ext         = $wp_filetype['ext'];
		$type        = $wp_filetype['type'];

		// No "lookalike" text/plain match found.
		if ( ! $ext || ! $type )
			return compact( 'ext', 'type', 'proper_filename' );
	} else {
		if ( 'image/heif' === $real_mime ) {
			// PHP switches around image/heic and image/heif for heif and heic respectively...
			// Convert either found to image/heic.
			$assumed_extension_and_mimes = [
				'heic|heif' => 'image/heic',
			];
		}

		if ( isset( $assumed_extension_and_mimes ) ) {
			// Redo basic extension validation and MIME mapping.
			$wp_filetype = \wp_check_filetype( $filename, $assumed_extension_and_mimes );
			$ext         = $wp_filetype['ext'];
			$type        = $wp_filetype['type'];
		}
	}

	// $type shouldn't have been populated if it wasn't in $allowed. Still...
	// Sanity: Must be an allowed mime type.
	if ( ! \in_array( $type, $allowed, true ) ) {
		$ext  = false;
		$type = false;
	}

	return compact( 'ext', 'type', 'proper_filename' );
}
