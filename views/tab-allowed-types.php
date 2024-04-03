<?php
/**
 * @package Pro_Mime_Types\Admin\Views
 */

namespace Pro_Mime_Types\Admin\Views;

// phpcs:disable, WordPress.WP.GlobalVariablesOverride -- We're not in the global space.

\defined( 'Pro_Mime_Types\PLUGIN_BASE_FILE' ) or die;

use const \Pro_Mime_Types\{
	MIME_DANGER_LEVEL,
	SUPPORTED_MIME_TYPES,
};

use function \Pro_Mime_Types\Admin\_get_mime_type_section_titles;

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

?>
<h2><?= \esc_html__( 'Currently allowed MIME types', 'pro-mime-types' ) ?></h2>

<p><?=
	\esc_html__( 'Another plugin or theme may influence MIME type support. In the lists below you can inspect if this is happening.', 'pro-mime-types' )
?></p>

<p><?=
	\esc_html__( 'The assumed safety listed considers how likely a file can be used in nefarious ways.', 'pro-mime-types' )
?></p>

<hr class=hr-separator>

<?php

// This is [ $extension_regex => $mime ]
$mime_type_allowed          = \get_allowed_mime_types();
$mime_type_settings_regexes = \Pro_Mime_Types\get_allowed_mime_types();

$intended_allowed_mime_types   = array_intersect_key( $mime_type_settings_regexes, $mime_type_allowed );
$extraneous_allowed_mime_types = array_diff_key( $mime_type_allowed, $intended_allowed_mime_types );

$header_translations = [
	'file-ext'  => \__( 'File Extensions', 'pro-mime-types' ),
	'mime-type' => \__( 'MIME type', 'pro-mime-types' ),
	'safety'    => \__( 'Assumed safety', 'pro-mime-types' ),
];

$i18n_assumed_safe = \__( 'This MIME type is considered safe for uploading.', 'pro-mime-types' );

$supported_mime_types = [];
// Make a new array which we can filter by key.
foreach ( SUPPORTED_MIME_TYPES as [ $extension_regex, $mime, $danger, $comment, $type ] )
	$supported_mime_types[ $extension_regex ] = [ $extension_regex, $mime, $danger, $comment, $type ];

