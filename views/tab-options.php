<?php
/**
 * @package Pro_Mime_Types\Admin\Views
 */

namespace Pro_Mime_Types\Admin\Views;

// phpcs:disable, WordPress.WP.GlobalVariablesOverride -- We're not in the global space.

\defined( 'Pro_Mime_Types\PLUGIN_BASE_FILE' ) or die;

use const \Pro_Mime_Types\{
	Admin\SAVE_NONCE,
	Admin\SAVE_ACTION,
	MIME_DANGER_LEVEL,
	SUPPORTED_MIME_TYPES,
	ALLOWED_MIME_TYPES_OPTIONS_NAME,
};

use function \Pro_Mime_Types\{
	is_network_mode,
	get_allowed_mime_types_settings,
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

?>
<h2><?= \esc_html__( 'MIME Type Options', 'pro-mime-types' ); ?></h2>

<p><?=
	\esc_html__( 'The options below will allow or block file types for upload within your WordPress installation.', 'pro-mime-types' ),
	' ',
	is_network_mode()
		? \esc_html__( 'These options affect all users on all sites of this network.', 'pro-mime-types' )
		: \esc_html__( 'These options affect all users of this site.', 'pro-mime-types' )
?></p>
<p><?=
	\esc_html__( "These options won't affect user upload permissions, only the permitted file extensions and MIME types for those who may already upload.", 'pro-mime-types' ),
	' ',
	\esc_html__( 'Other plugins and themes may still override these options.', 'pro-mime-types' )
?></p>

<hr class=hr-separator>

<form method=post action="<?= \esc_url( \admin_url( 'admin-post.php' ) ) ?>">
	<?php
	\wp_nonce_field( SAVE_NONCE['action'], SAVE_NONCE['name'] );
	// The next field allows callback to `admin_post_' . Admin\SAVE_ACTION`
	?>
	<input type=hidden name=action value="<?= \esc_attr( SAVE_ACTION ) ?>">
	<?php
	// Clone for sorting by reference.
	$supported_mime_types = SUPPORTED_MIME_TYPES;

	$mime_type_titles = [
		'image'       => \__( 'Image file formats', 'pro-mime-types' ),
		'audio'       => \__( 'Audio file formats', 'pro-mime-types' ),
		'video'       => \__( 'Video file formats', 'pro-mime-types' ),
		'document'    => \__( 'Document file formats', 'pro-mime-types' ),
		'spreadsheet' => \__( 'Spreadsheet file formats', 'pro-mime-types' ),
		'interactive' => \__( 'Interactive file formats', 'pro-mime-types' ),
		'text'        => \__( 'Text file formats', 'pro-mime-types' ),
		'archive'     => \__( 'Archive file formats', 'pro-mime-types' ),
		'code'        => \__( 'Code file formats', 'pro-mime-types' ),
		'misc'        => \__( 'Miscellaneous file formats', 'pro-mime-types' ),
		'other'       => \__( 'Other file formats', 'pro-mime-types' ), // Not recognized or registered properly.
	];

	// Set unrecognized types to 'other'; we need this prior sorting.
	foreach ( $supported_mime_types as [ $_er, $_mime, $_danger, $_comment, &$_type ] )
		$_type = \array_key_exists( $_type, $mime_type_titles ) ? $_type : 'other';

	// Now sort by $mime_type_titles' order.
	$mime_type_titles_key_order = array_flip( array_keys( $mime_type_titles ) );
	uasort(
		$supported_mime_types,
		fn( $a, $b ) => $mime_type_titles_key_order[ $a[4] ] <=> $mime_type_titles_key_order[ $b[4] ]
	);

	// This creates [ 'option_key' => index ] for faster parsing.
	$currently_allowed_mime_type_options = array_flip( get_allowed_mime_types_settings( true ) );

	$header_translations = [
		'upload'    => \_x( 'Upload', 'Table header: Allow file uploading', 'pro-mime-types' ),
		'file-ext'  => \__( 'File Extensions', 'pro-mime-types' ),
		'mime-type' => \__( 'MIME type', 'pro-mime-types' ),
	];
	$i18n_assumed_safe   = \__( 'This MIME type is considered safe for uploading.', 'pro-mime-types' );

	$previous_type = '';

	foreach ( $supported_mime_types as $option_name => [ $extension_regex, $mime, $danger, $comment, $type ] ) {
		if ( $type !== $previous_type ) {
			if ( $previous_type ) {
				// Close last opened.
				?>
					</table>
				</div>
			</div>
				<?php
			}
			?>
			<div class=pmt-settings-accordion>
				<h3 class=pmt-settings-accordion-heading>
					<button aria-expanded=false class=pmt-settings-accordion-trigger aria-controls="pmt-settings-accordion-block-<?= \sanitize_key( $type ) ?>" type=button>
						<span class=title><?= \esc_html( $mime_type_titles[ $type ] ) ?></span>
						<span class=icon></span>
					</button>
				</h3>
				<div id="pmt-settings-accordion-block-<?= \sanitize_key( $type ) ?>" class=pmt-settings-accordion-panel hidden>
					<table class="widefat striped form-table pmt-settings-form">
						<tr>
							<th scope=col><?= \esc_html( $header_translations['upload'] ) ?></th>
							<th scope=col><?= \esc_html( $header_translations['file-ext'] ) ?></th>
							<th scope=col><?= \esc_html( $header_translations['mime-type'] ) ?></th>
						</tr>
			<?php
			$previous_type = $type;
		}

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

		$currently_allowed = isset( $currently_allowed_mime_type_options[ $option_name ] );
		$option_field      = ALLOWED_MIME_TYPES_OPTIONS_NAME . "[$option_name]";
		?>
		<tr>
			<td data-colname="<?= \esc_attr( $header_translations['upload'] ) ?>" data-select=true>
				<select name="<?= \esc_attr( $option_field ) ?>">
					<option value=1 <?php \selected( $currently_allowed, true ); ?>>
						<?= \esc_html__( 'Allow', 'pro-mime-types' ) ?>
					</option>
					<option value=0 <?php \selected( $currently_allowed, false ); ?>>
						<?= \esc_html__( 'Disallow', 'pro-mime-types' ) ?>
					</option>
				</select>
				<span class=pmt-warning data-pmt-tooltip="<?= \esc_attr( $comment ?: $i18n_assumed_safe ) ?>"><span class="dashicons <?= \esc_attr( $dashicon ) ?>"></span></span>
			</td>
			<td data-colname="<?= \esc_attr( $header_translations['file-ext'] ) ?>">
				<label for="<?= \esc_attr( $option_field ) ?>">
					<?= \esc_html( str_replace( '|', ', ', $extension_regex ) ) ?>
				</label>
			</td>
			<td data-colname="<?= \esc_attr( $header_translations['mime-type'] ) ?>">
				<?= \esc_html( $mime ) ?>
			</td>
		</tr>
		<?php
	}
	?>
			</table>
		</div>
	</div>

	<hr class=hr-separator>

	<?php \submit_button(); ?>
</form>
<?php
//* # Le Form end
