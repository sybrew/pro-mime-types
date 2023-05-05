<?php
//* Overwrites $mime_to_ext
//* Uses getimagesize_mimes_to_exts
//* From wp-incluses/functions.php

//* UNUSED FOR NOW

/**
 * Attempt to determine the real file type of a file.
 *
 * If unable to, the file name extension will be used to determine type.
 *
 * If it's determined that the extension does not match the file's real type,
 * then the "proper_filename" value will be set with a proper filename and extension.
 *
 * Currently this function only supports validating images known to getimagesize().
 *
 * @since 3.0.0
 *
 * @param string $file     Full path to the file.
 * @param string $filename The name of the file (may differ from $file due to $file being
 *                         in a tmp directory).
 * @param array   $mimes   Optional. Key is the file extension with value as the mime type.
 * @return array Values for the extension, MIME, and either a corrected filename or false
 *               if original $filename is valid.
 */

/**
 * Filter the list mapping image mime types to their respective extensions.
 *
 * @since 3.0.0
 *
 * @param  array $mime_to_ext Array of image mime types and their matching extensions.
 */
/*
add_action( 'init', 'pmt_getimagesize_mimes_to_exts' );
*/
function pmt_getimagesize_mimes_to_exts() {
	add_filter( 'getimagesize_mimes_to_exts', array(
					'image/jpeg' => 'jpg',
					'image/png'  => 'png',
					'image/gif'  => 'gif',
					'image/bmp'  => 'bmp',
					'image/tiff' => 'tif',
					'image/svg+xml' => 'svg', // new
				) );
}
