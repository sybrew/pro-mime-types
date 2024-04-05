<?php
/**
 * @package Pro_Mime_Types\Upgrade
 */

namespace Pro_Mime_Types\Upgrade;

\defined( 'Pro_Mime_Types\VERSION' ) or die;

use const \Pro_Mime_Types\{
	ALLOWED_MIME_TYPES_OPTIONS_NAME,
	DB_VERSION,
	DB_VERSION_OPTION_NAME,
	MIME_DANGER_LEVEL,
	SUPPORTED_MIME_TYPES,
};

use function \Pro_Mime_Types\{
	get_allowed_mime_types_settings,
	get_db_version,
	is_network_mode,
};

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

const UPGRADE_LOCK_OPTION_NAME = 'pmt_upgrade.lock';

/**
 * Registers or upgrades the settings.
 *
 * @since 2.0.0
 * @access private
 *
 * @return bool True when done, false when locked.
 */
function _register_or_upgrade_settings() {

	$timeout = 5 * \MINUTE_IN_SECONDS; // Same as WP Core, function update_core().

	$lock = _set_upgrade_lock( $timeout );
	// Lock failed to create--probably because it was already locked (or the database failed us).
	if ( ! $lock ) return false;

	register_shutdown_function( __NAMESPACE__ . '\\_release_upgrade_lock' );

	\wp_raise_memory_limit( 'pmt_upgrade' );

	$ini_max_execution_time = (int) ini_get( 'max_execution_time' );
	if ( 0 !== $ini_max_execution_time )
		set_time_limit( max( $ini_max_execution_time, $timeout ) );

	// Get unaltered settings.
	$settings = is_network_mode()
		? \get_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME )
		: \get_option( ALLOWED_MIME_TYPES_OPTIONS_NAME );

	$success = false === $settings
		? _register_or_migrate_settings() // Register or migrate from < 2.0
		: _upgrade_settings();

	_release_upgrade_lock();

	/**
	 * Clear the cache to prevent a get_option() from retrieving a stale database version to the cache.
	 * Not all caching plugins recognize 'flush', so delete the options cache too, just to be safe.
	 *
	 * @see WordPress's `.../update-core.php`
	 */
	\wp_cache_flush();
	\wp_cache_delete( 'alloptions', 'options' );

	return $success;
}

/**
 * Upgrades the settings.
 *
 * @since 2.1.0
 * @access private
 *
 * @return bool True when done, false on failure.
 */
function _upgrade_settings() {

	/**
	 * Clear the cache to prevent an update_option() from saving a stale database version to the cache.
	 * Not all caching plugins recognize 'flush', so delete the options cache too, just to be safe.
	 *
	 * @see WordPress's `.../update-core.php`
	 */
	\wp_cache_flush();
	\wp_cache_delete( 'alloptions', 'options' );

	$current_version = get_db_version();

	switch ( true ) {
		case $current_version < 2100:
			// Convert from 2.0 to 2.1+; gets automaticlly from either network mode or single site.
			$supported_types = _update_extension_regexes_to_mime_type_options( get_allowed_mime_types_settings( true ) );

			// Migrate
			$success = is_network_mode()
				? \update_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, $supported_types )
				: \update_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, $supported_types );

			if ( ! $success ) break;
			// Pass through

		case true:
			$success = is_network_mode()
				? \update_site_option( DB_VERSION_OPTION_NAME, DB_VERSION )
				: \update_option( DB_VERSION_OPTION_NAME, DB_VERSION );
	}

	return $success ?? false;
}

/**
 * Registers or migrates the settings from Pro Mime Types 2.0 and earlier.
 *
 * @since 2.1.0
 * @access private
 * @global WPDB $wpdb
 *
 * @return bool True when done, false on failure.
 */
