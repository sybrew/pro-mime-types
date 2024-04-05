= 2.1.1 =

* Fixed: Addressed an issue where the upgrader fired twice on new sites, causing the default settings not disable support for mime types with multiple file extensions.

= 2.1.0 =

* Upgrade: The stored settings will now convert from regex-based to key-based. This allows new types to be added and existing regexes to be adjusted much more reliably via the filter.
	* This changes the return value of function `Pro_Mime_Types\get_allowed_mime_types_settings()`. Since the value is useable in the same manner as before, we didn't change or deprecate the function name.
* Added: AVIF is now supported (`image/avif`, extensions `avif` or `avifs`). The site requires WP 6.5 or later to make the upload editable -- however, WP 6.5 has various bugs with this.
* Added: The settings link has been added to Pro Mime Types's listing on the plugin activation page.
	* Only the network administrator will see this in network mode, conveniently accessible from any subsite.
* Added: Pro Mime Types now registers the current "database version" in option `pro_mime_types_db_version`, separately for networks and single-site activations (depending on how it's activated). This option allows for upgrading the settings over time.
* Changed: constant `Pro_Mime_Types\SUPPORTED_MIME_TYPES` is now an associative array (with index keys) instead of a sequential one.
	* This change directly affects filter `pmt_supported_mime_types`, but sequential values can still be used, though they may not be stored properly.
* Fixed: .exe is now assigned the MIME type PHP recognizes instead of the one Windows does.
* Fixed: When setting the plugin to single-site mode from network mode, the plugin's network options are no longer cleared.

= 2.0.1 =

* Changed: `.svg` was inattentively marked as a "safe" file extension, which it's not. Consider using the [Safe SVG plugin](https://wordpress.org/plugins/safe-svg/) to sanitize SVG uploads (you must still allow them via Pro Mime Types). If you do not use SVGs, you should disallow them from being uploaded.
* Improved: The plugin is faster now since it memoizes repeated translations.
* Updated: New translations are available.

= 2.0.0 =

After eight years without updates (yet still working with the latest version of WordPress), Pro Mime Types got rewritten from the ground up. Now it's written by a senior PHP developer (me) instead of a noob (also me).

* Security: Resolved a security vulnerability (CSRF) due to missing nonces, where a rogue actor could enable and disable MIME-type support after tricking an admin into clicking a rogue link. Props Nguyen Xuan Chien via PatchStack.
* Added: You can now submit translations for this plugin via WordPress.org.
* Added: `application/postscript`, `application/vnd.ms-cab-compressed`, `application/x-apple-diskimage`, `audio/aac`, `audio/ac3`, `audio/aiff`, `audio/flac`, `image/heic`, and `image/webp` are now supported MIME types.
* Added: `.2mg`, `.aac`, `.ac3`, `.aff`, `.ai`, `.aif`, `.aiff`, `.cab`, `.dmg`, `.flac`, `.heic`, `.img`, `.jfif`, `.jif`, `.md`, `.mp1`, `.mp2`, `.mpeg`, `.ogm`, `.smi`, `.vob`, `.xcf`, and `.xml` are now supported extensions.
* Added: Extensive type detection for the image and video MIME types mentioned above. WordPress uses this for shortcodes and file extension conversion.
* Added: This plugin can now be used on a WordPress Multisite network in single-site mode.
	* However, network administrative capabilities are still required to change MIME-type support. Regular site administrators cannot enable MIME-type support on a network for your security.
* Added: You can now filter in custom MIME types via filter `pmt_supported_mime_types`. That filter must be registered before `plugins_loaded`.
* Added: The plugin options are saved when installing the plugin. This prevents extra database lookups on every request when the options are left as default.
* Added: Now allows uploading of file types even if WordPress fails to deduct them.
* Added: You can now sort through text, code, and miscellaneous file types in the Media Library.
* Changed: The "allowed MIME types" list now shows the actual values recognized by WordPress instead of inferring from the options set with this plugin.
* Changed: The plugin translation domain is now `pro-mime-types`, from `promimetypes`.
* Changed: Now requires WP 5.3 or later because it adds a test for the following PHP requirement.
* Changed: Now requires PHP 7.4 or later.
* Changed: The plugin and all its files now use license GPLv3, from "GPLv2 or later."
* Improved: All MIME type options are now stored in a single row, comma separated by "extension regex," instead of using 90 rows (one for each type).
	* After updating, all options will be migrated. It would be best if you did not downgrade, or you'll get 90 extraneous settings in your database.
	* Note: During the upgrade, if the plugin operates in single-site mode on a network, then **only** the first site that receives a request will get migrated the options; all other sites will use the default settings. This plugin has too small of a reach to consider this edge case. Moreover, the settings weren't always reachable in single-site mode, anyway.
* Improved: Overall performance by implementing better coding standards.
* Removed: Pro Sites support and all related settings; that plugin has been abandoned.
* Removed: Shortcode `superadmin_showmimetypes`.
* Removed: All old functions, filters, actions, callbacks, and globals.
* Removed: Support for `.swf` and `.flv` file extensions. Shockwave Flash is long gone.
* Removed: Support for `.dv` (DeltaVision) and `.sea` (Compact Pro) file sorting in WordPress's Media Library. I couldn't find a valid MIME type.
* Removed: Support for `.php` (PHP Hypertext Processor) file sorting in WordPress's Media Library. Consider using FTP.
	* Implementing this via Pro Mime Types would allow the uploading of PHP files, which we will refuse to implement.

= 1.0.7 =
* Fixed: Fatal Error on attachment call.
* Confirmed: WP 4.3.1 support.

= 1.0.6 =
* Fixed: Wrong call in ext2type filter.

= 1.0.5 =
* Added: Default MIME options (enabled for safe, disabled for the rest) (effective only before the first save has been made).
* Added: Default MIME Types are directly active on the first activation.
* Added: single-site compatibility.
* Added: Extra option saving sanitation.
* Removed: Pro Sites information on non-multisite installations.
* Cleaned: HTML.
* Cleaned: PHP code.
* Compatibility: PHP7 & WP 4.3.0 tested.

= 1.0.4 =
* Fixed PHP warning.

= 1.0.3 =
* Made the shortcodes conforming to the WordPress coding standards.

= 1.0.2 =
* Now uses Object Cache to determine Pro Site level, updates every 4 hours.

= 1.0.1 =
* Fixed PHP notice.
* Loaded global variable $promimes within 'init' instead of 'wp'.

= 1.0.0 =
* Initial Release.
