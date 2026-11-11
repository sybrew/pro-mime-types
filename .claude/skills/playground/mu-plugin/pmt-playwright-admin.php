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

add_action(
	'set_auth_cookie',
	'pmt_playground_sync_auth_cookie',
	10,
	5,
);
add_action(
	'set_logged_in_cookie',
	'pmt_playground_sync_logged_in_cookie',
);
add_action(
	'init',
	'pmt_playground_admin_login',
	0,
);

/**
 * Copies the auth cookie into $_COOKIE so auth_redirect() passes on this request.
 *
 * @since 2.2.0
 *
 * @param string $cookie     Cookie value.
 * @param int    $expire     Cookie expire.
 * @param int    $expiration Session expire.
 * @param int    $user_id    User ID.
 * @param string $scheme     Cookie scheme.
 * @return void
 */
function pmt_playground_sync_auth_cookie( $cookie, $expire, $expiration, $user_id, $scheme ) {
	$_COOKIE[ 'secure_auth' === $scheme ? SECURE_AUTH_COOKIE : AUTH_COOKIE ] = $cookie;
}

/**
 * Copies the logged-in cookie into $_COOKIE for this request.
 *
 * @since 2.2.0
 *
 * @param string $cookie Cookie value.
 * @return void
 */
function pmt_playground_sync_logged_in_cookie( $cookie ) {
	$_COOKIE[ LOGGED_IN_COOKIE ] = $cookie;
}

/**
 * Returns the current request path.
 *
 * @since 2.2.0
 *
 * @return string
 */
function pmt_playground_request_path() {

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path = parse_url( $uri, PHP_URL_PATH );

	return is_string( $path ) ? $path : '';
}

/**
 * Whether the path ends with a suffix. PHP 7.4 has no str_ends_with().
 *
 * @since 2.2.0
 *
 * @param string $path   Request path.
 * @param string $suffix Suffix to test.
 * @return bool
 */
function pmt_playground_ends_with( $path, $suffix ) {

	$len = strlen( $suffix );

	if ( $len > strlen( $path ) )
		return false;

	return substr( $path, -$len ) === $suffix;
}

/**
 * Whether this request should receive a Playwright admin session.
 *
 * Front-end HTML stays logged-out. HTTP capture does not send the header.
 *
 * @since 2.2.0
 *
 * @return bool
 */
function pmt_playground_wants_admin() {

	if ( defined( 'WP_ADMIN' ) && WP_ADMIN )
		return true;

	$path = pmt_playground_request_path();

	if ( pmt_playground_ends_with( $path, '/wp-login.php' ) )
		return true;

	return false !== strpos( $path, '/wp-json/' );
}

/**
 * Logs in the Playground admin when Playwright sends the admin header.
 *
 * @since 2.2.0
 *
 * @return void
 */
function pmt_playground_admin_login() {

	if ( empty( $_SERVER['HTTP_X_PMT_PLAYGROUND_ADMIN'] ) ) return;

	if ( '1' !== $_SERVER['HTTP_X_PMT_PLAYGROUND_ADMIN'] ) return;

	if ( ! pmt_playground_wants_admin() ) return;

	if ( ! is_user_logged_in() ) {
		$user = get_user_by( 'login', 'admin' );

		if ( ! $user ) return;

		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true );
	}

	if ( ! pmt_playground_ends_with(
		pmt_playground_request_path(),
		'/wp-login.php',
	) ) {
		return;
	}

	wp_safe_redirect( admin_url() );
	exit;
}