function _register_or_migrate_settings() {
	global $wpdb;

	// Delete options; $success may, in an unpredicted event, return false otherwise.
	is_network_mode()
		? \delete_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME )
		: \delete_option( ALLOWED_MIME_TYPES_OPTIONS_NAME );

	/**
	 * Clear the cache to prevent an update_option() from saving a stale database version to the cache.
	 * Not all caching plugins recognize 'flush', so delete the options cache too, just to be safe.
	 *
	 * @see WordPress's `.../update-core.php`
	 */
	\wp_cache_flush();
	\wp_cache_delete( 'alloptions', 'options' );

	$old_supported_regex = [];

	// We used to separate storage based on site mode, rather than plugin activation mode.
	if ( \is_multisite() ) {
		$old_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s",
				$wpdb->esc_like( 'pmt_mime_type_' ) . '%',
			)
		);

		if ( $old_results ) {
			foreach ( $old_results as $row ) {
				switch ( $row->meta_value ) { // Either 2 or 1; disallowed and allowed.
					case 1: // Allowed.
						$old_supported_regex[] = str_replace( 'pmt_mime_type_', '', $row->meta_key );
						// Ignore all other settings.
				}
			}
		}
	} else {
		$old_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( 'pmt_mime_type_' ) . '%',
			)
		);

		if ( $old_results ) {
			foreach ( $old_results as $row ) {
				switch ( $row->option_value ) { // Either 2 or 1; disallowed and allowed.
					case 1: // Allowed.
						$old_supported_regex[] = str_replace( 'pmt_mime_type_', '', $row->option_name );
						// Ignore all other settings.
				}
			}
		}
	}

	if ( $old_results ) {
		// Migrate from < 2.0
		foreach (
			[
				'jpg|jpeg|jpe' => 'jpg|jpeg|jpe|jif|jfif',
				'mp3|m4a|m4b'  => 'mp1|mp2|mp3|m3a|m4a|m4b',
				'ogv'          => 'ogv|ogm',
			]
			as $old => $new
		) {
			if ( \in_array( $old, $old_supported_regex, true ) ) {
				// It'd be faster if we'd collect the "$old", and then perform an array_diff... oh well.
				$old_supported_regex   = array_diff( $old_supported_regex, [ $old ] );
				$old_supported_regex[] = $new;
			}
		}

		// SWF and FLV are long gone. Let's stop recognizing them.
		$old_supported_regex = array_diff( $old_supported_regex, [ 'swf', 'flv' ] );

		// Convert extensions to 2.1+
		$supported_types = _update_extension_regexes_to_mime_type_options( $old_supported_regex );
	} else {
		// Register new installation.
		$supported_types = [];

		// Extract to reduce array access opcodes in loop.
		$safe = MIME_DANGER_LEVEL['safe'];

		// SUPPORTED_MIME_TYPES: extension_regex, mime, danger, comment, type
		foreach ( SUPPORTED_MIME_TYPES as $option => [ , , $danger ] )
			if ( $safe === $danger )
				$supported_types[] = $option;

		$supported_types = implode( ',', $supported_types );
	}

	$success = is_network_mode()
		? \update_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, $supported_types )
		: \update_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, $supported_types );

	// Try again later. Don't warn user -- the plugin will simply be unavailable.
	if ( ! $success )
		return false;

	// Delete old options, if any.
	if ( $old_results ) {
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s",
				$wpdb->esc_like( 'pmt_mime_type_' ) . '%',
			)
		);
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( 'pmt_mime_type_' ) . '%',
			)
		);
	}

	// This separate branch passes the bar for db version 2100.
	return (bool) (
		is_network_mode()
			? \update_site_option( DB_VERSION_OPTION_NAME, 2100 )
			: \update_option( DB_VERSION_OPTION_NAME, 2100 )
	);
}

/**
 * Creates the upgrade lock.
 *
 * We don't use WordPress's native locking mechanism because it requires too many dependencies.
 * We lock on a multisite-table level if on multisite -- not regarding if the plugin operates
 * in network mode.
 *
 * @since 2.0.0
 * @see WP_Upgrader::create_lock()
 *
 * @param int $release_timeout The timeout of the lock.
 * @return bool False if a lock couldn't be created or if the lock is still valid. True otherwise.
 */
function _set_upgrade_lock( $release_timeout ) {
	global $wpdb;

	if ( \is_multisite() ) {
		$lock_result = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO `$wpdb->sitemeta` ( `meta_key`, `meta_value` ) VALUES (%s, %s) /* LOCK */",
				UPGRADE_LOCK_OPTION_NAME,
				time(),
			)
		);
	} else {
		$lock_result = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO `$wpdb->options` ( `option_name`, `option_value`, `autoload` ) VALUES (%s, %s, 'no') /* LOCK */",
				UPGRADE_LOCK_OPTION_NAME,
				time(),
			)
		);
	}

	if ( ! $lock_result ) {
		$lock_result = \get_site_option( UPGRADE_LOCK_OPTION_NAME );

		// If a lock couldn't be created, and there isn't a lock, bail.
		if ( ! $lock_result )
			return false;

		// Check to see if the lock is still valid. If it is, bail.
		if ( $lock_result > ( time() - $release_timeout ) )
			return false;

		// There must exist an expired lock, clear it...
		_release_upgrade_lock();

		// ...and re-gain it.
		return _set_upgrade_lock( $release_timeout );
	}

	// Update the lock, as by this point we've definitely got a lock, just need to fire the actions.
	\update_site_option( UPGRADE_LOCK_OPTION_NAME, time() );

	return true;
}

/**
 * Releases the upgrade lock on shutdown.
 *
 * When the upgrader halts, timeouts, or crashes for any reason, this will run.
 *
 * @since 2.0.0
 */
function _release_upgrade_lock() {
	\delete_site_option( UPGRADE_LOCK_OPTION_NAME );
}

/**
 * Updates file extension regexes to mime type option names
 * (2.0 to 2.1 ALLOWED_MIME_TYPES_OPTIONS_NAME option value).
 *
 * @since 2.1.0
 * @param string[] $extension_regexes File extension regexes.
 * @return string[] Mime type option names.
 */
function _update_extension_regexes_to_mime_type_options( $extension_regexes ) {

	$supported_types = [];

	// This extracts SUPPORTED_MIME_TYPES to become [ 'avif' => 'avif|avifs', 'bpm' => 'bmp', ... ]
	$options = array_combine(
		array_keys( SUPPORTED_MIME_TYPES ),
		array_column( SUPPORTED_MIME_TYPES, 0 ),
	);

	foreach ( $extension_regexes as $regex )
		$supported_types[] = array_search( $regex, $options, true ) ?: '';

	return implode( ',', array_filter( $supported_types, 'strlen' ) );
}
