<?php
/**
 * @package Pro_Mime_Types\Admin\Views
 */

namespace Pro_Mime_Types\Admin\Views;

// phpcs:disable, WordPress.WP.GlobalVariablesOverride -- We're not in the global space.

\defined( 'Pro_Mime_Types\PLUGIN_BASE_FILE' ) or die;

use const \Pro_Mime_Types\{
	MIME_DANGER_LEVEL,
	Admin\PAGE_HOOK,
	Admin\SAVED_RESPONSE,
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

if ( is_network_mode() ) {
	/**
	 * @since 2.0.0
	 * @param array The naviational tabs for network mode.
	 */
	$tabs = \apply_filters(
		'pmt_navigation_tabs_network',
		[
			''              => [
				'title' => \_x( 'Options', 'Tab title', 'pro-mime-types' ),
				'link'  => \network_admin_url( 'settings.php?page=' . PAGE_HOOK ),
			],
			'allowed-types' => [
				'title' => \_x( 'Allowed Types', 'Tab title', 'pro-mime-types' ),
				'link'  => \network_admin_url( 'settings.php?page=' . PAGE_HOOK . '&tab=allowed-types' ),
			],
		],
	);
} else {
	/**
	 * @since 2.0.0
	 * @param array The naviational tabs.
	 */
	$tabs = \apply_filters(
		'pmt_navigation_tabs',
		[
			''              => [
				'title' => \_x( 'Options', 'Tab title', 'pro-mime-types' ),
				'link'  => \admin_url( 'options-general.php?page=' . PAGE_HOOK ),
			],
			'allowed-types' => [
				'title' => \_x( 'Allowed Types', 'Tab title', 'pro-mime-types' ),
				'link'  => \admin_url( 'options-general.php?page=' . PAGE_HOOK . '&tab=allowed-types' ),
			],
		],
	);
}

// First check if 'tab' is set in _GET, otherwise it'll warn with an empty string.
// phpcs:ignore, WordPress.Security.NonceVerification.Recommended -- Affects output view only.
$current_tab = isset( $_GET['tab'], $tabs[ $_GET['tab'] ] ) ? $_GET['tab'] : '';

?>
<div class=pmt-settings-header>
	<div class=pmt-settings-title-section>
		<h1>Pro Mime Types</h1>
	</div>
	<nav class="pmt-settings-tabs-wrapper hide-if-no-js" aria-label="<?= \esc_attr__( 'Secondary menu', 'default' ); ?>">
		<?php
		$tab_attributes = [
			'active'   => 'class="pmt-settings-tab active" aria-current="true"',
			'inactive' => 'class="pmt-settings-tab"',
		];
		foreach ( $tabs as $tab_key => $tab ) {
			printf(
				'<a href="%s" %s>%s</a>',
				\esc_url( $tab['link'] ),
				// phpcs:ignore, WordPress.Security.EscapeOutput.OutputNotEscaped -- String literals.
				$tab_attributes[ $tab_key === $current_tab ? 'active' : 'inactive' ],
				\esc_html( $tab['title'] )
			);
		}
		?>
	</nav>
</div>

<hr class=wp-header-end>

<div class="notice notice-error hide-if-js inline">
	<p><?= \esc_html__( 'Pro Mime Types settings require JavaScript.', 'pro-mime-types' ); ?></p>
</div>

<?php
// phpcs:ignore, WordPress.Security.NonceVerification.Recommended -- Affects output view only.
switch ( (int) ( $_GET[ SAVED_RESPONSE ] ?? -1 ) ) {
	case 0:
		?>
		<div id=message class="notice notice-error is-dismissible inline"><p>
			<?= \esc_html__( 'Settings failed to save.', 'pro-mime-types' ) ?>
		</p></div>
		<?php
		break;
	case 1:
		?>
		<div id=message class="notice notice-success is-dismissible inline"><p>
			<?= \esc_html__( 'Settings saved.', 'pro-mime-types' ) ?>
		</p></div>
		<?php
		break;
	case 2:
		?>
		<div id=message class="notice notice-info is-dismissible inline"><p>
			<?= \esc_html__( 'No settings were changed.', 'pro-mime-types' ) ?>
		</p></div>
		<?php
		break;
}

/**
 * Outputs the notices for the current Pro Mime Types admin page.
 *
 * @since 2.0.0
 *
 * @param string $current_tab The current admin tab.
 */
\do_action( 'pmt_admin_notices', $current_tab );
?>

<div class="pmt-settings-body hide-if-no-js">
	<?php
	/**
	 * Outputs the content of the current Pro Mime Types admin page.
	 *
	 * @since 2.0.0
	 *
	 * @param string $current_tab The current admin tab.
	 */
	\do_action( 'pmt_admin_tab_content', $current_tab );
	?>
</div>
