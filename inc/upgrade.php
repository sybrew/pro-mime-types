<?php
/**
 * @package Pro_Mime_Types\Upgrade
 */

namespace Pro_Mime_Types\Upgrade;

\defined( 'Pro_Mime_Types\VERSION' ) or die;

use function \Pro_Mime_Types\is_network_mode;

use const \Pro_Mime_Types\{
	MIME_DANGER_LEVEL,
	ALLOWED_MIME_TYPES_OPTIONS_NAME,
};

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

const UPGRADE_LOCK_OPTION_NAME = 'pmt_upgrade.lock';

/**
 * Registers or upgrades the settings.
 *
 * @since 2.0.0
 * @access private
 * @global WPDB $wpdb
 *
 * @return bool True when done, false when locked.
 */
function _register_or_upgrade_settings() {
	global $wpdb;

	$timeout = 5 * \MINUTE_IN_SECONDS; // Same as WP Core, function update_core().

	$lock = _set_upgrade_lock( $timeout );
	// Lock failed to create--probably because it was already locked (or the database failed us).
	if ( ! $lock ) return false;

	register_shutdown_function( __NAMESPACE__ . '\\_release_upgrade_lock' );

	\wp_raise_memory_limit( 'pmt_upgrade' );

	$ini_max_execution_time = (int) ini_get( 'max_execution_time' );
	if ( 0 !== $ini_max_execution_time )
		set_time_limit( max( $ini_max_execution_time, $timeout ) );

	\wp_cache_flush();
	\wp_cache_delete( 'alloptions', 'options' );

	if ( \is_multisite() ) {
		$old_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s",
				$wpdb->esc_like( 'pmt_mime_type_' ) . '%',
			)
		);

		if ( $old_results ) {
			$supported_mime_types = [];

			foreach ( $old_results as $row ) {
				switch ( $row->meta_value ) { // Either 2 or 1; disallowed and allowed.
					case 1: // Allowed.
						$supported_mime_types[] = str_replace( 'pmt_mime_type_', '', $row->meta_key );
						break;
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
			$supported_mime_types = [];

			foreach ( $old_results as $row ) {
				switch ( $row->option_value ) { // Either 2 or 1; disallowed and allowed.
					case 1: // Allowed.
						$supported_mime_types[] = str_replace( 'pmt_mime_type_', '', $row->option_name );
						break;
					// Ignore everything else.
				}
			}
		}
	}

	if ( empty( $supported_mime_types ) ) {
		// phpcs:ignore, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- unpack list.
		foreach ( SUPPORTED_MIME_TYPES as [ $extension_regex, $mime, $danger ] )
			if ( MIME_DANGER_LEVEL['safe'] === $danger )
				$supported_mime_types[] = $extension_regex;
	}

	// SWF and FLV are long gone. Let's stop recognizing it.
	$supported_mime_types = array_diff( $supported_mime_types, [ 'swf', 'flv' ] );

	// Delete options; $success may in an unpredicted event return false otherwise.
	\delete_option( ALLOWED_MIME_TYPES_OPTIONS_NAME );
	\delete_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME );

	// Migrate;
	$success = is_network_mode()
		? \update_site_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, implode( ',', $supported_mime_types ) )
		: \update_option( ALLOWED_MIME_TYPES_OPTIONS_NAME, implode( ',', $supported_mime_types ) );

	if ( ! $success ) return false;

	// Delete old options, if any.
	if ( ! empty( $old_results ) ) {
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

	_release_upgrade_lock();

	/**
	 * Clear the cache to prevent a get_option() from retrieving a stale database version to the cache.
	 * Not all caching plugins recognize 'flush', so delete the options cache too, just to be safe.
	 *
	 * @see WordPress's `.../update-core.php`
	 */
	\wp_cache_flush();
	\wp_cache_delete( 'alloptions', 'options' );

	return true;
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
				time()
			)
		);
	} else {
		$lock_result = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO `$wpdb->options` ( `option_name`, `option_value`, `autoload` ) VALUES (%s, %s, 'no') /* LOCK */",
				UPGRADE_LOCK_OPTION_NAME,
				time()
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
 * @since 4.0.0
 * @since 4.1.0 Now uses a controllable option instead of a transient.
 */
function _release_upgrade_lock() {
	\delete_site_option( UPGRADE_LOCK_OPTION_NAME );
}