?>
<div class=pmt-settings-accordion>
	<h3 class=pmt-settings-accordion-heading>
		<button aria-expanded=false class=pmt-settings-accordion-trigger aria-controls=pmt-settings-accordion-block-by-pro-mime-types type=button>
			<span class=title><?=
			\esc_html__( 'Allowed via Pro Mime Types', 'pro-mime-types' ),
			' ',
			\esc_html( sprintf(
				'(%s)',
				\number_format_i18n( \count( $intended_allowed_mime_types ) )
			) )
			?></span>
			<span class=icon></span>
		</button>
	</h3>
	<div id=pmt-settings-accordion-block-by-pro-mime-types class=pmt-settings-accordion-panel hidden>
		<?php
		if ( empty( $intended_allowed_mime_types ) ) {
			\esc_html_e( 'No allowed MIME types found.', 'pro-mime-types' );
		} else {
			?>
			<table class="widefat striped form-table pmt-settings-form">
				<tr>
					<th scope=col><?= \esc_html( $header_translations['file-ext'] ) ?></th>
					<th scope=col><?= \esc_html( $header_translations['mime-type'] ) ?></th>
					<th scope=col><?= \esc_html( $header_translations['safety'] ) ?></th>
				</tr>
				<?php
				foreach ( $intended_allowed_mime_types as $extension_regex => $int ) {
					[ $extension_regex, $mime, $danger, $comment, $type ] =
						   $supported_mime_types[ $extension_regex ]
						?? [
							// translators: %s = File extension
							sprintf( \__( 'Removed support: %s', 'pro-mime-types' ), $extension_regex ),
							'',
							MIME_DANGER_LEVEL['dangerous'],
							\__( 'Unregistered MIME type!', 'pro-mime-types' ),
							'other',
						];

					switch ( $danger ) {
						case MIME_DANGER_LEVEL['safe']:
							$dashicon = 'dashicons-yes-alt';
							break;
						case MIME_DANGER_LEVEL['low-risk']:
							$dashicon = 'dashicons-flag';
							break;
						case MIME_DANGER_LEVEL['high-risk']:
							$dashicon = 'dashicons-warning';
							break;
						case MIME_DANGER_LEVEL['dangerous']:
							$dashicon = 'dashicons-dismiss';
					}

					?>
					<tr>
						<td data-colname="<?= \esc_attr( $header_translations['file-ext'] ) ?>">
							<?= \esc_html( str_replace( '|', ', ', $extension_regex ) ) ?>
						</td>
						<td data-colname="<?= \esc_attr( $header_translations['mime-type'] ) ?>">
							<?= \esc_html( $mime ) ?>
						</td>
						<td data-colname="<?= \esc_attr( $header_translations['safety'] ) ?>" data-select=true>
							<span class=pmt-warning data-pmt-tooltip="<?= \esc_attr( $comment ?: $i18n_assumed_safe ) ?>"><span class="dashicons <?= \esc_attr( $dashicon ) ?>"></span></span>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		?>
	</div>
</div>
<div class=pmt-settings-accordion>
	<h3 class=pmt-settings-accordion-heading>
		<button aria-expanded=false class=pmt-settings-accordion-trigger aria-controls=pmt-settings-accordion-block-elsewhere type=button>
			<span class=title><?=
			\esc_html__( 'Allowed via other software', 'pro-mime-types' ),
			' ',
			\esc_html( sprintf(
				'(%s)',
				\number_format_i18n( \count( $extraneous_allowed_mime_types ) )
			) )
			?></span>
			<span class=icon></span>
		</button>
	</h3>
	<div id=pmt-settings-accordion-block-elsewhere class=pmt-settings-accordion-panel hidden>
		<?php
		if ( empty( $extraneous_allowed_mime_types ) ) {
			\esc_html_e( 'No allowed MIME types found.', 'pro-mime-types' );
		} else {
			?>
			<table class="widefat striped form-table pmt-settings-form">
				<tr>
					<th scope=col><?= \esc_html( $header_translations['file-ext'] ) ?></th>
					<th scope=col><?= \esc_html( $header_translations['mime-type'] ) ?></th>
					<th scope=col><?= \esc_html( $header_translations['safety'] ) ?></th>
				</tr>
				<?php
				foreach ( $extraneous_allowed_mime_types as $extension_regex => $mime ) {
					[ $extension_regex, $mime, $danger, $comment, $type ] =
						   $supported_mime_types[ $extension_regex ]
						?? [
							// translators: %s = File extension
							sprintf( \__( 'Unregistered: %s', 'pro-mime-types' ), $extension_regex ),
							$mime,
							MIME_DANGER_LEVEL['dangerous'],
							\__( 'Unregistered MIME type.', 'pro-mime-types' ),
							'other',
						];

					switch ( $danger ) {
						case MIME_DANGER_LEVEL['safe']:
							$dashicon = 'dashicons-yes-alt';
							break;
						case MIME_DANGER_LEVEL['low-risk']:
							$dashicon = 'dashicons-flag';
							break;
						case MIME_DANGER_LEVEL['high-risk']:
							$dashicon = 'dashicons-warning';
							break;
						case MIME_DANGER_LEVEL['dangerous']:
							$dashicon = 'dashicons-dismiss';
					}

					?>
					<tr>
						<td data-colname="<?= \esc_attr( $header_translations['file-ext'] ) ?>">
							<?= \esc_html( str_replace( '|', ', ', $extension_regex ) ) ?>
						</td>
						<td data-colname="<?= \esc_attr( $header_translations['mime-type'] ) ?>">
							<?= \esc_html( $mime ) ?>
						</td>
						<td data-colname="<?= \esc_attr( $header_translations['safety'] ) ?>" data-select=true>
							<span class=pmt-warning data-pmt-tooltip="<?= \esc_attr( $comment ?: $i18n_assumed_safe ) ?>"><span class="dashicons <?= \esc_attr( $dashicon ) ?>"></span></span>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		?>
	</div>
</div>
